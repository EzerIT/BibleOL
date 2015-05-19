<?php

if (empty($avail_classes))
    echo "<h2>", $this->lang->line('no_classes_enroll'), "</h2>\n";
else {
    echo "<h1>", $this->lang->line('click_enroll'), "</h1>\n";
    echo "<table>\n";
    foreach ($avail_classes as $clid) {
        $cl = $all_classes[$clid];
        echo "<tr>\n";
        echo "<td>$cl->classname</td>\n";
        echo "<td><a class=\"makebutton\" href=\"",site_url('userclass/enroll_in'),"?classid=$cl->id\">", $this->lang->line('enroll'), "</a></td>\n";
        echo "</tr>\n";
    }
    echo "</table>\n";
    echo "<p>&nbsp;</p>\n";
}

if (empty($old_classes))
    echo "<h2>", $this->lang->line('enrolled_in_no_classes'), "</h2>\n";
else {
    echo "<h2>", $this->lang->line('enrolled_in_these_classes'), "</h2>\n";
    echo "<table>\n";
    foreach ($old_classes as $clid) {
        $cl = $all_classes[$clid];
        echo "<tr>\n";
        echo "<td>$cl->classname</td>\n";
        echo "</tr>\n";
    }
    echo "</table>\n";
}
