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

function make_full_name($u) {
    if ($u->family_name_first)
        return "$u->last_name$u->first_name"; // Note: No space
    else {
        if (empty($u->first_name))
            return $u->last_name;
        if (empty($u->last_name))
            return $u->first_name;

        return "$u->first_name $u->last_name";
    }
}

// Creates a dummy user structure with id=0
function make_dummy_user() {
    $u = new stdClass;
    $u->id = 0;
    $u->first_name = '';
    $u->last_name = '';
    $u->username = '';
    $u->password = '';
    $u->reset = '';
    $u->reset_time = 0;
    $u->isadmin = 0;
    $u->email = '';
    $u->oauth2_login = '';
    $u->created_time = 0;
    $u->last_login = 0;
    $u->warning_sent = 0;
    $u->isteacher = 0;
    $u->preflang = '';
    $u->prefvariant = '';
    $u->family_name_first = 0;
    $u->istranslator = 0;
    $u->accept_policy = 0;
    $u->policy_lang = '';
    $u->acc_code = '';
    $u->acc_code_time = 0;

    return $u;
}

class Mod_users extends CI_Model {
    const CURRENT_POLICY_DATE = 1512390210;  // 2017-12-04
    const ACCEPT_CODE_EXPIRY = 15*60; // 15 minutes

    private $me;

    public function __construct() {
        parent::__construct();

        $this->config->load('ol');
        $this->load->database();

        $user_id = intval($this->session->userdata('ol_user'));  // Sets $user_id to 0 if userdata('ol_user') is not set
        
        $query = $this->db->where('id',$user_id)->get('user');
		$this->me = $query->row();
        
		if (is_null($this->me))
            $this->me = make_dummy_user();
    }

    public function is_admin() {
        return true;
        return $this->me->isadmin && $this->accepted_current_policy();
    }

    public function is_teacher() {
        return true;
        return ($this->me->isteacher || $this->me->isadmin) && $this->accepted_current_policy(); // All admins are teachers
    }

    public function is_translator() {
        return true;
        return ($this->me->istranslator || $this->me->isadmin) && $this->accepted_current_policy(); // All admins are translator
    }

    public function my_id() {
        return intval($this->me->id);
    }

    public function my_name() {
        return make_full_name($this->me);
    }

    public function user_full_name(int $uid) {
        $query = $this->db->select('first_name,last_name,family_name_first')->where('id',$uid)->get('user');
        $user = $query->row();
        if (!$user)
            throw new DataException($this->lang->line('illegal_user_id'));

        return make_full_name($user);
    }
    
    public function get_me() {
        return $this->me;
    }

    public function is_logged_in() {
        return $this->me->id > 0 && $this->accepted_current_policy();
    }

    public function is_logged_in_noaccept() {
        return $this->me->id > 0 && !$this->accepted_current_policy();
    }

    public function is_me(int $user_id) {
        return $user_id==$this->me->id;
    }

    // Returns true if login is successful, otherwise returns null. Sets $this->me
    public function verify_login(string $name, string $pw) {
        $pw_md5 = md5($this->config->item('pw_salt') . $pw);

        $query = $this->db->where('username',$name)->where('password',$pw_md5)->get('user');
        if ($this->me = $query->row())
            return true;

        $this->me = make_dummy_user();
        return false;
    }

    public function update_login_stat() {
        $this->me->last_login = time();
        $this->me->warning_sent = 0;
        $this->db->where('id',$this->me->id)->update('user',array('last_login'   => $this->me->last_login,
                                                                  'warning_sent' => $this->me->warning_sent));
    }

    public function check_admin() {
        if (!$this->mod_users->is_admin())
            throw new DataException($this->lang->line('must_be_admin'));
    }

    public function check_teacher() {
        if (!$this->mod_users->is_teacher())
            throw new DataException($this->lang->line('must_be_teacher'));
    }

    public function check_translator() {
        if (!$this->mod_users->is_translator())
            throw new DataException($this->lang->line('must_be_translator'));
    }

    public function check_logged_in() {
        if (!$this->mod_users->is_logged_in())
            throw new DataException($this->lang->line('must_be_logged_in'));
    }


    public function check_logged_in_local() {
        if (!$this->mod_users->is_logged_in())
            throw new DataException($this->lang->line('must_be_logged_in'));

        if (!empty($this->me->oauth2_login))
            throw new DataException($this->lang->line("must_not_be_{$user_info->oauth2_login}"));
    }

    public function check_logged_in_oauth2(string $authority) {
        if (!$this->mod_users->is_logged_in())
            throw new DataException($this->lang->line('must_be_logged_in'));

        if ($this->me->oauth2_login!==$authority)
            throw new DataException($this->lang->line("must_be_$authority"));
    }

    public function set_login_session() {
        $this->session->set_userdata('ol_user', $this->me->id);

        if (!empty($this->me->preflang) && $this->me->preflang!='none')
            $this->session->set_userdata('language', $this->me->preflang);

        // If user's preferred variant is 'main', set current variant to '' (Indicating main variant)
        // If user's preferred variant is '<something>', set current variant to '<something>'
        // If user's preferred variant is 'none', don't change current variant
        if (!empty($this->me->prefvariant) && $this->me->prefvariant!='none')
            $_SESSION['variant'] = $this->me->prefvariant=='main' ? '' : $this->me->prefvariant;
    }

    public function clear_login_session() {
        $this->session->unset_userdata('ol_user');
        $this->me = make_dummy_user();
    }

    public function get_all_users() {
        $query = $this->db->get('user');
        return $query->result();
    }

