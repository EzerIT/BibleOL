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
    private $singleMonads; ///< Maps a monad number to the SingleMonadObject for a particular word. (Type: Map<Integer, SingleMonadObject>)
    public $monadObjects; ///< A list of the MonadObject%s in sentence set at each level.
                           ///< $this->monadObjects[$x][$y][$z] is the $z'th monad at level $y in sentence set $x.
    public $bookTitle;    ///< Book title

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
    private function indirectLookup($feat, $mo, $sentenceg) {
        assert(isset($sentenceg->indirdb));
        assert(isset($sentenceg->sql));
        assert(isset($sentenceg->sqlargs));
        assert(isset($sentenceg->multiple));

        $key_array = array();
        foreach ($sentenceg->sqlargs as $sqlarg)
            $key_array[] = htmlspecialchars_decode($mo->features[$sqlarg]);

        $key = implode(',',$key_array) . ',' . $feat;

        if (!isset($this->indirectLookupCache[$key])) {
            if (!isset($this->indir_db_handle[$sentenceg->indirdb])) {
                $CI =& get_instance();
                $this->indir_db_handle[$sentenceg->indirdb] = 
                    $sentenceg->indirdb=='mysql'
                    ? $CI->db
                    : $CI->load->database(array('database' => 'db/' . $sentenceg->indirdb,
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
            $query = $this->indir_db_handle[$sentenceg->indirdb]
                ->select($sentenceg->sql[0])
                ->where(vsprintf($sentenceg->sql[2],$key_array))
                ->get($sentenceg->sql[1]);

            if ($sentenceg->multiple) {
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
                    $this->indirectLookupCache[$key] = '';
            }
        }

        $mo->features[$feat] = $this->indirectLookupCache[$key];
            
        
        

//        switch (count($this->indirectLookupCache[$key])) {
//          case 0:
//                $mo->features[$feat] = '';
//                break;
// 
//          case 1:
//                $mo->features[$feat] = $this->indirectLookupCache[$key][0];
//                break;
// 
//          default:
//                $mo->features[$feat] = $this->indirectLookupCache[$key];
//                break;
//        }
    }


    /// Fetches the names of all the features for the Emdros object at a particular level
    /// (word/phrase/clause etc.), and returns it as an array.
    /// @param $gl The sentencegrammar information for the relevant level
    /// @param $all Each feature is stored in this array.
    /// @param $indirect Maps features that are to be retrieved outside Emdros to sentencegrammar information
    private static function getOneLeveFeatureString($gl, array &$all, string &$subtype, array &$subtypeall, array &$indirect) {
        if (isset($gl->items)) {
            foreach ($gl->items as $it)
                self::getOneLeveFeatureString($it,$all,$subtype,$subtypeall,$indirect);
        }
        else {
            if (isset($gl->name)) {
                if (strstr($gl->name, ':')===false) {
                    if (isset($gl->sqlargs)) {
                        $indirect[$gl->name] = $gl;
                        foreach($gl->sqlargs as $n)
                            $all[$n] = $n;  // Uses an array to emulate a set
                    }
                    else
                        $all[$gl->name] = $gl->name;  // Uses an array to emulate a set
                }
                else {
                    list($subt, $name) = explode(':', $gl->name);
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
        self::getOneLeveFeatureString($dbi->sentencegrammar[$grammarListIx], $all, $subtype, $subtypeall, $indirect);
        return implode(',',$all);
    }

    /// Creates a Dictionary object. This constructor reads information from the relevant
    /// Emdros database and builds the text component hierarchy.
    /// @param $params['mset'] The monads that describe the sentence.
    /// @param $params['inQuiz'] Is this part of a quiz (in which case there is only one top-level object)?
    function __construct(array $params) {
        $msets = $params['msets'];
        $inQuiz = $params['inQuiz'];
        $showIcons = $params['showIcons'];

        $CI =& get_instance();
        $CI->load->library('picdb');

        $dbinfo = $CI->db_config->dbinfo; // Cached here

        $number_sets = count($msets);

        //$this->inQuiz = $inQuiz;

        $this->maxLevels = count($dbinfo->sentencegrammar);
        ++$this->maxLevels; // We need to add an artificial top-level object (the patriarch)

        // If this is a text display, we must extend the msets to cover an entire sentence
		if (!$inQuiz) {
            assert("$number_sets==1");

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
            $mset_union->addSetNoConsolidate($mset);

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
                        . " [$subtype GET " . implode($subtypeAllFeat, ',') . "]"
                        . "] GOqxqxqx\n";
                else
                    $command .= "SELECT ALL OBJECTS IN $mset WHERE [$sg->objType "
                        . (empty($allFeat) ? '' : "GET $allFeat")
                        . "] GOqxqxqx\n";
            }
        }

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
                            foreach ($indirect as $feat => $sentenceg)
                                $this->indirectLookup($feat, $mo, $sentenceg);
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
 
		
		// Set book, chapter, and verse information on each SingleMonadObject
		// TODO: We might use simpler code for the inQuiz case, because we only need the reference for the first word.

        $uni_count = count($dbinfo->universeHierarchy);
        for ($unix=0; $unix<$uni_count; ++$unix) {
            $last_uni_level = $unix===$uni_count-1;

            $sh = $emdros_data[$mqlresult_index++]->get_sheaf();
			assert($sh->number_of_straws()<=1);
 
			if ($sh->number_of_straws()==1) {
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
            $this->singleMonads[$thisMo->get_mo()->get_monadset()->getSingleInteger()] = $thisMo;
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
 
 
    /// Retrieves the text to display for a particular monad number. This consists of the text
	/// proper plus a (possibly empty) suffix.
	/// @param $monad The monad number.
	/// @return The text to display for the specified monad number.
    public function getVisual($monad) {
        $smo = $this->singleMonads[$monad];
        return $smo->get_text() . $smo->get_suffix();
    }
 
}
