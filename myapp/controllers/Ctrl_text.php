<?php
class Ctrl_text extends MY_Controller {
    public $loc; // Localization

    public function __construct() {
        parent::__construct();
        $this->lang->load('text', $this->language);
    }

	public function index() {
        $this->select_text();
	}

	public function select_text() {
        try {
            $this->load->helper('form');
            $this->load->library('form_validation');

            // MODEL:
            $this->load->model('mod_askemdros');
            $db_books = $this->mod_askemdros->db_and_books();

            $this->form_validation->set_rules('db', '', '');
            $this->form_validation->set_rules('chapter', $this->lang->line('chapter'), 'trim|required|is_natural_no_zero');
            $this->form_validation->set_rules('vfrom', $this->lang->line('first_verse'), 'trim|is_natural_no_zero');
            $this->form_validation->set_rules('vto', $this->lang->line('last_verse'), 'trim|is_natural_no_zero');
            $this->form_validation->set_rules('showicons', '', '');

            foreach ($db_books as $dbb)
                $this->form_validation->set_rules('book_' . $dbb['name'], '', '');

            if ($this->form_validation->run()) {
                $db = '/' . $this->input->post('db');
                $book = '/' . $this->input->post('book_' . $this->input->post('db'));
                $chapter = '/' . $this->input->post('chapter');
                $vfrom = $this->input->post('vfrom');
                $vto = $this->input->post('vto');
                $showicons = $this->input->post('showicons');

                if ($vfrom !== '') {
                    $vfrom = '/' . $vfrom;
                    if ($vto !== '')
                        $vto = '/' . $vto;
                }
                else
                    $vto = '';

                if ($showicons==='on')
                    redirect("/text/show_text$db$book$chapter$vfrom$vto?icons=on");
                else
                    redirect("/text/show_text$db$book$chapter$vfrom$vto");
            }

            // VIEW:
            $this->load->view('view_top1', array('title' => $this->lang->line('select_text')));
            $this->load->view('view_top2');
            $this->load->view('view_menu_bar', array('langselect' => true,
                                                     'more_help_items' => array('viewing_text' => 'help_this_page')));

            $copyrights = [];
            $dbnames = [];
            
            foreach ($db_books as $dbb) {
                $dbnames[$dbb['name']] = $dbb['databaseName'];
                $copyrights[$dbb['name']] = $dbb['loc_copyright'];
            }

            $center_text = $this->load->view('view_select_text',array('databases' => $db_books), true);
            $right_text =  $this->load->view('view_copyrights',array('db_books' => $db_books), true);

            $this->load->view('view_main_page', array('left_title' => $this->lang->line('select_a_passage_title'),
                                                      'left' => $this->lang->line('select_a_passage'),
                                                      'center' => $center_text,
                                                      'right_title' => $this->lang->line('corpus_copyright_title'),
                                                      'right' => $right_text));
            $this->load->view('view_bottom');
        }
        catch (DataException $e) {
            $this->error_view($e->getMessage(), $this->lang->line('select_text'));
        }
    }


    private static function condval($val, $default) {
        if ($val===null)
            return $default;
        if (is_numeric($val))
            return intval($val);
        return $val;
    }