    public function get_all_users_part(int $limit, int $offset, string $orderby, string $sortorder) {
        $query = $this->db->order_by($orderby,$sortorder)->get('user',$limit,$offset);
        return $query->result();
    }

    public function count_users() {
        $query = $this->db->select('count(*) as count')->get('user');
        return $query->row()->count;
    }

    public function get_teachers() {
        $query = $this->db->where('`isteacher`=1 OR `isadmin`=1',null,false)->get('user');
        $teachers = $query->result();
        usort($teachers, 'teacher_cmp');
        return $teachers;
    }

    // $userid==-1 means create new user
    public function get_user_by_id(int $userid) {
        if ($userid===-1) {
            // Create new user
            $user = make_dummy_user();
            $user->id = null; // Indicates new user
            $user->created_time = time();
            $user->last_login = time()-10; // Assume that the user has logged in at least once to prevent expiry.
                                           // The -10 is used when listing users to indicate that this is a fake value.
            $user->preflang = 'none';
            $user->prefvariant = '';
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
            return $query->row();
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

    public function set_user(stdClass $user_info, string $pw=null) {
        if (!empty($pw))
            $user_info->password = md5($this->config->item('pw_salt') . $pw);

        if (is_null($user_info->id)) // Insert new user
            $query = $this->db->insert('user', $user_info);
        else // Update existing user
            $query = $this->db->where('id',$user_info->id)->update('user',$user_info);

        return $query;
    }

    
    public function delete_user(int $userid) {
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
    public function new_oauth2_user(string $authority, string $oauth2_user_id, string $first_name, string $last_name, bool $family_name_first, string $email=null) {
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

            if ($first_name!==$this->me->first_name || $last_name!==$this->me->last_name || $email!==$this->me->email) {
                $this->me->first_name = $first_name;
                $this->me->last_name = $last_name;
                $this->me->email = empty($email) ? null : $email;
                $query = $this->db->where('id',$this->me->id)->update('user',array('first_name' => $this->me->first_name,
                                                                                   'last_name'  => $this->me->last_name,
                                                                                   'email'      => $this->me->email));
            }
            return false;
        }
        else {
            // Create new user
            $this->me = make_dummy_user();
			$this->me->id = null; // Indicates new user
			$this->me->first_name = $first_name;
			$this->me->last_name = $last_name;
			$this->me->family_name_first = $family_name_first;
			$this->me->username = $username;
			$this->me->password = 'NONE';
			$this->me->email = empty($email) ? null : $email;
			$this->me->oauth2_login = $authority;
			$this->me->created_time = time();
			$this->me->last_login = time();
			$this->me->preflang = $this->language;
			$this->me->prefvariant = empty($_SESSION['variant']) ? 'none' :  $_SESSION['variant'];

            $query = $this->db->insert('user', $this->me);
      
            $this->me->id = $this->db->insert_id();

            return true;
        }
    }

    // Delete accounts older than $time seconds where the user has never logged in
    public function delete_new_inactive(int $time) {
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
    public function old_inactive(int $level, int $time) {
        $now = time();
        if ($level==0) {
            $query = $this->db->where('last_login <',$now-$time)->where('last_login >',0)
                ->get('user');
            $users = $query->result();
   
            foreach ($users as $u)
                $this->delete_user(intval($u->id));
        }
        else {
            $query = $this->db->where('last_login <',$now-$time)->where('last_login >',0)->where('warning_sent <',$level)
                ->get('user');
            $users = $query->result();
            $this->db->where('last_login <',$now-$time)->where('last_login >',0)->where('warning_sent <',$level)
                ->update('user',array('warning_sent'=>$level));
        }

        return $users;
    }

    public function generate_acceptance_code() {
        // Generate a random password acceptance code
        $this->me->acc_code = sprintf("%08x",mt_rand()).sprintf("%08x",mt_rand()).sprintf("%08x",mt_rand()).sprintf("%08x",mt_rand());
        $this->me->acc_code_time = time();
        $this->db->where('id',$this->me->id)->update('user',array('acc_code'      => $this->me->acc_code,
                                                                  'acc_code_time' => $this->me->acc_code_time));
        return $this->me->acc_code;
    }

    
    // Returns true if accept code is correct, otherwise returns null.
    // If $set_me is true, this function sets $this->me and updates the database.
    // $policy_lang is not used if $set_me==false
    public function verify_accept_code(string $acc_code, string $policy_lang, bool $set_me) {
        if ($this->me->acc_code==$acc_code && time()-$this->me->acc_code_time < self::ACCEPT_CODE_EXPIRY) {
            if ($set_me) {
                $this->me->accept_policy = time();
                $this->me->policy_lang = $policy_lang;
                $this->db->where('id',$this->me->id)->update('user',array('accept_policy' => $this->me->accept_policy,
                                                                          'policy_lang' => $this->me->policy_lang));
            }
            return true;
        }
        else
            return false;
    }

    public function accepted_current_policy() {
        return $this->me->accept_policy >= self::CURRENT_POLICY_DATE;
    }

    public function revoke_google_permissions() {
        // Revoke user permissions
        $url = "https://accounts.google.com/o/oauth2/revoke?token=" . $this->session->userdata('access_token');
        $options = array(
            'http' => array(
                'method'  => 'GET'
                )
            );
        $context  = stream_context_create($options);
        $result = @file_get_contents($url, false, $context);
            
        $items = explode(' ',$http_response_header[0]);
        return $items[1]; // $items[1] is the HTTP error code
    }
    
  }
