<?php

/* Copyright 2017 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk */

/**
 * This is a collection of functions to manipulate the statistics database.
 */
class Mod_statistics extends CI_Model {

    public static $sign_extend;

    public function __construct() {
        self::$sign_extend  = (-1) & ~0xffffffff; // All 1's followed by 32 zeros
    }

    // Mimics the Java function, using 32-bit arithmetic
    private static function hashCode(string $s) {
        $len = strlen($s);
        $h = 0;
        for ($i=0; $i<$len; ++$i)
            $h = ($h*31 + ord($s[$i])) & 0xffffffff;

        if ($h<=0x7fffffff)
            return $h; // A positive value is returned as-is
        else
            return $h|self::$sign_extend; // A negative value is sign-extended
    }


    /** Insert a quiz template into the database unless it is already there.
     * @param quizFile The file containing the template.
     * @param dbName The name of the corpus database.
     * @param dbProp The name of the associated properties (for localization).
     * @param qoName The quiz object used in the quiz.
     * @return The ID of the quiz template in the database. -1 if the database is unusable.
     */
    public function newQuizTemplate(integer $userid, string $quizFile, string $fileContent, string $dbName, string $dbProp, string $qoName) {
        $hash = self::hashCode($fileContent);

        $query = $this->db->where('pathname',$quizFile)->where('quizcodehash',$hash)->where('userid',$userid)->get('sta_quiztemplate');

        foreach ($query->result() as $row)
            if ($row->quizcode===$fileContent)
                return intval($row->id);  // Already in database

        $this->db->insert('sta_quiztemplate', array('pathname' => $quizFile,
                                                    'dbname' => $dbName,
                                                    'dbpropname' => $dbProp,
                                                    'qoname' => $qoName,
                                                    'quizcode' => $fileContent,
                                                    'quizcodehash' => $hash,
                                                    'userid' => $userid));

        return $this->db->insert_id();
    }

    /** Stores information about the start of a quiz.
     * @param templid The ID of the quiz template as returned by {@link #newQuizTemplate(File, String, String, String)}.
     * @param universeStrings The universe used for this quiz.
     * @return The ID of the quiz in the database. -1 if the database is unusable.
     */
    public function startQuiz(integer $userid, integer $templid, array $universeStrings) {
        $this->db->insert('sta_quiz', array('templid' => $templid,
                                            'userid' => $userid,
                                            'start' => time(), // UNIX time
                                            'valid' => true));

        $quizid =  $this->db->insert_id();
 
        $data = array();
        foreach ($universeStrings as $uniItem)
            $data[] = array('quizid' => $quizid,
                            'userid' => $userid,
                            'component' => $uniItem);
        
        $this->db->insert_batch('sta_universe',$data);

        return $quizid;
    }


