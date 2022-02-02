<?php

/* This is common code to calculate grades from percentages */

/**
* Creates default array of Grade Scales/Scheme
* @return Array of schemes
*/
function createArrayOfGradeSchemes() {
  return array(
    // Percentage Grading, which does not need the extra array of gade scales
    "percent" => array(
      "SchemeName"=>"Percentage Grading",
      "SchemeType"=>"P",
      "GradeScale"=>array(),
    ),
    // Decimal (0-10) Grading, which does not need the extra array of gade scales
    "decimal" => array(
      "SchemeName"=>"Decimal (0-10) Grading",
      "SchemeType"=>"D",
      "GradeScale"=>array(),
    ),
    // Letter Grading
    "usletter" => array(
      "SchemeName"=>"US Letter Grading",
      "SchemeType"=>"M", // Stands for Mnemonic, which means it needs a table (the array below) to link the percentange range with the corresponding symbol
      // Pay atention on the GradeScale array to leave no gaps. Always from highest to lowest percentages
      "GradeScale"=>array(
        "A" => array(
          "high" => 101,  // Alway put the percentage highher because the calculation logic assumes greater than "low" and less than " high" to select that specific grade so we leave no gaps
          "low"  => 95,
        ),
        "A-" => array(
          "high" => 95,  // Alway put the percentage highher because the calculation logic assumes greater than "low" and less than " high" to select that specific grade so we leave no gaps
          "low"  => 90,
        ),
        "B+" => array(
          "high" => 90,  // Alway put the percentage highher because the calculation logic assumes greater than "low" and less than " high" to select that specific grade so we leave no gaps
          "low"  => 85,
        ),
        "B" => array(
          "high" => 85,  // Alway put the percentage highher because the calculation logic assumes greater than "low" and less than " high" to select that specific grade so we leave no gaps
          "low"  => 80,
        ),
        "B-" => array(
          "high" => 80,  // Alway put the percentage highher because the calculation logic assumes greater than "low" and less than " high" to select that specific grade so we leave no gaps
          "low"  => 75,
        ),
        "C+" => array(
          "high" => 75,  // Alway put the percentage highher because the calculation logic assumes greater than "low" and less than " high" to select that specific grade so we leave no gaps
          "low"  => 70,
        ),
        "C" => array(
          "high" => 70,  // Alway put the percentage highher because the calculation logic assumes greater than "low" and less than " high" to select that specific grade so we leave no gaps
          "low"  => 65,
        ),
        "C-" => array(
          "high" => 65,  // Alway put the percentage highher because the calculation logic assumes greater than "low" and less than " high" to select that specific grade so we leave no gaps
          "low"  => 60,
        ),
        "D" => array(
          "high" => 60,  // Alway put the percentage highher because the calculation logic assumes greater than "low" and less than " high" to select that specific grade so we leave no gaps
          "low"  => 55,
        ),
        "F" => array(
          "high" => 55,  // Alway put the percentage highher because the calculation logic assumes greater than "low" and less than " high" to select that specific grade so we leave no gaps
          "low"  => -10,
        ),
      ),
    ),
    // German Numeric Grade System (1 is the best score and 6 the worst)
    "german" => array(
      "SchemeName"=>"German Grade System",
      "SchemeType"=>"M", // Stands for Mnemonic, which means it needs a table (the array below) to link the percentange range with the corresponding symbol
      // Pay atention on the GradeScale array to leave no gaps. Always from highest to lowest percentages
      "GradeScale"=>array(
        "1+" => array(
          "high" => 101,  // Alway put the percentage highher because the calculation logic assumes greater than "low" and less than " high" to select that specific grade so we leave no gaps
          "low"  => 99,
        ),
        "1" => array(
          "high" => 99,  // Alway put the percentage highher because the calculation logic assumes greater than "low" and less than " high" to select that specific grade so we leave no gaps
          "low"  => 95,
        ),
        "1-" => array(
          "high" => 95,  // Alway put the percentage highher because the calculation logic assumes greater than "low" and less than " high" to select that specific grade so we leave no gaps
          "low"  => 91,
        ),
        "2+" => array(
          "high" => 91,  // Alway put the percentage highher because the calculation logic assumes greater than "low" and less than " high" to select that specific grade so we leave no gaps
          "low"  => 88,
        ),
        "2" => array(
          "high" => 88,  // Alway put the percentage highher because the calculation logic assumes greater than "low" and less than " high" to select that specific grade so we leave no gaps
          "low"  => 85,
        ),
        "2-" => array(
          "high" => 85,  // Alway put the percentage highher because the calculation logic assumes greater than "low" and less than " high" to select that specific grade so we leave no gaps
          "low"  => 81,
        ),
        "3+" => array(
          "high" => 81,  // Alway put the percentage highher because the calculation logic assumes greater than "low" and less than " high" to select that specific grade so we leave no gaps
          "low"  => 78,
        ),
        "3" => array(
          "high" => 78,  // Alway put the percentage highher because the calculation logic assumes greater than "low" and less than " high" to select that specific grade so we leave no gaps
          "low"  => 70,
        ),
        "3-" => array(
          "high" => 70,  // Alway put the percentage highher because the calculation logic assumes greater than "low" and less than " high" to select that specific grade so we leave no gaps
          "low"  => 66,
        ),
        "4+" => array(
          "high" => 66,  // Alway put the percentage highher because the calculation logic assumes greater than "low" and less than " high" to select that specific grade so we leave no gaps
          "low"  => 61,
        ),
        "4" => array(
          "high" => 61,  // Alway put the percentage highher because the calculation logic assumes greater than "low" and less than " high" to select that specific grade so we leave no gaps
          "low"  => 54,
        ),
        "4-" => array(
          "high" => 54,  // Alway put the percentage highher because the calculation logic assumes greater than "low" and less than " high" to select that specific grade so we leave no gaps
          "low"  => 50,
        ),
        "5+" => array(
          "high" => 50,  // Alway put the percentage highher because the calculation logic assumes greater than "low" and less than " high" to select that specific grade so we leave no gaps
          "low"  => 40,
        ),
        "5" => array(
          "high" => 40,  // Alway put the percentage highher because the calculation logic assumes greater than "low" and less than " high" to select that specific grade so we leave no gaps
          "low"  => 20,
        ),
        "5-" => array(
          "high" => 20,  // Alway put the percentage highher because the calculation logic assumes greater than "low" and less than " high" to select that specific grade so we leave no gaps
          "low"  => 10,
        ),
        "6" => array(
          "high" => 10,  // Alway put the percentage highher because the calculation logic assumes greater than "low" and less than " high" to select that specific grade so we leave no gaps
          "low"  => -10,
        ),
      ),
    ),
  );
}

