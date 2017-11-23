<p>ACCEPT POLICY</p>

<?= form_open('login/accept_policy_yes') ?>
  <input type="hidden" name="acceptance_code" value="<?= $acceptance_code ?>" />
  <input type="hidden" name="user_id" value="<?= $user_id ?>" />
  <input class="btn btn-primary" type="submit" name="submit" value="<?= $this->lang->line('yes') ?>" />
  <p><a class="btn btn-primary" href="<?= site_url('login/accept_policy_no') ?>"><?= $this->lang->line('no') ?></a></p>
</form>


