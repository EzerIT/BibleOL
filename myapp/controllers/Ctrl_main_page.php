<?php
class Ctrl_main_page extends MY_Controller {
    public function __construct() {
        parent::__construct();
    }

	public function index() {
        if ($this->mod_users->is_logged_in_noaccept()) {
            // User needs to accept new policy

            $this->lang->load('privacy', $this->language);

            $acceptance_code = $this->mod_users->generate_acceptance_code();

            $this->load->helper('form');

            // VIEW:
            $this->load->view('view_top1', array('title' => $this->lang->line('policy')));
            $this->load->view('view_top2');
            $this->load->view('view_menu_bar', array('langselect' => true));
            $center_text = $this->load->view('view_accept_policy', array('acceptance_code' => $acceptance_code,
                                                                         'user_id' => $this->mod_users->my_id()), true);

            $this->load->view('view_main_page', array('left_title' => $this->lang->line('new_privacy_header'),
                                                      'left' => $this->lang->line('new_privacy_intro'),
                                                      'center' => $center_text));
        
            $this->load->view('view_bottom');
        }
        else {
            $this->load->model('mod_intro_text');
            $data['left_title'] = $this->mod_intro_text->left_text_title();
            $data['left'] = $this->mod_intro_text->left_text();
            $data['center'] = $this->mod_intro_text->center_text();
            $data['right_title'] = $this->mod_intro_text->right_text_title();
            $data['right'] = $this->mod_intro_text->right_text();
            $data['logos'] = true;
        
            $this->load->view('view_top1', array('title'=>'Bible Online Learner'));
            $this->load->view('view_top2');
            $this->load->view('view_menu_bar', array('langselect' => true));
            $this->load->view('view_main_page',$data);
            $this->load->view('view_bottom');
        }
	}
}
