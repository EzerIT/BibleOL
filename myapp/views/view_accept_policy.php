<?php
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

<div class="panel panel-default">
  <div class="panel-heading"><?= $this->lang->line('do_you_accept') ?></div>
  <div class="panel-body">
    <?= form_open("login/accept_policy_yes") ?>
      <input type="hidden" name="acceptance_code" value="<?= $acceptance_code ?>" />
      <input type="hidden" name="policy_lang" value="<?= $policy_lang ?>" />
      <input class="btn btn-primary" type="submit" name="submit" value="<?= $this->lang->line('yes') ?>" />
      <?= anchor('login/accept_policy_no', $this->lang->line('no'), array('class' => 'btn btn-primary')) ?>
    </form>
  </div>
</div>




