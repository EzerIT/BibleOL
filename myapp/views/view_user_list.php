<table class="type1 small">
  <tr>
    <th><?= $this->lang->line('user_name') ?></th>
    <th><?= $this->lang->line('first_name') ?></th>
    <th><?= $this->lang->line('last_name') ?></th>
    <th><?= $this->lang->line('email') ?></th>
    <th><?= $this->lang->line('administrator') ?></th>
    <th><?= $this->lang->line('teacher') ?></th>
    <th><?= $this->lang->line('last_login') ?></th>
    <th><?= $this->lang->line('user_operations') ?></th>
  </tr>
  <?php foreach ($allusers as $user): ?>
    <tr>
      <td class="leftalign"><?= $user->username ?></td>
      <td class="leftalign"><?= $user->first_name ?></td>
      <td class="leftalign"><?= $user->last_name ?></td>
      <td class="leftalign"><?= $user->email ?></td>
      <td><?= $user->isadmin ? $this->lang->line('yes') : $this->lang->line('no') ?></td>
      <td><?= $user->isteacher ? $this->lang->line('yes') : $this->lang->line('no') ?></td>
      <td class="leftalign"><?= $user->last_login<$user->created_time ? $this->lang->line('never') : date($this->lang->line('date_time_format'), $user->last_login) ?></td>
      <td class="leftalign">
     <a href="<?= site_url("userclass/classes_for_user?userid=$user->id") ?>"><?= str_replace(' ', '&nbsp;', $this->lang->line('assign_to_class')) ?></a>
     <a href="<?= site_url("users/edit_one_user?userid=$user->id") ?>"><?= $this->lang->line('user_edit') ?></a>
     <?php // You cannot delete yourself.
           // You cannot delete a teacher or an administrator, unless you are an administrator.
           if ($my_id!=$user->id && ((!$user->isadmin && !$user->isteacher) || $isadmin)): ?>
          <a onclick="genericConfirm('<?= $this->lang->line('delete_user') ?>',
                                     '<?= sprintf($this->lang->line('delete_user_confirm'), "\'$user->username\'") ?>',
                                     '<?= site_url("users/delete_user?userid=$user->id") ?>');
                      return false;"
             href="#"><?= $this->lang->line('user_delete') ?></a>
        <?php endif; ?>
      </td>
    </tr>
  <?php endforeach; ?>
</table>
<p><a class="makebutton" href="<?= site_url("users/edit_one_user?userid=-1") ?>"><?= $this->lang->line('add_user') ?></a></p>
