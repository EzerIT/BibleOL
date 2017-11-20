<?php
    $valerr = validation_errors();
    if (!empty($valerr))
        echo "<div class=\"alert alert-danger\">$valerr</div>\n";
?>

<p style="margin-top:10px">
  <a id="showsel" class="label label-primary" href="#"><?= $this->lang->line('show_selector') ?></a>
  <a id="hidesel" class="label label-primary" style="display:none" href="#"><?= $this->lang->line('hide_selector') ?></a>
</p>

<script>
    $(function() {
        $('#showsel').click(
            function() {
                $('#selector').show();
                $('#showsel').hide();
                $('#hidesel').show();
                return false;
            }
        );
        $('#hidesel').click(
            function() {
                $('#selector').hide();
                $('#showsel').show();
                $('#hidesel').hide();
                return false;
            }
        );
    });
</script>
        
<div class="panel panel-default" id="selector" style="display:none">
  <div class="panel-body">
    <?= form_open("statistics/student_time",array('method'=>'get')) ?>
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

      <p>&nbsp;</p>
      <div>
        <span style="font-weight:bold"><?= $this->lang->line('class_prompt') ?></span>
        <select name="classid">
          <option value="0" <?= set_select('classid', 0, true) ?>><?= $this->lang->line('ignore_class') ?></option>
          <?php foreach($classlist as $cl): ?>
            <?php if ($cl->id==$classid) $myclassname = $cl->classname; ?>
            <option value="<?= $cl->id ?>" <?= set_select('classid', $cl->id) ?>><?= htmlspecialchars($cl->classname) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
     
      <p><input class="btn btn-primary" style="margin-top:10px;" type="submit" name="submit" value="<?= $this->lang->line('OK_button') ?>"></p>
    </form>
  </div>
</div>

<script>
    $(function() {
            $(datepicker_period('input[name="start_date"]','input[name="end_date"]'));
        });
</script>

<?php if ($classid==0): ?>
  <h1><?= $this->lang->line('stat_all_ex') ?></h1>
<?php else: ?>
  <h1><?= sprintf($this->lang->line('stat_for_class'), htmlspecialchars($myclassname)) ?></h1>
<?php endif; ?>
  <h1><?= sprintf($this->lang->line('student_is'), htmlspecialchars($user_full_name)) ?></h1>
        
<?php $totaltime = array_sum($total); ?>
<?php if ($totaltime==0): ?>
  <div class="alert alert-warning"><?= $this->lang->line('no_data') ?></div>
