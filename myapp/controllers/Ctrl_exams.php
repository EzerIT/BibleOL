<?php

class Ctrl_exams extends MY_Controller
{
    public $loc; // Localization

    public function __construct()
    {
        parent::__construct();

        $this->lang->load('exams', $this->language);
        $this->load->model('mod_classes');
        $this->load->model('mod_exams');
        $this->load->model('mod_quizpath');
        $this->load->model('mod_userclass');
        $this->load->helper('varset');
    }

    public function index()
    {
        $this->show_files();
    }

    public function build()
    {
        $exam = $_POST["exam"];
        $ename = $_POST["name"];
        $path = __DIR__."/../../exam/" . $ename;
        if (!file_exists($path)) {
            mkdir($path);
        }
        foreach ($exam as $url) {
            copy($url, $path . "/" . basename($url));
        }
        print($ename);
        exit();
    }


    public function active_exams()
    {
      try {
        if (!($this->mod_users->is_logged_in())) throw new DataException($this->lang->line('must_be_logged_in'));

        $this->load->model('mod_askemdros');

        $exams_per_page = $this->config->item('exams_per_page');
        $exam_count = $this->mod_exams->count_exams();
        $page_count = intval(ceil($exam_count/$exams_per_page));

        $offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
        if ($offset>=$page_count)
            $offset = $page_count-1;
        if ($offset<0)
            $offset = 0;

        if (isset($_GET['orderby']) && in_array($_GET['orderby'],
                                                array('exam_name', 'owner'), true))
            $orderby = $_GET['orderby'];
        else
            $orderby = 'exam_name';

        $sortorder = isset($_GET['desc']) ? 'desc' : 'asc';


        /* PUT IN A MODEL
         * NEED TO CREATE Mod_exams_active
         */
        $active_exams_list = array();
        $future_exams_list = array();
        $past_exams_list = array();
        $user_id = $this->mod_users->my_id();

        # Get classes user is part of.
        if ($this->mod_users->is_teacher())
          $owned_classes = $this->mod_classes->get_classes_owned();
        else
          $owned_classes = $this->mod_userclass->get_classes_for_user($user_id);

        foreach ($owned_classes as $class_id){
          //$class_id = $class_row->id;
          $active_exam_query = $this->db->get_where('exam_active', array('class_id' => $class_id))->result();
          foreach ($active_exam_query as $exam_row) {
            if ($exam_row->exam_end_date > date('Y-m-d') ||
                ($exam_row->exam_end_date == date('Y-m-d') && $exam_row->exam_end_time > date('G:i'))){
              if ($exam_row->exam_start_date < date('Y-m-d') ||
                  ($exam_row->exam_start_date == date('Y-m-d') && $exam_row->exam_start_time <= date('G:i'))){
                array_push($active_exams_list, $exam_row);
              } else {
                array_push($future_exams_list, $exam_row);
              }
            } else {
              array_push($past_exams_list, $exam_row);
            }
          }
        }




        $javascripts = array('jstree/jquery.jstree.js',
                          'ckeditor/ckeditor.js',
                          'ckeditor/adapters/jquery.js',
                          'js/editquiz.js');

        $this->load->view('view_top1', array('title' => $this->lang->line('active_exams'),
                                                            'css_list' => array('styles/jstree.css'),
                                                            'js_list' => $javascripts));
        $this->load->view('view_font_css', array('fonts' => $this->mod_askemdros->font_selection));
        $this->load->view('view_top2');
        $this->load->view('view_menu_bar', array('langselect' => false));
        $this->load->view('view_alert_dialog');

        // Main view of the page.
        $center_text = $this->load->view(
            'view_active_exams',
            array(
              'active_exams_list' => $active_exams_list,
              'future_exams_list' => $future_exams_list,
              'offset' => $offset,
              'orderby' => $orderby,
              'page_count' => $page_count,
              'past_exams_list' => $past_exams_list,
              'sortorder' => $sortorder,
            ),
            true
        );
        $this->load->view('view_main_page', array('left_title' => $this->lang->line('active_exams'),
            'left' => $this->lang->line('active_exams_description'),
            'center' => $center_text
        ));

        $this->load->view('view_bottom');
      } catch (DataException $e) {
        $this->error_view($e->getMessage(), $this->lang->line('exam_mgmt'));
      }
    }


