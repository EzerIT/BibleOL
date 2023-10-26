<?php
    $valerr = validation_errors();
    if (!empty($valerr))
        echo "<div class=\"alert alert-danger\">$valerr</div>\n";
    // Load helper for grades
    $this->load->helper('calc_grades_helper');
?>

<?php if ($status!=2): ?>
    <p style="margin-top:10px">
      <a id="showsel" class="badge badge-primary" href="#"><?= $this->lang->line('show_selector') ?></a>
      <a id="hidesel" class="badge badge-primary" style="display:none" href="#"><?= $this->lang->line('hide_selector') ?></a>
    </p>

    <script>
        $(function() {
            $('#showsel').click(
                function() {
                    $('#selector').show();
                    $('#showsel').hide();
                    $('#hidesel').show();

                    legend_adjust($('#leftpanel'), $('#centerpanel'));

                    return false;
                }
            );
            $('#hidesel').click(
                function() {
                    $('#selector').hide();
                    $('#showsel').show();
                    $('#hidesel').hide();

                    legend_adjust($('#leftpanel'), $('#centerpanel'));

                    return false;
                }
            );
        });
    </script>
<?php else: ?>
    <h1><?= sprintf($this->lang->line('exam_grades_for_class'), htmlspecialchars($classname)) ?></h1>
<?php endif; ?>

<div class="card mb-3" id="selector" <?= $status==2 ? '' : 'style="display:none"' ?>>
  <div class="card-body">
    <?= form_open("grades/teacher_exam",array('method'=>'get')) ?>
      <input type="hidden" name="classid" value="<?= $classid ?>">

      <p>&nbsp;</p>
      <div>
        <span style="font-weight:bold"><?= $this->lang->line('exam_prompt') ?></span>
        <select name="exam">
          <option value="" <?= set_select('exam', '', true) ?>></option>
          <?php foreach($exam_list as $ex): ?>
            <?php $ex2 = htmlspecialchars($ex["name"]); ?>
            <option value="<?= $ex["id"] ?>" <?= set_select('exam', $ex["id"]) ?>><?= $ex2 ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <?php
      global $arrayOfGradeSchemes;
      loadArrayOfGradeSchemes();
      ?>
      <p>&nbsp;</p>
      <div>
        <span style="font-weight:bold"><?= $this->lang->line('grade_system_prompt') ?></span>
        <select name="grade_system">
          <!-- <option value="" <?= set_select('grade_system', '', true) ?>></option> -->
          <?php foreach($arrayOfGradeSchemes as $gs => $gs_array): ?>
            <?php $gs2 = htmlspecialchars($gs);
            $gs_name = htmlspecialchars($gs_array["SchemeName"]); ?>
            <option value="<?= $gs2 ?>" <?= set_select('grade_system', $gs) ?>><?= $gs_name ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <BR>

      <div class="row">
        <div class="form-group col">
          <label for="max_time" class="col-form-label"><?= $this->lang->line('show_max_time_prompt') ?></label>
          <input class="text" id="max_time" name="max_time" value="0" type="text" >
        </div>
      </div>

      <!-- <div class="row">
        <div class="form-group col">
          <label for="nongraded" class="col-form-label"><?= $this->lang->line('show_non_graded_prompt') ?></label>
          <input class="checkbox" id="nongraded" name="nongraded" value="on" type="checkbox" <?= set_checkbox('nongraded','on') ?>>
        </div>
      </div> -->

      <p><input class="btn btn-primary" style="margin-top:10px;" type="submit" name="submit" value="<?= $this->lang->line('OK_button') ?>"></p>
    </form>
  </div>
</div>

<!-- <script>
    $(function() {
            $(datepicker_period('input[name="start_date"]','input[name="end_date"]'));
        });
</script> -->