    public function endQuiz() {
        $this->load->database();

        $this->input->post(NULL, true); // returns all POST items with XSS filter 

        $quizid = $this->input->post('quizid');
        if ($quizid===false) {
            error_log("CT Bad quizid: " . print_r($_POST,true)); // TODO: ?
            return;
        }

        // We need to adjust question times for differences between the server's and the client's
        // clock and for time lost in communication. Therefore we merely use the time spent on each
        // question on the client and add this to the server's notion of the quiz start time.

        $query = $this->db->select('userid,start')->where('id',$quizid)->get('sta_quiz');

        $num_rows = $query->num_rows();
        if ($num_rows !== 1) {
            error_log("CT Num rows: $num_rows"); // TODO: ?
            return; // Problems with the database
        }
    
        $row = $query->row();
        $time = $row->start;

        if ($row->userid != $this->mod_users->my_id())
            return; // Illegal user id

        foreach ($this->input->post('questions') as $question) {
            $time += $question['end_time'] - $question['start_time'];
            $this->db->insert('sta_question', array('quizid' => $quizid,
                                                    'txt' => $question['text'],
                                                    'location' => $question['location'],
                                                    'time' => $time,
                                                    'userid' => $this->mod_users->my_id()));

            $questid =  $this->db->insert_id();
            

            // Update show feature information
            $show_feat = $question['show_feat'];

            $maxFeatno =  count($show_feat['names']); // The number of features is the number of names
            $qono = 0; // Quiz object number
            $featno = 0; // Feature number

            if (isset($show_feat['values'])) { // Check that the question was not empty
                $data = array();
                foreach ($show_feat['values'] as $val) {
                    $data[] = array('questid' => $questid,
                                    'qono' => $qono+1,
                                    'name' => $show_feat['names'][$featno],
                                    'value' => $val,
                                    'userid' => $this->mod_users->my_id());
                    if (++$featno === $maxFeatno) {
                        // Next question object
                        ++$qono;
                        $featno = 0;
                    }
                }
                $this->db->insert_batch('sta_displayfeature',$data);
            }

            // Update request feature information
            $req_feat = $question['req_feat'];

            $maxFeatno =  count($req_feat['names']); // The number of features is the number of names
            $qono = 0; // Quiz object number
            $featno = 0; // Feature number
            $ix = 0; // Index into 'correct_answer', 'users_answer', and 'users_answer_was_correct'

            if (isset($req_feat['correct_answer'])) { // Check that the question was not empty
                $data = array();
                foreach ($req_feat['correct_answer'] as $val) {
                    $data[] = array('questid' => $questid,
                                    'qono' => $qono+1,
                                    'name' => $req_feat['names'][$featno],
                                    'value' => $val,
                                    'answer' => $req_feat['users_answer'][$ix],
                                    'correct' => $req_feat['users_answer_was_correct'][$ix]=='true',
                                    'userid' => $this->mod_users->my_id());
                    ++$ix;
                    if (++$featno === $maxFeatno) {
                        // Next question object
                        ++$qono;
                        $featno = 0;
                    }
                }
                $this->db->insert_batch('sta_requestfeature',$data);
            }

            // Set end time for quiz
            $query = $this->db->where('id',$quizid)->update('sta_quiz',array('end' => $time,
                                                                             'grading' => $question['grading']));
        }
    }

    // Get all templates with finished quizzes for user $user_id
    public function allTemplates(integer $user_id) {
        $query = $this->db->select('sta_quiztemplate.id AS qtid,pathname,dbname,dbpropname,qoname')
            ->from('sta_quiz')->join('sta_quiztemplate','sta_quiztemplate.id=sta_quiz.templid')
            ->where('sta_quiz.valid',1)->where('sta_quiz.userid',$user_id)->where('sta_quiz.end IS NOT NULL')
            ->group_by('sta_quiztemplate.id')->order_by('pathname')->get();
        return $query->result();
    }

    public function allQuizzes(integer $qtid) {
        $query = $this->db->select('sta_quiz.id, FROM_UNIXTIME(`start`) Time, '
                                   . '`end`-`start` Duration,'
                                   . 'sum(correct) Correct, sum(1-correct) Wrong ', false)
            ->from('sta_quiz')
            ->join('sta_question','sta_quiz.id=sta_question.quizid')
            ->join('sta_requestfeature','sta_question.id=sta_requestfeature.questid')
            ->where('sta_quiz.valid',1)->where('sta_quiz.templid',$qtid)->where('end IS NOT NULL')
            ->group_by('sta_quiz.id')->get();
        return $query->result();
    }


    public function allReqFeatures(integer $qtid) {
        $query = $this->db->select('sta_quiz.id,name,value,count(*) cnt')
            ->from('sta_quiz')
            ->join('sta_question','sta_quiz.id=sta_question.quizid')
            ->join('sta_requestfeature','sta_question.id=sta_requestfeature.questid')
            ->where('sta_quiz.valid',1)->where('sta_quiz.templid',$qtid)->where('end IS NOT NULL')->where('correct',0)
            ->group_by(array('sta_quiz.id','name','value'))
            ->order_by('sta_quiz.id')->get();
        return $query->result();
    }

