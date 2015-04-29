<?php
class Ctrl_text extends MY_Controller {
    public $loc; // Localization

    public function __construct() {
        parent::__construct();
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
            $this->form_validation->set_rules('chapter', 'Chapter', 'trim|required|is_natural_no_zero');
            $this->form_validation->set_rules('vfrom', 'First verse', 'trim|is_natural_no_zero');
            $this->form_validation->set_rules('vto', 'Last verse', 'trim|is_natural_no_zero');
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
            $this->load->view('view_top1', array('title' => 'Select Text'));
            $this->load->view('view_top2');
            $this->load->view('view_menu_bar');

            $center_text = $this->load->view('view_select_text',array('databases' => $db_books), true);

            $this->load->view('view_main_page', array('left' => '<h1>Select a Passage to Display</h1>',
                                                      'center' => $center_text));
            $this->load->view('view_bottom');
        }
        catch (DataException $e) {
            $this->error_view($e->getMessage(), 'Select Text');
        }
    }


    private static function condval($val, $default) {
        if ($val===false)
            return $default;
        if (is_numeric($val))
            return intval($val);
        return $val;
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


            // VIEW:
            $this->load->view('view_top1', array('title' => $this->mod_askemdros->book_title,
                                                 'js_list'=>array('js/ol.js')));
            $this->load->view('view_font_css', array('fonts' => $this->mod_askemdros->font_selection));
            $this->load->view('view_top2');
            $this->load->view('view_menu_bar');
            $this->load->view('view_text_display', array('is_quiz' => false,
                                                         'mql_list' => $this->mql->mql_list,
                                                         'useTooltip_str' => $this->mod_askemdros->use_tooltip ? 'true' : 'false',
                                                         'quizData_json' => 'null',
                                                         'dbinfo_json' => $this->mod_askemdros->dbinfo_json,
                                                         'dictionaries_json' => $this->mod_askemdros->dictionaries_json,
                                                         'localization_json' => $this->mod_askemdros->localization_json,
                                                         'typeinfo_json' => $this->mod_askemdros->typeinfo_json,
                                                         'shebanq_link' => $shebanq_link));
            $this->load->view('view_bottom');
        }
        catch (DataException $e) {
            $this->error_view($e->getMessage(), 'Show Text');
        }
    }

	public function select_quiz() {
        try {
            // MODEL:
            $this->load->model('mod_quizpath');
            $this->mod_quizpath->init(isset($_GET['dir']) ? $_GET['dir'] : '', true, true);

            $dirlist = $this->mod_quizpath->dirlist(true);

            // VIEW:
            $this->load->helper('varset');
            $this->load->view('view_top1', array('title' => 'Directory'));
            $this->load->view('view_top2');
            $this->load->view('view_menu_bar');
            $center_text = $this->load->view('view_quizdir',
                                             array('dirlist' => $dirlist,
                                                   'is_logged_in' => $this->mod_users->is_logged_in()),
                                             true);
            $this->load->view('view_main_page', array('left' => '<h1>Select a Quiz</h1><p>Click on a folder to open it,
                                                             or select the number of questions you want from a
                                                             particular quiz and whether you want to use the
                                                             preset passage selection or specify your own
                                                             passages.</p>',
                                                      'center' => $center_text));
            $this->load->view('view_bottom');

        }
        catch (DataException $e) {
            $this->error_view($e->getMessage(), 'Directory');
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


        $this->show_quiz_common($_GET['quiz'], $number_of_quizzes);
    }

    public function convert_quiz() {
        // NOTE: For many exercise file names, this function requires that the following
        // configuration item be set in myapp/config/config.php:
        //
        // $config['permitted_uri_chars'] = 'a-z 0-9~%.:(),æÆ_\-';


        if (!$this->input->is_cli_request()) {
            echo '<pre>This command can only be run from the command line</pre>';
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
        $this->mod_askemdros->convert_WIVU_to_ETCBC4($filename);
    }


    // Displays a quiz with the universe specified by the user.
	public function show_quiz_sel() {
        if (!isset($_POST['quiz']) || !isset($_POST['count']) || !isset($_POST['sel'])) {
            // VIEW:
            $this->load->view('view_top1', array('title' => 'Quiz'));
            $this->load->view('view_top2');
            $this->load->view('view_menu_bar');
            $this->load->view('view_error',array('text' => 'Do not access this URL directly.'));
            $this->load->view('view_bottom');
            return;
        }

        $this->show_quiz_common($_POST['quiz'], intval($_POST['count']), $_POST['sel']);
    }

    // Common code for show_quiz() and show_quiz_sel()
	private function show_quiz_common(string $quiz, integer $number_of_quizzes, array $universe = null) {
        try {
            // MODEL:
            $this->load->model('mod_quizpath');
            $this->load->model('mod_askemdros');
            $this->mod_quizpath->init($quiz, false, true);

            $this->mod_askemdros->show_quiz($number_of_quizzes, $universe);

            // VIEW:
            $javascripts = array('js/ol.js');
            if ($this->quiz_data->quizFeatures->useVirtualKeyboard) {
                switch ($this->db_config->dbinfo->charSet) {
                  case 'hebrew':
                        $javascripts[] = 'virtualkeyboard/vk_loader.js?vk_layout=IL%20Biblical%20Hebrew%20(SIL)&amp;vk_skin=goldie';
                        break;

                  case 'greek':
                        $javascripts[] = 'virtualkeyboard/vk_loader.js?vk_layout=GR%20Greek%20Polytonic&amp;vk_skin=goldie';
                        break;

                  case 'transliterated_hebrew':
                        // Nothing for now
                        break;
                }
            }
            $this->load->view('view_top1', array('title' => 'Quiz',
                                                 'css_list' => array('styles/selectbox.css'),
                                                 'js_list' => $javascripts));
            $this->load->view('view_font_css', array('fonts' => $this->mod_askemdros->font_selection));
            $this->load->view('view_top2');
            $this->load->view('view_menu_bar');
            $this->load->view('view_text_display', array('is_quiz' => true,
                                                         'mql_list' => isset($this->mql) ? $this->mql->mql_list : '',
                                                         'useTooltip_str' => $this->mod_askemdros->use_tooltip ? 'true' : 'false',
                                                         'quizData_json' => $this->mod_askemdros->quiz_data_json,
                                                         'dbinfo_json' => $this->mod_askemdros->dbinfo_json,
                                                         'dictionaries_json' => $this->mod_askemdros->dictionaries_json,
                                                         'localization_json' => $this->mod_askemdros->localization_json,
                                                         'typeinfo_json' => $this->mod_askemdros->typeinfo_json,
                                                         'is_logged_in' => $this->mod_users->is_logged_in()));
            $this->load->view('view_bottom');
        }
        catch (DataException $e) {
            $this->error_view($e->getMessage(), 'Quiz');
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
            $this->loc = json_decode($this->db_config->localization_json);

            $this->load->library('universe_tree', array('markedList' => $this->mod_askemdros->universe));

            // VIEW:
            $this->load->view('view_top1', array('title' => 'Select Passages',
                                                 'css_list'=>array('styles/jstree.css'),
                                                 'js_list'=>array('jstree/jquery.jstree.js')));
            $this->load->view('view_top2');
            $this->load->view('view_menu_bar');
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
            $this->load->view('view_main_page', array('left' => '<h1>Select the Passages to Use for the Quiz...</h1>
                                                             <p>...and then press the &ldquo;Start quiz&rdquo;
                                                             button at the bottom of the page</p>',
                                                      'center' => $center_text));
            $this->load->view('view_bottom');
        }
        catch (DataException $e) {
            $this->error_view($e->getMessage(), 'Show Quiz Passages');
        }
    }

	public function add_universe_level() {
        // $_GET is typically array("rangelow"=>"7032", "rangehigh"=>"7323", "ref"=>"Genesis:16", 
        //                          "lev"=>"3", "db"=>"ETCBC4", "prop"=>"BHS4")

        // MODEL:
        $this->load->model('mod_askemdros');
        $this->mod_askemdros->setup($_GET['db'], $_GET['prop']);
        $this->loc = json_decode($this->db_config->localization_json);
        
        $this->load->library('universe_tree');
        $res = $this->universe_tree->expand_level(intval($_GET['rangelow']), intval($_GET['rangehigh']),
                                                  $_GET['ref'], intval($_GET['lev']));

        // VIEW:
        echo json_encode($res);
    }

    public function edit_quiz() {
        try {
            if (!isset($_GET['quiz']))
                throw new DataException("Missing quiz filename");

            $this->mod_users->check_admin();

            $this->load->model('mod_quizpath');
            $this->load->model('mod_askemdros');

            $this->mod_quizpath->init($_GET['quiz'], false, false);
            
            $this->mod_askemdros->edit_quiz();

            $this->loc = json_decode($this->db_config->localization_json);
            $this->load->library('universe_tree', array('markedList' => $this->mod_askemdros->universe));

            $javascripts = array('jstree/jquery.jstree.js',
                                 'ckeditor/ckeditor.js',
                                 'ckeditor/adapters/jquery.js',
                                 'js/editquiz.js');

            switch ($this->db_config->dbinfo->charSet) {
              case 'hebrew':
                    $javascripts[] = 'virtualkeyboard/vk_loader.js?vk_layout=IL%20Biblical%20Hebrew%20(SIL)&amp;vk_skin=goldie';
                    break;

              case 'greek':
                    $javascripts[] = 'virtualkeyboard/vk_loader.js?vk_layout=GR%20Greek%20Polytonic&amp;vk_skin=goldie';
                    break;

              case 'transliterated_hebrew':
                    // Nothing for now
                    break;
            }

            //  View
            $this->load->view('view_top1', array('title' => 'Edit Quiz',
                                                 'css_list' => array('styles/jstree.css'),
                                                 'js_list' => $javascripts));
            $this->load->view('view_font_css', array('fonts' => $this->mod_askemdros->font_selection));
            $this->load->view('view_top2');
            $this->load->view('view_menu_bar');
            $this->load->view('view_alert_dialog');

            $center_text = $this->load->view('view_edit_quiz',
                                             array('decoded_3et_json' => json_encode($this->mod_askemdros->decoded_3et),
                                                   'dbinfo_json' => $this->mod_askemdros->dbinfo_json,
                                                   'localization_json' => $this->mod_askemdros->localization_json,
                                                   'typeinfo_json' => $this->mod_askemdros->typeinfo_json,
                                                   'universe' => json_encode($this->mod_askemdros->universe),
                                                   'dir' => dirname($_GET['quiz']),
                                                   'quiz' => substr(basename($_GET['quiz']),0,-4)), // Strips .3et
                                             true)
                . $this->load->view('view_passage_tree_script',
                                    array('tree_data' => $this->universe_tree->get_jstree(),
                                          'markedList' => $this->mod_askemdros->universe,
                                          'db' => $this->mod_askemdros->setup_db,
                                          'prop' => $this->mod_askemdros->setup_prop),
                                    true);

            $this->load->view('view_main_page', array('left' => "<h1>Edit Quiz</h1>
                                                                 <p>Using database:<br>
                                                                 {$this->loc->dbdescription}
                                                                 Version&nbsp;{$this->db_config->dbinfo->databaseVersion}</p>",
                                                      'center' => $center_text));
            $this->load->view('view_bottom');
        }
        catch (DataException $e) {
            $this->error_view($e->getMessage(), 'Edit Quiz');
        }
    }

    public function new_quiz() {
        try {
            if (!isset($_POST['dir']))
                throw new DataException("Missing folder name");
            $dir = trim($_POST['dir']);

            if (!isset($_POST['db']))
                throw new DataException("Missing database name");
            $db = $_POST['db'];

            $this->mod_users->check_admin();

            $this->load->model('mod_askemdros');

            $decoded_3et_json = $this->mod_askemdros->new_quiz($db);

            $this->loc = json_decode($this->db_config->localization_json);
            $this->load->library('universe_tree', array('markedList' => $this->mod_askemdros->universe));

            $javascripts = array('jstree/jquery.jstree.js',
                                 'ckeditor/ckeditor.js',
                                 'ckeditor/adapters/jquery.js',
                                 'js/editquiz.js');

            switch ($this->db_config->dbinfo->charSet) {
              case 'hebrew':
                    $javascripts[] = 'virtualkeyboard/vk_loader.js?vk_layout=IL%20Biblical%20Hebrew%20(SIL)&amp;vk_skin=goldie';
                    break;

              case 'greek':
                    $javascripts[] = 'virtualkeyboard/vk_loader.js?vk_layout=GR%20Greek%20Polytonic&amp;vk_skin=goldie';
                    break;

              case 'transliterated_hebrew':
                    // Nothing for now
                    break;
            }


            //  View
            $this->load->view('view_top1', array('title' => 'Edit Quiz',
                                                 'css_list' => array('styles/jstree.css'),
                                                 'js_list' => $javascripts));
            $this->load->view('view_font_css', array('fonts' => $this->mod_askemdros->font_selection));
            $this->load->view('view_top2');
            $this->load->view('view_menu_bar');
            $this->load->view('view_alert_dialog');

            $center_text = $this->load->view('view_edit_quiz',
                                             array('decoded_3et_json' => $decoded_3et_json,
                                                   'dbinfo_json' => $this->mod_askemdros->dbinfo_json,
                                                   'localization_json' => $this->mod_askemdros->localization_json,
                                                   'typeinfo_json' => $this->mod_askemdros->typeinfo_json,
                                                   'universe' => '[]',
                                                   'dir' => $dir,
                                                   'quiz' => null),
                                             true)
                . $this->load->view('view_passage_tree_script',
                                    array('tree_data' => $this->universe_tree->get_jstree(),
                                          'markedList' => array(),
                                          'db' => $this->mod_askemdros->setup_db,
                                          'prop' => $this->mod_askemdros->setup_prop),
                                    true);

            $this->load->view('view_main_page', array('left' => "<h1>Edit Quiz</h1>
                                                                 <p>Using database:<br>
                                                                 {$this->loc->dbdescription}
                                                                 Version&nbsp;{$this->db_config->dbinfo->databaseVersion}</p>",
                                                      'center' => $center_text));
            $this->load->view('view_bottom');
        }
        catch (DataException $e) {
            $this->error_view($e->getMessage(), 'Edit Quiz');
        }
    }


    public function check_submit_quiz() {
        try {
            $this->mod_users->check_admin();

            if (!isset($_GET['dir']))
                throw new DataException("Missing folder name");
            if (!isset($_GET['quiz']))
                throw new DataException("Missing quiz filename");

            // MODEL:
            $this->load->model('mod_quizpath');
            $this->mod_quizpath->init(rawurldecode($_GET['dir']) . '/' . rawurldecode($_GET['quiz']) . '.3et', false, false, false);
            if ($this->mod_quizpath->file_exists())
                echo "EXISTS\n";
            else
                echo "OK\n";
        }
        catch (DataException $e) {
            echo $e->getMessage(),"\n";
        }
    }

    public function submit_quiz() {
        try {
            $this->mod_users->check_admin();

            if (!isset($_POST['dir']))
                throw new DataException("Missing folder name");
            if (!isset($_POST['quiz']))
                throw new DataException("Missing quiz filename");

            if (!isset($_POST['quizdata']))
                throw new DataException("Missing quiz data");

            // MODEL:
            $this->load->model('mod_quizpath');
            $this->load->model('mod_askemdros');

            $this->mod_quizpath->init(rawurldecode($_POST['dir']) . '/' . rawurldecode($_POST['quiz']) . '.3et', false, false, false);
            $this->mod_askemdros->save_quiz(json_decode(urldecode($_POST['quizdata'])));

            redirect("/file_manager?dir=" . $_POST['dir']);
        }
        catch (DataException $e) {
            $this->error_view($e->getMessage(), 'Edit Quiz');
        }
    }

    private function strip_mql(string__OR__null $mql) {
        if ($mql)
            return preg_replace('/^[^\[]*\[ *([\w]+) *(focus)?/i', '[$1 NORETRIEVE ', $mql);
        else
            return '';
    }

    public function import_shebanq() {
        try {
            $this->mod_users->check_admin();

            if (!isset($_GET['id']) || !is_numeric($_GET['id']))
                throw new DataException("Missing or bad SHEBANQ ID");
            if (!isset($_GET['version']))
                throw new DataException("Missing SHEBANQ version");

            $tmpfname = tempnam(sys_get_temp_dir(), 'shebanq.'.getmypid());

            $ch = curl_init("http://shebanq.ancient-data.org/hebrew/query.json?id=".$_GET['id']);
            $fp = fopen($tmpfname, "w");

            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_exec($ch);
            curl_close($ch);
            fclose($fp);

            $this->load->helper('file');
            $json_data = read_file($tmpfname);
            @unlink($this->tmpfname);

            $data = json_decode($json_data);

            if ($data->good) {
                if (isset($data->data->versions->$_GET['version']))
                    echo "OK\n", $this->strip_mql($data->data->versions->$_GET['version']->mql), "\n";
                else
                    echo "ERROR\nVersion ", $_GET['version'], " does not exist\n";
            }
            else
                echo "ERROR\n{$data->msg[0][0]}: {$data->msg[0][1]}\n";
        }
        catch (DataException $e) {
            echo "ERROR\n", $e->getMessage(), "\n";
        }
    }
}
