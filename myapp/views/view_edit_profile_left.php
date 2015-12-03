<p><?= $this->lang->line('click_to_delete_profile') ?></p>

<p><a class="btn btn-danger" onclick="genericConfirm('<?= $this->lang->line('delete_profile') ?>',
                            '<?= $this->lang->line('delete_profile_confirm') ?>',
                            '<?= site_url("users/delete_me") ?>');
             return false;"
     href="#"><?= $this->lang->line('delete_profile_button') ?></a></p>
