<?php $valerr = validation_errors('<p class="error">','</p>'); ?>

<div id="logincenter">
  <?php if (!$sent): ?>
    <h1>Forgot user name or password</h1>
    <div class="ui-corner-all" id="loginbox">
      <?= !empty($valerr) ? $valerr : ''?>
      <p>Type user name or e-mail address</p>
      <?= form_open('/users/forgot_pw') ?>
        <table>
          <tr><td>User name:</td><td><input type="text" name="username" size="20" /></td></tr>
          <tr><td>E-mail address:</td><td><input type="text" name="email" size="20" /></td></tr>
        </table>
        <input class="makebutton" type="submit" name="submit" value="OK" />
        <input class="makebutton" type="button" name="cancel" value="Cancel" onClick="location='<?=site_url('/login')?>'" />
      </form>
    </div>
  <?php else: ?>
    <h1>An e-mail containing your user name and information about how to reset your password has been sent to
         <?= $user_found->email ?></h1>
  <?php endif; ?>
</div>   
