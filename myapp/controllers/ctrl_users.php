<?php
function username_cmp($u1, $u2) {
    if ($u1->username === $u2->username)
        return 0;
    return ($u1->username < $u2->username) ? -1 : 1;
}

class Ctrl_users extends MY_Controller {
    const MIN_PW_LENGTH = 5;

    public function __construct() {
        parent::__construct();
        $this->lang->load('users', $this->language);
    }

	public function index() {
        $this->users();
    }

    public function users() {
        try {
            $this->mod_users->check_admin();

            $allusers = $this->mod_users->get_all_users();
            usort($allusers, 'username_cmp');

            // VIEW:
            $this->load->view('view_top1', array('title' => $this->lang->line('users')));
            $this->load->view('view_top2');
            $this->load->view('view_menu_bar', array('langselect' => true));
            $this->load->view('view_confirm_dialog');
            $center_text = $this->load->view('view_user_list',
                                             array('allusers' => $allusers,
                                                   'my_id' => $this->mod_users->my_id()),
                                             true);
            $this->load->view('view_main_page', array('left' => '<h1>' . $this->lang->line('user_list') . '</h1>'
                                                      . '<p>' . $this->lang->line('configure_your_users') . '</p>',
                                                      'center' => $center_text));
            $this->load->view('view_bottom');
        }
        catch (DataException $e) {
            $this->error_view($e->getMessage(), $this->lang->line('users'));
        }
    }

    public function delete_me() {
        try {
            $this->mod_users->check_logged_in_google(false);
            $this->mod_users->delete_user($this->mod_users->my_id());

            // Log out
            $this->mod_users->set_login_session(0, false);

            // VIEW:
            $this->load->view('view_top1', array('title' => $this->lang->line('user_profile_deleted')));
            $this->load->view('view_top2');
            $this->load->view('view_menu_bar', array('langselect' => false));

            $center_text = $this->load->view('view_user_deleted', null, true);

            $this->load->view('view_main_page', array('center' => $center_text));
            $this->load->view('view_bottom');
        }
        catch (DataException $e) {
            $this->error_view($e->getMessage(), $this->lang->line('users'));
        }
    }

    public function delete_me_google() {
        try {
            $this->mod_users->check_logged_in_google(true);
 
            // Revoke user permissions
            $url = "https://accounts.google.com/o/oauth2/revoke?token=" . $this->session->userdata('access_token');
            $options = array(
                'http' => array(
                    'method'  => 'GET'
                    )
                );
            $context  = stream_context_create($options);
            $result = @file_get_contents($url, false, $context);
            
            $items = explode(' ',$http_response_header[0]); // $items[1] is the HTTP error code
            switch ($items[1]) {
              case '400':
                    // Token not recognized by Google

                    // Log out
                    $this->mod_users->set_login_session(0, false);

                    throw new DataException($this->lang->line('google_no_response_delete'));
                    break;

              case '200':
                    // Token successfully revoked

                    $this->mod_users->delete_user($this->mod_users->my_id());

                    // Log out
                    $this->mod_users->set_login_session(0, false);

                    // VIEW:
                    $this->load->view('view_top1', array('title' => $this->lang->line('user_profile_deleted')));
                    $this->load->view('view_top2');
                    $this->load->view('view_menu_bar', array('langselect' => false));

                    $center_text = $this->load->view('view_user_deleted', null, true);
                    
                    $this->load->view('view_main_page', array('center' => $center_text));
                    $this->load->view('view_bottom');
                    break;
                    
              default:
                    // Other error
                    throw new DataException($this->lang->line('google_no_valid_reply'));
            }
        }
        catch (DataException $e) {
            $this->error_view($e->getMessage(), $this->lang->line('users'));
        }
    }

    public function delete_user() {
        try {
            $this->mod_users->check_admin();

            $userid = is_numeric($_GET['userid']) ? intval($_GET['userid']) : 0;
        
            if ($userid<=0)
                throw new DataException($this->lang->line('illegal_user_id'));

            if ($this->mod_users->is_me($userid))
                throw new DataException($this->lang->line('cannot_delete_self'));

            $this->mod_users->delete_user($userid);
            redirect('/users');
        }
        catch (DataException $e) {
            $this->error_view($e->getMessage(), $this->lang->line('users'));
        }
    }

    public function password_length_check($password1) {
        if (!is_null($password1) && strlen($password1)>0 && strlen($password1)<self::MIN_PW_LENGTH) {
			$this->form_validation->set_message('password_length_check',
                                                sprintf($this->lang->line('pw_min_length'),self::MIN_PW_LENGTH));
            return false;
        }
        else
            return true;
    }

