<?php

class Mod_classes extends CI_Model {
    public function __construct() {
        parent::__construct();

        $this->config->load('ol');
        $this->load->database();
    }

    private function site_condition(string $variant=null) {
        return $variant ? "(site is null OR site='$variant')" : 'site is null';
    }

    /// Builds an array of all the classes index by class id.
    public function get_all_classes(string $variant=null) {
        $query = $this->db
            ->select('*, class.id as clid, user.id as uid, class.password as clpass')
            ->from('class')
            ->where($this->site_condition($variant))
            ->join('user','ownerid=user.id', 'left')
            ->get();

        $all_classes = array();
        foreach ($query->result() as $row)
            $all_classes[$row->clid] = $row;

        return $all_classes;
    }

    // $classid==-1 means create new class
    public function get_class_by_id(int $classid, string $variant=null) {
        if ($classid===-1) {
            // Create new class
            $cl = new stdClass();
			$cl->id = null; // Indicates new class
			$cl->classname = '';
			$cl->password = '';
			$cl->enrol_before = '';
            $cl->ownerid = $this->mod_users->my_id();
            $cl->site = $variant;
        }
        else {
            $query = $this->db->where('id',$classid)->where($this->site_condition($variant))->get('class');
            $cl = $query->row();
            if (!$cl)
                throw new DataException($this->lang->line('illegal_class_id'));
        }
        return $cl;
    }

    public function get_classes_by_ids(array $classids, string $variant=null) {
        if (empty($classids))
            return array();
        
        $query = $this->db->where_in('id',$classids)->where($this->site_condition($variant))->get('class');
        return $query->result();
    }

    public function get_classes_owned(string $variant=null) {
        if ($this->mod_users->is_admin())
            $query = $this->db->select('id')->where($this->site_condition($variant))->get('class');
        else
            $query = $this->db->select('id')->where('ownerid',$this->mod_users->my_id())->where($this->site_condition($variant))->get('class');

        $res = array();
        foreach ($query->result() as $row)
            $res[] = $row->id;

        return $res;
    }
     
    public function get_named_classes_owned($all=true, string $variant=null) {
        if ($all && $this->mod_users->is_admin())
            $query = $this->db->select('id,classname')->where($this->site_condition($variant))->get('class');
        else
            $query = $this->db->select('id,classname')->where('ownerid',$this->mod_users->my_id())->where($this->site_condition($variant))->get('class');

        return $query->result();
    }
     
    public function set_class(stdClass $class_info, string $variant=null) {
        if (empty($class_info->password))
            $class_info->password = null;
        if (empty($class_info->enrol_before))
            $class_info->enrol_before = null;

        if (is_null($class_info->id)) { // Insert new class
            $class_info->site = $variant;
            $query = $this->db->insert('class', $class_info);
        }
        else // Update existing class
            $query = $this->db->where('id',$class_info->id)->update('class',$class_info);

        return $query;
    }

    public function delete_class(int $classid, string $variant=null) {
        $this->db->where('id', $classid)->where($this->site_condition($variant))->delete('class');
        if ($this->db->affected_rows()==0)
            throw new DataException($this->lang->line('illegal_class_id'));

        $this->db->where('classid', $classid)->delete('userclass');
        $this->db->where('classid', $classid)->delete('classexercise');
    }

    public function chown_class(int $classid, int $userid) {
        $query = $this->db->where("`id`=$userid AND (`isteacher`=1 OR `isadmin`=1)",null,false)->get('user');
        if ($row = $query->row())
            // User exists and is a teacher
            $query = $this->db->where('id',$classid)->update('class',array('ownerid' => $userid));
        else
            throw new DataException($this->lang->line('not_teacher'));
    }

  }