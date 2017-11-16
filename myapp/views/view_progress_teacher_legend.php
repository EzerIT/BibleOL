<div id="legend">
  <div id="mykey"></div>
  <div id="allkey">
    <input type="checkbox" style="margin-left:20px" checked name="selectall" value=""><?= $this->lang->line('all') ?>
  </div>
            
  <?php if (!empty($nongraded)): ?>
    <p><?= $this->lang->line('students_marked_star') ?></p>
  <?php endif; ?>
</div>
