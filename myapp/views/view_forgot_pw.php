<?php $valerr = validation_errors('<p class="alert alert-danger">','</p>'); ?>

<?php if (!$sent): ?>
  <h1><?= $this->lang->line('forgot_name_or_password') ?></h1>
    <?= !empty($valerr) ? $valerr : ''?>
    <p><?= $this->lang->line('type_name_or_email') ?></p>
    <?= form_open('/users/forgot_pw') ?>
      <table class="padded">
        <tr>
          <td><label for="username" class="control-label"><?= $this->lang->line('user_name') ?></label></td>
          <td><input type="text" id="username" name="username"></td>
        </tr>
        <tr>
          <td><label for="email" class="control-label"><?= $this->lang->line('email_address') ?></label></td>
          <td><input type="text" id="email" name="email"></td>
        </tr>
      </table>

      <p style="height:2px">&nbsp;</p>

      <input class="btn btn-primary" type="submit" name="submit" value="<?= $this->lang->line('OK_button') ?>" />
      <input class="btn btn-default" type="button" name="cancel" value="<?= $this->lang->line('cancel_button') ?>" onClick="location='<?=site_url('/login')?>'" />
    </form>
<?php else: ?>
  <h1><?= sprintf($this->lang->line('email_sent_msg'), $user_found->email) ?></h1>
<?php endif; ?>


