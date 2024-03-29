<?php
function fontsize($fs) {
    if ($fs>0)
        return "font-size: {$fs}pt;\n";
    else
        return '';
}

function heightsize($fs) {
    if ($fs>0)
        return "height: {$fs}pt;\n";
    else
        return '';
}

?>

<style>
<?php foreach ($fonts as $f): ?>
    .<?= $f->name ?> {
        font-family: <?= $f->font_family ?> !important;
        direction: <?= $f->direction ?>;
        text-align: <?= $f->direction=='rtl' ? 'right' : 'left' ?>
    }

    <?php if ($f->name==='hebrew'): ?>
        #virtualKeyboard.HE div.kbButton span {  /* Main character on a key */
            font-family: <?= $f->font_family ?>;
            font-size: 13pt;
        }
        #virtualKeyboard.HE div#kbDesk.modeNormal div.kbButton span.hiddenShiftCaps { /* Shifted character in unhifted mode */
            font-size: 9pt;
        }
        #virtualKeyboard.HE div#kbDesk.modeCaps div.kbButton span.hiddenShiftCaps { /* Shifted character in Caps Lock mode */
            font-size: 9pt;
        }
  <?php endif; ?>

    <?php if ($f->name==='greek'): ?>
        #virtualKeyboard.EL div.kbButton span {  /* Main character on a key */
            font-family: <?= $f->font_family ?>;
            font-size: 13pt;
        }
        #virtualKeyboard.EL div#kbDesk.modeNormal div.kbButton span.hiddenShiftCaps { /* Shifted character in unhifted mode */
            font-size: 9pt;
        }
        #virtualKeyboard.EL div#kbDesk.modeCaps div.kbButton span.hiddenShiftCaps { /* Shifted character in Caps Lock mode */
            font-size: 9pt;
        }
        #virtualKeyboard.EL div#kbDesk.modeNormal div.kbButton span.charAlt { /* Alt character in unhifted mode */
            font-size: 9pt;
        }
    <?php endif; ?>

    .textdisplay.<?= $f->name ?> {
        <?= fontsize($f->text_size) ?>
        font-weight: <?= $f->text_bold ? 'bold' : 'normal' ?>;
        font-style: <?= $f->text_italic ? 'italic' : 'normal' ?>;
    }

    select.<?= $f->name ?>,
    .wordgrammar.<?= $f->name ?>, #quiztab td.<?= $f->name ?>{
        <?= fontsize($f->feature_size) ?>
        font-weight: <?= $f->feature_bold ? 'bold' : 'normal' ?>;
        font-style: <?= $f->feature_italic ? 'italic' : 'normal' ?>;
    }

    .bol-tooltip.<?= $f->name ?> {
        <?= fontsize($f->tooltip_size) ?>
        font-weight: <?= $f->tooltip_bold ? 'bold' : 'normal' ?>;
        font-style: <?= $f->tooltip_italic ? 'italic' : 'normal' ?>;
    }

    input.<?= $f->name ?> {
        <?= fontsize($f->input_size) ?>
        font-weight: <?= $f->input_bold ? 'bold' : 'normal' ?>;
        font-style: <?= $f->input_italic ? 'italic' : 'normal' ?>;
    }

    div.styled-select,
    div.styled-select select {
      <?= heightsize($f->feature_size * 1.5) ?>
    }

<?php endforeach; ?>
</style>
