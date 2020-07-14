<!--
This page is the home page for exams.
It has a list of all the created exams and
professors can edit exams or choose to create
a new exam. In each case a the professor is
redirected to the appropriate page.
-->

<style>
/*
Styling for the page.
This is used to fine tune individual CSS
style characteristics for individual elements
on the page.
*/
.editButton{
  justify-content: center;
  cursor: pointer;

  background-color: white;
  color: #007bff;

  padding: 8px;
  border-radius: 8px;
}
</style>


<h1><?= sprintf($this->lang->line('number_of_exams'), $exam_count) ?></h1>
<h2><?= sprintf($this->lang->line('exams_per_page'), $exams_per_page) ?></h2>
<nav>
    <ul class="pagination">
        <?php for ($p=0; $p<$page_count; ++$p): ?>
            <li class="<?= $p==$offset ? 'active' : '' ?> page-item">
                <a class="page-link" href="<?= site_url("exams?offset=$p&orderby=$orderby&$sortorder") ?>">
                    <?= $p+1 ?>
                </a>
            </li>
        <?php endfor; ?>
    </ul>
</nav>

<?php
    function make_exam_header($me, $label, $field, $sortorder, $orderby){
      if ($orderby===$field) {
          $link_sortorder = $sortorder == 'desc' ? 'asc' : 'desc';
          $arrow = ' <span class="fas fa-caret-' . ($sortorder=='desc' ? 'down' : 'up') . '" aria-hidden="true">';
      }
      else {
        $link_sortorder = 'asc';

        // FIX ARROW
        // NOT SORTING
        $arrow = '';
      }

      return '<th style="white-space"><a href="' . site_url("exams?offset=0&orderby=$field&$link_sortorder") . '">' . $me->lang->line($label) . $arrow . "</a></th>\n";
    }
?>


<div class="table-responsive">
<table class="type2 table table-striped">
    <tr>
        <?= make_exam_header($this, 'exam_name', 'examname', $sortorder, $orderby) ?>
        <?= make_exam_header($this, 'owner', 'ownerid', $sortorder, $orderby) ?>
        <th><?= $this->lang->line('user_operations') ?></th>
    </tr>
    <?php foreach ($allexams as $exam): ?>
        <tr>
            <td class="leftalign"><?= $exam->exam_name ?></td>
            <td class="leftalign"><?= $this->mod_users->user_full_name($exam->ownerid) ?></td>
            <td class="leftalign">
                <a class="badge badge-primary" href="/exams/edit_exam?exam=<?= $exam->exam_name ?>"><?= $this->lang->line('edit_exam') ?></a>
                <a class="badge badge-primary" href="/exams/active_exams"><?= $this->lang->line('take_exam') ?></a>
                <a class="badge badge-danger" href="#" onclick="dltexam('<?= $exam->exam_name ?>');"><?= $this->lang->line('delete_exam') ?></a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
</div>


<!--
This button redirects to the exam creation page.
-->
<div>
  <a class="btn btn-primary" href="/exams/new_exam"><?= $this->lang->line('create_new_exam_button') ?></a>
</div>


<!-- Take Exam Modal -->
<div class="modal fade" id="take-exam-dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header justify-content-between">
        <div><h4 class="modal-title"><?= $this->lang->line('take_exam') ?></h4></div>
        <div><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button></div>
      </div>
      <div class="modal-body">
        <div class="alert alert-danger" id="take-error" role="alert">
          <span class="fas fa-exclamation-circle" aria-hidden="true"></span>
          <span id="take-error-text"></span>
        </div>

        <p>
          Take exam dialog.
        </p>

        <form id="take-form" action="<?= site_url('exams/take_exam') ?>" method="post">
          <p>Start date</p>
          <input type="date" name="start_date">
          <br>
          <p>Start time</p>
          <input type="time" name="start_time">
          <br>
          <br>
          <p>End date</p>
          <input type="date" name="end_date">
          <br>
          <p>End time</p>
          <br>
          <input type="time" name="end_time">
          <br>
          <br>
          <p>Duration</p>
        </form>

      </div>
      <div class="modal-footer">
        <button type="button" id="take-dialog-ok" class="btn btn-primary"><?= $this->lang->line('OK_button') ?></button>
        <button type="button" class="btn btn-outline-dark" data-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>


<!-- Delete Exam Modal -->
<div class="modal fade" id="delete-exam-dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header justify-content-between">
        <div><h4 class="modal-title"><?= $this->lang->line('delete_exam') ?></h4></div>
        <div><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button></div>
      </div>
      <div class="modal-body">
        <div class="alert alert-danger" id="delete-error" role="alert">
          <span class="fas fa-exclamation-circle" aria-hidden="true"></span>
          <span id="delete-error-text"></span>
        </div>

        <p>
          The following exam will be deleted:
        </p>

        <p id="delete-exam">
        </p>


        <form id="delete-form" action="<?= site_url('exams/delete_exam') ?>" method="post">
          <input type="hidden" name="exname" id="delete-examname">
        </form>

      </div>
      <div class="modal-footer">
        <button type="button" id="delete-dialog-ok" class="btn btn-primary">Delete</button>
        <button type="button" class="btn btn-outline-dark" data-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>


<script>
  function dltexam(examname) {
    $('#delete-examname').attr('value', examname);
    document.getElementById('delete-exam').innerHTML = examname;
    $('#delete-error').hide();
    $("#delete-exam-dialog").modal("show");
  }

  $(function() {
    $('#delete-dialog-ok').click(function() {
      $('#delete-form').submit();
    })
  })
</script>
