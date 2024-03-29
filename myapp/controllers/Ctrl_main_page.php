<?php
class Ctrl_main_page extends MY_Controller {
    public function __construct() {
        parent::__construct();
    }

	public function index() {
        $this->lang->load('login', $this->language);
        
        if ($this->mod_users->is_logged_in_noaccept()) {
            if (isset($_SESSION['new_oauth2'])) {
                $authority = $_SESSION['new_oauth2'];

                $this->load->helper('form');
                $this->load->helper('myurl');
                $this->lang->load('privacy', $this->language);

                $acceptance_code = $this->mod_users->generate_acceptance_code();

                // VIEW:
                $this->load->view('view_top1', array('title' => $this->lang->line("new_{$authority}_user")));
                $this->load->view('view_top2');
                $this->load->view('view_menu_bar', array('langselect' => true));
                $center_text = $this->load->view('view_new_oauth2_user',
                                                 array('authority' => $authority,
                                                       'user_info' => $this->mod_users->get_me(),
                                                       'acceptance_code' => $acceptance_code),
                                                 true);
                $this->lang->load('intro_text', $this->language); // For 'welcome' below
                $this->load->view('view_main_page',array('left_title' => $this->lang->line('welcome'),
                                                         'left' => $this->lang->line('first_you_must_accept_policy'),
                                                         'center' => $center_text));
                $this->load->view('view_bottom');
            }
            else {
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
        }
        else {
            $this->load->model('mod_intro_text');
            $this->lang->load('text', $this->language);
            $data['center'] = $this->mod_intro_text->center_text();
            $data['landingpage'] = true;
            $data['logos'] = true;
            if ($this->mod_users->is_logged_in())
                $data['logged_in_name'] = $this->mod_users->my_name();
        
            $this->load->view('view_top1', array('title'=>'Bible Online Learner'));
            $this->load->view('view_top2');
            $this->load->view('view_menu_bar', array('langselect' => true));
            $this->load->view('view_main_page',$data);
            $this->load->view('view_bottom');
        }
	}
}
