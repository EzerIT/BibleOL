<table class="type1 small">
  <tr><th>User name</th><th>First name</th><th>Last name</th><th>E-mail</th><th>Administrator</th><th>May see full WIVU</th><th>Operations</th></tr>
  <?php foreach ($allusers as $user): ?>
    <tr>
      <td class="leftalign"><?= $user->username ?></td>
      <td class="leftalign"><?= $user->first_name ?></td>
      <td class="leftalign"><?= $user->last_name ?></td>
      <td class="leftalign"><?= $user->email ?></td>
      <td><?= $user->isadmin ? 'Yes' : 'No' ?></td>
      <td><?= $user->isadmin ? '-' : ($user->may_see_wivu ? 'Yes' : 'No' ) ?></td>
      <td class="leftalign">
        <a href="<?= site_url("userclass/classes_for_user?userid=$user->id") ?>">Assign&nbsp;to&nbsp;class</a>
        <a href="<?= site_url("users/edit_one_user?userid=$user->id") ?>">Edit</a>
        <?php if ($my_id!=$user->id): ?>
          <a onclick="genericConfirm('Delete user',
                                     'Do you want to delete user \'<?= $user->username ?>\' including all their data on this site and the statistics site?',
                                     '<?= site_url("users/delete_user?userid=$user->id") ?>');
                      return false;"
             href="#">Delete</a>
        <?php endif; ?>
      </td>
    </tr>
  <?php endforeach; ?>
</table>
<p><a class="makebutton" href="<?= site_url("users/edit_one_user?userid=-1") ?>">Add new user</a></p>
