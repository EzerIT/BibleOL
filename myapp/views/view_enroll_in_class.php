<h2><?= $this->lang->line('you_can_enroll_in') ?></h2>

<?php if (empty($avail_classes)): ?>
  <div class="alert alert-warning"><?= $this->lang->line('no_classes_enroll') ?></div>
<?php else: ?>
<table class="type2 table table-striped">
  <tr>
    <th><?= $this->lang->line('class_name') ?></th>
    <th><?= $this->lang->line('class_operations') ?></th>
  </tr>
  <?php foreach ($avail_classes as $clid): ?>
    <?php $cl = $all_classes[$clid]; ?>
    <tr>
      <td class="leftalign" style="width:200px"><?= $cl->classname ?></td>
      <td class="leftalign">
        <?= anchor(build_get('userclass/enroll_in',array('classid' => $cl->clid)),
                   $this->lang->line('enroll'),
                   array('class' => 'label label-primary')) ?>

      </td>
    </tr>
  <?php endforeach; ?>
</table>

<?php endif; ?>


<h2><?= $this->lang->line('you_are_enrolled_in') ?></h2>

<?php if (empty($old_classes)): ?>
  <div class="alert alert-warning"><?= $this->lang->line('no_classes') ?></div>
<?php else: ?>

<table class="type2 table table-striped">
  <tr>
    <th><?= $this->lang->line('class_name') ?></th>
    <th><?= $this->lang->line('class_operations') ?></th>
  </tr>
  <?php foreach ($old_classes as $clid => $access): ?>
    <?php $cl = $all_classes[$clid]; ?>
    <tr>
      <td class="leftalign" style="width:200px"><?= $cl->classname ?></td>
      <td class="leftalign">
        <a class="label label-warning" onclick="genericConfirmSm('<?= $this->lang->line('unenroll_class') ?>',
                                       '<?= sprintf($this->lang->line('unenroll_class_confirm'), "\'$cl->classname\'") ?>',
                                       '<?= site_url("userclass/unenroll_from?classid=$cl->clid") ?>');
                        return false;"
        href="#"><?= $this->lang->line('unenroll') ?></a><br>
        <?php
            if ($access) {
                echo $this->lang->line('teacher_can_access'),'&nbsp;',
                    anchor(build_get('userclass/manage_access',array('classid' => $cl->clid, 'grant' => false)),
                           $this->lang->line('revoke_access'),
                           array('class' => 'label label-danger'));
            }
            else {
                echo $this->lang->line('teacher_cannot_access'),'&nbsp;',
                    anchor(build_get('userclass/manage_access',array('classid' => $cl->clid, 'grant' => true)),
                           $this->lang->line('grant_access'),
                           array('class' => 'label label-success'));
            }
        ?>
      </td>
    </tr>
  <?php endforeach; ?>
</table>

<?php endif; ?>
