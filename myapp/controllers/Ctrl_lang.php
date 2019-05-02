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
              case 'fr':
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

    
    public function variant() {
        if (isset($_GET['variant'])) {
            $newvariant = $_GET['variant'];

            if (in_array($newvariant, $this->config->item('variants')))
                $_SESSION['variant'] = $newvariant;
            elseif ($newvariant=='none')
                $_SESSION['variant'] = null;
            else {
                $this->error_view($this->lang->line('unknown_variant'), $this->lang->line('set_variant'));  // TODO: Localize
                return;
            }
        }
        redirect($_SERVER['HTTP_REFERER']);
    }
  }