   public function edit_one_user() {
        try {
            $this->mod_users->check_admin();

            $userid = isset($_GET['userid']) ? intval($_GET['userid']) : 0;
        
            if ($userid==0)
                throw new DataException($this->lang->line('illegal_user_id'));

            $user_info = $this->mod_users->get_user_by_id($userid);

            $this->load->helper('form');
            $this->load->library('form_validation');

            // For pedagogical reasons, the set_rules() functions must be called in the order the
            // fields appear in the form

            if ($userid<0) {
                // Extra validation when creating a new user
                $this->form_validation->set_message('is_unique', sprintf($this->lang->line('user_name_used'), $this->input->post('username')));
                $this->form_validation->set_rules('username', $this->lang->line('user_name'), 'trim|required|strip_tags|is_unique[user.username]');
            }

            if ($user_info->google_login) {
                $this->form_validation->set_rules('isadmin', '', '');

                if ($this->form_validation->run()) {
                    $user_info->isadmin = $this->input->post('isadmin')==='yes';

                    $query = $this->mod_users->set_user($user_info, null);

                    redirect('/users');
                }
                else {
                    // VIEW:
                    $this->load->view('view_top1', array('title' => $this->lang->line('edit_user')));
                    $this->load->view('view_top2');
                    $this->load->view('view_menu_bar', array('langselect' => true));

                    $center_text = $this->load->view('view_edit_user',array('userid' => $userid, 'user_info' => $user_info), true);

                    $this->load->view('view_main_page', array('left' => '<h1>' . $this->lang->line('edit_user_information') . '</h1>',
                                                              'center' => $center_text));
                    $this->load->view('view_bottom');
                }
            }
            else {
                // This is not a Google user, or this is a new account

                // Common validation rules
                $this->form_validation->set_rules('first_name', $this->lang->line('first_name'), 'trim|required|strip_tags');
                $this->form_validation->set_rules('last_name', $this->lang->line('last_name'), 'trim|required|strip_tags');
                $this->form_validation->set_rules('isadmin', '', '');
                $this->form_validation->set_rules('email', $this->lang->line('email'), 'trim|valid_email|strip_tags');

                if ($userid>0) {
                    // Extra validation when editing an existing user
                    $this->form_validation->set_rules('password1', $this->lang->line('new_password'),
                                                      'trim|matches[password2]|callback_password_length_check');
                    $this->form_validation->set_rules('password2', $this->lang->line('repeat_new_password'), 'trim');
                }
                else {
                    // Extra validation when creating a new user
                    $this->form_validation->set_rules('password1', $this->lang->line('password'),
                                                      'trim|required|min_length['.self::MIN_PW_LENGTH.']|matches[password2]');
                    $this->form_validation->set_rules('password2', $this->lang->line('repeat_password'), 'trim|required');
                }

                if ($this->form_validation->run()) {
                    $user_info->first_name = $this->input->post('first_name');
                    $user_info->last_name = $this->input->post('last_name');
                    $user_info->isadmin = $this->input->post('isadmin')==='yes';
                    $user_info->email = $this->input->post('email');

                    if ($userid<=0)
                        $user_info->username = $this->input->post('username');

                    $pw = $this->input->post('password1');
                    $query = $this->mod_users->set_user($user_info, $pw);

                    if ($userid<=0 && $user_info->email) {
                        // Inform user
                        $this->load->library('email');

                        $this->email->from($this->config->item('mail_sender_address'), $this->config->item('mail_sender_name'));
                        $this->email->to($user_info->email); 
                        $this->email->subject($this->lang->line('account_created_subject'));
                        $this->email->message(sprintf($this->lang->line('account_created_message1'),
                                                      $user_info->first_name, $user_info->last_name,
                                                      $user_info->username,
                                                      $pw)
                                              . ($user_info->isadmin ? $this->lang->line('account_created_message2') : '')
                                              . sprintf($this->lang->line('account_created_message3'), site_url('login')));
                        $this->email->send();
                    }

                    redirect('/users');
                }
                else {
                    // VIEW:
                    $this->load->view('view_top1', array('title' => $this->lang->line('edit_user')));
                    $this->load->view('view_top2');
                    $this->load->view('view_menu_bar', array('langselect' => true));

                    $center_text = $this->load->view('view_edit_user',array('userid' => $userid, 'user_info' => $user_info), true);

                    $this->load->view('view_main_page', array('left' => '<h1>' . $this->lang->line('edit_user_information') . '</h1>',
                                                              'center' => $center_text));
                    $this->load->view('view_bottom');
                }
            }
        }
        catch (DataException $e) {
            $this->error_view($e->getMessage(), $this->lang->line('users'));
        }
    }


