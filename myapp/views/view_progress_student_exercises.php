<?php
    $valerr = validation_errors();
    if (!empty($valerr))
        echo "<div class=\"alert alert-danger\">$valerr</div>\n";
?>

   <?= form_open("statistics/student_exercise",array('method'=>'get')) ?>
    <input type="hidden" name="userid" value="<?= $userid ?>">

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

  <input type="hidden" name="templ" value="<?= $quiz ?>">
  <input type="hidden" name="userid" value="<?= $userid ?>">
            
  <p><input class="btn btn-primary" style="margin-top:10px;" type="submit" name="submit" value="<?= $this->lang->line('OK_button') ?>"></p>
</form>

  <script>
        $(datepicker_period('input[name="start_date"]','input[name="end_date"]'));
  </script>

  <h1>Statistics for exercise &ldquo;<?= htmlspecialchars($quiz) ?>&rdquo;</h1>
  <h1>Student is <?= htmlspecialchars($user_full_name) ?></h1>

  <h1>HUSK: Valg af med/uden grading</h1>

<?php if ($status!=2): ?>

  <?php if ($status==0): ?>

    <h2>No data</h2>

  <?php else: ?>

    <?php
      $res = array();
      $resspf = array();
      foreach ($resscore as $date => $r) {
          $res[]    = "['$date',{$r['percentage']},null,'Date: $date<br>Questions: {$r['count']}<br>Per min.: " . round($r['featpermin'],1) . "']";
          $roundpct = round($r['percentage']);
          $resspf[] = "['$date',{$r['featpermin']},null,'Date: $date<br>Correct: $roundpct%']";
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

    <h2>Percentage of correct answers by date</h2>
    <canvas style="background:#f8f8f8; display:inline-block; vertical-align:top;" id="cvs" width="800" height="500">
      [No canvas support]
    </canvas>

    <h2>Answering speed by date</h2>
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

      $(function() {
          var scatterdata = {
              id: 'cvs',
              data: <?= $resx ?>,
              options: {
                  xmin: '<?= $start_date ?>',
                  xmax: '<?= $end_date ?>',
                  gutterLeft: 70,
                  gutterBottom: 45,
                  tickmarks:  myTick,
                  ymin: 0,
                  ymax: 100,
                  shadow: false,
                  unitsPost: '%',
                  titleYaxis: 'Correct',
                  titleYaxisX: 12,
                  titleXaxis: 'Week number',
                  titleXaxisY: 490,
                  textAccessible: true,

                  line: true,
                  lineLinewidth: 2,
                  lineColors: ['#f00'],
                  labels: [<?php for ($w=$minweek; $w<=$maxweek; ++$w) echo '"',Statistics_timeperiod::format_week($w),'",'; ?>],
                  numxticks: <?= $maxweek-$minweek+1 ?>,
              }
          };

          var scatterdataspf = $.extend(true, {}, scatterdata);  // This is a deep copy
          scatterdataspf.id = 'cvsspf';
          scatterdataspf.data = <?= $resxspf ?>;
          scatterdataspf.options.titleYaxis = 'Question items per minute';//'Seconds per question item';
          scatterdataspf.options.unitsPost = null;
          scatterdataspf.options.ymax = null;
          scatterdataspf.options.scaleDecimals = 1;

          scatter = new RGraph.Scatter(scatterdata).draw();
          scatterspf = new RGraph.Scatter(scatterdataspf).on('firstdraw', adaptScale).draw();

          var hbarconf = {
            id: 'featcanvas',
            data: [<?= implode(",", $featpct) ?>],
            options: {
                labels: [<?= implode(",", $featname) ?>],
                gutterLeftAutosize: true,
                gutterBottom: 45,
                vmargin: 5,
                scaleZerostart: true,
                xmin: 0,
                xmax: 100,
                unitsPost: '%',
                titleXaxis: 'Correct',
                titleXaxisY: <?= $canvasheight-10 ?>,
                textAccessible: true
            }
        };
        
        new RGraph.HBar(hbarconf).draw();

//        var totaltempnames_html = [< ?= implode(',', $totaltempnames_html) ? >];
//        var totaltempnames_url  = [< ?= implode(',', $totaltempnames_url)  ? >];

//        for (var i in hbarconf.options.labels) {
//            var found = RGraph.text2.find({
//                id: 'featcanvas',
//                text: hbarconf.options.labels[i]
//            });
// 
//            $(found[0]).wrap('<div title="' + totaltempnames_html[i] + '"><a href="' + totaltempnames_url[i] + '"></a></div>')
//            $(found[0]).css({color: 'blue'});
//        }


      });
      </script>

  <?php endif; ?>
<?php endif; ?>
