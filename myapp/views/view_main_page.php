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
    <?php if (isset($logos)): ?>
      <div class="panel panel-primary hidden-xs">
        <div class="panel-body centeralign">
          <a class="navbar-link" href="http://www.ezer.dk" target="_blank"><img alt="" src="<?= site_url('images/ezer_web_trans_lille.png') ?>"></a>
          <p>&nbsp;</p>
          <a class="navbar-link" href="http://3bmoodle.dk" target="_blank"><img alt="" height="43" src="<?= site_url('images/3bm_logo.png') ?>"></a>
        </div>
      </div>
    <?php endif; ?>
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
