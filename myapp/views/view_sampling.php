<?php if (isset($is_exam) && $is_exam): ?>
  <div id="quiz_id" style="display: none;"><?= $quizid ?></div>
  <div id="exam_id" style="display: none;"><?= $examid ?></div>
  <div id="exercise_lst" style="display: none;"><?= $exercise_lst ?></div>
<?php endif; ?>

<div class="row">
    






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
    let sentence_i = '<div class="ltr">';
    for(let i = 0; i < monadObjects.length; i++){
      let entry = monadObjects[i][0];
      for(let j = 0; j < entry.length; j++){
        let word = entry[j]['text'];
        if(j !== entry.length - 1)
          sentence_i = sentence_i + '<span  class="textdisplay greek">'  + word + '</span>' + '&nbsp;&nbsp;&nbsp;';
        else
          sentence_i = sentence_i + '<span class="textdisplay greek">'  + word + '</span>' + '</div>';
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
      let cell_txt = $(`<td style="white-space:pre; word-wrap: break-word;">${sentences[i]}</td>`);
      row.append(cell_ref);
      row.append(cell_txt);
      $('#book_table_0').append(row);
    }
    //console.log('References: ', references);
</script>




</div><!-- end of div class="row" -->
