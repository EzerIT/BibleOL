<?php
 
$ix = 0;
$head[] = anchor(site_url(),'Home');
 
$ix = count($head);
$head[] = anchor('#','Text and Exercises');
$content[$ix][] = anchor(site_url('text/select_text'),'Display text');
$content[$ix][] = anchor(site_url('text/select_quiz'),'Exercises');
 
if ($this->session->userdata('ol_user')!==false && $this->session->userdata('ol_user')>0) {
    // Logged in
    $ix = count($head);
    $head[] = anchor('#','My Data');
    $content[$ix][] = anchor(site_url('statistics'),'Statistics');
    $content[$ix][] = anchor(site_url('config'),'Font preferences');
    $content[$ix][] = anchor(site_url('users/profile'),'Profile');
    $content[$ix][] = anchor(site_url('userclass/enroll'),'Enroll in class');
    
    if ($this->session->userdata('ol_admin')) {
        // Administrator
        $ix = count($head);
        $head[] = anchor('#','Administration');
        $content[$ix][] = anchor(site_url('users'),'Users');
        $content[$ix][] = anchor(site_url('classes'),'Classes');
        $content[$ix][] = anchor(site_url('file_manager'),'Manage exercises');
    }
 
    $ix = count($head);
    $head[] = anchor('#','User Access');
    $content[$ix][] = anchor(site_url('login'),'Logout');
    $content[$ix][] = anchor(site_url('privacy'),'Privacy policy');
}
else {
    // Not logged in 
 
    $ix = count($head);
    $head[] = anchor('#','User Access');
    $content[$ix][] = anchor(site_url('login'),'Login');
    $content[$ix][] = anchor(site_url('privacy'),'Privacy policy');
}
 
// Calculate number of rows
$rows = 0; 
foreach ($content as $c)
    if (count($c) > $rows)
        $rows = count($c);
 
$cols = $ix+1;
 
?>


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
