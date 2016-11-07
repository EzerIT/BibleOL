<?php
define('BUTTONS_PER_LINE', 8);

// This is how you export $editing as a global variable
global $editing_glob;
$editing_glob = $editing;

function button_type(integer $index, integer__OR__null $button_index) {
    return $index===$button_index ? "btn-info" : "btn-default";
}


function build_url2(array $get_parms)
{
    global $editing_glob;
    
    switch ($editing_glob) {
      case 'url':
            return site_url('urls/edit_url?' . http_build_query($get_parms));

      case 'lexicon':
            return site_url('translate/edit_lex?' . http_build_query($get_parms) . '#targetlang');
    }
}


function show_buttons(string $head, 
                      array $get_parms,
                      array $button_array,
                      string $style,
                      integer $gloss_count) {
    $CI =& get_instance();

    $right_to_left = in_array($get_parms['src_lang'], array('heb','aram'));
?>
    <div class="panel panel-info">
      <div class="panel-heading">
        <h3 class="panel-title"><?= $head ?></h3>
      </div>
      <div class="panel-body">
        <table>
          <tr>
            <td>
              <a style="width:100%" href="<?= build_url2(array('buttonix'=>-1) + $get_parms) ?>"
                   class="btn <?= button_type(-1, $get_parms['buttonix']) ?>"><?= sprintf($CI->lang->line('top_glosses'),$gloss_count) ?></a>
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
                     <a style="width:100%" href="<?= build_url2(array('buttonix'=>$ix) + $get_parms) ?>"
                          class="btn <?= button_type($ix, $get_parms['buttonix']) ?>">
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

<?php switch ($get_parms['src_lang']):
  case 'all-with-greek':
  case 'all': ?>
    <?php show_buttons($this->lang->line('hebrew_glosses'),  array('src_lang'=>'heb')   + $get_parms, $heb_buttons,   'heb-default',$gloss_count); ?>
    <?php show_buttons($this->lang->line('aramaic_glosses'), array('src_lang'=>'aram')  + $get_parms, $aram_buttons,  'heb-default',$gloss_count); ?>

    <?php if ($get_parms['src_lang']=='all-with-greek'): ?>
          <?php show_buttons($this->lang->line('greek_glosses'), array('src_lang'=>'greek') + $get_parms, $greek_buttons, 'greek-default',$gloss_count); ?>
    <?php else: ?>

      <div class="panel panel-info">
        <div class="panel-heading">
          <h3 class="panel-title"><?= $this->lang->line('greek_glosses') ?></h3>
        </div>
        <div class="panel-body">
          <?= $this->lang->line('no_greek') ?>
        </div><!--panel-body-->
      </div><!--panel-->

    <?php endif; ?>
    <?php break; ?>

  <?php case 'heb': ?>
    <?php show_buttons($this->lang->line('hebrew_glosses'),
                       array('src_lang'=>'heb') + $get_parms, 
                       $buttons,
                       'heb-default',
                       $gloss_count); ?>
    <?php break; ?>

  <?php case 'aram': ?>
    <?php show_buttons($this->lang->line('aramaic_glosses'),
                       array('src_lang'=>'aram') + $get_parms,
                       $buttons,
                       'heb-default',
                       $gloss_count); ?>
    <?php break; ?>

  <?php case 'greek': ?>
    <?php show_buttons($this->lang->line('greek_glosses'),
                       array('src_lang'=>'greek') + $get_parms,
                       $buttons,
                       'greek-default',
                       $gloss_count); ?>
    <?php break; ?>

<?php endswitch; ?>
