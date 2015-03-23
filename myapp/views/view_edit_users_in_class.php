<h1>Users in the Class '<?= $classname ?>'</h1>

<?= form_open("userclass/users_in_class?classid=$classid") ?>
<table class="form">
  <tr><th>User</th><th>In this class?</th></tr>
  
  <?php foreach ($allusers as $user): ?>
    <tr>
      <td><?= "$user->first_name $user->last_name" ?></td>
     <td><input type="checkbox" name="inclass[]" value="<?= $user->id ?>" <?= set_checkbox('inclass[]', $user->id, in_array($user->id, $old_users)) ?>></td>
    </tr>
  <?php endforeach; ?>
</table>

<p><input class="makebutton" type="submit" name="submit" value="OK">
   <a class="makebutton" href="<?= site_url('classes') ?>">Cancel</a>
</p>
</form>
