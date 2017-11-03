<?php
    $valerr = validation_errors();
    if (!empty($valerr))
        echo "<div class=\"alert alert-danger\">$valerr</div>\n";
?>

   <?= form_open("statistics/student_exercise",array('method'=>'get')) ?>
    <input type="hidden" name="userid" value="<?= $userid ?>">

    <p><?= $this->lang->line('specify_period') ?></p>
    <table>
      <tr>
        <td style="font-weight:bold;padding-right:5px;padding-left:20px;"><?= $this->lang->line('period_from') ?></td>
        <td style="padding-left:5px"><input type="text" name="start_date" value="<?= $start_date ?>"></td>
      </tr>
      <tr>
        <td style="font-weight:bold;padding-right:5px;padding-left:20px;"><?= $this->lang->line('period_to') ?></td>
        <td style="padding-left:5px"><input type="text" name="end_date" value="<?= $end_date ?>"></td>
      </tr>
    </table>

  <div class="row">
  <div class="form-group">
    <label for="nongraded" class="col-sm-3 control-label"><?= $this->lang->line('show_non_graded_prompt') ?></label>
    <div class="col-sm-9">
      <input class="checkbox" style='display:inline-block' id="nongraded" name="nongraded"
        <?= $may_see_nongraded ? '' : 'disabled' ?>
        value="on" type="checkbox" <?= set_checkbox('nongraded', $may_see_nongraded ? 'on' : 'off') ?>>
      <span><?= $may_see_nongraded ? '' : '(Permission not granted)' ?></span>
    </div>
  </div>
  </div>


           
  <input type="hidden" name="templ" value="<?= $quiz ?>">
  <input type="hidden" name="userid" value="<?= $userid ?>">

  
           
  <p><input class="btn btn-primary" style="margin-top:10px;" type="submit" name="submit" value="<?= $this->lang->line('OK_button') ?>"></p>
</form>

  <script>
      $(function() {
              $(datepicker_period('input[name="start_date"]','input[name="end_date"]'));
          });
  </script>

  <h1><?= sprintf($this->lang->line('statistics_for_exercise'),htmlspecialchars($quiz)) ?></h1>
  <h1><?= sprintf($this->lang->line('student_is'),htmlspecialchars($user_full_name)) ?></h1>

<?php if ($status!=2): ?>

  <?php if ($status==0): ?>

    <h2>No data</h2>

  <?php else: ?>

    <?php
      define("SHOW_WEEK_LIMIT", 10*24*3600); // 10 days

      if ($maxpoint-$minpoint > SHOW_WEEK_LIMIT) {
          $showweek = true;
          $scale_start = Statistics_timeperiod::last_monday($minpoint);
          $scale_end = Statistics_timeperiod::next_monday($maxpoint-1);
      }
      else {
          $showweek = false;
          $scale_start = Statistics_timeperiod::last_midnight($minpoint);
          $scale_end = Statistics_timeperiod::next_midnight($maxpoint-1);
      }

      $res = array();
      $resspf = array();
      foreach ($resscore as $date => $r) {
          $textdate = Statistics_timeperiod::format_date($date);
          $res[]    = "[$date,{$r['percentage']},null,'{$this->lang->line('date_colon')} $textdate<br>{$this->lang->line('question_count')} {$r['count']}<br>{$this->lang->line('per_min')} " . round($r['featpermin'],1) . "']";
          $roundpct = round($r['percentage']);
          $resspf[] = "[$date,{$r['featpermin']},null,'{$this->lang->line('date_colon')} $textdate<br>{$this->lang->line('correct_colon')} $roundpct%']";
      }

      $resx    = "[[" . implode(",", $res) . "]]";
      $resxspf = "[[" . implode(",", $resspf) . "]]";

      $canvasheight = count($resfeat)*25 + 70;

      $featname = array();
      $featpct = array();
       
      foreach ($resfeat as $rf) {
          // Localize if possible, otherwise just retain the symbolic feature name
          $featname[] = "'" . (isset($featloc->{$rf->rfname}) ? $featloc->{$rf->rfname} : $rf->rfname)   . "'";
          $featpct[] = $rf->pct;
      }
    ?>

    <h2><?= $this->lang->line('pct_correct_by_date') ?></h2>
    <canvas style="background:#f8f8f8; display:inline-block; vertical-align:top;" id="cvs" width="800" height="500">
      [No canvas support]
    </canvas>

    <h2><?= $this->lang->line('speed_by_date') ?></h2>
    <canvas style="background:#f8f8f8; display:inline-block; vertical-align:top;" id="cvsspf" width="800" height="500">
      [No canvas support]
    </canvas>

    <h2><?= $this->lang->line('pct_correct_by_feature') ?></h2>
    <canvas style="background:#f8f8f8; display:inline-block; vertical-align:top;" id="featcanvas" width="800" height="<?= $canvasheight ?>">
    [No canvas support]
