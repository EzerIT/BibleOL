<?php

require_once('include/typeinfo.inc.php');

/// This class holds pathnames for files pertaining to a single Emdros database.
class database_file {
    public $emdros_db;  ///< The name of the Emdros database file
    public $dbinfo;     ///< The name of the file containing database information (dbinfo) for the Emdros database
    public $propertiesName; 
    public $typeinfo;   ///< The name of the file containing type information for the Emdros database
    public $bookorder;  ///< The order of the books in the Emdros database
    private $subsetOf;  ///< The name of an Emdros database, if any, that is a superset of this one

    /// Creates a database_file from information in a dbinfo JSON file.
    /// @param $dbinfo An anonymous structure containing database information.
    public function __construct($dbinfo) {
        $db = $dbinfo->databaseName;
        $pr = $dbinfo->propertiesName;

        $this->propertiesName = $pr;
        $this->subsetOf = $dbinfo->subsetOf;
        $this->emdros_db = "db/$db";
        $this->dbinfo = "db/$pr.db.json";
        $this->typeinfo = "db/$db.typeinfo.json";
        $this->bookorder = "db/$db.bookorder";
    }

    /// Fetches the name of properties files of a superset database, if any.
    /// @return The name of properties files of a superset database, if one exists; otherwise returns null.
    public function getSuperProp() {
        if (is_null($this->subsetOf))
            return null;
        return $this->subsetOf->properties;
    }
  }


/// This class holds information about all Emdros databases in general and one database in
/// particular. When this class is instantiated the $allfiles and $allfiles_enumerate fields are set
/// to contain information about all databases. Then, when the user selects a particular database,
/// the remaining fields are initalizes to reflect the users choice through calls to init_config()
/// or init_config_dbf().
class Db_config {
    private $allfiles;          ///< An associative array mapping all database properties names to database_file objects
    public $allfiles_enumerate; ///< The databases to show when selecting text

    public $dbinfo_json;        ///< A JSON string describing the database information (dbinfo) structure of the selected Emdros database
	public $dbinfo;             ///< The database information structure of the selected Emdros database
	public $l10n_json;          ///< A JSON string representing localization for the selected Emdros database
	public $typeinfo_json;      ///< A JSON string representing type information for the selected Emdros database
	public $typeinfo;           ///< Type information for this Emdros database
	public $emdros_db;          ///< The name of the Emdros database file!
    private $src_lang;			///< Array of names of source language lexicons (Hebrew, Aramaic, greek)
	private $glosslang;			///< Array of gloss languages
    
    /// Utility function which checks if one string ends with another string.
    /// @param $haystack String in which to search.
    /// @param $needle String to search for.
    /// @return True if $haystack ends with $needle, false otherwise.
    private static function endswith(string $haystack, string $needle) {
        return substr($haystack, -strlen($needle))===$needle;
    }

    /// Utility function which checks if one string starts with another string.
    /// @param $haystack String in which to search.
    /// @param $needle String to search for.
    /// @return True if $haystack starts with $needle, false otherwise.
    private static function startswith(string $haystack, string $needle) {
        return strncmp($haystack, $needle, strlen($needle))==0;
    }

    /// Creates a Db_config object. This constructor loads the $allfiles and $allfiles_enumerate
    /// fields with information about all available Emdros databases.
    public function __construct() {
        $CI =& get_instance();
        $CI->load->helper(array('file','directory','translation'));


        // TODO: We could speed this up by storing it in a database table
        $this->allfiles = array();
        $allf = directory_map('db',1);

        foreach ($allf as $af) {
            if (!self::endswith($af, '.db.json')) /* Not a database (Directory names end in slash) */
                continue;

            $dbinfo_json = $this->read_or_throw("db/$af");
            $dbinfo = json_decode($dbinfo_json);

            $this->allfiles[$dbinfo->propertiesName] = new database_file($dbinfo);
        }

        // Remove subset files
        $add_to_allfiles = array();
        $this->allfiles_enumerate = array();

        foreach ($this->allfiles as $pr => &$dbf) {
            $superset = $dbf->getSuperProp();
            if (!isset($this->allfiles[$superset])) {   // A superset does not exits...
                $this->allfiles_enumerate[$pr] = $dbf;  // ...therefore we may enumerate this database
                if (!is_null($superset))                // The superset could have existed
                    $add_to_allfiles[$superset] = $dbf; // ...therefore we make the superset point to the subset 
            }
        }

        // Commit additions
        foreach ($add_to_allfiles as $pr => $dbf2)
            $this->allfiles[$pr] = $dbf2;
    }


