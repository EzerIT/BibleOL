<?php

class Mod_askemdros extends CI_Model {
    public $use_tooltip;
    public $font_selection;

    // The following default values are overwritten if no error occurs
    public $book_title = 'Error';
    public $dictionaries_json = 'null';
    public $quiz_data_json = 'null';

    public $dbinfo_json = 'null';
    public $l10n_json = 'null';
    public $typeinfo_json = 'null';

    public $setup_db;
    public $setup_prop;

    public $decoded_3et;
    public $universe;

    private $contents;

    public function __construct() {
        parent::__construct();

        $query = $this->db->where('user_id',$this->mod_users->my_id())->get('userconfig');
		if ($row = $query->row())
			$this->use_tooltip = $row->usetooltip;
		else
			$this->use_tooltip = 0;

        $query = $this->db->from('alphabet')->join('font','font.alphabet_id=alphabet.id')
            ->where('user_id',$this->mod_users->my_id())->get();

        $this->font_selection = $query->result();

        if (empty($this->font_selection)) { // User has not specified a font
            $query = $this->db->from('alphabet')->join('font','font.alphabet_id=alphabet.id')
                ->where('user_id',0)->get(); // Get font from default user (user_id=0)
            $this->font_selection = $query->result();
        }
    }

    public function setup(string $db, string $prop) {
        $this->setup_db = $db;
        $this->setup_prop = $prop;
        $this->load->helper(array('xmlhandler','sheaf'));
        $this->load->library('db_config');
        $this->db_config->init_config($db,$prop,$this->language);
        $this->load->driver('mql',array('db' => $this->db_config->emdros_db,
                                        'driver' => $this->config->item('mql_driver')));
    }

    public function fullUniverse() {
        $ms = new OlMonadSet();

        $emdros_data = $this->mql->exec("SELECT MIN_M GOqxqxqx\nSELECT MAX_M GOqxqxqx\n");
        $ms->addOne($emdros_data[0]->get_table()->get_cell(0,0),
                    $emdros_data[1]->get_table()->get_cell(0,0));
        return $ms;
    }
        
    public function getMonadsAtLevel(OlMonadSet $ms, int $hier_level) {
        $hier = $this->db_config->dbinfo->universeHierarchy[$hier_level];

        $emdros_data = $this->mql->exec("SELECT ALL OBJECTS IN $ms WHERE [$hier->type GET $hier->feat] GOqxqxqx");
        
        $sh = $emdros_data[0]->get_sheaf();
        
        $res = array();

        foreach ($emdros_data[0]->get_sheaf()->get_straws() as $str) {
            $matchob = $str->get_first_matched_object();
            $res[$matchob->get_feature($hier->feat)] = $matchob->get_monadset();
        }

        return $res;
    }


    private function parsePath(array $paths, array $use_selection = null) {
        if (is_null($use_selection)) {
            // Use universe specified in quiz file
            $path_count = count($paths);

            $command = '';
            $ms = new OlMonadSet();

            foreach ($paths as $p) {
                if (empty($p)) // $path = array('')
                    return self::fullUniverse();
            
                $split_p = explode(':',$p);
                $num = count($split_p);

                assert($num<=count($this->db_config->dbinfo->universeHierarchy));
    
                $command .= "SELECT ALL OBJECTS WHERE ";

                for ($i=0; $i<$num; ++$i)
                    $command .= "[{$this->db_config->dbinfo->universeHierarchy[$i]->type} {$this->db_config->dbinfo->universeHierarchy[$i]->type}={$split_p[$i]} ";

                for ($i=0; $i<$num; ++$i)
                    $command .= ']';

                $command .= " GOqxqxqx\n";
            }

            $emdros_data = $this->mql->exec($command); 

            for ($i=0; $i<$path_count; ++$i) {
                $sh = $emdros_data[$i]->get_sheaf();
                if ($sh->isEmpty())
                    continue; // This can happen if the universe specification contains elements not in the corpus

                // Find lowest level of information (i.e., verse if present, else chapter if present, else book)
                for ( ; $sh!=null; $sh=$sh->get_first_straw()->get_first_matched_object()->get_sheaf())
                    $last_non_null_sheaf = $sh;

                $ms->addSet($last_non_null_sheaf->get_first_straw()->get_first_matched_object()->get_monadset());
            }
        }
        else {
            // Use universe specified by user
            $ms = new OlMonadSet();
            
            foreach ($use_selection as $mysel) {
                $split_mysel = explode('/',$mysel);
                $num = count($split_mysel);
                assert($num==3 && is_numeric($split_mysel[1]) && is_numeric($split_mysel[2]));
                
                $ms->addOne(intval($split_mysel[1]),intval($split_mysel[2]));
            }
        }
        return $ms;
    }

