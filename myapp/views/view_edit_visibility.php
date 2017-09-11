
<h1><?= sprintf($this->lang->line('visibility_folder'), $dir) ?></h1>

<?= form_open(build_get('file_manager/edit_visibility',array('dir' => $dir))) ?>
     <p><?= translate('Check here if folder is visible to everybody:') ?> <input id="selectall" class="narrow" type="checkbox" name="inclass[]" value="0" <?= set_checkbox('inclass[]', 0, in_array(0, $old_classes)) ?>><br><?= translate('(If not checked, only the classes indicated in the table below can see the exercises in this folder.)') ?></p>
     <p style="margin-top:30px"><?= translate('In this table, indicate which classes use this folder:') ?></p>
<table class="form">

     <tr><th><?= $this->lang->line('class') ?></th><th><?= translate('Class uses folder') ?></th></tr>


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
