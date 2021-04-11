
<object id="quiz_frame" data="/exams/show_quiz?quiz=<?= str_replace("/", "%2F", array_shift($exercises)) ?>&examid=<?= $exam_id ?>&exercise_lst=<?= str_replace("/", "%2F", implode("~",$exercises)) ?>" width="100%" onLoad="get_id()">
</object>


<script>
function get_id(){
  var frame = document.getElementById("quiz_frame");
  frame.style.height = frame.contentWindow.document.body.scrollHeight + 'px';
  var quizid = document.getElementById("quiz_frame").contentWindow.document.getElementById("quiz_id").innerHTML;
  document.getElementById("quiz_frame").contentWindow.document.getElementById("finish").addEventListener("click", function() {
    document.getElementById("quizid").value = document.getElementById("quizid").value + ',' + quizid;
  })
}

</script>
