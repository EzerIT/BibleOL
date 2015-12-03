    <?php $valerr = validation_errors();
      if (!empty($valerr))
          echo "<div class=\"alert alert-danger\">$valerr</div>\n";
    ?>

    <?= form_open("classes/edit_one_class?classid=$classid") ?>
      <table class="form">
        <tr>
            <td><?= $this->lang->line('class_name') ?></td>
            <td class="norb"><input type="text" name="classname" value="<?= set_value('classname',$class_info->classname) ?>"></td>
            <td class="nolb"><?= $this->lang->line('field_required') ?></td>
        </tr>
        <tr>
          <td><?= $this->lang->line('class_pw') ?></td>
          <td class="norb"><input type="text" name="password" value="<?= set_value('password',$class_info->password) ?>"></td>
          <td class="nolb"><?= $this->lang->line('leave_blank_if_no_pw') ?></td>
        </tr>
        <tr>
          <td><?= $this->lang->line('enroll_before') ?></td>
          <td class="norb"><input type="text" name="enrol_before" value="<?= set_value('enrol_before',$class_info->enrol_before) ?>"></td>
          <td class="nolb"><?= $this->lang->line('date_format_or_blank') ?></td>
        </tr>
      </table>
      <p style="height:2px">&nbsp;</p>
      <p><input class="btn btn-primary" type="submit" name="submit" value="<?= $this->lang->line('OK_button') ?>">
          <a class="btn btn-default" href="<?= site_url('classes') ?>"><?= $this->lang->line('cancel_button') ?></a></p>
    </form>
