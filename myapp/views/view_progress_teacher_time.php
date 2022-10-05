<?php
    $valerr = validation_errors();
    if (!empty($valerr))
        echo "<div class=\"alert alert-danger\">$valerr</div>\n";
?>

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

<div class="card mb-3" id="selector" style="display:none">
  <div class="card-body">
    <?= form_open("statistics/teacher_time",array('method'=>'get')) ?>
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
   
      <p><input class="btn btn-primary" style="margin-top:10px;" type="submit" name="submit" value="<?= $this->lang->line('OK_button') ?>"></p>
    </form>
  </div>
</div>  


  <script>
      $(function() {
              $(datepicker_period('input[name="start_date"]','input[name="end_date"]'));
          });
  </script>

  <h1><?= sprintf($this->lang->line('stat_for_class'), htmlspecialchars($classname)) ?></h1>

  <?php if ($status==1): ?>
  <?php if (array_sum($total)==0): ?>

    <div class="alert alert-warning"><?= $this->lang->line('no_data') ?></div>

  <?php else: ?>

  <?php
      $durx = array();
      foreach ($dur as $w => $val)
          $durx[$w] = '[' . implode(',',$val) . ']';
   
      $student_captions = array();
      $ix = 0;
      foreach ($students as $id => $name) {
          $student_captions[$ix] = "'<input type=\"checkbox\" checked name=\"users\" value=\"$ix\">&nbsp;"
              . anchor(build_get('statistics/student_time',
                                 array('userid' => $id,
                                       'start_date' => $start_date,
                                       'end_date' => $end_date,
                                       'classid' => $classid)), addslashes($name))
              . "'";
          ++$ix;
      }
  ?>
      
  <h2><?= $this->lang->line('time_by_all_students') ?></h2>
  <canvas style="background:#f8f8f8; display:block;" id="totalcanvas" width="800" height="500">
    [No canvas support]
  </canvas>
          
          
  <hr style="margin-top:50px">          
  <h2><?= $this->lang->line('time_by_each_students') ?></h2>
  <canvas style="background:#f8f8f8; display:inline-block; vertical-align:top;" id="studentscanvas" width="800" height="500">
    [No canvas support]
  </canvas>

  
  <p style="margin-top:10px">
          <a id="show1" class="badge badge-primary" href="#"><?= $this->lang->line('show_table') ?></a>
          <a id="hide1" class="badge badge-primary" style="display:none" href="#"><?= $this->lang->line('hide_table') ?></a>
  </p>
  <div class="table-responsive" id="table1" style="display:none">
  <table class="type2 table table-striped autowidth">
    <caption><?= $this->lang->line('time_by_student_caption') ?></caption>
    <tr>
      <th><?= $this->lang->line('iso_week_no') ?></th>
      <?php 
         foreach ($total as $w => $ignore)
           echo '<th class="text-center">',Statistics_timeperiod::format_week($w),'</th>';
      ?>
    </tr>
    <?php foreach ($students as $id=>$name): ?>
      <tr>
        <td><?= $name ?></td>
        <?php foreach ($dur as $w => $st): ?>
          <?php $h = $st[$id]; ?>
          <td class="text-center"><?= sprintf("%d:%02d",floor($h),round(($h-floor($h))*60)) ?></td>
        <?php endforeach; ?>
      </tr>
    <?php endforeach; ?>
    <tr>
      <td><?= $this->lang->line('total') ?></td>
      <?php foreach ($total as $w => $h): ?>
        <td class="text-center"><?= sprintf("%d:%02d",floor($h),round(($h-floor($h))*60)) ?></td>
      <?php endforeach; ?>
    </tr>
  </table>
  </div>


  <script>
    function set_config(config,on,data,colors) {
        config.data = [];
        config.options.colors = [];

        no_of_periods = data.length;
        no_of_students = data.length==0 ? 0 : data[0].length;
        if (no_of_students<2)
            $('#allkey').hide();

        
        for (s=0; s<no_of_students; ++s)
            if (on[s])
                config.options.colors.push(colors[s]);

        for (p=0; p<no_of_periods; ++p) {
            config.data.push([]);
            for (s=0; s<no_of_students; ++s)
                if (on[s])
                    config.data[p].push(data[p][s]);
        }
    }




    $(function() {
       $('#show1').click(
           function() {
               $('#table1').show();
               $('#show1').hide();
               $('#hide1').show();

               $("html, body").animate({ scrollTop: $(document).height() }, 1000); // Scroll to bottom

               legend_adjust($('#leftpanel'), $('#centerpanel'));
               
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

        var weeklabels = [<?php foreach ($total as $w => $ignore) echo '"',Statistics_timeperiod::format_week($w),'",'; ?>];
        var weekdates =  [<?php foreach ($total as $w => $ignore) echo '"',Statistics_timeperiod::format_date($w),'",'; ?>];

        graph_bar('totalcanvas', [<?= implode(",", $total) ?>], weeklabels, '<?= $this->lang->line('hours') ?>', '<?= $this->lang->line('iso_week_no') ?>');
        
        var bar2colors = ['#f00','#0f0','#00f','#0ff','#ff0','#f0f','#000',
                          '#800','#080','#008','#08f','#8f0','#80f','#0f8','#f80','#f08',
                          '#088','#880','#808',
                          '#f88','#8f8','#88f',
                          '#ff8','#f8f','#8ff','#888'];
        var bar2on = [<?php for ($i=0; $i<count($student_captions); ++$i) echo 'true,'; ?>];
        var bar2data = [<?= implode(",", $durx) ?>];

        var studentscanvas = $("#studentscanvas")[0];
    
        var bar2config = {
            id: 'studentscanvas',
            data: null,
            options: {
                labels: weeklabels,
                colors: null,
                gutterLeft: 55,
                gutterBottom: 45,
                hmargin: 7,
                hmarginGrouped: 1,
                titleYaxis: '<?= $this->lang->line('hours') ?>',                  
                titleYaxisX: 12,                  
                titleXaxis: '<?= $this->lang->line('iso_week_no') ?>',                  
                titleXaxisY: 490,
                textAccessible: true
            }
        };

        set_config(bar2config,bar2on,bar2data,bar2colors);
        var bar2  = new RGraph.Bar(bar2config).on('firstdraw', adaptScale).draw();

        RGraph.HTML.Key('mykey', {
            'colors': bar2.Get('colors'),
            'labels': [<?= implode(",", $student_captions) ?> ]
        });


        var users_elem = $('input[name="users"]');
        var selectall_elem = $('input[name="selectall"]');

        weekno_tooltip('totalcanvas', weeklabels, '<?= $this->lang->line('starts') ?>' + '\n', weekdates);
        weekno_tooltip('studentscanvas', weeklabels, '<?= $this->lang->line('starts') ?>' + '\n', weekdates);

        function userchange(e) {
            bar2on[$(this).prop('value')] = $(this).prop('checked');

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
            
            set_config(bar2config,bar2on,bar2data,bar2colors);
            RGraph.reset(studentscanvas);
            bar2  = new RGraph.Bar(bar2config).on('firstdraw', adaptScale).draw();
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