    public function test_quiz(){
        $this->mod_users->check_teacher();

        if (!isset($_POST['dir']))
            throw new DataException($this->lang->line('missing_folder_name'));
        if (!isset($_POST['quiz']))
            throw new DataException($this->lang->line('missing_quiz_filename'));

        if (!isset($_POST['quizdata']))
            throw new DataException("Missing quiz data");
        
        // MODEL:
        $this->load->model('mod_quizpath' );
        $this->load->model('mod_askemdros');
        $this->load->model('mod_localize');

        $this->mod_quizpath->init(rawurldecode($_POST['dir']) . '/' . rawurldecode($_POST['quiz']) . '.3et', false, false, false);

        $dir = rawurldecode($_POST['dir']);
        $quiz = rawurldecode($_POST['quiz']);
        $quiz_name = rawurldecode($dir) . '/' . rawurldecode($quiz);
        $quizdata = json_decode(urldecode($_POST['quizdata']));

        
        // Package quiz data
        $res = $this->mod_askemdros->package_test_quiz($quizdata);
        $this->mod_quizpath->set_owner($this->mod_users->my_id());

        $number_of_quizzes = 5;
        $universe = null;

        $this->mod_askemdros->show_test_quiz($number_of_quizzes, $quizdata, $universe);

        // VIEW:
        $javascripts = array('js/ol.js');
        if ($this->quiz_data->quizFeatures->useVirtualKeyboard) {
            switch ($this->db_config->dbinfo->charSet) {
              case 'hebrew':
                    $javascripts[] = 'VirtualKeyboard.full.3.7.2/vk_loader.js?vk_layout=IL%20Biblical%20Hebrew%20(SIL)&amp;vk_skin=goldie';
                    break;

              case 'greek':
                    $javascripts[] = 'VirtualKeyboard.full.3.7.2/vk_loader.js?vk_layout=GR%20Greek%20Polytonic&amp;vk_skin=goldie';
                    break;

              case 'transliterated_hebrew':
              case 'latin':
                    // Nothing for now
                    break;
            }
        }
        $display_data = array(
            'is_quiz' => true,
            'mql_list' => isset($this->mql) ? $this->mql->mql_list : '',
            'useTooltip_str' => $this->mod_askemdros->use_tooltip ? 'true' : 'false',
            'quizData_json' => $this->mod_askemdros->quiz_data_json,
            'dbinfo_json' => $this->mod_askemdros->dbinfo_json,
            'dictionaries_json' => $this->mod_askemdros->dictionaries_json,
            'l10n_json' => $this->mod_askemdros->l10n_json,
            'l10n_js_json' => $this->mod_localize->get_json(),
            'typeinfo_json' => $this->mod_askemdros->typeinfo_json,
            'is_logged_in' => $this->mod_users->is_logged_in(),
            'quiz_name' => $quiz_name
          );
        
        $this->load->view('view_top1', array('title' => $this->lang->line('quiz'),
          'css_list' => array('styles/selectbox.css'),
          'js_list' => $javascripts));
        $this->load->view('view_font_css', array('fonts' => $this->mod_askemdros->font_selection));
        $this->load->view('view_top2');
        $this->load->view('view_menu_bar', array('langselect' => false));
        
        $this->load->view('view_test_quiz', $display_data);
        $this->load->view('view_bottom');
                

    }
    
	public function show_text() {
        try {
            $showIcons = isset($_GET['icons']) && $_GET['icons']==='on';

            $db      = self::condval($this->uri->segment(3), 'ETCBC4');
            $book    = self::condval($this->uri->segment(4), $db==='ETCBC4' ? 'Genesis' : 'Matthew');
            $chapter = self::condval($this->uri->segment(5), 1);
            $vfrom   = self::condval($this->uri->segment(6), 0);
            $vto     = self::condval($this->uri->segment(7), $vfrom);

            // MODEL:
            $this->load->model('mod_askemdros');
            $this->mod_askemdros->show_text($db, $book, $chapter, $vfrom, $vto, $showIcons);
            $shebanq_link = $this->mod_askemdros->shebanq_link($db, $book, $chapter);
            $this->load->model('mod_localize');


            // VIEW:
            $this->load->view('view_top1', array('title' => $this->mod_askemdros->book_title,
                                                 'js_list'=>array('js/ol.js')));
            $this->load->view('view_font_css', array('fonts' => $this->mod_askemdros->font_selection));
            $this->load->view('view_top2');

            $help_page = $db==='ETCBC4' ? 'viewing_text2/heb' :
                         ($db==='nestle1904' ? 'viewing_text2/gr' :
                         'viewing_text');
            
            $this->load->view('view_menu_bar', array('langselect' => true,
                                                     'more_help_items' => array($help_page => 'help_this_page')));
                
                $this->load->view('view_text_display', array('is_quiz' => false,
                                                         'mql_list' => $this->mql->mql_list,
                                                         'useTooltip_str' => $this->mod_askemdros->use_tooltip ? 'true' : 'false',
                                                         'quizData_json' => 'null',
                                                         'dbinfo_json' => $this->mod_askemdros->dbinfo_json,
                                                         'dictionaries_json' => $this->mod_askemdros->dictionaries_json,
                                                         'l10n_json' => $this->mod_askemdros->l10n_json,
                                                         'l10n_js_json' => $this->mod_localize->get_json(),
                                                         'typeinfo_json' => $this->mod_askemdros->typeinfo_json,
                                                         'shebanq_link' => $shebanq_link));
            $this->load->view('view_bottom');
        }
        catch (DataException $e) {
            $this->error_view($e->getMessage(), $this->lang->line('show_text'));
        }
    }

