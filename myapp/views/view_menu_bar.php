<?php

/* For top level menu item */
function make_anchor1(string $url, string $txt) {
    return anchor(site_url($url), get_instance()->lang->line($txt), 'class="nav-link pt-3 pb-3 pl-3 pr-3"');
}

/* For dropdown menu item */
function make_anchor2(string $url, string $txt) {
    return anchor(site_url($url), get_instance()->lang->line($txt), 'class="dropdown-item"');
}

$this->lang->load('menu', $this->language);

$ix = 0;
$head[] = make_anchor1('', 'home');

if (!$this->mod_users->is_logged_in_noaccept()) {
    // The user has not logged in, or has logged in and accepted policy
    $ix = count($head);
    $head[] = $this->lang->line('text_and_exercises');
    $content[$ix][] = make_anchor2('text/select_text', 'display_text');
    $content[$ix][] = make_anchor2('text/select_quiz', 'exercises');
}

if ($this->mod_users->is_logged_in()) {
    // Logged in
    $content[$ix][] = make_anchor2('exams/active_exams', 'exams');
    
    $ix = count($head);
    $head[] = $this->lang->line('my_data');
    //$content[$ix][] = make_anchor2('statistics', 'statistics');
    $content[$ix][] = make_anchor2('config', 'font_preferences');
    $content[$ix][] = make_anchor2('users/profile', 'profile');
    $content[$ix][] = make_anchor2('userclass/enroll', 'enroll_in_class');
    $content[$ix][] = make_anchor2('statistics/student_time', 'my_progress');

    if ($this->mod_users->is_teacher())
        $content[$ix][] = make_anchor2('statistics/teacher_progress', 'students_progress');

    if ($this->config->item('lj_enabled')) {
        $this->load->helper('lj_menu_helper');
        lj_menu_add($head, $content);
    }

    if ($this->mod_users->is_teacher() || $this->mod_users->is_translator()) {
        // Teacher or translator
        $ix = count($head);
        $head[] = $this->lang->line('administration');
        if ($this->mod_users->is_teacher()) {
            // Teacher
            $content[$ix][] = make_anchor2('users', 'users');
            $content[$ix][] = make_anchor2('classes', 'classes');
            $content[$ix][] = make_anchor2('file_manager', 'manage_exercises');
            $content[$ix][] = make_anchor2('exams', 'manage_exams');
        }
        if ($this->mod_users->is_translator()) {
            $content[$ix][] = make_anchor2('translate/translate_if', 'translate_interface');
            $content[$ix][] = make_anchor2('translate/translate_grammar', 'translate_grammar');
            $content[$ix][] = make_anchor2('translate/translate_lex', 'translate_lexicon');
            $content[$ix][] = make_anchor2('translate/select_download_lex', 'download_lexicon');
        }
        if ($this->mod_users->is_admin())
            $content[$ix][] = make_anchor2('urls', 'manage_gloss_links');
    }

    $ix = count($head);
    $head[] = $this->lang->line('user_access');
    $content[$ix][] = make_anchor2('login', 'logout');
    $content[$ix][] = make_anchor2('privacy', 'privacy_policy');
}
elseif ($this->mod_users->is_logged_in_noaccept()) {
    // The user did not accept policy.
    $ix = count($head);
    $head[] = $this->lang->line('user_access');
    $content[$ix][] = make_anchor2('login', 'logout');
    $content[$ix][] = make_anchor2('privacy', 'privacy_policy');
}
else {
    // Not logged in

    $ix = count($head);
    $head[] = $this->lang->line('user_access');
    $content[$ix][] = make_anchor2('login', 'login');
    $content[$ix][] = make_anchor2('privacy', 'privacy_policy');
}

$cols = $ix+1;

?>
<nav class="navbar navbar-expand-lg navbar-light bg-light pt-0 pb-0 mb-3">
  <div class="divnavbar">
    <a class="navbar-brand d-block d-lg-none" href="<?= site_url('/') ?>">Bible Online Learner</a>
    <button class="navbar-toggler mt-1 mb-1" type="button" data-toggle="collapse" data-target="#mainMenu" aria-controls="mainMenu" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="mainMenu">
        <span class="navbar-text pr-3"><a class="navbar-brand d-none d-lg-inline" href="<?= site_url('/') ?>">Bible Online Learner</a></span>
      <ul class="navbar-nav">
        <?php for ($c=0; $c<$cols; ++$c): ?>
          <?php if (!isset($content[$c])): ?>
            <li class="nav-item"><?= $head[$c] ?></li>
          <?php else: ?>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle pt-3 pb-3 pl-3 pr-3" href="#" id="navbarDropdown<?= $c ?>" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?= $head[$c] ?></a>

              <div class="dropdown-menu" aria-labelledby="navbarDropdown<?= $c ?>">

                <?php for ($r=0; $r<count($content[$c]); ++$r): ?>
                  <?= $content[$c][$r] ?>
                <?php endfor; ?>

              </div>
            </li>
          <?php endif; ?>
        <?php endfor; ?>

        <?php if ($langselect): ?>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle pt-3 pb-3 pl-3 pr-3" href="#" id="navbarDropdownLang" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><img src="<?= site_url('images/icon20x24px-exported-transparent.png') ?>" alt=""> <?= $this->lang->line('language') ?></a>
            <div class="dropdown-menu" aria-labelledby="navbarDropdownLang">
              <a class="dropdown-item" href="<?= site_url('/lang?lang=da') ?>">Dansk</a>
              <a class="dropdown-item" href="<?= site_url('/lang?lang=en') ?>">English</a>
              <a class="dropdown-item" href="<?= site_url('/lang?lang=de') ?>">Deutsch</a>
              <a class="dropdown-item" href="<?= site_url('/lang?lang=fr') ?>">Français</a>
              <a class="dropdown-item" href="<?= site_url('/lang?lang=nl') ?>">Nederlands</a>
              <a class="dropdown-item" href="<?= site_url('/lang?lang=pt') ?>">Português</a>
              <a class="dropdown-item" href="<?= site_url('/lang?lang=es') ?>">Español</a>
              <a class="dropdown-item" href="<?= site_url('/lang?lang=zh-simp') ?>">中文（简体）</a>
              <a class="dropdown-item" href="<?= site_url('/lang?lang=zh-trad') ?>">中文（繁體）</a>
            </div>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<div class="container-fluid">
  <div class="row">
