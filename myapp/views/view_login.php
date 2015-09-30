   <?php $valerr = validation_errors('<p class="error">','</p>'); ?>

    <div id="logincenter">
      <h1><?= $this->lang->line('please_log_in') ?></h1>
      <div class="ui-corner-all" id="loginbox">
        <?= !empty($valerr) ? $valerr : ''?>
        <?= form_open('login') ?>
          <table>
            <tr><td><?= $this->lang->line('user_name') ?></td><td><input class="logintext" type="text" name="login_name" /></td></tr>
            <tr><td><?= $this->lang->line('password') ?></td><td><input class="logintext" type="password" name="password" /></td></tr>
          </table>
          <input class="makebutton" class="button" type="submit" name="submit" value="<?= $this->lang->line('login_button') ?>" />
        </form>
        <p><a href="<?= site_url('/users/forgot_pw') ?>"><?= $this->lang->line('forgotten') ?></a></p>
        <p><a href="<?= site_url('/users/sign_up') ?>"><?= $this->lang->line('sign_up') ?></a></p>
      </div>
      <?php if ($google_login_enabled): ?>
         <div id="oauth2box">
          <p><?= $this->lang->line('or') ?></p>
          <p><a class="zocial googleplus" href="https://accounts.google.com/o/oauth2/auth?<?= $google_request ?>"><?= $this->lang->line('sign_in_google') ?></a></p>
          <p><a class="zocial facebook" href="https://www.facebook.com/dialog/oauth?<?= $facebook_request ?>"><?= $this->lang->line('sign_in_facebook') ?></a></p>
        </div>
        <div id="oauth2text">
          <p><?= $this->lang->line('warn_multiple_login') ?></p>
        </div>
      <?php endif; ?>
    </div>   
