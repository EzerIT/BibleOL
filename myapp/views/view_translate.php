<?php

function build_url($editing,$get_parms)
{
    switch ($editing) {
      case 'interface':
            return site_url(build_get('translate/translate_if', $get_parms));

      case 'grammar':
            return site_url(build_get('translate/translate_grammar', $get_parms));
            
      case 'lexicon':
            return site_url(build_get('translate/edit_lex', $get_parms) . '#targetlang');
    }
}

function replace_quot($s) {
    return preg_replace(array('/&/', '/"/'), array('&amp;','&quot;'),$s);
}

$short_target_lang = $get_parms['lang_edit'];
$long_target_lang = $lang_list[$short_target_lang];

if ($editing=='interface' || $editing=='grammar') {
    $count_untrans = count($untranslated);
    $strings = $count_untrans>1 ? 'strings' : 'string';
}
else
    $count_untrans = 0;

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
      <?php if ($editing!='lexicon'): ?>
        $('#groupsel')
            .on('change', function() {
                    <?php $groupsel_parms = $get_parms;
                    $groupsel_parms['offset'] = 0;
                    $groupsel_parms['group'] = '{0}';
                    ?>
                    document.location='<?= build_url($editing,$groupsel_parms) ?>'.format($(this).val());
                });
      <?php endif; ?>

      <?php if ($editing=='grammar'): ?>
        $('#dbsel')
            .on('change', function() {
                    <?php $dbsel_parms = $get_parms;
                    $dbsel_parms['db'] = '{0}';
                    ?>
                    document.location='<?= build_url($editing,$dbsel_parms) ?>'.format($(this).val());
                });
      <?php endif; ?>
            
        $('#langeditsel')
            .on('change', function() {
                    <?php $langeditsel_parms = $get_parms;
                          $langeditsel_parms['lang_edit'] = '{0}';
                    ?>
                    document.location='<?= build_url($editing,$langeditsel_parms) ?>'.format($(this).val());
                });

        $('#langshowsel')
            .on('change', function() {
                    <?php $langshowsel_parms = $get_parms;
                          $langshowsel_parms['lang_show'] = '{0}';
                    ?>
                    document.location='<?= build_url($editing,$langshowsel_parms) ?>'.format($(this).val());
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

        $('#show-notrans')
            .click(function() {
                    $('#untranslated-info-dialog').modal('show');
                });
        
        $(window)
            .on('beforeunload', function() {
                    if (!is_submitting && $('.modif-indicator[value=true]').length>0)
                        return 'You haven\'t saved your changes.';
                });

        });
</script>


<p id="targetlang"><strong><?= $this->lang->line('target_language') ?></strong>

<select id="langeditsel">
  <?php foreach ($lang_list as $lshort => $llong): ?>
     <option value="<?= $lshort ?>" <?= $lshort==$short_target_lang ? 'selected="selected"' : '' ?>><?= $llong ?></option>
  <?php endforeach; ?>
</select>
</p>

<?php
  switch ($editing) {
    case 'interface':
          echo '<p><strong>',$this->lang->line('text_group'),":</strong>\n";
          echo "<select id=\"groupsel\">\n";
          foreach ($group_list as $tg)
              echo "<option value=\"$tg\"", $tg==$get_parms['group'] ? 'selected="selected"' : '', ">$tg</option>\n";
          echo "</select>\n";
          echo "</p>\n";

          echo '<p>',sprintf($this->lang->line('items_text_group'),$line_count),"</p>\n";
          echo '<p>',sprintf($this->lang->line('each_page_shows'),$lines_per_page),"</p>\n";
          break;

    case 'grammar':
          echo '<p><strong>',$this->lang->line('text_database'),":</strong>\n";
          echo "<select id=\"dbsel\">\n";
          foreach ($db_list as $db)
              echo "<option value=\"$db\"", $db==$get_parms['db'] ? 'selected="selected"' : '', ">$db</option>\n";
          echo "</select>\n";
          echo "</p>\n";

          echo '<p><strong>',$this->lang->line('name_prefix'),":</strong>\n";
          echo "<select id=\"groupsel\">\n";
          foreach ($group_list as $tg)
              echo "<option value=\"$tg\"", $tg==$get_parms['group'] ? 'selected="selected"' : '', ">$tg</option>\n";
          echo "</select>\n";
          echo "</p>\n";
          break;
  }
?>