   public function profile() {
       try {
           $this->mod_users->check_logged_in();

           $user_info = $this->mod_users->get_me();
           assert('!is_null($user_info)');

           if ($user_info->google_login) {
               // VIEW:
               $this->load->view('view_top1', array('title' => $this->lang->line('display_user')));
               $this->load->view('view_top2');
               $this->load->view('view_menu_bar', array('langselect' => true));
               $this->load->view('view_confirm_dialog');
            
               $left_text = $this->load->view('view_google_profile_left', null, true);
               $center_text = $this->load->view('view_google_profile',array('user_info' => $user_info), true);

               $this->load->view('view_main_page', array('left' => $left_text,
                                                         'center' => $center_text));
               $this->load->view('view_bottom');
           }
           else {
               // Not a Google user

               $this->load->helper('form');
               $this->load->library('form_validation');

               $this->form_validation->set_rules('first_name', $this->lang->line('first_name'), 'trim|required|strip_tags');
               $this->form_validation->set_rules('last_name', $this->lang->line('last_name'), 'trim|required|strip_tags');
               $this->form_validation->set_rules('email', $this->lang->line('email'), 'trim|valid_email|strip_tags');

               $this->form_validation->set_rules('password1', $this->lang->line('new_password'),
                                                 'trim|matches[password2]|callback_password_length_check');
               $this->form_validation->set_rules('password2', $this->lang->line('repeat_new_password'), 'trim');


               if ($this->form_validation->run()) {
                   $user_info->first_name = $this->input->post('first_name');
                   $user_info->last_name = $this->input->post('last_name');
                   $user_info->email = $this->input->post('email');

                   $pw = $this->input->post('password1');

                   $query = $this->mod_users->set_user($user_info, $pw);

                   redirect('/');
               }
               else {
                   // VIEW:
                   $this->load->view('view_top1', array('title' => $this->lang->line('edit_user')));
                   $this->load->view('view_top2');
                   $this->load->view('view_menu_bar', array('langselect' => true));
                   $this->load->view('view_confirm_dialog');

                   $left_text = $this->load->view('view_edit_profile_left', null, true);
                   $center_text = $this->load->view('view_edit_profile', array('user_info' => $user_info), true);

                   $this->load->view('view_main_page', array('left' => $left_text,
                                                             'center' => $center_text));
                   $this->load->view('view_bottom');
               }
           }
       }
       catch (DataException $e) {
           $this->error_view($e->getMessage(), $this->lang->line('users'));
       }
   }

    private $user_found;

    // Checks that either username or email has been set
    public function check_username_or_email($notused) {
        $username = $this->input->post('username');
        $email = $this->input->post('email');
        if ($username==='' && $email==='') {
			$this->form_validation->set_message('check_username_or_email',
                                                $this->lang->line('specify_name'));
            return false;
        }
        else {
            $user_info = $this->mod_users->get_user_by_name_or_email($username,$email);
            if (is_null($user_info)) {
                $this->form_validation->set_message('check_username_or_email', $this->lang->line('user_not_found'));
                return false;
            }
            if (is_array($user_info)) {
                $unames = '';
                foreach ($user_info as $ui)
                    $unames .= $ui->username . ' ';

                $this->form_validation->set_message('check_username_or_email',
                                                    sprintf($this->lang->line('several_accounts'), $unames));
                return false;
            }
            $this->user_found = $user_info;
            return true;
        }
    }


