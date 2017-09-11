<h1><?= sprintf($this->lang->line('you_are_enrolled'),$classname) ?></h1>

<?php if (!is_null($dir)): ?>
<p><?= sprintf(translate("Go to folder %s."),anchor(build_get('/text/select_quiz',
                                                              array('dir' => $dir)),
                                                    $dir)) ?></p>
<?php endif; ?>
