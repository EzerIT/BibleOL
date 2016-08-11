<?php
class Ctrl_main_page extends MY_Controller {
    public function __construct() {
        parent::__construct();
    }

	public function index() {
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
