<?php
 
$this->lang->load('menu', $this->language);

$ix = 0;
$head[] = anchor(site_url(), $this->lang->line('home'));
 
$ix = count($head);
$head[] = anchor('#', $this->lang->line('text_and_exercises'));
$content[$ix][] = anchor(site_url('text/select_text'), $this->lang->line('display_text'));
$content[$ix][] = anchor(site_url('text/select_quiz'), $this->lang->line('exercises'));
 
if ($this->session->userdata('ol_user')!==false && $this->session->userdata('ol_user')>0) {
    // Logged in
    $ix = count($head);
    $head[] = anchor('#', $this->lang->line('my_data'));
    $content[$ix][] = anchor(site_url('statistics'), $this->lang->line('statistics'));
    $content[$ix][] = anchor(site_url('config'), $this->lang->line('font_preferences'));
    $content[$ix][] = anchor(site_url('users/profile'), $this->lang->line('profile'));
    $content[$ix][] = anchor(site_url('userclass/enroll'), $this->lang->line('enroll_in_class'));
    
    if ($this->session->userdata('ol_teacher')) {
        // Teacher
        $ix = count($head);
        $head[] = anchor('#', $this->lang->line('administration'));
        $content[$ix][] = anchor(site_url('users'), $this->lang->line('users'));
        $content[$ix][] = anchor(site_url('classes'), $this->lang->line('classes'));
        $content[$ix][] = anchor(site_url('file_manager'), $this->lang->line('manage_exercises'));
    }
 
    $ix = count($head);
    $head[] = anchor('#', $this->lang->line('user_access'));
    $content[$ix][] = anchor(site_url('login'), $this->lang->line('logout'));
    $content[$ix][] = anchor(site_url('privacy'), $this->lang->line('privacy_policy'));
}
else {
    // Not logged in 
 
    $ix = count($head);
    $head[] = anchor('#', $this->lang->line('user_access'));
    $content[$ix][] = anchor(site_url('login'), $this->lang->line('login'));
    $content[$ix][] = anchor(site_url('privacy'), $this->lang->line('privacy_policy'));
}
 
// Calculate number of rows
$rows = 0; 
foreach ($content as $c)
    if (count($c) > $rows)
        $rows = count($c);
 
$cols = $ix+1;
 
?>

<?php if ($langselect): ?>
    <div style="float:right"><br><a href="<?= site_url('/lang?lang=da') ?>">Dansk</a> <a href="<?= site_url('/lang?lang=en') ?>">English</a></div>
<?php endif; ?>

<ul class="dropdown">
<?php
    for ($c=0; $c<$cols; ++$c) {
        echo "<li>$head[$c]";

        if (isset($content[$c])) {
            echo '<ul class="sub_menu">';
            for ($r=0; $r<count($content[$c]); ++$r)
                echo "<li>{$content[$c][$r]}</li>";
            echo '</ul>';
        }
        echo '</li>';
    }
?>
</ul>

