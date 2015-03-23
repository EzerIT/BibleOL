    <?php $valerr = validation_errors();
      if (!empty($valerr))
          echo "<div class=\"error\">$valerr</div>\n";
    ?>

    <?= form_open("users/edit_one_user?userid=$userid") ?>
      <table class="form">
        <tr>
          <?php if ($user_info->google_login): ?>
            <td colspan="2">This user logs in with Google</td>
          <?php else: ?>
            <td>User name:</td>
            <td>
              <?php if ($userid==-1): ?>
                <input type="text" name="username" value="<?= set_value('username',$user_info->username) ?>"> (Required)
              <?php else: ?>
                <input type="hidden" name="username" value="<?= $user_info->username ?>">
                <?= $user_info->username ?> (Cannot be changed)
              <?php endif; ?>
            </td>
          <?php endif; ?>
      </tr>
        <tr>
          <td>First name:</td>
          <?php if ($user_info->google_login): ?>
            <td><?= $user_info->first_name ?> (Cannot be changed)</td>
          <?php else: ?>
            <td><input type="text" name="first_name" value="<?= set_value('first_name',$user_info->first_name) ?>"> (Required)</td>
          <?php endif; ?>
        </tr>
        <tr>
          <td>Last name:</td>
          <?php if ($user_info->google_login): ?>
            <td><?= $user_info->last_name ?> (Cannot be changed)</td>
          <?php else: ?>
            <td><input type="text" name="last_name" value="<?= set_value('last_name',$user_info->last_name) ?>"> (Required)</td>
          <?php endif; ?>
        </tr>
        <tr>
          <td>E-mail:</td>
          <?php if ($user_info->google_login): ?>
            <td><?= $user_info->email ?> (Cannot be changed)</td>
          <?php else: ?>
            <td><input type="text" name="email" value="<?= set_value('email',$user_info->email) ?>"></td>
          <?php endif; ?>
        </tr>
        <tr>
          <td>Administrator:</td>
          <td>
            <input class="narrow" type="radio" name="isadmin" value="yes" <?= set_radio('isadmin', 'yes', !!$user_info->isadmin) ?>>Yes
            <input class="narrow" type="radio" name="isadmin" value="no" <?= set_radio('isadmin', 'no', !$user_info->isadmin) ?>>No
          </td>
        </tr>
        <tr>
          <td>May see full WIVU database:</td>
          <td>
            <input class="narrow" type="radio" name="may_see_wivu" value="yes" <?= set_radio('may_see_wivu', 'yes', !!$user_info->may_see_wivu) ?>>Yes
            <input class="narrow" type="radio" name="may_see_wivu" value="no"  <?= set_radio('may_see_wivu', 'no',   !$user_info->may_see_wivu) ?>>No (Ignored for administrators)
          </td>
        </tr>
        <?php if (!$user_info->google_login): ?>
          <tr>
            <td><?= $userid==-1 ? 'Password' : 'New password' ?>:</td>
            <td>
              <input type="password" name="password1" value="">
              <?php if ($userid!=-1): ?>
                (Leave blank if not changing password)
              <?php else: ?>
                (Required)
              <?php endif; ?>
            </td>
          </tr>
          <tr>
            <td>Repeat <?= $userid==-1 ? '' : 'new' ?> password:</td>
            <td>
              <input type="password" name="password2" value="">
              <?php if ($userid!=-1): ?>
                (Leave blank if not changing password)
              <?php else: ?>
                (Required)
              <?php endif; ?>
            </td>
          </tr>
        <?php endif; ?>
      </table>
      <p><input class="makebutton" type="submit" name="submit" value="OK">
          <a class="makebutton" href="<?= site_url('users') ?>">Cancel</a></p>
    </form>