    // Create xml configuration file
    public function create_config_file($examname, array $exercises)
    {

            // Set up XML file
        $dom = new DOMDocument();
        $dom->encoding = 'utf-8';
        $dom->xmlVersion = '1.0';
        $dom->formatOutput = true;
        $dom->preserveWhiteSpace = false;
        $now = date('Y-m-d H:i:s');

        // Add root tags to XML file
        $root = $dom->createElement('exam');
        $dom->appendChild($root);
        $examname_node = $dom->createElement('examname', $examname);
        $root->appendChild($examname_node);
        $teacher_id = $dom->createElement('teacher_id', $this->mod_users->my_id());
        $root->appendChild($teacher_id);
        $description = $dom->createElement('description', 'Description');
        $root->appendChild($description);


        // Add exercise tags to XML file
        // <exercise numq="10" time="0">exercisename.3et</exercise>
        $order = 1;
        foreach ($exercises as $key => $value) {
            if ($key != 0) {
                $value = str_replace('"', '', $value);
                print($value);
                $exercise_node = $dom->createElement('exercise');
                $root->appendChild($exercise_node);
                $exercise_name = $dom->createElement('exercisename', $value);
                $exercise_node->appendChild($exercise_name);
                $numq_node = $dom->createElement('numq', '0');
                $exercise_node->appendChild($numq_node);
                $weight_node = $dom->createElement('weight', '1');
                $exercise_node->appendChild($weight_node);
                $order_node = $dom->createElement('order', $order);
                $exercise_node->appendChild($order_node);

                $order = $order + 1;
            }
        }

        $dom->save('/var/www/BibleOL/exam/'.$examname.'/config.xml');
        return $dom->saveXML();
    }


    /**
     * First checks if the user is a teacher
     * Creates a new directory with the same name as the exam
     * Copies exercises that are currently in exercise_list to the new directory
     */
    public function create_exam()
    {
        try {
            $this->mod_users->check_teacher();

            if (isset($_POST['create_exam'])) {
                $create = trim($_POST['create_exam']);

                if (preg_match('|[/?*;{}"\'\\\\]|', $create)) {
                    throw new DataException($this->lang->line('illegal_char_folder_name'));
                }

                $exercise_lst = $_POST['exercise_list'];

                $base_pth = '/var/www/BibleOL/';

                $exam_loc = $base_pth.'exam/'.$create;

                // chmod 7-7 required
                mkdir($exam_loc);

                $ex_ar = explode(',', $exercise_lst);


                $ex_pth = $base_pth . 'quizzes';

                foreach ($ex_ar as $key => $exrcs) {
                    $exrcs = str_replace('"', '', $exrcs);
                    $org_pth = $ex_pth . "/" . $exrcs;
                    if (($exrcs) != 'undefined') {
                        $new_pth = $exam_loc . "/" . basename($exrcs);
                        copy($org_pth, $new_pth);
                    }
                }

                $this->create_config_file($create, $ex_ar);

                $xml = simplexml_load_file($exam_loc . "/config.xml") or die("error");

              	$data = array(
              		'exam_name' => $create,
              		'ownerid' => $this->mod_users->my_id(),
              		'pathname' => 'exam/' . $create,
              		'examcode' => $xml->asXML(),
              		'examcodehash' => hash("md5", $xml)
              	);
                $this->db->insert('bol_exam', $data);

                redirect("/exams/edit_exam?exam=$create");
            }
        } catch (DataException $e) {
            $this->error_view($e->getMessage(), $this->lang->line('illegal_char_folder_name'));
        }
    }


    // Process take exam form when submitted on
    // manage_exams page.
    public function create_exam_instance()
    {
      try {
        $this->mod_users->check_teacher();

        echo "Redirecting...";
        var_dump($_GET);

        $exam_name = $_GET["exname"];
        // Get class id from class name.
        $class_name = $_GET["class_select"];
        $query = $this->db->get_where('class', array('classname' => $class_name));
        $class_id = $query->row()->id;
        $exam_start_date = $_GET["start_date"];
        $exam_end_date = $_GET["end_date"];
        $exam_length = $_GET["duration"];
        $exam_start_time = $_GET["start_time"];
        $exam_end_time = $_GET["end_time"];

        $data = array(
          'exam_name' => $exam_name,
          'class_id' => $class_id,
          'exam_start_date' => $exam_start_date,
          'exam_end_date' => $exam_end_date,
          'exam_length' => $exam_length,
          'exam_start_time' => $exam_start_time,
          'exam_end_time' => $exam_end_time
        );



        $this->db->insert('exam_active', $data);

        redirect("/exams/active_exams");


      } catch (DataException $e) {
        $this->error_view($e->getMessage(), $this->lang->line('exam_mgmt'));
      }

    }


    // DELETE EXISTING EXAM
    public function delete_exam(){
        $this->mod_users->check_teacher();

        $exname = $_POST["exname"];

        # Remove exam folder.
        $expath = '/var/www/BibleOL/exam/'.$exname;
        array_map('unlink', glob("$expath/*.*"));
        rmdir($expath);

        # Remove exam from database.
        $this->db->delete('bol_exam', array('exam_name' => $exname));

        redirect("/exams");
    }


