<?php

  // Documentation: Done

require_once('include/typeinfo.inc.php');
require_once('include/monadobject.inc.php');

/// This class holds the information required to display and answer a single sentence (or whatever
/// sequence of monads the system will display).
/// 
/// Crucial to the understanding of this class is the concept of a _level_. A level is a
/// non-negative number that describes a particular group of text objects. Level 0 refers to the
/// lowest level Emdros object, typically a 'word'. Levels 1, 2, etc. refer to the objects that
/// describe higher level sentence components: typically, level 1 is 'phrase', level 2 is 'clause'
/// etc.
/// 
/// At each level, the Dictionary contains a list of MonadObjects that describe the sentence
/// components found at that level. At level 0, there are SingleMonadObject%s. At the higher levels,
/// there are MultipleMonadObject%s.
///
/// For each set of monads there is a single top-level object, known as the _patriarch_.
/// 
/// A MonadObject contains the data found in the Emdros database.
class Dictionary {
    private $maxLevels;    ///< The number of levels.
    public $sentenceSets; ///< All the monads in this Dictionary object. Indexed by sentence set number.
    public $sentenceSetsQuiz; ///< All the quiz-related monads in this Dictionary object. Indexed by sentence set number.
    private $singleMonads; ///< Maps a monad number to the SingleMonadObject for a particular word. (Type: Map<Integer, SingleMonadObject>)
    private $singleMonadsM; ///< For each question in a quiz, maps a monad number to the SingleMonadObject for a particular word. (Type: Map<Integer, SingleMonadObject>)
    public $monadObjects; ///< A list of the MonadObject%s in sentence set at each level.
                           ///< $this->monadObjects[$x][$y][$z] is the $z'th monad at level $y in sentence set $x.
    public $bookTitle;    ///< Book title
    private $glosslimit;  ///< Gloss limit for quizzes


    /// Gets the title of the book.
    /// @return The title of the book
    public function get_book_title() {
        return $this->bookTitle;
    }

    /// Gets the set of monads in this object.
    /// @return The set of monads in this object.
    public function getSentenceSet() {
        return $this->sentenceSet;
    }


    private $indir_db_handle = array(); // Maps databases to database handles
    private $indirectLookupCache = array(); // Maps keys to values
    private static $firstrow;

    // Look up a feature outside Emdros
    // Note: Features in $mo->features are HTML encoded
    public function indirectLookup($feat, $mo, $fset, $test_glosslimit) {
        assert(isset($fset->indirdb));
        assert(isset($fset->sql) || isset($fset->sql_command));
        assert(isset($fset->sqlargs));
        assert(isset($fset->multiple));

        if ($test_glosslimit && ($fset->isGloss ?? false)) {
            assert(isset($mo->features['frequency_rank']));
            if ($mo->features['frequency_rank'] <= $this->glosslimit) {
                $mo->features[$feat] = '&#x26d4;'; // No entry sign
                return;
            }
        }
    
        $key_array = array();
        foreach ($fset->sqlargs as $sqlarg)
            $key_array[] = htmlspecialchars_decode($mo->features[$sqlarg]);

        $key = implode(',',$key_array) . ',' . $feat;

        if (!isset($this->indirectLookupCache[$key])) {
            if (!isset($this->indir_db_handle[$fset->indirdb])) {
                $CI =& get_instance();
                $this->indir_db_handle[$fset->indirdb] = 
                    $fset->indirdb=='mysql'
                    ? $CI->db
                    : $CI->load->database(array('database' => 'db/' . $fset->indirdb,
                                                'dbdriver' => 'sqlite3',
                                                'dbprefix' => '',
                                                'pconnect' => FALSE,
                                                'db_debug' => TRUE,
                                                'cache_on' => FALSE,
                                                'cachedir' => '',
                                                'char_set' => 'utf8',
                                                'dbcollat' => 'utf8_general_ci'),
                                          true);
            }

            if (isset($fset->sql_command_variant))
                $query = $this->indir_db_handle[$fset->indirdb]->query(vsprintf($fset->sql_command_variant,$key_array));
            elseif (isset($fset->sql_command))
                $query = $this->indir_db_handle[$fset->indirdb]->query(vsprintf($fset->sql_command,$key_array));
            else 
                $query = $this->indir_db_handle[$fset->indirdb]
                    ->select($fset->sql[0])
                    ->where(vsprintf($fset->sql[2],$key_array), NULL, false)
                    ->get($fset->sql[1]);

            if ($fset->multiple) {
                // We may get more than one answer from the database
                $this->indirectLookupCache[$key] = array();

                $result = $query->result_array();
                foreach ($result as $row) {
                    if (count($row)==1)
                        // Several rows, one field
                        $this->indirectLookupCache[$key][] = htmlspecialchars(current($row));
                    else {
                        // Several rows, several fields
                        foreach ($row as &$r)
                            $r = htmlspecialchars($r);
                        $this->indirectLookupCache[$key][] = $row;
                    }
                }
            }
            else {
                // We should get only one answer from the database
                $row = $query->row_array();
                
                if (isset($row)) {
                    if (count($row)==1)
                        // One row, one field
                        $this->indirectLookupCache[$key] = htmlspecialchars(current($row));
                    else {
                        // One row, several fields
                        foreach ($row as &$r)
                            $r = htmlspecialchars($r);
                        $this->indirectLookupCache[$key] = $row;
                    }
                }
                else
                    $this->indirectLookupCache[$key] = '*';
            }
        }

        $mo->features[$feat] = $this->indirectLookupCache[$key];
    }


