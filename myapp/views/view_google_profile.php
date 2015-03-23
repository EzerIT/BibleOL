      <table class="form">
        <tr>
          <td colspan="2">You have logged in with Google</td>
        </tr>
        <tr>
          <td>First name:</td>
          <td><?= $user_info->first_name ?></td>
        </tr>
        <tr>
          <td>Last name:</td>
          <td><?= $user_info->last_name ?></td>
        </tr>
        <tr>
          <td>E-mail:</td>
          <td><?= $user_info->email ?></td>
        </tr>
        <tr>
          <td>May see full WIVU database:</td>
          <td>
            <?= $user_info->may_see_wivu ? 'Yes' : 'No' ?>
          </td>
        </tr>
      </table>
    </form>

