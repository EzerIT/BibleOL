<h1><?= sprintf($this->lang->line('enter_password_for'), $classname) ?></h1>

<?php $valerr = validation_errors('<p class="alert alert-danger">','</p>'); ?>


<?= form_open("userclass/enroll_in?classid=$classid") ?>

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
   <a class="btn btn-default" href="<?= site_url('/userclass/enroll') ?>"><?= $this->lang->line('cancel_button') ?></a></p>
</p>
</form>

