<?php
    $valerr = validation_errors();
    if (!empty($valerr))
        echo "<div class=\"alert alert-danger\">$valerr</div>\n";
?>

<div class="card">
    <?= form_open("statistics/teacher_exercises",array('method'=>'get')) ?>
    <input type="hidden" name="classid" value="<?= $classid ?>">

    <p>Specify date period (in the UTC time zone):</p>
    <table>
      <tr>
        <td style="font-weight:bold;padding-right:5px;padding-left:20px;">From:</td>
        <td style="padding-left:5px"><input type="text" name="start_date" value="<?= $start_date ?>"></td>
      </tr>
      <tr>
        <td style="font-weight:bold;padding-right:5px;padding-left:20px;">To (and including):</td>
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
  <h2>Statistics for exercise &ldquo;<?= htmlspecialchars($quiz) ?>&rdquo;</h2>

  <?php if ($status==0): ?>

    <h2>No data</h2>

  <?php else: ?>

    <?php
      foreach ($resscoreall as $r1) {
          $res1 = array();
          $res1spf = array();

          foreach ($r1 as $date => $r) {
              $res1[]    = "['$date',{$r['percentage']},null,'Date: $date<br>Questions: {$r['count']}<br>Per min.: " . round($r['featpermin'],1) . "']";
              $roundpct = round($r['percentage']);
              $res1spf[] = "['$date',{$r['featpermin']},null,'Date: $date<br>Correct: $roundpct%']";
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
    </div>

    <canvas style="background:#f8f8f8; display:inline-block; vertical-align:top;" id="cvsspf" width="800" height="500">
      [No canvas support]
    </canvas>

    <h2>Percentage of correct answers by feature</h2>
    <canvas style="background:#f8f8f8; display:inline-block; vertical-align:top;" id="featcanvas" width="800" height="<?= $canvasheight ?>">
    [No canvas support]
    </canvas>

    <script>
      function pad(number) {
          return number<10 ? '0'+number : number;
      }

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

      function adaptScale(obj, e) {
          // Change number of decimals on y axis depending on max value
          if (obj.scale2.max < 0.05)
              obj.set('scaleDecimals', 3);
          else if (obj.scale2.max < 0.5)
              obj.set('scaleDecimals', 2);
          else if (obj.scale2.max < 5)
              obj.set('scaleDecimals', 1);
          else
              obj.set('scaleDecimals', 0);

          this.firstDraw=false; // Prevent firstdraw event from firing again. (Probably bug in RGraph.)
          RGraph.redraw();
      }

      $(function() {
          <?php if (count($students)<2): ?>
            $('#allkey').hide();
          <?php endif; ?>
                
          var dataorig = <?= $resx ?>;
          var dataorigspf = <?= $resxspf ?>;
          var scatterdata = {
              id: 'cvs',
              data: null,
              options: {
                  xmin: '<?= $scale_start ?>',
                  xmax: '<?= $scale_end ?>',
                  gutterLeft: 70,
                  gutterBottom: 45,
                  tickmarks:  myTick,
                  ymin: 0,
                  ymax: 100,
                  shadow: false,
                  unitsPost: '%',
                  titleYaxis: 'Correct',
                  titleYaxisX: 12,
                  titleXaxis: <?= $showweek ? "'(ISO) Week number'" : "'Date'" ?>,
                  titleXaxisY: 490,
                  textAccessible: true,

                  line: true,
                  lineLinewidth: 2,
                  lineColors: ['#f00','#0f0','#00f','#0ff','#ff0','#f0f','#000',
                                   '#800','#080','#008','#08f','#8f0','#80f','#0f8','#f80','#f08',
                                   '#088','#880','#808',
                                   '#f88','#8f8','#88f',
                                   '#ff8','#f8f','#8ff','#888'],
                  labels: [<?php
                           $numxticks = 0;
                           if ($showweek) {
                               for ($w=$minpoint; $w<=$maxpoint; ++$w) {
                                   echo '"',Statistics_timeperiod::format_week($w),'",';
                                   ++$numxticks;
                               }
                           }
                           else {
                               for ($ut=$minpoint; $ut<=$maxpoint; $ut += 24*3600) {
                                   echo '"',Statistics_timeperiod::format_day($ut),'",';
                                   ++$numxticks;
                               }
                           }
                           ?>],
                  numxticks: <?= $numxticks ?>,
              }
          };

          var scatterdataspf = $.extend(true, {}, scatterdata);  // This is a deep copy
          scatterdataspf.id = 'cvsspf';
          scatterdataspf.options.titleYaxis = 'Question items per minute';//'Seconds per question item';
          scatterdataspf.options.unitsPost = null;
          scatterdataspf.options.ymax = null;
          scatterdataspf.options.scaleDecimals = 1;

          scatterdata.data = RGraph.array_clone(dataorig);
          scatterdataspf.data = RGraph.array_clone(dataorigspf);
          scatter = new RGraph.Scatter(scatterdata).draw();
          new RGraph.Scatter(scatterdataspf).on('firstdraw', adaptScale).draw();


          var hbarcolors = ['#f00','#0f0','#00f','#0ff','#ff0','#f0f','#000',
                            '#800','#080','#008','#08f','#8f0','#80f','#0f8','#f80','#f08',
                            '#088','#880','#808',
                            '#f88','#8f8','#88f',
                            '#ff8','#f8f','#8ff','#888'];
          var hbaron = [<?php for ($i=0; $i<count($student_captions); ++$i) echo 'true,'; ?>];
          var hbardata = <?= $featdata ?>;

          
          var hbarconfig = {
              id: 'featcanvas',
              data: null,
              options: {
                  labels: [<?= implode(",", $featname) ?>],
                  gutterLeftAutosize: true,
                  gutterBottom: 45,
                  scaleZerostart: true,
                  xmin: 0,
                  xmax: 100,
                  vmarginGrouped: 1,
                  vmargin: 5,
                  colors: null,
                  unitsPost: '%',
                  titleXaxis: 'Correct',
                  titleXaxisY: <?= $canvasheight-10 ?>,
                  textAccessible: true
              }
          };

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


          /**
           * The function that is called once per tickmark, to draw it
           *
           * @param object obj           The chart object
           * @param object data          The chart data
           * @param number x             The X coordinate
           * @param number y             The Y coordinate
           * @param number xVal          The X value
           * @param number yVal          The Y value
           * @param number xMax          The maximum X scale value
           * @param number xMax          The maximum Y scale value
           * @param string color         The color of the tickmark
           * @param string dataset_index The index of the data (which starts at zero
           * @param string data_index    The index of the data in the dataset (which starts at zero)
           */
          function myTick (obj, data, x, y, xVal, yVal, xMax, yMax, color, dataset_index, data_index) {
              co = document.getElementById(obj.canvas.id).getContext('2d');
              co.strokeStyle = color;
              co.fillStyle = obj.original_colors['chart.line.colors'][dataset_index];
              co.fillRect(x-4, y-4, 8, 8);
          }

      });
      </script>

  <?php endif; ?>
<?php endif; ?>
