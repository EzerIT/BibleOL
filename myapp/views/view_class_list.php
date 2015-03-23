<table class="type1 small">
  <tr><th>Class name</th><th>Password</th><th>Enroll before</th><th>Operations</th></tr>
  <?php foreach ($allclasses as $cl): ?>
    <tr>
      <td class="leftalign"><?= $cl->classname ?></td>
      <td class="leftalign"><?= empty($cl->password) ? '-' : $cl->password ?></td>
      <td class="leftalign"><?= empty($cl->enrol_before) ? '-' : $cl->enrol_before ?></td>
      <td class="leftalign">
        <a href="<?= site_url("userclass/users_in_class?classid=$cl->id") ?>">Assign&nbsp;users</a>
        <a href="<?= site_url("classes/edit_one_class?classid=$cl->id") ?>">Edit</a>
        <a onclick="genericConfirm('Delete class',
                                     'Do you want to delete the class \'<?= $cl->classname ?>\'?',
                                     '<?= site_url("classes/delete_class?classid=$cl->id") ?>');
                      return false;"
             href="#">Delete</a>
      </td>
    </tr>
  <?php endforeach; ?>
</table>
<p><a class="makebutton" href="<?= site_url("classes/edit_one_class?classid=-1") ?>">Add new class</a></p>
