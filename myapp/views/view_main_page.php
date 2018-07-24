<?php /* Assumption isset($right_title) implies isset($left_title) */

/* Optionally shows a panel title and text */
function show_panel(&$title, &$text, $extraclass='') {
    if (!isset($text))
        return;

    echo "<div class=\"card\">\n";
    echo "  <h6 class=\"card-header bg-primary text-light\">$title</h6>\n";
    echo "  <div class=\"card-body $extraclass\">$text</div>\n";
    echo "</div>\n";
}

/* Shows a panel title and optionally a text */
function show_panel2(&$title, &$text) {
    echo "<div class=\"card\">\n";
    echo "  <h6 class=\"card-header bg-primary text-light\">$title</h6>\n";
    if (isset($text))
        echo "  <div class=\"card-body\">$text</div>\n";
    echo "</div>\n";
}

/* Shows logos */
function logos() {
    echo "  <div class=\"card mt-3 d-none d-md-block\">\n";
    echo "    <div class=\"card-body centeralign\">\n";
    echo "      <a class=\"navbar-link\" href=\"http://www.ezer.dk\" target=\"_blank\"><img alt=\"\" src=\"",site_url('images/ezer_web_trans_lille.png'),"\"></a>\n";
    echo "      <p>&nbsp;</p>\n";
    echo "      <a class=\"navbar-link\" href=\"http://3bmoodle.dk\" target=\"_blank\"><img alt=\"\" height=\"43\" src=\"",site_url('images/3bm_logo.png'),"\"></a>\n";
    echo "    </div>\n";
    echo "  </div>\n";
}

?>    


<div class="col-md-3" id="leftpanel">
  <?php
      show_panel2($left_title, $left);

      if (isset($logos))
          logos();

      show_panel($extraleft_title, $extraleft, 'student-legend');
  ?>
</div>


<div class="<?= isset($right_title) ? 'col-md-6' : 'col-md-9' ?>" id="centerpanel">
  <div class="centerblock">
    <?= $center ?>
  </div>
</div>


<?php if (isset($right_title)): ?>
  <div class="col-md-3" id="rightpanel">
    <?php show_panel($right_title, $right) ?>
  </div>
<?php endif; ?>
