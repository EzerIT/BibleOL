<?php /* Assumption isset($right_title) implies isset($left_title) */

/* Show intro only if landingpage */
function show_intro(&$center, &$landingpage) {
  if (!isset($landingpage))
      return;

    echo <<<EOD
    <div class="row" id="landing-row">
      <div class="col-md-8" id="landingpanel">
        <img class="graphic-element" src="/images/dadel/DaDEL_ID_graphic_element_RGB.png">
        <div id="landingtext">
          $center
        </div>
      </div>
      <div class="col-md-4" id="loginpanel">
        <h1>Press the button to login</h1>
        <form action="/login">
          <button class="btn btn-primary">Login</button>
        </form>
        <h1>No login...?</h1>
        <form action="/users/sign_up">
          <button class="btn btn-outline-dark">Sign up for free</button>
        </form>
        <form action="/text/select_text">
          <button class="btn btn-outline-dark">Start text reading</button>
        </form>
        <form action="/text/select_quiz">
          <button class="btn btn-outline-dark">Try an exercise</button>
        </form>
      </div>
    </div>

EOD;
}

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
    echo "  <div class=\"row logointro\">";
    echo "    <h2>Partners behind BibleOL</h2>";
    echo "  </div>";
    echo "  <div class=\"row logopanel\">";
    echo "    <div class=\"col-md-3\">";
    echo "      <a class=\"navbar-link\" href=\"http://www.ezer.dk\" target=\"_blank\"><img class=\"logo\" src=\"images/logos/ezer_web_trans_lille.png\" style=\"width: 140px; text-align: center;\"></a></div>";
    echo "    <div class=\"col-md-3\">";
    echo "      <a class=\"navbar-link\" href=\"http://vu.nl\" target=\"_blank\"><img class=\"logo\" src=\"images/logos/vu.png\" style=\"width: 260px\"></a></div>";
    echo "    <div class=\"col-md-3\">";
    echo "      <a class=\"navbar-link\" href=\"http://pthu.nl\" target=\"_blank\"><img class=\"logo\"  src=\"images/logos/pthu.png\" style=\"width: 260px\"></a></div>";
    echo "    <div class=\"col-md-3\">";
    echo "      <a class=\"navbar-link\"  href=\"http://3bmoodle.dk\" target=\"_blank\"><img class=\"logo\" src=\"images/logos/3bm_logo.png\" style=\"width: 150px\"></a></div>";
    echo "  </div>";
}

?>



<?php 
  show_intro($center, $landingpage);
?>

<?php if (!isset($landingpage)): ?>
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
<div class="<?= isset($right_title) ? 'col-md-6' : 'col-md-9' ?> centerpanel" id="centerpanel">
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
