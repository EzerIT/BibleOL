<?php
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
            $this->mod_users->check_teacher();

            $users_per_page = $this->config->item('users_per_page');
            $user_count = $this->mod_users->count_users();
            $page_count = intval(ceil($user_count/$users_per_page));

            $offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
            if ($offset>=$page_count)
                $offset = $page_count-1;
            if ($offset<0)
                $offset = 0;

            $allusers = $this->mod_users->get_all_users_part($users_per_page,$offset*$users_per_page);

            // VIEW:
            $this->load->view('view_top1', array('title' => $this->lang->line('users')));
            $this->load->view('view_top2');
            $this->load->view('view_menu_bar', array('langselect' => true));
            $this->load->view('view_confirm_dialog');
            $center_text = $this->load->view('view_user_list',
                                             array('allusers' => $allusers,
                                                   'offset' => $offset,
                                                   'users_per_page' => $users_per_page,
                                                   'user_count' => $user_count,
                                                   'page_count' => $page_count,
                                                   'isadmin' => $this->mod_users->is_admin(),
                                                   'my_id' => $this->mod_users->my_id()),
                                             true);
            $this->load->view('view_main_page', array('left_title' => $this->lang->line('user_list'),
                                                      'left' => $this->lang->line('configure_your_users'),
                                                      'center' => $center_text));
            $this->load->view('view_bottom');
        }
        catch (DataException $e) {
            $this->error_view($e->getMessage(), $this->lang->line('users'));
        }
    }

    private function delete_common() {
        $this->mod_users->delete_user($this->mod_users->my_id());

        // Log out
        $this->mod_users->set_login_session(0, false, false);

        // VIEW:
        $this->load->view('view_top1', array('title' => $this->lang->line('user_profile_deleted')));
        $this->load->view('view_top2');
        $this->load->view('view_menu_bar', array('langselect' => false));

        $center_text = $this->load->view('view_user_deleted', null, true);

        $this->load->view('view_main_page', array('left_title' => $this->lang->line('user_profile_deleted'),
                                                  'center' => $center_text));
        $this->load->view('view_bottom');
    }

    public function delete_me() {
        try {
            $this->mod_users->check_logged_in_local();
            $this->delete_common();
        }
        catch (DataException $e) {
            $this->error_view($e->getMessage(), $this->lang->line('users'));
        }
    }

    public function delete_me_google() {
        try {
            $this->mod_users->check_logged_in_oauth2('google');
 
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
                    $this->mod_users->set_login_session(0, false, false);

                    throw new DataException($this->lang->line('google_no_response_delete'));
                    break;

              case '200':
                    // Token successfully revoked
                    $this->delete_common();
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

    public function delete_me_facebook() {
        try {
            $this->mod_users->check_logged_in_oauth2('facebook');
 
            // Facebook does not allow revoking user permissions from the server

            $this->delete_common();
        }
        catch (DataException $e) {
            $this->error_view($e->getMessage(), $this->lang->line('users'));
        }
    }

    public function delete_user() {
        try {
            $this->mod_users->check_teacher();

            $userid = is_numeric($_GET['userid']) ? intval($_GET['userid']) : 0;
            $offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
        
            if ($userid<=0)
                throw new DataException($this->lang->line('illegal_user_id'));

            if ($this->mod_users->is_me($userid))
                throw new DataException($this->lang->line('cannot_delete_self'));

            $user_info = $this->mod_users->get_user_by_id($userid);

            // Only an administrator can delete other administrators and teachers
            if (!$this->mod_users->is_admin() && ($user_info->isadmin || $user_info->isteacher))
                throw new DataException($this->lang->line('only_admin_delete'));

            $this->mod_users->delete_user($userid);
            redirect("/users?offset=$offset");
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

   public function sign_up() {
        try {
            if ($this->mod_users->is_logged_in())
                throw new DataException($this->lang->line('already_logged_in'));

            $user_info = $this->mod_users->get_user_by_id(-1);

            $this->load->helper('form');
            $this->load->library('form_validation');

            // For pedagogical reasons, the set_rules() functions must be called in the order the
            // fields appear in the form

            $this->form_validation->set_message('is_unique', sprintf($this->lang->line('user_name_used'), $this->input->post('username')));
            $this->form_validation->set_rules('username', $this->lang->line('user_name'), 'trim|required|strip_tags|is_unique[user.username]');

            // Common validation rules
            $this->form_validation->set_rules('first_name', $this->lang->line('first_name'), 'trim|required|strip_tags');
            $this->form_validation->set_rules('last_name', $this->lang->line('last_name'), 'trim|required|strip_tags');
            $this->form_validation->set_rules('email', $this->lang->line('email'), 'trim|required|valid_email|strip_tags');
            $this->form_validation->set_rules('preflang', '', '');
            
            if ($this->form_validation->run()) {
                $user_info->first_name = $this->input->post('first_name');
                $user_info->last_name = $this->input->post('last_name');
                $user_info->isadmin = false;
                $user_info->isteacher = false;
                $user_info->email = $this->input->post('email');
                $user_info->username = $this->input->post('username');
                $user_info->created_time = time();
                $user_info->last_login = 0;  // This means never logged in. User must log in within 48 hours.
                $user_info->warning_sent = 0;
                $user_info->preflang = $this->input->post('preflang');

                $pw = $this->generate_pw();
                $query = $this->mod_users->set_user($user_info, $pw);

                // Inform user
                $this->load->library('email');
                $this->lang->load_secondary('users',$user_info->preflang);

                $this->email->from($this->config->item('mail_sender_address'), $this->config->item('mail_sender_name'));
                $this->email->to($user_info->email); 
                $this->email->subject($this->lang->line_secondary('account_created_subject'));
                $this->email->message(sprintf($this->lang->line_secondary('account_you_created_message1'),
                                              $user_info->first_name, $user_info->last_name,
                                              $user_info->username,
                                              $pw)
                                      . sprintf($this->lang->line_secondary('account_you_created_message3'), site_url('login')));
                $this->email->send();

                // VIEW:
                $this->load->view('view_top1', array('title' => $this->lang->line('create_account')));
                $this->load->view('view_top2');
                $this->load->view('view_menu_bar', array('langselect' => true));

                $this->load->view('view_main_page', array('left_title' => $this->lang->line('you_created_account'),
                                                          'center' => '<h1>'
                                                          . sprintf($this->lang->line('password_sent'), $user_info->email)
                                                          . '</h1>'));
                $this->load->view('view_bottom');
            }
            else {
                // VIEW:
                $this->load->view('view_top1', array('title' => $this->lang->line('create_account')));
                $this->load->view('view_top2');
                $this->load->view('view_menu_bar', array('langselect' => true));

                $center_text = $this->load->view('view_edit_user',array('userid' => -2,
                                                                        'user_info' => $user_info,
                                                                        'isadmin' => false,
                                                                        'isteacher' => false,
                                                                        'curlang' => $this->language_short), true);

                $this->load->view('view_main_page', array('left_title' => $this->lang->line('specify_user_information'),
                                                          'center' => $center_text));
                $this->load->view('view_bottom');
            }
        }
        catch (DataException $e) {
            $this->error_view($e->getMessage(), $this->lang->line('users'));
        }
   }

   public function edit_one_user() {
        try {
            $this->mod_users->check_teacher();

            $userid = isset($_GET['userid']) ? intval($_GET['userid']) : 0;
            $offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;

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

            if (!empty($user_info->oauth2_login)) {
                $this->form_validation->set_rules('isadmin', '', '');
                $this->form_validation->set_rules('isteacher', '', '');
                $this->form_validation->set_rules('preflang', '', '');

                if ($this->form_validation->run()) {
                    if ($this->mod_users->is_admin())
                        $user_info->isadmin = $this->input->post('isadmin')==='yes';
                    if ($this->mod_users->is_teacher())
                        $user_info->isteacher = $this->input->post('isteacher')==='yes';
                    
                    $user_info->preflang = $this->input->post('preflang');

                    $query = $this->mod_users->set_user($user_info, null);

                    redirect("/users?offset=$offset");
                }
                else {
                    // VIEW:
                    $this->load->view('view_top1', array('title' => $this->lang->line('edit_user')));
                    $this->load->view('view_top2');
                    $this->load->view('view_menu_bar', array('langselect' => true));

                    $center_text = $this->load->view('view_edit_user',array('userid' => $userid,
                                                                            'offset' => $offset,
                                                                            'user_info' => $user_info,
                                                                            'isadmin' => $this->mod_users->is_admin(),
                                                                            'isteacher' => $this->mod_users->is_teacher(),
                                                                            'curlang' => $user_info->preflang), true);

                    $this->load->view('view_main_page', array('left_title' => $this->lang->line('edit_user_information'),
                                                              'center' => $center_text));
                    $this->load->view('view_bottom');
                }
            }
            else {
                // This is a local user, or this is a new account

                // Common validation rules
                $this->form_validation->set_rules('first_name', $this->lang->line('first_name'), 'trim|required|strip_tags');
                $this->form_validation->set_rules('last_name', $this->lang->line('last_name'), 'trim|required|strip_tags');
                $this->form_validation->set_rules('isadmin', '', '');
                $this->form_validation->set_rules('isteacher', '', '');
                $this->form_validation->set_rules('email', $this->lang->line('email'), 'trim|valid_email|strip_tags');
                $this->form_validation->set_rules('preflang', '', '');

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
                    if ($this->mod_users->is_admin())
                        $user_info->isadmin = $this->input->post('isadmin')==='yes';
                    if ($this->mod_users->is_teacher())
                        $user_info->isteacher = $this->input->post('isteacher')==='yes';

                    $user_info->email = $this->input->post('email');
                    $user_info->preflang = $this->input->post('preflang');

                    if ($userid<=0)
                        $user_info->username = $this->input->post('username');

                    $pw = $this->input->post('password1');
                    $query = $this->mod_users->set_user($user_info, $pw);

                    if ($userid<=0 && $user_info->email) {
                        // Inform user
                        $this->load->library('email');
                        $this->lang->load_secondary('users',$user_info->preflang);

                        $this->email->from($this->config->item('mail_sender_address'), $this->config->item('mail_sender_name'));
                        $this->email->to($user_info->email); 
                        $this->email->subject($this->lang->line_secondary('account_created_subject'));
                        $this->email->message(sprintf($this->lang->line_secondary('account_created_message1'),
                                                      $user_info->first_name, $user_info->last_name,
                                                      $user_info->username,
                                                      $pw)
                                              . ($user_info->isadmin ? $this->lang->line_secondary('account_created_message2') : '')
                                              . ($user_info->isteacher ? $this->lang->line_secondary('account_created_message2t') : '')
                                              . sprintf($this->lang->line_secondary('account_created_message3'), site_url('login')));
                        $this->email->send();
                    }

                    redirect("/users?offset=$offset");
                }
                else {
                    // VIEW:
                    $this->load->view('view_top1', array('title' => $this->lang->line('edit_user')));
                    $this->load->view('view_top2');
                    $this->load->view('view_menu_bar', array('langselect' => true));

                    $center_text = $this->load->view('view_edit_user',array('userid' => $userid,
                                                                            'offset' => $offset,
                                                                            'user_info' => $user_info,
                                                                            'isadmin' => $this->mod_users->is_admin(),
                                                                            'isteacher' => $this->mod_users->is_teacher(),
                                                                            'curlang' => $user_info->preflang), true);

                    $this->load->view('view_main_page', array('left_title' => $this->lang->line('edit_user_information'),
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

           if (!empty($user_info->oauth2_login)) {
               // OAuth2-authorized user
               $this->load->helper('form');
               $this->load->library('form_validation');

               $this->form_validation->set_rules('preflang', '', '');

               if ($this->form_validation->run()) {
                   $user_info->preflang = $this->input->post('preflang');
                   $query = $this->mod_users->set_user($user_info, $pw);
                   redirect('/');
               }
               else {
                   // VIEW:
                   $this->load->view('view_top1', array('title' => $this->lang->line('display_user')));
                   $this->load->view('view_top2');
                   $this->load->view('view_menu_bar', array('langselect' => true));
                   $this->load->view('view_confirm_dialog');
            
                   $left_text = $this->load->view('view_oauth2_profile_left',
                                                  array('authority' => $user_info->oauth2_login),
                                                  true);
                   $center_text = $this->load->view('view_oauth2_profile',array('user_info' => $user_info), true);

                   $this->load->view('view_main_page', array('left_title' => $this->lang->line('this_your_profile'),
                                                             'left' => $left_text,
                                                             'center' => $center_text));
                   $this->load->view('view_bottom');
               }
           }
           else {
               // Local user

               $this->load->helper('form');
               $this->load->library('form_validation');

               $this->form_validation->set_rules('first_name', $this->lang->line('first_name'), 'trim|required|strip_tags');
               $this->form_validation->set_rules('last_name', $this->lang->line('last_name'), 'trim|required|strip_tags');
               $this->form_validation->set_rules('email', $this->lang->line('email'), 'trim|valid_email|strip_tags');
               $this->form_validation->set_rules('preflang', '', '');

               $this->form_validation->set_rules('password1', $this->lang->line('new_password'),
                                                 'trim|matches[password2]|callback_password_length_check');
               $this->form_validation->set_rules('password2', $this->lang->line('repeat_new_password'), 'trim');


               if ($this->form_validation->run()) {
                   $user_info->first_name = $this->input->post('first_name');
                   $user_info->last_name = $this->input->post('last_name');
                   $user_info->email = $this->input->post('email');
                   $user_info->preflang = $this->input->post('preflang');

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

                   $this->load->view('view_main_page', array('left_title' => $this->lang->line('edit_user_profile'),
                                                             'left' => $left_text,
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

                $this->load->view('view_main_page', array('left_title' => $this->lang->line('handle_forgotten'),
                                                          'center' => $this->lang->line('no_email')));
                $this->load->view('view_bottom');
                return;
            }

            // Generate a random password reset key
            $reset_key = sprintf("%08x",mt_rand()).sprintf("%08x",mt_rand()).sprintf("%08x",mt_rand()).sprintf("%08x",mt_rand());
            $this->mod_users->set_reset_key($this->user_found,$reset_key);

            // Inform user
            $this->load->library('email');
            $this->lang->load_secondary('login',$this->user_found->preflang);

            $this->email->from($this->config->item('mail_sender_address'), $this->config->item('mail_sender_name'));
            $this->email->to($this->user_found->email); 
            $this->email->subject($this->lang->line_secondary('forgotten_subject'));
            $this->email->message(sprintf($this->lang->line_secondary('forgotten_message'),
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

        $this->load->view('view_main_page', array('left_title' => $this->lang->line('handle_forgotten'),
                                                  'center' => $center_text));
        $this->load->view('view_bottom');
    }

    // Generate a password, eight random characters from the following set
    // which deliberately excludes I, l, 1, O and 0
    private function generate_pw() {
        $pwchar = "abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789";
        $strl = strlen($pwchar)-1;
        return $pwchar[mt_rand(0,$strl)]
             . $pwchar[mt_rand(0,$strl)]
             . $pwchar[mt_rand(0,$strl)]
             . $pwchar[mt_rand(0,$strl)]
             . $pwchar[mt_rand(0,$strl)]
             . $pwchar[mt_rand(0,$strl)]
             . $pwchar[mt_rand(0,$strl)]
             . $pwchar[mt_rand(0,$strl)];
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
            
            $this->load->view('view_main_page', array('left_title' => $this->lang->line('password_reset'),
                                                      'center' => '<h1>' . $this->lang->line('reset_link_bad') . '</h1>'));
            $this->load->view('view_bottom');
            return;
        }

        $this->lang->load_secondary('login',$user_info->preflang);

        if (!$user_info->email) {
            // This can happen if the user's email address has been removed after the user requested
            // a password reset mail

            // VIEW:
            $this->load->view('view_top1', array('title' => $this->lang->line_secondary('cannot_reset_password')));
            $this->load->view('view_top2');
            $this->load->view('view_menu_bar', array('langselect' => true));
            
            $this->load->view('view_main_page', array('left_title' => $this->lang->line_secondary('password_reset'),
                                                      'center' => $this->lang->line_secondary('no_email')));

            $this->load->view('view_bottom');
            return;
        }

        $user_info->reset = null;
        $user_info->reset_time = 0;
        $pw = $this->generate_pw();
        $this->mod_users->set_user($user_info, $pw);

        // Inform user
        $this->load->library('email');

        $this->email->from($this->config->item('mail_sender_address'), $this->config->item('mail_sender_name'));
        $this->email->to($user_info->email); 
        $this->email->subject($this->lang->line_secondary('password_reset_subject'));
        $this->email->message(sprintf($this->lang->line_secondary('password_reset_message'),
                                      $user_info->first_name, $user_info->last_name,
                                      $user_info->username, $pw));
        $this->email->send();

        // VIEW:
        $this->load->view('view_top1', array('title' => $this->lang->line_secondary('password_reset')));
        $this->load->view('view_top2');
        $this->load->view('view_menu_bar', array('langselect' => true));
        $this->load->view('view_main_page', array('left_title' => $this->lang->line_secondary('password_reset'),
                                                  'center' => '<h1>' 
                                                              . sprintf($this->lang->line_secondary('password_reset_sent'), $user_info->email)
                                                              . '</h1>'));
        $this->load->view('view_bottom');

    }

    private function warning_mail(stdClass $u, string $warning) {
        $this->lang->clear_secondary();
        $this->lang->load_secondary('users',$u->preflang);

        $this->email->from($this->config->item('mail_sender_address'), $this->config->item('mail_sender_name'));
        $this->email->to($u->email); 
        $this->email->subject($this->lang->line_secondary('expiry_warning_subject'));

        $message = sprintf($this->lang->line_secondary($warning),
                           $u->first_name, $u->last_name, $this->config->item('site_url'));

        switch ($u->oauth2_login) {
          case 'google':
          case 'facebook':
                $message .= $this->lang->line_secondary("expiry_warning_message_{$u->oauth2_login}");
                break;

          default:
                $message .= sprintf($this->lang->line_secondary('expiry_warning_message_local'), $u->username);
                break;
        }

        $this->email->message($message);
        $this->email->send();
    }

    // Intended to be run from cron.
    // Performs three kinds of expiration:
    //   * New accounts where the user has not logged in are deleted after 48 hours.
    //   * Accounts where the user has not been active for 9 months are sent a warning and made invisible.
    //   * Accounts where the user has not been active for 17 months are sent a warning.
    //   * Accounts where the user has not been active for 18 months are deleted.
    public function expire_users() {
        if (!$this->input->is_cli_request()) {
            echo '<pre>This command can only be run from the command line</pre>';
            die;
        }

        // MODEL:
        $users_old_18months = $this->mod_users->old_inactive(0, 18*30*24*3600);  // 18 months - delete
        $users_old_17months = $this->mod_users->old_inactive(2, 17*30*24*3600);  // 17 months - warning 2
        $users_old_9months = $this->mod_users->old_inactive(1, 9*30*24*3600);  // 9 months - warning 1
        $users_new_inactive = $this->mod_users->delete_new_inactive(48*3600);  // 48 hours
         
        // Send mails:
        $this->load->library('email');
         
        foreach ($users_old_17months as $u)
            if (!empty($u->email)) 
                $this->warning_mail($u,'expiry_warning_2_message');
         
        foreach ($users_old_9months as $u)
            if (!empty($u->email)) 
                $this->warning_mail($u,'expiry_warning_1_message');
         
        // VIEW:
        if (count($users_new_inactive)>0) {
            echo "New accounts deleted because of 48 hours of inactivity:\n";
            foreach ($users_new_inactive as $u) {
                if (!empty($u->email)) 
                    echo "    $u->username ($u->first_name $u->last_name - $u->email)\n";
                else
                    echo "    $u->username ($u->first_name $u->last_name - NO EMAIL)\n";
            }
            echo "\n";
        }

        if (count($users_old_9months)>0) {
            echo "Old accounts warned because of 9 months of inactivity:\n";
            foreach ($users_old_9months as $u) {
                if (!empty($u->email)) 
                    echo "    $u->username ($u->first_name $u->last_name - $u->email)\n";
                else
                    echo "    $u->username ($u->first_name $u->last_name - NO EMAIL)\n";
            }
            echo "\n";
        }
         
        if (count($users_old_17months)>0) {
            echo "Old accounts warned because of 17 months of inactivity:\n";
            foreach ($users_old_17months as $u) {
                if (!empty($u->email)) 
                    echo "    $u->username ($u->first_name $u->last_name - $u->email)\n";
                else
                    echo "    $u->username ($u->first_name $u->last_name - NO EMAIL)\n";
            }
            echo "\n";
        }
         
        if (count($users_old_18months)>0) {
            echo "Old accounts deleted because of 18 months of inactivity:\n";
            foreach ($users_old_18months as $u) {
                if (!empty($u->email)) 
                    echo "    $u->username ($u->first_name $u->last_name - $u->email)\n";
                else
                    echo "    $u->username ($u->first_name $u->last_name - NO EMAIL)\n";
            }
            echo "\n";
        }
    }
  }