<h1><?= $this->lang->line('edit_user_profile') ?></h1>

<p>&nbsp;</p>
<p>&nbsp;</p>

<p><a class="btn btn-danger" onclick="genericConfirm('<?= $this->lang->line('delete_profile') ?>',
                            '<?= $this->lang->line('delete_profile_confirm') ?>',
                            '<?= site_url("users/delete_me") ?>');
             return false;"
     href="#"><?= $this->lang->line('delete_profile_button') ?></a></p>
