<?php

class Mod_classdir extends CI_Model {
    public function __construct() {
        parent::__construct();

        $this->config->load('ol');
        $this->load->database();
    }

    public function get_classes_for_dir(string $dir) {
        $dir = rtrim($dir, '/');

        $query = $this->db->select('id')->where('pathname',$dir)->get('exercisedir');

        $res = array();
        if ($query->num_rows()>0) {
            $row = $query->row();
            $pathid = $row->id;
            
            $query = $this->db->select('classid')->where('pathid',$pathid)->get('classexercise');
            foreach ($query->result() as $row)
                $res[] = $row->classid;
        }

        return $res;
    }
   
    public function update_classes_for_dir(string $dir, array $old_classes, array $new_classes) {
        $dir = rtrim($dir, '/');

        $query = $this->db->select('id')->where('pathname',$dir)->get('exercisedir');
        if ($query->num_rows()===0) {
            $query = $this->db->insert('exercisedir', array('pathname' => $dir));
            $pathid = $this->db->insert_id();
        }
        else {
            $row = $query->row();
            $pathid = $row->id;
        }
         

        // Insert new classes
        foreach ($new_classes as $newid) {
            if (in_array($newid, $old_classes))
                continue;

            $this->db->insert('classexercise', array('pathid' => $pathid, 'classid' => $newid));
        }

        // Remove old classes
        foreach ($old_classes as $oldid) {
            if (in_array($oldid, $new_classes))
                continue;

            $this->db->where(array('pathid' => $pathid, 'classid' => $oldid))->delete('classexercise');
        }
    }

    public function rmdir(string $dir) {
        $dir = rtrim($dir, '/');

        $query = $this->db->select('id')->where('pathname',$dir)->get('exercisedir');
        if ($query->num_rows()>0) {
            $row = $query->row();
            $pathid = $row->id;
            
            $this->db->where('pathid',$pathid)->delete('classexercise');
            $this->db->where('id',$pathid)->delete('exercisedir');
        }
    }

    // Checks if member of the classes in $classes may access the path in $path
    public function may_access(string $path, array $classes) {
        if (empty($path))
            return true;

        $classes[] = 0; // Everybody is in this class
        
        $this->load->helper('varset');
        $components = explode('/', $path);
        $checking = '';
        foreach ($components as $comp) {
            $checking = composedir($checking, $comp);

            if (is_dir("quizzes/$checking")) {
                $classes_for_dir = $this->get_classes_for_dir($checking);
                $may_see = count(array_intersect($classes_for_dir, $classes)) > 0;
                if (!$may_see)
                    return false;
            }
        }
        return true;
    }
        

    public function filter_directories(array $directories, string $relativedir, array $classes) {
        $classes[] = 0; // Everybody is in this class

        $this->load->helper('varset');
        $good_directories = array();
        foreach ($directories as $dir) {
            $classes_for_dir = $this->get_classes_for_dir(composedir($relativedir, $dir));
            $may_see = count(array_intersect($classes_for_dir, $classes)) > 0;
            if ($may_see)
                $good_directories[] = $dir;
        }

        return $good_directories;
    }
  }