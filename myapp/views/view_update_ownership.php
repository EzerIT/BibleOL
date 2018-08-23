<h2><?= $this->lang->line('added_ownership') ?></h2>
<?php if (count($added)==0): ?>
    <p><?= $this->lang->line('ownership_none') ?></p>
<?php else: ?>
    <?php sort($added); ?>
    <table class="type1">
      <?php foreach ($added as $a): ?>
          <tr><td class="leftalign"><?= $a ?></td></tr>
      <?php endforeach; ?>
    </table>
<?php endif; ?>

<h2><?= $this->lang->line('deleted_ownership') ?></h2>
<?php if (count($deleted)==0): ?>
    <p><?= $this->lang->line('ownership_none') ?></p>
<?php else: ?>
    <?php sort($deleted); ?>
    <table class="type1">
      <?php foreach ($deleted as $a): ?>
          <tr><td class="leftalign"><?= $a ?></td></tr>
      <?php endforeach; ?>
    </table>
<?php endif; ?>
