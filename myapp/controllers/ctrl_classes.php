<?php
function classname_cmp($c1, $c2) {
    if ($c1->classname === $c2->classname)
        return 0;
    return ($c1->classname < $c2->classname) ? -1 : 1;
}

class Ctrl_classes extends MY_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('mod_classes');
    }

	public function index() {
        $this->classes();
    }

    public function classes() {
        try {
            $this->mod_users->check_admin();

            $allclasses = $this->mod_classes->get_all_classes();
            usort($allclasses, 'classname_cmp');

            // VIEW:
            $this->load->view('view_top1', array('title' => 'classes'));
            $this->load->view('view_top2');
            $this->load->view('view_menu_bar');
            $this->load->view('view_confirm_dialog');
            $center_text = $this->load->view('view_class_list',
                                             array('allclasses' => $allclasses),
                                             true);
            $this->load->view('view_main_page', array('left' => '<h1>Class List</h1><p>Configure your classes</p>',
                                                      'center' => $center_text));
            $this->load->view('view_bottom');
        }
        catch (DataException $e) {
            $this->error_view($e->getMessage(), 'Classes');
        }
    }


    public function delete_class() {
        try {
            $this->mod_users->check_admin();

            $classid = isset($_GET['classid']) ? intval($_GET['classid']) : 0;
        
            if ($classid<=0)
                throw new DataException('Illegal class ID');

            $this->mod_classes->delete_class($classid);
            redirect('/classes');
        }
        catch (DataException $e) {
            $this->error_view($e->getMessage(), 'Classes');
        }
    }

    private static $days_in_month = array(31,28,31,30,31,30,31,31,30,31,30,31);

    public function date_valid_check($date) {
        if (empty($date))
            return true;

        $pat = '/^([0-9][0-9][0-9][0-9])-([01][0-9])-([0-3][0-9])$/'; // Pattern to match dates
        if (!preg_match($pat, $date, $match)) {
			$this->form_validation->set_message('date_valid_check',
                                                'The \'Enroll before\' field, if specified, ' 
                                                . 'must have the form YYYY-MM-DD (for example, 2013-12-25).');
            return false;
        }

        $year = $match[1];
        $month = $match[2];
        $day =  $match[3];

        $is_leap = $year%4 == 0; // That's all we care about now
        
        if ($is_leap && $month==2 && $day==29)
            return true;

        if ($year<2000 || $year>2099 || $month<1 || $month>12 || $day<1 || $day>self::$days_in_month[$month-1]) {
			$this->form_validation->set_message('date_valid_check', 'The \'Enrol before\' date is invalid.');
            return false;
        }

        return true;
    }

   public function edit_one_class() {
        try {
            $this->mod_users->check_admin();

            $classid = isset($_GET['classid']) ? intval($_GET['classid']) : 0;
        
            if ($classid==0)
                throw new DataException('Illegal class ID');

            $class_info = $this->mod_classes->get_class_by_id($classid);

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

            $this->form_validation->set_message('is_unique', 'The class name "'.$this->input->post('classname').'" is already in use');
            $this->form_validation->set_rules('classname', 'Class name', "trim|required|strip_tags$is_unique");
            $this->form_validation->set_rules('password', 'Class password', 'trim|strip_tags');
            $this->form_validation->set_rules('enrol_before', 'Enrol before', 'trim|strip_tags|callback_date_valid_check');

            if ($this->form_validation->run()) {
                $class_info->classname = $this->input->post('classname');
                $class_info->password = $this->input->post('password');
                $class_info->enrol_before = $this->input->post('enrol_before');

                $query = $this->mod_classes->set_class($class_info);

                redirect('/classes');
            }
            else {
                // VIEW:
                $this->load->view('view_top1', array('title' => 'Edit user'));
                $this->load->view('view_top2');
                $this->load->view('view_menu_bar');
                
                $center_text = $this->load->view('view_edit_class',array('classid' => $classid, 'class_info' => $class_info), true);

                $this->load->view('view_main_page', array('left' => '<h1>Edit class information</h1>',
                                                          'center' => $center_text));
                $this->load->view('view_bottom');
            }
        }
        catch (DataException $e) {
            $this->error_view($e->getMessage(), 'Classes');
        }
    }

  }