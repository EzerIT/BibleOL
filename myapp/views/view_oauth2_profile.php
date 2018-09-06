    <?php $valerr = validation_errors();
      if (!empty($valerr))
          echo "<div class=\"alert alert-danger\">$valerr</div>\n";
    ?>

      <script>
        $(function() {
            function set_full_name() {
                var first_name = '<?= $user_info->first_name ?>';
                var last_name = '<?= $user_info->last_name ?>';
                
                if ($('[name=family_name_first]:checked').val()=='yes')
                    $('#fullname').html('（' + last_name + first_name + '）');
                else
                    $('#fullname').html('(' + first_name + ' ' + last_name + ')');
            }

            set_full_name();
                    
            $('[name=family_name_first]').on('change', set_full_name);
        });
                
    </script>

    <?= form_open("users/profile") ?>
      <table class="form">
        <tr>
          <td colspan="2" class="norb"><?= $this->lang->line("you_login_{$user_info->oauth2_login}"); ?></td>
          <td class="nolb"></td>
        </tr>
        <tr>
          <td><?= $this->lang->line('first_name'); ?></td>
          <td class="norb"><?= $user_info->first_name ?></td>
          <td class="nolb"></td>
        </tr>
        <tr>
          <td><?= $this->lang->line('last_name'); ?></td>
          <td class="norb"><?= $user_info->last_name ?></td>
          <td class="nolb"></td>
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
          <td><?= $this->lang->line('email'); ?></td>
          <td class="norb"><?= $user_info->email ?></td>
          <td class="nolb"></td>
        </tr>
        <tr>
          <td><?= $this->lang->line('preferred_language') ?></td>
          <td class="norb">
            <select name="preflang">
              <option value="none" <?= set_select('preflang', 'none', $user_info->preflang=='none') ?>><?= $this->lang->line('no_language') ?></option>
              <option value="en" <?= set_select('preflang', 'en', $user_info->preflang=='en') ?>><?= $this->lang->line('english') ?></option>
              <option value="de" <?= set_select('preflang', 'de', $user_info->preflang=='de') ?>><?= $this->lang->line('german') ?></option>
              <option value="da" <?= set_select('preflang', 'da', $user_info->preflang=='da') ?>><?= $this->lang->line('danish') ?></option>
              <option value="fr" <?= set_select('preflang', 'fr', $user_info->preflang=='fr') ?>><?= $this->lang->line('french') ?></option>
              <option value="nl" <?= set_select('preflang', 'nl', $user_info->preflang=='nl') ?>><?= $this->lang->line('dutch') ?></option>
              <option value="pt" <?= set_select('preflang', 'pt', $user_info->preflang=='pt') ?>><?= $this->lang->line('portuguese') ?></option>
              <option value="es" <?= set_select('preflang', 'es', $user_info->preflang=='es') ?>><?= $this->lang->line('spanish') ?></option>
              <option value="zh-simp" <?= set_select('preflang', 'zh-simp', $user_info->preflang=='zh-simp') ?>><?= $this->lang->line('simp_chinese') ?></option>
              <option value="zh-trad" <?= set_select('preflang', 'zh-trad', $user_info->preflang=='zh-trad') ?>><?= $this->lang->line('trad_chinese') ?></option>
            </select>
          <td class="nolb"></td>
          </td>
      </table>
      <p style="height:2px">&nbsp;</p>
      <p><input class="btn btn-primary" type="submit" name="submit" value="<?= $this->lang->line('OK_button') ?>">
            <a class="btn btn-outline-dark" href="<?= site_url('/') ?>"><?= $this->lang->line('cancel_button') ?></a></p>
    </form>

