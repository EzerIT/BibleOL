<?php
class Ctrl_google extends MY_Controller {
    public $loc; // Localization

    public function __construct() {
        parent::__construct();
        $this->config->load('ol');
    }


    /// When Google has tried to authenticate a user, control is transferred to this function. If
    /// all is well, $_GET['code'] contains an authorization code that can be converted to an access
    /// code that can be used to retrieve user information.
    /// For details about the protocol, see https://developers.google.com/accounts/docs/OAuth2WebServer.
	public function callback() {
        try {
            if (isset($_GET['error'])) {
                if ($_GET['error']==='access_denied')
                    throw new DataException('Access denied from Google');
                else
                    throw new DataException($_GET['error']);
            }
 
            // Check that it was indeed us who sent the authorization request
            if (!isset($_GET['state']) || $_GET['state']!==$this->session->userdata('state'))
                throw new DataException("Bad state information");
 
            if (!isset($_GET['code']))
                throw new DataException("Wrong answer from Google");

            // Ask Google to convert the authorization code into an access token
            $url = 'https://accounts.google.com/o/oauth2/token';
            $data = array(
                'code' => $_GET['code'],
                'client_id' => $this->config->item('google_client_id'),
                'client_secret' => $this->config->item('google_client_secret'),
                'redirect_uri' => site_url('/google/callback'),
                'grant_type' => 'authorization_code');
 
            $options = array(
                'http' => array(
                    'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                    'method'  => 'POST',
                    'content' => http_build_query($data),
                    ),
                );

            $context  = stream_context_create($options);
            $result = file_get_contents($url, false, $context);
 
            if (empty($result))
                throw new DataException("No valid reply from Google");
 
            $google_access_info = json_decode($result);
            $this->session->set_userdata('access_token', $google_access_info->access_token);
 
            // Fetch user information from Google
            $url = "https://www.googleapis.com/oauth2/v1/userinfo?access_token=$google_access_info->access_token";
            $options = array(
                'http' => array(
                    'method'  => 'GET'
                    )
                );
            $context  = stream_context_create($options);
            $result = file_get_contents($url, false, $context);
 
            if (empty($result))
                throw new DataException("No valid reply from Google");
 
            $google_user_info = json_decode($result);


            // MODEL:
            if ($this->mod_users->new_google_user($google_user_info->id,
                                                  $google_user_info->given_name,
                                                  $google_user_info->family_name,
                                                  $google_user_info->email)) {

                // VIEW:
                $this->load->view('view_top1', array('title'=>'New Google User'));
                $this->load->view('view_top2');
                $this->load->view('view_menu_bar');
                $center_text = $this->load->view('view_new_google_user',
                                                 array('google_user_info' => $google_user_info),
                                                 true);
                $this->load->view('view_main_page',array('left' => '<h1>Welcome to Bible Online Learner</h1>',
                                                         'center' => $center_text));
                $this->load->view('view_bottom');
            }
            else
                header("Location: " . site_url());
        }
        catch (DataException $e) {
            // VIEW:
            $this->load->view('view_top1', array('title' => 'Users'));
            $this->load->view('view_top2');
            $this->load->view('view_menu_bar');
            $this->load->view('view_error',array('text' => $e->getMessage()));
            $this->load->view('view_bottom');
        }
    }
  }