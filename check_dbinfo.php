<?php
  // Copyright © 2014 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk

  // This is for command line usage
  //     php check_dbinfo.php xxx.typeinfo.json xxx.db.json
  // Checks that all object features from the xxx.typeinfo.json file are also set in the xxx.db.json file


error_reporting(E_ALL|E_STRICT);
ini_set('display_errors', 'stderr');
ini_set('track_errors', true);

mb_internal_encoding('UTF-8');


if ($_SERVER['argc']!==3) {
    print "Usage: php check_dbinfo.php xxx.typeinfo.json xxx.db.json\n";
    die;
}


$input = @file_get_contents($_SERVER['argv'][1]) or die ("Failed opening file {$_SERVER['argv'][1]}:\nError was '$php_errormsg'\n");

$typeinfo = json_decode($input);

$input = @file_get_contents($_SERVER['argv'][2]) or die ("Failed opening file {$_SERVER['argv'][2]}:\nError was '$php_errormsg'\n");

$props = json_decode($input);


foreach ($typeinfo->objTypes as $ot) {
    $os = $props->objectSettings->$ot;
    if (!isset($os->featuresetting))
        continue;

    foreach ($typeinfo->obj2feat->$ot as $x=>$y) {
        if ($x==="self")
            continue;

        echo "$ot : $x => $y ";
        if (isset($os->featuresetting->$x))
            echo "OK\n";
        else
            echo "MISSING\n";
    }
}
