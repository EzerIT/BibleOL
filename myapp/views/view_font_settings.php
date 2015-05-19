<?php
  $styles = array('text'    => '<strong>'.$this->lang->line('style_text_display').'</strong>',
                  'feature' => '<strong>'.$this->lang->line('style_features')    .'</strong><br><span class="styleexplanation">'.$this->lang->line('style_features_explain').'</span>',
                  'input'   => '<strong>'.$this->lang->line('style_input')       .'</strong><br><span class="styleexplanation">'.$this->lang->line('style_input_explain').'</span>',
                  'tooltip' => '<strong>'.$this->lang->line('style_tooltip')     .'</strong><br><span class="styleexplanation">'.$this->lang->line('style_tooltip_explain').'</span>',
                  );
?>
<script>
    var l10n_js = <?= $l10n_js_json ?>;

    function boldChange(e) {
        $('#' + e.data.alphabet + e.data.where + 'sample')
            .css('font-weight', $('#' + e.data.alphabet + e.data.where + 'bold').prop('checked') ? 'bold' : 'normal');     
    }

    function italicChange(e) {
        $('#' + e.data.alphabet + e.data.where + 'sample')
            .css('font-style', $('#' + e.data.alphabet + e.data.where + 'italic').prop('checked') ? 'italic' : 'normal');     
    }

    function sizeChange(e) {
        $('#' + e.data.alphabet + e.data.where + 'sample')
            .css('font-size',$('#' + e.data.alphabet + e.data.where + 'size').prop('value') + 'pt');
    }

</script>

<?= form_open('config/fonts') ?>
  <div id="font_tabs">
    <ul>
      <?php foreach ($alphabets as $alph): ?>
        <li><a href="#tab_<?= $alph ?>"><?= ucfirst($this->lang->line('alphabet_'.$font_setting[$alph]->name)) ?></a></li>
      <?php endforeach; ?>
    </ul>
   
    <?php foreach ($alphabets as $alph): ?>
      <div id="tab_<?= $alph ?>" class="fontsetting">
        <h1><?= $this->lang->line('select_font_family') ?></h1>
        <table id="<?= $alph ?>font" class="fontfamily striped">
          <tr><th class="leftalign"><?= $this->lang->line('name') ?></th><th><?= $this->lang->line('sample') ?></th><th><?= $this->lang->line('selected') ?></th></tr>
        </table>

        <hr>

        <h1><?= $this->lang->line('select_attributes') ?></h1>
        <table class="striped fontattrib">
          <tr>
            <th class="settingsfor"><?= $this->lang->line('select_settings_for') ?></th>
            <th><?= $this->lang->line('bold') ?></th>
            <th><?= $this->lang->line('italic') ?></th>
            <th><?= $this->lang->line('size') ?></th>
            <th><?= $this->lang->line('text_sample') ?></th>
          </tr>
          <?php foreach ($styles as $stylename => $styledesc): ?>
            <?php $stylename_bold = $stylename . '_bold';
                  $stylename_italic = $stylename . '_italic';
                  $stylename_size = $stylename . '_size';
                  ?>
            <tr>
              <td class="settingsfor"><?= $styledesc ?></td>
              <td class="centeralign"><input id="<?= $alph.$stylename ?>bold"
                     name="<?= $alph.$stylename ?>bold"
                     value="on"
                     type="checkbox"
                     <?= set_checkbox($alph.$stylename.'bold','on',$font_setting[$alph]->$stylename_bold==1) ?>
                     ></td>
              <td class="centeralign"><input id="<?= $alph.$stylename ?>italic"
                     name="<?= $alph.$stylename ?>italic"
                     value="on"
                     type="checkbox"
                     <?= set_checkbox($alph.$stylename.'italic','on',$font_setting[$alph]->$stylename_italic==1) ?>
                     ></td>
              <td class="centeralign"><!-- Replace with <input type="number"> when there is better browser support for this -->
              <input style="width:40px;"
                     id="<?= $alph.$stylename ?>size"
                     name="<?= $alph.$stylename ?>size"
                     value="<?= set_value($alph.$stylename.'size',$font_setting[$alph]->$stylename_size) ?>"
                     ></td>
              <td class="<?= $alph ?>sample <?= $font_setting[$alph]->direction ?>"
                                              id="<?= $alph.$stylename ?>sample"
                                              ><?= $font_setting[$alph]->sample ?></td>
            </tr>
          <?php endforeach; ?>
        </table>
   
        <script>
          $(function() {
              var fs = new FontSelector('<?= $alph ?>',
                                        '<?= $font_setting[$alph]->sample ?>',
                                        '<?= $font_setting[$alph]->direction ?>');
              var fonts = [
                  <?php foreach ($avail_fonts[$alph] as $font): ?>
                  {name:'<?= $font[0] ?>', webfont:<?= $font[1]?'true':'false' ?>},
                  <?php endforeach; ?>
              ];
              fs.detectFonts(fonts,
                             '<?= $personal_font[$alph] ?>',
                             '<?= get_radio_setting($alph.'choice', $choice_values[$alph]) ?>');
   
              <?php foreach ($styles as $stylename => $styledesc): ?>
                {
                    var eloc = {alphabet:'<?=$alph?>',where:'<?=$stylename?>'};
      
                    $('#<?= $alph.$stylename ?>bold').change(eloc, boldChange);
                    boldChange({data:eloc});
                    $('#<?= $alph.$stylename ?>italic').change(eloc, italicChange);
                    italicChange({data:eloc});
                    $('#<?= $alph.$stylename ?>size').spinner();
                    $('#<?= $alph.$stylename ?>size').on('spinstop',null,eloc, sizeChange);
                    sizeChange({data:eloc});
                }
              <?php endforeach; ?>
          });
        </script>
      </div>
    <?php endforeach; ?>
  </div>
  <div class="buttons">
    <button type="submit" value="Submit"><?= $this->lang->line('OK_button') ?></button>
    <input type="button" onclick="window.location='<?=site_url() ?>';" value="<?= $this->lang->line('cancel_button') ?>">
  </div>
</form>
<script>
  $(function() {
      $("#font_tabs").tabs();
      $('button').button();
      $('input[type="button"]').button();
  });
</script>