    /// Fetches the names of all the features for the Emdros object at a particular level
    /// (word/phrase/clause etc.), and returns it as an array.
    /// @param $gl The sentencegrammar information for the relevant level
    /// @param $all Each feature is stored in this array.
    /// @param $indirect Maps features that are to be retrieved outside Emdros to its feature configuration
    private static function getOneLeveFeatureString($gl, string $objType, array &$all, string &$subtype, array &$subtypeall, array &$indirect) {
        if (isset($gl->items)) {
            foreach ($gl->items as $it)
                self::getOneLeveFeatureString($it,$objType,$all,$subtype,$subtypeall,$indirect);
        }
        else {
            if (isset($gl->name)) {
                $glName = $gl->name;
                if (strstr($glName, ':')===false) {
                    $objSettings =& get_instance()->db_config->dbinfo->objectSettings;
                    if (isset($objSettings->$objType->featuresetting->$glName->sqlargs)) {
                        $indirect[$glName] = $objSettings->$objType->featuresetting->$glName;
                        foreach($objSettings->$objType->featuresetting->$glName->sqlargs as $n)
                            $all[$n] = $n;  // Uses an array to emulate a set
                    }
                    else
                        $all[$glName] = $glName;  // Uses an array to emulate a set
                }
                else {
                    list($subt, $name) = explode(':', $glName);
                    assert($subtype==='' || $subtype===$subt);
                    $subtype = $subt;
                    $namecomponents = explode('_TYPE_', $name); // Split off format specification
                    $subtypeall[] = $namecomponents[0];
                }
            }
        }
    }
 
    /// Fetches the names of all the features for the Emdros object at a particular level
    /// (word/phrase/clause etc.), and returns it as a string.
    /// @param $dbi The database information struction
    /// @param $grammarListIx The level
    /// @param $indirect Maps features that are to be retrieved outside Emdros to sentencegrammar information
    /// @return A comma-separated string of feature names
    private static function getAllFeaturesString($dbi, $grammarListIx, &$subtype, &$subtypeall, array &$indirect) {
        $all = array();
        $subtype = '';
        $subtypeall = array();
        self::getOneLeveFeatureString($dbi->sentencegrammar[$grammarListIx],
                                      $dbi->sentencegrammar[$grammarListIx]->objType,
                                      $all, $subtype, $subtypeall, $indirect);
        return implode(',',$all);
    }

