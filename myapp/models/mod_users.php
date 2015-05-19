<?php

class Mod_users extends CI_Model {
    private $admin;
    private $user_id;

    private $me;

    public function __construct() {
        parent::__construct();

        $this->config->load('ol');
        $this->load->database();

        $this->user_id = $this->session->userdata('ol_user');
        if ($this->user_id===false)
            $this->user_id = 0;
        else
            $this->user_id = intval($this->user_id);
            
        $query = $this->db->where('id',$this->user_id)->get('user');
		if ($this->me = $query->row())
			$this->admin = $this->me->isadmin;
		else
			$this->admin = false;
    }

    public function is_admin() {
        return $this->admin;
    }

    public function my_id() {
        return $this->user_id;
    }

    public function my_name() {
        if (!$this->me)
            return null;
        return "{$this->me->first_name} {$this->me->last_name}";
    }

    public function get_me() {
        if (!$this->me)
            return null;
        return $this->me;
    }

    public function is_logged_in() {
        return $this->user_id>0;
    }

    public function is_me(integer $user_id) {
        return $user_id==$this->user_id;
    }

    // Returns user info if login is successful, otherwise returns null
    public function verify_login(string $name, string $pw) {
        $pw_md5 = md5($this->config->item('pw_salt') . $pw);

        $query = $this->db->where('username',$name)->where('password',$pw_md5)->get('user');
		if ($row = $query->row())
            return $row;
        else
            return null;
    }

    public function check_admin() {
        if (!$this->mod_users->is_admin())
            throw new DataException($this->lang->line('must_be_admin'));
    }

    public function check_logged_in() {
        if (!$this->mod_users->is_logged_in())
            throw new DataException($this->lang->line('must_be_logged_in'));
    }


    public function check_logged_in_google(boolean $must_be_google) {
        if (!$this->mod_users->is_logged_in())
            throw new DataException($this->lang->line('must_be_logged_in'));

        $user_info = $this->mod_users->get_me();
        assert('!is_null($user_info)');

        if ($must_be_google) {
            if (!$user_info->google_login)
                throw new DataException($this->lang->line('must_be_google'));
        }
        else {
            if ($user_info->google_login)
                throw new DataException($this->lang->line('must_not_be_google'));
        }
    }



    public function set_login_session(integer $user, $admin) {
        $this->session->set_userdata('ol_user', $user);
        $this->session->set_userdata('ol_admin', $admin);  // NOTE: 'ol_admin' is only a hint. Do not rely on it.
        $this->user_id = $user;
        $this->admin = $admin;
    }

    public function get_all_users() {
        $query = $this->db->get('user');
        return $query->result();
    }

    // $userid==-1 means create new user
    public function get_user_by_id(integer $userid) {
        if ($userid===-1) {
            // Create new user
            $user = new stdClass();
            $user->id = null; // Indicates new user
            $user->first_name = '';
            $user->last_name = '';
            $user->username = '';
            $user->password = '';
            $user->isadmin = 0;
            $user->email = '';
            $user->google_login = false;
        }
        else {
            $query = $this->db->where('id',$userid)->get('user');
            $user = $query->row();
            if (!$user)
                throw new DataException($this->lang->line('illegal_user_id'));
        }
        return $user;
    }


    // Returns: Null if user not found
    //          User information structure if one user found
    //          Array of user information structures if more than one user found
    public function get_user_by_name_or_email(string $username, string $email) {
        if ($username!='') {
            $query = $this->db->where('username',$username)->where('google_login',0)->get('user');
            return $query->num_rows()>0 ? $query->row() : null;
        }
        
        $query = $this->db->where('email',$email)->where('google_login',0)->get('user');
        $count = $query->num_rows();

        if ($count===1)
            return $query->row();
        else if ($count>1)
            return $query->result();
        else
            return null;
    }

    // Returns: Null if user not found
    //          User information structure if one user found
    public function get_user_by_reset_key(string $reset_key) {
        $query = $this->db->where('reset',$reset_key)->get('user');
        if ($query->num_rows()===0)
            return null;
        else {
            $row = $query->row();
            if (time()-$row->reset_time > 48*3600) // Reset key has expired
                return null;
            return $row;
        }
    }

    public function set_reset_key(stdClass $user_info, string $reset_key) {
        $this->db->where('id',$user_info->id)->update('user',array('reset'=>$reset_key, 'reset_time'=>time()));
    }

    public function set_user(stdClass $user_info, string $pw) {
        if (!empty($pw))
            $user_info->password = md5($this->config->item('pw_salt') . $pw);

        if (is_null($user_info->id)) // Insert new user
            $query = $this->db->insert('user', $user_info);
        else // Update existing user
            $query = $this->db->where('id',$user_info->id)->update('user',$user_info);

        return $query;
    }

    
    public function delete_user(integer $userid) {
        $this->db->where('id', $userid)->delete('user');
        if ($this->db->affected_rows()==0)
            throw new DataException($this->lang->line('illegal_user_id'));

        $this->db->where('user_id', $userid)->delete('font');
        $this->db->where('user_id', $userid)->delete('personal_font');
        $this->db->where('userid', $userid)->delete('sta_displayfeature');
        $this->db->where('userid', $userid)->delete('sta_question');
        $this->db->where('userid', $userid)->delete('sta_quiz');
        $this->db->where('userid', $userid)->delete('sta_quiztemplate');
        $this->db->where('userid', $userid)->delete('sta_requestfeature');
        $this->db->where('userid', $userid)->delete('sta_universe');
        $this->db->where('user_id', $userid)->delete('userconfig');
        $this->db->where('userid', $userid)->delete('userclass');
    }

    
    /// @return True if this is the first time this Google user logs in.
    public function new_google_user(string $google_id, string $first_name, string $last_name, string $email) {
        $query = $this->db->where('google_login',1)->where('username',"ggl_$google_id")->get('user');

		if ($row = $query->row()) {
            $this->me = $row;
			$this->admin = $this->me->isadmin;
            $this->user_id = $this->me->id;

            $this->session->set_userdata('ol_user', $this->user_id);
            $this->session->set_userdata('ol_admin', $this->admin);  // NOTE: 'ol_admin' is only a hint. Do not rely on it.

            if ($first_name!==$this->me->first_name || $last_name!==$this->me->last_name || $email!==$this->me->email) {
                $query = $this->db->where('id',$this->user_id)->update('user',array('first_name' => $first_name,
                                                                                    'last_name' => $last_name,
                                                                                    'email' => $email));
            }

            return false;
        }
        else {
            // Create new user
            $user = new stdClass();
            $user->id = null; // Indicates new user
            $user->first_name = $first_name;
            $user->last_name = $last_name;
            $user->username = "ggl_$google_id";
            $user->password = 'NONE';
            $user->isadmin = 0;
            $user->email = $email;
            $user->google_login = true;
   
            $query = $this->db->insert('user', $user);
      
			$this->admin = false;
            $this->user_id = $this->db->insert_id();

            $this->session->set_userdata('ol_user', $this->user_id);
            $this->session->set_userdata('ol_admin', $this->admin);  // NOTE: 'ol_admin' is only a hint. Do not rely on it.

            return true;
        }
    }
  }