    /// Reads all the contents of a file.
    /// @param $filename The name of the file to read
    /// @return The content of the file.
    /// @throws DataException if the file cannot be read.
    private function read_or_throw(string $filename) {
        $data = file_get_contents($filename);
        if ($data===false)
            throw new DataException("Missing file: $filename");
        return $data;
    }


    /// Reads the contents of a bookorder file. Each line is such a file has this format:
    /// @code Isaiah/2,6-9,36,38,40-53,66 @endcode
    /// where "Isaiah" is the name of the book and 2,6-9,etc. are the chapters that exist in the Emdros database.
    /// @param $filename The name of the file to read
    /// @return An array whose elements are arrays of two elements: Book name and chapter list.
    /// @throws DataException if the file cannot be read.
    private function read_bookorder_file(string $filename) {
        $handle = @fopen($filename, 'r');
        if ($handle===false)
            throw new DataException("Missing file: $filename");
        $res = array();
        while (($buffer = fgets($handle))!==false) {
            $buffer = trim($buffer);
            if ($buffer!=='')
                $res[] = explode('/',$buffer);
        }
        return $res;
    }


    /// Initializes this object with information about a single Emdros database.
    /// @param $db The name of the Emdros database.
    /// @param $pr The name of the properties file (localization) for the Emdros database.
    /// @param $language The selected localization language.
    /// @throws DataException if the database does not exist.
    public function init_config(string $db, string $pr, string $language, $dothrow=true) {
        $propname = empty($pr) ? $db : $pr;
        if (!isset($this->allfiles[$propname]))
            if ($dothrow)
                throw new DataException("Illegal database name: $propname");
            else
                return false;

        $dbf = $this->allfiles[$propname];

        $this->init_config_dbf($dbf, $language);
        return true;
    }


    // Returns variant grammar table, or null for main table
    private function create_variant_grammar_table() {
        $CI =& get_instance();

        if (!empty($_SESSION['variant'])) {
            $variant_grammar_table = 'db_localize_' . $_SESSION['variant'];
            // Create database for variant if it does not exist
            if (!$CI->db->table_exists($variant_grammar_table)) {
                $CI->load->dbforge();
                $CI->dbforge->add_field(array('id' => array('type' => 'INT', 'auto_increment' => true),
                                              'db' => array('type'=>'TINYTEXT'),
                                              'lang' => array('type'=>'TINYTEXT'),
                                              'json' => array('type'=>'MEDIUMTEXT')
                                            ));
                $CI->dbforge->add_key('id', TRUE);
                $CI->dbforge->create_table($variant_grammar_table);
            }
            return $variant_grammar_table;
        }
        else 
            return null;
    }


    /// Initializes this object with information about a single Emdros database.
    /// @param $dbf A database_file object describing the select Emdros database.
    /// @param $language The selected localization language.
    public function init_config_dbf(database_file $dbf, string $language) {
        if ($dbf->emdros_db=='db/ETCBC4') {
            $this->src_lang = array('Hebrew','Aramaic');
            $this->glosslang = get_heblex_translations();
        }
        elseif ($dbf->emdros_db=='db/nestle1904') {
            $this->src_lang = array('greek');
            $this->glosslang = get_greeklex_translations();
        }
        elseif ($dbf->emdros_db=='db/jvulgate' || $dbf->emdros_db=='db/VC') {
            $this->src_lang = array('latin');
            $this->glosslang = get_latinlex_translations();
        }

        $this->dbinfo_json = $this->read_or_throw($dbf->dbinfo);
        $this->dbinfo = json_decode($this->dbinfo_json);
        $this->addgloss_dbinfo();
        
        $CI =& get_instance();
        $query = $CI->db->select('json')->where('db',$dbf->propertiesName)->where('lang',$language)->get('db_localize');
        $this->l10n_json = $query->row()->json;
        if ($language!='en') {
            // Replace unknown terms with those from English
            $query = $CI->db->select('json')->where('db',$dbf->propertiesName)->where('lang','en')->get('db_localize');
            $eng_l10n_json = $query->row()->json;

            $l1 = json_decode($eng_l10n_json, true);
            $l2 = json_decode($this->l10n_json, true);
            $this->l10n_json = json_encode(array_replace_recursive($l1, $l2));
        }

        $this->addgloss_l10n_json($language);

        // Add translations for current variant
        $variant_grammar_table = $this->create_variant_grammar_table();
        if ($variant_grammar_table) {
            $query = $CI->db->select('json')->where('db',$dbf->propertiesName)->where('lang',$language)->get($variant_grammar_table);
            if ($query->num_rows()!=0) {
                // Replace the relevant localizations with variant values
                $l1 = json_decode($this->l10n_json, true);
                $l2 = json_decode($query->row()->json, true);
                $this->l10n_json = json_encode(array_replace_recursive($l1, $l2));
            }
        }

        $this->typeinfo_json = $this->read_or_throw($dbf->typeinfo);
        $this->addgloss_typeinfo_json();
        $this->typeinfo = new TypeInfo($this->typeinfo_json);

        $this->bookorder = $this->read_bookorder_file($dbf->bookorder);
         
        $this->emdros_db = $dbf->emdros_db;
    }

