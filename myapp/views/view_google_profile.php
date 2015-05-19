      <table class="form">
        <tr>
          <td colspan="2"><?= $this->lang->line('you_login_google'); ?></td>
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
      </table>
    </form>

