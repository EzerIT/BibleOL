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
                  <a class="badge badge-primary" href="/exams/edit_exam?exam=<?= $exam->id ?>"><?= $this->lang->line('edit_exam') ?></a>
                  <a class="badge badge-primary" href="#" onclick="cr_exam_inst('<?= $exam->exam_name ?>', <?= $exam->id ?>);"><?= $this->lang->line('create_exam_instance') ?></a>
                  <a class="badge badge-danger" href="#" onclick="dltexam('<?= $exam->id ?>', '<?= $exam->exam_name ?>');"><?= $this->lang->line('delete_exam') ?></a>
              </td>
          </tr>
      <?php endforeach; ?>
  </table>
</div>

<!--
This button redirects to the exam creation page.
-->
<div>
  <a class="btn btn-primary" href="<?= site_url('/file_manager') ?>"><?= $this->lang->line('create_new_exam_button') ?></a>
</div>


<!-- Take Exam Modal -->
<div class="modal fade" id="create-exam-instance-dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header justify-content-between">
        <div><h4 class="modal-title"><?= $this->lang->line('create_exam_instance') ?></h4></div>
        <div><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button></div>
      </div>
      <div class="modal-body">
        <div class="alert alert-danger" id="create-instance-error" role="alert">
          <span class="fas fa-exclamation-circle" aria-hidden="true"></span>
          <p id="create-instance-error-text"></p>
        </div>

        <form id="create-instance-form" action="<?= site_url('exams/create_exam_instance') ?>" method="get">
          <input type="hidden" name="exname" id="create-instance-examname">
          <input type="hidden" name="exid" id="create-instance-examid">
          <?= $this->lang->line('class') ?>: <select id="class_select" name="class_select">
            <?php foreach ($n_o_c as $owned_class): ?>
              <option value="<?= $owned_class->id ?>"><?= $owned_class->classname ?></option>
            <?php endforeach; ?>
          </select>
          <br>
          <br>
          <?= $this->lang->line('instance_name') ?>: <input type="text" id="instance_name" name="instance_name" required>
          <br>
          <br>
          <?= $this->lang->line('start_date') ?>: <input type="date" id="date1" name="start_date" required>
          <br>
          <?= $this->lang->line('start_time') ?>: <input type="time" id="time1" name="start_time" required>
          <br>
          <br>
          <?= $this->lang->line('end_date') ?>: <input type="date" id="date2" name="end_date" required>
          <br>
          <?= $this->lang->line('end_time') ?>: <input type="time" id="time2" name="end_time" required>
          <br>
          <br>
          <?= $this->lang->line('duration') ?>: <input type="number" id="duration" name="duration" min="1" step="1" required>
        </form>

      </div>
      <div class="modal-footer">
        <button type="button" id="create-instance-dialog-ok" class="btn btn-primary"><?= $this->lang->line('OK_button') ?></button>
        <button type="button" class="btn btn-outline-dark" data-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>


<script>
  function cr_exam_inst(examname, examid) {
    $('#create-instance-error').hide();
    $('#create-instance-examname').attr('value', examname);
    $('#create-instance-examid').attr('value', examid);
    $('#instance_name').attr('value', examname);
    let tomo = new Date();
    tomo.setDate(tomo.getDate() + 1);
    document.getElementById('date1').valueAsDate = tomo;
    document.getElementById('time1').defaultValue = "18:00";
    document.getElementById('date2').valueAsDate = tomo;
    document.getElementById('time2').defaultValue = "20:00";
    document.getElementById('duration').defaultValue = 90;
    document.getElementById('duration').min = 0;
    $("#create-exam-instance-dialog").modal("show");
  }

  $(function() {

    $('#create-instance-dialog-ok').click(function() {
      let duration_value = document.getElementById('duration').value;
      let class_selected = document.getElementById('class_select').value;
      let cr_inst_err_txt = document.getElementById('create-instance-error-text');
      cr_inst_err_txt.innerText = "Creating instance...";
      $('#create-instance-error').show();
      if (!duration_value || duration_value < 1){
        cr_inst_err_txt.innerText = "Invalid duration";
      }
      else if (!class_selected || class_selected <= 0) {
        cr_inst_err_txt.innerText = "Invalid class";
      }
      else if (document.getElementById('instance_name').value.length == 0){
        cr_inst_err_txt.innerText = "Invalid Name";
      }
      else{
        $('#create-instance-form').submit();
      }
    })
  })
</script>


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
          <input type="hidden" name="exid" id="delete-examid">
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
  function dltexam(examid, examname) {
    $('#delete-examid').attr('value', examid);
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