</canvas>


    <script>
      $(function() {
          var xlabels = [<?php
                           $numxticks = 0;
                           if ($showweek) {
                               for ($ut=$scale_start; $ut<$scale_end; $ut += Statistics_timeperiod::SECS_PER_WEEK) {
                                   echo '"',Statistics_timeperiod::format_week($ut),'",';
                                   ++$numxticks;
                               }
                           }
                           else {
                               for ($ut=$scale_start; $ut<$scale_end; $ut += Statistics_timeperiod::SECS_PER_DAY) {
                                   echo '"',Statistics_timeperiod::format_day($ut),'",';
                                   ++$numxticks;
                               }
                           }
                         ?>];
          <?php if ($showweek): ?>
            var weekdates = [<?php
                               for ($ut=$scale_start; $ut<$scale_end; $ut += Statistics_timeperiod::SECS_PER_WEEK)
                                   echo '"',Statistics_timeperiod::format_date($ut),'",';
                             ?>];
          <?php else: ?>
            var daydates = [<?php
                               for ($ut=$scale_start; $ut<$scale_end; $ut += Statistics_timeperiod::SECS_PER_DAY)
                                   echo '"',Statistics_timeperiod::format_date($ut),'",';
                             ?>];
          <?php endif; ?>
              
          var scatterdata = make_scatterconfig('cvs', <?= $resx ?>, <?= $scale_start ?>, <?= $scale_end ?>,
                                             '%', '<?= $this->lang->line('correct') ?>', '<?= $showweek ? $this->lang->line('iso_week_no') : $this->lang->line('date') ?>',
                                             xlabels, <?= $numxticks ?>);
          var scatterdataspf = make_scatterconfig('cvsspf',<?= $resxspf ?>, <?= $scale_start ?>, <?= $scale_end ?>,
                                                  null, '<?= $this->lang->line('question_items_per_min') ?>', '<?= $showweek ? $this->lang->line('iso_week_no') : $this->lang->line('date') ?>',
                                                  xlabels, <?= $numxticks ?>);
          
          scatterdataspf.options.ymax = null;
          scatterdataspf.options.scaleDecimals = 1;

          scatter = new RGraph.Scatter(scatterdata).draw();
          scatterspf = new RGraph.Scatter(scatterdataspf).on('firstdraw', adaptScale).draw();

          <?php if ($showweek): ?>
              weekno_tooltip('cvs', xlabels, '<?= $this->lang->line('starts') ?>' + '\n', weekdates);
              weekno_tooltip('cvsspf', xlabels, '<?= $this->lang->line('starts') ?>' + '\n', weekdates);
          <?php else: ?>
              weekno_tooltip('cvs', xlabels, '', daydates);
              weekno_tooltip('cvsspf', xlabels, '', daydates);
          <?php endif; ?>


          var hbarconfig = make_hbarconfig('featcanvas', [<?= implode(",", $featpct) ?>], [<?= implode(",", $featname) ?>], '<?= $this->lang->line('correct') ?>', <?= $canvasheight-10 ?>);

          new RGraph.HBar(hbarconfig).draw();

      });
      </script>

  <?php endif; ?>
<?php endif; ?>
