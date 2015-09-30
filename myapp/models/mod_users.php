<?php

function teacher_cmp($t1, $t2) {
    if ($t1->last_name < $t2->last_name)
        return -1;
    if ($t1->last_name > $t2->last_name)
        return 1;
    if ($t1->first_name < $t2->first_name)
        return -1;
    if ($t1->first_name > $t2->first_name)
        return 1;
    return 0;
}


class Mod_users extends CI_Model {
    private $admin;
    private $teacher;
    private $user_id;

    private $me;

    public function __construct() {
        parent::__construct();

        $this->config->load('ol');
        $this->load->database();

        $this->user_id = $this->session->userdata('ol_user');
        //echo "<pre>",print_r($this->session, true);die;

        if ($this->user_id===false)
            $this->user_id = 0;
        else
            $this->user_id = intval($this->user_id);
            
        $query = $this->db->where('id',$this->user_id)->get('user');
		if ($this->me = $query->row()) {
			$this->admin = $this->me->isadmin;
			$this->teacher = $this->me->isteacher;
        }
		else {
			$this->admin = false;
			$this->teacher = false;
        }
    }

    public function is_admin() {
        return $this->admin;
    }

    public function is_teacher() {
        return $this->teacher || $this->admin; // All admins are teachers
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
		if ($row = $query->row()) {
            $this->db->where('id',$row->id)->update('user',array('last_login'=>time(), 'warning_sent'=>0));
            return $row;
        }
        else
            return null;
    }

    public function check_admin() {
        if (!$this->mod_users->is_admin())
            throw new DataException($this->lang->line('must_be_admin'));
    }

    public function check_teacher() {
        if (!$this->mod_users->is_teacher())
            throw new DataException($this->lang->line('must_be_teacher'));
    }

    public function check_logged_in() {
        if (!$this->mod_users->is_logged_in())
            throw new DataException($this->lang->line('must_be_logged_in'));
    }


    public function check_logged_in_local() {
        if (!$this->mod_users->is_logged_in())
            throw new DataException($this->lang->line('must_be_logged_in'));

        $user_info = $this->mod_users->get_me();
        assert('!is_null($user_info)');

        if (!empty($user_info->oauth2_login))
            throw new DataException($this->lang->line("must_not_be_{$user_info->oauth2_login}"));
    }

    public function check_logged_in_oauth2(string $authority) {
        if (!$this->mod_users->is_logged_in())
            throw new DataException($this->lang->line('must_be_logged_in'));

        $user_info = $this->mod_users->get_me();
        assert('!is_null($user_info)');

        if ($user_info->oauth2_login!==$authority)
            throw new DataException($this->lang->line("must_be_$authority"));
    }

    public function set_login_session(integer $user, $admin, $teacher, $preflang=null) {
        $this->session->set_userdata('ol_user', $user);
        $this->session->set_userdata('ol_admin', $admin);  // NOTE: 'ol_admin' is only a hint. Do not rely on it.
        $this->session->set_userdata('ol_teacher', $teacher);  // NOTE: 'ol_teacher' is only a hint. Do not rely on it.
        if (!is_null($preflang) && $preflang!='none')
            $this->session->set_userdata('language', $preflang);

        $this->user_id = $user;
        $this->admin = $admin;
        $this->teacher = $teacher;
    }

    public function get_all_users() {
        $query = $this->db->get('user');
        return $query->result();
    }

    public function get_teachers() {
        $query = $this->db->where('`isteacher`=1 OR `isadmin`=1',null,false)->get('user');
        $teachers = $query->result();
        usort($teachers, 'teacher_cmp');
        return $teachers;
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
            $user->oauth2_login = null;
            $user->created_time = time();
            $user->last_login = time()-10; // Assume that the user has logged in at least once to prevent expiry.
                                           // The -10 is used when listing users to indicate that this is a fake value.
            $user->warning_sent = 0;
            $user->isteacher = 0;
            $user->preflang = 'none';
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
            $query = $this->db->where('username',$username)->where('oauth2_login',null)->get('user'); //TODO: Test this
            return $query->num_rows()>0 ? $query->row() : null;
        }
        
        $query = $this->db->where('email',$email)->where('oauth2_login',null)->get('user'); //TODO: Test this
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