	public function select_quiz() {
        try {
            // MODEL:
            $this->load->model('mod_quizpath');
            $this->load->helper('varset');
            $this->mod_quizpath->init(set_or_default($_GET['dir'],''), true, true);

            $dirlist = $this->mod_quizpath->dirlist(true);

            // VIEW:
            $this->load->view('view_top1', array('title' => $this->lang->line('directory')));
            $this->load->view('view_top2');
            $this->load->view('view_menu_bar', array('langselect' => true,
                                                     'more_help_items' => array('running_exercises' => 'help_this_page')));
            $center_text = $this->load->view('view_quizdir',
                                             array('dirlist' => $dirlist,
                                                   'curdir' => set_or_default($_GET['dir'],''),
                                                   'is_logged_in' => $this->mod_users->is_logged_in()),
                                             true);
            $this->load->view('view_main_page', array('left_title' => $this->lang->line('select_quiz'),
                                                      'left' => $this->lang->line('click_folder'),
                                                      'center' => $center_text));
            $this->load->view('view_bottom');

        }
        catch (DataException $e) {
            $this->error_view($e->getMessage(), $this->lang->line('directory'));
        }
    }

    

    // Displays a quiz with the universe specified in the .3et file.
	public function show_quiz() {
        if (!isset($_GET['quiz'])) {
            $this->select_quiz();
            return;
        }

        if (!isset($_GET['count']) || !is_numeric($_GET['count']))
            $number_of_quizzes = 5;
        else
            $number_of_quizzes = intval($_GET['count']);

        if (array_key_exists('examid', $_GET)) {
          $quiz = $_GET['quiz'];
          $exam_parameters = $_SESSION['exam_parameters'];
          $numq = $exam_parameters[str_replace("+", "%2B", $quiz)]['numq'];

          if ($numq <= 0) {
            $numq = 10;
          }
          $this->show_quiz_common($_GET['quiz'], $numq, $universe = null, $examid=$_GET['examid'], $exercise_lst=$_GET['exercise_lst']);

        } else {
          $this->show_quiz_common($_GET['quiz'], $number_of_quizzes);
        }
    }

    public function convert_quiz() {
        // NOTE: For many exercise file names, this function requires that the following
        // configuration item be set in myapp/config/config.php:
        //
        // $config['permitted_uri_chars'] = 'a-z 0-9~%.:(),æÆ_\-';


        if (!is_cli()) {
            echo '<pre>'.$this->lang->line('only_cli').'</pre>';
            die;
        }

		if ($this->uri->total_segments()<3) {
            echo $this->uri->total_segments(),"\n";
			print "Usage: php index.php text convert_quiz <filename>\n";
			die;
		}

        $filename = $this->uri->segment(3);
        for ($i=4; $i<=$this->uri->total_segments(); ++$i)
            $filename .= '/' . $this->uri->segment($i);

        $filename = html_entity_decode($filename); // Fixes changes made by CI_URI::_filter_uri

        $this->load->model('mod_askemdros');
        $this->mod_askemdros->convert_ETCBC4_v7($filename);
    }


    // Displays a quiz with the universe specified by the user.
	public function show_quiz_sel() {
        if (!isset($_POST['quiz']) || !isset($_POST['count']) || !isset($_POST['sel'])) {
            // VIEW:
            $this->load->view('view_top1', array('title' => $this->lang->line('quiz')));
            $this->load->view('view_top2');
            $this->load->view('view_menu_bar', array('langselect' => true));
            $this->load->view('view_error',array('text' => $this->lang->line('no_direct_url')));
            $this->load->view('view_bottom');
            return;
        }

        $this->show_quiz_common($_POST['quiz'], intval($_POST['count']), $_POST['sel']);
    }

