<h1><?= $this->lang->line("welcome_new_{$authority}_user") ?></h1>

<?php if (empty($user_info->email)): ?>
    <p><?= sprintf($this->lang->line("your_{$authority}_name_no_email"),
                   $user_info->first_name, $user_info->last_name) ?></p>
<?php else: ?>
    <p><?= sprintf($this->lang->line("your_{$authority}_name"),
                   $user_info->first_name, $user_info->last_name, $user_info->email) ?></p>
<?php endif; ?>

<p><?= $this->lang->line('enjoy') ?></p>

<p><?= $this->lang->line('use_menu') ?></p>
