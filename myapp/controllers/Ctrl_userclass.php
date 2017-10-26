<?php
function realname_cmp($u1, $u2) {
    $un1 = make_full_name($u1);
    $un2 = make_full_name($u2);

    if ($un1 === $un2)
        return 0;
    return ($un1 < $un2) ? -1 : 1;
}

function classname_cmp($c1, $c2) {
    if ($c1->classname === $c2->classname)
        return 0;
    return ($c1->classname < $c2->classname) ? -1 : 1;
}



/// This class manages the relationship between users and classes.
class Ctrl_userclass extends MY_Controller {
    public function __construct() {
        parent::__construct();
        $this->lang->load('userclass', $this->language);
        $this->lang->load('class', $this->language);
        $this->load->model('mod_classes');
        $this->load->model('mod_userclass');
    }

    public function users_in_class() {
        try {
            $this->mod_users->check_teacher();

            $classid = isset($_GET['classid']) ? intval($_GET['classid']) : 0;
        
            if ($classid<=0)
                throw new DataException($this->lang->line('illegal_class_id'));

            $class_info = $this->mod_classes->get_class_by_id($classid);

            if ($class_info->ownerid!=$this->mod_users->my_id() && !$this->mod_users->is_admin())
                throw new DataException($this->lang->line('not_class_owner'));


            $all_users = $this->mod_users->get_all_users();
            usort($all_users, 'realname_cmp');

            $old_users = $this->mod_userclass->get_users_in_class($classid);

            $this->load->helper('form');
            $this->load->library('form_validation');

            $this->form_validation->set_rules('inclass[]', '', 'callback_always_true');  // Dummy rule. At least one rule is required
            
            if ($this->form_validation->run()) {
                $new_users = $this->input->post('inclass');
                if (!$new_users) // post() returns false when nothing is selected...
                    $new_users = array(); // ...so we set the array to empty

                $this->mod_userclass->update_users_in_class($classid, $old_users, $new_users);
                redirect('/classes');
            }
            else {
                // VIEW:
                $this->load->view('view_top1', array('title' => $this->lang->line('uc_edit_class')));
                $this->load->view('view_top2');
                $this->load->view('view_menu_bar', array('langselect' => true));
                
                $center_text = $this->load->view('view_edit_users_in_class',
                                                 array('classid' => $classid,
                                                       'classname' => $class_info->classname,
                                                       'allusers' => $all_users,
                                                       'old_users' => $old_users),
                                                 true);
             
                $this->load->view('view_main_page', array('left_title' => $this->lang->line('assign_users_to_class'),
                                                          'left' => '<p>'
                                                          . sprintf($this->lang->line('select_users_for_class'),$class_info->classname)
                                                          .'</p>',
                                                          'center' => $center_text));
                $this->load->view('view_bottom');
                return;
            }
        }
        catch (DataException $e) {
            $this->error_view($e->getMessage(), $this->lang->line('uc_classes'));
        }
    }

    // Dummy validation function
    public function always_true($field) {
        return true;
    }

    public function classes_for_user() {
        try {
            $this->mod_users->check_teacher();

            $userid = isset($_GET['userid']) ? intval($_GET['userid']) : 0;
            $offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
            $orderby = isset($_GET['orderby']) ? $_GET['orderby'] : 'username';
            $sortorder = isset($_GET['desc']) ? 'desc' : 'asc';

            $extras = "offset=$offset&orderby=$orderby&$sortorder";


            if ($userid<=0)
                throw new DataException($this->lang->line('illegal_user_id'));

            $user_info = $this->mod_users->get_user_by_id($userid);

            $all_classes = $this->mod_classes->get_all_classes();
            $owned_classes = $this->mod_classes->get_classes_owned();
            $old_classes = $this->mod_userclass->get_classes_for_user($userid);
            usort($all_classes, 'classname_cmp');

            $this->load->helper('form');
            $this->load->library('form_validation');

            $this->form_validation->set_rules('foruser[]', '', 'callback_always_true');  // Dummy rule. At least one rule is required

            if ($this->form_validation->run()) {
                $new_classes = $this->input->post('foruser');
                if (!$new_classes) // post() returns false when nothing is selected...
                    $new_classes = array(); // ...so we set the array to empty

                $this->mod_userclass->update_classes_for_user($userid, $old_classes, $new_classes, $owned_classes);
                redirect("/users?$extras");
            }
            else {
                // VIEW:
                $this->load->view('view_top1', array('title' => $this->lang->line('uc_edit_user')));
                $this->load->view('view_top2');
                $this->load->view('view_menu_bar', array('langselect' => true));
                
                $center_text = $this->load->view('view_edit_classes_for_user',
                                                 array('userid' => $userid,
                                                       'extras' => $extras,
                                                       'user_name' => make_full_name($user_info),
                                                       'allclasses' => $all_classes,
                                                       'owned_classes' => $owned_classes,
                                                       'old_classes' => $old_classes),
                                                 true);
             
                $this->load->view('view_main_page', array('left_title' => $this->lang->line('assign_user_to_classes'),
                                                          'left' => '<p>'
                                                          . sprintf($this->lang->line('select_classes_for_user'),make_full_name($user_info))
                                                          .'<p>',
                                                          'center' => $center_text));
                $this->load->view('view_bottom');
                return;
            }
        }
        catch (DataException $e) {
            $this->error_view($e->getMessage(), $this->lang->line('uc_classes'));
        }
    }

