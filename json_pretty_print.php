<?php
  // Copyright © 2013 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk

  // This is for command line usage
  // Usage:
  //    php prop2json.php <properties file>
  // Outputs a JSON version of the Java properties file (localization file) to stdout.


error_reporting(E_ALL|E_STRICT);
ini_set('display_errors', 'stderr');
ini_set('track_errors', true);

mb_internal_encoding('UTF-8');


if (($_SERVER['argc']!==2 && $_SERVER['argc']!==3) || ($_SERVER['argv'][1]!=='-p' && $_SERVER['argv'][1]!=='-u')) {
    print "Usage: php (-p|-u) json_pretty_print.php [<input file>]\n";
    print "       -p for pretty\n";
    print "       -u for ugly\n";
    die;
}

$filename = $_SERVER['argc']===3 ? $_SERVER['argv'][2] : 'php://stdin';

$input = @file_get_contents($filename) or die ("Failed opening file {$filename}:\nError was '$php_errormsg'\n");

$props = json_decode($input);


if ($_SERVER['argv'][1]==='-p')
    print(json_encode($props, JSON_PRETTY_PRINT));
else
    print(json_encode($props));