    // Strips the monads from user-specified universe items
    private static function strip_monads(array $use_selection) {
        if (is_null($use_selection))
            return null;

        $stripped_selection = array();
        foreach ($use_selection as $path_with_monads)
            $stripped_selection[] = preg_replace('|/.*|', '', $path_with_monads);
        return $stripped_selection;
    }


    private function parseQuiz(string $filename, array $use_selection = null) {
        $this->parseQuizBasic($filename);

        $sentenceSelector = isset($this->decoded_3et->sentenceSelection->mql) 
            ? $this->decoded_3et->sentenceSelection->mql
            : "[{$this->decoded_3et->sentenceSelection->object} NORETRIEVE {$this->decoded_3et->sentenceSelection->featHand}]";

        $qoSelector = isset($this->decoded_3et->quizObjectSelection->mql) 
            ? $this->decoded_3et->quizObjectSelection->mql
            : $this->decoded_3et->quizObjectSelection->featHand->__toString();

        // A full universe path looks like this: <path></path>
        // Depending on the XML parser used, this may either result in a path which is either array() or array('').
        // The following statement streamlines this as array('').
        if (count($this->decoded_3et->selectedPaths)===0)
            $this->decoded_3et->selectedPaths = array('');

        if ($this->mod_users->is_logged_in()) {
            $this->load->model('mod_statistics');
            $templid = $this->mod_statistics->newQuizTemplate($this->mod_users->my_id(), $filename, $this->contents, $this->decoded_3et->database,
                                                              $this->decoded_3et->properties, $this->decoded_3et->quizObjectSelection->object);
            if (is_null($use_selection) || $this->decoded_3et->fixedquestions>0)
                $quizid = $this->mod_statistics->startQuiz($this->mod_users->my_id(), $templid,
                                                           $this->decoded_3et->selectedPaths);
            else
                $quizid = $this->mod_statistics->startQuiz($this->mod_users->my_id(), $templid, 
                                                           self::strip_monads($use_selection));
        }
        else
            $quizid = -1;

        $this->load->library('quiz_data',array('quizid' => $quizid,
                                               'universe' => self::parsePath($this->decoded_3et->selectedPaths, $this->decoded_3et->fixedquestions>0 ? null : $use_selection),
                                               'senSelect' => $sentenceSelector,
                                               'qoSelect' => $qoSelector,
                                               'desc' => $this->decoded_3et->desc,
                                               'maylocate' => $this->decoded_3et->maylocate,
                                               'sentbefore' => $this->decoded_3et->sentbefore,
                                               'sentafter' => $this->decoded_3et->sentafter,
                                               'fixedquestions' => $this->decoded_3et->fixedquestions,
                                               'show_features' => $this->decoded_3et->quizFeatures->showFeatures,
                                               'request_features' => $this->decoded_3et->quizFeatures->requestFeatures,
                                               'dontshow_features' => $this->decoded_3et->quizFeatures->dontShowFeatures,
                                               'dontshow_objects' => $this->decoded_3et->quizFeatures->dontShowObjects,
                                               'oType' => $this->decoded_3et->quizObjectSelection->object));
    }

