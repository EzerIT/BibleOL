<?php if (!$is_logged_in): ?>
  <div class="warning">
       <h1>Warning:</h1>
       <p>You are not logged in. Statistics about your quizzes will not be recorded.</p>
  </div>
<?php endif; ?>

<?php if (!is_null($dirlist['parentdir'])): ?>
  <h1>This is the Folder <i><?= rtrim($dirlist['relativedir'],'/') ?></i></h1>
<?php else: ?>
  <h1>This is the Top Folder</h1>
<?php endif; ?>

<?php if (!is_null($dirlist['parentdir']) || !empty($dirlist['directories'])): ?>

  <h2>Select a folder</h2>

  <?php if (!is_null($dirlist['parentdir'])): ?>
    <p>
      <span class="ui-icon ui-icon-arrowreturnthick-1-w" style="display:inline-block;"></span>
      <?= anchor(site_url("text/select_quiz?dir={$dirlist['parentdir']}"), 'Parent') ?>
    </p>
  <?php endif; ?>

  <?php foreach ($dirlist['directories'] as $d): ?>
    <p>
      <span class="ui-icon ui-icon-folder-collapsed" style="display:inline-block;"></span>
      <?= anchor(site_url('text/select_quiz?dir=' . composedir($dirlist['relativedir'], $d)), $d) ?>
    </p>
  <?php endforeach; ?>
<?php endif; ?>

<?php if (!empty($dirlist['files'])): ?>
  <h2>Select an Exercise</h2>
  <table>
    <tr>
      <th>Quiz name</th>
      <th>Select number of questions<br>using preset passages</th>
      <th>Select number of questions<br>and specify your own passages</th>
    </tr>
   
  <?php foreach ($dirlist['files'] as $f): ?>
    <tr>
      <td><span class="ui-icon ui-icon-document" style="display:inline-block;"></span><?= $f ?></td>
      <td style="text-align: center;">
        <?= anchor(site_url('text/show_quiz?quiz=' . composedir($dirlist['relativedir'], $f) . '.3et&count=5'), '5') ?>&nbsp;&nbsp;&nbsp;
        <?= anchor(site_url('text/show_quiz?quiz=' . composedir($dirlist['relativedir'], $f) . '.3et&count=10'), '10') ?>&nbsp;&nbsp;&nbsp;
        <?= anchor(site_url('text/show_quiz?quiz=' . composedir($dirlist['relativedir'], $f) . '.3et&count=25'), '25') ?>
      </td>
      <td style="text-align: center;">
        <?= anchor(site_url('text/show_quiz_univ?quiz=' . composedir($dirlist['relativedir'], $f) . '.3et&count=5'), '5') ?>&nbsp;&nbsp;&nbsp;
        <?= anchor(site_url('text/show_quiz_univ?quiz=' . composedir($dirlist['relativedir'], $f) . '.3et&count=10'), '10') ?>&nbsp;&nbsp;&nbsp;
        <?= anchor(site_url('text/show_quiz_univ?quiz=' . composedir($dirlist['relativedir'], $f) . '.3et&count=25'), '25') ?>
      </td>
    </tr>
  <?php endforeach; ?>
  </table>
<?php endif; ?>
