<?php

// For debugging
function preprint($x) {
    echo "<pre>",print_r($x,true),"</pre>";
  }
function predump($x) {
    echo "<pre>";
    var_dump($x);
    echo "</pre>";
  }
    
class MY_Controller extends CI_Controller {
    public $language;
    public $language_short;
    public $language_short2;   // Used by lang attribute in <html lang="..."> element

    public function __construct() {
        parent::__construct();
        if (!isset($_SERVER['REMOTE_ADDR']))
            $_SERVER['REMOTE_ADDR'] = '0.0.0.0'; // Used by session management

        $this->load->library('session');
        $this->load->model('mod_users');
        $this->load->helper('url');
        $this->load->helper('myurl');
        $this->load->database();

        $this->language = $this->session->userdata('language');

        if ($this->language === false) { // No language selected on website
            $this->load->library('user_agent');
            $langlist = $this->agent->languages(); // Get language list from browser
            if (isset($langlist[0]))
                $this->language = $langlist[0];
        }

        switch ($this->language) {
          case 'zh':
          case 'zh-cn':
          case 'zh-sg':
          case 'zh-simp':
          case 'zh-Hans':
                $this->language_short = 'zh-simp';
                $this->language_short2 = 'zh-Hans';
                $this->language = 'zh-simp';
                break;

          case 'zh-tw':
          case 'zh-hk':
          case 'zh-trad':
          case 'zh-Hant':
                $this->language_short = 'zh-trad';
                $this->language_short2 = 'zh-Hant';
                $this->language = 'zh-trad';
                break;

          case 'da':
          case 'de':
          case 'nl':
          case 'pt':
          case 'es':
          case 'fr':
          case 'zh-simp':
          case 'zh-trad':
          case 'am':
                $this->language_short = $this->language;
                $this->language_short2 = $this->language;
                break;

          default:
                $this->language_short = 'en';
                $this->language_short2 = 'en';
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