<h1><?= $this->lang->line("welcome_new_{$authority}_user") ?></h1>

<?php if (empty($user_info->email)): ?>
    <p><?= sprintf($this->lang->line("your_{$authority}_name_no_email"),
                   make_full_name($user_info)) ?></p>
<?php else: ?>
    <p><?= sprintf($this->lang->line("your_{$authority}_name"),
                   make_full_name($user_info), $user_info->email) ?></p>
<?php endif; ?>

<p><?= $this->lang->line('enjoy') ?></p>

<p><?= $this->lang->line('first_you_must_accept_policy') ?></p>

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

<div class="card mb-3">
  <h5 class="card-header bg-light text-dark"><?= $this->lang->line('do_you_accept') ?></h5>

  <div class="card-body">
    <?= form_open("login/accept_policy_yes") ?>
      <input type="hidden" name="acceptance_code" value="<?= $acceptance_code ?>" />
      <input type="hidden" name="policy_lang" value="<?= $policy_lang ?>" />
      <input class="btn btn-primary" type="submit" name="submit" value="<?= $this->lang->line('yes') ?>" />
      <?= anchor(build_get('oauth2/accept_policy_no', array('acceptance_code' => $acceptance_code)),
                 $this->lang->line('no'),
                 array('class' => 'btn btn-primary')) ?>
    </form>
  </div>
</div>