    /// Creates a Dictionary object. This constructor reads information from the relevant
    /// Emdros database and builds the text component hierarchy.
    /// @param $params['mset'] The monads that describe the text.
    /// @param $params['mset_quiz'] The monads that describe the part of the text that contains question objects. (Only set in quizzes.)
    /// @param $params['inQuiz'] Is this part of a quiz (in which case there is only one top-level object)?
    /// @param $params['showIcons'] Show icons be shown in the text?
    /// @param $params['glosslimit'] (optional) Limit for showing glosses in quizzes
    function __construct(array $params) {
        $msets = $params['msets'];  // Possibly includes surrounding sentences
        $this->sentenceSetsQuiz = isset($params['msets_quiz']) ? $params['msets_quiz'] : null;
        $inQuiz = $params['inQuiz'];
        $showIcons = $params['showIcons'];
        $this->glosslimit = $params['glosslimit'] ?? 0;

        $CI =& get_instance();
        $CI->load->library('picdb');

        $dbinfo = $CI->db_config->dbinfo; // Cached here

        $number_sets = count($msets);

        $this->maxLevels = count($dbinfo->sentencegrammar);
        ++$this->maxLevels; // We need to add an artificial top-level object (the patriarch)

        // If this is a text display, we must extend the msets to cover an entire sentence
		if (!$inQuiz) {
            assert($number_sets==1);

            $emdros_data = $CI->mql->exec("GET OBJECTS HAVING MONADS IN $msets[0] [" 
                                          . $dbinfo->sentencegrammar[$this->maxLevels-2]->objType
                                          ." ] GOqxqxqx"); 
            
            $sh = $emdros_data[0]->get_sheaf();

            $mset2 = new OlMonadSet();

            foreach ($sh->get_straws() as $str)
                foreach ($str->get_matched_objects() as $mo) {
                    $mset2->addSet($mo->get_monadset());

					// TODO: For now, we simply reduce the number of monads fetched to 1603 (which is the length of 1 Kings 8)
					if ($mset2->size() > 1603) {
                        INFORMATION("The size of the passages is too large, it has been reduced"); // TODO: Fix this
						break;
					}
				}
            $msets = array($mset2);
        }

        $this->sentenceSets = $msets;
 
        $this->singleMonads = array();
        $this->singleMonadsM = array();
        for ($msetIndex=0; $msetIndex<$number_sets; ++$msetIndex)
            $this->singleMonadsM[$msetIndex] = array();
        $this->monadObjects = array();
 
        for ($msetIndex=0; $msetIndex<$number_sets; ++$msetIndex) {
            $moarr = array();
            for ($i=0; $i<$this->maxLevels; ++$i)
                $moarr[] = array();   // Indexed by level, contains monad objects
            $this->monadObjects[] = $moarr; // Indexed by mset, contains levels
        }


        // We build a long command to reduce the number of times we have to start the mql process

        assert($dbinfo->objHasSurface === $dbinfo->sentencegrammar[0]->objType);

        $mset_union = new OlMonadSet();
        foreach ($msets as $mset)
            $mset_union->addSet($mset);

        $command = '';
        $indirect = array();

        foreach ($msets as $mset) {
            for ($sdiIndex=0; $sdiIndex<$this->maxLevels-1; ++$sdiIndex) {
                $sg = $dbinfo->sentencegrammar[$sdiIndex];

                $allFeat = self::getAllFeaturesString($dbinfo, $sdiIndex, $subtype, $subtypeAllFeat, $indirect);
                if ($sdiIndex==0) {
                    $allFeat .= ",$dbinfo->surfaceFeature";
                    if (isset($dbinfo->suffixFeature))
                        $allFeat .= ",$dbinfo->suffixFeature";
                }

                if ($subtype!=='')
                    $command .= "SELECT ALL OBJECTS IN $mset WHERE [$sg->objType "
                        . (empty($allFeat) ? '' : "GET $allFeat")
                        . " [$subtype GET " . implode(',',$subtypeAllFeat) . "]"
                        . "] GOqxqxqx\n";
                else
                    $command .= "SELECT ALL OBJECTS IN $mset WHERE [$sg->objType "
                        . (empty($allFeat) ? '' : "GET $allFeat")
                        . "] GOqxqxqx\n";
            }
        }

        if (!$inQuiz)
            foreach ($dbinfo->universeHierarchy as $uht)
                $command .= "GET OBJECTS HAVING MONADS IN $mset_union [$uht->type GET $uht->feat] GOqxqxqx\n";

        $emdros_data = $CI->mql->exec($command);

        $mqlresult_index = 0;

		for ($msetIndex=0; $msetIndex<$number_sets; ++$msetIndex) {
            for ($sdiIndex=0; $sdiIndex<$this->maxLevels-1; ++$sdiIndex) {
                $sh = $emdros_data[$mqlresult_index++]->get_sheaf();
 
                foreach ($sh->get_straws() as $str) {
                    foreach ($str->get_matched_objects() as $mo) {
                        if ($sdiIndex==0) {
                            foreach ($indirect as $feat => $fsetting)
                                $this->indirectLookup($feat, $mo, $fsetting, true);
                        }
                        $this->addMonadObject($msetIndex, $sdiIndex, $mo);
                    }
                }
            }
		}
		
		// Create artifical top-level object
		for ($msetIndex=0; $msetIndex<$number_sets; ++$msetIndex) {
            $mset = $msets[$msetIndex];
			$mo = new OlMatchedObject(-1, 'Patriarch');
			$mo->set_monadset($mset);
			$this->addMonadObject($msetIndex, $this->maxLevels-1, $mo);
        }
 
		//*****************************************
        // Add book, chapter, and verse information

        $uni_count = count($dbinfo->universeHierarchy);
        
        if (!$inQuiz) {
            // Set book, chapter, and verse information on each SingleMonadObject
            
            for ($unix=0; $unix<$uni_count; ++$unix) {
                $last_uni_level = $unix===$uni_count-1;

                $sh = $emdros_data[$mqlresult_index++]->get_sheaf();
                assert($sh->number_of_straws()==1);
 
                $lastSmo = null;
 
                $str = $sh->get_first_straw();
                foreach ($str->get_matched_objects() as $mo) {
                    $featureValue = $mo->get_feature($dbinfo->universeHierarchy[$unix]->feat);
                    if (is_numeric($featureValue))
                        $featureValue = intval($featureValue);
 
                    $newPoint = true; // Did we enter a new book, chapter, or verse?
                    foreach ($mo->get_monadset() as $monad) {
                        if (isset($this->singleMonads[$monad])) {
                            $smo = $this->singleMonads[$monad];
                            $smo->add_bcv($featureValue);
                            $smo->add_sameAsPrev(!$newPoint);
                            if ($lastSmo!=null)
                                $lastSmo->add_sameAsNext(!$newPoint);
                            $lastSmo = $smo;
                            if ($this->bookTitle==null)
                                $this->bookTitle = $featureValue;  // This only makes sense for non-quiz
                            if ($showIcons && $last_uni_level && $newPoint) { 
                                $smo->set_pics($CI->picdb->get_pics($smo->get_bcv()));
                                $smo->set_urls($CI->picdb->get_urls($smo->get_bcv()));
                            }
                            $newPoint = false;
                        }
                    }
                }
                if ($lastSmo!=null)
                    $lastSmo->add_sameAsNext(false);
            }
        }
        else {
            // Set book, chapter, and verse information on the first quiz object monad

 			$command = '';
			foreach ($this->sentenceSetsQuiz as $question_monads)
				foreach ($dbinfo->universeHierarchy as $uht)
					$command .= "GET OBJECTS HAVING MONADS IN {{$question_monads->low()}} [$uht->type GET $uht->feat] GOqxqxqx\n";
			$emdros_data = $CI->mql->exec($command);
			$mqlresult_index = 0;

            for ($msetIndex=0; $msetIndex<$number_sets; ++$msetIndex) {
                $question_monads = $this->sentenceSetsQuiz[$msetIndex];

				foreach ($dbinfo->universeHierarchy as $uht) {
					$sh = $emdros_data[$mqlresult_index++]->get_sheaf();
					assert($sh->number_of_straws()==1);
 
					$str = $sh->get_first_straw();
					assert($str->number_of_matched_objects()==1);
 
					$featureValue = $str->get_first_matched_object()->get_feature($uht->feat);
					if (is_numeric($featureValue))
						$featureValue = intval($featureValue);
 
					$smo = $this->singleMonadsM[$msetIndex][$question_monads->low()];
                    $smo->add_bcv($featureValue);
 
				}
			}
        }
 
		$this->constructHierarchy();
    }

