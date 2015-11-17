    <?php $valerr = validation_errors();
      if (!empty($valerr))
          echo "<div class=\"error\">$valerr</div>\n";
    ?>

      <h2><?= $this->lang->line('make_a_selection') ?></h2>

    <?= form_open('text/select_text') ?>
      <table>
        <?php $default = true; ?>
        <?php foreach($databases as $db): ?>
          <tr>
            <td><input type="radio" name="db" value="<?= $db['name'] ?>" <?= set_radio('db', $db['name'], $default) ?>></td>
            <td><?= str_replace(" ","&nbsp;", $db['loc_desc']) ?></td>
            <?php if ($db['name']==='ETCBC4'): ?>
            <td rowspan="4"><div style="vertical-align:middle;"><?= $db['loc_copyright'] ? "$db[loc_copyright]" : "" ?></div></td>
            <?php elseif ($db['name']==='ETCBC4-translit'): ?>
            <?php elseif ($db['name']==='ETCBC4-test'): ?>
            <?php elseif ($db['name']==='ETCBC4-test-translit'): ?>
            </tr><tr><td colspan="3">&nbsp;</td><!-- Make space -->
            <?php else: ?>
            <td><?= $db['loc_copyright'] ? "$db[loc_copyright]" : "" ?></td>
            <?php endif; ?>
          </tr>
          <?php $default = false; ?>
        <?php endforeach; ?>
      </table>

      <p><?= $this->lang->line('book_prompt') ?>
      <?php foreach($databases as $db): ?>
        <select name="book_<?= $db['name'] ?>">
          <?php foreach($db['order'] as $book_name): ?>
            <option value="<?= $book_name[0] ?>" data-chaps="<?= @$book_name[1] ?>" <?= set_select('book_'.$db['name'], $book_name[0]) ?>><?= $db['loc_books']->$book_name[0] ?></option>
          <?php endforeach; ?>
        </select>
      <?php endforeach; ?></p>
      <p><?= $this->lang->line('chapter_prompt') ?> <input type="text" name="chapter" value="<?= set_value('chapter') ?>"> <span id="valid_chap"></span></p>
      <p><?= $this->lang->line('first_verse_leave') ?> <input type="text" name="vfrom" value="<?= set_value('vfrom') ?>"></p>
      <p><?= $this->lang->line('last_verse_leave') ?> <input type="text" name="vto" value="<?= set_value('vto') ?>"></p>

      <p><input id="showicons" name="showicons" value="on" type="checkbox" <?= set_checkbox('showicons','on') ?>><?= $this->lang->line('show_link_icons') ?></p>     


      <p><input type="submit" name="submit" value="<?= $this->lang->line('display') ?>"></p>
    </form>

    <script>
      $(function() {
          var allSelectors = $('select[name^="book_"]');

          allSelectors.on('change', null, function(e) {
              var chaps = $($(this).find(":selected")[0]).data('chaps');
              $("#valid_chap").text(chaps!='' ? ('(<?= $this->lang->line('valid_chapters') ?> ' + chaps + ')') : '');
          });


          $('input[name="db"]').on('change',null, function(e) {
              allSelectors.hide();
              $('select[name="book_' + $(this).prop('value') + '"]').show().trigger('change');
          });

          allSelectors.hide();
          $('select[name="book_' + $('input[name="db"]:checked').prop('value') + '"]').show().trigger('change');
      });
    </script>