/**
* Loads array of Grade Scales/Scheme to memmory from file
* @return bool $success if successful
*/
function loadArrayOfGradeSchemes() {
  global $arrayOfGradeSchemes;

  // Check if the file exists, if not create it

  // Check if the file is readble (if not, throw a error)

  //load from file to memory
  // TODO: FIXME: for now, just creating the array in from the defaults.
  $arrayOfGradeSchemes = createArrayOfGradeSchemes();

  return $arrayOfGradeSchemes;  // Always return the array if everything goes fine
}

/**
* Save array of Grade Scales/Scheme from memmory to disk
* @return bool $success if successful
*/
function saveArrayOfGradeSchemes() {
  global $arrayOfGradeSchemes;

  // Check if the file exists, if not create it

  // Check if the file is writable (if not, throw a error)

  //load from file to memory
  // TODO: FIXME: Do somethin, the function is just a stub for now

  return true;  // Always return true
}

/**
* Calculate grade
* @param string $schemaID Key on the $arrayOfGradeSchemes
* @param float $percentage to calculate the grades from
* @return $grade|-1  Returns -1 if schema is invalid
*/
function calculateGrade($schemaID, $percentage) {
  global $arrayOfGradeSchemes;

  // see if the array is loaded
  if ( !isset($arrayOfGradeSchemes) ) {
    // Loads it if not
    loadArrayOfGradeSchemes();
  }

  // Finds the right schema. Returns -1 if schema not found
  if ( empty($arrayOfGradeSchemes[$schemaID]) ) {
    return -1;
  }
  $gradeSystem = $arrayOfGradeSchemes[$schemaID];

  // return the grade according with the type
  switch ($gradeSystem["SchemeType"]) {
    case 'P':
      // return the raw percentage
      return round($percentage)."%";
      break;
    case 'D':
      // return the decimal equivalent
      return round($percentage/10,1);
      break;
    case 'M':
      // finds the right highest grade we can give given the [ercentage]
      foreach ($gradeSystem["GradeScale"] as $gradeSymbol => $value) {
        // Check if the $percentage is betwen the mas and minimum of this level
        if ( ($percentage >= $value["low"] && $percentage < $value["high"]) ) {
          // Return the symbol
          return $gradeSymbol;
        }
      }
      // If not found, it is a invalid grade.  Return -1
      return -1;
      break;

    default:
      // If invalid parameters were given, return -1.
      return -1;
      break;
  }
}