    /// Creates a MonadObject corresponding to a given OlMatchedObject and adds it to
	/// the sentence hierarchy.
	/// @param $msetIndex The index into the arry of OlMonadSet%s.
	/// @param $level The level at which the sentence object should be added.
	/// @param $matob An OlMatchedObject describing the text component.
    private function addMonadObject($msetIndex, $level, OlMatchedObject $matob) {
        if ($level==0) {
            // Add SingleMonadObject
            $thisMo = new SingleMonadObject($matob);
            $this->monadObjects[$msetIndex][0][] = $thisMo;

            $monad = $thisMo->get_mo()->get_monadset()->getSingleInteger();
            $this->singleMonads[$monad] = $thisMo;
            $this->singleMonadsM[$msetIndex][$monad] = $thisMo;
            
        }
        else {
            // Add MultipleMonadObject
            $mmo = new MultipleMonadObject($matob);
            $this->monadObjects[$msetIndex][$level][] = $mmo;
        }
    }
 
	/// This function is called once the MonadObject%s have been created. It provides
	/// parent-child linking.
	private function constructHierarchy() {
		//////////////////////////////////////////////////
		// Construct child-parent linkage for MonadObjects
		//////////////////////////////////////////////////
 
        $dummyidd = 10000000;

        $number_sets = count($this->sentenceSets);

		for ($msetIndex=0; $msetIndex<$number_sets; ++$msetIndex) {
            $moarr = &$this->monadObjects[$msetIndex]; // Cached here;
			for ($i=1; $i<$this->maxLevels; ++$i) {
				// Find constituent MonadObjects
 
				foreach ($moarr[$i] as $parentMo) { // Loop through monads at level i
					foreach ($moarr[$i-1] as $childMo) { // Loop through mondads at child level
						if ($childMo->contained_in($parentMo))
							$parentMo->add_child($childMo);
					}
				}
 
				// Find MonadObjects without parents
				foreach ($moarr[$i-1] as $childMo) {
					if ($childMo->get_parent()===null) {
						$matobj = new OlMatchedObject($dummyidd++, 'dummy');
						$matobj->monadset = $childMo->mo->monadset;
 
						$mmo = new MultipleMonadObject($matobj);
						$moarr[$i][] = $mmo;
						$mmo->add_child($childMo);
					}
				}
			}

//            // Print hierarchy
//            for ($i=1; $i<$this->maxLevels; ++$i) {
//                foreach ($moarr[$i] as $parentMo) {
//                    echo "<pre>parent = ",$parentMo->get_id_d()," : ";
//                    foreach ($parentMo->children_idds as $cid)
//                        echo $cid," ";
//                    echo "</pre>";
//                }
//            }
        }
    }
 
 
    /// Retrieves the text to display for a particular monad number.
	/// @param $monad The monad number.
	/// @return The text to display for the specified monad number.
    public function getVisual($monad) {
        $smo = $this->singleMonads[$monad];
        return $smo->get_text() . $smo->get_suffix();
    }
}
