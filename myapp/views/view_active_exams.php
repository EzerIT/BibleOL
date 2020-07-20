<?php
foreach ($active_exams_list as $active_exam) {
  var_dump($active_exam);
  echo "<br>";
}
?>

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
    <p>
      List of active exams.
    </p>
  </div>
  <div class="tab-pane fade" id="future_exams" role="tabpanel" aria-labelledby="profile-tab">
    <p>
      List of future exams.
    </p>
  </div>
  <div class="tab-pane fade" id="past_exams" role="tabpanel" aria-labelledby="contact-tab">
    <p>
      List of past exams.
    </p>
  </div>
</div>