<?php if ($status!=2): ?>
  <h1><?= sprintf($this->lang->line('exam_grades_for_class'), htmlspecialchars($classname)) ?></h1>
  <BR>

  <?php if ($status==0): ?>

      <div class="alert alert-warning"><?= $this->lang->line('no_data') ?></div>

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
              $res1[]    = "[$date,{$r['percentage']},null,'{$this->lang->line('date_colon')} $textdate<br>{$this->lang->line('question_count_colon')} {$r['count']}<br>{$this->lang->line('per_min')} " . round($r['featpermin'],1) . "']";
              $roundpct = round($r['percentage']);
              $res1spf[] = "[$date,{$r['featpermin']},null,'{$this->lang->line('date_colon')} $textdate<br>{$this->lang->line('correct_colon')} $roundpct%']";
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
          $student_captions[$ix] = "'<input type=\"checkbox\" checked name=\"users\" value=\"$ix\">&nbsp;"
              . anchor(build_get('grades/student_exercise',
                                 array('userid' => $id,
                                       'templ' => $quiz,
                                       'nongraded' => $nongraded ? 'on' : 'off',
                                       'start_date' => $start_date,
                                       'end_date' => $end_date)), addslashes($name))
              . "'";
          ++$ix;
      }
    ?>

    <div style="display:none">
      <h2><?= $this->lang->line('hgst_pct_correct_by_date') ?></h2>
      <canvas style="background:#f8f8f8; display:inline-block; vertical-align:top;" id="cvs" width="800" height="500">
        [No canvas support]
      </canvas>

      <hr style="margin-top:20px">
      <h2><?= $this->lang->line('hgst_speed_by_date') ?></h2>
      <canvas style="background:#f8f8f8; display:inline-block; vertical-align:top;" id="cvsspf" width="800" height="500">
        [No canvas support]
      </canvas>
    </div>


    <p style="margin-top:10px">
          <a id="show1" class="badge badge-primary" style="display:none" href="#"><?= $this->lang->line('show_table') ?></a>
          <!-- <a id="hide1" class="badge badge-primary" href="#"><?= $this->lang->line('hide_table') ?></a> -->
    </p>
    <BR>
    <div class="table-responsive" id="table1" style="display:block">
      <h2><?= sprintf($this->lang->line('grades_for_exam'),htmlspecialchars($quiz)) ?></h2>
      <table class="type2 table table-striped autowidth">
        <caption><?= $this->lang->line('grds_exam_by_student_caption') ?></caption>
        <tr>
          <th><?= $this->lang->line('student') ?></th>
          <th class="text-center"><?= $this->lang->line('date') ?></th>
          <th class="text-center"><?= $this->lang->line('correct') ?></th>
          <th class="text-center"><?= $this->lang->line('quiz_grade') ?></th>
          <th class="text-center"><?= $this->lang->line('best_total_time') ?></th>
          <th class="text-center"><?= $this->lang->line('hgst_avr_per_qi') ?></th>
          <th></th>
        </tr>
        <?php reset($students);
              $st = current($students);
              if ( empty($max_time) ) {
                $max_time = 3600; // Sets to 1h, which virtually disables the feature
              }
              ?>
        <?php $hiddenStyles = array();
        foreach ($resscoreall_ind as $ra): ?>
          <?PHP $lineId = 0; $stk = str_replace(" ", "__", $st); $hiddenStyles["$stk"] = ".{$stk}_hiddenDetails {
            visibility: collapse;
          }
          ";

          //Reset the counters each student
          $tot_percent = 0;
          $tot_percWeighted = 0;
          $tot_grade = 0;
          $tot_gradeWeighted = 0;
          $startTime = 0;
          $ncounter = 0;
          $tot_weight = 0;
          $tot_duration = 0;
          $tot_featpMin = 0;
          ?>
        <?php foreach ($ra as $time => $result): ?>
          <?php
          // Prepares data
          $ncounter += 1;
          if ( $startTime == 0 ) {
            $startTime = $time;
          }
          $tot_duration += $result["duration"];
          $tot_featpMin += $result['featpermin'] <= 0 ? -1 : 60/$result['featpermin'];
          $tot_weight += $result['weight'];
          $tot_percent += $result['percentage'];
          $tot_percWeighted += $result['percentage'] * $result['weight'];
          if ( round(60/$result['featpermin'])<=$max_time ) {
            // if the user took less than the max alloted time per question
            $tot_grade += $result['percentage'];
            $tot_gradeWeighted += $result['percentage'] * $result['weight'];
          }
          ?>
        <?php endforeach; ?>
        <tr class="<?php echo 'headerDet';  ?>">
          <td><?= $st ?></td>
          <td class="text-center"><?= Statistics_timeperiod::format_time($startTime) ?></td>
          <td class="text-center"><?= round($tot_percWeighted/$tot_weight) . "% (" .  round($tot_percent/$ncounter)  ?>%)</td>
          <!-- <td class="text-center"><?php echo calculateGrade($grade_system, ($tot_percWeighted/$tot_weight));?></td> -->
          <td class="text-center"><?= anchor(build_get('grades/teacher_quizz_detail/classid/' . $classid . '/quizzid/'.$result["quizzid"] . '/userid/'.$result["userid"], array() ), (round(60/$result['featpermin'])<=$max_time)?calculateGrade($grade_system, ($tot_percWeighted/$tot_weight)):calculateGrade($grade_system, 0)) ?></td>
          <td class="text-center"><?= $result["duration"] ?></td>
          <td class="text-center"><?= $tot_featpMin > 0 ? sprintf("%.1f",round(60/($tot_featpMin/$ncounter))) : "" ?></td>
          <td class="text-center">
              <a id="det_<?php echo $stk;?>" class="badge badge-primary" href="#"><?= $this->lang->line('detail') ?></a>
          </td>
        </tr>

        <?php
        // Print the exercise pieces
        foreach ($ra as $time => $result): ?>
        <tr class="<?php echo "{$stk}_hiddenDetails";  ?>">
          <td>>>> <?= $result["exercise_name"] ?></td>
          <td class="text-center"><?= Statistics_timeperiod::format_time($time) ?></td>
          <td class="text-center"><?= round($result['percentage']) ?>%</td>
          <!-- <td class="text-center"><?= (round(60/$result['featpermin'])<=$max_time)?calculateGrade($grade_system, $result['percentage']):calculateGrade($grade_system, 0) ?></td> -->
          <td class="text-center"><?= anchor(build_get('grades/teacher_quizz_detail/classid/' . $classid . '/quizzid/'.$result["quizzid"] . '/userid/'.$result["userid"], array() ), (round(60/$result['featpermin'])<=$max_time)?calculateGrade($grade_system, $result['percentage']):calculateGrade($grade_system, 0)) ?></td>
          <td class="text-center"><?= $result["duration"] ?></td>
          <td class="text-center"><?= sprintf("%.1f",round(60/$result['featpermin'])) ?></td>
          <td class="text-center"></td>
        </tr>
        <?php endforeach; ?>
        <?php $st = next($students); ?>
        <?php endforeach; ?>
      </table>
      <?php if ($nongraded): ?>
        <p><?= $this->lang->line('students_marked_star') ?></p>
      <?php endif; ?>
    </div>
    <style>
    <?php foreach ($hiddenStyles as $key => $value) {
      // print each style
      echo $value;
    }
    ?>
    </style>


    <hr style="margin-top:10px">
    <h2><?= $this->lang->line('pct_correct_by_feature') ?></h2>
    <canvas style="background:#f8f8f8; display:inline-block; vertical-align:top;" id="featcanvas" width="800" height="<?= $canvasheight ?>">
    [No canvas support]
    </canvas>

    <p style="margin-top:10px">
          <a id="show2" class="badge badge-primary" href="#"><?= $this->lang->line('show_table') ?></a>
          <a id="hide2" class="badge badge-primary" style="display:none" href="#"><?= $this->lang->line('hide_table') ?></a>
    </p>
    <div class="table-responsive" id="table2" style="display:none">
      <table class="type2 table table-striped autowidth">
        <caption><?= $this->lang->line('features_by_student_caption') ?></caption>
        <tr>
          <th><?= $this->lang->line('feature') ?></th>
          <th><?= $this->lang->line('student') ?></th>
          <th class="text-center"><?= $this->lang->line('correct') ?></th>
        </tr>
        <?php foreach ($featpct as $fn => $fp): ?>
        <?php reset($students);
              $st = current($students); ?>
        <?php foreach ($fp as $pct): ?>
        <tr>
          <td><?= isset($featloc->{$fn}) ? $featloc->{$fn} : $fn ?></td>
          <td><?= $st ?></td>
          <td class="text-center"><?= round($pct) ?>%</td>
        </tr>
        <?php $st = next($students); ?>
        <?php endforeach; ?>
        <?php endforeach; ?>
      </table>
      <?php if ($nongraded): ?>
        <p><?= $this->lang->line('students_marked_star') ?></p>
      <?php endif; ?>
    </div>




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

          <?php
          // one function for each button
          foreach ($hiddenStyles as $key => $value) {
            // print each style
            echo "
            $('#det_" . $key . "').click(
              function() {
                if ($('." . $key . "_hiddenDetails').css('visibility')=='visible') {
                  $('." . $key . "_hiddenDetails').css('visibility',' collapse');
                  $('#det_" . $key . "').text('" . $this->lang->line('detail') . "')
                }
                else {
                  $('." . $key . "_hiddenDetails').css('visibility',' visible');
                  $('#det_" . $key . "').text('" . $this->lang->line('hide_detail') . "')
                }

                //legend_adjust($('#leftpanel'), $('#centerpanel'));

                return false;
              }
            );
            ";
          }
          ?>

          $('#show1').click(
              function() {
                  $('#table1').show();
                  $('#show1').hide();
                  $('#hide1').show();

                  legend_adjust($('#leftpanel'), $('#centerpanel'));

                  return false;
              }
              );
          $('#hide1').click(
              function() {
                  $('#table1').hide();
                  $('#show1').show();
                  $('#hide1').hide();

                  legend_adjust($('#leftpanel'), $('#centerpanel'));

                  return false;
              }
              );
          $('#show2').click(
              function() {
                  $('#table2').show();
                  $('#show2').hide();
                  $('#hide2').show();

                  $("html, body").animate({ scrollTop: $(document).height() }, 1000); // Scroll to bottom

                  legend_adjust($('#leftpanel'), $('#centerpanel'));

                  return false;
              }
              );
          $('#hide2').click(
              function() {
                  $('#table2').hide();
                  $('#show2').show();
                  $('#hide2').hide();

                  legend_adjust($('#leftpanel'), $('#centerpanel'));

                  return false;
              }
              );

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
                                             '%', '<?= $this->lang->line('correct') ?>', '<?= $showweek ? $this->lang->line('iso_week_no') : $this->lang->line('date') ?>',
                                             xlabels, <?= $numxticks ?>);
          var scatterdataspf = make_scatterconfig('cvsspf', RGraph.array_clone(dataorigspf), <?= $scale_start ?>, <?= $scale_end ?>,
                                                  null, '<?= $this->lang->line('question_items_per_min') ?>', '<?= $showweek ? $this->lang->line('iso_week_no') : $this->lang->line('date') ?>',
                                                  xlabels, <?= $numxticks ?>);
          scatterdataspf.options.ymax = null;
          scatterdataspf.options.scaleDecimals = 1;


          scatter = new RGraph.Scatter(scatterdata).draw();
          new RGraph.Scatter(scatterdataspf).on('firstdraw', adaptScale).draw();


          <?php if ($showweek): ?>
              weekno_tooltip('cvs', xlabels, '<?= $this->lang->line('starts') ?>' + '\n', weekdates);
              weekno_tooltip('cvsspf', xlabels, '<?= $this->lang->line('starts') ?>' + '\n', weekdates);
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

          var hbarconfig = make_hbarconfig('featcanvas', null, [<?= implode(",", $featname) ?>], '<?= $this->lang->line('correct') ?>', <?= $canvasheight-10 ?>);
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

          // Make floating legend
          $('#extraleft').css('position','sticky').css('top','10px');

          fix_legend_height($('#leftpanel'), $('#centerpanel'));

          });
      </script>

  <?php endif; ?>
<?php endif; ?>
