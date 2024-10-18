<?php if (isset($is_exam) && $is_exam): ?>
  <div id="quiz_id" style="display: none;"><?= $quizid ?></div>
  <div id="exam_id" style="display: none;"><?= $examid ?></div>
  <div id="exercise_lst" style="display: none;"><?= $exercise_lst ?></div>
<?php endif; ?>




<div id="unlim" style="display:none"><?= $is_unlimited ?></div>

<script>
  function formatTime(time) {
    
    var hours = Math.floor(time / 60 / 60);
    var minutes = Math.floor((time % (60 * 60)) / 60);
    var seconds = Math.floor((time % (60)));
    return minutes + "m " + seconds + "s ";
  }

  var seconds = <?= isset($time_seconds) ? $time_seconds : -1000 ?>;
  if (seconds != -1000){ 
    console.log('HELLO FROM SCIRPT');
    var number_small_questions = <?= isset($number_small_questions) ? $number_small_questions : $number_of_quizzes; ?>;
    var total_time = number_small_questions * seconds;

    //seconds = seconds * 60;
    var deadline = (new Date().getTime() / 1000) + total_time;
    var isExam = <?php echo isset($is_exam) ? 'true' : 'false'; ?>;
    
    //var exam_status =  isset($is_exam) && $is_exam ?>;
    //console.log('Exam Status: ', exam_status);
    var quiz_idx = 0
    function iterateTimer(){
      var timeLeft = deadline - new Date().getTime() / 1000;
      console.log('timeLeft: ' + timeLeft);
      
      
      if (timeLeft < 0 && isExam == false) {
        document.getElementById('finish').click();
      }
      else if(timeLeft < 11) {
        document.getElementById('timeLeft').style.color = '#ffa5c7';
      }
      var timestamp = formatTime(timeLeft);
      document.getElementById("timeLeft").innerHTML =   timestamp;
    
      
    }


    $(document).ready(function(){  
      setInterval(iterateTimer, 1000);  
    });

    setInterval(function(){
      console.log('Hello From setInterval');
      var timeLeft = deadline - new Date().getTime() / 1000;
      console.log('timeLeft: ', timeLeft);
      if (timeLeft < 0) {
        document.getElementById('timer').style.display = 'none';
      }
      else if(timeLeft < 11) {
        document.getElementById('timeLeft').style.color = '#ffa5c7';
      }
      var timestamp = formatTime(timeLeft);
      document.getElementById("timeLeft").innerHTML =   timestamp;
    
    
    }, 1000);
  }

</script>

<div class="row">
    <div class="col-lg-3 sidepanel">
        <?php if ($is_quiz && (!isset($is_exam) || !$is_exam)): ?>
            <div class="card mb-3 d-none d-lg-block">
                <h5 class="card-header bg-primary text-light"><?= $this->lang->line('shortcut_heading') ?></h5>
                <div class="card-body"><?= $this->lang->line('shortcut_text') ?></div>
            </div>
        <?php endif; ?>
    </div>


