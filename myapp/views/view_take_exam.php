<?php
//print_r(array_shift($exercises));
?>
<!-- <div><iframe id="quiz" src="/exams/show_quiz?quiz=<?= str_replace("/", "%2F", $exercises[0]) ?>&count=10" width="100%" height="700px"></div> -->

<object id="quiz_frame" data="/exams/show_quiz?quiz=<?= str_replace("/", "%2F", array_shift($exercises)) ?>&count=10&examid=<?= $exam_id ?>" width="600" height="400" onLoad="get_id()">
</object>

<form id="exam_form" action="/exams/submit_exam_quiz.php">
  <input type="hidden" id="quizid" name="quizid">
  <input type="hidden" id="examid" name="examid" value="<?= $exam_id ?>">
  <input type="submit" value="Submit">
<form>
<p id="quiz_id"></p>

<script>
function get_id(){
  var quizid = document.getElementById("quiz_frame").contentWindow.document.getElementById("quiz_id").innerHTML;
  document.getElementById("quiz_id").innerHTML = quizid;
  //document.getElementById("quizid_input").value = quizid;
  document.getElementById("quiz_frame").contentWindow.document.getElementById("finish").addEventListener("click", function() {
    document.getElementById("quizid").value = document.getElementById("quizid").value + ',' + quizid;
    // var next_quiz = <?= str_replace("/", "%2F", array_shift($exercises)) ?>;
    // if (next_quiz) {
    //   document.getElementById("quiz_frame").data = "/exams/show_quiz?quiz=<?= str_replace("/", "%2F", array_shift($exercises)) ?>&count=10";
    // }
  })
}

</script>































<!--
<!--
<!-- <a href="/">Test</a>
<br>
<?php
  // var_dump($exercises);
  echo $exercises[0];
  echo count($exercises);
  $active = 0;
  //$this->load->library('quiz_data');
?>

<br>

<p id="active">0</p>
<p id="act_q"></p>

<p id="quizid"></p>

<div id="exam_buttons">
  <button id="next_quiz" onclick="next_quiz()">Next</button>
</div>

<script>
function next_quiz(){
  document.getElementById("quizid").innerHTML = <?= $this->input->post('quizid') ?>;
  <?php
  $active += 1;
  ?>
  document.getElementById("active").innerHTML = <?= $active ?>;
  // document.getElementById("act_q").innerHTML = <?= $exercises[$active] ?>;
  document.getElementById("quiz").src = "/exams/show_quiz?quiz=<?= str_replace("/", "%2F", $exercises[$active]) ?>&count=10";
}

</script> -->

<!-- <iframe id="quiz" src="/exams/show_quiz?quiz=<?= str_replace("/", "%2F", $exercises[0]) ?>&count=10" width="100%" height="700px"> --> --> -->