    // $date is in the form YYYY-MM-DD
    private static function before_date($date) {
        assert('$date[4]==="-" && $date[7]==="-"');

        $stringdate = substr($date,0,4) . substr($date,5,2) . substr($date,8,2);
        date_default_timezone_set('Europe/Copenhagen');
        $today = date('Ymd');
        return $today <= $stringdate;
    }
        
    public function enroll() {
        try {
            $this->mod_users->check_logged_in();

            $userid = $this->mod_users->my_id();

            $all_classes = $this->mod_classes->get_all_classes();
            $old_classes = $this->mod_userclass->get_classes_and_access_for_user($userid);
            $avail_classes = array();

            foreach ($all_classes as $ix => $ac)
                if (!array_key_exists($ac->clid, $old_classes) && (empty($ac->enrol_before) ||self::before_date($ac->enrol_before)))
                    $avail_classes[] = $ac->clid;

            // VIEW:
            $this->load->view('view_top1', array('title' => $this->lang->line('enroll_in_class')));
            $this->load->view('view_top2');
            $this->load->view('view_menu_bar', array('langselect' => true));
            $this->load->view('view_confirm_dialog');
                
            $center_text = $this->load->view('view_enroll_in_class',
                                             array('all_classes' => $all_classes,
                                                   'old_classes' => $old_classes,
                                                   'avail_classes' => $avail_classes,
                                                   'curdir' => null,
                                                   'dir' => null),
                                             true);
             
            $this->load->view('view_main_page', array('left_title' =>  $this->lang->line('enroll_in_classes'),
                                                      'center' => $center_text));
            $this->load->view('view_bottom');
        }
        catch (DataException $e) {
            $this->error_view($e->getMessage(), $this->lang->line('classes'));
        }
    }
    
    public function enroll_by_folder() {
        try {
            $this->load->model('mod_classdir');
            $this->mod_users->check_logged_in();

            if (!isset($_GET['dir']) || !isset($_GET['curdir']))
                throw new DataException($this->lang->line('missing_folder_name'));
            
            $userid = $this->mod_users->my_id();

            $classids_for_dir = $this->mod_classdir->get_classes_for_dir($_GET['dir']);
            
            // If everybody can access the folder, this is a faked request
            if (in_array(0,$classids_for_dir))
                throw new DataException(translate('Folder does not require enrollment'));
            
            $all_classes = $this->mod_classes->get_all_classes();
            $old_classes = $this->mod_userclass->get_classes_for_user($userid);

            $avail_classes = array();
            foreach ($classids_for_dir as $cid) {
                // If the user is already enrolled in a relevant class, this is a faked request
                if (in_array($cid, $old_classes))
                    throw new DataException($this->lang->line('already_enrolled'));

                if (empty($all_classes[$cid]->enrol_before) || self::before_date($all_classes[$cid]->enrol_before))
                    $avail_classes[] = $cid;
            }

            if (empty($avail_classes))
                throw new DataException(translate('No classes with access to the folder are available for enrollment'));

            
            // VIEW:
            $this->load->view('view_top1', array('title' => $this->lang->line('enroll_in_class')));
            $this->load->view('view_top2');
            $this->load->view('view_menu_bar', array('langselect' => true));
                
            $center_text = $this->load->view('view_enroll_by_folder',
                                             array('all_classes' => $all_classes,
                                                   'old_classes' => $old_classes,
                                                   'avail_classes' => $avail_classes,
                                                   'curdir' => $_GET['curdir'],
                                                   'dir' => $_GET['dir']),
                                             true);
             
            $this->load->view('view_main_page', array('left_title' =>  $this->lang->line('enroll_in_classes'),
                                                      'center' => $center_text));
            $this->load->view('view_bottom');
        }
        catch (DataException $e) {
            $this->error_view($e->getMessage(), $this->lang->line('classes'));
        }
    }

