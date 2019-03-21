<?php $valerr = validation_errors('<p class="alert alert-danger">','</p>'); ?>


<div class="mx-auto" id="logincenter">
  <div class="card mt-3">
    <h5 class="card-header bg-primary text-light"><?= $this->lang->line('please_log_in') ?></h5>
    <div class="card-body">
      <?= !empty($valerr) ? $valerr : ''?>
      <?= form_open('login') ?>
          
        <table class="padded">
          <tr>
            <td><label for="login_name" class="col-form-label text-nowrap"><?= $this->lang->line('user_name') ?></label></td>
            <td><input type="text" id="login_name" name="login_name"></td>
          </tr>
          <tr>
            <td><label for="password" class="col-form-label text-nowrap"><?= $this->lang->line('password') ?></label></td>
            <td><input class="logintext" type="password" id="password" name="password"></td>
          </tr>
        </table>

        <p style="height:2px">&nbsp;</p>
 
        <input class="btn btn-primary" type="submit" name="submit" value="<?= $this->lang->line('login_button') ?>" />
      </form>

      <p style="height:2px">&nbsp;</p>
      <p><a href="<?= site_url('/users/forgot_pw') ?>"><?= $this->lang->line('forgotten') ?></a></p>
      <p><a href="<?= site_url('/users/sign_up') ?>"><?= $this->lang->line('sign_up') ?></a></p>
    </div>
  </div>
   
  <?php if ($google_login_enabled || $facebook_login_enabled): ?>
    <p class="text-center"><?= $this->lang->line('or') ?></p>
    <?php if ($google_login_enabled): ?>
      <p class="text-center"><a class="googlelogin" href="https://accounts.google.com/o/oauth2/auth?<?= htmlspecialchars($google_request) ?>"><img src="images/google.png"><span><?= $this->lang->line('sign_in_google') ?></span></a></p>
    <?php endif; ?>
    <?php if ($facebook_login_enabled): ?>
      <p class="text-center"><a class="zocial facebook" href="https://www.facebook.com/dialog/oauth?<?= htmlspecialchars($facebook_request) ?>"><?= $this->lang->line('sign_in_facebook') ?></a></p>
    <?php endif; ?>
    <p class="text-justify"><?= $this->lang->line('warn_multiple_login') ?></p>
  <?php endif; ?>
</div>
