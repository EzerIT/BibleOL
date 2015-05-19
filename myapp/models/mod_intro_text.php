<?php

class Mod_intro_text extends CI_Model {
    public function __construct() {
        parent::__construct();
        $this->lang->load('intro_text', $this->language);
    }

    function center_text() {
        return $this->lang->line('intro_center');
    }

    function right_text() {
        return
            '<h1>' . $this->lang->line('intro_right_head') . '</h1>' .
            $this->lang->line('intro_right1') .
            '<img style="float:right;" src="images/eplotlogo.png" alt="Logo">' .
            $this->lang->line('intro_right2') .
            '<img style="float:right;" alt="SHEBANQ Logo" src="images/shebanq_logo55.png">' .
            $this->lang->line('intro_right3');
    }

    function left_text() {
        $name = $this->mod_users->my_name();

        return
            '<h1>' . (is_null($name)
                      ? $this->lang->line('welcome')
                      : sprintf($this->lang->line('welcome2'), $name)) . '</h2>'
            . '<p>&nbsp;</p><p class="centeralign"><img alt="" src="images/BibleOL.png"></p>';
    }
}
