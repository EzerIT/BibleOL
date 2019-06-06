<?php

$full_name = "/var/www/BibleOL/exam/" . $exam;
$xml = simplexml_load_file($full_name . "/config.xml") or die("error");

$exercise_list = array();
$feature_values = array();

$datetime_format = "/^[0-9]{4}-[0-9]{2}-[0-9]{2}T[0-9]{2}:[0-9]{2}$/";

if(isset($_POST["submit"])) {
	foreach ($_POST as $key => $value){
		if (preg_match($datetime_format, $value)){
			$value = str_replace("T", " ", $value) . ":00";
		}
		$feature_values[] = $value;

	}

	foreach ($xml->exercise as $x){
		$array = json_decode(json_encode((array) $x), TRUE);
		foreach ($array as $key => $value){
			if($key != "exercisename"){
				$removed = array_shift($feature_values);
				$x->$key = $removed;
			}
		}
	}

	$xml->asXML($full_name . "/config.xml");
}




?>


<head>
<style>
body {font-family: Arial;}

/* Style the tab */
.tab {
    overflow: hidden;
    border: 1px solid #ccc;
    background-color: #f1f1f1;
}

/* Style the buttons inside the tab */
.tab button {
    background-color: inherit;
    float: left;
    border: none;
    outline: none;
    cursor: pointer;
    padding: 14px 16px;
    transition: 0.3s;
    font-size: 17px;
}

/* Change background color of buttons on hover */
.tab button:hover {
    background-color: #ddd;
}

/* Create an active/current tablink class */
.tab button.active {
    background-color: #ccc;
}

/* Style the tab content */
.tabcontent {
    display: none;
    padding: 6px 12px;
    border: 1px solid #ccc;
    border-top: none;
}

.tab {

}

#Features {
  border-radius: 5px;
  border: solid 1px black;
}

#exercise {
  border-radius: 5px;
  border: solid 1px black;
}
</style>
</head>




<div>
  <form action="" method="post">
    <h3>Editing exam: <?php echo $exam; ?></h3>
    <br>
    <h5>Exam Description</h5>
    <textarea id="txtdesc" style="width:100%; height:100px" wrap="hard">Hello</textarea>
	 <?php foreach ($xml->exercise as $x): ?>
	   <div id="exercise">
	     <h5><?php echo $x->exercisename; ?></h5>
	     <?php
	     $array = json_decode(json_encode((array) $x), TRUE);
	     //print_r($array);
	     ?>
	     <?php foreach ($array as $key => $value): ?>
				 <?php if($key == "plan_start" or $key == "plan_end"): ?>
				   <div id="feature">
						 <input name="<?php echo $x->exercisename . $key; ?>" value="<?php echo substr($value, 0, 10) . "T" . substr($value, 11, 5); ?>" type="datetime-local">
						 <?php echo $key ?>
					 </div>
	       <?php elseif($key != "exercisename"): ?>
	         <div id="feature">
              <input name="<?php echo $x->exercisename . $key; ?>" value="<?php echo $value; ?>" min="0" step="1" type="number">
              <?php echo $key; ?>
            </div>
          <?php endif; ?>
	     <?php endforeach; ?>
	   </div>
	 <?php endforeach; ?>
    <input type="submit" value="Save" name="submit" class="btn btn-primary">
  </form>
</div>


<!--
<div id="quiz_tabs" class="ui-tabs ui-widget ui-widget-content ui-corner-all">

  <ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
    <li class="ui-tabs-panel ui-widget-content ui-corner-bottom" aria-labelledby="ui-id-1" role="tabpanel" aria-expanded="true" aria-hidden="false">
    	<a href="#tab_description" id="ui-id-1" class="ui-tabs-anchor" role="presentation" tabindex="-1"><?= $this->lang->line('description') ?></a>
    </li>
    <li class="ui-tabs-panel ui-widget-content ui-corner-bottom" aria-labelledby="ui-id-1" role="tabpanel" aria-expanded="true">
    	<a href="#tab_features"><?= $this->lang->line('features') ?></a>
    </li>
  </ul>

  <div id="tab_description" class="tabcontent">
    <textarea id="txtdesc" style="width:100%; height:100px" wrap="hard"></textarea>
  </div>
  <div id="tab_features" class="tabcontent">
    <input type="radio">
  </div>
</div>
-->

<div style="display:none" id="virtualkbcontainer">
  <div id="virtualkbid"></div>
  <input id="firstinput" type="text">
</div>


<script>
function openTab(evt, tabName){
  var i, tabcontent, tablinks;
  tabcontent = document.getElementsByClassName("tabcontent");
  for (i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = "none";
  }
  tablinks = document.getElementsByClassName("tablinks");
  for (i = 0; i < tablinks.length; i++){
    tablinks[i].className = tablinks[i].className.replace(" active", "");
  }
  document.getElementById(tabName).style.display = "block";
  evt.currentTarget.className += " active";
}
</script>

