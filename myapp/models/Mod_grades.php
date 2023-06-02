<?php

/* Copyright 2017 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk */

/**
 * This is a collection of functions to manipulate the statistics/grades database. By Mabio Coelho (mabioc@andrews.edu)
 */
class Mod_grades extends CI_Model {

    public static $sign_extend;
    private $quizzespath;

    public function __construct() {
        self::$sign_extend  = (-1) & ~0xffffffff; // All 1's followed by 32 zeros

       $this->quizzespath = getcwd() . '/quizzes';
//        $this->quizzespath = '/var/www/html/3bmoodle/bibleol/quizzes';
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
    public function newQuizTemplate(int $userid, string $quizFile, string $fileContent, string $dbName, string $dbProp, string $qoName) {
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
    public function startQuiz(int $userid, int $templid, array $universeStrings) {
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
        }

        /* Set end time and grading for quiz (MRCN: and the total number of questions) */
        // Get the total number of features for this quiz
        //$tot_features=$this->quizRequestedFeatures($quizid);
        // Write the results to the DB
        $query = $this->db
            ->from('sta_question as sq')
            ->select('sq.quizid, sq.time, rf.correct, sq.location, rf.value, rf.answer, rf.qono')
            ->join('sta_requestfeature as rf','rf.questid = sq.id')
            ->where('sq.quizid',$quizid)
            ->get();
        $this->db->where('id',$quizid)->update('sta_quiz',array('end' => $time,
                                                                         'grading' => $this->input->post('grading')=='true' ? 1 : 0,
                                                                         'tot_questions' => sizeof($query->result()) ));
    }

    // Get the number of features requested by a quiz
    public function quizRequestedFeatures(int $quizid) {
      $tot_features = 1;

      $query = $this->db->select('quizcode')
      ->from('sta_quiz')->join('sta_quiztemplate','sta_quiztemplate.id=sta_quiz.templid')
      ->where('sta_quiz.ID',$quizid)
      ->get();

      $quizSrc = $query->result();

      if ( !empty($quizSrc) ) {
        $matches=array();
        if ( preg_match_all('/<request.*>(.*)<\/request>/',$quizSrc[0]->quizcode, $matches) ) {
          $tot_features = sizeof($matches[0]);
        }

      }
      // error_log("AAH: DEBUG: Tot Features denerated: " . $tot_features);

      return $tot_features;
    }

    // Get all templates with finished quizzes for user $user_id
    public function allTemplates(int $user_id) {
        $query = $this->db->select('sta_quiztemplate.id AS qtid,pathname,dbname,dbpropname,qoname')
            ->from('sta_quiz')->join('sta_quiztemplate','sta_quiztemplate.id=sta_quiz.templid')
            ->where('sta_quiz.valid',1)->where('sta_quiz.userid',$user_id)->where('sta_quiz.end IS NOT NULL')
            ->group_by('sta_quiztemplate.id')->order_by('pathname')->get();
        return $query->result();
    }

    public function allQuizzes(int $qtid) {
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


    public function allReqFeatures(int $qtid) {
        $query = $this->db->select('sta_quiz.id,name,value,count(*) cnt')
            ->from('sta_quiz')
            ->join('sta_question','sta_quiz.id=sta_question.quizid')
            ->join('sta_requestfeature','sta_question.id=sta_requestfeature.questid')
            ->where('sta_quiz.valid',1)->where('sta_quiz.templid',$qtid)->where('end IS NOT NULL')->where('correct',0)
            ->group_by(array('sta_quiz.id','name','value'))
            ->order_by('sta_quiz.id')->get();
        return $query->result();
    }

    // Get one database information for a set of template IDs.
    // We assume that the underlying database information is the same for all IDs.
    public function get_templ_db(array $templids) {
        assert(!empty($templids));

        // We take the last entry, presumably the most current
        $query = $this->db
            ->select('dbname,dbpropname,qoname')
            ->where_in('id',$templids)
            ->order_by('id','DESC')
            ->limit(1)
            ->get('sta_quiztemplate');

        return $query->row();
    }

    // Get all templates relating to $classid with finished quizzes for users in $userids
    public function get_templates_for_class_and_students(int $classid, array $userids) {
        if (empty($userids))
            return array();

        // Find all pathids relating to $classid
        $query = $this->db
            ->select('pathname')
            ->from('classexercise')
            ->join('exercisedir','classexercise.pathid=exercisedir.id')
            ->where('classexercise.classid',$classid)
            ->get();

        $templids = array();
        foreach ($query->result() as $row) {
            // Find all templates for a relevant student relating to each path

            // We need to escape parentheses i pathnames twice. So ( becomes \\(
            $escaped_pathname = str_replace("\\","\\\\",addcslashes($row->pathname,"()"));

            $query2 = $this->db
                ->select('id')
                ->where("pathname REGEXP '^{$this->quizzespath}/$escaped_pathname/[^/]*$'")
                ->where_in('userid',$userids)
                ->get('sta_quiztemplate');
            foreach ($query2->result() as $row2)
                $templids[] = (int)$row2->id;
        }
        return $templids;
    }

    // Get all templates for finished quizzes for users in $userids
    public function get_templates_for_students(array $userids) {
        if (empty($userids))
            return array();

        $templids = array();
        $query = $this->db
            ->select('id')
            ->where_in('userid',$userids)
            ->get('sta_quiztemplate');

        foreach ($query->result() as $row)
            $templids[] = (int)$row->id;

        return $templids;
    }

    // //Get Availiable exams for the class
    // public function get_exams_for_class(int $classid) {
    //   // Build a query
    //   $query = $this->db
    //       ->select('instance_name,bol_exam_active.id')
    //       ->from('exam_results')
    //       ->join('exam_active','activeexamid=exam_active.exam_id')
    //       ->join('exam','exam_active.exam_id=exam.id')
    //       ->where('exam_active.class_id',$classid)
    //       ->get();
    //
    //   $exams = array(); // This is used as a set
    //   foreach ($query->result() as $row) {
    //     $exams[] = array("id"=>$row->id, "name"=>$row->instance_name);
    //   }
    //   return $exams;
    // }

    //Get Availiable exams for the class
    public function get_exams_for_class(int $classid) {
      // Build a query
      $query = $this->db
          ->select('instance_name, id')
          ->from('exam_active')
          ->where('class_id',$classid)
          ->get();

      $exams = array(); // This is used as a set
      foreach ($query->result() as $row) {
        $exams[] = array("id"=>$row->id, "name"=>$row->instance_name);
      }
      return $exams;
    }

    // Get all pathnames of templates relating to $classid
    public function get_pathnames_for_class(int $classid) {
        // Find all pathids relating to $classid
        $query = $this->db
            ->select('pathname')
            ->from('classexercise')
            ->join('exercisedir','classexercise.pathid=exercisedir.id')
            ->where('classexercise.classid',$classid)
            ->get();

        $pathset = array(); // This is used as a set
        foreach ($query->result() as $row) {
            // Find all templates for a relevant student relating to each path

            // We need to escape parentheses i pathnames twice. So ( becomes \\(
            $escaped_pathname = str_replace("\\","\\\\",addcslashes($row->pathname,"()"));

            $query2 = $this->db
                ->select('pathname')
                ->where("pathname REGEXP '^{$this->quizzespath}/$escaped_pathname/[^/]*$'")
                ->get('sta_quiztemplate');
            foreach ($query2->result() as $row2)
                $pathset[$row2->pathname] = true;
        }

        $result = array();
        $prefix_length = strlen($this->quizzespath . '/');
        foreach ($pathset as $pathname => $ignore)
            $result[] = substr($pathname,$prefix_length,-4); // Strip prefix and '.3et'

        sort($result);
        return $result;
    }

    // Check if the ustand is enrolled in that specific class
    public function check_if_enrolled($classid) {
      if ($classid <=0) {
        return false;
      }
      $query = $this->db
      ->from('class c')
      //
      ->select('c.classname, c.id',false)
      ->join('userclass uc','c.id=uc.classid')
      ->where('uc.userid',$this->mod_users->my_id())
      ->where('c.id',$classid)->get();

      $result = $query->row();

      if ( !$result ) {
        return false;
      }
      else {
        return $query->result();
      }

    }

    // Get IDs of all classes to which $exercise belongs
    private function get_classes_for_pathname(string $exercise) {
        $query = $this->db
            ->select('classid')
            ->from('classexercise')
            ->join('exercisedir','classexercise.pathid=exercisedir.id')
            ->where('pathname',dirname($exercise))
            ->get();

        $classes = array();

        foreach ($query->result() as $row)
            $classes[] = $row->classid;

        return $classes;
    }

    public function may_see_nongraded(int $student, string $exercise) {
        if ($this->mod_users->my_id()==$student)
            return true;

        $this->load->model('mod_userclass');

        $classes = $this->get_classes_for_pathname($exercise);
        return $this->mod_userclass->gave_access($student, $classes);
    }


    public function get_pathnames_for_templids(array $templids) {
        if (empty($templids))
            return array();

        $query = $this->db
            ->select('id,pathname')
            ->where_in('id',$templids)
            ->get('sta_quiztemplate');

        $templs = array();
        $prefix_length = strlen($this->quizzespath . '/');
        foreach ($query->result() as $row)
            $templs[$row->id] = substr($row->pathname,$prefix_length,-4); // Strip prefix and '.3et'

        return $templs;
    }

    public function get_templids_for_pathname_and_user(string $path, int $userid) {
        $query = $this->db
            ->select('id')
            ->where('pathname',"$this->quizzespath/$path.3et")
            ->where('userid',$userid)
            ->get('sta_quiztemplate');

        $ids = array();
        foreach ($query->result() as $row)
            $ids[] = $row->id;

        return $ids;
    }


    // Find all user IDs and template IDs that match the specified exercise pathname
    // The result is sorted by user ID
    public function get_users_and_templ(string $path, int $myid=-1) {
        if ($myid==-1){
          $query = $this->db
          ->select('id,userid')
          ->where('pathname',"$this->quizzespath/$path.3et")
          ->get('sta_quiztemplate');
        }
        else {
          $query = $this->db
          ->select('id,userid')
          ->where('userid',$myid)
          ->get('sta_quiztemplate');
        }

        $users_templ = array();
        foreach ($query->result() as $row) {
            if (!isset($users_templ[$row->userid]))
                $users_templ[$row->userid] = array();
            $users_templ[$row->userid][] = (int)$row->id;
        }

        ksort($users_templ); // Sort by user ID

        return $users_templ;
    }

    // Find all user IDs and exam IDs that match the specified exam activeexamid
    // The result is sorted by user ID
    public function get_users_and_exam_results(string $activeexamid) {
        $query = $this->db
            ->select('activeexamid id,userid')
            ->distinct()
            ->where('activeexamid', $activeexamid)
            ->get('exam_results');

        $users_exam = array();
        foreach ($query->result() as $row) {
            if (!isset($users_exam[$row->userid]))
                $users_exam[$row->userid] = array();
            $users_exam[$row->userid][] = (int)$row->id;
        }

        ksort($users_exam); // Sort by user ID

        return $users_exam;
    }

    // Gets data grouped by day. The index will be noon on the relevant day
    public function get_score_by_date_user_templ(int $uid,array $templids,int $period_start,int $period_end, bool $nongraded, $calculate_percentages = false) {
        if (empty($templids))
            return array();

        // Get results per quiz
        if ($calculate_percentages) {
          $query = $this->db
          ->from('sta_quiz q')
          //
          ->select('rf.userid, q.id,`start`,`end`-`start` `duration`,sum(`rf`.`correct`) `correct`,q.tot_questions `cnt`, sum(`rf`.`correct`)/q.tot_questions*100 `perc`',false)
          ->join('sta_question quest','quest.quizid=q.id')
          ->join('sta_requestfeature rf','quest.id=rf.questid')
          ->where('rf.userid',$uid);
        } else {
          $query = $this->db
          ->from('sta_quiz q')
          ->select('rf.userid, q.id,`start`,`end`-`start` `duration`,sum(`rf`.`correct`) `correct`,q.tot_questions `cnt`, sum(`rf`.`correct`)/q.tot_questions*100 `perc`',false)
          ->join('sta_question quest','quest.quizid=q.id')
          ->join('sta_requestfeature rf','quest.id=rf.questid')
          ->where('rf.userid',$uid);
        }

        if (!$nongraded)
            $query = $query->where('(grading is null OR grading=1)');

        $query = $query
            ->where_in('q.templid',$templids)
            ->where('q.start >=',$period_start)
            ->where('q.start <=',$period_end)
            ->where('end IS NOT NULL')
            ->where('valid',1)
            ->group_by('rf.userid, q.id');
            // TODO: MRCN
            if ($calculate_percentages) {
              $query = $query->order_by('perc desc');
            }
            /////////////////////////////
            $query = $query->get();

        // Consolidate by date
        $perdate = array();
        // TODO: MRCN
        // TODO:  Fix this later: hack to get just the fhighest counted.
        $int_counter=0;
        foreach ($query->result() as $row) {
          // TODO: MRCN part of the // HACK: Get just the first/highest result
            if ($int_counter>0 && $calculate_percentages) {
              break;
            }

            $day = Statistics_timeperiod::round_to_noon((int)$row->start);
            if (!isset($perdate[$day]))
                $perdate[$day] = array('duration' => 0,
                                             'correct' => 0,
                                             'userid' => $row->userid,
                                             'count' => 0,
                                             'original_count' => 0);
            $perdate[$day]['duration'] += $row->duration;
            $perdate[$day]['correct'] += $row->correct;
            $perdate[$day]['count'] += $row->cnt;
            // $perdate[$day]['original_count'] += sizeof($this->get_quizz_detail($uid,$row->id));  //Count based in the number of questions, not the number of words
            // TODO: MRCN part of the // HACK:
            $int_counter +=1;
        }

        foreach ($perdate as $k => &$v) {
            $v['percentage'] = 100*$v['correct'] / $v['count'];
            $v['featpermin'] = 60*$v['count'] / $v['duration'];
        }

        return $perdate;
    }

    // Gets data grouped by user each item). The index will be noon on the relevant day
    public function get_score_by_user_templ(int $uid,array $templids,int $period_start,int $period_end, bool $nongraded, $calculate_percentages = false) {
        if (empty($templids))
            return array();

        // Get results per quiz
        if ($calculate_percentages) {
          $query = $this->db
          ->from('sta_quiz q')
          //
          ->select('rf.userid, q.id,`start`,`end`-`start` `duration`,sum(`rf`.`correct`) `correct`,q.tot_questions `cnt`, sum(`rf`.`correct`)/q.tot_questions*100 `perc`',false)
          ->join('sta_question quest','quest.quizid=q.id')
          ->join('sta_requestfeature rf','quest.id=rf.questid')
          ->where('rf.userid',$uid);
        } else {
          $query = $this->db
          ->from('sta_quiz q')
          ->select('rf.userid, q.id,`start`,`end`-`start` `duration`,sum(`rf`.`correct`) `correct`,q.tot_questions `cnt`, sum(`rf`.`correct`)/q.tot_questions*100 `perc`',false)
          ->join('sta_question quest','quest.quizid=q.id')
          ->join('sta_requestfeature rf','quest.id=rf.questid')
          ->where('rf.userid',$uid);
        }

        if (!$nongraded)
            $query = $query->where('(grading is null OR grading=1)');

        $query = $query
            ->where_in('q.templid',$templids)
            ->where('q.start >=',$period_start)
            ->where('q.start <=',$period_end)
            ->where('end IS NOT NULL')
            ->where('valid',1)
            ->group_by('rf.userid, q.id');
            // TODO: MRCN
            if ($calculate_percentages) {
              $query = $query->order_by('perc desc');
            }
            /////////////////////////////
            $query = $query->get();

        // Consolidate by date
        $perdate = array();
        // TODO: MRCN
        // TODO:  Fix this later: hack to get just the fhighest counted.
        $int_counter=0;
        foreach ($query->result() as $row) {
          // // TODO: MRCN part of the // HACK: Get just the first/highest result
          //   if ($int_counter>0 && $calculate_percentages) {
          //     break;
          //   }

            $day = Statistics_timeperiod::round_to_noon((int)$row->start);
            $day = $row->start;
            if (!isset($perdate[$day]))
                $perdate[$day] = array('duration' => 0,
                                             'correct' => 0,
                                             'userid' => $row->userid,
                                             'quizzid' => $row->id,
                                             'count' => 0,
                                             'original_count' => 0);
            $perdate[$day]['duration'] += $row->duration;
            $perdate[$day]['correct'] += $row->correct;
            $perdate[$day]['count'] += $row->cnt;
            // $perdate[$day]['original_count'] += sizeof($this->get_quizz_detail($uid,$row->id));  //Count based in the number of questions, not the number of words
            // // TODO: MRCN part of the // HACK:
            // $int_counter +=1;
        }

        foreach ($perdate as $k => &$v) {
            $v['percentage'] = 100*$v['correct'] / $v['count'];
            $v['featpermin'] = 60*$v['count'] / $v['duration'];
        }

        return $perdate;
    }

    public function get_score_by_user_active_exam(int $uid,array $examids,/*int $period_start,int $period_end,*/ bool $nongraded, $calculate_percentages = false) {
        if (empty($examids))
            return array();

        // Get results per exam, per quiz
        // if ($calculate_percentages) {
          $query = $this->db
          ->from('exam_results er')
          //
          ->select('rf.userid, q.id,`start`,`end`-`start` `duration`,sum(`rf`.`correct`) `correct`,q.tot_questions `cnt`, sum(`rf`.`correct`)/q.tot_questions*100 `perc`, ex.examcode',false)
          ->join('exam_active exa','exa.id=er.activeexamid')
          ->join('exam ex','exa.exam_id=ex.id')
          ->join('sta_quiz q','q.id=er.quizid')
          ->join('sta_question quest','quest.quizid=q.id')
          ->join('sta_requestfeature rf','quest.id=rf.questid')
          ->where('rf.userid',$uid);
        // } else {
        //   $query = $this->db
        //   ->from('sta_quiz q')
        //   ->select('q.id,`start`,`end`-`start` `duration`,sum(`rf`.`correct`) `correct`,q.tot_questions `cnt`, sum(`rf`.`correct`)/q.tot_questions*100 `perc`',false)
        //   ->join('sta_question quest','quest.quizid=q.id')
        //   ->join('sta_requestfeature rf','quest.id=rf.questid')
        //   ->where('rf.userid',$uid);
        // }

        if (!$nongraded)
            $query = $query->where('(grading is null OR grading=1)');

        $query = $query
            ->where_in('er.activeexamid',$examids)
            // ->where('q.start >=',$period_start)
            // ->where('q.start <=',$period_end)
            ->where('q.end IS NOT NULL')
            ->where('valid',1)
            ->group_by('rf.userid, q.id,ex.examcode ');
            // // TODO
            ///////////////
            $query = $query->get();

        // Consolidate by date
        $perdate = array();
        $ex_count = 0;
        // TODO: MRCN
        // TODO:  Fix this later: hack to get just the fhighest counted.
        $int_counter=0;
        foreach ($query->result() as $row) {
          // // TODO: MRCN part of the // HACK: Get just the first/highest result
          //   if ($int_counter>0 && $calculate_percentages) {
          //     break;
          //   }

            // $day = Statistics_timeperiod::round_to_noon((int)$row->start);
            $day = $row->start;
            // get the wight for this interation
            $matches=array();
            if ( preg_match_all('/<weight.*>([0-9]*)<\/weight>/',$row->examcode, $matches) ) {
              $weight=$matches[1][$ex_count];
            }
            else {
              $weight=1;
            }
            $matches=array();
            if ( preg_match_all('/<exercisename.*>(.*)<\/exercisename>/',$row->examcode, $matches) ) {
              $exercise_name=$matches[1][$ex_count];
            }
            else {
              $exercise_name="N/A";
            }
            // increment the exercise count
            $ex_count += 1;

            // sets the defaults to 0
            if (!isset($perdate[$day]))
                $perdate[$day] = array('duration' => 0,
                                             'correct' => 0,
                                             'count' => 0,
                                             'exercise_name' => '',
                                             'quizzid' => $row->id,
                                             'userid' => $row->userid,
                                             'weight' => 0);
            $perdate[$day]['duration'] += $row->duration;
            $perdate[$day]['correct'] += $row->correct;
            $perdate[$day]['count'] += $row->cnt;
            // $perdate[$day]['count'] += sizeof($this->get_quizz_detail($uid,$row->id));  //Count based in the number of questions, not the number of words
            $perdate[$day]['exercise_name'] = $exercise_name;
            $perdate[$day]['weight'] += $weight;
            // $perdate[$day]['quizzid'] = $row->id;
            // // TODO: MRCN part of the // HACK:
            // $int_counter +=1;
        }

        foreach ($perdate as $k => &$v) {
            $v['percentage'] = 100*$v['correct'] / $v['count'];
            if ($v['duration'] <= 0) {
              $v['featpermin'] = 0;
              continue;
            }
            $v['featpermin'] = 60*$v['count'] / $v['duration'];
        }

        return $perdate;
    }

    // Get weights of the exercises
    public function get_weight_by_exam(int $exam_id, int $exercise_num) {

    }

    public function get_features_by_date_user_templ(int $uid,array $templids,int $period_start,int $period_end, bool $nongraded, bool $highest_score_first = false) {
        if (empty($templids))
            return array();

        $query = $this->db
            ->from('sta_quiz q')
            // ->select('rf.name rfname,sum(`rf`.`correct`)/q.tot_questions*100 `pct`')
            ->select('rf.name rfname,sum(`rf`.`correct`)/count(*)*100 `pct`')
            ->join('sta_question quest','quest.quizid=q.id')
            ->join('sta_requestfeature rf','quest.id=rf.questid')
            ->where('rf.userid',$uid);

        if (!$nongraded)
            $query = $query->where('(grading is null OR grading=1)');

        if (!$highest_score_first) {
          $query = $query
                ->where_in('q.templid',$templids)
                ->where('q.start >=',$period_start)
                ->where('q.start <=',$period_end)
                ->where('end IS NOT NULL')
                ->where('valid',1)
                // ->group_by('q.id,rfname')
                ->group_by('rfname')
                ->get();
        } else {
          // MRCN
          $query = $query
                  ->where_in('q.templid',$templids)
                  ->where('q.start >=',$period_start)
                  ->where('q.start <=',$period_end)
                  ->where('end IS NOT NULL')
                  ->where('valid',1)
                  ->group_by('rfname')
                  // ->group_by('q.id, rfname')
                  // // ->group_by('q.id, rfname')
                  // ->order_by('pct desc')
                  ->get();
        }

        return $query->result();
    }


    // Calculate rates by exam results
    public function get_features_by_date_exam_result(int $uid,array $exams,/* int $period_start,int $period_end,*/ bool $nongraded, bool $highest_score_first = false) {
        if (empty($exams))
            return array();

        $query = $this->db
            ->from('exam_results er')
            // ->select('rf.name rfname,sum(`rf`.`correct`)/q.tot_questions*100 `pct`')
            ->select('rf.name rfname,sum(`rf`.`correct`)/count(*)*100 `pct`')
            ->join('sta_quiz q','q.id=er.quizid')
            ->join('sta_question quest','quest.quizid=q.id')
            ->join('sta_requestfeature rf','quest.id=rf.questid')
            ->where('rf.userid',$uid);

        if (!$nongraded)
            $query = $query->where('(grading is null OR grading=1)');

        //TODO: To make the following if statement work as intended

        if (!$highest_score_first) {
          $query = $query
                ->where_in('er.activeexamid',$exams)
                // ->where('q.start >=',$period_start)
                // ->where('q.start <=',$period_end)
                ->where('end IS NOT NULL')
                ->where('valid',1)
                // ->group_by('q.id,rfname')
                ->group_by('rfname')
                ->get();
        } else {
          // MRCN
          $query = $query
                  ->where_in('er.activeexamid',$exams)
                  // ->where('q.start >=',$period_start)
                  // ->where('q.start <=',$period_end)
                  ->where('end IS NOT NULL')
                  ->where('valid',1)
                  ->group_by('rfname')
                  // ->group_by('q.id, rfname')
                  // // ->group_by('q.id, rfname')
                  // ->order_by('pct desc')
                  ->get();
        }

        return $query->result();
    }


    // Get answers for quizzes by quizzid
    public function get_quizz_detail(int $uid,int $quizzid) {
//       $this->db->select('distinct value,  qono, questid')->from('sta_displayfeature');
//       $subQ = $this->db->get_compiled_select();
//       // $selectQ = "SELECT q.*, rf.qono, rf.answer, rf.correct,rf.name,rf.value, df.value disp_value FROM sta_quiz q
//       $selectQ = "SELECT * FROM sta_quiz q
// join sta_question quest ON quest.quizid=q.id
// join sta_requestfeature rf ON quest.id=rf.questid
// join (SELECT distinct value,  qono, questid FROM sta_displayfeature) df ON rf.questid=df.questid
// where q.id=$quizzid";
        if (empty($quizzid))
            return array();

        // $query = $this->db
        //     ->select($selectQ)
        //     ->get();
        $query = $this->db
            ->from('sta_quiz as q')
            ->select('sq.quizid, sq.time, rf.correct, sq.location, rf.value, rf.answer, rf.qono, sq.txt, GROUP_CONCAT(df.name) disp_type, GROUP_CONCAT(df.value) disp_value')
            ->join('sta_question as sq','sq.quizid=q.id')
            ->join('sta_requestfeature as rf','rf.questid = sq.id')
            ->join('sta_displayfeature as df','rf.questid=df.questid and rf.qono=df.qono')
            ->where('sq.quizid',$quizzid)
            ->group_by('rf.id, rf.questid, rf.qono')
            ->get();
        // $query = $this->db
        //     ->from('sta_question as sq')
        //     ->select('sq.quizid, sq.time, rf.correct, sq.location, rf.value, rf.answer, rf.qono, sq.txt')
        //     ->join('sta_requestfeature as rf','rf.questid = sq.id')
        //     ->where('sq.quizid',$quizzid)
        //     ->get();

        return $query->result();
    }

    public function get_quizzes_duration(array $templids, int $start, int $end) {
        if (empty($templids))
            return array();

        $query = $this->db
            ->select('`userid`, `templid`, `start`, `end`-`start` `duration`', false)
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
    public function purge(int $userid) {
        $this->db->where('userid',$userid)->where('valid',1)->update('sta_quiz',array('valid' => 0));
    }
}
