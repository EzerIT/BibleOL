<?php if (!$is_logged_in): ?>
  <div class="alert alert-warning">
       <h1><?= $this->lang->line('warning') ?></h1>
       <p><?= $this->lang->line('not_logged_in') ?></p>
  </div>
<?php endif; ?>

<?php if (!is_null($dirlist['parentdir'])): ?>
  <h1><?= sprintf($this->lang->line('this_is_folder'), rtrim($dirlist['relativedir'],'/')) ?></h1>
<?php else: ?>
  <h1><?= $this->lang->line('this_is_top_folder') ?></h1>
<?php endif; ?>

<?php if (!is_null($dirlist['parentdir']) || !empty($dirlist['directories'])): ?>

  <h2><?= $this->lang->line('select_folder') ?></h2>

  <?php if (!is_null($dirlist['parentdir'])): ?>
    <p>
      <span class="glyphicon glyphicon-arrow-up" style="display:inline-block;"></span>
      <?= anchor(build_get('text/select_quiz',array('dir' => $dirlist['parentdir'])), $this->lang->line('parent')) ?>
    </p>
  <?php endif; ?>

  <?php foreach ($dirlist['directories'] as $d): ?>
    <p>
      <span class="glyphicon glyphicon-folder-close" style="display:inline-block;"></span>
      <?php if ($d[1]): /* Directory is visible to user */ ?>
        <?= anchor(build_get('text/select_quiz',array('dir' => composedir($dirlist['relativedir'], $d[0]))), $d[0]) ?>
      <?php else: ?>
        <?= anchor(build_get('userclass/enroll_by_folder',array('dir' => composedir($dirlist['relativedir'], $d[0]),
                                                                'curdir' => $curdir)), $d[0]) ?> <?= translate('(Restricted access)') ?>
      <?php endif; ?>
    </p>
  <?php endforeach; ?>
<?php endif; ?>

<?php if (!empty($dirlist['files'])): ?>
  <h2><?= $this->lang->line('select_exercise') ?></h2>
  <table class="type2 table table-striped">
    <tr>
      <th><?= $this->lang->line('quiz_name') ?></th>
      <th class="centeralign"><?= $this->lang->line('select_number_preset') ?></th>
      <th class="centeralign"><?= $this->lang->line('select_number_own') ?></th>
    </tr>
   
  <?php foreach ($dirlist['files'] as $f): ?>
    <tr>
      <td><span class="glyphicon glyphicon-file" style="display:inline-block;"></span><?= $f->filename ?></td>
      <td style="text-align: center;">
        <?= anchor(build_get('text/show_quiz',array('quiz' => composedir($dirlist['relativedir'], $f->filename) . '.3et', 'count' => 5)), '5') ?>&nbsp;&nbsp;&nbsp;
        <?= anchor(build_get('text/show_quiz',array('quiz' => composedir($dirlist['relativedir'], $f->filename) . '.3et', 'count' => 10)), '10') ?>&nbsp;&nbsp;&nbsp;
        <?= anchor(build_get('text/show_quiz',array('quiz' => composedir($dirlist['relativedir'], $f->filename) . '.3et', 'count' => 25)), '25') ?>
      </td>
      <td style="text-align: center;">
        <?= anchor(build_get('text/show_quiz_univ',array('quiz' => composedir($dirlist['relativedir'], $f->filename) . '.3et', 'count' => 5)), '5') ?>&nbsp;&nbsp;&nbsp;
        <?= anchor(build_get('text/show_quiz_univ',array('quiz' => composedir($dirlist['relativedir'], $f->filename) . '.3et', 'count' => 10)), '10') ?>&nbsp;&nbsp;&nbsp;
        <?= anchor(build_get('text/show_quiz_univ',array('quiz' => composedir($dirlist['relativedir'], $f->filename) . '.3et', 'count' => 25)), '25') ?>
      </td>
    </tr>
  <?php endforeach; ?>
  </table>
<?php endif; ?>
