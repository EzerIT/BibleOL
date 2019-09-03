<?php

function getFeatureSetting(string $otype, string $feature) {
    $CI =& get_instance();
    $dbinfo = $CI->db_config->dbinfo; // Cached here

    if ($feature==='visual') {
        $otype = $dbinfo->objHasSurface;
        $feature = $dbinfo->surfaceFeature;
    }
    return $dbinfo->objectSettings->$otype->featuresetting->$feature;
}

function getFeatureHideWord(string $otype, string $feature) {
    $gfs = getFeatureSetting($otype, $feature);
    return !empty($gfs->hideWord);
}


class ExtendedQuizFeatures {
	public $showFeatures; // Vector
	public $requestFeatures; //Vector<Pair<DoubleName,Boolean>> 
    public $dontShowFeatures; //Vector<String> 
    public $dontShowObjects; //Vector<map<string,string>> 
    public $objectType;
	public $dontShow;
    public $useVirtualKeyboard;
    public $useDropdown = false;
    public $additionalFeatures;
    public $allFeatures; // All showfeatures and requestfeatures (excluding 'visual') and additionalFeatures
    public $pseudoFeatures;

    function __construct(array $sf, array $rf, array $dsf, array $dso, string $oType) {
        $CI =& get_instance();
        $dbinfo = $CI->db_config->dbinfo; // Cached here

        $this->showFeatures = $sf;
        $this->requestFeatures = $rf;
        $this->dontShowFeatures = $dsf;
        $this->dontShowObjects = $dso;
        $this->objectType = $oType;
        $this->dontShow = false;
        $this->useVirtualKeyboard = false;

        $all = array(); // In this array the index will be equal to the value, thus emulating a set
        $this->pseudoFeatures = array();

        foreach ($sf as $f) {
            $gfs = getFeatureSetting($oType, $f);
            if (isset($gfs->sqlargs)) {
                $this->pseudoFeatures[] = $f;
                foreach ($gfs->sqlargs as $sqlarg)
                    $all[$sqlarg] = $sqlarg;
            }
            else if ($f!=='visual')
                $all[$f] = $f;
        }

        foreach ($rf as $f) {
            $gfs = getFeatureSetting($oType, $f->name);
            if (isset($gfs->sqlargs)) {
                $this->pseudoFeatures[] = $f->name;
                foreach ($gfs->sqlargs as $sqlarg)
                    $all[$sqlarg] = $sqlarg;
            }
            else if ($f->name!=='visual')
                $all[$f->name] = $f->name;

            if (!empty($gfs->hideWord))
                $this->dontShow = true;
            if (!empty($gfs->foreignText))
                $this->useVirtualKeyboard = true;

            if ($f->usedropdown)
                $this->useDropdown = true;
        }

        if (isset($dbinfo->objectSettings->$oType->additionalfeatures)) {
            $this->additionalFeatures = $dbinfo->objectSettings->$oType->additionalfeatures;
            foreach ($this->additionalFeatures as $f)
                $all[$f] = $f;
        }

        $this->allFeatures = implode(',', $all);
    }
}

/**
 * Contains all data and methods required to uniquely identify and manipulate 
 * the candidate sentences in a quiz.
 */
class Quiz_data {
	private $universe;
	private $mqlSentenceSelection;
	private $oType;
	private $mqlQuizObjectSelection;
    public $quizid;
	public $quizFeatures;
    public $desc;
    public $maylocate;
	
	private $mainSheaf; // Contains all candidate sentences
	private $order; // Vector<Integer>
	private $nextCandidate; // Index into m_order of next unused sentence
	private $numberOfCandidates;

    private $CI;


	// Data about retrieved questions:
    public $monad2Id; /* Map<Integer, Integer>  *//* Maps monads to the question objects (assuming that a monad is not part of multiple question
									   * objects. When would that happen? If this is a problem, replace this type by MultiMap (which
									   * you must write yourself).
									   */
    public $id2FeatVal; /*Map<Integer, Map<String,String>> */ // Maps the question objects to their feature=>value map


