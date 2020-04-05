<?php

class Ctrl_exams extends MY_Controller
{
    public $loc; // Localization

    public function __construct()
    {
        parent::__construct();

        $this->lang->load('exams', $this->language);
        $this->load->model('mod_exams');
        $this->load->model('mod_quizpath');
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

                $this -> create_config_file($create, $ex_ar);

                $user_id = intval($this->session->userdata('ol_user'));  // Sets $user_id to 0 if userdata('ol_user') is not set

                $query = $this->db->where('id', $user_id)->get('user');
                $q = $this->mod_users->check_teacher;

                $txt = gettype($q);
                fwrite($test_file, $txt);
                fclose($test_file);

                redirect("/exams/edit_exam?exam=$create");
            }
        } catch (DataException $e) {
            $this->error_view($e->getMessage(), $this->lang->line('illegal_char_folder_name'));
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
        $plan_start_node = $dom->createElement('plan_start', $now);
        $root->appendChild($plan_start_node);
        $plan_end_node = $dom->createElement('plan_end', $now);
        $root->appendChild($plan_end_node);
        $time_node = $dom->createElement('time', '0');
        $root->appendChild($time_node);


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


    // CREATE A NEW EXAM
    public function new_exam()
    {
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


    // EDIT EXISTING EXAM
    // Also used in exam creation.
    public function edit_exam()
    {
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
    }
}
