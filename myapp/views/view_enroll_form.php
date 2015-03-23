<h1>Enter Password for &ldquo;<?= $classname ?>&rdquo;</h1>

<?php $valerr = validation_errors('<p class="error">','</p>'); ?>


<?= form_open("userclass/enroll_in?classid=$classid") ?>

<?= !empty($valerr) ? $valerr : ''?>


<table class="form">
  <tr>
    <td>Class password:</td>
    <td>
      <input type="password" name="password" value="">
    </td>
  </tr>
</table>
<p><input class="makebutton" type="submit" name="submit" value="OK">
   <a class="makebutton" href="<?= site_url('/userclass/enroll') ?>">Cancel</a></p>
</p>
</form>

