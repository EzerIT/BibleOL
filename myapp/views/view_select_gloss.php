<?php
define('BUTTONS_PER_LINE', 8);

function button_type(integer $index, integer__OR__null $button_index) {
    return $index===$button_index ? "btn-info" : "btn-default";
}
   

function show_buttons(string $head, 
                      string $lang,
                      string $top, 
                      array $button_array,
                      string $style,
                      integer__OR__null $button_index,
                      boolean $right_to_left) {
?>
    <div class="panel panel-info">
      <div class="panel-heading">
        <h3 class="panel-title"><?= $head ?></h3>
      </div>
      <div class="panel-body">
        <table>
          <tr>
            <td>
              <a style="width:100%" href="<?= site_url("urls/edit_url/$lang/-1") ?>" class="btn <?= button_type(-1, $button_index) ?>"><?= $top ?></a>
            </td>
          </tr>
        </table>
        <p>&nbsp;</p>
        <table class="<?= $style ?>">
          <?php for ($r=0; $r<(count($button_array)-1)/BUTTONS_PER_LINE + 1; ++$r): ?>
             <tr>
               <?php for ($c=0; $c<BUTTONS_PER_LINE; ++$c): ?>
                 <?php $ix = $right_to_left ? $r*BUTTONS_PER_LINE+(BUTTONS_PER_LINE-$c-1) : $r*BUTTONS_PER_LINE+$c ?>
                 <td>
                   <?php if ($ix<count($button_array)): ?>
                     <a style="width:100%" href="<?= site_url("urls/edit_url/$lang/$ix") ?>" class="btn <?= button_type($ix, $button_index) ?>">
                       <?= $button_array[$ix][0] ?>
                     </a>
                   <?php endif; ?>
                 </td>
               <?php endfor; ?>
             </tr>
           <?php endfor; ?>
        </table>
      </div><!--panel-body-->
    </div><!--panel-->
<?php
}
?>

<?php switch ($language):
  case 'all': ?>
    <?php show_buttons($this->lang->line('hebrew_glosses'),  'heb', sprintf($this->lang->line('top_glosses'),FREQUENT_GLOSSES), $heb_buttons, 'heb-default', null, true); ?>
    <?php show_buttons($this->lang->line('aramaic_glosses'), 'aram', sprintf($this->lang->line('top_glosses'),FREQUENT_GLOSSES), $aram_buttons, 'heb-default', null, true); ?>

    <div class="panel panel-info">
      <div class="panel-heading">
        <h3 class="panel-title"><?= $this->lang->line('greek_glosses') ?></h3>
      </div>
      <div class="panel-body">
        <?= $this->lang->line('no_greek') ?>
      </div><!--panel-body-->
    </div><!--panel-->
    <?php break; ?>

  <?php case 'heb': ?>
    <?php show_buttons($this->lang->line('hebrew_glosses'),
                       'heb', 
                       sprintf($this->lang->line('top_glosses'),FREQUENT_GLOSSES),
                       $buttons,
                       'heb-default',
                       $button_index,
                       true); ?>
    <?php break; ?>

  <?php case 'aram': ?>
    <?php show_buttons($this->lang->line('aramaic_glosses'),
                       'aram',
                       sprintf($this->lang->line('top_glosses'),FREQUENT_GLOSSES),
                       $buttons,
                       'heb-default',
                       $button_index,
                       true); ?>
    <?php break; ?>

<?php endswitch; ?>
