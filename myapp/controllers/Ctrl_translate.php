<?php
class Ctrl_translate extends MY_Controller {

    public function __construct() {
        parent::__construct();
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
            
            $lang_list = $this->mod_translate->get_all_languages();
            asort($lang_list);
            
            $lang_show = isset($_GET['lang_show']) ? $_GET['lang_show'] : 'en';
            if (!array_key_exists($lang_show, $lang_list))
                $lang_show = 'en';

            $lang_edit = isset($_GET['lang_edit']) ? $_GET['lang_edit'] : 'da';
            if (!array_key_exists($lang_edit, $lang_list))
                throw new DataException('Uknown destination language');

            $textgroup = isset($_GET['textgroup']) ? $_GET['textgroup'] : '';
            if (!in_array($textgroup, $textgroup_list))
                $textgroup = $textgroup_list[0];

            $lines_per_page = 20; // $this->config->item('lines_per_page');
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
            $this->load->view('view_top1', array('title' => 'Translate User Interface'));
            $this->load->view('view_top2');
            $this->load->view('view_menu_bar', array('langselect' => true));
            $this->load->view('view_confirm_dialog');

            $get_parms = array('offset' => $offset,
                               'sortorder' => $sortorder,
                               'orderby' => $orderby,
                               'textgroup' => $textgroup,
                               'lang_show' => $lang_show,
                               'lang_edit' => $lang_edit);

            $center_text = $this->load->view('view_if_lines_list',
                                             array('get_parms' => $get_parms,
                                                   'textgroup_list' => $textgroup_list,
                                                   'lang_list' => $lang_list,
                                                   'alllines' => $alllines,
                                                   'untranslated' => $untranslated,
                                                   'lines_per_page' => $lines_per_page,
                                                   'line_count' => $line_count,
                                                   'page_count' => $page_count),
                                             true);
            $this->load->view('view_main_page', array('left_title' => 'Translate User Interface',
                                                      'left' => 'Provide a translation of each item for the user interface.',
                                                      'center' => $center_text));
            $this->load->view('view_bottom');
        }
        catch (DataException $e) {
            $this->error_view($e->getMessage(), 'Translate User Interface');
        }
    }


    function update_if()
    {
        try {
            $this->mod_users->check_translator();

            if (!isset($_GET['lang_edit']))
                throw new DataException('Missing language identification');
            if (!isset($_GET['textgroup']))
                throw new DataException('Missing text group identification');

            $lang_edit = $_GET['lang_edit'];
            $textgroup = $_GET['textgroup'];

            $this->mod_translate->update_if_lines($lang_edit, $textgroup, $_POST);

            redirect("/translate/translate_if?$_SERVER[QUERY_STRING]");
        }
        catch (DataException $e) {
            $this->error_view($e->getMessage(), 'Translate User Interface');
        }
    }

  }