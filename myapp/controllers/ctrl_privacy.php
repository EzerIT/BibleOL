<?php
class Ctrl_privacy extends MY_Controller {
    public function __construct() {
        parent::__construct();
    }

    public function index() {
        // VIEW:
        $this->load->view('view_top1', array('title' => 'Privacy Policy'));
        $this->load->view('view_top2');
        $this->load->view('view_menu_bar');

        $center_text = $this->load->view('view_privacy', null, true);

        $this->load->view('view_main_page', array('left' => '<h1>Privacy Policy</h1>',
                                                  'center' => $center_text));
        $this->load->view('view_bottom');
    }
  }