    public function preview_results(){
        // MODEL:
        $this->load->model('mod_quizpath' );
        $this->load->model('mod_askemdros');

        $preview_data = json_encode($_POST);
        //$die=4/0;
        $number_of_quizzes = 5;
    }

    // Common code for show_quiz() and show_quiz_sel()
	private function show_quiz_common(string $quiz, int $number_of_quizzes, array $universe = null, int $examid = null, string $exercise_lst = null) {
        try {
            // MODEL:
            $this->load->model('mod_quizpath');
            $this->load->model('mod_askemdros');
            $this->mod_quizpath->init($quiz, false, true);

            $this->mod_askemdros->show_quiz($number_of_quizzes, $universe);
            $this->load->model('mod_localize');

            // VIEW:
            $javascripts = array('js/ol.js');
            if ($this->quiz_data->quizFeatures->useVirtualKeyboard) {
                switch ($this->db_config->dbinfo->charSet) {
                  case 'hebrew':
                        $javascripts[] = 'VirtualKeyboard.full.3.7.2/vk_loader.js?vk_layout=IL%20Biblical%20Hebrew%20(SIL)&amp;vk_skin=goldie';
                        break;

                  case 'greek':
                        $javascripts[] = 'VirtualKeyboard.full.3.7.2/vk_loader.js?vk_layout=GR%20Greek%20Polytonic&amp;vk_skin=goldie';
                        break;

                  case 'transliterated_hebrew':
                  case 'latin':
                        // Nothing for now
                        break;
                }
            }

            $display_data = array(
              'is_quiz' => true,
              'mql_list' => isset($this->mql) ? $this->mql->mql_list : '',
              'useTooltip_str' => $this->mod_askemdros->use_tooltip ? 'true' : 'false',
              'quizData_json' => $this->mod_askemdros->quiz_data_json,
              'dbinfo_json' => $this->mod_askemdros->dbinfo_json,
              'dictionaries_json' => $this->mod_askemdros->dictionaries_json,
              'l10n_json' => $this->mod_askemdros->l10n_json,
              'l10n_js_json' => $this->mod_localize->get_json(),
              'typeinfo_json' => $this->mod_askemdros->typeinfo_json,
              'is_logged_in' => $this->mod_users->is_logged_in(),
            );

            $exam_data = array(
              'is_exam' => $examid !== null,
              'examid' => $examid,
              'exercise_lst' => $exercise_lst,
              'quizid' => $this->quiz_data->quizid,
            );

            if ($examid) {
              $display_data = array_merge($display_data, $exam_data);
            }


            $this->load->view('view_top1', array('title' => $this->lang->line('quiz'),
                                                 'css_list' => array('styles/selectbox.css'),
                                                 'js_list' => $javascripts));
            $this->load->view('view_font_css', array('fonts' => $this->mod_askemdros->font_selection));
            $this->load->view('view_top2');
            if ($examid === null) {
              $this->load->view('view_menu_bar', array('langselect' => false));
            }
            $this->load->view('view_text_display', $display_data);
            if ($examid === null) {
              $this->load->view('view_bottom');
            }
        }
        catch (DataException $e) {
            $this->error_view($e->getMessage(), $this->lang->line('quiz'));
        }
    }

