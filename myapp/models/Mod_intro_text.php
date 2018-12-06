<?php

class Mod_intro_text extends CI_Model {
    public function __construct() {
        parent::__construct();
        $this->lang->load('intro_text', $this->language);
    }

    function center_text() {
        $this->load->model('mod_sitetext');
        return $this->mod_sitetext->get_sitetext('intro_center', $this->language_short, $this->config->item('url_variant'));
    }

    function credits_title() {
        return $this->lang->line('intro_right_head');
    }

    function select_site_title() {
        return $this->lang->line('select_site_head');
    }

    function select_site_text() {
        $sites = array('dbi' => 'Dansk Bibel-Institut',
                       'nn' => 'No Name');
        $res = '';
        foreach ($this->config->item('all_sites') as $abb) {
            $name = isset($sites[$abb]) ? $sites[$abb] : $abb;
            $res .= '<p>'
                . anchor(sprintf('%s://%s.%s/',$this->config->item('sites_protocol'), $abb, $this->config->item('base_site')), $name)
                . '</p>';
        }
        $res .= '<p>'
            . anchor(sprintf('%s://%s/',$this->config->item('sites_protocol'), $this->config->item('base_site')), $this->lang->line('base_site'))
                . '</p>';
        return $res;
    }

    
    function credits_text() {
        return
            $this->lang->line('intro_right1') .
            '<img style="float:right;" src="images/eplotlogo.png" alt="Logo">' .
            $this->lang->line('intro_right2') .
            '<img style="float:right;" alt="SHEBANQ Logo" src="images/shebanq_logo55.png">' .
            $this->lang->line('intro_right3') .
            $this->lang->line('intro_right4');
    }

    function left_text_title() {
        $name = $this->mod_users->my_name();

        return empty($name) ? $this->lang->line('welcome')
                            : sprintf($this->lang->line('welcome2'), $name);
    }

    function left_text() {
        return '<p class="centeralign"><img alt="" src="images/BibleOL.png"></p>'
            . '<p class="centeralign">' . anchor(site_url('credits'),$this->lang->line('credits')) . '</p>';
    }
}
