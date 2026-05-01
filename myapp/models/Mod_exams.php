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
    $query = $this->db->get_where('exam', array('id' => $id));
    return $query->row();
  }

  public function get_all_exams(){
      $query = $this->db->get_where('exam', array('archived' => 0));
      return $query->result();
  }

  public function get_all_exams_part(int $limit, int $offset, string $orderby, string $sortorder){
      $query = $this->db->order_by($orderby, $sortorder)->get_where('exam', array('archived' => 0), $limit, $offset);
      return $query->result();
  }

  public function count_exams(){
      $query = $this->db->select('count(*) as count')->get('exam');
      return $query->row()->count;
  }

  public function get_template_id(int $quizid): ?int {
    $query = $this->db->get_where('sta_quiz', array('id' => $quizid));
    $row = $query->row();

    if (is_null($row)) {
      return null;
    }

    return (int)$row->templid;
  }

  public function get_active_exam(int $id) {
    $query = $this->db->get_where('exam_active', array('id' => $id));
    return $query->row();
  }

  public function exam_results_uses_attempt_id(): bool {
    return $this->db->field_exists('attempt_id', 'exam_results');
  }

  public function get_completed_attempt_exercises(int $user_id, int $exam_attempt_id) {
      $completed = [];
      $exam_attempt = $this->get_exam_attempt_by_id($exam_attempt_id);

      if (is_null($exam_attempt) || (int)$exam_attempt->userid !== $user_id) {
          return $completed;
      }

      $this->db->select('exam_results.quiztemplid');
      $this->db->from('exam_results');

      if ($this->exam_results_uses_attempt_id()) {
          // New schema: exam_results links directly to the specific exam attempt.
          $this->db->where('exam_results.attempt_id', $exam_attempt_id);
      }
      else {
          // Legacy schema: exam_results links to the active exam and user instead.
          $this->db->where('exam_results.userid', $user_id);
          $this->db->where('exam_results.activeexamid', (int)$exam_attempt->activeexamid);
      }

      $results = $this->db->get()->result();

      foreach ($results as $row) {
          $template_id = $row->quiztemplid;

          // retrieve template
          $template = $this->db
              ->get_where('sta_quiztemplate', ['id' => $template_id])
              ->row();

          if (!$template) {
              continue; // skip missing templates
          }

          // convert full quiz path to relative path
          $path = str_replace(
              realpath(__DIR__ . "/../../quizzes") . '/',
              '',
              $template->pathname
          );

          $completed[] = $path;
      }

      return $completed;
  }


  /******************************* bol_exam_attempt ************************************/
  public function get_latest_attempt(int $user_id, int $active_exam_id): ?stdClass {
    return $this->db
      ->where('userid', $user_id)
      ->where('activeexamid', $active_exam_id)
      ->order_by('attempt_count', 'DESC')
      ->limit(1)
      ->get('exam_attempt')
      ->row();
  }

  public function user_exam_attempt_in_progress(int $user_id, int $active_exam_id): bool {
    $latest_exam_attempt = $this->get_latest_attempt($user_id, $active_exam_id);
    if (
      is_null($latest_exam_attempt)
      || (bool)$latest_exam_attempt->is_done
    ) {
      return false;
    }

    return true;
  }

  public function get_exam_attempt_is_done(int $user_id, int $active_exam_id, int $attempt_count = 1): bool {
    $this->db->where([
      'userid' => $user_id,
      'activeexamid' => $active_exam_id,
      'attempt_count' => $attempt_count
    ]);
    $this->db->limit(1);
    $query = $this->db->get('exam_attempt');
    $row = $query->row();

    return !is_null($row) && (bool)$row->is_done;
  }

  public function user_has_completed_all_attempts_available(int $user_id, int $active_exam_id): bool {
    $active_exam = $this->get_active_exam($active_exam_id);

    if (is_null($active_exam) || is_null($active_exam->maximum_attempts)) {
      return false;
    }

    $this->db->where([
      'userid' => $user_id,
      'activeexamid' => $active_exam_id,
      'is_done' => true
    ]);
    $attempts_completed = $this->db->count_all_results('exam_attempt');
    
    return $attempts_completed >= (int)$active_exam->maximum_attempts;
  }

  public function get_exam_attempt_by_id(int $exam_attempt_id): ?stdClass {
    return $this->db
      ->where('id', $exam_attempt_id)
      ->limit(1)
      ->get('exam_attempt')
      ->row();
  }

  public function set_exam_attempt_is_done(int $exam_attempt_id): int {
    $this->db->where(['id' => $exam_attempt_id]);
    $this->db->update(
      'exam_attempt',
      ['is_done' => true]
    );
    return $this->db->affected_rows();
  }
}

?>
