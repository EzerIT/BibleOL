<?php
class Ctrl_urls extends MY_Controller {
    public $gloss_count = 300;

    public function __construct() {
        parent::__construct();
        $this->lang->load('urls', $this->language);
        $this->load->model('mod_urls');
    }

	public function index() {
        $this->select_lang();
    }

    public function select_lang() {
        try {
            $this->mod_users->check_admin();

            $heb_buttons = $this->mod_urls->get_heb_buttons();
            $aram_buttons = $this->mod_urls->get_aram_buttons();

            // VIEW:
            $this->load->view('view_top1', array('title' => $this->lang->line('urls')));
            $this->load->view('view_top2');
            $this->load->view('view_menu_bar', array('langselect' => true));

            $get_parms = array('src_lang' => 'all',
                               'buttonix' => null);

            $center_text = $this->load->view('view_select_gloss',
                                             array('heb_buttons' => $heb_buttons,
                                                   'aram_buttons' => $aram_buttons,
                                                   'gloss_count' => $this->gloss_count,
                                                   'editing' => 'url',
                                                   'get_parms' => $get_parms),
                                             true);
            $this->load->view('view_main_page', array('left_title' => $this->lang->line('select_gloss_range_head'),
                                                      'left' => sprintf($this->lang->line('select_gloss_range'),$this->gloss_count),
                                                      'center' => $center_text));
            $this->load->view('view_bottom');
     

        }
        catch (DataException $e) {
            $this->error_view($e->getMessage(), $this->lang->line('urls'));
        }
    }

    public function edit_url() {
        try {
            $this->mod_users->check_admin();

            $language = isset($_GET['src_lang']) ? $_GET['src_lang'] : 'heb';
            $button_index = isset($_GET['buttonix']) ? intval($_GET['buttonix']) : 0;

            switch ($language) {
              case 'heb':
                    $buttons = $this->mod_urls->get_heb_buttons();
                    $words = $button_index==-1
                        ? $this->mod_urls->get_frequent_glosses('Hebrew',$this->gloss_count)
                        : $this->mod_urls->get_glosses('Hebrew',$buttons[$button_index][1],$buttons[$button_index][2]);
                    $this->mod_urls->get_heb_urls('Hebrew',$words);
                    break;

              case 'aram':
                    $buttons = $this->mod_urls->get_aram_buttons();
                    $words = $button_index==-1
                        ? $this->mod_urls->get_frequent_glosses('Aramaic',$this->gloss_count)
                        : $this->mod_urls->get_glosses('Aramaic',$buttons[$button_index][1],$buttons[$button_index][2]);
                    $this->mod_urls->get_heb_urls('Aramaic',$words);
                    break;

              default:
                    throw new DataException($this->lang->line('illegal_lang_code'));
            }


            // VIEW:
            $this->load->view('view_top1', array('title' => $this->lang->line('urls')));
            $this->load->view('view_top2');
            $this->load->view('view_menu_bar', array('langselect' => true));

            $get_parms = array('src_lang' => $language,
                               'buttonix' => $button_index);
            
            $center_text = $this->load->view('view_select_gloss',
                                             array('buttons' => $buttons,
                                                   'gloss_count' => $this->gloss_count,
                                                   'editing' => 'url',
                                                   'get_parms' => $get_parms),
                                             true)
                . $this->load->view('view_edit_url',
                                    array('language' => $language,
                                          'words' => $words,
                                          'get_parms' => $get_parms),
                                    true);
            $this->load->view('view_main_page', array('left_title' => $this->lang->line('select_gloss_range_head'),
                                                      'left' => sprintf($this->lang->line('select_gloss_range'),$this->gloss_count),
                                                      'center' => $center_text));
            $this->load->view('view_bottom');
        }
        catch (DataException $e) {
            $this->error_view($e->getMessage(), $this->lang->line('urls'));
        }
    }

    public function change_url() {
        try {
            $this->mod_users->check_admin();

            if (!isset($_POST['link']) || !isset($_POST['icon'])
                || !isset($_POST['id']) || !is_numeric($_POST['id'])
                || !isset($_POST['requesturi'])
                || !isset($_POST['scrolltop'])
                || !is_numeric($_POST['scrolltop']))
                throw new DataException($this->lang->line('bad_post_parameters'));

            if (intval($_POST['id'])==-1) {
                // Create new link
                if (!isset($_POST['lex']) || !isset($_POST['language'])
                    || ($_POST['language']!='Hebrew' && $_POST['language']!='Aramaic'))
                    throw new DataException($this->lang->line('bad_post_parameters'));

                $this->mod_urls->create_heb_url($_POST['lex'], $_POST['language'], $_POST['link'], $_POST['icon']);
            }
            else
                $this->mod_urls->set_heb_url(intval($_POST['id']), $_POST['link'], $_POST['icon']);

            header("Location: " . site_url($_POST['requesturi']) . '&scrolltop=' . $_POST['scrolltop']);
        }
        catch (DataException $e) {
            $this->error_view($e->getMessage(), $this->lang->line('urls'));
        }
    }


    public function delete_url() {
        try {
            $this->mod_users->check_admin();

            if (!isset($_POST['urlid']) || !is_numeric($_POST['urlid'])
                || !isset($_POST['requesturi'])
                || !isset($_POST['scrolltop'])
                || !is_numeric($_POST['scrolltop']))
                throw new DataException($this->lang->line('bad_post_parameters'));

            $this->mod_urls->delete_heb_url(intval($_POST['urlid']));

            header("Location: " . site_url($_POST['requesturi']) . '&scrolltop=' . $_POST['scrolltop']);
        }
        catch (DataException $e) {
            $this->error_view($e->getMessage(), $this->lang->line('urls'));
        }

    }

  }
