<?php

class Mod_intro_text extends CI_Model {
    public function __construct() {
        parent::__construct();
        $this->lang->load('intro_text', $this->language);
    }

    function center_text() {
        return $this->lang->line('intro_center');
    }

    function right_text_title() {
        return $this->lang->line('intro_right_head');
    }

    function right_text() {
        return
            $this->lang->line('intro_right1') .
            '<img style="float:right;" src="images/eplotlogo.png" alt="Logo">' .
            $this->lang->line('intro_right2') .
            '<img style="float:right;" alt="SHEBANQ Logo" src="images/shebanq_logo55.png">' .
            $this->lang->line('intro_right3');
    }

    function left_text_title() {
        $name = $this->mod_users->my_name();

        return empty($name) ? $this->lang->line('welcome')
                            : sprintf($this->lang->line('welcome2'), $name);
    }

    function left_text() {
        return '<p class="centeralign"><img alt="" src="images/BibleOL.png"></p>';
    }
}
