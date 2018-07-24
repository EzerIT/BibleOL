<?php

/* For top level menu item */
function make_anchor1(string $url, string $txt, $th) {
    return anchor(site_url($url), $th->lang->line($txt), 'class="nav-link pt-3 pb-3 pl-3 pr-3"');
}

/* For dropdown menu item */
function make_anchor2(string $url, string $txt, $th) {
    return anchor(site_url($url), $th->lang->line($txt), 'class="dropdown-item"');
}

$this->lang->load('menu', $this->language);

$ix = 0;
$head[] = make_anchor1('', 'home', $this);
 
if (!$this->mod_users->is_logged_in_noaccept()) {
    // The user has not logged in, or has logged in and accepted policy
    $ix = count($head);
    $head[] = $this->lang->line('text_and_exercises');
    $content[$ix][] = make_anchor2('text/select_text', 'display_text', $this);
    $content[$ix][] = make_anchor2('text/select_quiz', 'exercises', $this);
}
 
if ($this->mod_users->is_logged_in()) {
    // Logged in
    $ix = count($head);
    $head[] = $this->lang->line('my_data');
    //$content[$ix][] = make_anchor2('statistics', 'statistics', $this);
    $content[$ix][] = make_anchor2('config', 'font_preferences', $this);
    $content[$ix][] = make_anchor2('users/profile', 'profile', $this);
    $content[$ix][] = make_anchor2('userclass/enroll', 'enroll_in_class', $this);
    $content[$ix][] = make_anchor2('statistics/student_time', 'my_progress', $this);

    if ($this->mod_users->is_teacher())
        $content[$ix][] = make_anchor2('statistics/teacher_progress', 'students_progress', $this);

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
            $content[$ix][] = make_anchor2('users', 'users', $this);
            $content[$ix][] = make_anchor2('classes', 'classes', $this);
            $content[$ix][] = make_anchor2('file_manager', 'manage_exercises', $this);
        }
        if ($this->mod_users->is_translator()) {
            $content[$ix][] = make_anchor2('translate/translate_if', 'translate_interface', $this);
            $content[$ix][] = make_anchor2('translate/translate_grammar', 'translate_grammar', $this);
            $content[$ix][] = make_anchor2('translate/translate_lex', 'translate_lexicon', $this);
            $content[$ix][] = make_anchor2('translate/select_download_lex', 'download_lexicon', $this);
        }
        if ($this->mod_users->is_admin())
            $content[$ix][] = make_anchor2('urls', 'manage_gloss_links', $this);
    }
 
    $ix = count($head);
    $head[] = $this->lang->line('user_access');
    $content[$ix][] = make_anchor2('login', 'logout', $this);
    $content[$ix][] = make_anchor2('privacy', 'privacy_policy', $this);
}
elseif ($this->mod_users->is_logged_in_noaccept()) {
    // The user did not accept policy.
    $ix = count($head);
    $head[] = $this->lang->line('user_access');
    $content[$ix][] = make_anchor2('login', 'logout', $this);
    $content[$ix][] = make_anchor2('privacy', 'privacy_policy', $this);
}
else {
    // Not logged in 
 
    $ix = count($head);
    $head[] = $this->lang->line('user_access');
    $content[$ix][] = make_anchor2('login', 'login', $this);
    $content[$ix][] = make_anchor2('privacy', 'privacy_policy', $this);
}
 
$cols = $ix+1;
 
?>
  <nav class="navbar navbar-expand-md navbar-light bg-light pt-0 pb-0">
      <div class="mx-lg-auto divnavbar">
      <a class="navbar-brand" href="<?= site_url('/') ?>">Bible Online Learner</a>
      <button class="navbar-toggler mt-1 mb-1" type="button" data-toggle="collapse" data-target="#mainMenu" aria-controls="mainMenu" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="mainMenu">
        <ul class="navbar-nav mr-auto">

        <?php for ($c=0; $c<$cols; ++$c): ?>
          <?php if (!isset($content[$c])): ?>
            <li class="nav-item"><?= $head[$c] ?></li>
          <?php else: ?>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle pt-3 pb-3 pl-3 pr-3" href="#" id="navbarDropdownX" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?= $head[$c] ?></a>

              <div class="dropdown-menu" aria-labelledby="navbarDropdownX">

                <?php for ($r=0; $r<count($content[$c]); ++$r): ?>
                  <?= $content[$c][$r] ?>
                <?php endfor; ?>

              </div>
            </li>
          <?php endif; ?>
        <?php endfor; ?>

        <?php if ($langselect): ?>
          <li class="dropdown">
             <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                aria-haspopup="true" aria-expanded="false"><img src="<?= site_url('images/icon20x24px-exported-transparent.png') ?>" alt=""> <?= $this->lang->line('language') ?><span class="caret"></span></a>
              <ul class="dropdown-menu">
               <li><a href="<?= site_url('/lang?lang=da') ?>">Dansk</a></li>
               <li><a href="<?= site_url('/lang?lang=en') ?>">English</a></li>
               <li><a href="<?= site_url('/lang?lang=de') ?>">Deutsch</a></li>
               <li><a href="<?= site_url('/lang?lang=nl') ?>">Nederlands</a></li>
               <li><a href="<?= site_url('/lang?lang=pt') ?>">Português</a></li>
               <li><a href="<?= site_url('/lang?lang=es') ?>">Español</a></li>
               <li><a href="<?= site_url('/lang?lang=zh-simp') ?>">中文（简体）</a></li>
               <li><a href="<?= site_url('/lang?lang=zh-trad') ?>">中文（繁體）</a></li>
              </ul>
          </li>
        <?php endif; ?>
      </ul>
    </div>
</nav>

<div class="container-fluid">
  <div class="row">
