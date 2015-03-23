<h1>Classes for user '<?= $user_name ?>'</h1>

<?= form_open("userclass/classes_for_user?userid=$userid") ?>
<table class="form">
  <tr><th>Class</th><th>In this class?</th></tr>
  
  <?php foreach ($allclasses as $cl): ?>
    <tr>
      <td><?= $cl->classname ?></td>
     <td><input type="checkbox" name="foruser[]" value="<?= $cl->id ?>" <?= set_checkbox('foruser[]', $cl->id, in_array($cl->id, $old_classes)) ?>></td>
    </tr>
  <?php endforeach; ?>
</table>

<p><input class="makebutton" type="submit" name="submit" value="OK">
     <a class="makebutton" href="<?= site_url('users') ?>">Cancel</a>
</p>
</form>
