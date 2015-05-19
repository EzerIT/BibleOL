<h1><?= $this->lang->line('welcome_new_google_user') ?></h1>

<p><?= sprintf($this->lang->line('your_google_name'),
               $google_user_info->given_name, $google_user_info->family_name, $google_user_info->email) ?></p>

<p><?= $this->lang->line('enjoy') ?></p>

<p><?= $this->lang->line('use_menu') ?></p>
