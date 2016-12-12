<?php
function stripSortIndex(string $s) {
    return (strlen($s)>0 && substr($s,0,1)==='#')
              ? substr(strstr($s," "),1)
              : $s;
}


class Ctrl_translate extends MY_Controller {
    public $gloss_count = 100;


    public function __construct() {
        parent::__construct();
        $this->lang->load('translate', $this->language);
        $this->lang->load('urls', $this->language);
        $this->load->model('mod_translate');
    }

	public function index() {
        $this->translate_if();
    }

    public function translate_if() {
        try {
            $this->mod_users->check_translator();

            $textgroup_list = $this->mod_translate->get_textgroup_list();
            sort($textgroup_list);

            $lang_list = $this->mod_translate->get_all_if_languages();
            asort($lang_list);

            $lang_show = isset($_GET['lang_show']) ? $_GET['lang_show'] : 'en';
            if (!array_key_exists($lang_show, $lang_list))
                $lang_show = 'en';

            $lang_edit = isset($_GET['lang_edit']) ? $_GET['lang_edit'] : 'da';
            if (!array_key_exists($lang_edit, $lang_list))
                throw new DataException('Uknown destination language');

            $textgroup = isset($_GET['group']) ? $_GET['group'] : '';
            if (!in_array($textgroup, $textgroup_list))
                $textgroup = $textgroup_list[0];

            $lines_per_page = $this->config->item('lines_per_page');
            $line_count = $this->mod_translate->count_if_lines($textgroup);
            $page_count = intval(ceil($line_count/$lines_per_page));

            $offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
            if ($offset>=$page_count)
                $offset = $page_count-1;
            if ($offset<0)
                $offset = 0;

            if (isset($_GET['orderby']) && in_array($_GET['orderby'], array('symbolic_name','text_show','text_edit'),true))
                $orderby = $_GET['orderby'];
            else
                $orderby = 'symbolic_name';


            $sortorder = isset($_GET['sortorder']) ? $_GET['sortorder'] : 'asc';
            if ($sortorder!='desc' && $sortorder!='asc')
                $sortorder = 'asc';

            $alllines = $this->mod_translate->get_if_lines_part($lang_edit,$lang_show,$textgroup,$lines_per_page,$offset*$lines_per_page,$orderby,$sortorder);


            $untranslated = $this->mod_translate->get_if_untranslated($lang_edit);

            // VIEW:
            $this->load->view('view_top1', array('title' => $this->lang->line('translate_user_interface')));
            $this->load->view('view_top2');
            $this->load->view('view_menu_bar', array('langselect' => true));

            $get_parms = array('offset' => $offset,
                               'sortorder' => $sortorder,
                               'orderby' => $orderby,
                               'group' => $textgroup,
                               'lang_show' => $lang_show,
                               'lang_edit' => $lang_edit);

            $center_text = $this->load->view('view_translate',
                                             array('editing' => 'interface',
                                                   'get_parms' => $get_parms,
                                                   'group_list' => $textgroup_list,
                                                   'lang_list' => $lang_list,
                                                   'alllines' => $alllines,
                                                   'untranslated' => $untranslated,
                                                   'lines_per_page' => $lines_per_page,
                                                   'line_count' => $line_count,
                                                   'page_count' => $page_count),
                                             true);
            $this->load->view('view_main_page', array('left_title' => $this->lang->line('translate_user_interface'),
                                                      'left' => $this->lang->line('translate_interface_desc'),
                                                      'center' => $center_text));
            $this->load->view('view_bottom');
        }
        catch (DataException $e) {
            $this->error_view($e->getMessage(), $this->lang->line('translate_user_interface'));
        }
    }


    function update_if() {
        try {
            $this->mod_users->check_translator();

            if (!isset($_GET['lang_edit']))
                throw new DataException('Missing language identification');
            if (!isset($_GET['group']))
                throw new DataException('Missing text group identification');

            $lang_edit = $_GET['lang_edit'];
            $textgroup = $_GET['group'];

            $this->mod_translate->update_if_lines($lang_edit, $textgroup, $_POST);

            redirect("/translate/translate_if?$_SERVER[QUERY_STRING]");
        }
        catch (DataException $e) {
            $this->error_view($e->getMessage(), $this->lang->line('translate_user_interface'));
        }
    }


