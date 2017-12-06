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


        // Detect langauge of privacy policy
        if (preg_match('/^\(([^)]*)\)(.*)/s', $this->lang->line('privacy_text'), $matches))
            $txt = $matches[2];
        else
            $txt = $this->lang->line('privacy_text');

        
        $this->load->view('view_main_page', array('left_title' => $this->lang->line('privacy_policy_title'),
                                                  'center' => $txt));
        $this->load->view('view_bottom');
    }
  }