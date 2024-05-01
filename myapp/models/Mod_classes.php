<?php

class Mod_classes extends CI_Model {
    public function __construct() {
        parent::__construct();

        $this->config->load('ol');
        $this->load->database();
    }


    /// Builds an array of all the classes index by class id.
    public function get_all_classes() {
        $query = $this->db->select('*, class.id as clid, user.id as uid, class.password as clpass')->from('class')->join('user','ownerid=user.id', 'left')->get();

        $all_classes = array();
        foreach ($query->result() as $row)
            $all_classes[$row->clid] = $row;

        return $all_classes;
    }

    // $classid==-1 means create new class
    public function get_class_by_id(int $classid) {
        if ($classid===-1) {
            // Create new class
            $cl = new stdClass();
			$cl->id = null; // Indicates new class
			$cl->classname = '';
			$cl->password = '';
			$cl->enrol_before = '';
            $cl->ownerid = $this->mod_users->my_id();
        }
        else {
            $query = $this->db->where('id',$classid)->get('class');
            $cl = $query->row();
            if (!$cl)
                throw new DataException($this->lang->line('illegal_class_id'));
        }
        return $cl;
    }

    public function get_classes_by_ids(array $classids) {
        if (empty($classids))
            return array();

        $query = $this->db->where_in('id',$classids)->get('class');
        return $query->result();
    }

    public function get_classes_owned($all=true) {
        if ($all && $this->mod_users->is_admin())
            $query = $this->db->select('id')->get('class');
        else
            $query = $this->db->select('id')->where('ownerid',$this->mod_users->my_id())->get('class');

        $res = array();
        foreach ($query->result() as $row)
            $res[] = $row->id;

        return $res;
    }

    public function get_named_classes_owned($all=true) {
        if ($all && $this->mod_users->is_admin()) {
            $query = $this->db->select('id,classname')->get('class');
            //echo 'Sample Result: ' . var_dump($query->result());
            return $query->result();
        }
        else {
            // Get classes owned by current user
            $owner_query = $this->db->select('id,classname')->where('ownerid',$this->mod_users->my_id())->get('class');
            $owned_classes = $owner_query->result();

            // Get classes where current user is a grader
            $grader_query = $this->db->select('classid')->from('grader')->where('graderid',$this->mod_users->my_id())->get();
            $grader_classes = array();
			$grader_classes_ids = array();

            // For each class that the user is a grader for, get the class object and add it to the array of grader_classes
            foreach ($grader_query->result() as $row) {
                // get the class object from the class ID
                $class_obj = $this->db->select('id,classname')->where('id',$row->classid)->get('class');

				if(!in_array($row->classid, $grader_classes_ids)) {
					// append the id to grader_classes_ids
					$grader_classes_ids[] = $row->classid;

                	// append the class object to the array of grader_classes
                  if (count($class_obj->result()) > 0) {
                	   $grader_classes[] = $class_obj->result()[0];
                  }
				}
            }
			//echo '<br><br><br>---------------------------------------------------<br>';
			//echo 'Grader Classes: ' . var_dump($grader_classes) . '<br>';
			//echo 'Owned Classes: ' . var_dump($owned_classes) . '<br>';


            // get the classes where the user is either a grader or a owner and remove duplicates
            $graded_or_owned_classes = array_merge($owned_classes, $grader_classes); // array_unique() only works with strings

            return $graded_or_owned_classes;
        }
    }

    // Get clases the ustand is enrolled in
    public function get_named_classes_enrolled($all=true) {
      $query = $this->db
      ->from('class c')
      //
      ->select('c.classname, c.id',false)
      ->join('userclass uc','c.id=uc.classid')
      ->where('uc.userid',$this->mod_users->my_id())->get();

      return $query->result();
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

    public function delete_class(int $classid) {
        $this->db->where('id', $classid)->delete('class');
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
