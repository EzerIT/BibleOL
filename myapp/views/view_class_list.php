<table class="type1 small">
  <tr><th><?= $this->lang->line('class_name') ?></th><th><?= $this->lang->line('class_pw') ?></th><th><?= $this->lang->line('enroll_before') ?></th><th><?= $this->lang->line('class_operations') ?></th></tr>
  <?php foreach ($allclasses as $cl): ?>
    <tr>
      <td class="leftalign"><?= $cl->classname ?></td>
      <td class="leftalign"><?= empty($cl->password) ? '-' : $cl->password ?></td>
      <td class="leftalign"><?= empty($cl->enrol_before) ? '-' : $cl->enrol_before ?></td>
      <td class="leftalign">
        <a href="<?= site_url("userclass/users_in_class?classid=$cl->id") ?>"><?= str_replace(' ', '&nbsp;', $this->lang->line('assign_users')) ?></a>
        <a href="<?= site_url("classes/edit_one_class?classid=$cl->id") ?>"><?= $this->lang->line('class_edit') ?></a>
        <a onclick="genericConfirm('<?= $this->lang->line('delete_class') ?>',
                                     '<?= sprintf($this->lang->line('delete_class_confirm'), "\'$cl->classname\'") ?>',
                                     '<?= site_url("classes/delete_class?classid=$cl->id") ?>');
                      return false;"
             href="#"><?= $this->lang->line('class_delete') ?></a>
      </td>
    </tr>
  <?php endforeach; ?>
</table>
<p><a class="makebutton" href="<?= site_url("classes/edit_one_class?classid=-1") ?>"><?= $this->lang->line('add_class') ?></a></p>
