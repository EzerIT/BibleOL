<?php
// This class handles the callback phase of the OAuth2 protocol for Google and Facebook authorization.
// For details about the protocol, see https://developers.google.com/accounts/docs/OAuth2WebServer
// and https://developers.facebook.com/docs/facebook-login/manually-build-a-login-flow.

class Ctrl_oauth2 extends MY_Controller {
    public $loc; // Localization

    public function __construct() {
        parent::__construct();
        $this->lang->load('login', $this->language);
        $this->config->load('ol');
    }

	public function google_callback() {
        $this->common_callback('google');
    }

	public function facebook_callback() {
        $this->common_callback('facebook');
    }

    // When an OAuth service has tried to authenticate a user, control is transferred to this function. If
    // all is well, $_GET['code'] contains an authorization code that can be converted to an access
    // code that can be used to retrieve user information.
    //
    // The parameter $authority is 'google' or 'facebook'
    private function common_callback(string $authority) {
        try {
            if (isset($_GET['error'])) {
                if ($_GET['error']==='access_denied')
                    throw new DataException($this->lang->line("access_denied_from_$authority"));
                else
                    throw new DataException($_GET['error']);
            }
 
            // Check that it was indeed us who sent the authorization request
            if (!isset($_GET['state']) || $_GET['state']!==$this->session->userdata('oauth2_state'))
                throw new DataException($this->lang->line('bad_state_information'));
 
            if (!isset($_GET['code']))
                throw new DataException($this->lang->line("wrong_answer_from_$authority"));

            // Ask authority to convert the authorization code into an access token
            switch ($authority) {
              case 'google':
                    $data = array('code' => $_GET['code'],
                                  'client_id' => $this->config->item('google_client_id'),
                                  'client_secret' => $this->config->item('google_client_secret'),
                                  'redirect_uri' => site_url('/oauth2/google_callback'),
                                  'grant_type' => 'authorization_code');
                    $url = 'https://accounts.google.com/o/oauth2/token';
 
                    $options = array(
                        'http' => array(
                            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                            'method'  => 'POST',
                            'content' => http_build_query($data),
                            ),
                        );
                    break;

              case 'facebook':
                    $data = array('code' => $_GET['code'],
                                  'client_id' => $this->config->item('facebook_client_id'),
                                  'client_secret' => $this->config->item('facebook_client_secret'),
                                  'redirect_uri' => site_url('/oauth2/facebook_callback'));
                    $url = 'https://graph.facebook.com/v2.4/oauth/access_token?' . http_build_query($data);
 
                    $options = array(
                        'http' => array(
                            'method'  => 'GET'
                            )
                        );
                    break;
            }

            $context  = stream_context_create($options);
            $result = file_get_contents($url, false, $context);
 
            if (empty($result))
                throw new DataException($this->lang->line("{$authority}_no_valid_reply"));
 
            $access_info = json_decode($result);
            $this->session->set_userdata('access_token', $access_info->access_token);
 
            // Fetch user information from authority
            switch ($authority) {
              case 'google':
                    $data = array('access_token' => $access_info->access_token);
                    $url = 'https://www.googleapis.com/oauth2/v1/userinfo?' . http_build_query($data);
                    break;
                    
              case 'facebook':
                    $data = array('access_token' => $access_info->access_token,
                                  'appsecret_proof' => hash_hmac('sha256', 
                                                                 $access_info->access_token, 
                                                                 $this->config->item('facebook_client_secret')),
                                  'fields' => 'id,first_name,last_name,email');
                    $url = 'https://graph.facebook.com/v2.4/me?' . http_build_query($data);
                    break;
            }

            $options = array(
                'http' => array(
                    'method'  => 'GET'
                    )
                );
            $context  = stream_context_create($options);
            $result = file_get_contents($url, false, $context);
 
            if (empty($result))
                throw new DataException($this->lang->line("{$authority}_no_valid_reply"));
 
            $user_info = json_decode($result);

            // Generate uniform member names
            if ($authority==="google") {
                $user_info->first_name = $user_info->given_name;
                $user_info->last_name = $user_info->family_name;
            }

            if (!isset($user_info->email))
                $user_info->email = null;


            // MODEL:
            if ($this->mod_users->new_oauth2_user($authority,
                                                  $user_info->id,
                                                  $user_info->first_name,
                                                  $user_info->last_name,
                                                  $user_info->email)) {

                // VIEW:
                $this->load->view('view_top1', array('title' => $this->lang->line("new_{$authority}_user")));
                $this->load->view('view_top2');
                $this->load->view('view_menu_bar', array('langselect' => false));
                $center_text = $this->load->view('view_new_oauth2_user',
                                                 array('authority' => $authority,
                                                       'user_info' => $user_info),
                                                 true);
                $this->lang->load('intro_text', $this->language); // For 'welcome' below
                $this->load->view('view_main_page',array('left_title' => $this->lang->line('welcome'),
                                                         'center' => $center_text));
                $this->load->view('view_bottom');
            }
            else
                header("Location: " . site_url());
        }
        catch (DataException $e) {
            // VIEW:
            $this->load->view('view_top1', array('title' => $this->lang->line('users')));
            $this->load->view('view_top2');
            $this->load->view('view_menu_bar', array('langselect' => true));
            $this->load->view('view_error',array('text' => $e->getMessage()));
            $this->load->view('view_bottom');
        }
    }
  }