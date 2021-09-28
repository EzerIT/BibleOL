<h1><?= $this->lang->line('grades_per_class_heading') ?></h1>

<?php if (count($classes)==0): ?>
     <h2><?= $this->lang->line('no_classes') ?></h2>
<?php else: ?>

  <table>
    <tr><th><?= $this->lang->line('class') ?></th><th style="padding-left:5px"><?= $this->lang->line('select_grouped_by') ?></th></tr>
  <?php foreach($classes as $cl): ?>
    <tr>
      <td><?= $cl->classname ?></td>
      <td style="padding-left:5px;text-align:center;">
       <a class="badge badge-primary" href="<?= site_url('grades/teacher_time') ?>?classid=<?= $cl->id ?>"><?= $this->lang->line('grouped_by_students') ?></a>
       <a class="badge badge-primary" href="<?= site_url('grades/teacher_exercises') ?>?classid=<?= $cl->id ?>"><?= $this->lang->line('grouped_by_exercises') ?></a>
     </td>
    </tr>
  <?php endforeach; ?>
  </table>
<?php endif; ?>
