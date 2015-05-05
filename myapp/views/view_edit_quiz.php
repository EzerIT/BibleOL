<div id="quiz_tabs">
  <ul>
    <li><a href="#tab_description">Description</a></li>
    <li><a href="#tab_universe">Passages</a></li>
    <li><a href="#tab_sentences">Sentences</a></li>
    <li><a href="#tab_sentence_units">Sentence Units</a></li>
    <li><a href="#tab_features">Features</a></li>
  </ul>
   
  <div id="tab_description">
    <textarea id="txtdesc" style="width:100%; height:100px" wrap="hard"></textarea>
  </div>
  <div id="tab_universe">
    <div id="passagetree">
    </div>
  </div>
  <div id="tab_sentences">
  </div>
  <div id="tab_sentence_units">
  </div>
  <div id="tab_features">
  </div>
</div>

<div style="display:none" id="virtualkbcontainer">
  <div id="virtualkbid"></div>
  <input id="firstinput" type="text"> <!--Initial attachment point for virtual keyboard --> 
</div>

<div class="buttons">
  <a class="makebutton" href="#" onclick="save_quiz(); return false;">Save</a>
  <a class="makebutton" href="<?=site_url('file_manager?dir=' . $dir) ?>">Cancel</a>
</div>

<script>
  var configuration = <?= $dbinfo_json ?>;
  var localization = <?= $localization_json ?>;
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

<!-- Quiz Filename dialog -->

<div id="filename-dialog" style="display:none" title="Specify File Name">
  <p class="error" id="filename-error"></p>
  <table>
    <tr>
      <td>Enter filename (without final '.3et')</td>
      <td>
        <input type="text" name="filename" id="filename-name" value="<?= $quiz ?>" size="60" class="text ui-widget-content ui-corner-all">
      </td>
    </tr>
  </table>
</div>

<!-- Confirm File Overwrite dialog -->

<div id="overwrite-dialog-confirm" style="display:none" title="Overwrite?">
  <p>
    <span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>
    The file already exists. Do you want to replace it?
  </p>
</div>

<!-- Import from SHEBANQ dialog -->

<div id="import-shebanq-dialog" style="display:none" title="Import from SHEBANQ">
  <p class="error" id="import-shebanq-error"></p>
  <table>
    <tr>
      <td>SHEBANQ query ID: </td>
      <td>
        <input type="text" name="query-id" id="import-shebanq-qid" value="" size="10" class="text ui-widget-content ui-corner-all">
      </td>
    </tr>
    <tr>
      <td>SHEBANQ database version: </td>
      <td>
        <input type="text" name="db-version" id="import-shebanq-dbvers" value="4b" size="10" class="text ui-widget-content ui-corner-all">
      </td>
    </tr>
  </table>
</div>

<!-- Confirm Sentence Unit MQL dialog -->

<div id="qo-dialog-confirm" style="display:none" title="MQL for sentence unit">
  <p id="qo-dialog-text"></p>
</div>

