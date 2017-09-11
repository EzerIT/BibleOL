<h1><?= sprintf($this->lang->line('enter_password_for'), $classname) ?></h1>

<?php $valerr = validation_errors('<p class="alert alert-danger">','</p>'); ?>

<?= form_open(build_get('/userclass/enroll_in',array('classid' => $classid,
                                                     'dir' => $dir,
                                                     'curdir' => $curdir))) ?>

<?= !empty($valerr) ? $valerr : ''?>


<table class="form">
  <tr>
    <td><?= $this->lang->line('class_password') ?></td>
    <td>
      <input type="password" name="password" value="">
    </td>
  </tr>
</table>

<p style="height:2px">&nbsp;</p>

<p><input class="btn btn-primary" type="submit" name="submit" value="<?= $this->lang->line('OK_button') ?>">
   <?= anchor(is_null($curdir) ? '/userclass/enroll' : build_get('/text/select_quiz', array('dir' => $curdir)),
              $this->lang->line('cancel_button'),
              'class="btn btn-default"') ?>
</p>
</form>

