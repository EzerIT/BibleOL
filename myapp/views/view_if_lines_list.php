<?php

function build_url($get_parms)
{
    return site_url('translate?' . http_build_query($get_parms));
}
?>

<script>
    String.prototype.format = function() {
        var args = arguments;
        // Replace {3} or %7B3%7D with argument number 3
        return this.replace(/({|%7B)(\d+)(}|%7D)/g, function(match, dummy2, num, dummy2) {
            return typeof args[num] != 'undefined'
                ? args[num]
                : match;
        });
    };

    $(function() {
        $('#textgroupsel')
            .on('change', function() {
                    <?php $textgroupsel_parms = $get_parms;
                          $textgroupsel_parms['offset'] = 0;
                          $textgroupsel_parms['textgroup'] = '{0}';
                    ?>
                    document.location='<?= build_url($textgroupsel_parms) ?>'.format($(this).val());
                });

        $('#langeditsel')
            .on('change', function() {
                    <?php $langeditsel_parms = $get_parms;
                          $langeditsel_parms['lang_edit'] = '{0}';
                    ?>
                    document.location='<?= build_url($langeditsel_parms) ?>'.format($(this).val());
                });

        $('#langshowsel')
            .on('change', function() {
                    <?php $langshowsel_parms = $get_parms;
                          $langshowsel_parms['lang_show'] = '{0}';
                    ?>
                    document.location='<?= build_url($langshowsel_parms) ?>'.format($(this).val());
                });


        
        $(".textinput")
            .on('input',function() {
                    $(".revertbutton[data-name=" + $(this).attr('name') + "]").show();
                    $("input[name=modif-" + $(this).attr('name') + "]").val('true');
                    $("input[name=submit]").prop('disabled',false);
                })
            .each(function() {
                    $(this).attr('data-orig-value', $(this).val());
                });

        function revert(name) {
            var ifield = $('.textinput[name=' + name + ']');
            ifield.val(ifield.attr('data-orig-value'));
            $(".revertbutton[data-name=" + name + "]").hide();
            $("input[name=modif-" + name + "]").val('false');
        }

        
        $('.revertbutton')
            .click(
                function() {
                    revert($(this).attr('data-name'));

                    if ($('.modif-indicator[value=true]').length==0) // Nothing has been modified
                        $("input[name=submit]").prop('disabled',true);

                    return false;
                }
                )
            .hide();
        
        $('.revert-all')
            .click(
                function() {
                    $('.revertbutton').each(
                        function() {
                            var name = $(this).attr('data-name');
                            if ($("input[name=modif-" + name + "]").val()=='true')
                                revert(name);
                        });
                    $("input[name=submit]").prop('disabled',true);
                    return false;
                }
                );
        
        $("input[name=submit]")
            .prop('disabled',true);

        var is_submitting = false;
        $("input[name=submit]")
            .click(function() {
                    is_submitting = true;
                });
        
        $(window)
            .on('beforeunload', function() {
                    if (!is_submitting && $('.modif-indicator[value=true]').length>0)
                        return 'You haven\'t saved your changes.';
                });

        });
</script>


<p><strong>Edit Language:</strong>

<select id="langeditsel">
  <?php foreach ($lang_list as $lshort => $llong): ?>
     <option value="<?= $lshort ?>" <?= $lshort==$get_parms['lang_edit'] ? 'selected="selected"' : '' ?>><?= $llong ?></option>
  <?php endforeach; ?>
</select>
</p>

<p><strong>Text Group:</strong>

<select id="textgroupsel">
  <?php foreach ($textgroup_list as $tg): ?>
        <option value="<?= $tg ?>" <?= $tg==$get_parms['textgroup'] ? 'selected="selected"' : '' ?>><?= $tg ?></option>
  <?php endforeach; ?>
</select>
</p>


<p>Number of items in this text group: <?= $line_count ?>.</p>
<p>Each page shows <?= $lines_per_page ?> items.</p>

<nav>
  <ul class="pagination">
    <?php for ($p=0; $p<$page_count; ++$p): ?>
      <?php $page_parms = $get_parms;
            $page_parms['offset'] = $p;
      ?>
      <li <?= $p==$get_parms['offset'] ? 'class="active"' : '' ?>><a href="<?= build_url($page_parms) ?>"><?= $p+1 ?></a></li>
    <?php endfor; ?>
  </ul>
</nav>

<?php
  function make_if_line_header($label, $field, $get_parms) {
      if ($get_parms['orderby']===$field) {
          $link_sortorder = $get_parms['sortorder']=='desc' ? 'asc' : 'desc';
          $arrow = ' <span class="glyphicon glyphicon-triangle-' . ($get_parms['sortorder']=='desc' ? 'bottom' : 'top') . '" aria-hidden="true">';
      }
      else {
          $link_sortorder = 'asc';
          $arrow = '';
      }
      $get_parms['offset'] = 0;
      $get_parms['sortorder'] = $link_sortorder;
      $get_parms['orderby'] = $field;
      return '<th style="white-space:nowrap"><a href="' . build_url($get_parms) . '">' . $label . $arrow . "</a></th>\n";
    }

    $language_selector = "<select id=\"langshowsel\">\n";
    foreach ($lang_list as $lshort => $llong)
        $language_selector .= "<option value=\"$lshort\" " . ($lshort==$get_parms['lang_show'] ? 'selected="selected"' : '')
                           . ">$llong</option>\n";

    $language_selector .= "</select>\n";
?>

    
<form action="<?= site_url("translate/posted") ?>" method="post">
    
<div class="table-responsive">
<table id="trans_table" class="type2 table table-striped">
  <tr>
     <?= make_if_line_header('Symbolic name', 'key', $get_parms) ?>
     <th>Comment</th>
     <th><?= $language_selector ?></th>
     <?= make_if_line_header($lang_list[$get_parms['lang_edit']], 'text_edit', $get_parms) ?>
     <th>Modified?</th>
  </tr>
  <?php foreach ($alllines as $line): ?>
    <tr>
      <td class="leftalign"><?= $line->key ?></td>
      <td class="leftalign"><?= $line->comment ?></td>
      <td class="leftalign"><?= $line->text_show ?></td>
      <td class="leftalign">
        <input type="text" class="textinput" name="<?= $line->key ?>" size="40" value="<?= $line->text_edit ?>">
      </td>
      <td class="centeralign">
        <a class="label label-danger revertbutton" data-name="<?= $line->key ?>" href="#">Revert</a>
        <input type="hidden" class="modif-indicator" name="modif-<?= $line->key ?>" value="false"></td>
      </td>
    </tr>
  <?php endforeach; ?>
</table>
</div>

<p><input class="btn btn-primary" type="submit" name="submit" value="Submit changes">
   <a class="btn btn-default revert-all" href="<?= build_url($get_parms) ?>">Revert all</a></p>

<form>
