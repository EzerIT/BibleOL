<?php
class Ctrl_statistics extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->lang->load('statistics', $this->language);
        $this->load->model('mod_statistics');
    }

    // Dummy validation function
    public function always_true($field) {
        return true;
    }

    private function check_logged_in() {
        // MODEL:
        if (!$this->mod_users->is_logged_in())
            throw new DataException($this->lang->line('must_be_logged_in'));
    }

	public function index() {
        $this->show_stat();
	}

    // Note: This function is never used. It is retained for the purpose of direct links to /statistics
	public function show_stat() {
        try {
            $this->check_logged_in();
            $this->load->library('db_config');

            $alltemplates = $this->mod_statistics->allTemplates($this->mod_users->my_id());
            $goodtemplates = array();
            foreach ($alltemplates as $templ) {
                $allquizzes = $this->mod_statistics->allQuizzes(intval($templ->qtid));
                if (count($allquizzes)>0) {
                    if (!$this->db_config->init_config($templ->dbname,$templ->dbpropname, $this->language, false))
                        continue;
                    $templ->l10n = json_decode($this->db_config->l10n_json);
                    $templ->obj2feat = $this->db_config->typeinfo->obj2feat;
                    $templ->quizzes = $allquizzes;
                    $templ->req_features = $this->mod_statistics->allReqFeatures(intval($templ->qtid));
                    $goodtemplates[] = $templ;
                }
            }

            
            $this->load->view('view_top1', array('title' => $this->lang->line('statistics_title')));
            $this->load->view('view_top2');
            $this->load->view('view_menu_bar', array('langselect' => true));
            $this->load->view('view_confirm_dialog');
            $center_text = $this->load->view('view_statistics', array('data' => $goodtemplates), true);
            $left_text = $this->load->view('view_statistics_left', array('name' => $this->mod_users->my_name()), true);
            $this->load->view('view_main_page', array('left_title' => $this->lang->line('statistics_title'),
                                                      'left' => $left_text,
                                                      'center' => $center_text));
            $this->load->view('view_bottom');
            
        }
        catch (DataException $e) {
            $this->error_view($e->getMessage(), $this->lang->line('statistics_title'));
        }
    }


    public function update_stat() {
        $this->mod_statistics->endQuiz();
    }

    public function student_time() {
    	$this->load->model('mod_users');
    	$this->load->model('mod_classes');
    	$this->load->model('mod_userclass');
    	$this->load->model('mod_statistics');
        $this->load->library('statistics_timeperiod',array('default_period'=>'long'));

        try {
//            $this->db->set_dbprefix('bol_');

            $this->load->helper('form');
            $this->load->library('form_validation');

            $this->form_validation->set_data($_GET);

            $this->statistics_timeperiod->set_validation_rules();
            $this->form_validation->set_rules('classid', '', 'callback_always_true');  // Dummy rule. At least one rule is required

			if ($this->form_validation->run()) {
                $this->statistics_timeperiod->ok_dates();

                $userid =  $this->input->get('userid');
                if (is_null($userid))
                    $userid = $this->mod_users->my_id(); // Default value
                else
                    $userid = (int)$userid;

                if (!$this->mod_users->is_teacher() && $userid!=$this->mod_users->my_id())
                    throw new DataException($this->lang->line('illegal_user_id'));

                $classid = $this->input->get('classid');
                if (is_null($classid))
                    $classid = 0;
            }
            else {
                $this->statistics_timeperiod->default_dates();

                $classid = 0;
                $userid = $this->mod_users->my_id();
            }


            $myclassids = $this->mod_userclass->get_classes_for_user($userid);
            $myclasses = $this->mod_classes->get_classes_by_ids($myclassids);

            
            // $classid==0 means ignore class information

            if ($classid>0) {
                if (!in_array($classid,$myclassids))
                    throw new DataException($this->lang->line('illegal_class_id'));

                $templates = $this->mod_statistics->get_templates_for_class_and_students((int)$classid,array($userid));
            }
            else
                $templates = $this->mod_statistics->get_templates_for_students(array($userid));

            $temp_id2path = $this->mod_statistics->get_pathnames_for_templids($templates);
                
            if (!empty($templates))
                $durations = $this->mod_statistics->get_quizzes_duration($templates,
                                                                         $this->statistics_timeperiod->start_timestamp(),
                                                                         $this->statistics_timeperiod->end_timestamp());
            else
                $durations = array();


            // $total[123456] will be the duration in week starting at UNIX time 123456
            // $totaltemp[$templatename] will be the total time spent on template $templatename
            $total = array();
            $totaltemp = array();

            $minweek = $this->statistics_timeperiod->start_week();
            $maxweek = $this->statistics_timeperiod->end_week();
            for ($w=$minweek; $w<$maxweek; $w+=Statistics_timeperiod::SECS_PER_WEEK)
                $total[$w] = 0;

            foreach ($durations as $d) {
                $hours = $d->duration / 3600;
                $w = $this->statistics_timeperiod->last_monday((int)$d->start);
                $total[$w] += $hours;

                $templname = $temp_id2path[$d->templid];
                if (!isset($totaltemp[$templname]))
                    $totaltemp[$templname] = $hours;
                else
                    $totaltemp[$templname] += $hours;
            }
            
            $user_full_name = $this->mod_users->user_full_name($userid);
            
            // VIEW:
            $this->load->view('view_top1', array('title' => $this->lang->line('student_graphs_title'),
                                                 'js_list' => array('RGraph/libraries/RGraph.common.core.js',
                                                                    'RGraph/libraries/RGraph.bar.js',
                                                                    'RGraph/libraries/RGraph.hbar.js',
                                                                    'RGraph/libraries/RGraph.common.dynamic.js',
                                                                    'RGraph/libraries/RGraph.common.tooltips.js',
                                                                    'RGraph/libraries/RGraph.common.key.js',
                                                                    'js/datepicker_period.js',
                                                                    'js/graphing.js')));
            $this->load->view('view_top2');
            $this->load->view('view_menu_bar', array('langselect' => true));
            
            $center_text = $this->load->view('view_progress_student_time', array('classid' => $classid,
                                                                                 'classlist' => $myclasses,
                                                                                 'userid' => $userid,
                                                                                 'user_full_name' => $user_full_name,
                                                                                 'start_date' => $this->statistics_timeperiod->start_string(),
                                                                                 'end_date' => $this->statistics_timeperiod->end_string(),
                                                                                 'total' => $total,
                                                                                 'totaltemp' => $totaltemp), true);


            
            $this->load->view('view_main_page', array('left_title' => $this->lang->line('select_period_heading'),
                                                      'left' => $this->lang->line('time_period_description')
                                                                . $this->lang->line('student_time_description'),
                                                      'center' => $center_text));
            $this->load->view('view_bottom');
        }
        catch (DataException $e) {
            $this->error_view($e->getMessage(), $this->lang->line('student_graphs_title'));
        }
    }
        

    public function student_exercise() {
//        $this->db->set_dbprefix('bol_');

		$this->load->model('mod_users');
		$this->load->model('mod_statistics');
        $this->load->library('statistics_timeperiod',array('default_period'=>'short'));
 
		try {
            $this->load->helper('form');
            $this->load->library('form_validation');
            $this->load->library('db_config');

            $this->form_validation->set_data($_GET);

            $this->statistics_timeperiod->set_validation_rules();
            $this->form_validation->set_rules('templ', 'Template', 'required');
            $this->form_validation->set_rules('userid', 'User ID', 'required');
            $this->form_validation->set_rules('nongraded', '', 'callback_always_true');  // Dummy rule. At least one rule is required

            $templ = $this->input->get('templ');
            $nongraded = $this->input->get('nongraded')=='on';

            $userid = $this->input->get('userid');
            if (is_null($userid))
                $userid = $this->mod_users->my_id();
            else
                $userid = (int)$userid;

            $may_see_nongraded = $this->mod_statistics->may_see_nongraded($userid, $templ);
            
			if ($this->form_validation->run()) {
                $this->statistics_timeperiod->ok_dates();

                if (!$this->mod_users->is_teacher() && $userid!=$this->mod_users->my_id())
                    throw new DataException($this->lang->line('illegal_user_id'));

                $see_nongraded = $nongraded && $may_see_nongraded;
                
                $templs = $this->mod_statistics->get_templids_for_pathname_and_user($templ, $userid);

                $resscore = $this->mod_statistics->get_score_by_date_user_templ($userid,
                                                                                $templs,
                                                                                $this->statistics_timeperiod->start_timestamp(),
                                                                                $this->statistics_timeperiod->end_timestamp(),
                                                                                $see_nongraded);

                
                $resfeat = $this->mod_statistics->get_features_by_date_user_templ($userid,
                                                                                  $templs,
                                                                                  $this->statistics_timeperiod->start_timestamp(),
                                                                                  $this->statistics_timeperiod->end_timestamp(),
                                                                                  $see_nongraded);

                // Localize feature names

                if (!empty($resfeat)) {
                    // We assume that the underlying database information never changed
                    $dbnames = $this->mod_statistics->get_templ_db($templs);
                    $this->db_config->init_config($dbnames->dbname,$dbnames->dbpropname, $this->language);
                    $l10n = json_decode($this->db_config->l10n_json);
                    $featloc = $l10n->emdrosobject->{$dbnames->qoname}; // We only need localization of feature names
                }
                else
                    $featloc = null;

                $status = empty($resscore) ? 0 : 1;  // 0=no data, 1=data
            }
            else {
                $this->statistics_timeperiod->default_dates();

                $resscore = null;
                $resfeat = null;
                $featloc = null;
                $status = 2; // 2=Bad data
            }

            $user_full_name = $this->mod_users->user_full_name($userid);
            
            // VIEW:
            $this->load->view('view_top1', array('title' => $this->lang->line('exercise_graphs_title'),
                                                 'js_list' => array('RGraph/libraries/RGraph.common.core.js',
                                                                    'RGraph/libraries/RGraph.scatter.js',
                                                                    'RGraph/libraries/RGraph.hbar.js',
                                                                    'RGraph/libraries/RGraph.common.dynamic.js',
                                                                    'RGraph/libraries/RGraph.common.tooltips.js',
                                                                    'RGraph/libraries/RGraph.common.key.js',
                                                                    'js/datepicker_period.js',
                                                                    'js/graphing.js')));

            $this->load->view('view_top2');
            $this->load->view('view_menu_bar', array('langselect' => true));
            
            $center_text = $this->load->view('view_progress_student_exercises', array('resscore' => $resscore,
                                                                                      'resfeat' => $resfeat,
                                                                                      'featloc' => $featloc,
                                                                                      'quiz' => $templ,
                                                                                      'status' => $status,
                                                                                      'userid' => $userid,
                                                                                      'user_full_name' => $user_full_name,
                                                                                      'nongraded' => $nongraded,
                                                                                      'may_see_nongraded' => $may_see_nongraded,
                                                                                      'start_date' => $this->statistics_timeperiod->start_string(),
                                                                                      'end_date' => $this->statistics_timeperiod->end_string(),
                                                                                      'minpoint' => $this->statistics_timeperiod->start_timestamp(),
                                                                                      'maxpoint' => $this->statistics_timeperiod->end_timestamp()), true);

            $this->load->view('view_main_page', array('left_title' => $this->lang->line('select_period_heading'),
                                                      'left' => $this->lang->line('time_period_description')
                                                                . $this->lang->line('student_exercise_description'),
                                                      'center' => $center_text));
            $this->load->view('view_bottom');
        }
        catch (DataException $e) {
            $this->error_view($e->getMessage(), $this->lang->line('exercise_graphs_title'));
        }
    }

    public function teacher_progress() {
    	$this->load->model('mod_users');
    	$this->load->model('mod_classes');

        try {
            $this->mod_users->check_teacher();

//            $this->db->set_dbprefix('bol_');
            
            $classes = $this->mod_classes->get_named_classes_owned(false);
//            $classes = $this->mod_classes->get_named_classes_owned(!false);
            
            $this->load->view('view_top1', array('title' => $this->lang->line('teacher_graphs_title')));
            $this->load->view('view_top2');
            $this->load->view('view_menu_bar', array('langselect' => true));
            
            $center_text = $this->load->view('view_progress_teacher_classes', array('classes' => $classes), true); 

            $this->load->view('view_main_page', array('left_title' => $this->lang->line('select_class_heading'),
                                                      'left' => $this->lang->line('select_class_description'),
                                                      'center' => $center_text));
            $this->load->view('view_bottom');
        }
        catch (DataException $e) {
            $this->error_view($e->getMessage(), $this->lang->line('teacher_graphs_title'));
        }
    }

    public function teacher_time() {
    	$this->load->model('mod_users');
    	$this->load->model('mod_classes');
    	$this->load->model('mod_userclass');
    	$this->load->model('mod_statistics');
        $this->load->library('statistics_timeperiod',array('default_period'=>'long'));

        try {
            $this->mod_users->check_teacher();

//            $this->db->set_dbprefix('bol_');

            $this->load->helper('form');
            $this->load->library('form_validation');

            $this->form_validation->set_data($_GET);

            $classid = (int)$this->input->get('classid');
            $class = $this->mod_classes->get_class_by_id($classid);
//			if ($classid<=0 || ($class->ownerid!=$this->mod_users->my_id() && $this->mod_users->my_id()!=25)) // TODO remove 25
			if ($classid<=0 || $class->ownerid!=$this->mod_users->my_id())
				throw new DataException($this->lang->line('illegal_class_id'));
            
            $this->statistics_timeperiod->set_validation_rules();

			if ($this->form_validation->run()) {
                $this->statistics_timeperiod->ok_dates();

                $status = 1; // 1 = OK
                
                $students = $this->mod_userclass->get_named_users_in_class($classid);
                if (empty($students))
                    throw new DataException('No students in class');

                $student_ids = array();
                foreach ($students as $st)
                    $student_ids[] = (int)$st->userid;

                $templates = $this->mod_statistics->get_templates_for_class_and_students($classid,$student_ids);
                if (!empty($templates))
                    $durations = $this->mod_statistics->get_quizzes_duration($templates,
                                                                             $this->statistics_timeperiod->start_timestamp(),
                                                                             $this->statistics_timeperiod->end_timestamp());
                else
                    $durations = array();

                // What students actually have results?
                $real_students = array(); // Will be used as a set
                foreach ($durations as $d)
                    $real_students[$d->userid] = true;
                ksort($real_students);
                $number_students = count($real_students);
            
                // $dur[123456][55] will be the duration for user 55 in the week starting at UNIX time 123456
                // $total[123456]  will be the total duration for all users in the week starting at UNIX time 123456
                $dur = array();
                $total = array();

                $minweek = $this->statistics_timeperiod->start_week();
                $maxweek = $this->statistics_timeperiod->end_week();
                for ($w=$minweek; $w<$maxweek; $w+=Statistics_timeperiod::SECS_PER_WEEK) {
                    $dur[$w] = array();
                    $total[$w] = 0;
                    foreach ($real_students as $st => $ignore)
                        $dur[$w][$st] = 0;
                }
                
                foreach ($durations as $d) {
                    $hours = $d->duration / 3600;
                    $w = $this->statistics_timeperiod->last_monday((int)$d->start);
                    $dur[$w][$d->userid] += $hours;
                    $total[$w] += $hours;
                }

                // Get student names
                foreach ($students as $st)
                    if (isset($real_students[$st->userid]))
                        $real_students[$st->userid] = $st->name;
			}
            else {
                $this->statistics_timeperiod->default_dates();
                
                $real_students = null;
                $dur = null;
                $total = null;
                
                $status = 2; // 2 = Bad
            }

            // VIEW:
            $this->load->view('view_top1', array('title' => $this->lang->line('student_graphs_title'),
                                                 'js_list' => array('RGraph/libraries/RGraph.common.core.js',
                                                                    'RGraph/libraries/RGraph.bar.js',
                                                                    'RGraph/libraries/RGraph.common.dynamic.js',
                                                                    'RGraph/libraries/RGraph.common.tooltips.js',
                                                                    'RGraph/libraries/RGraph.common.key.js',
                                                                    'js/datepicker_period.js',
                                                                    'js/graphing.js',
                                                                    'js/handle_legend.js')));
            $this->load->view('view_top2');
            $this->load->view('view_menu_bar', array('langselect' => true));

            $center_text = $this->load->view('view_progress_teacher_time', array('status' => $status,
                                                                                 'classid' => $classid,
                                                                                 'classname' => $class->classname,
                                                                                 'students' => $real_students,
                                                                                 'start_date' => $this->statistics_timeperiod->start_string(),
                                                                                 'end_date' => $this->statistics_timeperiod->end_string(),
                                                                                 'dur' => $dur,
                                                                                 'total' => $total), true);

            $main_params = array('left_title' => $this->lang->line('select_period_heading'),
                                 'left' => $this->lang->line('time_period_description'),
                                 'center' => $center_text);

            if (array_sum($total)>0) {
                $main_params['extraleft'] = $this->load->view('view_progress_teacher_legend', array(), true);
                $main_params['extraleft_title'] = $this->lang->line('students');
            }
                
            $this->load->view('view_main_page', $main_params);
            $this->load->view('view_bottom');
        }
        catch (DataException $e) {
            $this->error_view($e->getMessage(), $this->lang->line('student_graphs_title'));
        }
    }
    
    public function teacher_exercises() {
    	$this->load->model('mod_users');
    	$this->load->model('mod_classes');
    	$this->load->model('mod_statistics');
        $this->load->library('statistics_timeperiod',array('default_period'=>'short'));

        try {
            $this->mod_users->check_teacher();

//            $this->db->set_dbprefix('bol_');


            $this->load->helper('form');
            $this->load->library('form_validation');
            $this->load->library('db_config');

            $this->form_validation->set_data($_GET);

            $classid = (int)$this->input->get('classid');
            $class = $this->mod_classes->get_class_by_id($classid);
//			if ($classid<=0 || ($class->ownerid!=$this->mod_users->my_id() && $this->mod_users->my_id()!=25)) // TODO remove 25
			if ($classid<=0 || $class->ownerid!=$this->mod_users->my_id())
				throw new DataException($this->lang->line('illegal_class_id'));

            $exercise_list = $this->mod_statistics->get_pathnames_for_class($classid);

            $this->statistics_timeperiod->set_validation_rules();
            $this->form_validation->set_rules('exercise', '', 'callback_always_true');  // Dummy rule. At least one rule is required
            $this->form_validation->set_rules('nongraded', '', 'callback_always_true');  // Dummy rule. At least one rule is required

            $nongraded = $this->input->get('nongraded')=='on';
            
			if ($this->form_validation->run()) {
                $this->statistics_timeperiod->ok_dates();

                $ex = $this->input->get('exercise');
                if (empty($ex)) {
                    $ex = '';
                    $status = 2; // 2=Initial display
                    $real_students = null;
                    $resall = null;
                    $resfeatall = null;
                    $featloc = null;
                }
                else {
                    // Find all user IDs and template IDs that match the specified pathname
                    $users_and_templs = $this->mod_statistics->get_users_and_templ($ex);

                    $resall = array();
                    $resfeatall = array();
                    $real_students = array(); // Will be used as a set

                    foreach ($users_and_templs as $uid => $templs) {
                        $see_nongraded = $nongraded && $this->mod_statistics->may_see_nongraded($uid, $ex);

                        $res = $this->mod_statistics->get_score_by_date_user_templ($uid,
                                                                                   $templs,
                                                                                   $this->statistics_timeperiod->start_timestamp(),
                                                                                   $this->statistics_timeperiod->end_timestamp(),
                                                                                   $see_nongraded);

                        $resfeat = $this->mod_statistics->get_features_by_date_user_templ($uid,
                                                                                          $templs,
                                                                                          $this->statistics_timeperiod->start_timestamp(),
                                                                                          $this->statistics_timeperiod->end_timestamp(),
                                                                                          $see_nongraded);

                        if (empty($res))
                            continue;
                        $resall[] = $res;
                        $resfeatall[] = $resfeat;
                        $real_students[$uid] = $see_nongraded;
                    }

                    $status = empty($resall) ? 0 : 1;  // 0=no data, 1=data

                    // Localize feature names
                    if (!empty($resfeatall)) {
                        // We assume that the underlying database information never changed
                        $dbnames = $this->mod_statistics->get_templ_db($templs);
                        $this->db_config->init_config($dbnames->dbname,$dbnames->dbpropname, $this->language);
                        $l10n = json_decode($this->db_config->l10n_json);
                        $featloc = $l10n->emdrosobject->{$dbnames->qoname}; // We only need localization of feature names
                    }
                    else
                        $featloc = null;
                    
                    // Get student names
                    foreach ($real_students as $uid => &$v)
                        $v = make_full_name($this->mod_users->get_user_by_id($uid)) . ($v ? ' *' : '');

                    // Because $users_and_temps is sorted by user ID, $real_students and $resall are sorted in the same order
                }
            }
            else {
                $this->statistics_timeperiod->default_dates();

                $ex = '';
                $status = 2; // 2=Initial display
                $real_students = null;
                $resall = null;
                $resfeatall = null;
                $featloc = null;
            }

            // VIEW:
            $this->load->view('view_top1', array('title' => $this->lang->line('exercise_graphs_title'),
                                                 'js_list' => array('RGraph/libraries/RGraph.common.core.js',
                                                                    'RGraph/libraries/RGraph.hbar.js',
                                                                    'RGraph/libraries/RGraph.scatter.js',
                                                                    'RGraph/libraries/RGraph.common.dynamic.js',
                                                                    'RGraph/libraries/RGraph.common.tooltips.js',
                                                                    'RGraph/libraries/RGraph.common.key.js',
                                                                    'js/datepicker_period.js',
                                                                    'js/graphing.js',
                                                                    'js/handle_legend.js')));

            $this->load->view('view_top2');
            $this->load->view('view_menu_bar', array('langselect' => true));
            
            $center_text = $this->load->view('view_progress_teacher_exercises', array('classid' => $classid,
                                                                                      'classname' => $class->classname,
                                                                                      'students' => $real_students,
                                                                                      'resscoreall' => $resall,
                                                                                      'resfeatall' => $resfeatall,
                                                                                      'featloc' => $featloc,
                                                                                      'status' => $status,
                                                                                      'quiz' => $ex,
                                                                                      'nongraded' => $nongraded,
                                                                                      'start_date' => $this->statistics_timeperiod->start_string(),
                                                                                      'end_date' => $this->statistics_timeperiod->end_string(),
                                                                                      'minpoint' => $this->statistics_timeperiod->start_timestamp(),
                                                                                      'maxpoint' => $this->statistics_timeperiod->end_timestamp(),
                                                                                      'exercise_list' => $exercise_list), true);

            $main_params = array('left_title' => $this->lang->line('select_period_heading'),
                                 'left' => $this->lang->line('time_period_description')
                                 . $this->lang->line('student_exercise_description'),
                                 'center' => $center_text);
            
            if ($status==1) {
                $main_params['extraleft'] = $this->load->view('view_progress_teacher_legend',
                                                              array('nongraded' => $nongraded),
                                                              true);
                $main_params['extraleft_title'] = $this->lang->line('students');
            }
            
            $this->load->view('view_main_page', $main_params);
            $this->load->view('view_bottom');
        }
        catch (DataException $e) {
            $this->error_view($e->getMessage(), $this->lang->line('exercise_graphs_title'));
        }
    }
    
}
