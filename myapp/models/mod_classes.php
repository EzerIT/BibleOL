<?php

class Mod_classes extends CI_Model {
    public function __construct() {
        parent::__construct();

        $this->config->load('ol');
        $this->load->database();
    }


    /// Builds an array of all the classes index by class id.
    public function get_all_classes() {
        $query = $this->db->get('class');

        $all_classes = array();
        foreach ($query->result() as $row)
            $all_classes[$row->id] = $row;

        return $all_classes;
    }

    // $classid==-1 means create new class
    public function get_class_by_id(integer $classid) {
        if ($classid===-1) {
            // Create new class
            $cl = new stdClass();
			$cl->id = null; // Indicates new class
			$cl->classname = '';
			$cl->password = '';
			$cl->enrol_before = '';
        }
        else {
            $query = $this->db->where('id',$classid)->get('class');
            $cl = $query->row();
            if (!$cl)
                throw new DataException($this->lang->line('illegal_class_id'));
        }
        return $cl;
    }

    public function set_class(stdClass $class_info) {
        if (empty($class_info->password))
            $class_info->password = null;
        if (empty($class_info->enrol_before))
            $class_info->enrol_before = null;

        if (is_null($class_info->id)) // Insert new class
            $query = $this->db->insert('class', $class_info);
        else // Update existing class
            $query = $this->db->where('id',$class_info->id)->update('class',$class_info);

        return $query;
    }

    public function delete_class(integer $classid) {
        $this->db->where('id', $classid)->delete('class');
        if ($this->db->affected_rows()==0)
            throw new DataException($this->lang->line('illegal_class_id'));

        $this->db->where('classid', $classid)->delete('userclass');
        $this->db->where('classid', $classid)->delete('classexercise');
    }


  }