<?php if ($this->config->item('url_variant')): ?>
  <h2><?= $this->lang->line('download_lex_main') ?></h2>
<?php endif; ?>

<?php foreach ($all_lex as $lex): ?>
  <p><a href="<?= $lex['url'] ?>"><?= $lex['from_name'] ?> - <?= $lex['to_name'] ?></a></p>
<?php endforeach; ?>

<?php if ($this->config->item('url_variant')): ?>
  <h2><?= sprintf($this->lang->line('download_lex_site'),$this->config->item('url_variant')) ?></h2>

  <?php foreach ($all_lex as $lex): ?>
    <p><a href="<?= $lex['url'] ?>/<?= $this->config->item('url_variant') ?>"><?= $lex['from_name'] ?> - <?= $lex['to_name'] ?></a></p>
  <?php endforeach; ?>

<?php endif; ?>


