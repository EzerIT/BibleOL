<?php $valerr = validation_errors();
  if (!empty($valerr))
      echo "<div class=\"alert alert-danger\">$valerr</div>\n";
?>

<h1><?= $this->lang->line('make_a_selection') ?></h1>

<?= form_open('text/select_text') ?>

  <div class="form-group">
  <div class="row">
    <p class="col-sm-3 like-label"><?= $this->lang->line('corpus_prompt') ?></p>
    <div class="col-sm-9">
      
      <?php $default = true; ?>
      <?php foreach($databases as $db): ?>
      <input type="radio"
             name="db"
             id="db_<?= $db['name'] ?>"
             value="<?= $db['name'] ?>"
             <?= set_radio('db', $db['name'], $default) ?>
             >
      <?= str_replace(" ","&nbsp;", $db['loc_desc']) ?><br>
      <?php $default = false; ?>
      <?php endforeach; ?>
    </div>
  </div>
  </div>
    

  <div class="form-group mb-0">
  <div class="row">
    <p class="col-sm-3 like-label"><?= $this->lang->line('book_prompt') ?></p>
    <div class="col-sm-9">
      <?php foreach($databases as $db): ?>
      <select name="book_<?= $db['name'] ?>">
        <?php foreach($db['order'] as $book_name): ?>
        <option value="<?= $book_name[0] ?>" data-chaps="<?= @$book_name[1] ?>" <?= set_select('book_'.$db['name'], $book_name[0]) ?>><?= $db['loc_books']->{$book_name[0]} ?></option>
        <?php endforeach; ?>
      </select>
      <?php endforeach; ?>
    </div>
  </div>
  </div>


  <div class="form-group mb-0">
  <div class="row">
    <label for="chapter" class="col-sm-3 col-form-label"><?= $this->lang->line('chapter_prompt') ?></label>
    <div class="col-sm-9">
      <input type="text" id="chapter" name="chapter" value="<?= set_value('chapter') ?>"> <span id="valid_chap"></span>
    </div>
  </div>
  </div>
  
  <div class="form-group mb-0">
  <div class="row">
    <label for="vfrom" class="col-sm-3 col-form-label"><?= $this->lang->line('first_verse_prompt') ?></label>
    <div class="col-sm-9">
      <input type="text" id="vfrom" name="vfrom" value="<?= set_value('vfrom') ?>">
    </div>
  </div>
  </div>

  <div class="form-group mb-0">
  <div class="row">
    <label for="vto" class="col-sm-3 col-form-label"><?= $this->lang->line('last_verse_prompt') ?></label>
    <div class="col-sm-9">
      <input type="text" id="vto" name="vto" value="<?= set_value('vto') ?>">
    </div>
  </div>
  </div>

  <div class="form-group mb-0">
  <div class="row">
    <label for="showicons" class="col-sm-3 col-form-label"><?= $this->lang->line('show_link_icons_prompt') ?></label>
    <div class="col-sm-9">
      <input class="checkbox mt-sm-3" id="showicons" name="showicons" value="on" type="checkbox" <?= set_checkbox('showicons','on') ?>>
    </div>
  </div>
  </div>

  <div class="row">
  <p class="col-sm-12"><button type="submit" class="btn btn-primary"><?= $this->lang->line('display') ?></button></p>
  </div>

</form>

<script>
  $(function() {
      var allSelectors = $('select[name^="book_"]');

      allSelectors.on('change', null, function(e) {
          var chaps = $($(this).find(":selected")[0]).data('chaps');
          $("#valid_chap").html(chaps!=''
                                ? ('<span style="white-space:nowrap;">(<?= $this->lang->line('valid_chapters') ?> ' + chaps + ')</span>')
                                : '');
      });


      $('input[name="db"]').on('change',null, function(e) {
          allSelectors.hide();
          $('select[name="book_' + $(this).prop('value') + '"]').show().trigger('change');
      });

      allSelectors.hide();
      $('select[name="book_' + $('input[name="db"]:checked').prop('value') + '"]').show().trigger('change');
  });
</script>
