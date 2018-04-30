<?php
class Ctrl_lang extends MY_Controller {
    public function __construct() {
        parent::__construct();
    }

    public function index() {
        if (isset($_GET['lang'])) {
            $newlang = $_GET['lang'];
            switch ($newlang) {
              case 'en':
              case 'de':
              case 'es':
              case 'nl':
              case 'pt':
              case 'da':
              case 'zh-simp':
              case 'zh-trad':
                    $this->session->set_userdata('language', $newlang);
                    break;

              default:
                    $this->error_view('Unknown language', 'Set language');
                    return;
            }
        }

        redirect($_SERVER['HTTP_REFERER']);
    }
  }