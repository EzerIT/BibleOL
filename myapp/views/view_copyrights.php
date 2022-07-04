<?php foreach ($db_books as $dbb): ?>
    <div class="copyright copyright_<?= $dbb['name'] ?>">
        <h2><?= $this->lang->line('corpus_database_colon') . ' ' . $dbb['databaseName'] ?></h2>
        <?= $dbb['loc_copyright'] ?>
    </div>
<?php endforeach; ?>
   
    
