<h1><?= sprintf($this->lang->line('enter_password_for'), $classname) ?></h1>

<?php $valerr = validation_errors('<p class="error">','</p>'); ?>


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
<p><input class="makebutton" type="submit" name="submit" value="<?= $this->lang->line('OK_button') ?>">
   <a class="makebutton" href="<?= site_url('/userclass/enroll') ?>"><?= $this->lang->line('cancel_button') ?></a></p>
</p>
</form>

