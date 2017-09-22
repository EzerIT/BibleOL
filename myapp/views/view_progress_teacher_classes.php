<h1>Statistics per Class</h1>

<?php if (count($classes)==0): ?>
     <h2>You have no classes</h2>
<?php else: ?>

  <table>
    <tr><th>Class</th><th style="padding-left:5px">Select statistics grouped by</th></tr>
  <?php foreach($classes as $cl): ?>
    <tr>
      <td><?= $cl->classname ?></td>
      <td style="padding-left:5px;text-align:center;">
       <a class="label label-primary" href="<?= site_url('statistics/teacher_time') ?>?classid=<?= $cl->id ?>">Students</a>
       <a class="label label-primary" href="<?= site_url('statistics/teacher_exercises') ?>?classid=<?= $cl->id ?>">Exercises</a>
     </td>
    </tr>
  <?php endforeach; ?>
  </table>
<?php endif; ?>