    // EDIT EXISTING EXAM
    // Also used in exam creation.
    public function edit_exam()
    {
      try {
        $this->mod_users->check_teacher();

        $this->load->model('mod_askemdros');
        $this->load->model('mod_localize');

        $javascripts = array('jstree/jquery.jstree.js',
                                 'ckeditor/ckeditor.js',
                                 'ckeditor/adapters/jquery.js',
                                 'js/editquiz.js');
        // View
        $this->load->view('view_top1', array('title' => $this->lang->line('edit_exam'),
                                                             'css_list' => array('styles/jstree.css'),
                                                             'js_list' => $javascripts));
        $this->load->view('view_font_css', array('fonts' => $this->mod_askemdros->font_selection));
        $this->load->view('view_top2');
        $this->load->view('view_menu_bar', array('langselect' => false));
        $this->load->view('view_alert_dialog');

        // Main view of the page.
        $center_text = $this->load->view(
                'view_edit_exam',
                array(
                                                                'exam' => basename($_GET['exam'])),
                true
            );
        $this->load->view('view_main_page', array('left_title' => $this->lang->line('edit_exam'),
                                                                    'left' => $this->lang->line('edit_exam_description'),

                                                                    'center' => $center_text));
        $this->load->view('view_bottom');
      } catch (DataException $e) {
        $this->error_view($e->getMessage(), $this->lang->line('exam_mgmt'));
      }
    }


    //Get all elements of a folder only one level down
    public function getChildren($dir)
    {
        $results = array();
        $files = scandir($dir);

        foreach ($files as $key => $value) {
            $path = realpath();
        }

        return $results;
    }


    public function getDirContents($dir, &$results = array())
    {
        $files = scandir($dir);

        foreach ($files as $key => $value) {
            $path = realpath($dir.DIRECTORY_SEPARATOR.$value);
            if (substr(basename($path), 0, 1)=='.') {
                continue;
            }
            if (!is_dir($path)) {
                $results[] = $path;
            } elseif ($value != "." && $value != "..") {
                $this -> getDirContents($path, $results);
                $results[] = $path;
            }
        }

        return $results;
    }


    /**
     * Stores all elements of a directory in an array
     * First element of all arrays is always the name of the directory itself
     * @param $dir Name of the directory
     * @return $results Array which contains all sub-directories and items in the main
     *		directory and all sub-directories
     */
    public function getDirFiles($dir)
    {
        $files = scandir($dir);

        $results = array();
        $results[] = realpath($dir);

        foreach ($files as $key => $value) {
            $path = realpath($dir.DIRECTORY_SEPARATOR.$value);
            if (substr(basename($path), 0, 1)=='.') {
                continue;
            }
            if (!is_dir($path)) {
                $results[] = $path;
            } elseif ($value != "." && $value != "..") {
                $results[] = $this -> getDirFiles($path);
            }
        }

        return $results;
    }


    public function getDirFolders($dir, &$results = array())
    {
        $files = scandir($dir);

        foreach ($files as $key => $value) {
            $path = realpath($dir.DIRECTORY_SEPARATOR.$value);
            if (!is_dir($path)) {
                $results[] = $path;
            } elseif ($value != "." && $value != "..") {
                $results[] = $path;
            }
        }

        return $results;
    }

    // Return array with all the exams from ./exam
    private function getExams()
    {
        $exams = $this->getDirFolders(__DIR__."/../../exam");
        $result = array();

        foreach ($exams as $exam) {
            $exam = str_replace("/var/www/BibleOL/exam/", "", $exam);
            if ($exam != 'README') {
                array_push($result, $exam);
            }
        }

        return $result;
    }