    function convert_ETCBC4_v7(string $filename) {
        $this->load->helper(array('file','xmlhandler','quiztemplate','convert_wivu'));

        if (!is_file($filename) || !is_readable($filename))
            throw new DataException(sprintf($this->lang->line('cannot_open_file'), $filename));

        $this->contents = file_get_contents($filename);

        if ($this->contents === false)
            throw new DataException(sprintf($this->lang->line('cannot_open_file'), $filename));

        $this->decoded_3et = harvest($this->contents);
        $conv = Convert_etcbc4_v7::convert($this->decoded_3et, $filename);

        $x = new stdClass;
        $x->subsetOf = false;

        $res = Template::writeAsXml($conv, $x);
        print $res;
    }

    public function decodeQuiz(string $filename) {
        $this->load->helper(array('file','xmlhandler','quiztemplate'));

        if (!is_file($filename) || !is_readable($filename))
            throw new DataException(sprintf($this->lang->line('cannot_open_file'), $filename));

        $this->contents = file_get_contents($filename);

        if ($this->contents === false)
            throw new DataException(sprintf($this->lang->line('cannot_open_file'), $filename));

        $this->decoded_3et = harvest($this->contents);
        return $this->decoded_3et;
    }
    
    private function parseQuizBasic(string $filename) {
        $this->load->helper(array('file','xmlhandler','quiztemplate'));

        if (!is_file($filename) || !is_readable($filename))
            throw new DataException(sprintf($this->lang->line('cannot_open_file'), $filename));

        $this->contents = file_get_contents($filename);

        if ($this->contents === false)
            throw new DataException(sprintf($this->lang->line('cannot_open_file'), $filename));

        $this->decoded_3et = harvest($this->contents);

        $this->setup($this->decoded_3et->database,$this->decoded_3et->properties);

        // Make sure glosses are not visible if a gloss language is requested
        if ($this->decoded_3et->quizObjectSelection->object===$this->db_config->dbinfo->objHasSurface) {
            $fsetting = $this->db_config->dbinfo->objectSettings->{$this->db_config->dbinfo->objHasSurface}->featuresetting;

            // Store all gloss features in $gloss_features
            $gloss_features = array();
            foreach (get_object_vars($fsetting) as $featname => $featval) {
                if (!empty($featval->isGloss)) {
                    $gloss_features[$featname] = true;
                }
            }

            // Set $gloss_features[] to false for request, display, and "don't show" features.
            // The remainder will be the "don't care" features.
            $requestGlossFound = false;
            foreach ($this->decoded_3et->quizFeatures->requestFeatures as $f) {
                if (!empty($fsetting->{$f->name}->isGloss)) {
                    $requestGlossFound = true;
                    $gloss_features[$f->name] = false;
                }
            }

            if ($requestGlossFound) {
                foreach ($this->decoded_3et->quizFeatures->showFeatures as $f)
                    if (!empty($fsetting->$f->isGloss))
                        $gloss_features[$f] = false;

                foreach ($this->decoded_3et->quizFeatures->dontShowFeatures as $f)
                    if (!empty($fsetting->$f->isGloss))
                        $gloss_features[$f] = false;

                // Mark the remaining gloss features as "don't show"
                foreach ($gloss_features as $f => $is_dontCare)
                    if ($is_dontCare)
                        $this->decoded_3et->quizFeatures->dontShowFeatures[] = $f;
            }
        }


        // A full universe path looks like this: <path></path>
        // Depending on the XML parser used, this may either result in a path which is either array() or array('').
        // The following statement streamlines this as array('').
        if (count($this->decoded_3et->selectedPaths)===0)
            $this->universe = array('');
        else
            $this->universe = $this->decoded_3et->selectedPaths;
    }