    public function forgot_pw() {
        $this->lang->load('login', $this->language);
        $this->load->helper('form');
        $this->load->library('form_validation');

        $this->form_validation->set_rules('username', $this->lang->line('user_name'), 'trim|xss_clean'); 
        $this->form_validation->set_rules('email', $this->lang->line('email_address'), 'trim|valid_email|callback_check_username_or_email|xss_clean');

        if ($this->form_validation->run()) {
            if (!$this->user_found->email) {
                // VIEW:
                $this->load->view('view_top1', array('title' => $this->lang->line('forgot_name_or_password')));
                $this->load->view('view_top2');
                $this->load->view('view_menu_bar', array('langselect' => true));

                $this->load->view('view_main_page', array('left' => '<h1>' . $this->lang->line('handle_forgotten') . '</h1>',
                                                          'center' => $this->lang->line('no_email')));
                $this->load->view('view_bottom');
                return;
            }

            // Generate a random password reset key
            $reset_key = sprintf("%08x",mt_rand()).sprintf("%08x",mt_rand()).sprintf("%08x",mt_rand()).sprintf("%08x",mt_rand());
            $this->mod_users->set_reset_key($this->user_found,$reset_key);

            // Inform user
            $this->load->library('email');

            $this->email->from($this->config->item('mail_sender_address'), $this->config->item('mail_sender_name'));
            $this->email->to($this->user_found->email); 
            $this->email->subject($this->lang->line('forgotten_subject'));
            $this->email->message(sprintf($this->lang->line('forgotten_message'),
                                          $this->user_found->first_name, $this->user_found->last_name,
                                          $this->user_found->username,
                                          site_url("users/reset/$reset_key")));
            $this->email->send();
            $sent = true;
        }
        else {
            $this->user_found = null;
            $sent = false;
        }

        // VIEW:
        $this->load->view('view_top1', array('title' => $this->lang->line('forgot_name_or_password')));
        $this->load->view('view_top2');
        $this->load->view('view_menu_bar', array('langselect' => true));

        $center_text = $this->load->view('view_forgot_pw', array('user_found' => $this->user_found, 'sent' => $sent), true);

        $this->load->view('view_main_page', array('left' => '<h1>' . $this->lang->line('handle_forgotten') . '</h1>',
                                                  'center' => $center_text));
        $this->load->view('view_bottom');
    }

    public function reset() {
        $this->lang->load('login', $this->language);

        $reset_key = $this->uri->segment(3);
        $user_info = $this->mod_users->get_user_by_reset_key($reset_key);

        if (is_null($user_info)) {
            // VIEW:
            $this->load->view('view_top1', array('title' => $this->lang->line('cannot_reset_password')));
            $this->load->view('view_top2');
            $this->load->view('view_menu_bar', array('langselect' => true));
            
            $this->load->view('view_main_page', array('left' => '<h1>' . $this->lang->line('password_reset') . '</h1>',
                                                      'center' => '<h1>' . $this->lang->line('reset_link_bad') . '</h1>'));
            $this->load->view('view_bottom');
            return;
        }

        if (!$user_info->email) {
            // This can happen if the user's email address has been removed after the user requested
            // a password reset mail

            // VIEW:
            $this->load->view('view_top1', array('title' => $this->lang->line('cannot_reset_password')));
            $this->load->view('view_top2');
            $this->load->view('view_menu_bar', array('langselect' => true));
            
            $this->load->view('view_main_page', array('left' => '<h1>' . $this->lang->line('password_reset') . '</h1>',
                                                      'center' => $this->lang->line('no_email')));

            $this->load->view('view_bottom');
            return;
        }

        // Generate a password, eight random characters from the following set
        // which deliberately excludes l, 1, O and 0
        $pwchar = "abcdefghijkmnopqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ23456789";
        $strl = strlen($pwchar)-1;
        $pw = $pwchar[mt_rand(0,$strl)]
            . $pwchar[mt_rand(0,$strl)]
            . $pwchar[mt_rand(0,$strl)]
            . $pwchar[mt_rand(0,$strl)]
            . $pwchar[mt_rand(0,$strl)]
            . $pwchar[mt_rand(0,$strl)]
            . $pwchar[mt_rand(0,$strl)]
            . $pwchar[mt_rand(0,$strl)];
            
        $user_info->reset = null;
        $user_info->reset_time = 0;
        $this->mod_users->set_user($user_info, $pw);

        // Inform user
        $this->load->library('email');

        $this->email->from($this->config->item('mail_sender_address'), $this->config->item('mail_sender_name'));
        $this->email->to($user_info->email); 
        $this->email->subject($this->lang->line('password_reset_subject'));
        $this->email->message(sprintf($this->lang->line('password_reset_message'),
                                      $user_info->first_name, $user_info->last_name,
                                      $user_info->username, $pw));
        $this->email->send();

        // VIEW:
        $this->load->view('view_top1', array('title' => $this->lang->line('password_reset')));
        $this->load->view('view_top2');
        $this->load->view('view_menu_bar', array('langselect' => true));
        $this->load->view('view_main_page', array('left' => '<h1>' . $this->lang->line('password_reset') . '</h1>',
                                                  'center' => '<h1>' 
                                                              . sprintf($this->lang->line('password_reset_sent'), $user_info->email)
                                                              . '</h1>'));
        $this->load->view('view_bottom');

    }
  }