<?php
class Ctrl_login extends MY_Controller {
    public function __construct() {
        parent::__construct();
    }

    public function password_check($password) {
        $login_name = $this->input->post('login_name');

        $user_info = $this->mod_users->verify_login($login_name, $password);

        if (is_null($user_info)) {
            $this->mod_users->set_login_session(0,false,false,false); // Paranoia
			$this->form_validation->set_message('password_check', $this->lang->line('bad_password'));
			return false;
        }
        else {
            $this->mod_users->set_login_session(intval($user_info->id), $user_info->isadmin, $user_info->isteacher, $user_info->istranslator, $user_info->preflang);
            return true;
        }
    }

    public function index() {
        $this->load->helper('security'); // Provides xss_clean. TODO: This testing should be done on output instead of input
                                         // TODO: Should xss_clean be replaced by strip_tags or vice versa?
        if ($this->mod_users->is_logged_in()) {
            // Log out
            $this->mod_users->set_login_session(0, false, false, false);
            redirect("/");
        }

        $this->lang->load('login', $this->language);

        $this->load->helper('form');
		$this->load->library('form_validation');

        $this->form_validation->set_rules('login_name', $this->lang->line('user_name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('password', $this->lang->line('password'), 'trim|required|callback_password_check');

		if ($this->form_validation->run())
            redirect("/");

        $this->session->unset_userdata(array('ol_user', 'ol_admin', 'ol_teacher', 'ol_translator', 'files', 'operation', 'from_dir'));

        // Set up parameters for OAuth2 authentication.
        $this->session->set_userdata('oauth2_state', md5(rand())); // Used to prevent forged requests

        // For details about the Google protocol,
        // see https://developers.google.com/accounts/docs/OAuth2WebServer.

        $google_request = array('response_type' => 'code',
                                'client_id' => $this->config->item('google_client_id'),
                                'redirect_uri' => site_url('/oauth2/google_callback'),
                                'scope' => 'https://www.googleapis.com/auth/userinfo.profile '
                                         . 'https://www.googleapis.com/auth/userinfo.email',
                                'state' => $this->session->userdata('oauth2_state'));

        // For details about the Facebook protocol,
        // see https://developers.facebook.com/docs/facebook-login/manually-build-a-login-flow.

        $facebook_request = array('response_type' => 'code',
                                  'client_id' => $this->config->item('facebook_client_id'),
                                  'redirect_uri' => site_url('/oauth2/facebook_callback'),
                                  'scope' => 'email',
                                  'state' => $this->session->userdata('oauth2_state'));

        // VIEW:
        $this->load->view('view_top1', array('title' => $this->lang->line('login'),
                                             'css_list' => array('zocial/css/zocial.css')));
        $this->load->view('view_top2');
        $this->load->view('view_menu_bar', array('langselect' => true));
        $this->load->view('view_login', array('google_login_enabled' => $this->config->item('google_login_enabled'),
                                              'google_request' => http_build_query($google_request),
                                              'facebook_login_enabled' => $this->config->item('facebook_login_enabled'),
                                              'facebook_request' => http_build_query($facebook_request)));
        $this->load->view('view_bottom');
    }
  }