<?php if (isset($is_exam) && $is_exam): ?>
  <div id="quiz_id" style="display: none;"><?= $quizid ?></div>
  <div id="exam_id" style="display: none;"><?= $examid ?></div>
  <div id="exercise_lst" style="display: none;"><?= $exercise_lst ?></div>
<?php endif; ?>

<div class="row">
    






<?php 
  //echo "Quiz Data: " . var_dump($quizData_json) . "<br>";
  //$data = json_encode(json_decode($dictionaries_json)->monadObjects[0]);
  //file_put_contents('data.json', $data);
?>

<script>
  var count = <?= $n_candidates ?>;
  $('#actual_count').empty();
  $('#actual_count').append(count);
</script>


<script>
    var useTooltip = <?= $useTooltip_str ?>;
    var configuration = <?= $dbinfo_json ?>;
    var dictionaries = <?= $dictionaries_json ?>;
    var l10n = <?= $l10n_json ?>;
    var l10n_js = <?= $l10n_js_json ?>;
    var typeinfo = <?= $typeinfo_json ?>;
    var quizdata = <?= $quizData_json ?>;
    var table_idx = <?= $table_idx ?>;
    var site_url = '<?= site_url() ?>';
    

    
</script>




<script>
  function initialize_vars(){
    let monadObjects = dictionaries['monadObjects'];  
    let sentences = [];
    let sentence_i = '<span class="textdisplay greek" style="white-space:break-spaces;" >';
    let references = [];
  }

  function order_by_reference(monadObjects) {
    // sort monadObjects by bcv_loc
    let references = [];
    for(let i = 0; i < monadObjects.length; i++){
      let ref = monadObjects[i][0][0]['bcv_loc'];
      let verse_number = ref.split(':')[1];

      if(verse_number.length == 1){
        verse_number_update = '0' + verse_number;
        ref = ref.split(':')[0] + ':' + verse_number_update
      }
      //console.log('Verse Number: ', verse_number);
      let ref_num = ref.replace(/\D/g, '');
      ref_num = parseInt(ref_num);
      references.push(ref_num);
    }

    let idx = Array.from({length: references.length}, (_, i) => i);
    idx.sort((a, b) => references[a] - references[b]);

    //references.sort();
    monadObjects = idx.map(i => monadObjects[i]);
    return monadObjects
  }

  function group_by_passage(monadObjects) {
    let monads_by_passage = {};
    for(let i = 0; i < monadObjects.length; i++) {
      let ref = monadObjects[i][0][0]['bcv_loc'];
      ref = ref.replace(/\d/g, '');
      ref = ref.replace(':', '');
      if(ref in monads_by_passage) {
        monads_by_passage[ref].push(monadObjects[i]);
      }
      else {
        monads_by_passage[ref] = [monadObjects[i]];
      }
    }
    console.log('Monads by Passage: ', monads_by_passage);

    return monads_by_passage;
  }
  /*
  var count = $n_candidates ;
  $('#actual_count').empty();
  $('#actual_count').append(count);
  */
  //--------------------------------------------------------------------------------
  // update_counts() function
  //
  // Parameters:
  //    button_idx (int) - the index of the button that is getting the count labeled onto it
  //    monads_by_passage (dict) - a dictionary of monads grouped by passage
  //    passage_name (string) - the name of the passage
  // Returns:
  //    None
  function update_counts(button_idx, monads_by_passage, passage_name) {
    let count = monads_by_passage[passage_name].length;
    $(`#actual_count${button_idx}`).empty();
    $(`#actual_count${button_idx}`).append(count);
  }



  //--------------------------------------------------------------------------------
  // generate_table() function
  //
  // generate the sentence and reference columns for the table
  // parameters: 
  //    monad_section (array) - a list of monads
  // returns:
  //    sentences (array) - a list of sentences
  function populate_table(monad_section, tidx) {
    sentences = [];
    sentence_i = '<span class="textdisplay greek" style="white-space:break-spaces;" >';
    for(let i = 0; i < monad_section.length; i++){
      let entry = monad_section[i][0];
      for(let j = 0; j < entry.length; j++){
        let word = entry[j]['text'];
        if(j !== entry.length - 1) {
          sentence_i = sentence_i  + word  + '&nbsp;&nbsp;&nbsp;';
        }
        else {
          sentence_i = sentence_i  + word + '</span>';
        }
      }
      sentences.push(sentence_i);
      sentence_i = '<span class="textdisplay greek" style="white-space:break-spaces;" >';
    }

    references = [];
  
    for(let i = 0; i < monad_section.length; i++) {
      let entry = monad_section[i][0][0];
      let bcv_loc = entry['bcv_loc'];
      references.push(bcv_loc);
      let row = $(`<tr></tr>`);
      let cell_ref = $(`<td style="text-align:center; vertical-align:middle;"><span class="location" style="font-weight:bold; text-transform:uppercase;">${bcv_loc}</span></td>`);
      let cell_txt = $(`<td style="white-space:pre; word-wrap: anywhere;">${sentences[i]}</td>`);
      row.append(cell_ref);
      row.append(cell_txt);
      //console.log('Entry: ', entry);
      
      $(`#book_table_${tidx}`).append(row);
    }
    
  }


  


  if(table_idx === 0) {
    initialize_vars();
  }
  else {
    for(let i = 0; i <= table_idx-1; i++){
      $(`#book_table_${i}`).remove();
    }
  }
  
  monadObjects = dictionaries['monadObjects'];
  monadObjects = order_by_reference(monadObjects);
  let monads_by_passage = group_by_passage(monadObjects);
  let passages = Object.keys(monads_by_passage);
  for(let i = 0; i < passages.length; i++){
    let monad_section = monads_by_passage[passages[i]];
    update_counts(i, monads_by_passage, passages[i]);
    populate_table(monad_section, i);

  }
  
  
    
</script>




</div><!-- end of div class="row" -->
