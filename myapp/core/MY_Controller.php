<?php

// For debugging
function preprint($x,$y=null) {
    echo "<pre>",print_r($x,true),"</pre>";
    if ($y)
        echo "<pre>",print_r($y,true),"</pre>";
  }
function predump($x,$y=null) {
    echo "<pre>";
    var_dump($x);
    if ($y)
        var_dump($y);
    echo "</pre>";
  }
    
class MY_Controller extends CI_Controller {
    public $language;
    public $if_translations;

    public function __construct() {
        parent::__construct();
        if (!isset($_SERVER['REMOTE_ADDR']))
            $_SERVER['REMOTE_ADDR'] = '0.0.0.0'; // Used by session management

        $this->load->library('session');
        $this->load->model('mod_users');
        $this->load->helper(array('url','myurl','translation'));
        $this->load->database();

        $this->if_translations = get_if_translations();
        $if_trans_by_code = make_code_index($this->if_translations);
        
        $this->language = $this->session->userdata('language');

        if (!isset($if_trans_by_code[$this->language])) { // No available language selected on website
            $this->load->library('user_agent');
            $langlist = $this->agent->languages(); // Get language list from browser

            $found = false;
            foreach ($langlist as $l) {
                if (isset($if_trans_by_code[$l])) { // Does Bible OL know this language?
                    $this->language = $l;
                    $found = true;
                    break;
                }
            }
            if (!$found)
                $this->language = 'en'; // English is the default language
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