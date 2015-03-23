    <div class="contentouter ui-corner-all">
      <div class="contentinner ui-corner-all">
        <?php if (isset($left)): ?>
          <div class="leftblock ui-corner-all">
            <?= $left ?>
          </div>
        <?php endif; ?>
        <div class="centerblock">
          <?= $center ?>
        </div>
         <?php if (isset($right)): ?>
          <div class="rightblock ui-corner-all">
            <?= $right; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