    private function normalize(string $s) {
        if ($this->CI->db_config->dbinfo->charSet==='greek') {
            if (extension_loaded('intl'))
                $s = Normalizer::normalize($s, Normalizer::FORM_C);

            // Implementation note: The conversion TONOS => OXIA happens here rather when the quiz
            // is created (in TypeScript), because this isolates the modifications to a single place
            // (namely here). Otherwise the modification should be made in each value input field
            // and in the MQL input field.

            return str_replace(
                // Conversion to accented charcters used in db
                array(//"\x3b",           // \u003b - SEMICOLON
                      //"\xc2\xb7",       // \u00b7 - MIDDLE DOT
                      "\xce\xac",       // \u03ac - GREEK SMALL LETTER ALPHA WITH TONOS
                      "\xce\xad",       // \u03ad - GREEK SMALL LETTER EPSILON WITH TONOS
                      "\xce\xae",       // \u03ae - GREEK SMALL LETTER ETA WITH TONOS
                      "\xce\xaf",       // \u03af - GREEK SMALL LETTER IOTA WITH TONOS
                      "\xcf\x8c",       // \u03cc - GREEK SMALL LETTER OMICRON WITH TONOS
                      "\xcf\x8d",       // \u03cd - GREEK SMALL LETTER UPSILON WITH TONOS
                      "\xcf\x8e",       // \u03ce - GREEK SMALL LETTER OMEGA WITH TONOS
                      "\xce\x90",       // \u0390 - GREEK SMALL LETTER IOTA WITH DIALYTIKA AND TONOS
                      "\xce\xb0",       // \u03b0 - GREEK SMALL LETTER UPSILON WITH DIALYTIKA AND TONOS
                    ),
                array(//"\xcd\xbe",       // \u037e - GREEK QUESTION MARK
                      //"\xce\x87",       // \u0387 - GREEK ANO TELEIA
                      "\xe1\xbd\xb1",   // \u1f71 - GREEK SMALL LETTER ALPHA WITH OXIA
					  "\xe1\xbd\xb3",	// \u1f73 - GREEK SMALL LETTER EPSILON WITH OXIA
					  "\xe1\xbd\xb5",	// \u1f75 - GREEK SMALL LETTER ETA WITH OXIA
					  "\xe1\xbd\xb7",	// \u1f77 - GREEK SMALL LETTER IOTA WITH OXIA
					  "\xe1\xbd\xb9",	// \u1f79 - GREEK SMALL LETTER OMICRON WITH OXIA
					  "\xe1\xbd\xbb",	// \u1f7b - GREEK SMALL LETTER UPSILON WITH OXIA
					  "\xe1\xbd\xbd",	// \u1f7d - GREEK SMALL LETTER OMEGA WITH OXIA
					  "\xe1\xbf\x93",	// \u1fd3 - GREEK SMALL LETTER IOTA WITH DIALYTIKA AND OXIA
					  "\xe1\xbf\xa3",	// \u1fe3 - GREEK SMALL LETTER UPSILON WITH DIALYTIKA AND OXIA
                    ),
                $s);
        }
        else
            return $s;
    }
	
	function __construct(array $params) {
        $this->CI =& get_instance();

	    $this->monad2Id   = array();
	    $this->id2FeatVal = array();

        $this->quizid = $params['quizid'];
		$this->universe = $params['universe'];
		$this->mqlSentenceSelection = $this->normalize($params['senSelect']);
		$this->oType = $params['oType']; // Cached here
		$this->mqlQuizObjectSelection = $this->normalize($params['qoSelect']);
        $this->quizFeatures = new ExtendedQuizFeatures($params['show_features'],$params['request_features'],$params['dontshow_features'],$params['dontshow_objects'],$params['oType']);
        $this->desc = $params['desc'];
        $this->maylocate = $params['maylocate'];
		
		$this->nextCandidate = 0;
	}

    private function fetchBookLimit(OlMonadSet $ms) {
        $emdat = $this->CI->mql->exec("GET OBJECTS HAVING MONADS IN {$ms} [{$this->CI->db_config->dbinfo->universeHierarchy[0]->type}]");

        return $emdat[0]->get_sheaf()->get_straws()[0]->get_matched_objects()[0]->get_monadset();
    }
    
