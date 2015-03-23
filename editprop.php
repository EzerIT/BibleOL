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
    print "Usage: php prop2json.php <input file>\n";
    die;
}


$input = @file_get_contents($_SERVER['argv'][1]) or die ("Failed opening file {$_SERVER['argv'][1]}:\nError was '$php_errormsg'\n");

$props = json_decode($input);
//print_r($props->emdrostype->tense_e);
//echo "------\n";
//print_r($props->emdrostype->clause_atom_relation_tense_of_verbal_predicate_e);
// 
//echo "------\n";

$props->emdrostype->tense_e->perfect = "Qatal (Perf)";
$props->emdrostype->tense_e->imperfect = "Yiqtol (Impf)";
$props->emdrostype->tense_e->wayyiqtol = "Wayyiqtol (weCImpf)";
$props->emdrostype->tense_e->weqatal = "Weqatal (weCPerf)";

$props->emdrostype->clause_atom_relation_tense_of_verbal_predicate_e->perfect = "Qatal (Perf)";
$props->emdrostype->clause_atom_relation_tense_of_verbal_predicate_e->imperfect = "Yiqtol (Impf)";
$props->emdrostype->clause_atom_relation_tense_of_verbal_predicate_e->wayyiqtol = "Wayyiqtol (weCImpf)";
//$props->emdrostype->clause_atom_relation_tense_of_verbal_predicate_e->weqatal = "WQtl (weCPerf)";

//print_r($props->emdrostype->tense_e);
//echo "------\n";
//print_r($props->emdrostype->clause_atom_relation_tense_of_verbal_predicate_e);


print(json_encode($props));
