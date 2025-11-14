<?php /* Assumption isset($right_title) implies isset($left_title) */

/* Optionally shows a panel title and text */
function show_panel(&$title, &$text, $extraclass='') {
    if (!isset($title))
        return;

    echo "  <div class=\"card mb-3\">\n";
    echo "    <h5 class=\"card-header bg-primary text-light\">$title</h5>\n";
    echo "    <div class=\"card-body $extraclass\">$text</div>\n";
    echo "  </div>\n";
}

/* Shows a panel title and optionally a text */
function show_panel2(&$title, &$text) {
    echo "<div class=\"card mb-3\">\n";
    echo "  <h5 class=\"card-header bg-primary text-light\">$title</h5>\n";
    if (isset($text))
        echo "  <div class=\"card-body\">$text</div>\n";
    echo "</div>\n";
}

/* Shows logos */
function logos() {
?>
    <div class="row logointro">
        <h2>Partners behind BibleOL</h2>
    </div>
    <div class="row logopanel">
        <div class="col-md-4">
            <a class="navbar-link" href="https://ministry.americanbible.org/nida-institute" target="_blank" ><img class="logo" src="images/logos/nida_logo.jpg" style="height:130px; text-align: center;"></a>
        </div>
        <div class="col-md-4">
        <a class="navbar-link" href="https://www.judith.dk/dadel" target="_blank"><img class="logo" src="images/logos/TheOnlineProfessor_screenB_coal.png" style="height:130px;"></a>
        </div>
        <div class="col-md-4">
            <a class="navbar-link" href="http://pthu.nl" target="_blank"><img class="logo"  src="images/logos/pthu.png" style="height:130px;"></a>
        </div>
    </div>
    <div class="row logopanel">
        <div class="col-md-4">
            <a class="navbar-link" href="https://www.andrews.edu" target="_blank"><img class="logo"  src="images/logos/au_signature_vertical_blue_tag.png" style="height:130px;"></a>
        </div>
        <div class="col-md-4">
            <a class="navbar-link" href="http://vu.nl" target="_blank"><img class="logo" src="images/logos/vu.png" style="height:130px;"></a>
        </div>
    </div>
<?php 
}
?>


<?php if (isset($landingpage)): ?>
    <div class="row" id="landing-row">
        <div class="col-md-8" id="landingpanel">
            <img class="graphic-element" src="<?= site_url("/images/dadel/DaDEL_ID_graphic_element_RGB.png") ?>" style="height:82%">
            <div id="landingtext">
                <?= $center ?>
            </div>
        </div>
        <div class="col-md-4" id="loginpanel">

            <?php if (!isset($logged_in_name)): ?>
                <h1><?= $this->lang->line('press_login_button') ?></h1>
                <p style="margin-left: 1em"><a class="btn btn-primary" href="<?= site_url("/login") ?>"><?= $this->lang->line('login') ?></a></p>
                <h1><?= $this->lang->line('no_login') ?></h1>
                <p style="margin-left: 1em;margin-bottom: 0"><a class="btn btn-outline-dark" href="<?= site_url("/users/sign_up") ?>"><?= $this->lang->line('sign_up') ?></a></p>
            <?php else: ?>
                <h1><?= sprintf($this->lang->line('welcome2'),$logged_in_name) ?></h1>
            <?php endif; ?>

            <p style="margin-left: 1em;margin-bottom: 0"><a class="btn btn-outline-dark" href="<?= site_url("/text/select_text") ?>"><?= $this->lang->line('start_text_reading') ?></a></p>
            <p style="margin-left: 1em;margin-bottom: 0"><a class="btn btn-outline-dark" href="<?= site_url("/text/select_quiz") ?>"><?= $this->lang->line('try_an_exercise') ?></a></p>

            <?php if (isset($logged_in_name)): ?>
                <p style="margin-left: 1em"><a class="btn btn-primary" href="<?= site_url("/login") ?>"><?= $this->lang->line('logout') ?></a></p>
            <?php endif; ?>

        </div>
    </div>
<?php else: ?>
    <div class="row">
<?php endif; ?>


<?php
      if (isset($left_title)) {
          echo "<div class=\"col-md-3 sidepanel\" id=\"leftpanel\">";
          show_panel2($left_title, $left);

          show_panel($extraleft_title, $extraleft, 'student-legend');

          echo "</div>";
      }
  ?>


<?php if (!isset($landingpage)): ?>
<div class="<?= !isset($left_title) && !isset($right_title)
                ? 'col-md-12'
                : (
                    isset($right_title)
                    ? 'col-md-6'
                    : ((isset($in_help) && $in_help)
                            ? 'col-md-9 col-lg-6'
                            : 'col-md-9') 
                    ) ?> centerpanel" id="centerpanel">
  <div class="centerblock">
    <?= $center ?>
  </div>
</div>
<?php endif; ?>


<?php if (isset($right_title)): ?>
  <div class="col-md-3 sidepanel" id="rightpanel">
    <?php show_panel($right_title, $right) ?>
  </div>
<?php endif; ?>

<?php
  if (isset($logos))
    logos();
?>

<?php if (!isset($landingpage)): ?>
  </div>
<?php endif; ?>
