<div class="table-responsive">
<table class="type2 table table-striped">
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
          <a class="label label-primary" href="<?= site_url("userclass/users_in_class?classid=$cl->clid") ?>"><?= str_replace(' ', '&nbsp;', $this->lang->line('assign_users')) ?></a>
          <a class="label label-primary" href="<?= site_url("classes/edit_one_class?classid=$cl->clid") ?>"><?= $this->lang->line('class_edit') ?></a>
          <a class="label label-danger" onclick="genericConfirmSm('<?= $this->lang->line('delete_class') ?>',
                                       '<?= sprintf($this->lang->line('delete_class_confirm'), "\'$cl->classname\'") ?>',
                                       '<?= site_url("classes/delete_class?classid=$cl->clid") ?>');
                        return false;"
               href="#"><?= $this->lang->line('class_delete') ?></a>
        <?php endif; ?>
        <?php if ($isadmin): ?>
          <a class="label label-primary" onclick="changeOwnerClass(<?= $cl->clid ?>); return false;" href="#"><?= $this->lang->line('change_owner_class') ?></a>
        <?php endif; ?>
      </td>
    </tr>
  <?php endforeach; ?>
</table>
<p><a class="btn btn-primary" href="<?= site_url("classes/edit_one_class?classid=-1") ?>"><?= $this->lang->line('add_class') ?></a></p>


  <?php //*********************************************************************
        // Change Owner dialog
        //*********************************************************************
    ?>
  <div id="chown-class-dialog" class="modal fade">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title"><?= $this->lang->line('change_owner_title') ?></h4>
        </div>
        <div class="modal-body">
          <div class="alert alert-danger" id="chown-class-error" role="alert">
            <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
            <span id="chown-class-error-text"></span>
          </div>
          <span><?= $this->lang->line('new_owner_prompt') ?></span>
          <select id="chown-class-selector">
            <option value="0" selected="selected"></option>
            <?php foreach ($teachers as $t): ?>
              <option value="<?= $t->id ?>"><?= $t->first_name . ' ' . $t->last_name ?></option>
            <?php endforeach; ?>
          </select>

          <input type="hidden" name="classid" id="classid-to-modify">
        </div>
        <div class="modal-footer">
          <button type="button" id="chown-class-dialog-ok" class="btn btn-primary"><?= $this->lang->line('OK_button') ?></button>
          <button type="button" class="btn btn-default" data-dismiss="modal"><?= $this->lang->line('cancel_button') ?></button>
        </div>
      </div>
    </div>
  </div>

  <script>
    $(function() {
        $('#chown-class-dialog-ok').click(function() {
            userid = $("#chown-class-selector option:selected").val();
            if (userid==0) {
                $('#chown-class-error-text').text('<?= $this->lang->line('no_user_selected') ?>');
                $('#chown-class-error').show();
                return false;
            }
            window.location = '<?= site_url("classes/change_owner") ?>?classid=' + $('#classid-to-modify').attr('value') + '&newowner=' + userid;
        });
    });


    function changeOwnerClass(classid) {
        $('#classid-to-modify').attr('value',classid);
        $('#chown-class-error').hide();
        $('#chown-class-dialog').modal('show');
    }
  </script>

</div>