<table class="type1 small">
  <tr>
    <th><?= $this->lang->line('class_name') ?></th>
    <th><?= $this->lang->line('class_pw') ?></th>
    <th><?= $this->lang->line('enroll_before') ?></th>
    <th><?= $this->lang->line('owner_name') ?></th>
    <th><?= $this->lang->line('class_operations') ?></th>
  </tr>
  <?php foreach ($allclasses as $cl): ?>
    <tr>
      <td class="leftalign"><?= $cl->classname ?></td>
      <td class="leftalign">
        <?php if ($myid==$cl->ownerid || $isadmin): ?>
          <?= empty($cl->clpass) ? '-' : $cl->clpass ?>
        <?php else: ?>
          <?= $this->lang->line('password_hidden') ?>
        <?php endif; ?>
      </td>
      <td class="leftalign"><?= empty($cl->enrol_before) ? '-' : $cl->enrol_before ?></td>
      <td class="leftalign"><?= $cl->ownerid==0 ? $this->lang->line('no_owner') : "$cl->first_name $cl->last_name" ?></td>
      <td class="leftalign">
        <?php if ($myid==$cl->ownerid || $isadmin): ?>
          <a href="<?= site_url("userclass/users_in_class?classid=$cl->clid") ?>"><?= str_replace(' ', '&nbsp;', $this->lang->line('assign_users')) ?></a>
          <a href="<?= site_url("classes/edit_one_class?classid=$cl->clid") ?>"><?= $this->lang->line('class_edit') ?></a>
          <a onclick="genericConfirm('<?= $this->lang->line('delete_class') ?>',
                                       '<?= sprintf($this->lang->line('delete_class_confirm'), "\'$cl->classname\'") ?>',
                                       '<?= site_url("classes/delete_class?classid=$cl->clid") ?>');
                        return false;"
               href="#"><?= $this->lang->line('class_delete') ?></a>
        <?php endif; ?>
        <?php if ($isadmin): ?>
          <a onclick="changeOwnerClass(<?= $cl->clid ?>); return false;" href="#"><?= $this->lang->line('change_owner_class') ?></a>
        <?php endif; ?>
      </td>
    </tr>
  <?php endforeach; ?>
</table>
<p><a class="makebutton" href="<?= site_url("classes/edit_one_class?classid=-1") ?>"><?= $this->lang->line('add_class') ?></a></p>

<!-- ChangeOwner dialog -->
<div id="chown-class-dialog" style="display:none">
  <p>
    <span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>
    <span><?= $this->lang->line('new_owner_prompt') ?></span>
    <select id="chown-class-selector">
      <option value="0" selected="selected"></option>
      <?php foreach ($teachers as $t): ?>
        <option value="<?= $t->id ?>"><?= $t->first_name . ' ' . $t->last_name ?></option>
      <?php endforeach; ?>
    </select>
  </p>
</div>


<script>
    function changeOwnerClass(classid) {
        $("#chown-class-dialog").dialog({
            autoOpen: false,
            resizable: false,
            modal: true,
            title: '<?= $this->lang->line('change_owner_title') ?>',
            buttons: {
                '<?= $this->lang->line('OK_button') ?>': function() {
                    userid = $("#chown-class-selector option:selected").val();
                    if (userid==0) {
                        $(this).dialog('close');
                        myalert('<?= $this->lang->line('no_user_selected_title') ?>', '<?= $this->lang->line('no_user_selected') ?>');
                        return false;
                    }    
                    window.location = '<?= site_url("classes/change_owner") ?>?classid=' + classid + '&newowner=' + userid;
                },
                
                '<?= $this->lang->line('cancel_button') ?>': function() {
                    $(this).dialog('close');
                }
            }
        }).dialog('open');
    }
</script>
