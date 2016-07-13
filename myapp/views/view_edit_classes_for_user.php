<h1><?= sprintf($this->lang->line('classes_for_user'), $user_name) ?></h1>

<?= form_open("userclass/classes_for_user?userid=$userid&$extras") ?>

<div class="form-group" style="max-width:300px">
  <table class="type2 table table-striped">
    <tr><th><?= $this->lang->line('class') ?></th><th class="text-center"><?= $this->lang->line('in_this_class') ?></th></tr>
   
    <?php foreach ($allclasses as $cl): ?>
       <?php if (in_array($cl->clid, $owned_classes)): ?>
        <tr>
          <td><?= $cl->classname ?></td>
          <td class="text-center"><input class="narrow" type="checkbox" name="foruser[]" value="<?= $cl->clid ?>" <?= set_checkbox('foruser[]', $cl->clid, in_array($cl->clid, $old_classes)) ?>></td>
        </tr>
      <?php endif; ?>
    <?php endforeach; ?>
  </table>
</div>

<p style="height:2px">&nbsp;</p>
<p><input class="btn btn-primary" type="submit" name="submit" value="<?= $this->lang->line('OK_button') ?>">
   <a class="btn btn-default" href="<?= site_url("users?$extras") ?>"><?= $this->lang->line('cancel_button') ?></a>
</p>
</form>