    public function show_quiz(int $number_of_quizzes, array $use_selection = null) {
        try {
            $this->load->library('db_config');

            self::parseQuiz($this->mod_quizpath->get_absolute(), $use_selection);
            if ($this->quiz_data->getCandidateSheaf())
                $numCandidates = $this->quiz_data->getNumberOfCandidates();
            else
                throw new DataException($this->lang->line('no_sentences_found'));

            if ($this->quiz_data->fixedquestions>0)
                $number_of_quizzes = $this->quiz_data->fixedquestions;
            $this->dictionaries_json = json_encode($this->quiz_data->getNextCandidate($number_of_quizzes));
            $this->quiz_data_json = json_encode($this->quiz_data);

            $this->dbinfo_json = $this->db_config->dbinfo_json;
            $this->l10n_json = $this->db_config->l10n_json;
            $this->typeinfo_json = $this->db_config->typeinfo_json;
        }
        catch (MqlException $e) {
            if (!empty($e->db_error)) 
                $error = $this->lang->line('mql_database_error_colon') . "\n$e->db_error";
            else
                $error = $this->lang->line('mql_compiler_error_colon') . "\n$e->compiler_error";

            $error = str_replace("\n","<br>",htmlspecialchars($error));
            throw new DataException($error);
        }
    }

    public function edit_quiz() {
        try {
            $this->load->library('db_config');

            self::parseQuizBasic($this->mod_quizpath->get_absolute());

            $this->dbinfo_json = $this->db_config->dbinfo_json;
            $this->l10n_json = $this->db_config->l10n_json;
            $this->typeinfo_json = $this->db_config->typeinfo_json;
        }
        catch (MqlException $e) { // TODO: Are any MQL commands executed?
            if (!empty($e->db_error)) 
                $error = $this->lang->line('mql_database_error_colon') . "\n$e->db_error";
            else
                $error = $this->lang->line('mql_compiler_error_colon') . "\n$e->compiler_error";

            $error = str_replace("\n","<br>",htmlspecialchars($error));
            throw new DataException($error);
        }
    }

    public function new_quiz(string $db) {
        try {
            $this->load->library('db_config');
            $this->setup($db, $db);
            
            $this->dbinfo_json = $this->db_config->dbinfo_json;
            $this->l10n_json = $this->db_config->l10n_json;
            $this->typeinfo_json = $this->db_config->typeinfo_json;

            return sprintf('{"desc":"",'
                           . '"database":"%s",'
                           . '"properties":"%s",'
                           . '"selectedPaths":[],'
                           . '"sentenceSelection":{"object":"%s","mql":null,"featHand":{"vhand":[]},"useForQo":true},'
                           . '"quizObjectSelection":{"object":"word","mql":null,"featHand":{"vhand":[]},"useForQo":true},'
                           . '"quizFeatures":{"showFeatures":[],"requestFeatures":[],"dontShowFeatures":[],"dontShowObjects":[]},'
                           . '"maylocate":true,'
                           . '"sentbefore":0,'
                           . '"sentafter":0,'
                           . '"fixedquestions":0'
                           . '}',
                           $this->db_config->dbinfo->databaseName, $db, $this->db_config->dbinfo->objHasSurface);
        }
        catch (MqlException $e) { // TODO: Are any MQL commands executed?
            if (!empty($e->db_error)) 
                $error = $this->lang->line('mql_database_error_colon') . "\n$e->db_error";
            else
                $error = $this->lang->line('mql_compiler_error_colon') . "\n$e->compiler_error";

            $error = str_replace("\n","<br>",htmlspecialchars($error));
            throw new DataException($error);
        }
    }

    public function save_quiz(stdClass $quizdata) {
        $this->setup($quizdata->database,$quizdata->properties);

        $this->load->helper(array('file','quiztemplate'));

        $res = Template::writeAsXml($quizdata, $this->db_config->dbinfo);

        if (!write_file($this->mod_quizpath->get_absolute(), $res))
            throw new DataException($this->lang->line('cannot_write_to_quiz_file'));
    }