    public function translate_grammar() {
        try {
            $this->mod_users->check_translator();

            $db_list = $this->mod_translate->get_all_db();
            asort($db_list);

            $lang_list = $this->mod_translate->get_all_if_languages();
            asort($lang_list);

            $db = isset($_GET['db']) ? $_GET['db'] : 'ETCBC4';
            if (!in_array($db, $db_list))
                $db = 'ETCBC4';
            
            $lang_show = isset($_GET['lang_show']) ? $_GET['lang_show'] : 'en';
            if (!array_key_exists($lang_show, $lang_list))
                $lang_show = 'en';

            $lang_edit = isset($_GET['lang_edit']) ? $_GET['lang_edit'] : 'da';
            if (!array_key_exists($lang_edit, $lang_list))
                throw new DataException('Uknown destination language');

            $grammargroup_list = $this->mod_translate->get_grammargroup_list($db);

            $grammargroup = isset($_GET['group']) ? $_GET['group'] : '';
            if (!in_array($grammargroup, $grammargroup_list))
                $grammargroup = $grammargroup_list[0];

            $alllines = $this->mod_translate->get_grammar_lines_part($lang_edit,$lang_show,$grammargroup);

            $untranslated = $this->mod_translate->get_grammar_untranslated($lang_edit);

            // VIEW:
            $this->load->view('view_top1', array('title' => $this->lang->line('translate_grammar_terms')));
            $this->load->view('view_top2');
            $this->load->view('view_menu_bar', array('langselect' => true));

            $get_parms = array('group' => $grammargroup,
                               'db' => $db,
                               'lang_show' => $lang_show,
                               'lang_edit' => $lang_edit);

            $center_text = $this->load->view('view_translate',
                                             array('editing' => 'grammar',
                                                   'get_parms' => $get_parms,
                                                   'group_list' => $grammargroup_list,
                                                   'db_list' => $db_list,
                                                   'lang_list' => $lang_list,
                                                   'alllines' => $alllines,
                                                   'untranslated' => $untranslated),
                                             true);
            $this->load->view('view_main_page', array('left_title' => $this->lang->line('translate_grammar_terms'),
                                                      'left' => $this->lang->line('translate_grammar_desc'),
                                                      'center' => $center_text));
            $this->load->view('view_bottom');
        }
        catch (DataException $e) {
            $this->error_view($e->getMessage(), $this->lang->line('translate_grammar_terms'));
        }
    }
    
    function update_grammar() {
        try {
            $this->mod_users->check_translator();

            if (!isset($_GET['lang_edit']))
                throw new DataException('Missing language identification');
            if (!isset($_GET['db']))
                throw new DataException('Missing text database identification');

            $lang_edit = $_GET['lang_edit'];
            $db = $_GET['db'];

            $this->mod_translate->update_grammar_lines($lang_edit, $db, $_POST);

            redirect("/translate/translate_grammar?$_SERVER[QUERY_STRING]");
        }
        catch (DataException $e) {
            $this->error_view($e->getMessage(), $this->lang->line('translate_grammar_terms'));
        }
    }

    public function translate_lex() {
        $this->load->model('mod_urls');
        try {
            $this->mod_users->check_translator();

            $heb_buttons = $this->mod_urls->get_heb_buttons_long();
            $aram_buttons = $this->mod_urls->get_aram_buttons_long();
            $greek_buttons = $this->mod_urls->get_greek_buttons_long();

            // VIEW:
            $this->load->view('view_top1', array('title' => $this->lang->line('translate_lex')));
            $this->load->view('view_top2');
            $this->load->view('view_menu_bar', array('langselect' => true));

            $get_parms = array('src_lang' => 'all-with-greek',
                               'buttonix' => null);

            $center_text = $this->load->view('view_select_gloss',
                                             array('heb_buttons' => $heb_buttons,
                                                   'aram_buttons' => $aram_buttons,
                                                   'greek_buttons' => $greek_buttons,
                                                   'gloss_count' => $this->gloss_count,
                                                   'editing' => 'lexicon',
                                                   'get_parms' => $get_parms),
                                             true);
            $this->load->view('view_main_page', array('left_title' => $this->lang->line('select_words_translate'),
                                                      'left' => sprintf($this->lang->line('select_gloss_translate_range'),$this->gloss_count),
                                                      'center' => $center_text));
            $this->load->view('view_bottom');
        }
        catch (DataException $e) {
            $this->error_view($e->getMessage(), $this->lang->line('translate_lex'));
        }
    }

