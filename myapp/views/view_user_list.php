<h1><?= sprintf($this->lang->line('number_of_users'), $user_count) ?></h1>
<h2><?= sprintf($this->lang->line('showing_per_page'), $users_per_page) ?></h2>
<nav>
  <ul class="pagination">
    <?php for ($p=0; $p<$page_count; ++$p): ?>
      <li class="<?= $p==$offset ? 'active' : '' ?> page-item"><a class="page-link" href="<?= site_url("users?offset=$p&orderby=$orderby&$sortorder") ?>"><?= $p+1 ?></a></li>
    <?php endfor; ?>
  </ul>
</nav>

<?php
  function make_user_header($me, $label, $field, $sortorder, $orderby) {
      if ($orderby===$field) {
          $link_sortorder = $sortorder=='desc' ? 'asc' : 'desc';
          $arrow = ' <span class="fas fa-caret-' . ($sortorder=='desc' ? 'down' : 'up') . '" aria-hidden="true">';
      }
      else {
          $link_sortorder = 'asc';
          $arrow = '';
      }

      return '<th style="white-space:nowrap"><a href="' . site_url("users?offset=0&orderby=$field&$link_sortorder") . '">' . $me->lang->line($label) . $arrow . "</a></th>\n";
    }
?>


<div class="table-responsive">
<table class="type2 table table-striped">
  <tr>
     <?= make_user_header($this, 'user_name', 'username', $sortorder, $orderby) ?>
     <?= make_user_header($this, 'first_name', 'first_name', $sortorder, $orderby) ?>
     <?= make_user_header($this, 'last_name', 'last_name', $sortorder, $orderby) ?>
     <?= make_user_header($this, 'email', 'email', $sortorder, $orderby) ?>
     <?= make_user_header($this, 'administrator', 'isadmin', $sortorder, $orderby) ?>
     <?= make_user_header($this, 'teacher', 'isteacher', $sortorder, $orderby) ?>
     <?= make_user_header($this, 'translator', 'istranslator', $sortorder, $orderby) ?>
     <?= make_user_header($this, 'last_login', 'last_login', $sortorder, $orderby) ?>
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
      <td><?= $user->istranslator ? $this->lang->line('yes') : $this->lang->line('no') ?></td>
      <td class="leftalign"><?= $user->last_login<$user->created_time ? $this->lang->line('never') : date($this->lang->line('date_time_format'), $user->last_login) ?></td>
      <td class="leftalign">
     <a class="badge badge-primary" href="<?= site_url("userclass/classes_for_user?userid=$user->id&offset=$offset&orderby=$orderby&$sortorder") ?>"><?= str_replace(' ', '&nbsp;', $this->lang->line('assign_to_class')) ?></a>
     <a class="badge badge-primary" href="<?= site_url("users/edit_one_user?userid=$user->id&offset=$offset&orderby=$orderby&$sortorder") ?>"><?= $this->lang->line('user_edit') ?></a>
     <?php // You cannot delete yourself.
           // You cannot delete a teacher or an administrator, unless you are an administrator.
           if ($my_id!=$user->id && ((!$user->isadmin && !$user->isteacher) || $isadmin)): ?>
          <a  class="badge badge-danger" onclick="genericConfirmSm('<?= $this->lang->line('delete_user') ?>',
                                     '<?= sprintf($this->lang->line('delete_user_confirm'), "\'$user->username\'") ?>',
                                     '<?= site_url("users/delete_user?userid=$user->id&offset=$offset&orderby=$orderby&$sortorder") ?>');
                      return false;"
             href="#"><?= $this->lang->line('user_delete') ?></a>
        <?php endif; ?>
      </td>
    </tr>
  <?php endforeach; ?>
</table>
</div>

<p style="height:2px">&nbsp;</p>

<p><a class="btn btn-primary" href="<?= site_url("users/edit_one_user?userid=-1") ?>"><?= $this->lang->line('add_user') ?></a></p>
