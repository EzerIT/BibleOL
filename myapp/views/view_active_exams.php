<ul class="nav nav-tabs" id="myTab" role="tablist">
  <li class="nav-item">
    <a class="nav-link active" id="active-tab" data-toggle="tab" href="#active_exams" role="tab" aria-controls="home" aria-selected="true">
      Active
    </a>
  </li>
  <li class="nav-item">
    <a class="nav-link" id="future-tab" data-toggle="tab" href="#future_exams" role="tab" aria-controls="profile" aria-selected="false">
      Future
    </a>
  </li>
  <!--
  <li class="nav-item">
    <a class="nav-link" id="past-tab" data-toggle="tab" href="#past_exams" role="tab" aria-controls="contact" aria-selected="false">
      Past
    </a>
  </li>
-->
</ul>

<div class="tab-content" id="myTabContent">
  <div class="tab-pane fade show active" id="active_exams" role="tabpanel" aria-labelledby="home-tab">
    <div class="table-responsive">
      <table class="type2 table table-striped">
        <tr>
          <th><?= $this->lang->line('class_name') ?></th>
          <th><?= $this->lang->line('instructor') ?></th>
          <th><?= $this->lang->line('exam_name') ?></th>
          <th><?= $this->lang->line('start_time') ?></th>
          <th><?= $this->lang->line('end_time') ?></th>
          <th><?= $this->lang->line('duration') ?></th>
          <th><?= $this->lang->line('user_operations') ?></th>
        </tr>
        <?php foreach ($active_exams_list as $exam): ?>
          <tr>
            <td class="leftalign"><?= $class_names[$exam->class_id] ?></td>
            <td class="leftalign"><?= $instructors[$exam->class_id] ?></td>
            <td class="leftalign"><?= $exam->instance_name ?></td>
            <td class="leftalign time"><?= $exam->exam_start_time ?></td>
            <td class="leftalign time"><?= $exam->exam_end_time ?></td>
            <td class="leftalign"><?= $exam->exam_length ?></td>
            <td class="leftalign">
              <a class="badge badge-primary" href="/exams/take_exam?exam=<?= $exam->id ?>">Take Exam</a>
              <?php if ($this->mod_users->is_teacher()): ?>
                <a class="badge badge-danger" href="#" onclick="dltexam(<?= $exam->id ?>, '<?= $exam->instance_name ?>');"><?= $this->lang->line('delete_exam_instance') ?></a>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </table>
    </div>
  </div>

  <div class="tab-pane fade" id="future_exams" role="tabpanel" aria-labelledby="profile-tab">
    <div class="table-responsive">
      <table class="type2 table table-striped">
        <tr>
          <th><?= $this->lang->line('class_name') ?></th>
          <th><?= $this->lang->line('instructor') ?></th>
          <th><?= $this->lang->line('exam_name') ?></th>
          <th><?= $this->lang->line('start_time') ?></th>
          <th><?= $this->lang->line('end_time') ?></th>
          <th><?= $this->lang->line('duration') ?></th>
          <th><?= $this->lang->line('user_operations') ?></th>
        </tr>
        <?php foreach ($future_exams_list as $exam): ?>
          <tr>
            <td class="leftalign"><?= $class_names[$exam->class_id] ?></td>
            <td class="leftalign"><?= $instructors[$exam->class_id] ?></td>
            <td class="leftalign"><?= $exam->instance_name ?></td>
            <td class="leftalign time"><?= $exam->exam_start_time ?></td>
            <td class="leftalign time"><?= $exam->exam_end_time ?></td>
            <td class="leftalign"><?= $exam->exam_length ?></td>
            <td class="leftalign">
              <?php if ($this->mod_users->is_teacher()): ?>
                <a class="badge badge-danger" href="#" onclick="dltexam(<?= $exam->id ?>, '<?= $exam->instance_name ?>');"><?= $this->lang->line('delete_exam_instance') ?></a>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </table>
    </div>
  </div>

  <!-- <div class="tab-pane fade" id="past_exams" role="tabpanel" aria-labelledby="contact-tab">
    <div class="table-responsive">
      <table class="type2 table table-striped">
        <tr>
          <th><?= $this->lang->line('class_name') ?></th>
          <th><?= $this->lang->line('exam_name') ?></th>
          <th><?= $this->lang->line('start_time') ?></th>
          <th><?= $this->lang->line('duration') ?></th>
          <th><?= $this->lang->line('user_operations') ?></th>
        </tr>
        <?php foreach ($past_exams_list as $exam): ?>
          <tr>
            <td class="leftalign"><?= $exam->class_id ?></td>
            <td class="leftalign"><?= $exam->instance_name ?></td>
            <td class="leftalign"><?= date("m-d-Y H:i", $exam->exam_end_time) ?></td>
            <td class="leftalign"><?= $exam->exam_length ?></td>
            <td class="leftalign">
              <a class="badge badge-primary" href="#">Hi</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </table>
    </div>
  </div> -->
</div>



<!-- Delete Exam Instance Modal -->
<div class="modal fade" id="delete-exam-instance-dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header justify-content-between">
        <div><h4 class="modal-title"><?= $this->lang->line('delete_exam_instance') ?></h4></div>
        <div><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button></div>
      </div>
      <div class="modal-body">
        <div class="alert alert-danger" id="delete-error" role="alert">
          <span class="fas fa-exclamation-circle" aria-hidden="true"></span>
          <span id="delete-error-text"></span>
        </div>

        <p>
          The following exam instance will be deleted:
        </p>

        <p id="delete-exam-instance">
        </p>


        <form id="delete-form" action="<?= site_url('exams/delete_exam_instance') ?>" method="post">
          <input type="hidden" name="exid" id="delete-exid">
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
  $(".time").each(function () {
    this.innerText = new Date((this.innerText) * 1000).toString().split(" (")[0];
  })

  function dltexam(examid, examname) {
    $('#delete-exid').attr('value', examid);
    document.getElementById('delete-exam-instance').innerHTML = examname;
    $('#delete-error').hide();
    $("#delete-exam-instance-dialog").modal("show");
  }

  $(function() {
    $('#delete-dialog-ok').click(function() {
      $('#delete-form').submit();
    })
  })
</script>
