<h1><?= sprintf($this->lang->line('classes_for_user'), $user_name) ?></h1>

<?= form_open("userclass/classes_for_user?userid=$userid") ?>
<table class="form">
  <tr><th><?= $this->lang->line('class') ?></th><th><?= $this->lang->line('in_this_class') ?></th></tr>

  <?php foreach ($allclasses as $cl): ?>
    <tr>
      <td><?= $cl->classname ?></td>
     <td><input type="checkbox" name="foruser[]" value="<?= $cl->id ?>" <?= set_checkbox('foruser[]', $cl->id, in_array($cl->id, $old_classes)) ?>></td>
    </tr>
  <?php endforeach; ?>
</table>

<p><input class="makebutton" type="submit" name="submit" value="<?= $this->lang->line('OK_button') ?>">
   <a class="makebutton" href="<?= site_url('users') ?>"><?= $this->lang->line('cancel_button') ?></a>
</p>
</form>
