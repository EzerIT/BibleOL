<?php
class Ctrl_login extends MY_Controller {
    public function __construct() {
        parent::__construct();
    }

    public function password_check($password) {
        $login_name = $this->input->post('login_name');

        $user_info = $this->mod_users->verify_login($login_name, $password);

        if (is_null($user_info)) {
            $this->mod_users->set_login_session(0,false); // Paranoia
			$this->form_validation->set_message('password_check', $this->lang->line('bad_password'));
			return false;
        }
        else {
            $this->mod_users->set_login_session($user_info->id, $user_info->isadmin);
            return true;
        }
    }

    public function index() {
        if ($this->mod_users->is_logged_in()) {
            // Log out
            $this->mod_users->set_login_session(0, false);
            redirect("/");
        }

        $this->lang->load('login', $this->language);

        $this->load->helper('form');
		$this->load->library('form_validation');

        $this->form_validation->set_rules('login_name', $this->lang->line('user_name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('password', $this->lang->line('password'), 'trim|required|callback_password_check');

		if ($this->form_validation->run())
            redirect("/");


        // Set up parameters for Google authentication.
        // For details about the protocol, see https://developers.google.com/accounts/docs/OAuth2WebServer.
        $this->session->unset_userdata(array('ol_user'=>'', 'ol_admin'=>'', 'files'=>'', 'operation'=>'', 'from_dir'=>''));

        $this->session->set_userdata('state', md5(rand())); // Used to prevent forged Google requests
        $google_request = array('response_type' => 'code',
                                'client_id' => $this->config->item('google_client_id'),
                                'redirect_uri' => site_url('/google/callback'),
                                'scope' => 'https://www.googleapis.com/auth/userinfo.profile '
                                         . 'https://www.googleapis.com/auth/userinfo.email',
                                'state' => $this->session->userdata('state'));

        // VIEW:
        $this->load->view('view_top1', array('title' => $this->lang->line('login'),
                                             'css_list' => array('zocial/css/zocial.css')));
        $this->load->view('view_top2');
        $this->load->view('view_menu_bar', array('langselect' => true));
        $this->load->view('view_login', array('google_login_enabled' => $this->config->item('google_login_enabled'),
                                              'google_request' => http_build_query($google_request)));
        $this->load->view('view_bottom');
    }
  }