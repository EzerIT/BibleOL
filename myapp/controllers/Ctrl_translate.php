<?php
function stripSortIndex(string $s) {
    return (strlen($s)>0 && substr($s,0,1)==='#')
         ? substr(strstr($s," "),1)
         : $s;
}


class Ctrl_translate extends MY_Controller {
    public $gloss_count = 100;  // Number of glosses per frequency button


    public function __construct() {
        parent::__construct();
        $this->lang->load('translate', $this->language);
        $this->lang->load('urls', $this->language);
        $this->load->model('mod_translate');
        $this->load->helper('translation');
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


            if (!empty($_SESSION['variant']))
                $untranslated = array(); // Variants do not keep track of untranslated items
            else
                $untranslated = $this->mod_translate->get_if_untranslated($lang_edit);

            // VIEW:
            $this->load->view('view_top1', array('title' => $this->lang->line('translate_user_interface')));
            $this->load->view('view_top2');
            $this->load->view('view_menu_bar', array('langselect' => true,
                                                     'more_help_items' => array('tr_ifgr/if' => 'help_this_page')));

            $get_parms = array('offset'    => $offset,
                               'sortorder' => $sortorder,
                               'orderby'   => $orderby,
                               'group'     => $textgroup,
                               'lang_show' => $lang_show,
                               'lang_edit' => $lang_edit);

            $center_text = $this->load->view('view_translate',
                                             array('editing'        => 'interface',
                                                   'get_parms'      => $get_parms,
                                                   'group_list'     => $textgroup_list,
                                                   'lang_list'      => $lang_list,
                                                   'alllines'       => $alllines,
                                                   'untranslated'   => $untranslated,
                                                   'lines_per_page' => $lines_per_page,
                                                   'line_count'     => $line_count,
                                                   'page_count'     => $page_count),
                                             true);
            $this->load->view('view_main_page', array('left_title' => $this->lang->line('translate_user_interface'),
                                                      'left'       => $this->lang->line('translate_interface_desc'),
                                                      'center'     => $center_text));
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

            if (!empty($_SESSION['variant']))
                $untranslated = array(); // Variants do not keep track of untranslated items
            else
                $untranslated = $this->mod_translate->get_grammar_untranslated($lang_edit);

            // VIEW:
            $this->load->view('view_top1', array('title' => $this->lang->line('translate_grammar_terms')));
            $this->load->view('view_top2');
            $this->load->view('view_menu_bar', array('langselect' => true,
                                                     'more_help_items' => array('tr_ifgr/gr' => 'help_this_page')));

            $get_parms = array('group'     => $grammargroup,
                               'db'        => $db,
                               'lang_show' => $lang_show,
                               'lang_edit' => $lang_edit);

            $center_text = $this->load->view('view_translate',
                                             array('editing'      => 'grammar',
                                                   'get_parms'    => $get_parms,
                                                   'group_list'   => $grammargroup_list,
                                                   'db_list'      => $db_list,
                                                   'lang_list'    => $lang_list,
                                                   'alllines'     => $alllines,
                                                   'untranslated' => $untranslated),
                                             true);
            $this->load->view('view_main_page', array('left_title' => $this->lang->line('translate_grammar_terms'),
                                                      'left'       => $this->lang->line('translate_grammar_desc'),
                                                      'center'     => $center_text));
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
            $latin_buttons = $this->mod_urls->get_latin_buttons_long();

            $heb_glosses = $this->mod_translate->get_number_glosses('heb');
            $aram_glosses = $this->mod_translate->get_number_glosses('aram');
            $greek_glosses = $this->mod_translate->get_number_glosses('greek');
            $latin_glosses = $this->mod_translate->get_number_glosses('latin');
            
            // VIEW:
            $this->load->view('view_top1', array('title' => $this->lang->line('translate_lex')));
            $this->load->view('view_top2');
            $this->load->view('view_menu_bar', array('langselect' => true,
                                                     'more_help_items' => array('tr_lex' => 'help_this_page')));

            $get_parms = array('src_lang' => 'all-with-greek',  // all-with-greek implies also Latin
                               'buttonix' => null);

            $center_text = $this->load->view('view_select_gloss',
                                             array('heb_buttons'   => $heb_buttons,
                                                   'aram_buttons'  => $aram_buttons,
                                                   'greek_buttons' => $greek_buttons,
                                                   'latin_buttons' => $latin_buttons,
                                                   'heb_glosses'   => $heb_glosses,
                                                   'aram_glosses'  => $aram_glosses,
                                                   'greek_glosses' => $greek_glosses,
                                                   'latin_glosses' => $latin_glosses,
                                                   'gloss_count'   => $this->gloss_count,
                                                   'editing'       => 'lexicon',
                                                   'get_parms'     => $get_parms),
                                             true);
            $this->load->view('view_main_page', array('left_title' => $this->lang->line('select_words_translate'),
                                                      'left'       => sprintf($this->lang->line('select_gloss_translate_range'),$this->gloss_count),
                                                      'center'     => $center_text));
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

                case 'latin':
                    $buttons = $this->mod_urls->get_latin_buttons_long();
                    list($stems,$books) = $this->mod_translate->get_localized_jvulgate();
                    break;

                default:
                    throw new DataException($this->lang->line('illegal_lang_code'));
            }

