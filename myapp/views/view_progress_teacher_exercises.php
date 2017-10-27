<?php
    $valerr = validation_errors();
    if (!empty($valerr))
        echo "<div class=\"alert alert-danger\">$valerr</div>\n";
?>

<div class="card">
    <?= form_open("statistics/teacher_exercises",array('method'=>'get')) ?>
    <input type="hidden" name="classid" value="<?= $classid ?>">

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

  <p>&nbsp;</p>
  <div>
    <span style="font-weight:bold">Exercise:</span>
    <select name="exercise">
      <option value="" <?= set_select('exercise', '', true) ?>></option>
      <?php foreach($exercise_list as $ex): ?>
        <?php $ex2 = htmlspecialchars($ex); ?>
        <option value="<?= $ex2 ?>" <?= set_select('exercise', $ex) ?>><?= $ex2 ?></option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="row">
  <div class="form-group">
    <label for="nongraded" class="col-sm-3 control-label"><?= $this->lang->line('show_non_graded_prompt') ?></label>
    <div class="col-sm-9">
      <input class="checkbox" id="nongraded" name="nongraded" value="on" type="checkbox" <?= set_checkbox('nongraded','on') ?>>
    </div>
  </div>
  </div>

  <p><input class="btn btn-primary" style="margin-top:10px;" type="submit" name="submit" value="<?= $this->lang->line('OK_button') ?>"></p>
</form>
</div>

  <script>
      $(function() {
              $(datepicker_period('input[name="start_date"]','input[name="end_date"]'));
          });
  </script>

   <h1>Statistics for class &ldquo;<?= htmlspecialchars($classname) ?>&rdquo;</h1>

