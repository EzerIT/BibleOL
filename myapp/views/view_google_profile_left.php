<h1><?= $this->lang->line('this_your_profile') ?></h1>

<p><?= $this->lang->line('change_through_google') ?></p>

<p>&nbsp;</p>
<p>&nbsp;</p>

<p><a class="makebutton" onclick="genericConfirm('<?= $this->lang->line('delete_profile'); ?>',
                            '<?= $this->lang->line('delete_google_profile1'); ?><br><br><?= $this->lang->line('delete_google_profile2'); ?>',
                            '<?= site_url("users/delete_me_google") ?>');
             return false;"
     href="#"><?= $this->lang->line('delete_profile_button') ?></a></p>