            $words = $button_index<0
                   ? $this->mod_translate->get_frequent_glosses($src_lang,$lang_edit,$lang_show,
                                                                (-1-$button_index)*$this->gloss_count,$this->gloss_count)
                   : $this->mod_translate->get_glosses($src_lang,$lang_edit,$lang_show,
                                                       $buttons[$button_index][1],$buttons[$button_index][2]);

            // VIEW:
            $this->load->view('view_top1', array('title' => $this->lang->line('translate_lex')));
            $this->load->view('view_top2');
            $this->load->view('view_menu_bar', array('langselect' => true,
                                                     'more_help_items' => array('tr_lex' => 'help_this_page')));

            $get_parms = array('src_lang'  => $src_lang,
                               'lang_show' => $lang_show,
                               'lang_edit' => $lang_edit,
                               'buttonix'  => $button_index);

            $center_text = $this->load->view('view_select_gloss',
                                             array('buttons'     => $buttons,
                                                   'num_glosses' => $this->mod_translate->get_number_glosses($src_lang),
                                                   'gloss_count' => $this->gloss_count,
                                                   'editing'     => 'lexicon',
                                                   'get_parms'   => $get_parms),
                                             true)
                         . $this->load->view('view_translate',
                                             array('editing'   => 'lexicon',
                                                   'get_parms' => $get_parms,
                                                   'lang_list' => $dst_langs,
                                                   'alllines'  => $words,
                                                   'stems'     => $stems,
                                                   'books'     => $books),
                                             true);

            $this->load->view('view_main_page', array('left_title' => $this->lang->line('translate_lex'),
                                                      'left'       => $this->lang->line('translate_lex_desc'),
                                                      'center'     => $center_text));
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
			die("Usage: php index.php translate if_db2php <language code>[_<variant>] <destination directory>\n");

