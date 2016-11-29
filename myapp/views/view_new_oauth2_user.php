<h1><?= $this->lang->line("welcome_new_{$authority}_user") ?></h1>

<?php if (empty($user_info->email)): ?>
    <p><?= sprintf($this->lang->line("your_{$authority}_name_no_email"),
                   make_full_name($user_info)) ?></p>
<?php else: ?>
    <p><?= sprintf($this->lang->line("your_{$authority}_name"),
                   make_full_name($user_info), $user_info->email) ?></p>
<?php endif; ?>

<p><?= $this->lang->line('enjoy') ?></p>

<p><?= $this->lang->line('use_menu') ?></p>
