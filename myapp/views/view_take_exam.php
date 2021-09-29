

<div id="timer">
  Time Left: <p id="timeLeft"></p>
</div>

<p id="test"></p>

<div>
<object id="quiz_frame" data="/exams/show_quiz?quiz=<?= str_replace("/", "%2F", array_shift($exercises)) ?>&examid=<?= $exam_id ?>&exercise_lst=<?= str_replace("/", "%2F", implode("~",$exercises)) ?>" width="100%" height="auto" onLoad="loaded()" onclick="frameClick()">
</object>
</div>


<script>
var deadline = <?= $deadline ?>;
setInterval(function(){
    var frame = document.getElementById('quiz_frame');
    if (frame.contentWindow.document.getElementById('done') !== null) {
      document.getElementById('timer').style.display = 'none';
    }
    timeLeft= deadline - new Date().getTime() / 1000;
    if (timeLeft < 0) {
      document.getElementById('quiz_frame').contentWindow.document.getElementById('finish').click();
      document.getElementById('timer').style.display = 'none';
    }
    var hours = Math.floor(timeLeft / 60 / 60);
    var minutes = Math.floor((timeLeft % (60 * 60)) / 60);
    var seconds = Math.floor((timeLeft % (60)));
    document.getElementById("timeLeft").innerHTML = hours + "h " + minutes + "m " + seconds + "s ";
},
1000
);

function loaded(){
  var frame = document.getElementById("quiz_frame");
  frame.style.height = frame.contentWindow.document.body.scrollHeight + 'px';
}

</script>