    public function edit_lex() {
        $this->load->model('mod_urls');
        try {
            $this->mod_users->check_translator();

            $src_lang = isset($_GET['src_lang']) ? $_GET['src_lang'] : 'heb';
            $button_index = isset($_GET['buttonix']) ? intval($_GET['buttonix']) : 0;

            if (!is_numeric($button_index))
                $button_index = 0;
            else
                $button_index = intval($button_index);

            $lexicon_lang_list = $this->mod_translate->get_all_lexicon_langs();
            $dst_langs = $lexicon_lang_list[$src_lang];

            $lang_show = isset($_GET['lang_show']) ? $_GET['lang_show'] : 'en';
            if (!array_key_exists($lang_show, $dst_langs))
                $lang_show = 'en';

            $lang_edit = isset($_GET['lang_edit']) ? $_GET['lang_edit'] : 'en';
            if (!array_key_exists($lang_edit, $dst_langs))
                throw new DataException('Uknown destination language');

            switch ($src_lang) {
              case 'heb':
                    $buttons = $this->mod_urls->get_heb_buttons_long();
                    list($stems,$books) = $this->mod_translate->get_localized_ETCBC4();
                    break;

              case 'aram':
                    $buttons = $this->mod_urls->get_aram_buttons_long();
                    list($stems,$books) = $this->mod_translate->get_localized_ETCBC4();
                    break;

              case 'greek':
                    $buttons = $this->mod_urls->get_greek_buttons_long();
                    list($stems,$books) = $this->mod_translate->get_localized_nestle1904();
                    break;

              default:
                    throw new DataException($this->lang->line('illegal_lang_code'));
            }

            $words = $button_index==-1
                ? $this->mod_translate->get_frequent_glosses($src_lang,$lang_edit,$lang_show,$this->gloss_count)
                : $this->mod_translate->get_glosses($src_lang,$lang_edit,$lang_show,$buttons[$button_index][1],$buttons[$button_index][2]);

            // VIEW:
            $this->load->view('view_top1', array('title' => $this->lang->line('translate_lex')));
            $this->load->view('view_top2');
            $this->load->view('view_menu_bar', array('langselect' => true));

            $get_parms = array('src_lang' => $src_lang,
                               'lang_show' => $lang_show,
                               'lang_edit' => $lang_edit,
                               'buttonix' => $button_index);

            $center_text = $this->load->view('view_select_gloss',
                                             array('buttons' => $buttons,
                                                   'gloss_count' => $this->gloss_count,
                                                   'editing' => 'lexicon',
                                                   'get_parms' => $get_parms),
                                             true)
                . $this->load->view('view_translate',
                                             array('editing' => 'lexicon',
                                                   'get_parms' => $get_parms,
                                                   'lang_list' => $dst_langs,
                                                   'alllines' => $words,
                                                   'stems' => $stems,
                                                   'books' => $books),
                                             true);

            $this->load->view('view_main_page', array('left_title' => $this->lang->line('translate_lex'),
                                                      'left' => $this->lang->line('translate_lex_desc'),
                                                      'center' => $center_text));
            $this->load->view('view_bottom');
        }
        catch (DataException $e) {
            $this->error_view($e->getMessage(), $this->lang->line('translate_lex'));
        }
    }

    function update_lex() {
        try {
            $this->mod_users->check_translator();

            if (!isset($_GET['src_lang']) || !isset($_GET['lang_edit']))
                throw new DataException('Missing language identification');

            $this->mod_translate->update_glosses($_GET['src_lang'], $_GET['lang_edit'], $_POST);

            redirect("/translate/edit_lex?$_SERVER[QUERY_STRING]");
        }
        catch (DataException $e) {
            $this->error_view($e->getMessage(), $this->lang->line('translate_lex'));
        }
    }


    
    function if_db2php() {
        if (!is_cli()) {
            echo '<pre>This command can only be run from the command line</pre>';
            die;
        }

		if ($_SERVER['argc']!=5)
			die("Usage: php index.php translate if_db2php <language code> <destination directory>\n");

        $this->mod_translate->if_db2php($_SERVER['argv'][3],$_SERVER['argv'][4]);
    }

    function if_php2db() {
        if (!is_cli()) {
            echo '<pre>This command can only be run from the command line</pre>';
            die;
        }

		if ($_SERVER['argc']!=5)
			die("Usage: php index.php translate if_php2db <language code> <source directory>\n");

        if ($_SERVER['argv'][3] == 'comment')
            $this->mod_translate->if_phpcomment2db($_SERVER['argv'][4]);
        else
            $this->mod_translate->if_php2db($_SERVER['argv'][3],$_SERVER['argv'][4]);
    }

    function gram_db2prop() {
        if (!is_cli()) {
            echo '<pre>This command can only be run from the command line</pre>';
            die;
        }

		if ($_SERVER['argc']!=4)
			die("Usage: php index.php translate gram_db2prop <destination directory>\n");

        $this->mod_translate->gram_db2prop($_SERVER['argv'][3]);
    }

    function gram_prop2db() {
        if (!is_cli()) {
            echo '<pre>This command can only be run from the command line</pre>';
            die;
        }

        $this->mod_translate->gram_prop2db();
    }

    
  }