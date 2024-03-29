<?php
function classname_cmp($c1, $c2) {
    if ($c1->classname === $c2->classname)
        return 0;
    return ($c1->classname < $c2->classname) ? -1 : 1;
}

class Ctrl_classes extends MY_Controller {
    public function __construct() {
        parent::__construct();
        $this->lang->load('class', $this->language);
        $this->load->model('mod_classes');
    }

	public function index() {
        $this->classes();
    }

    public function classes() {
        try {
            $this->mod_users->check_teacher();
            $this->lang->load('owner', $this->language);

            if ($this->mod_users->is_admin())
                $teachers = $this->mod_users->get_teachers();
            else
                $teachers = array();

            $allclasses = $this->mod_classes->get_all_classes();
            usort($allclasses, 'classname_cmp');

            // VIEW:
            $this->load->view('view_top1', array('title' => $this->lang->line('classes')));
            $this->load->view('view_top2');
            $this->load->view('view_menu_bar', array('langselect' => true));
            $this->load->view('view_confirm_dialog');
            $this->load->view('view_alert_dialog');

            $center_text = $this->load->view('view_class_list',
                                             array('allclasses' => $allclasses,
                                                   'teachers' => $teachers,
                                                   'myid' => $this->mod_users->my_id(),
                                                   'isadmin' => $this->mod_users->is_admin()),
                                             true);
            $this->load->view('view_main_page', array('left_title' => $this->lang->line('class_list'),
                                                      'left' => $this->lang->line('configure_your_classes'),
                                                      'center' => $center_text));
            $this->load->view('view_bottom');
        }
        catch (DataException $e) {
            $this->error_view($e->getMessage(), $this->lang->line('classes'));
        }
    }

    public function change_owner() {
        try {
            $this->lang->load('owner', $this->language);
            $this->mod_users->check_admin();

            $classid = isset($_GET['classid']) ? intval($_GET['classid']) : 0;
            if ($classid<=0)
                throw new DataException($this->lang->line('illegal_class_id'));

            $newowner = isset($_GET['newowner']) ? intval($_GET['newowner']) : 0;
            if ($newowner<=0)
                throw new DataException($this->lang->line('illegal_user_id'));

            $this->mod_classes->chown_class($classid, $newowner);

            redirect('/classes');
        }
        catch (DataException $e) {
            $this->error_view($e->getMessage(), $this->lang->line('classes'));
        }
    }


    public function delete_class() {
        try {
            $this->mod_users->check_teacher();

            $classid = isset($_GET['classid']) ? intval($_GET['classid']) : 0;
        
            if ($classid<=0)
                throw new DataException($this->lang->line('illegal_class_id'));

            $class_info = $this->mod_classes->get_class_by_id($classid);

            if ($class_info->ownerid!=$this->mod_users->my_id() && !$this->mod_users->is_admin())
                throw new DataException($this->lang->line('not_class_owner'));

            $this->mod_classes->delete_class($classid);
            redirect('/classes');
        }
        catch (DataException $e) {
            $this->error_view($e->getMessage(), $this->lang->line('classes'));
        }
    }

    private static $days_in_month = array(31,28,31,30,31,30,31,31,30,31,30,31);

