<?php $valerr = validation_errors('<p class="error">','</p>'); ?>

<div id="logincenter">
  <?php if (!$sent): ?>
    <h1><?= $this->lang->line('forgot_name_or_password') ?></h1>
    <div class="ui-corner-all" id="loginbox">
      <?= !empty($valerr) ? $valerr : ''?>
      <p><?= $this->lang->line('type_name_or_email') ?></p>
      <?= form_open('/users/forgot_pw') ?>
        <table>
          <tr><td><?= $this->lang->line('user_name') ?></td><td><input type="text" name="username" size="20" /></td></tr>
          <tr><td><?= $this->lang->line('email_address') ?></td><td><input type="text" name="email" size="20" /></td></tr>
        </table>
        <input class="makebutton" type="submit" name="submit" value="<?= $this->lang->line('OK_button') ?>" />
        <input class="makebutton" type="button" name="cancel" value="<?= $this->lang->line('cancel_button') ?>" onClick="location='<?=site_url('/login')?>'" />
      </form>
    </div>
  <?php else: ?>
    <h1><?= sprintf($this->lang->line('email_sent_msg'), $user_found->email) ?></h1>
  <?php endif; ?>
</div>   
