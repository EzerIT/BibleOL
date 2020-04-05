<?php

// Creates a dummy exam structure with id=0
function make_dummy_exam() {
    $e = new stdClass;
    $e->id = 0;
    $e->exam_name = '';
    $e->ownerid = 0;
    $e->pathname = '';
    $e->examcode = '';
    $e->examcodehash = '';

    return $e;
}


class Mod_exams extends CI_Model{
    private $exam;

    public function __construct(){
        parent::__construct();

        $this->config->load('ol');
        $this->load->database();

        $exam_id = intval($this->session->userdata('ol_exam'));
        $query = $this->db->where('id', $exam_id)->get('exam');
        $this->exam = $query->row();

        if (is_null($this->exam)) {
            $this->exam = make_dummy_exam();
        }
    }

    public function get_id(){
        return intval($this->exam->id);
    }

    public function get_name(){
        return $this->exam->exam_name;
    }

    public function get_exam(){
        return $this->exam;
    }

    public function get_all_exams(){
        $query = $this->db->get('exam');
        return $query->result();
    }

    public function get_all_exams_part(int $limit, int $offset, string $orderby, string $sortorder){
        $query = $this->db->order_by($orderby, $sortorder)->get('exam', $limit, $offset);
        return $query->result();
    }

    public function count_exams(){
        $query = $this->db->select('count(*) as count')->get('exam');
        return $query->row()->count;
    }
}

?>
