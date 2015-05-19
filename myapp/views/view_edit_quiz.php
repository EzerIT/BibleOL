<div id="quiz_tabs">
  <ul>
    <li><a href="#tab_description"><?= $this->lang->line('description') ?></a></li>
    <li><a href="#tab_universe"><?= $this->lang->line('passages') ?></a></li>
    <li><a href="#tab_sentences"><?= $this->lang->line('sentences') ?></a></li>
    <li><a href="#tab_sentence_units"><?= $this->lang->line('sentence_units') ?></a></li>
    <li><a href="#tab_features"><?= $this->lang->line('features') ?></a></li>
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
  <a class="makebutton" href="#" onclick="save_quiz(); return false;"><?= $this->lang->line('save_button') ?></a>
  <a class="makebutton" href="<?=site_url('file_manager?dir=' . $dir) ?>"><?= $this->lang->line('cancel_button') ?></a>
</div>

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

<!-- Quiz Filename dialog -->

<div id="filename-dialog" style="display:none" title="<?= $this->lang->line('specify_file_name') ?>">
  <p class="error" id="filename-error"></p>
  <table>
    <tr>
      <td><?= $this->lang->line('enter_filename_no_3et') ?></td>
      <td>
        <input type="text" name="filename" id="filename-name" value="<?= $quiz ?>" size="50" class="text ui-widget-content ui-corner-all">
      </td>
    </tr>
  </table>
</div>

<!-- Confirm File Overwrite dialog -->

<div id="overwrite-dialog-confirm" style="display:none" title="<?= $this->lang->line('overwrite') ?>">
  <p>
    <span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>
    <?= $this->lang->line('file_exists_overwrite') ?>
  </p>
</div>

<!-- Import from SHEBANQ dialog -->

<div id="import-shebanq-dialog" style="display:none" title="<?= $this->lang->line('import_from_shebanq') ?>">
  <p class="error" id="import-shebanq-error"></p>
  <table>
    <tr>
      <td><?= $this->lang->line('shebanq_query_id_prompt') ?></td>
      <td>
        <input type="text" name="query-id" id="import-shebanq-qid" value="" size="10" class="text ui-widget-content ui-corner-all">
      </td>
    </tr>
    <tr>
      <td><?= $this->lang->line('shebanq_version_prompt') ?></td>
      <td>
        <input type="text" name="db-version" id="import-shebanq-dbvers" value="4b" size="10" class="text ui-widget-content ui-corner-all">
      </td>
    </tr>
  </table>
</div>

<!-- Confirm Sentence Unit MQL dialog -->

<div id="qo-dialog-confirm" style="display:none" title="<?= $this->lang->line('mql_sentence_unit') ?>">
  <p id="qo-dialog-text"></p>
</div>

