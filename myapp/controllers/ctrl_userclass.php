<?php
function realname_cmp($u1, $u2) {
    $un1 = "$u1->first_name $u1->last_name";
    $un2 = "$u2->first_name $u2->last_name";

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
        $this->load->model('mod_classes');
        $this->load->model('mod_userclass');
    }

    public function users_in_class() {
        try {
            $this->mod_users->check_admin();

            $classid = isset($_GET['classid']) ? intval($_GET['classid']) : 0;
        
            if ($classid<=0)
                throw new DataException('Illegal class ID');

            $class_info = $this->mod_classes->get_class_by_id($classid);

            $all_users = $this->mod_users->get_all_users();
            usort($all_users, 'realname_cmp');

            $old_users = $this->mod_userclass->get_users_in_class($classid);

            $this->load->helper('form');
            $this->load->library('form_validation');

            $this->form_validation->set_rules('inclass[]', '', '');
            
            if ($this->form_validation->run()) {
                $new_users = $this->input->post('inclass');
                if (!$new_users) // post() returns false when nothing is selected...
                    $new_users = array(); // ...so we set the array to empty

                $this->mod_userclass->update_users_in_class($classid, $old_users, $new_users);
                redirect('/classes');
            }
            else {
                // VIEW:
                $this->load->view('view_top1', array('title' => 'Edit class'));
                $this->load->view('view_top2');
                $this->load->view('view_menu_bar');
                
                $center_text = $this->load->view('view_edit_users_in_class',
                                                 array('classid' => $classid,
                                                       'classname' => $class_info->classname,
                                                       'allusers' => $all_users,
                                                       'old_users' => $old_users),
                                                 true);
             
                $this->load->view('view_main_page', array('left' => "<h1>Assign Users to Class</h1>
                                                                     <p>Select the users that should
                                                                     belong to the class '$class_info->classname'.</p>",
                                                          'center' => $center_text));
                $this->load->view('view_bottom');
                return;
            }
        }
        catch (DataException $e) {
            $this->error_view($e->getMessage(), 'Classes');
        }
    }

    public function classes_for_user() {
        try {
            $this->mod_users->check_admin();

            $userid = isset($_GET['userid']) ? intval($_GET['userid']) : 0;
        
            if ($userid<=0)
                throw new DataException('Illegal user ID');

            $user_info = $this->mod_users->get_user_by_id($userid);

            $all_classes = $this->mod_classes->get_all_classes();
            usort($all_classes, 'classname_cmp');
            $old_classes = $this->mod_userclass->get_classes_for_user($userid);

            $this->load->helper('form');
            $this->load->library('form_validation');

            $this->form_validation->set_rules('foruser[]', '', '');
            
            if ($this->form_validation->run()) {
                $new_classes = $this->input->post('foruser');
                if (!$new_classes) // post() returns false when nothing is selected...
                    $new_classes = array(); // ...so we set the array to empty

                $this->mod_userclass->update_classes_for_user($userid, $old_classes, $new_classes);
                redirect('/users');
            }
            else {
                // VIEW:
                $this->load->view('view_top1', array('title' => 'Edit user'));
                $this->load->view('view_top2');
                $this->load->view('view_menu_bar');
                
                $center_text = $this->load->view('view_edit_classes_for_user',
                                                 array('userid' => $userid,
                                                       'user_name' => "$user_info->first_name $user_info->last_name",
                                                       'allclasses' => $all_classes,
                                                       'old_classes' => $old_classes),
                                                 true);
             
                $this->load->view('view_main_page', array('left' =>  "<h1>Assign User to Classes</h1>
                                                                     <p>Select the classes to which 
                                                                     $user_info->first_name $user_info->last_name
                                                                     should belong.</p>",
                                                          'center' => $center_text));
                $this->load->view('view_bottom');
                return;
            }
        }
        catch (DataException $e) {
            $this->error_view($e->getMessage(), 'Classes');
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
            $old_classes = $this->mod_userclass->get_classes_for_user($userid);
            $avail_classes = array();

            foreach ($all_classes as $ix => $ac)
                if (!in_array($ac->id, $old_classes) && (empty($ac->enrol_before) ||self::before_date($ac->enrol_before)))
                    $avail_classes[] = $ac->id;

            // VIEW:
            $this->load->view('view_top1', array('title' => 'Enroll in Class'));
            $this->load->view('view_top2');
            $this->load->view('view_menu_bar');
                
            $center_text = $this->load->view('view_enroll_in_class',
                                             array('all_classes' => $all_classes,
                                                   'old_classes' => $old_classes,
                                                   'avail_classes' => $avail_classes),
                                             true);
             
            $this->load->view('view_main_page', array('left' =>  "<h1>Enroll in Classes</h1>",
                                                      'center' => $center_text));
            $this->load->view('view_bottom');
        }
        catch (DataException $e) {
            $this->error_view($e->getMessage(), 'Classes');
        }
    }

    private $enroll_class;

    public function class_password_check($password) {
        if ($password === $this->enroll_class->password)
            return true;
        else {
			$this->form_validation->set_message('class_password_check', 'Wrong class password');
			return false;
        }
    }


    public function enroll_in() {
        try {
            $this->mod_users->check_logged_in();

            $userid = $this->mod_users->my_id();
            $classid = isset($_GET['classid']) ? intval($_GET['classid']) : 0;

            $all_classes = $this->mod_classes->get_all_classes();
            $old_classes = $this->mod_userclass->get_classes_for_user($userid);
            $avail_classes = array();

            foreach ($all_classes as $ix => $ac)
                if (!in_array($ac->id, $old_classes))
                    $avail_classes[] = $ac->id;

            if (in_array($classid, $old_classes))
                throw new DataException('You are already enrolled in this class');

            if (!in_array($classid, $avail_classes))
                throw new DataException('You cannot enroll in this class');

            $this->enroll_class = $all_classes[$classid];

            $this->load->helper('form');
            $this->load->library('form_validation');

            $this->form_validation->set_rules('password', 'Class password', 'trim|required|callback_class_password_check');

            if (empty($this->enroll_class->password) || $this->form_validation->run()) {
                $this->mod_userclass->enroll_user_in_class($userid, $classid);

                // VIEW:
                $this->load->view('view_top1', array('title' => 'Enroll in Class'));
                $this->load->view('view_top2');
                $this->load->view('view_menu_bar');
                
                $this->load->view('view_main_page', array('left' => '<h1>Enrolled</h1>',
                                                          'center' => "<h1>You are now enrolled in 
                                                                       &ldquo;{$this->enroll_class->classname}&rdquo;</h1>"));
                $this->load->view('view_bottom');
            }
            else {
                // VIEW:
                $this->load->view('view_top1', array('title' => 'Enroll in Class'));
                $this->load->view('view_top2');
                $this->load->view('view_menu_bar');
                
                $center_text = $this->load->view('view_enroll_form',
                                                 array('classid' => $classid,
                                                       'classname' => $this->enroll_class->classname),
                                                 true);

                $this->load->view('view_main_page', array('left' => '<h1>Enter class password</h1>',
                                                          'center' => $center_text));
                $this->load->view('view_bottom');
            }
        }
        catch (DataException $e) {
            $this->error_view($e->getMessage(), 'Classes');
        }
    }

  }