    // Displays a universe selection tree in preparation for the execution of a quiz
	public function show_quiz_univ() {
        try {
            if (!isset($_GET['quiz'])) {
                $this->select_quiz();
                return;
            }

            if (!isset($_GET['count']) || !is_numeric($_GET['count']))
                $number_of_quizzes = 5;
            else
                $number_of_quizzes = intval($_GET['count']);

            // MODEL:
            $this->load->model('mod_askemdros');
            $this->load->model('mod_quizpath');
            $this->mod_quizpath->init($_GET['quiz'], false, true);

            $this->mod_askemdros->get_quiz_universe();
            $this->loc = json_decode($this->db_config->l10n_json);

            $this->load->library('universe_tree', array('markedList' => $this->mod_askemdros->universe));

            // VIEW:
            $this->load->view('view_top1', array('title' => $this->lang->line('select_passages'),
                                                 'css_list'=>array('styles/jstree.css'),
                                                 'js_list'=>array('jstree/jquery.jstree.js')));
            $this->load->view('view_top2');
            $this->load->view('view_menu_bar', array('langselect' => true));
            $this->load->view('view_alert_dialog');
            $center_text = $this->load->view('view_passage_select',
                                             array('quiz' => $_GET['quiz'],
                                                   'count' => $number_of_quizzes),
                                             true)
                . $this->load->view('view_passage_tree_script',
                                    array('tree_data' => $this->universe_tree->get_jstree(),
                                          'markedList' => $this->mod_askemdros->universe,
                                          'db' => $this->mod_askemdros->setup_db,
                                          'prop' => $this->mod_askemdros->setup_prop),
                                    true);
            $this->load->view('view_main_page', array('left_title' => $this->lang->line('quiz_instruct1'),
                                                      'left' => $this->lang->line('quiz_instruct2'),
                                                      'center' => $center_text));
            $this->load->view('view_bottom');
        }
        catch (DataException $e) {
            $this->error_view($e->getMessage(), $this->lang->line('show_quiz_passages'));
        }
    }

	public function add_universe_level() {
        // $_GET is typically array("rangelow"=>"7032", "rangehigh"=>"7323", "ref"=>"Genesis:16",
        //                          "lev"=>"3", "db"=>"ETCBC4", "prop"=>"BHS4")

        // MODEL:
        $this->load->model('mod_askemdros');
        $this->mod_askemdros->setup($_GET['db'], $_GET['prop']);
        $this->loc = json_decode($this->db_config->l10n_json);

        $this->load->library('universe_tree');
        $res = $this->universe_tree->expand_level(intval($_GET['rangelow']), intval($_GET['rangehigh']),
                                                  $_GET['ref'], intval($_GET['lev']));

        // VIEW:
        echo json_encode($res);
    }

