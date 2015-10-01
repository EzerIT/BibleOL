<script>
    $(function () {
          $('#selectall').on('change',null, function(e) {
              if ($(this).prop('checked'))
                  $('.dirclasslist').hide();
              else
                  $('.dirclasslist').show();
          });

          var w = $('#everybody').width();
          $('#everybody').width(w); // Make the width of '#everybody' fixed

          <?php if (in_array(0, $old_classes)): ?>
              $('.dirclasslist').hide();
          <?php endif; ?>
        });
</script>


<h1><?= sprintf($this->lang->line('visibility_folder'), $dir) ?></h1>

<?= form_open("file_manager/edit_visibility?dir=$dir") ?>
<table class="form">
  <tr><th><?= $this->lang->line('class') ?></th><th><?= $this->lang->line('visible_to_class') ?></th></tr>
  <tr>
     <td id="everybody"><i><?= $this->lang->line('everybody') ?></i></td>
     <td class="centeralign"><input id="selectall" class="narrow" type="checkbox" name="inclass[]" value="0" <?= set_checkbox('inclass[]', 0, in_array(0, $old_classes)) ?>></td>
  </tr>
  
  <?php foreach ($allclasses as $cl): ?>
    <tr class="dirclasslist">
      <td><?= $cl->classname ?></td>
      <td class="centeralign"><input type="checkbox" class="narrow" name="inclass[]" value="<?= $cl->clid ?>" <?= set_checkbox('inclass[]', $cl->clid, in_array($cl->clid, $old_classes)) ?>></td>
    </tr>
  <?php endforeach; ?>
</table>

<p><input class="makebutton" type="submit" name="submit" value="<?= $this->lang->line('OK_button') ?>">
     <a class="makebutton" href="<?= site_url("file_manager?dir=$dir") ?>"><?= $this->lang->line('cancel_button') ?></a>
</p>
</form>