    // Get all templates relating to $classid with finished quizzes for users in $userids
    // TODO: This should be refined with a better class/exercise relationship implementation
    public function get_templates_for_class_and_students(integer $classid, array $userids) {
        // Find all pathids relating to $classid
        $cwd = getcwd();

        $query = $this->db
            ->select('pathname')
            ->from('classexercise')
            ->join('exercisedir','classexercise.pathid=exercisedir.id')
            ->where('classexercise.classid',$classid)
            ->get();

        $templids = array();
        foreach ($query->result() as $row) {
            // Find all templates for a relevant student relating to each path
            $query2 = $this->db
                ->select('id')
                ->where("pathname REGEXP '^$cwd/quizzes/{$row->pathname}/[^/]*$'")
//                ->where("pathname REGEXP '^/var/www/html/3bmoodle/bibleol/quizzes/{$row->pathname}/[^/]*$'")
                ->where_in('userid',$userids)
                ->get('sta_quiztemplate');
            foreach ($query2->result() as $row2)
                $templids[] = (int)$row2->id;
        }
        return $templids;
    }

    // Get all pathnames of templates relating to $classid
    // TODO: This should be refined with a better class/exercise relationship implementation
    public function get_pathnames_for_class(integer $classid) {
        // Find all pathids relating to $classid
        $cwd = getcwd();

        $query = $this->db
            ->select('pathname')
            ->from('classexercise')
            ->join('exercisedir','classexercise.pathid=exercisedir.id')
            ->where('classexercise.classid',$classid)
            ->get();

        $pathset = array(); // This is used as a set
        foreach ($query->result() as $row) {
            // Find all templates for a relevant student relating to each path
            $query2 = $this->db
                ->select('pathname')
                ->where("pathname REGEXP '^$cwd/quizzes/{$row->pathname}/[^/]*$'")
//                ->where("pathname REGEXP '^/var/www/html/3bmoodle/bibleol/quizzes/{$row->pathname}/[^/]*$'")
                ->get('sta_quiztemplate');
            foreach ($query2->result() as $row2)
                $pathset[$row2->pathname] = true;
        }

        $result = array();
        $prefix_length = strlen("$cwd/quizzes/");
//        $prefix_length = strlen('/var/www/html/3bmoodle/bibleol/quizzes/');
        foreach ($pathset as $pathname => $ignore)
            $result[] = substr($pathname,$prefix_length,-4); // Strip prefix and '.3et'

        sort($result);
        return $result;
    }
    
    // Find all user IDs and template IDs that match the specified exercise pathname
    // The result is sorted by user ID
    public function get_users_and_templ(string $path) {
        $cwd = getcwd();

        $query = $this->db
            ->select('id,userid')
            ->where('pathname',"$cwd/quizzes/$path.3et")
//            ->where('pathname',"/var/www/html/3bmoodle/bibleol/quizzes/$path.3et")
            ->get('sta_quiztemplate');

        $users_templ = array();
        foreach ($query->result() as $row) {
            if (!isset($users_templ[$row->userid]))
                $users_templ[$row->userid] = array();
            $users_templ[$row->userid][] = (int)$row->id;
        }

        ksort($users_templ); // Sort by user ID

        return $users_templ;
    }

    public function get_score_by_date_user_templ(integer $uid,array $templids,integer $period_start,integer $period_end) {
        $query = $this->db
            ->from('sta_quiz q')
            ->select('substr(from_unixtime(`q`.`start`),1,10) `st`,sum(`rf`.`correct`)/count(*)*100 `pct`,count(*) `cnt`,60*count(*)/sum(`end`-`start`) `featpermin`')
            ->join('sta_question quest','quizid=q.id')
            ->join('sta_requestfeature rf','quest.id=rf.questid')
            ->where('rf.userid',$uid)
            ->where('(grading is null OR grading=1)')
            ->where_in('q.templid',$templids)
            ->where('q.start >=',$period_start)
            ->where('q.start <',$period_end)
            ->where('end IS NOT NULL')
            ->where('valid',1)
            ->group_by('st')
            ->order_by('st')
            ->get()->result();
        return $query;
    }
    
    public function get_quizzes_duration(array $templids, integer $start, integer $end) {
        $query = $this->db
            ->select('`userid`, `start`, `end`-`start` `duration`', false)
            ->where_in('templid',$templids)
            ->where('start >=',$start)
            ->where('start <',$end)
            ->where('end IS NOT NULL')
            ->where('valid',1)
            ->get('sta_quiz');
        return $query->result();
    }

    
    /** Removes the content from the database.
     */
    public function purge(integer $userid) {
        $this->db->where('userid',$userid)->where('valid',1)->update('sta_quiz',array('valid' => 0));
    }


}
