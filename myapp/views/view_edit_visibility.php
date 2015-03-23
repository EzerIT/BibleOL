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


<h1>Visibility of <?= $friendly_name ?></h1>

<?= form_open("file_manager/edit_visibility?dir=$dir") ?>
<table class="form">
  <tr><th>Class</th><th>Visible to this class?</th></tr>
  <tr>
     <td id="everybody"><i>Everybody</i></td>
     <td class="centeralign"><input id="selectall" class="narrow" type="checkbox" name="inclass[]" value="0" <?= set_checkbox('inclass[]', 0, in_array(0, $old_classes)) ?>></td>
  </tr>
  
  <?php foreach ($allclasses as $cl): ?>
    <tr class="dirclasslist">
      <td><?= $cl->classname ?></td>
      <td class="centeralign"><input type="checkbox" class="narrow" name="inclass[]" value="<?= $cl->id ?>" <?= set_checkbox('inclass[]', $cl->id, in_array($cl->id, $old_classes)) ?>></td>
    </tr>
  <?php endforeach; ?>
</table>

<p><input class="makebutton" type="submit" name="submit" value="OK">
     <a class="makebutton" href="<?= site_url("file_manager?dir=$dir") ?>">Cancel</a>
</p>
</form>
