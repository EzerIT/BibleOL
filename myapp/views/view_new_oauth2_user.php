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

<div class="panel panel-default">
  <div class="panel-heading"><?= $this->lang->line('do_you_accept') ?></div>
  <div class="panel-body">
    <?= form_open("login/accept_policy_yes") ?>
      <input type="hidden" name="acceptance_code" value="<?= $acceptance_code ?>" />
      <input type="hidden" name="user_id" value="<?= $user_id ?>" />
      <input type="hidden" name="policy_lang" value="<?= $policy_lang ?>" />
      <input class="btn btn-primary" type="submit" name="submit" value="<?= $this->lang->line('yes') ?>" />
      <a class="btn btn-primary" href="<?= build_get('/oauth2/accept_policy_no',
                                                     array('user_id' => $user_id,
                                                           'acceptance_code' => $acceptance_code,
                                                           'authority' => $authority))
                                       ?>"><?= $this->lang->line('no') ?></a>
    </form>
  </div>
</div>