    private $enroll_class;

    public function class_password_check($password) {
        if ($password === $this->enroll_class->clpass)
            return true;
        else {
			$this->form_validation->set_message('class_password_check', $this->lang->line('wrong_class_password'));
			return false;
        }
    }


    public function enroll_in() {
        try {
            $this->mod_users->check_logged_in();
            $this->load->helper('varset');

            $userid = $this->mod_users->my_id();
            $classid = isset($_GET['classid']) ? (int)$_GET['classid'] : 0;

            $all_classes = $this->mod_classes->get_all_classes();
            $old_classes = $this->mod_userclass->get_classes_for_user($userid);
            $avail_classes = array();

            foreach ($all_classes as $ix => $ac)
                if (!in_array($ac->id, $old_classes))
                    $avail_classes[] = $ac->clid;

            if (in_array($classid, $old_classes))
                throw new DataException($this->lang->line('already_enrolled'));

            if (!in_array($classid, $avail_classes))
                throw new DataException($this->lang->line('cannot_enroll'));

            $this->enroll_class = $all_classes[$classid];

            $this->load->helper('form');
            $this->load->library('form_validation');

            $this->form_validation->set_rules('password', $this->lang->line('class_password'), 'trim|required|callback_class_password_check');

            if (empty($this->enroll_class->clpass) || $this->form_validation->run()) {
                $this->mod_userclass->enroll_user_in_class($userid, $classid);

                if (empty($_GET['dir'])) // Enrolling from the 'enroll' menu
                    header("Location: " . site_url('/userclass/enroll'));

                // The following code is used when enrolling from a folder
                // VIEW:
                $this->load->view('view_top1', array('title' => $this->lang->line('enroll_in_class')));
                $this->load->view('view_top2');
                $this->load->view('view_menu_bar', array('langselect' => true));


                $center_text = $this->load->view('view_enrolled',
                                                 array('classname' => $this->enroll_class->classname,
                                                       'dir' => set_or_default($_GET['dir'],null)),
                                             true);


                
                $this->load->view('view_main_page', array('left_title' => $this->lang->line('enrolled'),
                                                          'center' => $center_text));

                $this->load->view('view_bottom');


            }
            else {
                // VIEW:
                $this->load->view('view_top1', array('title' => $this->lang->line('enroll_in_class')));
                $this->load->view('view_top2');
                $this->load->view('view_menu_bar', array('langselect' => true));
                
                $center_text = $this->load->view('view_enroll_form',
                                                 array('classid' => $classid,
                                                       'classname' => $this->enroll_class->classname,
                                                       'curdir' => set_or_default($_GET['curdir'],null),
                                                       'dir' => set_or_default($_GET['dir'],null)),
                                                 true);

                $this->load->view('view_main_page', array('left_title' => $this->lang->line('enter_class_password'),
                                                          'center' => $center_text));
                $this->load->view('view_bottom');
            }
        }
        catch (DataException $e) {
            $this->error_view($e->getMessage(), $this->lang->line('classes'));
        }
    }

    public function manage_access() {
        try {
            $this->mod_users->check_logged_in();
            $this->load->helper('varset');

            $userid = $this->mod_users->my_id();
            $classid = isset($_GET['classid']) ? (int)$_GET['classid'] : 0;

            $enrolled_in = $this->mod_userclass->get_classes_for_user($userid);

            if (!in_array($classid, $enrolled_in))
                throw new DataException($this->lang->line('not_enrolled'));
            
            $this->mod_userclass->change_access($userid, $classid, set_or_default($_GET['grant'],0));
            header("Location: " . site_url('/userclass/enroll'));
        }
        catch (DataException $e) {
            $this->error_view($e->getMessage(), $this->lang->line('classes'));
        }
    }

    public function unenroll_from() {
        try {
            $this->mod_users->check_logged_in();
            $this->load->helper('varset');

            $userid = $this->mod_users->my_id();
            $classid = isset($_GET['classid']) ? (int)$_GET['classid'] : 0;

            $enrolled_in = $this->mod_userclass->get_classes_for_user($userid);

            if (!in_array($classid, $enrolled_in))
                throw new DataException($this->lang->line('not_enrolled'));

            $this->mod_userclass->unenroll_user_from_class($userid, $classid);
            header("Location: " . site_url('/userclass/enroll'));
        }
        catch (DataException $e) {
            $this->error_view($e->getMessage(), $this->lang->line('classes'));
        }
    }

  }
