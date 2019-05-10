<?php foreach ($all_lex as $lex): ?>
  <p><a href="<?= $lex['url'] ?>"><?= $lex['from_name'] ?> - <?= $lex['to_name'] ?>
    <?= !empty($lex['variant']) ? sprintf($this->lang->line('lex_variant'), $lex['variant']) : '' ?>
    </a></p>
<?php endforeach; ?>


