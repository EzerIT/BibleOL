<?php foreach ($all_lex as $lex): ?>
  <p><a href="<?= $lex['url'] ?>"><?= $lex['from_name'] ?> - <?= $lex['to_name'] ?>
    <?= !empty($lex['variant']) ? sprintf('(%s)' /*$this->lang->line('lex_variant')*/,$lex['variant']) : '' ?> <!-- TODO Localize -->
    </a></p>
<?php endforeach; ?>


