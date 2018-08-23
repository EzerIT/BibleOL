<p><?= $this->lang->line("change_through_$authority") ?></p>

<p class="d-none d-md-block">&nbsp;</p>
<p class="d-none d-md-block">&nbsp;</p>

<p><a class="btn btn-danger" onclick="genericConfirm('<?= $this->lang->line('delete_profile'); ?>',
                            '<?= $this->lang->line("delete_oauth2_profile1"); ?><br><br><?= $this->lang->line("delete_{$authority}_profile2"); ?>',
                            '<?= site_url("users/delete_me_{$authority}") ?>');
             return false;"
     href="#"><?= $this->lang->line('delete_profile_button') ?></a></p>

