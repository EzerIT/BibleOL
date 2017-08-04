<?php

/// This class manages the relationship between users and classes.
class Mod_userclass extends CI_Model {
    public function __construct() {
        parent::__construct();

        // No need to load database here; it has been loaded by mod_users which is always loaded together with this class
    }

    public function get_users_in_class(integer $classid) {
        $query = $this->db->select('userid')->where('classid',$classid)->get('userclass');
        $res = array();
        foreach ($query->result() as $row)
            $res[] = $row->userid;

        return $res;
    }

    public function get_named_users_in_class(integer $classid) {
        $query = $this->db
            ->select("userid,IF(family_name_first,CONCAT(last_name,first_name),CONCAT(first_name,' ',last_name)) name")
            ->join('user','user.id=userclass.userid')
            ->where('classid',$classid)
            ->get('userclass');

        return $query->result();
    }

    public function update_users_in_class(integer $classid, array $old_userids, array $new_userids) {
        // Insert new users
        foreach ($new_userids as $newid) {
            if (in_array($newid, $old_userids))
                continue;

            $this->enroll_user_in_class($newid, $classid);
        }

        // Remove old users
        foreach ($old_userids as $oldid) {
            if (in_array($oldid, $new_userids))
                continue;

            $this->unenroll_user_from_class($oldid, $classid);
        }
    }

    public function get_classes_for_user(integer $userid) {
        $query = $this->db->select('classid')->where('userid',$userid)->get('userclass');
        $res = array();
        foreach ($query->result() as $row)
            $res[] = $row->classid;

        return $res;
    }
     
    public function update_classes_for_user(integer $userid, array $old_classes, array $new_classes, array $owned_classes) {
        // Insert new classes
        foreach ($new_classes as $newid) {
            if (in_array($newid, $old_classes) || !in_array($newid, $owned_classes))
                continue;

            $this->enroll_user_in_class($userid, $newid);
        }

        // Remove old classes
        foreach ($old_classes as $oldid) {
            if (in_array($oldid, $new_classes) || !in_array($oldid, $owned_classes))
                continue;

            $this->unenroll_user_from_class($userid, $oldid);
        }
    }

    public function enroll_user_in_class($userid, $classid) { // Warning: There have been problems with adding type checks here
        $this->db->insert('userclass', array('userid' => $userid, 'classid' => $classid));
    }

    public function unenroll_user_from_class($userid, $classid) { // Warning: There have been problems with adding type checks here
        $this->db->where(array('userid' => $userid, 'classid' => $classid))->delete('userclass');
    }
  }