    public function manage_exams()
    {
        $this->mod_users->check_teacher();

        $exams_per_page = $this->config->item('exams_per_page');
        $exam_count = $this->mod_exams->count_exams();
        $page_count = intval(ceil($exam_count/$exams_per_page));

        $offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
        if ($offset>=$page_count)
            $offset = $page_count-1;
        if ($offset<0)
            $offset = 0;

        if (isset($_GET['orderby']) && in_array($_GET['orderby'],
                                                array('exam_name', 'owner'), true))
            $orderby = $_GET['orderby'];
        else
            $orderby = 'exam_name';

        $sortorder = isset($_GET['desc']) ? 'desc' : 'asc';

        $allexams = $this->mod_exams->get_all_exams_part($exams_per_page,$offset*$exams_per_page,$orderby,$sortorder);
        $name_owned_classes = $this->mod_classes->get_named_classes_owned(false);

        $this->load->view('view_top1', array('title' => $this->lang->line('exam_mgmt')));
        $this->load->view('view_top2');
        $this->load->view('view_menu_bar', array('langselect' => true));
        $this->load->view('view_confirm_dialog');
        $this->load->view('view_alert_dialog');

        $center_text = $this->load->view('view_manage_exams',
                                          array(
                                          'allexams' => $allexams,
                                          'exam_count' => $exam_count,
                                          'exams_per_page' => $exams_per_page,
                                          'n_o_c' => $name_owned_classes,
                                          'offset' => $offset,
                                          'orderby' => $orderby,
                                          'page_count' => $page_count,
                                          'sortorder' => $sortorder
                                        ),
                                        true
        );

        $this->load->view('view_main_page', array('left_title' => $this->lang->line('exam_mgmt'),
                                              'left' => $this->lang->line('exam_mgmt_description'),
                                              'center' => $center_text));
        $this->load->view('view_bottom');
    }


    // CREATE A NEW EXAM
    public function new_exam()
    {
      try {
        $this->mod_users->check_teacher();

        $this->lang->load('owner', $this->language);
        $this->lang->load('exams', $this->language);

        //$dirlist = $this->mod_quizpath->dirlist(false);

        $this->load->model('mod_askemdros');
        $db_books = $this->mod_askemdros->db_and_books();

        if ($this->mod_users->is_admin()) {
            $teachers = $this->mod_users->get_teachers();
        } else {
            $teachers = array();
        }

        // VIEW:
        $this->load->view('view_top1', array('title' => $this->lang->line('create_exam')));
        $this->load->view('view_top2');
        $this->load->view('view_menu_bar', array('langselect' => true));
        $this->load->view('view_confirm_dialog');
        $this->load->view('view_alert_dialog');

        // Load the main view file.
        // This is the file that the users will mostly be interacting with.
        // The array contains any variables that might be needed by the view file.
        $center_text = $this->load->view(
            'view_new_exam',
            array(//'dirlist' => $dirlist,
                                               'examlist' => $this->getDirFolders(__DIR__."/../../exam"),
                                               'exerciselist' => $this->getDirContents(__DIR__."/../../quizzes"),
                                               'dir_files' => $this->getDirFiles(__DIR__."/../../quizzes"),
                                               //'show_contents' => $this->showContents('dir_files'),
                                               //'is_top' => $this->mod_quizpath->is_top(),
                                               'databases' => $db_books,
                                               'isadmin' => $this->mod_users->is_admin(),
                                               'teachers' => $teachers,
                                               'copy_or_move' => $this->session->userdata('operation')),
            true
        );

        $this->load->view('view_main_page', array('left_title' => $this->lang->line('create_exam'),
                                                  'left' => $this->lang->line('create_exam_description'),
                                                  'center' => $center_text));
        $this->load->view('view_bottom');
      } catch (DataException $e) {
        $this->error_view($e->getMessage(), $this->lang->line('exam_mgmt'));
      }
    }


    public function take_exam(){
      try {
        $this->mod_users->is_logged_in();

        $query = $this->db->get_where('bol_exam', array('exam_name' => $_GET['exam']));
        $examcode = $query->row()->examcode;
        $xml = simplexml_load_string($examcode);
        $exercises = array();
        foreach ($xml->exercise as $exercise) {
          array_push($exercises, $exercise->exercisename);
        }

        $this->load->model('mod_quizpath');
        $this->load->model('mod_askemdros');
        //$this->mod_quizpath->init()

        $this->load->view('view_top1', array('title' => $this->lang->line('take_exam')));
        $this->load->view('view_top2');
        $this->load->view('view_menu_bar', array('langselect' => true));
        $this->load->view('view_confirm_dialog');
        $this->load->view('view_alert_dialog');


        $center_text = $this->load->view(
          'view_take_exam',
          array(
            'xml' => $xml,
            'exercises' => $exercises,
          ),
          true
        );

        $this->load->view(
          'view_main_page',
          array(
            'left_title' => $this->lang->line('take_exam'),
            'left' => $this->lang->line('take_exam_description'),
            'center' => $center_text
          )
        );

        $this->load->view('view_bottom');
      } catch (DataException $e) {
        $this->error_view($e->getMessage(), $this->lang->line('exam_mgmt'));
      }
    }


    public function show_files()
    {
        try {
            $this->mod_users->check_teacher();

            $this->mod_quizpath->init(set_or_default($_GET['dir'], ''), true, false);

            $this->manage_exams();
        } catch (DataException $e) {
            $this->error_view($e->getMessage(), $this->lang->line('exam_mgmt'));
        }
    }


}
