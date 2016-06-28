<?php
 
$this->lang->load('menu', $this->language);

$ix = 0;
$head[] = anchor(site_url(), $this->lang->line('home'));
 
$ix = count($head);
$head[] = $this->lang->line('text_and_exercises');
$content[$ix][] = anchor(site_url('text/select_text'), $this->lang->line('display_text'));
$content[$ix][] = anchor(site_url('text/select_quiz'), $this->lang->line('exercises'));
 
if ($this->session->userdata('ol_user')!==null && $this->session->userdata('ol_user')>0) {
    // Logged in
    $ix = count($head);
    $head[] = $this->lang->line('my_data');
    $content[$ix][] = anchor(site_url('statistics'), $this->lang->line('statistics'));
    $content[$ix][] = anchor(site_url('config'), $this->lang->line('font_preferences'));
    $content[$ix][] = anchor(site_url('users/profile'), $this->lang->line('profile'));
    $content[$ix][] = anchor(site_url('userclass/enroll'), $this->lang->line('enroll_in_class'));

    if ($this->config->item('lj_enabled')) {
        $this->load->helper('lj_menu_helper');
        lj_menu_add($head, $content);
    }
    
    if ($this->session->userdata('ol_teacher') || $this->session->userdata('ol_admin')) {
        // Teacher
        $ix = count($head);
        $head[] = $this->lang->line('administration');
        $content[$ix][] = anchor(site_url('users'), $this->lang->line('users'));
        $content[$ix][] = anchor(site_url('classes'), $this->lang->line('classes'));
        $content[$ix][] = anchor(site_url('file_manager'), $this->lang->line('manage_exercises'));
        if ($this->session->userdata('ol_admin'))
            $content[$ix][] = anchor(site_url('urls'), $this->lang->line('manage_gloss_links'));
    }
 
    $ix = count($head);
    $head[] = $this->lang->line('user_access');
    $content[$ix][] = anchor(site_url('login'), $this->lang->line('logout'));
    $content[$ix][] = anchor(site_url('privacy'), $this->lang->line('privacy_policy'));
}
else {
    // Not logged in 
 
    $ix = count($head);
    $head[] = $this->lang->line('user_access');
    $content[$ix][] = anchor(site_url('login'), $this->lang->line('login'));
    $content[$ix][] = anchor(site_url('privacy'), $this->lang->line('privacy_policy'));
}
 
$cols = $ix+1;
 
?>

<nav id="myNavbar" class="navbar navbar-default navbar-static-top">
  <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbarCollapse">
        <span class="sr-only">Toggle navigation</span><!-- For screen reader -->
        <span class="icon-bar"></span><!-- Line on menu toggle button -->
        <span class="icon-bar"></span><!-- Line on menu toggle button -->
        <span class="icon-bar"></span><!-- Line on menu toggle button -->
      </button>
      <a class="navbar-brand" href="<?= site_url('/') ?>">Bible Online Learner</a>
    </div>

    <div class="collapse navbar-collapse" id="navbarCollapse">
      <ul class="nav navbar-nav">
        <?php for ($c=0; $c<$cols; ++$c): ?>
          <?php if (!isset($content[$c])): ?>
            <li><?= $head[$c] ?></li>
          <?php else: ?>
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                 aria-haspopup="true" aria-expanded="false"><?= $head[$c] ?><span class="caret"></span></a>

              <ul class="dropdown-menu">

                <?php for ($r=0; $r<count($content[$c]); ++$r): ?>
                  <li><?= $content[$c][$r] ?></li>
                <?php endfor; ?>

              </ul>
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
              </ul>
          </li>
        <?php endif; ?>
      </ul>
        <div class="navbar-text navbar-right logo hidden-xs hidden-sm hidden-md"><a class="navbar-link" href="http://www.ezer.dk" target="_blank"><img alt="" src="<?= site_url('images/ezer_web_trans_lille.png') ?>"></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a class="navbar-link" href="http://3bmoodle.dk" target="_blank"><img alt="" height="43" src="<?= site_url('images/3bm_logo.png') ?>"></a></div>
    </div>
</nav>

<div class="container-fluid">
  <div class="row">
