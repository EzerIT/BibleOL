<?php /* Assumption isset($right) implies isset($left) */ ?>

<?php if (isset($right)): ?>

  <div class="col-sm-3">
    <div class="leftblock">
      <?= $left ?>
    </div>
  </div>
   
  <div class="col-sm-6">
    <div class="centerblock">
      <?= $center ?>
    </div>
  </div>
   
  <div class="col-sm-3">
    <div class="rightblock">
      <?= $right; ?>
    </div>
  </div>

<?php elseif (isset($left)): ?>

  <div class="col-sm-3">
    <div class="leftblock">
      <?= $left ?>
    </div>
  </div>
   
  <div class="col-sm-9">
    <div class="centerblock">
      <?= $center ?>
    </div>
  </div>

<?php else: ?>

  <?= $center ?>

<?php endif; ?>
