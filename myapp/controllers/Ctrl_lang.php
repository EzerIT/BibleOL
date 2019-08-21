<?php
class Ctrl_lang extends MY_Controller {
    public function __construct() {
        parent::__construct();
    }

    public function index() {
        if (isset($_GET['lang'])) {
            $newlang = $_GET['lang'];

            $this->load->helper('translation');
            
            $if_trans = get_if_translations();

            $found = false;
            foreach ($if_trans as $ift) {
                if ($ift->abb==$newlang) {
                    $this->session->set_userdata('language', $newlang);
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $this->error_view('Unknown language', 'Set language');
                return;
            }
        }

        redirect($_SERVER['HTTP_REFERER']);
    }

    
    public function variant() {
        $this->lang->load('translate', $this->language);

        if (isset($_GET['variant'])) {
            $newvariant = $_GET['variant'];

            if (in_array($newvariant, $this->config->item('variants')))
                $_SESSION['variant'] = $newvariant;
            elseif ($newvariant=='main')
                $_SESSION['variant'] = null;
            else {
                $this->error_view($this->lang->line('unknown_variant'), $this->lang->line('set_variant'));  // TODO: Localize
                return;
            }
        }
        redirect($_SERVER['HTTP_REFERER']);
    }
  }