<div class="col-lg-6 col-md-8 quizpanel">
  <?php if ($is_quiz && !$is_logged_in): ?>
    <div class="centerblock alert alert-warning">
         <h1><?= $this->lang->line('warning') ?></h1>
         <p><?= $this->lang->line('not_logged_in') ?></p>
    </div>
  <?php endif; ?>

  <div class="textcontainer-background" id="textcontainer">

    <?php if ($is_quiz): ?>
      <div id="quizdesc" class="cke_editable cke_editable_themed cke_contents_ltr cke_show_borders"></div>
    <?php else: ?>
      <h1></h1>
    <?php endif; ?>

    <div id="greyspace"></div>
    <div id="myview">
      <ul><li><a class="myview" href="#gramtabs"><h2><?= $this->lang->line('my_view') ?></h2></a></li></ul>
      <div id="tabs-background">
        <?php if ($is_quiz): ?>
            

            <div class="selectbutton row" id="locate_choice">
            <label style="font-size:14pt; font-weight:normal; text-transform:capitalize;"><?= $this->lang->line('locate') ?><input id="locate_cb" type="checkbox">
              <span class="location" style="font-weight:bold; text-transform:uppercase;"></span>
            </label>

            <div style="margin-left:auto; margin-right: auto;">
              <label id="timer" class="bg-secondary text-light" style="font-size:14pt; font-weight:normal; text-transform:capitalize;"><?= $this->lang->line('timer_label_section')?>
                <span id="timeLeft" style="font-weight:bold; text-transform:uppercase;"> </span>
              </label>
            </div>
          </div>
        <?php endif; ?>
        <?php if (!$is_quiz): ?>
            <h2></h2>
        <?php endif; ?>
      </div>
      <div class="grammarborder">
        <div id="gramtabs"></div>
      </div>
    </div>

    <div id="textarea">
    <?php if (!$is_quiz && $shebanq_link): ?>
        <div style="float:right;"><a href="<?= $shebanq_link ?>" title="Find chapter at SHEBANQ" target="shebanq"><img src="<?= site_url('/images/shebanq_logo32.png') ?>" alt=""></a></div>
      <?php endif; ?>
    </div>

    <?php if ($is_quiz): ?>
      <!-- Progress bar
       <progress> works in most browsers, <div id="progressbar"> is for older browsers and uses JQuery UI*/ -->
      <!-- <p class="inline"><?= $this->lang->line('progress') ?></p> -->
      <div id="progressbarfield">
        <progress id="progress" value="0" max="1"></progress>
        <div id="progressbar"></div>
      </div>

      <!-- Virtual keyboard -->
      <!-- <div id="virtualkbcontainer"><div id="virtualkbid"></div></div> -->
      <div id="quizcontainer"></div>
      <div id="buttonlist2">
          <button id="prev_question" class="btn btn-quiz" type="button"><?= $this->lang->line('previous') ?></button>
          <button id="next_question" class="btn btn-quiz" type="button"><?= $this->lang->line('next') ?></button>
          <button id="finish" class="btn btn-quiz" type="button"><?= isset($is_exam) && $is_exam ? $this->lang->line('finish_section') : $this->lang->line('finish') ?></button>
          <?php if (!(isset($is_exam) && $is_exam)): ?>
            <button id="finishNoStats" class="btn btn-quiz" type="button"><?= $this->lang->line('finish_no_grading') ?></button>
          <?php endif; ?>
      </div>

    <?php endif; ?>

    <p><button id="togglemql"><?= $this->lang->line('toggle_mql') ?></button></p>
    <pre class="mqlarea"><?= $mql_list ?></pre>
  </div>
  <div style="margin-bottom:700px"><!-- Make room for a too long grammar information box relating to last item in text --></div>
</div>

<div id="grammardisplaycontainer" class="col-lg-3 col-md-4">
  <div class="grammardisplay ui-widget ui-widget-content" id="aa2"></div>
</div>

<!-- Dialog for displaying grammar information -->
<div class="modal fade" id="grammar-info-dialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header justify-content-between">
        <div><h4 class="modal-title" id="grammar-info-label"></h4></div>
        <div><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>
      </div>
      <div class="modal-body" id="grammar-info-body">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-dark" data-dismiss="modal"><?= $this->lang->line('close_button') ?></button>
      </div>
    </div>
  </div>
</div>


<script>
    var useTooltip = <?= $useTooltip_str ?>;
    var configuration = <?= $dbinfo_json ?>;
    var dictionaries = <?= $dictionaries_json ?>;
    var l10n = <?= $l10n_json ?>;
    var l10n_js = <?= $l10n_js_json ?>;
    var typeinfo = <?= $typeinfo_json ?>;
    var quizdata = <?= $quizData_json ?>;
    var site_url = '<?= site_url() ?>';

    <?php $this->load->helper('icon_helper'); ?>
    var l_icon_map = <?= L_icon::json() ?>;

    $(function() {
            $('.mqlarea').hide();
            $('button#togglemql').click(function() {
                    $('.mqlarea').toggle();
                });
        });
</script>
</div><!-- end of div class="row" -->

<?php if($is_unlimited): ?>
  <script>
    var unlimited_msg = 'Unlimited';
    document.getElementById("timeLeft").innerHTML = unlimited_msg;
    document.getElementById('timeLeft').style.color = '#c3e3f7';
  </script>
<?php endif; ?>