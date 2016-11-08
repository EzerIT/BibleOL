<?php

class MY_Controller extends CI_Controller {
    public $language;
    public $language_short;

    public function __construct() {
        parent::__construct();
        if (!isset($_SERVER['REMOTE_ADDR']))
            $_SERVER['REMOTE_ADDR'] = '0.0.0.0'; // Used by session management

        $this->load->library('session');
        $this->load->model('mod_users');
        $this->load->helper('url');
        $this->load->database();

        $this->language = $this->session->userdata('language');

        if ($this->language === false) { // No language selected on website
            $this->load->library('user_agent');
            $langlist = $this->agent->languages(); // Get language list from browser
            if (isset($langlist[0]))
                $this->language = $langlist[0];
        }
            
        switch ($this->language) {
          case 'da':
          case 'pt':
          case 'es':
          case 'zh':
          case 'zh-trad':
                $this->language_short = $this->language;
                break;

          default:
                $this->language_short = 'en';
                $this->language = 'english';  // English language files are not named 'en', but 'english'
                break;
        }

        $this->lang->load('common', $this->language);
    }

    protected function error_view(string $msg, string $title) {
        // VIEW:
        $this->load->view('view_top1', array('title' => $title));
        $this->load->view('view_top2');
        $this->load->view('view_menu_bar', array('langselect' => true));
        $this->load->view('view_error',array('text' => $msg));
        $this->load->view('view_bottom');
    }
  }