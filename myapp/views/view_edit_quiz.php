<div class="quizeditor" style="display:none;">
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
    <div style="margin-top:10px"><input id="maylocate_cb" type="checkbox" name="maylocate" value="maylocate"> <?= $this->lang->line('may_locate') ?></div>
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
  <a class="btn btn-primary" href="#" onclick="save_quiz(); return false;"><?= $this->lang->line('save_button') ?></a>
  <a class="btn btn-outline-dark" href="<?=site_url(build_get('file_manager',array('dir' => $dir))) ?>"><?= $this->lang->line('cancel_button') ?></a>
</div>
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

 <?php //*********************************************************************
       // hideFeatures dialog 
       //*********************************************************************
    ?>
<div id="feature-limit-dialog" class="modal fade">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header justify-content-between">
        <div><h4 class="modal-title"><?= $this->lang->line('hide_features') ?></h4></div>
        <div><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button></div>
      </div>
      <div class="modal-body">
        <span class="fas fa-question-circle" style="float:left; margin:0 7px 20px 0;" aria-hidden="true"></span>
        <span id="feature-limit-body"></span>
      </div>
      <div class="modal-footer">
          <button type="button" id="feature-limit-dialog-save" class="btn btn-primary"><?= $this->lang->line('save_button') ?></button>
          <button type="button" class="btn btn-outline-dark" data-dismiss="modal"><?= $this->lang->line('cancel_button') ?></button>
      </div>
    </div>
  </div>
</div>

 <?php //*********************************************************************
        // Quiz Filename dialog 
        //*********************************************************************
    ?>
  <div id="filename-dialog" class="modal fade">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-header justify-content-between">
          <div><h4 class="modal-title"><?= $this->lang->line('specify_file_name') ?></h4></div>
          <div><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button></div>
        </div>
        <div class="modal-body">
          <div class="alert alert-danger" id="filename-error" role="alert">
            <span class="fas fa-exclamation-circle" aria-hidden="true"></span>
            <span id="filename-error-text"></span>
          </div>
          <div class="form-group">
            <label for="filename-name"><?= $this->lang->line('enter_filename_no_3et') ?></label>
            <input type="text" name="filename" id="filename-name" value="<?= $quiz ?>" class="form-control">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" id="filename-dialog-save" class="btn btn-primary"><?= $this->lang->line('save_button') ?></button>
          <button type="button" class="btn btn-outline-dark" data-dismiss="modal"><?= $this->lang->line('cancel_button') ?></button>
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
      <div class="modal-header justify-content-between">
        <div><h4 class="modal-title"><?= $this->lang->line('overwrite') ?></h4></div>
        <div><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button></div>
      </div>
      <div class="modal-body">
        <span class="fas fa-question-circle" style="float:left; margin:0 7px 20px 0;" aria-hidden="true"></span>
        <span><?= $this->lang->line('file_exists_overwrite') ?></span>
      </div>
      <div class="modal-footer">
        <button type="button" id="overwrite-yesbutton" class="btn btn-primary"><?= $this->lang->line('yes') ?></button>
        <button type="button" class="btn btn-outline-dark" data-dismiss="modal"><?= $this->lang->line('no') ?></button>
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
        <div class="modal-header justify-content-between">
          <div><h4 class="modal-title"><?= $this->lang->line('import_from_shebanq') ?></h4></div>
          <div><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button></div>
        </div>
        <div class="modal-body">
          <div class="alert alert-danger" id="import-shebanq-error" role="alert">
            <span class="fas fa-exclamation-circle" aria-hidden="true"></span>
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
          <button type="button" class="btn btn-outline-dark" data-dismiss="modal"><?= $this->lang->line('cancel_button') ?></button>
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
      <div class="modal-header justify-content-between">
        <div><h4 class="modal-title"><?= $this->lang->line('mql_sentence_unit') ?></h4></div>
        <div><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button></div>
      </div>
      <div class="modal-body">
        <span class="fas fa-question-circle" style="float:left; margin:0 7px 20px 0;" aria-hidden="true"></span>
        <p id="qo-dialog-text"></p>
      </div>
      <div class="modal-footer">
        <button type="button" id="qo-yesbutton" class="btn btn-primary"><?= $this->lang->line('yes') ?></button>
        <button type="button" id="qo-nobutton" class="btn btn-outline-dark" data-dismiss="modal"><?= $this->lang->line('no') ?></button>
        <button type="button" id="qo-okbutton" class="btn btn-primary" data-dismiss="modal"><?= $this->lang->line('OK_button') ?></button>
      </div>
    </div>
  </div>
</div>

