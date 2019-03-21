<?php
class Ctrl_login extends MY_Controller {
    public function __construct() {
        parent::__construct();
    }

    public function password_check($password) {
        $login_name = $this->input->post('login_name');

        if ($this->mod_users->verify_login($login_name, $password))
            return true;  // Password OK

        // Wrong password
        $this->mod_users->clear_login_session(); // Paranoia
        $this->form_validation->set_message('password_check', $this->lang->line('bad_password'));
        return false;
    }

    public function accept_policy_yes() {
        if (isset($_POST['acceptance_code']) &&
            isset($_POST['policy_lang']) &&
            $this->mod_users->verify_accept_code($_POST['acceptance_code'],
                                                 $_POST['policy_lang'],
                                                 true)) {

            // Successful login
            $this->mod_users->update_login_stat();
        }
        else
            $this->mod_users->clear_login_session();

        unset($_SESSION['new_oauth2']);
        
        redirect("/");
    } 

    public function accept_policy_no() {
        $this->mod_users->clear_login_session();
        
        $this->lang->load('privacy', $this->language);

        // VIEW:
        $this->load->view('view_top1', array('title' => $this->lang->line('policy')));
        $this->load->view('view_top2');
        $this->load->view('view_menu_bar', array('langselect' => true));
        $this->load->view('view_rejected_policy', array('reject_text' => $this->lang->line('you_rejected_text')));
        $this->load->view('view_bottom');
    }
    
    public function index() {
        $this->load->helper('security'); // Provides xss_clean. TODO: This testing should be done on output instead of input
                                         // TODO: Should xss_clean be replaced by strip_tags or vice versa?
        if ($this->mod_users->is_logged_in()) {
            // Log out
            $this->mod_users->clear_login_session();
            redirect("/");
        }

        $this->lang->load('login', $this->language);

        $this->load->helper('form');
		$this->load->library('form_validation');

        $this->form_validation->set_rules('login_name', $this->lang->line('user_name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('password', $this->lang->line('password'), 'trim|required|callback_password_check');

		if ($this->form_validation->run()) {
            if ($this->mod_users->accepted_current_policy()) {
                // Successful login
                $this->mod_users->update_login_stat();
                $this->mod_users->set_login_session();
            }
            else {
                // User needs to accept new policy, so don't log anything yet
                $this->mod_users->set_login_session();
            }
            
            redirect("/");
        }

        $this->session->unset_userdata(array('ol_user', 'files', 'operation', 'from_dir'));

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
                                             // Here, the Roboto font should be loaded, but apparently this has
                                             // already happened through some unknown magic.
                                             'css_list' => array( //'https://fonts.googleapis.com/css?family=Roboto',
                                                                 'zocial/css/zocial.css')));
        $this->load->view('view_top2');
        $this->load->view('view_menu_bar', array('langselect' => true));
        $this->load->view('view_login', array('google_login_enabled' => $this->config->item('google_login_enabled'),
                                              'google_request' => http_build_query($google_request),
                                              'facebook_login_enabled' => $this->config->item('facebook_login_enabled'),
                                              'facebook_request' => http_build_query($facebook_request)));
        $this->load->view('view_bottom');
    }
  }