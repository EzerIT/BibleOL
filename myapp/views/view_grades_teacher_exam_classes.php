<h1><?= $this->lang->line('Exam_grades_per_class_heading') ?></h1>

<?php if (count($classes)==0): ?>
     <h2><?= $this->lang->line('no_classes') ?></h2>
<?php else: ?>

  <table>
    <tr><th><?= $this->lang->line('class') ?></th><th style="padding-left:5px"><?= $this->lang->line('select_grouped_by') ?></th></tr>
  <?php foreach($classes as $cl): ?>
    <tr>
      <td><?= $cl->classname ?></td>
      <td style="padding-left:5px;text-align:center;">
       <a class="badge badge-primary" href="<?= site_url('grades/teacher_exam') ?>?classid=<?= $cl->id.($student?"&sid=".$this->mod_users->my_id():"") ?>"><?= $this->lang->line('grouped_by_exams') ?></a>
     </td>
    </tr>
  <?php endforeach; ?>
  </table>
<?php endif; ?>