    public function date_valid_check($date) {
        if (empty($date))
            return true;

        $pat = '/^([0-9][0-9][0-9][0-9])-([0-9][0-9])-([0-9][0-9])$/'; // Pattern to match date format (not date values)
        if (!preg_match($pat, $date, $match)) {
			$this->form_validation->set_message('date_valid_check', $this->lang->line('date_invalid_format'));
            return false;
        }

        $year = $match[1];
        $month = $match[2];
        $day =  $match[3];

        $is_leap = $year%4 == 0; // That's all we care about now
        
        if ($is_leap && $month==2 && $day==29)
            return true;

        if ($year<2000 || $year>2099 || $month<1 || $month>12 || $day<1 || $day>self::$days_in_month[$month-1]) {
			$this->form_validation->set_message('date_valid_check', $this->lang->line('date_invalid'));
            return false;
        }

        return true;
    }
    public function add_one_grader() {
        // load the form helper and validation library
        $this->load->helper('form');
        $this->load->library('form_validation');

        // set validation rules for the grader username
        $this->form_validation->set_rules('grader_username', $this->lang->line('grader_username'), "trim|required");


        // get the classid from the URL
        $classid = isset($_GET['classid']) ? intval($_GET['classid']) : 0;

        // from the id get the class name
        $class_info = $this->mod_classes->get_class_by_id($classid);
        $class_name = $class_info->classname;

		$dne_errorname = 'dne_error';

        if ($this->form_validation->run()) {
          
            
            // Get the grader ID from the form submission
            $grader_query = $this->db->select('id')->from('user')->where('username',$this->input->post('grader_username'))->get();
            $grader_result = $grader_query->result();
			if(count($grader_result) == 0) {
				// redirect to the classes list page
				redirect('/classes/add_one_grader?classid=' . $classid . '&' . $dne_errorname . '=1' . '&grader_username=' . $this->input->post('grader_username'));	
			}
			else {
				$grader_id = $grader_result[0]->id;
				// Insert the new grader into the grader table
				$insert_data = array('classid' => $classid, 'graderid' => $grader_id);
				$this->db->insert('grader', $insert_data);
					
				// redirect to the classes list page
				redirect('/classes');
			}
			
        }
        else {
			// check if the entered username does not exist
			$input_data = parse_url($_SERVER['REQUEST_URI'] );
			parse_str($input_data["query"], $params);
			if(array_key_exists($dne_errorname, $params)){
				$custom_error = 'There is no user with the username ' . $params["grader_username"];
				$custom_error = sprintf($this->lang->line('no_user_found'), $params["grader_username"]);
			}
			else{
				$custom_error = '';
			}
			 
			


            // if the grader username has not been entered, then display the add grader form to the user
            $top_text = $this->lang->line('add_grader');
            $this->load->view('view_top1', array('title' => $top_text));
            $this->load->view('view_top2');
            $this->load->view('view_menu_bar', array('langselect' => true)); 
			
            $center_text = $this->load->view('view_add_grader', array('class_name' => $class_name, 'classid' => $classid, 'custom_error' => $custom_error), true);
            //$center_text = "<div class=\"alert alert-danger\">$valerr</div>\n";
			$this->load->view('view_main_page', array('left_title' => $top_text, 'center' => $center_text));
            $this->load->view('view_bottom');

        }
        
    }

    public function edit_one_class() {
        try {
            $this->mod_users->check_teacher();

            $classid = isset($_GET['classid']) ? intval($_GET['classid']) : 0;
        
            if ($classid==0)
                throw new DataException($this->lang->line('illegal_class_id'));

            $class_info = $this->mod_classes->get_class_by_id($classid);

            if ($class_info->ownerid!=$this->mod_users->my_id() && !$this->mod_users->is_admin())
                throw new DataException($this->lang->line('not_class_owner'));
            

            $this->load->helper('form');
            $this->load->library('form_validation');

            // For pedagogical reasons, the set_rules() functions must be called in the order the
            // fields appear in the form

            if ($classid<0) // If this is a new class, its name must be unique
                $is_unique = '|is_unique[class.classname]';
            elseif ($this->input->post('classname') != $class_info->classname) // If the class name is modified, its name must be unique
                $is_unique = '|is_unique[class.classname]';
            else // If the class name is not modified, don't check for uniqueness
                $is_unique = '';

            $this->form_validation->set_message('is_unique', sprintf($this->lang->line('class_name_used'), $this->input->post('classname')));
            $this->form_validation->set_rules('classname', $this->lang->line('class_name'), "trim|required|strip_tags$is_unique");
            $this->form_validation->set_rules('password', $this->lang->line('class_pw'), 'trim|strip_tags');
            $this->form_validation->set_rules('enrol_before', $this->lang->line('enroll_before'), 'trim|strip_tags|callback_date_valid_check');

            if ($this->form_validation->run()) {
                $class_info->classname = $this->input->post('classname');
                $class_info->password = $this->input->post('password');
                $class_info->enrol_before = $this->input->post('enrol_before');

                $query = $this->mod_classes->set_class($class_info);

                redirect('/classes');
            }
            else {
                // VIEW:
                $this->load->view('view_top1', array('title' => $this->lang->line('edit_class')));
                $this->load->view('view_top2');
                $this->load->view('view_menu_bar', array('langselect' => true));
                
                $center_text = $this->load->view('view_edit_class',array('classid' => $classid, 'class_info' => $class_info), true);

                $this->load->view('view_main_page', array('left_title' => $this->lang->line('edit_class_information'),
                                                          'center' => $center_text));
                $this->load->view('view_bottom');
            }
        }
        catch (DataException $e) {
            $this->error_view($e->getMessage(), $this->lang->line('classes'));
        }
    }

  }