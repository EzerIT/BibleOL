<? foreach ($all_lex as $lex): ?>
  <p><a href="<?= $lex['url'] ?>"><?= $lex['from_name'] ?> - <?= $lex['to_name'] ?></a></p>
<? endforeach; ?>