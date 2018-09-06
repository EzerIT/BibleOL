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
      <table class="form">
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
              <option value="en" <?= set_select('preflang', 'en', $curlang=='en') ?>><?= $this->lang->line('english') ?></option>
              <option value="de" <?= set_select('preflang', 'de', $curlang=='de') ?>><?= $this->lang->line('german') ?></option>
              <option value="da" <?= set_select('preflang', 'da', $curlang=='da') ?>><?= $this->lang->line('danish') ?></option>
              <option value="fr" <?= set_select('preflang', 'fr', $curlang=='fr') ?>><?= $this->lang->line('french') ?></option>
              <option value="nl" <?= set_select('preflang', 'nl', $curlang=='nl') ?>><?= $this->lang->line('dutch') ?></option>
              <option value="pt" <?= set_select('preflang', 'pt', $curlang=='pt') ?>><?= $this->lang->line('portuguese') ?></option>
              <option value="es" <?= set_select('preflang', 'es', $curlang=='es') ?>><?= $this->lang->line('spanish') ?></option>
              <option value="zh-simp" <?= set_select('preflang', 'zh-simp', $curlang=='zh-simp') ?>><?= $this->lang->line('simp_chinese') ?></option>
              <option value="zh-trad" <?= set_select('preflang', 'zh-trad', $curlang=='zh-trad') ?>><?= $this->lang->line('trad_chinese') ?></option>
            </select>
          </td>
          <td class="nolb"></td>
        </tr>
        <?php if ($isadmin): ?>
          <tr>
            <td><?= $this->lang->line('administrator') ?></td>
            <td class="norb">
              <input class="narrow" type="radio" name="isadmin" value="yes" <?= set_radio('isadmin', 'yes', !!$user_info->isadmin) ?>><?= $this->lang->line('yes') ?>
              <input class="narrow" type="radio" name="isadmin" value="no" <?= set_radio('isadmin', 'no', !$user_info->isadmin) ?>><?= $this->lang->line('no') ?>
            </td>
            <td class="nolb"></td>
          </tr>
        <?php endif; ?>
        <?php if ($isteacher): ?>
          <tr>
            <td><?= $this->lang->line('teacher') ?></td>
            <td class="norb">
              <input class="narrow" type="radio" name="isteacher" value="yes" <?= set_radio('isteacher', 'yes', !!$user_info->isteacher) ?>><?= $this->lang->line('yes') ?>
              <input class="narrow" type="radio" name="isteacher" value="no" <?= set_radio('isteacher', 'no', !$user_info->isteacher) ?>><?= $this->lang->line('no') ?>
            </td>
            <td class="nolb"></td>
          </tr>
        <?php endif; ?>
        <?php if ($istranslator): ?>
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

      <?php if ($userid==-2): ?>
        <div class="panel panel-default" style="margin-top: 20px">
          <div class="panel-body">
            <?php
               $this->lang->load('privacy', $this->language);
               // Detect language of privacy policy
               if (preg_match('/^\(([^)]*)\)(.*)/s', $this->lang->line('privacy_text'), $matches)) {
                   $policy_lang = $matches[1];
                   echo $matches[2];
               }
               else {
                   $policy_lang = 'Unknown';
                   echo $this->lang->line('privacy_text');
               }
            ?>
          </div>
        </div>

        <div class="panel panel-default">
          <div class="panel-heading"><?= $this->lang->line('do_you_accept') ?></div>
          <div class="panel-body">
            <input type="hidden" name="policy_lang" value="<?= $policy_lang ?>" />
            <p><input class="btn btn-primary" type="button" onclick="$('#thisform').submit()" name="emulsubmit" value="<?= $this->lang->line('OK_button') ?>">
              <a class="btn btn-default" href="<?= site_url('/') ?>"><?= $this->lang->line('cancel_button') ?></a></p>
          </div>
        </div>
      <?php else: ?>
        <p style="height:2px">&nbsp;</p>
        <p><input class="btn btn-primary" type="submit" name="submit" value="<?= $this->lang->line('OK_button') ?>">
          <a class="btn btn-default" href="<?= site_url("users?$extras") ?>"><?= $this->lang->line('cancel_button') ?></a></p>
      <?php endif; ?>
    </form>
