<?php /* Assumption isset($right_title) implies isset($left_title) */ ?>

<?php if (isset($right_title)): ?>

  <div class="col-sm-3">
    <div class="panel panel-primary">
      <div class="panel-heading">
        <h3 class="panel-title"><?= $left_title ?></h3>
      </div>
      <?php if (isset($left)): ?>
        <div class="panel-body"><?= $left ?></div>
      <?php endif; ?>  
  </div>
  </div>
   
  <div class="col-sm-6">
    <div class="centerblock">
      <?= $center ?>
    </div>
  </div>
   
  <div class="col-sm-3">
    <div class="panel panel-primary">
      <div class="panel-heading">
        <h3 class="panel-title"><?= $right_title ?></h3>
      </div>
      <?php if (isset($right)): ?>
        <div class="panel-body"><?= $right ?></div>
      <?php endif; ?>  
    </div>
  </div>

<?php elseif (isset($left_title)): ?>

  <div class="col-sm-3">
    <div class="panel panel-primary">
      <div class="panel-heading">
        <h3 class="panel-title"><?= $left_title ?></h3>
      </div>
      <?php if (isset($left)): ?>
        <div class="panel-body"><?= $left ?></div>
      <?php endif; ?>  
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
