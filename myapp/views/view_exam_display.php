<div id="quiz_id" style="display: none;"><?= $quizid ?></div>
<div id="exam_id" style="display: none;"><?= $examid ?></div>
<div id="exercise_lst" style="display: none;"><?= $exercise_lst ?></div>
<div class="col-lg-3">
  <div class="grammarselector" id="gramselect"></div>
  <?php if (!$is_quiz): ?>
    <p><a class="btn btn-primary" id="cleargrammar" href="#"><?= $this->lang->line('clear_grammar') ?></a></p>
  <?php endif; ?>
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
      <div id="quizdesc"></div>
    <?php else: ?>
      <?php if ($shebanq_link): ?>
        <div style="float:right;"><a href="<?= $shebanq_link ?>" title="Find chapter at SHEBANQ" target="shebanq"><img src="<?= site_url('/images/shebanq_logo32.png') ?>" alt=""></a></div>
      <?php endif; ?>
      <h1></h1>
    <?php endif; ?>

    <div id="textarea"></div>

    <?php if ($is_quiz): ?>
      <div id="virtualkbcontainer"><div id="virtualkbid"></div></div>
      <div style="overflow-x:auto; overflow-y:hidden">
        <table id="quiztab"></table>
      </div>
      <p><span id="locate_choice"><input id="locate_cb" type="checkbox"><?= $this->lang->line('locate') ?> <span class="location"></span></span></p>
      <?php /* <progress> works in most browsers, <div id="progressbar"> is for older browsers and uses JQuery UI*/ ?>
      <p class="inline"><?= $this->lang->line('progress') ?></p>
      <progress id="progress" value="0" max="1"></progress>
      <div id="progressbar"></div>
      <p id="progresstext" class="inline"></p>
      <div id="buttonlist2">
        <button id="next_question" type="button"><?= $this->lang->line('next') ?></button>
        <button id="finish" type="button"><?= $this->lang->line('finish') ?></button>
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
