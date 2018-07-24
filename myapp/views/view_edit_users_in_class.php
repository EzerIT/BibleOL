<h1><?= sprintf($this->lang->line('users_in_class'), $classname) ?></h1>

<p class="d-lg-none "><?= $this->lang->line('swipe') ?></p>

<?php $count= count($allusers); ?>

<?= form_open("userclass/users_in_class?classid=$classid") ?>

<div class="row">
  <div class="col-sm-6" style="max-width:300px">
    <div class="table-responsive">
      <table class="type2 table table-striped">
        <tr><th><?= $this->lang->line('user') ?></th><th class="text-center"><?= $this->lang->line('in_this_class') ?></th></tr>

        <?php for ($i=0; $i<$count/2; ++$i): ?>
          <?php $user = $allusers[$i]; ?>
          <tr>
            <td><?= make_full_name($user) ?></td>
            <td class="text-center"><input class="narrow" type="checkbox" name="inclass[]" value="<?= $user->id ?>" <?= set_checkbox('inclass[]', $user->id, in_array($user->id, $old_users)) ?>></td>
          </tr>
        <?php endfor; ?>
      </table>
    </div>
  </div>

  <div class="col-sm-6" style="max-width:300px;" >
    <div class="table-responsive">
      <table class="type2 table table-striped">
        <tr><th><?= $this->lang->line('user') ?></th><th class="text-center"><?= $this->lang->line('in_this_class') ?></th></tr>

        <?php for (; $i<$count; ++$i): ?>
          <?php $user = $allusers[$i]; ?>
          <tr>
            <td><?= make_full_name($user) ?></td>
            <td class="text-center"><input class="narrow" type="checkbox" name="inclass[]" value="<?= $user->id ?>" <?= set_checkbox('inclass[]', $user->id, in_array($user->id, $old_users)) ?>></td>
          </tr>
        <?php endfor; ?>
      </table>
    </div>
  </div>
</div>

<p style="height:2px">&nbsp;</p>
<p><input class="btn btn-primary" type="submit" name="submit" value="<?= $this->lang->line('OK_button') ?>">
   <a class="btn btn-secondary" href="<?= site_url('classes') ?>"><?= $this->lang->line('cancel_button') ?></a>
</p>
</form>
