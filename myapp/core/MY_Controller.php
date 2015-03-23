<?php

class MY_Controller extends CI_Controller {
    public function __construct() {
        parent::__construct();
        if (!isset($_SERVER['REMOTE_ADDR']))
            $_SERVER['REMOTE_ADDR'] = '0.0.0.0'; // Used by session management

        $this->load->library('session');
        $this->load->model('mod_users');
        $this->load->helper('url');
        $this->load->database();
    }

    protected function error_view(string $msg, string $title) {
        // VIEW:
        $this->load->view('view_top1', array('title' => $title));
        $this->load->view('view_top2');
        $this->load->view('view_menu_bar');
        $this->load->view('view_error',array('text' => $msg));
        $this->load->view('view_bottom');
    }
  }