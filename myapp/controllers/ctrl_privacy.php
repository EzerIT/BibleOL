<?php
class Ctrl_privacy extends MY_Controller {
    public function __construct() {
        parent::__construct();
        $this->lang->load('privacy', $this->language);
    }

    public function index() {
        // VIEW:
        $this->load->view('view_top1', array('title' => $this->lang->line('privacy_policy_title')));
        $this->load->view('view_top2');
        $this->load->view('view_menu_bar', array('langselect' => true));

        $this->load->view('view_main_page', array('left' => '<h1>'.$this->lang->line('privacy_policy_title').'</h1>',
                                                  'center' => $this->lang->line('privacy_text')));
        $this->load->view('view_bottom');
    }
  }