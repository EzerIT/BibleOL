    <?php $valerr = validation_errors();
      if (!empty($valerr))
          echo "<div class=\"error\">$valerr</div>\n";
    ?>

    <?= form_open("classes/edit_one_class?classid=$classid") ?>
      <table class="form">
        <tr>
            <td>Class name:</td>
            <td>
              <input type="text" name="classname" value="<?= set_value('classname',$class_info->classname) ?>"> (Required)
            </td>
        </tr>
        <tr>
          <td>Password:</td>
          <td>
            <input type="text" name="password" value="<?= set_value('password',$class_info->password) ?>">
            Leave blank if no password is required.
          </td>
        </tr>
        <tr>
          <td>Enroll before:</td>
            <td><input type="text" name="enrol_before" value="<?= set_value('enrol_before',$class_info->enrol_before) ?>">
            Use format: YYYY-MM-DD. Leave blank if there is no last date.
          </td>
        </tr>
      </table>
      <p><input class="makebutton" type="submit" name="submit" value="OK">
          <a class="makebutton" href="<?= site_url('classes') ?>">Cancel</a></p>
    </form>
