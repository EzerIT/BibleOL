<?php
class Ctrl_credits extends MY_Controller {
    public function index() {
        $this->load->model('mod_intro_text');

        // VIEW:
        $this->load->view('view_top1', array('title' => $this->lang->line('credits_title')));
        $this->load->view('view_top2');
        $this->load->view('view_menu_bar', array('langselect' => true));


        $this->load->view('view_main_page', array('left_title' => $this->mod_intro_text->credits_title(),
                                                  'center' => $this->mod_intro_text->credits_text()));


        
        $this->load->view('view_bottom');
    }


  }