<?php if (isset($is_exam) && $is_exam): ?>
  <div id="quiz_id" style="display: none;"><?= $quizid ?></div>
  <div id="exam_id" style="display: none;"><?= $examid ?></div>
  <div id="exercise_lst" style="display: none;"><?= $exercise_lst ?></div>
<?php endif; ?>

<div class="row">
    


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
            <div class="selectbutton" id="locate_choice">
            <label style="font-size:14pt; font-weight:normal; text-transform:capitalize;"><?= $this->lang->line('locate') ?><input id="locate_cb" type="checkbox">
            <span class="location" style="font-weight:bold; text-transform:uppercase;"></span></label>
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

<?php 
  //echo "Quiz Data: " . var_dump($quizData_json) . "<br>";
  $data = json_encode(json_decode($dictionaries_json)->monadObjects[0]);
  file_put_contents('data.json', $data);
?>


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



<script>
    //console.log("Dictionaries: " + JSON.stringify(dictionaries['monadObjects']));
    let monadObjects = dictionaries['monadObjects'];
    //console.log('Monad Objects: ', monadObjects);
    let sentences = [];
    let sentence_i = '';
    for(let i = 0; i < monadObjects.length; i++){
      let entry = monadObjects[i][0];
      for(let j = 0; j < entry.length; j++){
        let word = entry[j]['text'];
        if(j !== entry.length - 1)
          sentence_i = sentence_i + '<span class="textdisplay greek">'  + word + '</span>' + '&nbsp;&nbsp;&nbsp;';
        else
          sentence_i = sentence_i + '<span class="textdisplay greek">'  + word + '</span>';
      }
      sentences.push(sentence_i);
      sentence_i = '';
    }
    console.log('Sentences: ', sentences);



    let references = [];
    for(let i = 0; i < monadObjects.length; i++) {
      let entry = monadObjects[i][0][0];
      let bcv_loc = entry['bcv_loc'];
      references.push(bcv_loc);
      let row = $(`<tr></tr>`);
      let cell_ref = $(`<td style="text-align:center; vertical-align:middle;"><span class="location" style="font-weight:bold; text-transform:uppercase;">${bcv_loc}</span></td>`);
      let cell_txt = $(`<td>${sentences[i]}</td>`);
      row.append(cell_ref);
      row.append(cell_txt);
      $('#book_table_0').append(row);
    }
    //console.log('References: ', references);
</script>




</div><!-- end of div class="row" -->
