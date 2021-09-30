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

    public function get_exam_by_id(int $id) {
      $query = $this->db->get_where('bol_exam', array('id' => $id));
      return $query->row();
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

    public function get_template_id(int $quizid){
      $query = $this->db->get_where('bol_sta_quiz', array('id' => $quizid));
      return $query->row()->templid;
    }

    public function get_active_exam(int $id) {
      $query = $this->db->get_where('bol_exam_active', array('id' => $id));
      return $query->row();
    }

    public function get_completed_exam_exercises(int $user_id, int $active_exam_id) {
      $completed = array();
      $query = $this->db->get_where('bol_exam_results', array('userid' => $user_id, 'activeexamid' => $active_exam_id));
      $res = $query->result();
      foreach ($res as $row) {
        $template_id = $row->quiztemplid;
        $query2 = $this->db->get_where('bol_sta_quiztemplate', array('id' => $template_id));
        $path = str_replace('/var/www/BibleOL/quizzes/', '', $query2->row()->pathname);
        array_push($completed, $path);
      }

      return $completed;
    }
}

?>
