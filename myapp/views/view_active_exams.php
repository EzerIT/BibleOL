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
  <li class="nav-item">
    <a class="nav-link" id="past-tab" data-toggle="tab" href="#past_exams" role="tab" aria-controls="contact" aria-selected="false">
      Past
    </a>
  </li>
</ul>



<div class="tab-content" id="myTabContent">
  <div class="tab-pane fade show active" id="active_exams" role="tabpanel" aria-labelledby="home-tab">
    <div class="table-responsive">
      <table class="type2 table table-striped">
        <tr>
          <th><?= $this->lang->line('class_name') ?></th>
          <th><?= $this->lang->line('exam_name') ?></th>
          <th><?= $this->lang->line('end_date') ?></th>
          <th><?= $this->lang->line('end_time') ?></th>
          <th><?= $this->lang->line('duration') ?></th>
          <th><?= $this->lang->line('user_operations') ?></th>
        </tr>
        <?php foreach ($active_exams_list as $exam): ?>
          <tr>
            <td class="leftalign"><?= $exam->class_id ?></td>
            <td class="leftalign"><?= $exam->exam_name ?></td>
            <td class="leftalign"><?= $exam->exam_end_date ?></td>
            <td class="leftalign"><?= $exam->exam_end_time ?></td>
            <td class="leftalign"><?= $exam->exam_length ?></td>
            <td class="leftalign">
              <a class="badge badge-primary" href="#"></a>
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
          <th><?= $this->lang->line('exam_name') ?></th>
          <th><?= $this->lang->line('start_date') ?></th>
          <th><?= $this->lang->line('start_time') ?></th>
          <th><?= $this->lang->line('duration') ?></th>
          <th><?= $this->lang->line('user_operations') ?></th>
        </tr>
        <?php foreach ($future_exams_list as $exam): ?>
          <tr>
            <td class="leftalign"><?= $exam->class_id ?></td>
            <td class="leftalign"><?= $exam->exam_name ?></td>
            <td class="leftalign"><?= $exam->exam_start_date ?></td>
            <td class="leftalign"><?= $exam->exam_start_time ?></td>
            <td class="leftalign"><?= $exam->exam_length ?></td>
            <td class="leftalign">
              <a class="badge badge-primary" href="#">Hi</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </table>
    </div>
  </div>

  <div class="tab-pane fade" id="past_exams" role="tabpanel" aria-labelledby="contact-tab">
    <p>
      List of past exams.
    </p>
  </div>
</div>
