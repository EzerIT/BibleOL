
<h1><?= sprintf($this->lang->line('visibility_folder'), $dir) ?></h1>

<?= form_open(build_get('file_manager/edit_visibility',array('dir' => $dir))) ?>
     <p><?= $this->lang->line('visible_all') ?> <input id="selectall" class="narrow" type="checkbox" name="inclass[]" value="0" <?= set_checkbox('inclass[]', 0, in_array(0, $old_classes)) ?>><br><?= $this->lang->line('visible_if_not_checked') ?></p>
     <p style="margin-top:30px"><?= $this->lang->line('indicate_classes') ?></p>
<table class="form">

     <tr><th><?= $this->lang->line('class') ?></th><th><?= $this->lang->line('class_uses_folder') ?></th></tr>


  <?php foreach ($allclasses as $cl): ?>
    <tr class="dirclasslist">
      <td><?= $cl->classname ?></td>
      <td class="centeralign"><input type="checkbox" class="narrow" name="inclass[]" value="<?= $cl->clid ?>" <?= set_checkbox('inclass[]', $cl->clid, in_array($cl->clid, $old_classes)) ?>></td>
    </tr>
  <?php endforeach; ?>
</table>

<p style="height:2px">&nbsp;</p>

<p><input class="btn btn-primary" type="submit" name="submit" value="<?= $this->lang->line('OK_button') ?>">
     <a class="btn btn-default" href="<?= site_url(build_get('file_manager',array('dir' => $dir))) ?>"><?= $this->lang->line('cancel_button') ?></a>
</p>
</form>