    private function addgloss_dbinfo() {
        $fsetting = $this->dbinfo->objectSettings->{$this->dbinfo->objHasSurface}->featuresetting;
        if (isset($fsetting->gloss)) {
            $CI =& get_instance();
            $CI->load->helper('create_lexicon_helper');

            // Replace 'gloss' with 'english', 'german, etc.
            foreach ($this->glosslang as $gl) {
                if (!empty($_SESSION['variant'])) {
                    // Create variant lexicons, unless they already exist
                    foreach ($this->src_lang as $src_l)
                        create_lexicon_table($src_l, $gl->abb, $_SESSION['variant'], true);
                }
            
                $langname = $gl->internal;
                $fsetting->$langname = clone $fsetting->gloss;
                $fsetting->$langname->sql_command = str_replace('LANG',$gl->abb,$fsetting->gloss->sql_command);

                if (!empty($_SESSION['variant']) && $fsetting->gloss->sql_command_variant)
                    $fsetting->$langname->sql_command_variant = str_replace(array('LANG','VARIANT'),
                                                                            array($gl->abb,$_SESSION['variant']),
                                                                            $fsetting->gloss->sql_command_variant);
                else
                    $fsetting->$langname->sql_command_variant = null;

                // The following is relevant only if the interface language is Chinese.
                // Non-Chinese glosses must be displayed with a smaller fontsize
                if ($gl->abb!='zh-Hans' && $gl->abb!='zh-Hant')
                    $fsetting->$langname->fontsize = "tenpoint";
                
                $fsetting->$langname->isGloss = true;  // Extra feature
            }
            unset($fsetting->gloss);

            // Replace 'GrammarGroupGlosses.glosses' with 'GrammarGroup.english', 'GrammarGroup.german' etc.
            
            foreach ($this->dbinfo->sentencegrammar as $sgo) {
                if ($sgo->objType===$this->dbinfo->objHasSurface) {
                    foreach ($sgo->items as $it) {
                        if ($it->mytype==='GrammarGroupGlosses') {
                            foreach ($this->glosslang as $gl) {
                                $feat = new stdClass;
                                $feat->mytype = 'GrammarFeature';
                                $feat->name = $gl->internal;
                                $it->items[] = $feat;
                            }
                            $it->mytype = 'GrammarGroup';
                        }
                    }
                }
            }
        }
        
        $this->dbinfo_json = json_encode($this->dbinfo);
    }

    // Extends the typeinfo variable with information for various gloss languages
    private function addgloss_typeinfo_json() {
        $typinf = json_decode($this->typeinfo_json);
        
        $osetting = $typinf->obj2feat->{$this->dbinfo->objHasSurface};

        foreach ($this->glosslang as $gl)
            $osetting->{$gl->internal} = 'string';

        $this->typeinfo_json = json_encode($typinf);
    }

    // Extends the l10n variable with words for various gloss languages
    private function addgloss_l10n_json(string $language) {
        $CI =& get_instance();
        $CI->lang->load('users', $language);

        $l10n = json_decode($this->l10n_json);
        
        $wsetting = $l10n->emdrosobject->{$this->dbinfo->objHasSurface};

        foreach ($this->glosslang as $gl) {
            $langname = $gl->internal;
            $wsetting->$langname = $CI->lang->line($langname);
        }
        
        $this->l10n_json = json_encode($l10n);
    }

  }
