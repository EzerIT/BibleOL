    <?php $valerr = validation_errors();
      if (!empty($valerr))
          echo "<div class=\"error\">$valerr</div>\n";
    ?>

    <?= form_open("users/edit_one_user?userid=$userid") ?>
      <table class="form">
        <tr>
          <?php if ($user_info->google_login): ?>
            <td colspan="3"><?= $this->lang->line('this_user_google') ?></td>
          <?php else: ?>
            <td><?= $this->lang->line('user_name') ?></td>
            <?php if ($userid==-1): ?>
              <td class="norb"><input type="text" name="username" value="<?= set_value('username',$user_info->username) ?>"></td>
              <td class="nolb"><?= $this->lang->line('field_required') ?></td>
            <?php else: ?>
              <td class="norb"><input type="hidden" name="username" value="<?= $user_info->username ?>"><?= $user_info->username ?></td>
              <td class="nolb"><?= $this->lang->line('cannot_change') ?></td>
            <?php endif; ?>
          <?php endif; ?>
      </tr>
        <tr>
          <td><?= $this->lang->line('first_name') ?></td>
          <?php if ($user_info->google_login): ?>
            <td class="norb"><?= $user_info->first_name ?></td>
            <td class="nolb"><?= $this->lang->line('cannot_change') ?></td>
          <?php else: ?>
            <td class="norb"><input type="text" name="first_name" value="<?= set_value('first_name',$user_info->first_name) ?>"></td>
            <td class="nolb"><?= $this->lang->line('field_required') ?></td>
          <?php endif; ?>
        </tr>
        <tr>
          <td><?= $this->lang->line('last_name') ?></td>
          <?php if ($user_info->google_login): ?>
            <td class="norb"><?= $user_info->last_name ?></td>
            <td class="nolb"><?= $this->lang->line('cannot_change') ?></td>
          <?php else: ?>
            <td class="norb"><input type="text" name="last_name" value="<?= set_value('last_name',$user_info->last_name) ?>"></td>
            <td class="nolb"><?= $this->lang->line('field_required') ?></td>
          <?php endif; ?>
        </tr>
        <tr>
          <td><?= $this->lang->line('email') ?></td>
          <?php if ($user_info->google_login): ?>
            <td class="norb"><?= $user_info->email ?></td>
            <td class="nolb"><?= $this->lang->line('cannot_change') ?></td>
          <?php else: ?>
            <td class="norb"><input type="text" name="email" value="<?= set_value('email',$user_info->email) ?>"></td>
            <td class="nolb"></td>
          <?php endif; ?>
        </tr>
        <tr>
          <td><?= $this->lang->line('administrator') ?></td>
          <td class="norb">
            <input class="narrow" type="radio" name="isadmin" value="yes" <?= set_radio('isadmin', 'yes', !!$user_info->isadmin) ?>><?= $this->lang->line('yes') ?>
            <input class="narrow" type="radio" name="isadmin" value="no" <?= set_radio('isadmin', 'no', !$user_info->isadmin) ?>><?= $this->lang->line('no') ?>
          </td>
          <td class="nolb"></td>
        </tr>
        <?php if (!$user_info->google_login): ?>
          <tr>
            <td><?= $userid==-1 ? $this->lang->line('password') : $this->lang->line('new_password') ?></td>
            <td class="norb"><input type="password" name="password1" value=""></td>
            <td class="nolb">
                <?php if ($userid!=-1): ?>
                  <?= $this->lang->line('leave_blank_pw') ?>
                <?php else: ?>
                  <?= $this->lang->line('field_required') ?>
                <?php endif; ?>
            </td>
          </tr>
          <tr>
            <td><?= $userid==-1 ? $this->lang->line('repeat_password') : $this->lang->line('repeat_new_password') ?></td>
            <td class="norb"><input type="password" name="password2" value=""></td>
            <td class="nolb">
              <?php if ($userid!=-1): ?>
                <?= $this->lang->line('leave_blank_pw') ?>
              <?php else: ?>
                <?= $this->lang->line('field_required') ?>
              <?php endif; ?>
            </td>
          </tr>
        <?php endif; ?>
      </table>
      <p><input class="makebutton" type="submit" name="submit" value="<?= $this->lang->line('OK_button') ?>">
          <a class="makebutton" href="<?= site_url('users') ?>"><?= $this->lang->line('cancel_button') ?></a></p>
    </form>