        $this->mod_translate->if_db2php($_SERVER['argv'][3],$_SERVER['argv'][4]);
    }

    function if_php2db() {
        if (!is_cli()) {
            echo '<pre>This command can only be run from the command line</pre>';
            die;
        }

        $incr = isset($_SERVER['argv'][3]) ? $_SERVER['argv'][3]=='-i' : false;
        $lc_ix = $incr ? 4 : 3;

		if ($_SERVER['argc']!=$lc_ix+2)
		    die("Usage: php index.php translate if_php2db [-i] <language code>[_variant] <source directory>\n");

        if ($_SERVER['argv'][$lc_ix] == 'comment')
            $this->mod_translate->if_phpcomment2db($_SERVER['argv'][$lc_ix+1], $incr);
        else
            $this->mod_translate->if_php2db($_SERVER['argv'][$lc_ix], $_SERVER['argv'][$lc_ix+1], $incr);
    }

    // $a is an array of the form ["key1" => ["key2" => "x"]]. This function returns the max length of key1 and key2
    private function find_lengths(array $a) {
        $k1 = 0;
        $k2 = 0;
        foreach ($a as $key1 => $bb) {
            if (strlen($key1)>$k1) $k1=strlen($key1);
            foreach ($bb as $key2 => $bbb) {
                if (strlen($key2)>$k2) $k2=strlen($key2);
            }
        }
        return [$k1,$k2];
    }
    
    function if_php_cmp_db() {
        if (!is_cli()) {
            echo '<pre>This command can only be run from the command line</pre>';
            die;
        }

		if ($_SERVER['argc']!=5)
		    die("Usage: php index.php translate if_php_cmp_db <language code>[_variant] <source directory>\n");

        $only_db = [];
        $only_php = [];
        $diff = [];
        $not_in_comment = [];
        
        $this->mod_translate->if_php_cmp_db($_SERVER['argv'][3],$_SERVER['argv'][4], $only_db, $only_php, $diff, $not_in_comment);

        if (!empty($only_db)) {
            list($tglen,$keylen) = $this->find_lengths($only_db);

            echo "ONLY IN DATABASE:\n";
            foreach ($only_db as $textgroup => $entries)
                foreach ($entries as $key => $value)
                    printf("|%-{$tglen}s|%-{$keylen}s|%s|\n",$textgroup,$key,$value);
            echo "\n";
        }

        if (!empty($only_php)) {
            list($tglen,$keylen) = $this->find_lengths($only_php);

            echo "ONLY IN PHP:\n";
            foreach ($only_php as $textgroup => $entries)
                foreach ($entries as $key => $value)
                    printf("|%-{$tglen}s|%-{$keylen}s|%s|\n",$textgroup,$key,$value);
            echo "\n";
        }

        if (!empty($diff)) {
            list($tglen,$keylen) = $this->find_lengths($diff);

            echo "DIFFERENT VALUES:\n";
            foreach ($diff as $textgroup => $entries)
                foreach ($entries as $key => $value) {
                    printf("|%-{$tglen}s|%-{$keylen}s|PHP|%s|\n",$textgroup,$key,$value[0]);
                    printf("|%-{$tglen}s|%-{$keylen}s|DB |%s|\n",$textgroup,$key,$value[1]);
                    echo "----------------------------------------------------------------\n";
                }
        }

        if (!empty($not_in_comment)) {
            echo "NOT IN COMMENT FILE:\n";
            foreach ($not_in_comment as $nic)
                echo $nic,"\n";
        }
    }

    
    function gram_db2prop() {
        if (!is_cli()) {
            echo '<pre>This command can only be run from the command line</pre>';
            die;
        }

		if ($_SERVER['argc']!=4 && $_SERVER['argc']!=5)
			die("Usage: php index.php translate gram_db2prop <destination directory> [<variant>]\n");

        $this->mod_translate->gram_db2prop($_SERVER['argv'][3], isset($_SERVER['argv'][4]) ? $_SERVER['argv'][4] : null);
    }

    function gram_prop2db() {
        if (!is_cli()) {
            echo '<pre>This command can only be run from the command line</pre>';
            die;
        }

		if ($_SERVER['argc']!=4 && $_SERVER['argc']!=5)
			die("Usage: php index.php translate gram_prop2db <source directory> [<variant>]\n");

        $this->mod_translate->gram_prop2db($_SERVER['argv'][3], isset($_SERVER['argv'][4]) ? $_SERVER['argv'][4] : null);
    }

    function select_download_lex()
    {
        try {
            $this->mod_users->check_translator();
            $this->load->helper('create_lexicon_helper');

            $lexicon_lang_list = $this->mod_translate->get_all_lexicon_langs();

            $all_lex = array();

            foreach ($lexicon_lang_list as $src_lang => $targets) {
                foreach ($targets as $dst_lang => $dst_lang_name) {
                    $all_lex[] = array('from_name' => $this->lang->line('lang_'.$src_lang),
                                       'to_name'   => $dst_lang_name,
                                       'variant'   => null,
                                       'url'       => site_url("/translate/download_lex/$src_lang/$dst_lang"));

                    if (!empty($this->config->item('variants')))
                        foreach ($this->config->item('variants') as $variant) {
                            if (!$this->mod_translate->empty_lex($src_lang, $dst_lang, $variant))
                                $all_lex[] = array('from_name' => $this->lang->line('lang_'.$src_lang),
                                                   'to_name'   => $dst_lang_name,
                                                   'variant'   => $variant,
                                                   'url'       => site_url("/translate/download_lex/$src_lang/$dst_lang/$variant"));
                        }
                }
            }
            
            // VIEW:
            $this->load->view('view_top1', array('title' => $this->lang->line('download_lex')));
            $this->load->view('view_top2');
            $this->load->view('view_menu_bar', array('langselect' => true,
                                                     'more_help_items' => array('down_lex' => 'help_this_page')));

            $center_text = $this->load->view('view_download_lex',
                                             array('all_lex' => $all_lex),true);

            $this->load->view('view_main_page', array('left_title' => $this->lang->line('download_lex'),
                                                      'left'       => $this->lang->line('download_lex_desc'),
                                                      'center'     => $center_text));
            $this->load->view('view_bottom');
        }
        catch (DataException $e) {
            $this->error_view($e->getMessage(), $this->lang->line('download_lex'));
        }
    }

    function download_lex_old()
    {
        // Download a lexicon in "old" format (that is, without transliterated lexemes)

        if (!is_cli()) {
            echo '<pre>This command can only be run from the command line</pre>';
            die;
        }
        
        if ($_SERVER['argc']!=5 && $_SERVER['argc']!=6)
            die("Usage: php index.php translate download_lex_old <source language> <target language> [<variant>]\n");

        $src_lang = strtolower($_SERVER['argv'][3]);
        $dst_lang = strtolower($_SERVER['argv'][4]);
        $variant = isset($_SERVER['argv'][5]) ? $_SERVER['argv'][5] : null;

        try {
            $result = $this->mod_translate->download_lex($src_lang, $dst_lang, $variant, true);
        }
        catch (DataException $e) {
            die($e->getMessage() . "\n");
        }

        echo $result;
    }

    function download_lex()
    {
        if (is_cli()) {
            if ($_SERVER['argc']!=5 && $_SERVER['argc']!=6)
                die("Usage: php index.php translate download_lex <source language> <target language> [<variant>]\n");

            $src_lang = strtolower($_SERVER['argv'][3]);
            $dst_lang = strtolower($_SERVER['argv'][4]);
            $variant = isset($_SERVER['argv'][5]) ? $_SERVER['argv'][5] : null;

            try {
                $result = $this->mod_translate->download_lex($src_lang, $dst_lang, $variant);
            }
            catch (DataException $e) {
                die($e->getMessage() . "\n");
            }

            echo $result;
        }
        else {
            try {
                $this->mod_users->check_translator();
                
                if ($this->uri->total_segments()!=4 && $this->uri->total_segments()!=5) {
                    throw new DataException($this->lang->line('malformed_url'));
                }

                $src_lang = strtolower($this->uri->segment(3));
                $dst_lang = strtolower($this->uri->segment(4));
                $variant = $this->uri->segment(5); // Will be "" if segment(5) does not exist

                $result = $this->mod_translate->download_lex($src_lang, $dst_lang, $variant);


                // Output download headers:
                header("Content-Type: text/csv");
                header("Content-Length: " . strlen($result));
                if ($variant)
                    header("Content-Disposition: attachment; filename=\"{$src_lang}_{$dst_lang}_{$variant}.csv\"");
                else
                    header("Content-Disposition: attachment; filename=\"{$src_lang}_{$dst_lang}.csv\"");

                echo $result;
            }
            catch (DataException $e) {
                $this->error_view($e->getMessage(), $this->lang->line('download_lex'));
            }
        }
    }

    function import_lex()
    {
        if (!is_cli()) {
            echo '<pre>This command can only be run from the command line</pre>';
            die;
        }

		if ($_SERVER['argc']!=6 && $_SERVER['argc']!=7)
			die("Usage: php index.php translate import_lex <source language> <target language> <CSV file> [<variant>]\n");

        try {
            $variant = isset($_SERVER['argv'][6]) ? $_SERVER['argv'][6] : null;
            $this->mod_translate->import_lex($_SERVER['argv'][3],$_SERVER['argv'][4],$_SERVER['argv'][5],$variant);
        }
        catch (DataException $e) {
            die($e->getMessage() . "\n");
        }
    }

    public function list_translations()
    {
        try {
            $this->mod_users->check_translator();

            $avail_translations = get_available_translations();

            usort($avail_translations, function($a, $b) { return $a->internal <=> $b->internal; });
            
            $trans_if_items = array();
            $trans_hebgrammar_items = array();
            $trans_hebtgrammar_items = array();
            $trans_greekgrammar_items = array();
            $trans_latingrammar_items = array();
            $trans_heblex_items = array();
            $trans_aramlex_items = array();
            $trans_greeklex_items = array();
            $trans_latinlex_items = array();
            foreach ($avail_translations as $t) {
                $trans_if_items[$t->abb] = array($this->mod_translate->count_if_translated($t->abb),
                                                 $this->mod_translate->count_if_lines(null));
                $trans_hebgrammar_items[$t->abb] = array($this->mod_translate->count_grammar_translated('ETCBC4',$t->abb),
                                                         $this->mod_translate->count_grammar_lines('ETCBC4'));
                $trans_hebtgrammar_items[$t->abb] = array($this->mod_translate->count_grammar_translated('ETCBC4-translit',$t->abb),
                                                          $this->mod_translate->count_grammar_lines('ETCBC4-translit'));
                $trans_greekgrammar_items[$t->abb] = array($this->mod_translate->count_grammar_translated('nestle1904',$t->abb),
                                                           $this->mod_translate->count_grammar_lines('nestle1904'));
                $trans_latingrammar_items[$t->abb] = array($this->mod_translate->count_grammar_translated('jvulgate',$t->abb),
                                                           $this->mod_translate->count_grammar_lines('jvulgate'));
                $trans_heblex_items[$t->abb] = array($this->mod_translate->count_lex_translated('Hebrew',$t->abb),
                                                     $this->mod_translate->count_lex_lines('Hebrew'));
                $trans_aramlex_items[$t->abb] = array($this->mod_translate->count_lex_translated('Aramaic',$t->abb),
                                                      $this->mod_translate->count_lex_lines('Aramaic'));
                $trans_greeklex_items[$t->abb] = array($this->mod_translate->count_lex_translated('greek',$t->abb),
                                                       $this->mod_translate->count_lex_lines('greek'));
                $trans_latinlex_items[$t->abb] = array($this->mod_translate->count_lex_translated('latin',$t->abb),
                                                       $this->mod_translate->count_lex_lines('latin'));
            }
            
            // VIEW:
            $this->load->view('view_top1', array('title' => 'Available localizations'));
            $this->load->view('view_top2');
            $this->load->view('view_menu_bar', array('langselect' => true,
                                                     'more_help_items' => array('avail_trans' => 'help_this_page')));
            $center_text = $this->load->view('view_language_list',
                                             array('avail_translations'       => $avail_translations,
                                                   'trans_if_items'           => $trans_if_items,
                                                   'trans_hebgrammar_items'   => $trans_hebgrammar_items,
                                                   'trans_hebtgrammar_items'  => $trans_hebtgrammar_items,
                                                   'trans_greekgrammar_items' => $trans_greekgrammar_items,
                                                   'trans_latingrammar_items' => $trans_latingrammar_items,
                                                   'trans_heblex_items'       => $trans_heblex_items,
                                                   'trans_aramlex_items'      => $trans_aramlex_items,
                                                   'trans_greeklex_items'     => $trans_greeklex_items,
                                                   'trans_latinlex_items'     => $trans_latinlex_items,
                                             ),
                                             true);
            $this->load->view('view_main_page', array('left_title' => 'Available Languages',
                                                      'left' => '<p>Here you can enable or disable localizations, and you can add new localization languages.</p>',
                                                      'center' => $center_text));
            $this->load->view('view_bottom');
        }
        catch (DataException $e) {
            $this->error_view($e->getMessage(), 'Available localizations');
        }
    }

    public function modify_localization() {
        try {
            $this->mod_users->check_translator();

            if ($this->uri->total_segments()!=5)
                throw new DataException($this->lang->line('malformed_url'));

            $enable = strtolower($this->uri->segment(3))=='enable';
            $loc_type = strtolower($this->uri->segment(4)); // 'iface', 'heblex', 'greeklex', or 'latinlex'
            $lang_abb = strtolower($this->uri->segment(5));


            if ($loc_type!='iface' && $loc_type!='heblex' && $loc_type!='greeklex' && $loc_type!='latinlex')
                throw new DataException($this->lang->line('malformed_url'));
            
            $this->mod_translate->modify_localization($enable,$loc_type,$lang_abb);

            redirect(site_url("/translate/list_translations"));
        }
        catch (DataException $e) {
            $this->error_view($e->getMessage(), 'Available localizations');
        }
    }

    public function add_language() {
        try {
            $this->mod_users->check_translator();

            if (!isset($_POST['internal-name'])
                || !isset($_POST['native-name'])
                || !isset($_POST['abbrev']))
            throw new DataException($this->lang->line('bad_post_parameters'));
            
            $internal_name = str_replace(' ','_',strtolower(trim($_POST['internal-name'])));
            $native_name = trim($_POST['native-name']);
            $abbrev = trim($_POST['abbrev']);

            $this->mod_translate->add_language($abbrev, $internal_name, $native_name);

            redirect(site_url("/translate/list_translations"));
        }
        catch (DataException $e) {
            $this->error_view($e->getMessage(), 'Create language');
        }
    }
}
