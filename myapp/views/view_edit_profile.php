    <?php $valerr = validation_errors();
      if (!empty($valerr))
          echo "<div class=\"error\">$valerr</div>\n";
    ?>

    <?= form_open("users/profile") ?>
      <table class="form">
        <tr>
          <td>User name:</td>
          <td>
            <?= $user_info->username ?> (Cannot be changed)
          </td>
        </tr>
        <tr>
          <td>First name:</td>
          <td><input type="text" name="first_name" value="<?= set_value('first_name',$user_info->first_name) ?>"> (Required)</td>
        </tr>
        <tr>
          <td>Last name:</td>
          <td><input type="text" name="last_name" value="<?= set_value('last_name',$user_info->last_name) ?>"> (Required)</td>
        </tr>
        <tr>
          <td>E-mail:</td>
          <td><input type="text" name="email" value="<?= set_value('email',$user_info->email) ?>"></td>
        </tr>
        <tr>
          <td>May see full WIVU database:</td>
          <td>
            <?= $user_info->may_see_wivu ? 'Yes' : 'No' ?> (Cannot be changed)
          </td>
        </tr>
        <tr>
          <td>New password:</td>
          <td>
            <input type="password" name="password1" value=""> (Leave blank if not changing password)
          </td>
        </tr>
        <tr>
          <td>Repeat new password:</td>
          <td>
            <input type="password" name="password2" value=""> (Leave blank if not changing password)
          </td>
        </tr>
      </table>
      <p><input class="makebutton" type="submit" name="submit" value="OK">
            <a class="makebutton" href="<?= site_url('/') ?>">Cancel</a></p>
    </form>