<?php if ($count_untrans>0): ?>        
  <p><input id="show-notrans" class="btn btn-danger" type="button" href="<?= build_url($editing,$get_parms) ?>"
     value="<?= sprintf($this->lang->line('show_strings_without_translation'),$count_untrans) ?>"></p>

  <!-- Dialog for displaying untranslated text -->
  <div class="modal fade" id="untranslated-info-dialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header justify-content-between">
          <div><h4 class="modal-title"><?= $this->lang->line('untranslated_strings') ?></h4></div>
          <div><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>
        </div>
        <div class="modal-body">
          <table class="table table-striped">
          <?php if ($editing=='grammar'): ?>
            <tr><th><?= $this->lang->line('name_prefix') ?></th><th><?= $this->lang->line('symbolic_name') ?></th></tr>
              <?php foreach ($untranslated as $ut): ?>
                  <tr><td><?= $ut['grammargroup'] ?></td><td><?= $ut['symbolic_name'] ?></td></tr>
              <?php endforeach; ?>
          <?php else: ?>
              <tr><th><?= $this->lang->line('text_group') ?></th><th><?= $this->lang->line('symbolic_name') ?></th></tr>
              <?php foreach ($untranslated as $ut): ?>
                  <tr><td><?= $ut['textgroup'] ?></td><td><?= $ut['symbolic_name'] ?></td></tr>
              <?php endforeach; ?>
          <?php endif; ?>
          </table>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-dark" data-dismiss="modal"><?= $this->lang->line('close_button') ?></button>
        </div>
      </div>
    </div>
  </div>
       
<?php endif; ?>
        
<?php if ($editing=='interface'): ?>
  <nav>
    <ul class="pagination">
      <?php for ($p=0; $p<$page_count; ++$p): ?>
        <?php $page_parms = $get_parms;
              $page_parms['offset'] = $p;
        ?>
         <li class="<?= $p==$get_parms['offset'] ? 'active' : '' ?> page-item"><a class="page-link" href="<?= build_url($editing,$page_parms) ?>"><?= $p+1 ?></a></li>
      <?php endfor; ?>
    </ul>
  </nav>
<?php endif; ?>

<?php
  function make_trans_line_header($editing, $label, $field, $get_parms) {
      if ($get_parms['orderby']===$field) {
          $link_sortorder = $get_parms['sortorder']=='desc' ? 'asc' : 'desc';
          $arrow = ' <span class="fas fa-caret-' . ($get_parms['sortorder']=='desc' ? 'down' : 'up') . '" aria-hidden="true">';
      }
      else {
          $link_sortorder = 'asc';
          $arrow = '';
      }
      $get_parms['offset'] = 0;
      $get_parms['sortorder'] = $link_sortorder;
      $get_parms['orderby'] = $field;
      return '<th style="white-space:nowrap"><a href="' . build_url($editing,$get_parms) . '">' . $label . $arrow . "</a></th>\n";
    }

    $language_selector = "<select id=\"langshowsel\">\n";
    foreach ($lang_list as $lshort => $llong)
        $language_selector .= "<option value=\"$lshort\" " . ($lshort==$get_parms['lang_show'] ? 'selected="selected"' : '')
                           . ">$llong</option>\n";

    $language_selector .= "</select>\n";
?>

<form action="<?= site_url(($editing=='interface'
                            ? 'translate/update_if?' 
                            : ($editing=='grammar' ? 'translate/update_grammar?'
                                : 'translate/update_lex?' )). http_build_query($get_parms)) ?>" method="post">
    