        // Most deletions are handled by foreign keys. The font and exerciseowner tables are
        // exceptions because entries here can have user_id/ownerid = 0
        $this->db->where('user_id', $userid)->delete('font');

        // Change ownership of exercises
        $query = $this->db->where('ownerid',$userid)->update('exerciseowner',array('ownerid' => 0));
    }


    /// @return True if this is the first time this user logs in.
    public function new_oauth2_user(string $authority, string $oauth2_user_id, string $first_name, string $last_name, string $email) {
        switch ($authority) {
          case 'google':
                $username = "ggl_$oauth2_user_id";
                break;

          case 'facebook':
                $username = "fcb_$oauth2_user_id";
                break;
        }

        $query = $this->db->where('oauth2_login',$authority)->where('username',$username)->get('user');

		if ($row = $query->row()) {
            $this->me = $row;
			$this->admin = $this->me->isadmin;
			$this->teacher = $this->me->isteacher;
            $this->user_id = $this->me->id;

            $this->session->set_userdata('ol_user', $this->user_id);
            $this->session->set_userdata('ol_admin', $this->admin);  // NOTE: 'ol_admin' is only a hint. Do not rely on it.
            $this->session->set_userdata('ol_teacher', $this->teacher);  // NOTE: 'ol_teacher' is only a hint. Do not rely on it.
            if ($row->preflang!='none')
                $this->session->set_userdata('language', $row->preflang);

            if ($first_name!==$this->me->first_name || $last_name!==$this->me->last_name || $email!==$this->me->email) {
                $query = $this->db->where('id',$this->user_id)->update('user',array('first_name' => $first_name,
                                                                                    'last_name' => $last_name,
                                                                                    'email' => empty($email) ? null : $email,
                                                                                    'last_login' => time(),
                                                                                    'warning_sent' => 0));
            }
            else
                $query = $this->db->where('id',$this->user_id)->update('user',array('last_login' => time(),
                                                                                    'warning_sent' => 0));

            return false;
        }
        else {
            // Create new user
            $user = new stdClass();
            $user->id = null; // Indicates new user
            $user->first_name = $first_name;
            $user->last_name = $last_name;
            $user->username = $username;
            $user->password = 'NONE';
            $user->isadmin = 0;
            $user->isteacher = 0;
            $user->email = empty($email) ? null : $email;
            $user->oauth2_login = $authority;
            $user->created_time = time();
            $user->last_login = time();
            $user->warning_sent = 0;
            $user->preflang = $this->language_short;

            $query = $this->db->insert('user', $user);
      
			$this->admin = false;
			$this->teacher = false;
            $this->user_id = $this->db->insert_id();

            $this->session->set_userdata('ol_user', $this->user_id);
            $this->session->set_userdata('ol_admin', $this->admin);  // NOTE: 'ol_admin' is only a hint. Do not rely on it.
            $this->session->set_userdata('ol_teacher', $this->teacher);  // NOTE: 'ol_teacher' is only a hint. Do not rely on it.
            $this->session->set_userdata('language', $user->preflang);

            return true;
        }
    }

    // Delete accounts older than $time seconds where the user has never logged in
    public function delete_new_inactive(integer $time) {
        $now = time();
        $query = $this->db->where('last_login',0)->where('created_time >',0)->where('created_time <',$now-$time)
            ->get('user');
        $users = $query->result();

        foreach ($users as $u)
            $this->delete_user(intval($u->id));

        return $users;
    }

    // Handle accounts where the user has not logged in for the last $time seconds
    // $level==0 means: Delete user
    // $level!=0 means: Set warning_sent to $level
    public function old_inactive(integer $level, integer $time) {
        $now = time();
        if ($level==0) {
            $query = $this->db->where('last_login <',$now-$time)
                ->get('user');
            $users = $query->result();
   
            foreach ($users as $u)
                $this->delete_user(intval($u->id));
        }
        else {
            $query = $this->db->where('last_login <',$now-$time)->where('warning_sent <',$level)
                ->get('user');
            $users = $query->result();
            $this->db->where('last_login <',$now-$time)->where('warning_sent <',$level)
                ->update('user',array('warning_sent'=>$level));
        }

        return $users;
    }
  }