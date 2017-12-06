<h1><?= sprintf($this->lang->line('you_are_enrolled'),$classname) ?></h1>

<?php if (!is_null($dir)): ?>
  <p><?= sprintf($this->lang->line('go_to_folder'),anchor(build_get('/text/select_quiz',
                                                                    array('dir' => $dir)),
                                                          $dir)) ?></p>
<?php endif; ?>