    public function get_quiz_universe() {
        $this->load->library('db_config');

        self::parseQuizBasic($this->mod_quizpath->get_absolute());
            
        // Do we need this?
        $this->dbinfo_json = $this->db_config->dbinfo_json;
        $this->l10n_json = $this->db_config->l10n_json;
        $this->typeinfo_json = $this->db_config->typeinfo_json;
    }

    private static $remove_databases = array("WIVU", "WIVU-a", "WIVU-b", "Tisch");

    private static $sort_order = array("ETCBC4" => 1,
                                       "ETCBC4-translit" => 2,
                                       "nestle1904" => 3);

    public static function comp_books(array $a, array $b) {
        return self::$sort_order[$a['name']] > self::$sort_order[$b['name']];
    }

    public function db_and_books() {
        $this->load->library('db_config');
        $db_books = array();
        foreach ($this->db_config->allfiles_enumerate as $name => $dbf) {
            if (in_array($name, self::$remove_databases))
                continue;

            $this->db_config->init_config_dbf($dbf, $this->language);
            $loc = json_decode($this->db_config->l10n_json);
            $db_books[] = array('name'=>$name,
                                'loc_desc'=>$loc->dbdescription,
                                'loc_copyright'=>isset($loc->dbcopyright) ? $loc->dbcopyright : null,
                                'loc_books'=>$loc->universe->book,
                                'order'=>$this->db_config->bookorder);
        }
        usort($db_books, 'Mod_askemdros::comp_books');
        return $db_books;
    }

    // TODO: This works only for references containing book, chapter and verse range
    // $vfrom==0 means use entire chapter
    private function find_monads(string $book, int $chapter, int $vfrom, int $vto) {
        if ($vfrom==0)
            // Fetch entire chapter
            $emdros_data = $this->mql->exec("SELECT ALL OBJECTS WHERE [chapter book=$book AND chapter=$chapter] GOqxqxqx");
        else
            $emdros_data = $this->mql->exec("SELECT ALL OBJECTS WHERE [verse book=$book AND chapter=$chapter "
                                            . "AND verse>=$vfrom AND verse<=$vto] GOqxqxqx");
  
        $sh = $emdros_data[0]->get_sheaf();
        if ($sh->isEmpty())
            throw new DataException($this->lang->line('no_text_found'));

        $mset = new OlMonadSet();

        foreach ($sh->get_straws() as $str)
            foreach ($str->get_matched_objects() as $mo)
            $mset->addSet($mo->get_monadset());

        return $mset;
    }

    public function show_text(string $db, string $book, int $chapter, int $vfrom, int $vto, bool $showIcons) {
        try {
            $this->setup($db,$db);

            $passage = $this->find_monads($book,$chapter,$vfrom,$vto);
            
            $this->load->library('dictionary', array('msets' => array($passage), 'inQuiz' => false, 'showIcons' => $showIcons));
            $this->book_title = $this->dictionary->get_book_title();
            $this->dictionaries_json = json_encode($this->dictionary);

            $this->dbinfo_json = $this->db_config->dbinfo_json;
            $this->l10n_json = $this->db_config->l10n_json;
            $this->typeinfo_json = $this->db_config->typeinfo_json;
        }
        catch (MqlException $e) {
            if (!empty($e->db_error)) 
                $error = $this->lang->line('mql_database_error_colon') . "\n$e->db_error";
            else
                $error = $this->lang->line('mql_compiler_error_colon') . "\n$e->compiler_error";

            $error = str_replace("\n","<br>",htmlspecialchars($error));
            throw new DataException($error);
        }
    }


    public function shebanq_link(string $db, string $book, int $chapter) {
        if ($db==='ETCBC4' || $db==='ETCBC4-translit')
            return "http://shebanq.ancient-data.org/hebrew/text?book=$book&amp;chapter=$chapter&mr=m";
        else
            return null;
    }
  }