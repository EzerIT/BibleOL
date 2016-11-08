<?php
  // Copyright © 2016 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk

  // This is for command line usage
  // Usage:
  //    php skip3.php <file>
  // Removes the first three bytes from a file, provided they are the Unicode BOM


error_reporting(E_ALL|E_STRICT);
ini_set('display_errors', 'stderr');
ini_set('track_errors', true);

mb_internal_encoding('UTF-8');


if ($_SERVER['argc']!==2) {
    echo $_SERVER['argc'],"\n";
    print "Usage: php skip3.php <file>\n";
    die;
}

$filename = $_SERVER['argv'][1];

$input = @file_get_contents($filename) or die ("Failed opening file {$filename}:\nError was '$php_errormsg'\n");

$bom = substr($input,0,3);

if (ord($bom[0])==0xef && 
    ord($bom[1])==0xbb &&
    ord($bom[2])==0xbf) {
    echo "$filename: Has BOM\n";
    file_put_contents($filename, substr($input,3));
}
else
    echo "$filename: No BOM\n";

