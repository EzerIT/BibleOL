    <?php $valerr = validation_errors();
      if (!empty($valerr))
          echo "<div class=\"alert alert-danger\">$valerr</div>\n";
    ?>

    <script>
        $(function() {
            function set_full_name() {
                <?php if (!empty($user_info->oauth2_login)): ?>
                    var first_name = '<?= $user_info->first_name ?>';
                    var last_name = '<?= $user_info->last_name ?>';
                <?php else: ?>
                    var first_name = $('[name=first_name]').val();
                    var last_name = $('[name=last_name]').val();
                <?php endif; ?>
                
                if ($('[name=family_name_first]:checked').val()=='yes')
                    $('#fullname').html('（' + last_name + first_name + '）');
                else
                    $('#fullname').html('(' + first_name + ' ' + last_name + ')');
            }

            set_full_name();
                    
            $('[name=family_name_first]').on('change', set_full_name);
            $('[name=first_name]').on('input', set_full_name);
            $('[name=last_name]').on('input', set_full_name);
        });
                
    </script>
      
    <?php // $userid is -1 when an administrator creates a new user, -2 when a user creates their own account ?>

    <?= form_open($userid==-2 ? "users/sign_up" : "users/edit_one_user?userid=$userid&$extras",array('id'=>'thisform')) ?>
      <table class="form mb-3">
        <tr>
          <?php if (!empty($user_info->oauth2_login)): ?>
            <td colspan="3"><?= $this->lang->line("this_user_{$user_info->oauth2_login}") ?></td>
          <?php else: ?>
            <td><?= $this->lang->line('user_name') ?></td>
            <?php if ($userid==-1 || $userid==-2): ?>
              <td class="norb"><input type="text" name="username" value="<?= set_value('username',$user_info->username) ?>"></td>
              <td class="nolb"><?= $this->lang->line('field_required') ?></td>
            <?php else: ?>
              <td class="norb"><input type="hidden" name="username" value="<?= $user_info->username ?>"><?= $user_info->username ?></td>
              <td class="nolb"><?= $this->lang->line('cannot_change') ?></td>
            <?php endif; ?>
          <?php endif; ?>
        </tr>
          <?php if ($userid!=-2): ?>
            <tr>
              <td><?= $this->lang->line('first_name') ?></td>
              <?php if (!empty($user_info->oauth2_login)): ?>
                <td class="norb"><?= $user_info->first_name ?></td>
                <td class="nolb"><?= $this->lang->line('cannot_change') ?></td>
              <?php else: ?>
                <td class="norb"><input type="text" name="first_name" value="<?= set_value('first_name',$user_info->first_name) ?>"></td>
                <td class="nolb"><?= $this->lang->line('field_required') ?></td>
              <?php endif; ?>
            </tr>
            <tr>
              <td><?= $this->lang->line('last_name') ?></td>
              <?php if (!empty($user_info->oauth2_login)): ?>
                <td class="norb"><?= $user_info->last_name ?></td>
                <td class="nolb"><?= $this->lang->line('cannot_change') ?></td>
              <?php else: ?>
                <td class="norb"><input type="text" name="last_name" value="<?= set_value('last_name',$user_info->last_name) ?>"></td>
                <td class="nolb"><?= $this->lang->line('field_required') ?></td>
              <?php endif; ?>
            </tr>
            <tr>
              <td><?= $this->lang->line('chinese_name_order') ?></td>
              <td class="norb">
                <input class="narrow" type="radio" name="family_name_first" value="yes" <?= set_radio('family_name_first', 'yes', !!$user_info->family_name_first) ?>><?= $this->lang->line('yes') ?>
                <input class="narrow" type="radio" name="family_name_first" value="no" <?= set_radio('family_name_first', 'no', !$user_info->family_name_first) ?>><?= $this->lang->line('no') ?>
              </td>
              <td id="fullname" class="nolb"></td>
            </tr>
          <?php endif; ?>
        <tr>
          <td><?= $this->lang->line('email') ?></td>
          <?php if (!empty($user_info->oauth2_login)): ?>
            <td class="norb"><?= $user_info->email ?></td>
            <td class="nolb"><?= $this->lang->line('cannot_change') ?></td>
          <?php else: ?>
            <td class="norb"><input type="text" name="email" value="<?= set_value('email',$user_info->email) ?>"></td>
            <td class="nolb"><?= $userid==-2 ? $this->lang->line('field_required') : '' ?></td>
          <?php endif; ?>
        </tr>
        <tr>
          <td><?= $this->lang->line('preferred_language') ?></td>
          <td class="norb">
            <select name="preflang">
              <option value="none" <?= set_select('preflang', 'none', $curlang=='none') ?>><?= $this->lang->line('no_language') ?></option>
              <?php foreach ($avail_if_trans as $ift): ?>
                <option value="<?= $ift->abb ?>" <?= set_select('preflang', $ift->abb, $curlang==$ift->abb) ?>><?= $this->lang->line($ift->internal) ?></option>
              <?php endforeach; ?>

            </select>
          </td>
          <td class="nolb"></td>
        </tr>
        <?php if (!empty($this->config->item('variants'))): ?>
          <tr>
            <td><?= $this->lang->line('preferred_variant') ?></td>
            <td class="norb">
              <select name="prefvariant">
               <option value="none" <?= set_select('prefvariant', 'none', $curvariant=='none') ?>><?= $this->lang->line('no_variant_option') ?></option>
               <option value="main" <?= set_select('prefvariant', 'main', $curvariant=='main') ?>><?= $this->lang->line('main_variant') ?></option>

               <?php foreach ($this->config->item('variants') as $var): ?>
                 <option value="<?= $var ?>" <?= set_select('prefvariant', $var, $curvariant==$var) ?>><?= $var ?></option>
               <?php endforeach; ?>

              </select>
            </td>
            <td class="nolb"></td>
          </tr>
        <?php endif; ?>
        <?php if ($isadmin): ?>
          <tr>
            <td><?= $this->lang->line('administrator') ?></td>
            <td class="norb">
              <input class="narrow" type="radio" name="isadmin" value="yes" <?= set_radio('isadmin', 'yes', !!$user_info->isadmin) ?>><?= $this->lang->line('yes') ?>
              <input class="narrow" type="radio" name="isadmin" value="no" <?= set_radio('isadmin', 'no', !$user_info->isadmin) ?>><?= $this->lang->line('no') ?>
            </td>
            <td class="nolb"></td>
          </tr>
          <tr>
            <td><?= $this->lang->line('teacher') ?></td>
            <td class="norb">
              <input class="narrow" type="radio" name="isteacher" value="yes" <?= set_radio('isteacher', 'yes', !!$user_info->isteacher) ?>><?= $this->lang->line('yes') ?>
              <input class="narrow" type="radio" name="isteacher" value="no" <?= set_radio('isteacher', 'no', !$user_info->isteacher) ?>><?= $this->lang->line('no') ?>
            </td>
            <td class="nolb"></td>
          </tr>
          <tr>
            <td><?= $this->lang->line('translator') ?></td>
            <td class="norb">
              <input class="narrow" type="radio" name="istranslator" value="yes" <?= set_radio('istranslator', 'yes', !!$user_info->istranslator) ?>><?= $this->lang->line('yes') ?>
              <input class="narrow" type="radio" name="istranslator" value="no" <?= set_radio('istranslator', 'no', !$user_info->istranslator) ?>><?= $this->lang->line('no') ?>
            </td>
            <td class="nolb"></td>
          </tr>
        <?php endif; ?>
        <?php if (empty($user_info->oauth2_login) && $userid!=-2): ?>
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

      <p style="height:2px">&nbsp;</p>
      <p><input class="btn btn-primary" type="submit" name="submit" value="<?= $this->lang->line('OK_button') ?>">
          <a class="btn btn-outline-dark" href="<?= site_url($userid==-2 ? "/" : "users?$extras") ?>"><?= $this->lang->line('cancel_button') ?></a></p>
    </form>