	public function getNextCandidate(int $request_number) {
        assert('isset($this->mainSheaf)');

        if ($request_number > $this->numberOfCandidates-$this->nextCandidate)
            $request_number = $this->numberOfCandidates-$this->nextCandidate;

        if ($request_number <= 0)
            return null;
            
			
        // ********************************************************
        // Populate the dictionary with the new candidate sentences
        // ********************************************************

        $msets = array();
        $extended_msets = array();
        $command = '';
        for ($i=0; $i<$request_number; ++$i) {
            $ms = $this->mainSheaf->get_monadset();
            $current_ms = $ms[$this->order[$this->nextCandidate++]];
            $msets[] = clone $current_ms;

            $booklimits = $this->fetchBookLimit($current_ms);
            // We assume that all of $current_ms is withing the same book

//			$befset = new OlMonadSet();
			if ($current_ms->low()>$booklimits->low()) { // TODO: Check against start of book instead of against 1
//				$befset->addOne($current_ms->low()-1,$current_ms->low()-1);
				$befset = $current_ms->low()-1;
				$emdat = $this->CI->mql->exec("GET OBJECTS HAVING MONADS IN {{$befset}} [{$this->CI->db_config->dbinfo->granularity}] GOqxqxqx");
				$current_ms->addSet($emdat[0]->get_sheaf()->get_straws()[0]->get_matched_objects()[0]->get_monadset());
			}

//			$aftset = new OlMonadSet();
			if ($current_ms->high()<$booklimits->high()) { // TODO: Check against end of book instead of against 99999999
//				$aftset->addOne($current_ms->high()+1,$current_ms->high()+1);
				$aftset = $current_ms->high()+1;
				$emdat = $this->CI->mql->exec("GET OBJECTS HAVING MONADS IN {{$aftset}} [{$this->CI->db_config->dbinfo->granularity}] GOqxqxqx");
				$current_ms->addSet($emdat[0]->get_sheaf()->get_straws()[0]->get_matched_objects()[0]->get_monadset());
			}
            $extended_msets[] = $current_ms;
        }

        $this->CI->load->library('dictionary', array('msets' => $extended_msets, 'msets_quiz' => $msets, 'inQuiz' => true, 'showIcons' => false));

        $mset_union = new OlMonadSet();
        foreach ($msets as $mset)
            $mset_union->addSetNoConsolidate($mset);

        // **************************************************************************
        // Populate the $this->monad2Id and $this->id2FeatValue with data from the quiz objects
        // **************************************************************************

        $this->CI->load->library('Suggest_answers');

        // The "\n" in the following MQL commands is required if the user entered text that ends with a comment
        if (empty($this->quizFeatures->allFeatures))
            $command .= "SELECT All OBJECTS IN $mset_union WHERE [$this->oType $this->mqlQuizObjectSelection\n] GOqxqxqx\n";
        else
            $command .= "SELECT All OBJECTS IN $mset_union WHERE [$this->oType $this->mqlQuizObjectSelection\n GET {$this->quizFeatures->allFeatures}] GOqxqxqx\n";

        $emdros_data = $this->CI->mql->exec($command);

        $sh = $emdros_data[0]->get_sheaf();
 
        foreach ($sh->get_straws() as $str) {
            foreach ($str->get_matched_objects() as $mo) {
                $visual = '';
                $id_d = $mo->get_id_d();

                foreach ($mo->get_monadset() as $monad) {
                    $this->monad2Id[$monad] = $id_d;
                    $visual .= $this->CI->dictionary->getVisual($monad);
                }

                foreach ($this->quizFeatures->pseudoFeatures as $psf)
                    $this->CI->dictionary->indirectLookup($psf, $mo,
                                                          getFeatureSetting($this->oType, $psf));

                $mo->set_feature('visual', trim($visual));// For simplicity, always add "visual" pseudo-feature

                $this->id2FeatVal[$id_d] = $mo->get_features();
                if ($this->quizFeatures->useDropdown) {
                    foreach ($this->quizFeatures->requestFeatures as $rf) {
                        if ($rf->usedropdown) {
                            $featuresetting = getFeatureSetting($this->oType, $rf->name);

                            $this->id2FeatVal[$id_d][$rf->name . '!suggest!'] =
                                Suggest_answers::findSuggestions($featuresetting->alternateshowrequestDb,
                                                                 $featuresetting->alternateshowrequestSql,
                                                                 $this->id2FeatVal[$id_d][$this->quizFeatures->additionalFeatures[0]], // TODO: For now there is never more than one additional feature
                                                                 $this->id2FeatVal[$id_d][$rf->name],
                                                                 2,10);
                        }
                    }
                }
            }
        }
		return $this->CI->dictionary;
	}
	
	public function getCandidateSheaf() {
        $quick_emdros_data = $this->CI->mql->exec("SELECT ALL OBJECTS IN $this->universe "
                                                  . "WHERE [{$this->CI->db_config->dbinfo->granularity} $this->mqlSentenceSelection\n] GOqxqxqx", true);

		$this->mainSheaf = $quick_emdros_data[0]->get_sheaf();

        if (!isset($this->mainSheaf) || !$this->mainSheaf->has_monadset()) {
            $this->numberOfCandidates = 0;
            return false;
        }

		$this->numberOfCandidates = count($this->mainSheaf->get_monadset());
		if ($this->numberOfCandidates==0)
			return false;

		$this->order = array();
		for ($i=0; $i<$this->numberOfCandidates; ++$i)
			$this->order[] = $i;
		shuffle($this->order); // Randomises $this->order 

		return true;
	}

    function getNumberOfCandidates() {
        return $this->numberOfCandidates;
    }

}