<?php if ($status!=2): ?>
  <h2><?= sprintf($this->lang->line('statistics_for_exercise'),htmlspecialchars($quiz)) ?></h2>

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

      foreach ($resscoreall as $r1) {
          $res1 = array();
          $res1spf = array();

          foreach ($r1 as $date => $r) {
              $textdate = Statistics_timeperiod::format_date($date);
              $res1[]    = "[$date,{$r['percentage']},null,'Date: $textdate<br>Questions: {$r['count']}<br>Per min.: " . round($r['featpermin'],1) . "']";
              $roundpct = round($r['percentage']);
              $res1spf[] = "[$date,{$r['featpermin']},null,'Date: $textdate<br>Correct: $roundpct%']";
          }
          $res2[]    = "[" . implode(",",$res1) . "]";
          $res2spf[] = "[" . implode(",",$res1spf) . "]";
      }

      $resx    = "[" . implode(",", $res2) . "]";
      $resxspf = "[" . implode(",", $res2spf) . "]";

      $featname = array();
      $featpct = array();

      for ($i=0; $i<count($resfeatall); ++$i) {
          for ($j=0; $j<count($resfeatall[$i]); ++$j) {
              if ($i==0) {
                  $rfn = $resfeatall[$i][$j]->rfname;
                  // Localize if possible, otherwise just retain the symbolic feature name
                  $featname[$rfn] = "'" . (isset($featloc->{$rfn}) ? $featloc->{$rfn} : $rfn)   . "'";

                  $featpct[$rfn] = array();
              }
              $featpct[$resfeatall[$i][$j]->rfname][$i] = $resfeatall[$i][$j]->pct;
          }
      }

      // 2 and 70 are experimental values, 15 is the width of each bar
      $canvasheight = max(count($featname)*(2+count($students)*15) + 70,
                          count($featname)*25 + 70);

      $fp1 = array();
      foreach ($featpct as $fp)
          $fp1[] = "[" . implode(",", $fp) . "]";
      $featdata = "[" . implode(",", $fp1) . "]";

      $student_captions = array();
      $ix = 0;
      foreach ($students as $id => $name) {
          $student_captions[$ix] = "'<input type=\"checkbox\" checked name=\"users\" value=\"$ix\">"
              . anchor(build_get('statistics/student_exercise',
                                 array('userid' => $id,
                                       'templ' => $quiz,
                                       'nongraded' => $nongraded ? 'on' : 'off',
                                       'start_date' => $start_date,
                                       'end_date' => $end_date)), addslashes($name))
              . "'";
          ++$ix;
      }
    ?>

    <canvas style="background:#f8f8f8; display:inline-block; vertical-align:top;" id="cvs" width="800" height="500">
      [No canvas support]
    </canvas>
    <div style="display:inline-block; vertical-align:top;">
      <div id="mykey"></div>
      <div id="allkey"><input type="checkbox" style="margin-left:20px" checked name="selectall" value="">All</div>
      <?php if ($nongraded): ?>
        <p style="width:200px">Students marked * include results not intended for grading</p>
      <?php endif; ?>
    </div>

    <canvas style="background:#f8f8f8; display:inline-block; vertical-align:top;" id="cvsspf" width="800" height="500">
      [No canvas support]
    </canvas>

    <h2>Percentage of correct answers by feature</h2>
    <canvas style="background:#f8f8f8; display:inline-block; vertical-align:top;" id="featcanvas" width="800" height="<?= $canvasheight ?>">
    [No canvas support]
    </canvas>

    <script>
      function set_config(config,on,data,colors) {
          config.data = [];
          config.options.colors = [];

          no_of_features = data.length;
          no_of_students = data.length==0 ? 0 : data[0].length;
          if (no_of_students<2)
              $('#allkey').hide();
    
          for (s=0; s<no_of_students; ++s)
              if (on[s])
                  config.options.colors.push(colors[s]);

          for (p=0; p<no_of_features; ++p) {
              config.data.push([]);
              for (s=0; s<no_of_students; ++s)
                  if (on[s])
                      config.data[p].push(data[p][s]);
          }
      }

      $(function() {
          <?php if (count($students)<2): ?>
            $('#allkey').hide();
          <?php endif; ?>
                
          var dataorig = <?= $resx ?>;
          var dataorigspf = <?= $resxspf ?>;

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

          var scatterdata = make_scatterconfig('cvs', RGraph.array_clone(dataorig), <?= $scale_start ?>, <?= $scale_end ?>,
                                             '%', 'Correct', <?= $showweek ? "'(ISO) Week number'" : "'Date'" ?>,
                                             xlabels, <?= $numxticks ?>);
          var scatterdataspf = make_scatterconfig('cvsspf', RGraph.array_clone(dataorigspf), <?= $scale_start ?>, <?= $scale_end ?>,
                                                  null, 'Question items per minute', <?= $showweek ? "'(ISO) Week number'" : "'Date'" ?>,
                                                  xlabels, <?= $numxticks ?>);
          scatterdataspf.options.ymax = null;
          scatterdataspf.options.scaleDecimals = 1;


          scatter = new RGraph.Scatter(scatterdata).draw();
          new RGraph.Scatter(scatterdataspf).on('firstdraw', adaptScale).draw();


          <?php if ($showweek): ?>
              weekno_tooltip('cvs', xlabels, 'Starts\n', weekdates);
              weekno_tooltip('cvsspf', xlabels, 'Starts\n', weekdates);
          <?php else: ?>
              weekno_tooltip('cvs', xlabels, '', daydates);
              weekno_tooltip('cvsspf', xlabels, '', daydates);
          <?php endif; ?>

          var hbarcolors = ['#f00','#0f0','#00f','#0ff','#ff0','#f0f','#000',
                            '#800','#080','#008','#08f','#8f0','#80f','#0f8','#f80','#f08',
                            '#088','#880','#808',
                            '#f88','#8f8','#88f',
                            '#ff8','#f8f','#8ff','#888'];
          var hbaron = [<?php for ($i=0; $i<count($student_captions); ++$i) echo 'true,'; ?>];
          var hbardata = <?= $featdata ?>;
          
          var hbarconfig = make_hbarconfig('featcanvas', null, [<?= implode(",", $featname) ?>], 'Correct', <?= $canvasheight-10 ?>);
          hbarconfig.options.vmarginGrouped = 1;
          hbarconfig.options.vmargin = 5;
          
          set_config(hbarconfig,hbaron,hbardata,hbarcolors);
          new RGraph.HBar(hbarconfig).draw();
          
          RGraph.HTML.Key('mykey', {
              'colors': scatter.Get('colors'),
              'labels': [<?= implode(",", $student_captions) ?> ]
              });
          
          var users_elem = $('input[name="users"]');
          var selectall_elem = $('input[name="selectall"]');

          var cvs = $("#cvs")[0];
          var cvsspf = $("#cvsspf")[0];
          var featcanvas = $("#featcanvas")[0];

          function userchange(e) {
              hbaron[$(this).prop('value')] = $(this).prop('checked');

              var state = 0;  // 0=unknown, 1=on, 2=off, 3=indeterminate
              users_elem.each(function() {
                  switch (state) {
                  case 0:
                      state = $(this).prop('checked') ? 1 : 2;
                      break;

                  case 1:
                      if (!$(this).prop('checked')) {
                          state = 3;
                          return false;
                      }
                      break;

                  case 2:
                      if ($(this).prop('checked')) {
                          state = 3;
                          return false;
                      }
                      break;
                  }
                  return true;
              });

              switch (state) {
              case 0:
              case 1:
                  selectall_elem.prop("indeterminate", false).prop("checked", true);
                  break;

              case 2:
                  selectall_elem.prop("indeterminate", false).prop("checked", false);
                  break;

              case 3:
                  selectall_elem.prop("indeterminate", true).prop("checked", true);
                  break;
              }

              var ix = $(this).prop('value');
              if ($(this).prop('checked')) {
                  scatterdata.data[ix] = RGraph.array_clone(dataorig[ix]);
                  scatterdataspf.data[ix] = RGraph.array_clone(dataorigspf[ix]);
              }
              else {
                  scatterdata.data[ix] = [[]];
                  scatterdataspf.data[ix] = [[]];
              }

              set_config(hbarconfig,hbaron,hbardata,hbarcolors);

              RGraph.reset(cvs);
              RGraph.reset(cvsspf);
              RGraph.reset(featcanvas);

              scatter = new RGraph.Scatter(scatterdata).draw();
              new RGraph.Scatter(scatterdataspf).on('firstdraw', adaptScale).draw();
              new RGraph.HBar(hbarconfig).draw();
          }

          function allchange(e) {
              users_elem
                  .prop("checked", $(this).prop('checked'))
                  .trigger("change");
          }

          users_elem.change(userchange);

          selectall_elem
              .change(allchange)
              .prop("indeterminate", false);


      });
      </script>

  <?php endif; ?>
<?php endif; ?>
