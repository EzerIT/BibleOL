<?php foreach ($langs as $abb => $name): ?>
    <p><?= anchor(site_url("site/site_text?lang=$abb"), $name) ?></p>
<?php endforeach; ?>
