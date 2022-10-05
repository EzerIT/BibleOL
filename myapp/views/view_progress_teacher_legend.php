<div id="legend">
  <div id="mykey"></div>
  <div id="allkey">
    <input type="checkbox" checked name="selectall" value="">&nbsp;<?= $this->lang->line('all') ?>
  </div>
            
  <?php if (!empty($nongraded)): ?>
    <p><?= $this->lang->line('students_marked_star') ?></p>
  <?php endif; ?>
</div>
