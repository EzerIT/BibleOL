<div class="col-lg-3">
</div>

<div class="col-lg-6 col-md-8">
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
      <ul><li><a class="myview" href="#gramtabs"><h2>MyView</h2></a></li></ul>
      <div id="tabs-background">
        <?php if ($is_quiz): ?>
          <h2><div class="selectbutton">
            <label style="font-size:14pt; font-weight:normal; text-transform:capitalize;">Locate<input id="locate_cb" type="checkbox" checked="checked">
            <span class="location" style="font-weight:bold; text-transform:uppercase;"> </span></label>
          </div></h2>
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
          <button id="finish" class="btn btn-quiz" type="button"><?= $this->lang->line('finish') ?></button>
          <button id="finishNoStats" class="btn btn-quiz" type="button"><?= $this->lang->line('finish_no_grading') ?></button>
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