<!--
<div class="buttons">
  <a class="btn btn-primary" href="#" onclick="save_quiz(); return false;"><?= $this->lang->line('save_button') ?></a>
  <a class="btn btn-default" href="<?=site_url('file_manager?dir=' . $dir) ?>"><?= $this->lang->line('cancel_button') ?></a>
</div>
-->


<script>
  var configuration = <?= $dbinfo_json ?>;
  var l10n = <?= $l10n_json ?>;
  var l10n_js = <?= $l10n_js_json ?>;
  var typeinfo = <?= $typeinfo_json ?>;
  var decoded_3et = <?= $decoded_3et_json ?>;
  var initial_universe = <?= $universe ?>;
  var submit_to = '<?= site_url("text/submit_quiz") ?>';
  var check_url = '<?= site_url("text/check_submit_quiz") ?>';
  var import_shebanq_url = '<?= site_url("shebanq/import_shebanq") ?>';
  var quiz_name = '<?= is_null($quiz) ? '' : $quiz ?>';
  var dir_name = '<?= $dir ?>';
</script>


<!-- Dialogs for this page follow -->

  <?php //*********************************************************************
        // Quiz Filename dialog
        //*********************************************************************
    ?>
  <div id="filename-dialog" class="modal fade">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title"><?= $this->lang->line('specify_file_name') ?></h4>
        </div>
        <div class="modal-body">
          <div class="alert alert-danger" id="filename-error" role="alert">
            <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
            <span id="filename-error-text"></span>
          </div>
          <div class="form-group">
            <label for="filename-name"><?= $this->lang->line('enter_filename_no_3et') ?></label>
            <input type="text" name="filename" id="filename-name" value="<?= $quiz ?>" class="form-control">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" id="filename-dialog-save" class="btn btn-primary"><?= $this->lang->line('save_button') ?></button>
          <button type="button" class="btn btn-default" data-dismiss="modal"><?= $this->lang->line('cancel_button') ?></button>
        </div>
      </div>
    </div>
  </div>


 <?php //*********************************************************************
        // Confirm File Overwrite dialog
        //*********************************************************************
    ?>
<div id="overwrite-dialog-confirm" class="modal fade">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title"><?= $this->lang->line('overwrite') ?></h4>
      </div>
      <div class="modal-body">
        <span class="glyphicon glyphicon-question-sign" style="float:left; margin:0 7px 20px 0;" aria-hidden="true"></span>
        <span><?= $this->lang->line('file_exists_overwrite') ?></span>
      </div>
      <div class="modal-footer">
        <button type="button" id="overwrite-yesbutton" class="btn btn-primary"><?= $this->lang->line('yes') ?></button>
        <button type="button" class="btn btn-default" data-dismiss="modal"><?= $this->lang->line('no') ?></button>
      </div>
    </div>
  </div>
</div>

 <?php //*********************************************************************
        // Import from SHEBANQ dialog
        //*********************************************************************
    ?>
  <div id="import-shebanq-dialog" class="modal fade">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title"><?= $this->lang->line('import_from_shebanq') ?></h4>
        </div>
        <div class="modal-body">
          <div class="alert alert-danger" id="import-shebanq-error" role="alert">
            <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
            <span id="import-shebanq-error-text"></span>
          </div>
          <div class="form-group">
            <label for="import-shebanq-qid"><?= $this->lang->line('shebanq_query_id_prompt') ?></label>
            <input type="text" name="query-id" id="import-shebanq-qid" value="" class="form-control">
          </div>
          <div class="form-group">
            <label for="import-shebanq-dbvers"><?= $this->lang->line('shebanq_query_id_prompt') ?></label>
            <input type="text" name="db-version" id="import-shebanq-dbvers" value="4b" class="form-control">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" id="import-shebanq-button" class="btn btn-primary"><?= $this->lang->line('import_button') ?></button>
          <button type="button" class="btn btn-default" data-dismiss="modal"><?= $this->lang->line('cancel_button') ?></button>
        </div>
      </div>
    </div>
  </div>


 <?php //*********************************************************************
        // Confirm Sentence Unit MQL dialog
        //*********************************************************************
    ?>
<div id="qo-dialog-confirm" class="modal fade">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title"><?= $this->lang->line('mql_sentence_unit') ?></h4>
      </div>
      <div class="modal-body">
        <span class="glyphicon glyphicon-question-sign" style="float:left; margin:0 7px 20px 0;" aria-hidden="true"></span>
        <p id="qo-dialog-text"></p>
      </div>
      <div class="modal-footer">
        <button type="button" id="qo-yesbutton" class="btn btn-primary"><?= $this->lang->line('yes') ?></button>
        <button type="button" id="qo-nobutton" class="btn btn-default" data-dismiss="modal"><?= $this->lang->line('no') ?></button>
        <button type="button" id="qo-okbutton" class="btn btn-primary" data-dismiss="modal"><?= $this->lang->line('OK_button') ?></button>
      </div>
    </div>
  </div>
</div>