    public function edit_quiz() {
        try {
            $is_new = 'false';
            if (!isset($_GET['quiz']))
                throw new DataException($this->lang->line('missing_quiz_filename'));

            $this->mod_users->check_teacher();

            $this->load->model('mod_quizpath');
            $this->load->model('mod_askemdros');
            $this->load->model('mod_localize');

            $this->mod_quizpath->init($_GET['quiz'], false, false);

            $this->mod_askemdros->edit_quiz();

            $this->loc = json_decode($this->db_config->l10n_json);
            $this->load->library('universe_tree', array('markedList' => $this->mod_askemdros->universe));

            $javascripts = array('jstree/jquery.jstree.js',
                                 'ckeditor/ckeditor.js',
                                 'ckeditor/adapters/jquery.js',
                                 'js/editquiz.js');

            switch ($this->db_config->dbinfo->charSet) {
              case 'hebrew':
                    $javascripts[] = 'VirtualKeyboard.full.3.7.2/vk_loader.js?vk_layout=IL%20Biblical%20Hebrew%20(SIL)&amp;vk_skin=goldie';
                    break;

              case 'greek':
                    $javascripts[] = 'VirtualKeyboard.full.3.7.2/vk_loader.js?vk_layout=GR%20Greek%20Polytonic&amp;vk_skin=goldie';
                    break;

              case 'transliterated_hebrew':
              case 'latin':
                    // Nothing for now
                    break;
            }

            //  View
            $this->load->view('view_top1', array('title' => $this->lang->line('edit_quiz'),
                                                 'css_list' => array('styles/jstree.css'),
                                                 'js_list' => $javascripts));
            $this->load->view('view_font_css', array('fonts' => $this->mod_askemdros->font_selection));
            $this->load->view('view_top2');
            $this->load->view('view_menu_bar', array('langselect' => false,
                                                     'more_help_items' => array('tabs' => 'help_this_page')));

            
            $this->load->view('view_alert_dialog');
            $request_features = $this->mod_askemdros->decoded_3et->quizFeatures->requestFeatures;
            $order_features = array();
            $order_val = 1;
            foreach ($request_features as $rf) {
                $order_features[$rf->name] = $order_val;
                $order_val++;
            }

            //echo 'Order Features: ' . json_encode($order_features) . '<br>';

            $center_text = $this->load->view('view_edit_quiz',
                                             array('decoded_3et_json' => json_encode($this->mod_askemdros->decoded_3et),
                                                   'dbinfo_json' => $this->mod_askemdros->dbinfo_json,
                                                   'l10n_json' => $this->mod_askemdros->l10n_json,
                                                   'l10n_js_json' => $this->mod_localize->get_json(),
                                                   'typeinfo_json' => $this->mod_askemdros->typeinfo_json,
                                                   'universe' => json_encode($this->mod_askemdros->universe),
                                                   'dir' => dirname($_GET['quiz']),
                                                   'quiz' => substr(basename($_GET['quiz']),0,-4), // Strips .3et
                                                   'order_features' => json_encode($order_features),
                                                   'is_new' => $is_new), 
                                             true)
                . $this->load->view('view_passage_tree_script',
                                    array('tree_data' => $this->universe_tree->get_jstree(),
                                          'markedList' => $this->mod_askemdros->universe,
                                          'db' => $this->mod_askemdros->setup_db,
                                          'prop' => $this->mod_askemdros->setup_prop),
                                    true);

            $this->load->view('view_main_page', array('left_title' => $this->lang->line('edit_quiz'),
                                                      'left' => sprintf($this->lang->line('using_database'),
                                                                        '<br>'.$this->loc->dbdescription)
                                                      .'<br>'.sprintf($this->lang->line('using_database_version'),
                                                                      $this->db_config->dbinfo->databaseVersion),
                                                      'center' => $center_text));
            $this->load->view('view_bottom');
        }
        catch (DataException $e) {
            $this->error_view($e->getMessage(), $this->lang->line('edit_quiz'));
        }
    }

    public function new_quiz() {
        try {
            $is_new = 'true';
            if (!isset($_POST['dir']))
                throw new DataException($this->lang->line('missing_folder_name'));
            $dir = trim($_POST['dir']);

            if (!isset($_POST['db']))
                throw new DataException($this->lang->line('missing_database_name'));
            $db = $_POST['db'];

            $this->mod_users->check_teacher();

            $this->load->model('mod_askemdros');
            $this->load->model('mod_localize');

            $decoded_3et_json = $this->mod_askemdros->new_quiz($db);

            $this->loc = json_decode($this->db_config->l10n_json);
            $this->load->library('universe_tree', array('markedList' => $this->mod_askemdros->universe));

            $javascripts = array('jstree/jquery.jstree.js',
                                 'ckeditor/ckeditor.js',
                                 'ckeditor/adapters/jquery.js',
                                 'js/editquiz.js');

            switch ($this->db_config->dbinfo->charSet) {
              case 'hebrew':
                    $javascripts[] = 'VirtualKeyboard.full.3.7.2/vk_loader.js?vk_layout=IL%20Biblical%20Hebrew%20(SIL)&amp;vk_skin=goldie';
                    break;

              case 'greek':
                    $javascripts[] = 'VirtualKeyboard.full.3.7.2/vk_loader.js?vk_layout=GR%20Greek%20Polytonic&amp;vk_skin=goldie';
                    break;

              case 'transliterated_hebrew':
              case 'latin':
                    // Nothing for now
                    break;
            }

            // initialize empty feature order
            $order_features = array();

            //  View
            $this->load->view('view_top1', array('title' => $this->lang->line('edit_quiz'),
                                                 'css_list' => array('styles/jstree.css'),
                                                 'js_list' => $javascripts));
            $this->load->view('view_font_css', array('fonts' => $this->mod_askemdros->font_selection));
            $this->load->view('view_top2');
            $this->load->view('view_menu_bar', array('langselect' => false,
                                                     'more_help_items' => array('tabs' => 'help_this_page')));
            $this->load->view('view_alert_dialog');

            $center_text = $this->load->view('view_edit_quiz',
                                             array('decoded_3et_json' => $decoded_3et_json,
                                                   'dbinfo_json' => $this->mod_askemdros->dbinfo_json,
                                                   'l10n_json' => $this->mod_askemdros->l10n_json,
                                                   'l10n_js_json' => $this->mod_localize->get_json(),
                                                   'typeinfo_json' => $this->mod_askemdros->typeinfo_json,
                                                   'universe' => '[]',
                                                   'dir' => $dir,
                                                   'quiz' => null,
                                                   'order_features' => json_encode($order_features),
                                                   'is_new' => $is_new),
                                             true)
                . $this->load->view('view_passage_tree_script',
                                    array('tree_data' => $this->universe_tree->get_jstree(),
                                          'markedList' => array(),
                                          'db' => $this->mod_askemdros->setup_db,
                                          'prop' => $this->mod_askemdros->setup_prop),
                                    true);


            $this->load->view('view_main_page', array('left_title' => $this->lang->line('edit_quiz'),
                                                      'left' => sprintf($this->lang->line('using_database'),
                                                                               '<br>'.$this->loc->dbdescription)
                                                               .'<br>'.sprintf($this->lang->line('using_database_version'),
                                                                               $this->db_config->dbinfo->databaseVersion),
                                                      'center' => $center_text));

            $this->load->view('view_bottom');
        }
        catch (DataException $e) {
            $this->error_view($e->getMessage(), $this->lang->line('edit_quiz'));
        }
    }

