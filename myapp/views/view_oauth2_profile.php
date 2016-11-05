    <?php $valerr = validation_errors();
      if (!empty($valerr))
          echo "<div class=\"alert alert-danger\">$valerr</div>\n";
    ?>

    <?= form_open("users/profile") ?>
      <table class="form">
        <tr>
          <td colspan="2"><?= $this->lang->line("you_login_{$user_info->oauth2_login}"); ?></td>
        </tr>
        <tr>
          <td><?= $this->lang->line('first_name'); ?></td>
          <td><?= $user_info->first_name ?></td>
        </tr>
        <tr>
          <td><?= $this->lang->line('last_name'); ?></td>
          <td><?= $user_info->last_name ?></td>
        </tr>
        <tr>
          <td><?= $this->lang->line('email'); ?></td>
          <td><?= $user_info->email ?></td>
        </tr>
        <tr>
          <td><?= $this->lang->line('preferred_language') ?></td>
          <td>
            <select name="preflang">
              <option value="none" <?= set_select('preflang', 'none', $user_info->preflang=='none') ?>><?= $this->lang->line('no_language') ?></option>
              <option value="en" <?= set_select('preflang', 'en', $user_info->preflang=='en') ?>><?= $this->lang->line('english') ?></option>
              <option value="da" <?= set_select('preflang', 'da', $user_info->preflang=='da') ?>><?= $this->lang->line('danish') ?></option>
              <option value="pt" <?= set_select('preflang', 'pt', $user_info->preflang=='pt') ?>><?= $this->lang->line('portuguese') ?></option>
              <option value="es" <?= set_select('preflang', 'es', $user_info->preflang=='es') ?>><?= $this->lang->line('spanish') ?></option>
              <option value="zh" <?= set_select('preflang', 'zh', $user_info->preflang=='zh') ?>><?= $this->lang->line('simp_chinese') ?></option>
            </select>
          </td>
      </table>
      <p style="height:2px">&nbsp;</p>
      <p><input class="btn btn-primary" type="submit" name="submit" value="<?= $this->lang->line('OK_button') ?>">
            <a class="btn btn-default" href="<?= site_url('/') ?>"><?= $this->lang->line('cancel_button') ?></a></p>
    </form>

