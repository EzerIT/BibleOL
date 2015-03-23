<?php
  $styles = array('text' => '<strong>Text display:</strong>',
                  'feature' => '<strong>Features:</strong><br><span class="styleexplanation">(This is the grammar information under each word)</span>',
                  'input' => '<strong>Input:</strong><br><span class="styleexplanation">(This is data you type)</span>',
                  'tooltip' => '<strong>Popup:</strong><br><span class="styleexplanation">(This is the grammar information to the right of the display)</span>',
                  );
?>
<script>
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
        <li><a href="#tab_<?= $alph ?>"><?= ucfirst($font_setting[$alph]->english) ?></a></li>
      <?php endforeach; ?>
    </ul>
   
    <?php foreach ($alphabets as $alph): ?>
      <div id="tab_<?= $alph ?>" class="fontsetting">
        <h1>Select Font Family</h1>
        <table id="<?= $alph ?>font" class="fontfamily striped">
          <tr><th class="leftalign">Name</th><th>Sample</th><th>Selected</th></tr>
        </table>

        <hr>

        <h1>Select Font Attributes</h1>
        <table class="striped fontattrib">
          <tr><th class="settingsfor">Set settings for...</th><th>Bold</td><th>Italic</th><th>Size</th><th>Text sample</th></tr>
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
    <button type="submit" value="Submit">OK</button>
    <input type="button" onclick="window.location='<?=site_url() ?>';" value="Cancel">
  </div>
</form>
<script>
  $(function() {
      $("#font_tabs").tabs();
      $('button').button();
      $('input[type="button"]').button();
  });
</script>
