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
        $CI->load->helper(array('file','directory'));


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

    /// Initializes this object with information about a single Emdros database.
    /// @param $dbf A database_file object describing the select Emdros database.
    /// @param $language The selected localization language.
    public function init_config_dbf(database_file $dbf, string $language) {
        $this->dbinfo_json = $this->read_or_throw($dbf->dbinfo);
        $this->dbinfo = json_decode($this->dbinfo_json);

        $CI =& get_instance();
        $query = $CI->db->select('json')->where('db',$dbf->propertiesName)->where('lang',$language)->get('db_localize');
        $this->l10n_json = $query->row()->json;

        $this->typeinfo_json = $this->read_or_throw($dbf->typeinfo);
        $this->typeinfo = new TypeInfo($this->typeinfo_json);

        $this->bookorder = $this->read_bookorder_file($dbf->bookorder);
         
        $this->emdros_db = $dbf->emdros_db;
    }
  }
