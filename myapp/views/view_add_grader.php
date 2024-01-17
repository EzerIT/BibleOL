<?php $valerr = validation_errors();
if (!empty($valerr))
    echo "<div class=\"alert alert-danger\">$valerr</div>\n";
?>

<?= form_open("classes/add_one_grader?classid=$classid") ?>
<table class="form">
    <tr>
        <td>
            <?= "Grader Username: " ?>
        </td>
        <td class="norb">
            <input type="text" name="grader_username">
        </td>
        <td class="nolb">
            <?= $this->lang->line('field_required') ?>
        </td>
    </tr>
</table>
<p style="height:2px">&nbsp;</p>
<p><input class="btn btn-primary" type="submit" name="submit" value="<?= $this->lang->line('OK_button') ?>">
    <a class="btn btn-outline-dark" href="<?= site_url('classes') ?>">
        <?= $this->lang->line('cancel_button') ?>
    </a>
</p>
</form>