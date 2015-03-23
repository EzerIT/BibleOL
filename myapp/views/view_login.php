   <?php $valerr = validation_errors('<p class="error">','</p>'); ?>

    <div id="logincenter">
      <h1>Please Log In</h1>
      <div class="ui-corner-all" id="loginbox">
        <?= !empty($valerr) ? $valerr : ''?>
        <?= form_open('login') ?>
          <table>
            <tr><td>User name</td><td><input class="logintext" type="text" name="login_name" /></td></tr>
            <tr><td>Password</td><td><input class="logintext" type="password" name="password" /></td></tr>
          </table>
          <input class="makebutton" class="button" type="submit" name="submit" value="Login" />
        </form>
        <p><a href="<?= site_url('/users/forgot_pw') ?>">Forgotten user name or password?</a></p>
      </div>
      <div id="googlebox">
        <p>or</p>
        <p><a class="zocial googleplus" href="https://accounts.google.com/o/oauth2/auth?<?= $google_request ?>">Sign in with Google+</a></p>
      </div>
    </div>   
