<?php

if (empty($avail_classes))
    echo "<h2>No classes available for enrollment</h2>\n";
else {
    echo "<h1>Click &ldquo;Enroll&rdquo; to Enroll in a Class</h1>\n";
    echo "<table>\n";
    foreach ($avail_classes as $clid) {
        $cl = $all_classes[$clid];
        echo "<tr>\n";
        echo "<td>$cl->classname</td>\n";
        echo "<td><a class=\"makebutton\" href=\"",site_url('userclass/enroll_in'),"?classid=$cl->id\">Enroll</a></td>\n";
        echo "</tr>\n";
    }
    echo "</table>\n";
    echo "<p>&nbsp;</p>\n";
}

if (empty($old_classes))
    echo "<h2>You are currently not enrolled in any classes</h2>\n";
else {
    echo "<h2>You are currently enrolled in these classes:</h2>\n";
    echo "<table>\n";
    foreach ($old_classes as $clid) {
        $cl = $all_classes[$clid];
        echo "<tr>\n";
        echo "<td>$cl->classname</td>\n";
        echo "</tr>\n";
    }
    echo "</table>\n";
}
