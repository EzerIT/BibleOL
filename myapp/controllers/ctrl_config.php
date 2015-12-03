<?php
class Ctrl_config extends MY_Controller {
    public function __construct() {
        parent::__construct();
    }

	public function index() {
        $this->fonts();
    }


    public function fonts() {
        $this->lang->load('font', $this->language);

        // MODEL:
        $this->load->model('mod_localize');
        $this->load->model('mod_config');
        if (!$this->mod_users->is_logged_in()) {
            // Not logged in

            // VIEW:
            $this->load->view('view_top1', array('title' => $this->lang->line('font_settings')));
            $this->load->view('view_top2');
            $this->load->view('view_menu_bar', array('langselect' => true));
            $this->load->view('view_error',array('text' => $this->lang->line('must_be_logged_in')));
            $this->load->view('view_bottom');
            return;
        }

        $alphabets = $this->mod_config->alphabets();

        $font_settings = array();
        $avail_fonts = array();
        $personal_fonts = array();
        $choice_values = array();

        foreach ($alphabets as $alph) {
            $font_setting[$alph] = $this->mod_config->font_setting($alph);
            $avail_fonts[$alph] = $this->mod_config->avail_fonts($alph);
            $personal_fonts[$alph] = $this->mod_config->personal_font($alph);
            $choice_values[$alph] = $alph . '_' . $this->mod_config->get_radio_button_value($font_setting[$alph]->font_family,
                                                                                            $avail_fonts[$alph],
                                                                                            $personal_fonts[$alph]);
        }

        $this->load->helper('form');
        // $this->load->library('form_validation');

		if (count($_POST) > 0) {
            $this->mod_config->set_font($alphabets, $font_setting, $avail_fonts, $_POST);
			redirect("/");
        }

        // VIEW:
        $this->load->view('view_top1', array('title' => $this->lang->line('font_settings'),
                                             'js_list' => array('js/fontdetect.js','js/fontselector.js')));
        $this->load->view('view_top2');
        $this->load->view('view_menu_bar', array('langselect' => true));
        $center_text = $this->load->view('view_font_settings',array('l10n_js_json' => $this->mod_localize->get_json(),
                                                                    'alphabets' => $alphabets,
                                                                    'font_setting' => $font_setting,
                                                                    'avail_fonts' => $avail_fonts,
                                                                    'choice_values' => $choice_values,
                                                                    'personal_font' => $personal_fonts), true);
        $this->load->view('view_main_page', array('left_title' => $this->lang->line('settings'),
                                                  'left' => $this->lang->line('configure_font'),
                                                  'center' => $center_text));
        $this->load->view('view_bottom');
    }

  }