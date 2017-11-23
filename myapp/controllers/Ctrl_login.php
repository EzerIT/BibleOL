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
        $this->mod_users->set_login_session(0,false,false,false); // Paranoia
        $this->form_validation->set_message('password_check', $this->lang->line('bad_password'));
        return false;
    }

    // This is almost indentical to a function in Ctrl_oauth2.cpp
    private function accept_policy(integer $user_id) {
        $acceptance_code = $this->mod_users->generate_acceptance_code($user_id);

        // VIEW:
        $this->load->view('view_top1', array('title' => $this->lang->line('policy')));
        $this->load->view('view_top2');
        $this->load->view('view_menu_bar', array('langselect' => true));
        $center_text = $this->load->view('view_accept_policy', array('acceptance_code' => $acceptance_code,
                                                                     'user_id' => $user_id), true);

        $this->load->view('view_main_page', array('center' => $center_text));
        
        $this->load->view('view_bottom');
    }

    public function accept_policy_yes() {
        if (isset($_POST['acceptance_code']) &&
            isset($_POST['user_id']) &&
            $this->mod_users->verify_accept_code($_POST['acceptance_code'], $_POST['user_id'])) {

            echo "YES - ",$_POST['acceptance_code']," - ", $_POST['user_id'],"\n";die;

            
            // Successful login
            $me = $this->mod_users->get_me();
            $uid = intval($_POST['user_id']);
            $this->mod_users->update_login_stat($uid);
            $this->mod_users->set_login_session($uid, $me->isadmin, $me->isteacher, $me->istranslator, $me->preflang);
            redirect("/");
        }
        else {
            echo "NO";die;
            $this->mod_users->set_login_session(0, false, false, false);
            redirect("/");
        }
    } 

    public function accept_policy_no() {
        // VIEW:
        $this->load->view('view_top1', array('title' => $this->lang->line('policy')));
        $this->load->view('view_top2');
        $this->load->view('view_menu_bar', array('langselect' => true));
        $this->load->view('view_rejected_policy');
        $this->load->view('view_bottom');
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

		if ($this->form_validation->run()) {
            if ($this->mod_users->accept_policy_current()) {
                // Successful login
                $me = $this->mod_users->get_me();
                $uid = intval($me->id);
                $this->mod_users->update_login_stat($uid);
                $this->mod_users->set_login_session($uid, $me->isadmin, $me->isteacher, $me->istranslator, $me->preflang);
                redirect("/");
            }
            else {
                // User needs to accept new policy
                $this->accept_policy(intval($this->mod_users->my_id()));
                return;
            }
        }

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