<h1><?= sprintf($this->lang->line('users_in_class'), $classname) ?></h1>

<?= form_open("userclass/users_in_class?classid=$classid") ?>
<table class="form">
  <tr><th><?= $this->lang->line('user') ?></th><th><?= $this->lang->line('in_this_class') ?></th></tr>
  
  <?php foreach ($allusers as $user): ?>
    <tr>
      <td><?= "$user->first_name $user->last_name" ?></td>
     <td><input type="checkbox" name="inclass[]" value="<?= $user->id ?>" <?= set_checkbox('inclass[]', $user->id, in_array($user->id, $old_users)) ?>></td>
    </tr>
  <?php endforeach; ?>
</table>

<p><input class="makebutton" type="submit" name="submit" value="<?= $this->lang->line('OK_button') ?>">
   <a class="makebutton" href="<?= site_url('classes') ?>"><?= $this->lang->line('cancel_button') ?></a>
</p>
</form>