    public function check_submit_quiz() {
        try {
            $this->mod_users->check_teacher();

            if (!isset($_GET['dir']))
                throw new DataException($this->lang->line('missing_folder_name'));
            if (!isset($_GET['quiz']))
                throw new DataException($this->lang->line('missing_quiz_filename'));

            // MODEL:
            $this->load->model('mod_quizpath');

            $quizname = rawurldecode($_GET['quiz']);
            if (preg_match('|[/?*;{}"\'\\\\]|',$quizname))
                echo "BADNAME\n";
            else {
                $this->mod_quizpath->init(rawurldecode($_GET['dir']) . "/$quizname.3et", false, false, false);
                if ($this->mod_quizpath->file_exists()) {
                    $owner = $this->mod_quizpath->get_excercise_owner();

                    if ($owner!=$this->mod_users->my_id() && !$this->mod_users->is_admin())
                        echo $this->lang->line('not_owner'),"\n";
                    else
                        echo "EXISTS\n";
                }
                else
                    echo "OK\n";
            }
        }
        catch (DataException $e) {
            echo $e->getMessage(),"\n";
        }
    }

    public function submit_quiz() {
        try {
            $this->mod_users->check_teacher();

            if (!isset($_POST['dir']))
                throw new DataException($this->lang->line('missing_folder_name'));
            if (!isset($_POST['quiz']))
                throw new DataException($this->lang->line('missing_quiz_filename'));

            if (!isset($_POST['quizdata']))
                throw new DataException("Missing quiz data");

            // MODEL:
            $this->load->model('mod_quizpath' );
            $this->load->model('mod_askemdros');

            $this->mod_quizpath->init(rawurldecode($_POST['dir']) . '/' . rawurldecode($_POST['quiz']) . '.3et', false, false, false);

            // Protect against malicious posting:
            if ($this->mod_quizpath->file_exists()) {
                $owner = $this->mod_quizpath->get_excercise_owner();

                if ($owner!=$this->mod_users->my_id() && !$this->mod_users->is_admin())
                    throw new DataException($this->lang->line('not_owner'));
            }

            $this->mod_askemdros->save_quiz(json_decode(urldecode($_POST['quizdata'])));
            $this->mod_quizpath->set_owner($this->mod_users->my_id());
            redirect('/file_manager?dir=' . $_POST['dir']); // Note: Don't use http_build_query, because $POST['dir'] is already encoded
        }
        catch (DataException $e) {
            $this->error_view($e->getMessage(), $this->lang->line('edit_quiz'));
        }
    }

    
}