<div class="table-responsive">
<table id="trans_table" class="type2 table table-striped">
  <tr>
    <?php
        switch ($editing) {
          case 'interface':
          echo make_trans_line_header($editing, $this->lang->line('symbolic_name'), 'symbolic_name', $get_parms);
              echo '<th>',$this->lang->line('comment'),'</th>';
              echo "<th>$language_selector</th>";
              echo make_trans_line_header($editing, $long_target_lang, 'text_edit', $get_parms);
              echo '<th>',$this->lang->line('modified'),'</th>';
              break;

          case 'grammar':
              echo '<th>',$this->lang->line('symbolic_name'),'</th>';
              echo '<th>',$this->lang->line('comment'),'</th>';
              echo "<th>$language_selector</th>";
              echo "<th>$long_target_lang</th>";
              echo '<th>',$this->lang->line('modified'),'</th>';
              break;

          case 'lexicon':
              if ($get_parms['src_lang']=='greek') {
                  echo '<th>',$this->lang->line('occurrences'),'</th>';
                  echo '<th>',$this->lang->line('strongs'),'</th>';
                  echo '<th>',$this->lang->line('lexeme'),'</th>';
                  echo '<th>',$this->lang->line('first_occurrence'),'</th>';
                  echo "<th>$language_selector</th>";
                  echo "<th>$long_target_lang</th>";
                  echo '<th>',$this->lang->line('modified'),'</th>';
              }
              else {
                  echo '<th>',$this->lang->line('occurrences'),'</th>';
                  echo '<th>',$this->lang->line('symbolic_lexeme'),'</th>';
                  echo '<th>',$this->lang->line('lexeme'),'</th>';
                  echo '<th>',$this->lang->line('stem'),'</th>';
                  echo '<th>',$this->lang->line('first_occurrence'),'</th>';
                  echo "<th>$language_selector</th>";
                  echo "<th>$long_target_lang</th>";
                  echo '<th>',$this->lang->line('modified'),'</th>';
              }
              break;
        }
    ?>
  </tr>

  <?php $lastlex = ''; ?>
  <?php foreach ($alllines as $line): ?>
    <tr>
      <?php switch ($editing):
        case 'interface': ?>
          <td class="leftalign"><?= $line->symbolic_name ?></td>
        <td class="leftalign"><?= htmlspecialchars($line->comment) ?></td>
          <td class="leftalign"><?= preg_replace('/\n/','<br>',htmlspecialchars($line->text_show)) ?></td>

          <td class="leftalign">
            <?php if ($line->use_textarea): ?>
              <textarea class="textinput" name="<?= $line->symbolic_name ?>" rows="5" cols="40"><?= replace_quot($line->text_edit) ?></textarea>
            <?php else: ?>
              <input type="text" class="textinput" name="<?= $line->symbolic_name ?>" size="40" value="<?= replace_quot($line->text_edit) ?>">
            <?php endif; ?>
          </td>
          <td class="centeralign">
        <a class="badge badge-danger revertbutton" data-name="<?= $line->symbolic_name ?>" href="#"><?= $this->lang->line('revert') ?></a>
            <input type="hidden" class="modif-indicator" name="modif-<?= $line->symbolic_name ?>" value="false"></td>
          </td>

        <?php break; ?>

        
        <?php case 'grammar': ?>
          <td class="leftalign"><?= $get_parms['group']=='info' ? $line->symbolic_name : substr($line->symbolic_name,strlen($get_parms['group'])+1) ?></td>
          <td class="leftalign"><?= htmlspecialchars(substr($line->comment,0,2)=="f:" ?
                                                     substr(strstr($line->comment," "),1) : $line->comment) ?></td>
          <td class="leftalign"><?= htmlspecialchars($line->text_show) ?></td>

          <td class="leftalign">
            <?php if (substr($line->comment,0,10)=='f:textarea'): ?>
              <textarea class="textinput" name="<?= $line->symbolic_name_dash ?>" rows="5" cols="40"><?= replace_quot($line->text_edit) ?></textarea>
            <?php else: ?>
              <input type="text" class="textinput" name="<?= $line->symbolic_name_dash ?>" size="40" value="<?= replace_quot($line->text_edit) ?>">
            <?php endif; ?>
          <td class="centeralign">
            <a class="badge badge-danger revertbutton" data-name="<?= $line->symbolic_name_dash ?>" href="#"><?= $this->lang->line('revert') ?></a>
            <input type="hidden" class="modif-indicator" name="modif-<?= $line->symbolic_name_dash ?>" value="false"></td>
          </td>
          </td>

        <?php break; ?>

        <?php case 'lexicon': ?>
          <?php if ($get_parms['src_lang']=='greek'): ?>
            <td class="centeralign"><?= $line->tally ?></td>
            <td class="centeralign"><?= $line->strongs ?><?= $line->strongs_unreliable ? '?' : '' ?></td>
            <td class="leftalign"><?= $line->lexeme ?></td>
            <td class="leftalign">
                 <a target="_blank" href="<?=
                    site_url(sprintf("/text/show_text/nestle1904/%s/%d/%d",$line->firstbook,$line->firstchapter,$line->firstverse))
                  ?>"><?= sprintf($books['_label'], $books[$line->firstbook],$line->firstchapter,$line->firstverse) ?></a>
            </td>
          <?php else: ?>
            <td class="centeralign"><?= $lastlex!=$line->lex ? $line->tally : '' ?></td>
            <td class="leftalign"><?= $lastlex!=$line->lex ? htmlspecialchars($line->lex) : '' ?></td>
            <td class="heb-default rtl"><?= $lastlex!=$line->lex ? $line->lexeme : '&nbsp;&nbsp;&#x2033;' ?></td>
            <?php $lastlex = $line->lex ?>
            <td class="leftalign"><?= stripSortIndex($stems[$line->vs]) ?></td>
            <td class="leftalign">
                 <a target="_blank" href="<?=
                    site_url(sprintf("/text/show_text/ETCBC4/%s/%d/%d",$line->firstbook,$line->firstchapter,$line->firstverse))
                  ?>"><?= sprintf($books['_label'], $books[$line->firstbook],$line->firstchapter,$line->firstverse) ?></a>
            </td>
          <?php endif;  ?>

          <td class="leftalign"><?= preg_replace('/\n/','<br>',htmlspecialchars($line->text_show)) ?></td>
          <td class="leftalign">
            <input type="text" class="textinput" name="<?= $line->lex_id ?>" size="40" value="<?= replace_quot($line->text_edit) ?>">
          </td>
          <td class="centeralign">
            <a class="badge badge-danger revertbutton" data-name="<?= $line->lex_id ?>" href="#"><?= $this->lang->line('revert') ?></a>
            <input type="hidden" class="modif-indicator" name="modif-<?= $line->lex_id ?>" value="false"></td>
          </td>

        <?php break; ?>
      <?php endswitch; ?>

    </tr>
  <?php endforeach; ?>
</table>
</div>

<p><input class="btn btn-primary" type="submit" name="submit" value="<?= $this->lang->line('submit_changes') ?>">
   <a class="btn btn-outline-dark revert-all" href="#"><?= $this->lang->line('revert_all') ?></a></p>

<form>
