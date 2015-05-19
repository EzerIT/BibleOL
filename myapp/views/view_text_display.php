<div class="grammarselector" id="gramselect"></div>
<div class="grammardisplay ui-widget ui-widget-content ui-corner-all" id="aa2"></div>
 
<div id="textcontainer">
  <?php if ($is_quiz && !$is_logged_in): ?>
    <div class="warning">
         <h1><?= $this->lang->line('warning') ?></h1>
         <p><?= $this->lang->line('not_logged_in') ?></p>
    </div>
  <?php endif; ?>

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
    <table id="quiztab"></table>
     <p><input id="locate_cb" type="checkbox"><?= $this->lang->line('locate') ?> <span class="location"></span></p>
     <?php /* <progess> works in most browsers, <div id="progressbar"> is for older browsers and uses JQuery UI*/ ?>
     <p class="inline"><?= $this->lang->line('progress') ?></p>
     <progress id="progress" value="0" max="1"></progress>
     <div id="progressbar"></div>
     <p id="progresstext" class="inline"></p>
    <div id="buttonlist1">
      <button id="check_answer" type="button"><?= $this->lang->line('check_answer') ?></button>
      <button id="show_answer" type="button"><?= $this->lang->line('show_answer') ?></button>
    </div>
    <div id="buttonlist2">
      <button id="next_question" type="button"><?= $this->lang->line('next') ?></button>
      <button id="finish" type="button"><?= $this->lang->line('finish') ?></button>
    </div>
  <?php endif; ?>

  <p><button id="togglemql"><?= $this->lang->line('toggle_mql') ?></button></p>
  <pre class="mqlarea"><?= $mql_list ?></pre>
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

         
    $(function() {
            $('.mqlarea').hide();
            $('button#togglemql').click(function() {
                    $('.mqlarea').toggle();
                });
        });
</script>