<?php else: ?>

  <?php
      function compare_ignorecase(string $s1, string $s2) {
          $s1 = strtolower($s1);
          $s2 = strtolower($s2);
          if ($s1<$s2) return -1;
          if ($s1>$s2) return 1;
          return 0;
      }

      uksort($totaltemp,'compare_ignorecase');
      $totaltempnames = array();
      $totaltempvalues = array();

      $totaltempnames_html = array();
      $totaltempnames_url = array();

      foreach ($totaltemp as $name => $value) {
          $totaltempnames_html[] = "'" . htmlspecialchars($name) . "'";
          $totaltempnames_url[] = "'" . site_url(build_get('statistics/student_exercise',
                                                           array('templ' => $name,
                                                                 'start_date' => $start_date,
                                                                 'end_date' => $end_date,
                                                                 'userid' => $userid
                                                               ))) . "'";
          
          if (strlen($name)>30)
              $name='...' . substr($name,-30);
          $totaltempnames[] = "'$name'";

          $totaltempvalues[] = $value * 60;
      }

      $canvasheight = count($totaltemp)*25 + 70;
  ?>
      
  <h2><?= sprintf($this->lang->line('total_time_spent'), floor($totaltime), round(($totaltime - floor($totaltime))*60)) ?></h2>
  <h2><?= $this->lang->line('time_per_week') ?></h2>
  <canvas style="background:#f8f8f8; display:block;" id="weekcanvas" width="800" height="500">
    [No canvas support]
  </canvas>


  <p style="margin-top:10px">
    <a id="show1" class="label label-primary" href="#"><?= $this->lang->line('show_table') ?></a>
    <a id="hide1" class="label label-primary" style="display:none" href="#"><?= $this->lang->line('hide_table') ?></a>
  </p>
  <div class="table-responsive" id="table1" style="display:none">
  <table class="type2 table table-striped autowidth">
    <caption><?= sprintf($this->lang->line('time_one_student_caption'),htmlspecialchars($user_full_name)) ?></caption>
    <tr>
      <th colspan="<?= count($total) ?>" class="text-center"><?= $this->lang->line('iso_week_no') ?></th>
    </tr>
    <tr>
      <?php 
         foreach ($total as $w => $ignore)
           echo '<th class="text-center">',Statistics_timeperiod::format_week($w),'</th>';
      ?>
    </tr>
    <tr>
      <?php foreach ($total as $w => $h): ?>
        <td><?= sprintf("%d:%02d",floor($h),round(($h-floor($h))*60)) ?></td>
      <?php endforeach; ?>
    </tr>
  </table>
  </div>

  <hr style="margin-top:50px">          
  <h2><?= $this->lang->line('time_per_exercise') ?></h2>
  <canvas style="background:#f8f8f8; display:inline-block; vertical-align:top;" id="excanvas" width="800" height="<?= $canvasheight ?>">
    [No canvas support]
  </canvas>

  <p style="margin-top:10px">
    <a id="show2" class="label label-primary" href="#"><?= $this->lang->line('show_table') ?></a>
    <a id="hide2" class="label label-primary" style="display:none" href="#"><?= $this->lang->line('hide_table') ?></a>
  </p>
  <div class="table-responsive" id="table2" style="display:none">
    <table class="type2 table table-striped autowidth">
      <caption><?= sprintf($this->lang->line('time_per_exercise_caption'),htmlspecialchars($user_full_name)) ?></caption>
      <?php foreach ($totaltemp as $name => $value): ?>
        <tr>
          <td><?= htmlspecialchars($name) ?></td>
          <td><?= sprintf("%d:%02d:%02d",floor($value),($value*60)%60,($value*3600)%60) ?></td>
        </tr>
      <?php endforeach; ?>
    </table>
  </div>

  <script>
    $(function() {
            $('#show1').click(
                function() {
                    $('#table1').show();
                    $('#show1').hide();
                    $('#hide1').show();
                    return false;
                }
                );
            $('#hide1').click(
                function() {
                    $('#table1').hide();
                    $('#show1').show();
                    $('#hide1').hide();
                    return false;
                }
                );
            $('#show2').click(
                function() {
                    $('#table2').show();
                    $('#show2').hide();
                    $('#hide2').show();

                    $("html, body").animate({ scrollTop: $(document).height() }, 1000); // Scroll to bottom
                    
                    return false;
                }
                );
            $('#hide2').click(
                function() {
                    $('#table2').hide();
                    $('#show2').show();
                    $('#hide2').hide();
                    return false;
                }
                );


        var weeklabels = [<?php foreach ($total as $w => $ignore) echo '"',Statistics_timeperiod::format_week($w),'",'; ?>];
        var weekdates =  [<?php foreach ($total as $w => $ignore) echo '"',Statistics_timeperiod::format_date($w),'",'; ?>];

        graph_bar('weekcanvas', [<?= implode(",", $total) ?>], weeklabels,
                  '<?= $this->lang->line('hours') ?>', '<?= $this->lang->line('iso_week_no') ?>');
        weekno_tooltip('weekcanvas', weeklabels, '<?= $this->lang->line('starts') ?>' + '\n', weekdates);
        
        var hbarconf = {
            id: 'excanvas',
            data: [<?= implode(",", $totaltempvalues) ?>],
            options: {
                labels: [<?= implode(",", $totaltempnames) ?>],
                gutterLeftAutosize: true,
                gutterBottom: 45,
                vmargin: 5,
                scaleZerostart: true,
                titleXaxis: '<?= $this->lang->line('minutes') ?>',                  
                titleXaxisY: <?= $canvasheight-10 ?>,
                textAccessible: true
            }
        };
        
        new RGraph.HBar(hbarconf).on('firstdraw', adaptScale).draw();

        var totaltempnames_html = [<?= implode(',', $totaltempnames_html) ?>];
        var totaltempnames_url  = [<?= implode(',', $totaltempnames_url)  ?>];

        for (var i in hbarconf.options.labels) {
            var found = RGraph.text2.find({
                id: 'excanvas',
                text: hbarconf.options.labels[i]
            });

            $(found[0]).wrap('<div title="' + totaltempnames_html[i] + '"><a href="' + totaltempnames_url[i] + '"></a></div>');
            $(found[0]).css({color: 'blue'});
        }

        
    });
  </script>

<?php endif; ?>
