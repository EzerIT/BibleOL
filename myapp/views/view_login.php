<?php $valerr = validation_errors('<p class="alert alert-danger">','</p>'); ?>


<div class="center-block" id="logincenter">
  <div class="panel panel-primary">
    <div class="panel-heading">
       <h1 class="panel-title"><?= $this->lang->line('please_log_in') ?></h1>
    </div>
    <div class="panel-body">
      <?= !empty($valerr) ? $valerr : ''?>
      <?= form_open('login') ?>
          
        <table class="padded">
          <tr>
            <td><label for="login_name" class="control-label"><?= $this->lang->line('user_name') ?></label></td>
            <td><input type="text" id="login_name" name="login_name"></td>
          </tr>
          <tr>
            <td><label for="password" class="control-label"><?= $this->lang->line('password') ?></label></td>
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
   
  <?php if ($google_login_enabled): ?>
    <p class="text-center"><?= $this->lang->line('or') ?></p>
    <p class="text-center"><a class="zocial googleplus" href="https://accounts.google.com/o/oauth2/auth?<?= htmlspecialchars($google_request) ?>"><?= $this->lang->line('sign_in_google') ?></a></p>
    <p class="text-center"><a class="zocial facebook" href="https://www.facebook.com/dialog/oauth?<?= htmlspecialchars($facebook_request) ?>"><?= $this->lang->line('sign_in_facebook') ?></a></p>
 
  <p class="text-justify"><?= $this->lang->line('warn_multiple_login') ?></p>
  <?php endif; ?>
</div>
