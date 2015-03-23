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


if ($_SERVER['argc']!==2) {
    print "Usage: php prop2json.php <properties file>\n";
    die;
}


$allprop = array();
$emdrosobject = array();

$file = @fopen($_SERVER['argv'][1],'r') or die ("Failed opening file {$_SERVER['argv'][1]}:\nError was '$php_errormsg'\n");

while (($s=fgets($file))!==false) {
    $s = trim($s);
    if (empty($s))
        continue;

    list($name,$value) = explode('=',$s,2);
    $parts = explode('.',$name);

    if (count($parts)===1)
        $allprop[$parts[0]] = $value;
    else {
        if (!isset($allprop[$parts[0]]))
            $allprop[$parts[0]] = array();
        if (!isset($allprop[$parts[0]][$parts[1]]))
            $allprop[$parts[0]][$parts[1]] = array();

        switch ($parts[0]) {
          case 'emdrosobject':
                if (count($parts)===2)
                    $allprop[$parts[0]][$parts[1]]['_objname'] = $value;
                else {
                    assert(count($parts)===3);
                    $allprop[$parts[0]][$parts[1]][$parts[2]] = $value;
                }
                break;

          case 'emdrostype':
          case 'grammargroup':
          case 'grammarfeature':
          case 'grammarmetafeature':
                assert(count($parts)===3);
                $allprop[$parts[0]][$parts[1]][$parts[2]] = $value;
                break;

          case 'grammarsubfeature':
                if (!isset($allprop[$parts[0]][$parts[1]][$parts[2]]))
                    $allprop[$parts[0]][$parts[1]][$parts[2]] = array();
                assert(count($parts)===4);
                $allprop[$parts[0]][$parts[1]][$parts[2]][$parts[3]] = $value;
                break;

          case 'universe':
                if (count($parts)===2)
                    $allprop[$parts[0]][$parts[1]]['_label'] = $value;
                else {
                    assert(count($parts)===3);
                    $allprop[$parts[0]][$parts[1]][$parts[2]] = $value;
                }
                break;
        }
    }
}
print(json_encode($allprop));
