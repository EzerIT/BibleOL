<?php
  // Copyright © 2013 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk

  // This is for command line usage
  // Usage:
  //    php dbxml2json.php <DBXML-file>
  // Outputs a JSON version of the database information to stdout.


  // Note may of these classes omit declaration of member variables. This is in order to ensure that
  // those member variables are only inluded in the JSON output if they are set.

error_reporting(E_ALL|E_STRICT);
ini_set('display_errors', 'stderr');
ini_set('track_errors', true);

mb_internal_encoding('UTF-8');



require_once('myapp/helpers/xmlhandler_helper.php');

class SubsetOf extends XmlHandler {
    public $name;

    /**  Name of superset property file. */
    public $properties;

    /**  Paths within the superset database provided by this subset. */
    public $provides = array();


    function open_handler(&$handlers, $elem) {
        switch ($elem['tag']) {
            case 'provides':
                  $this->provides[] = $elem['value'];
                  break;

          case 'name':
                  $this->name = $elem['value'];
                  break;

          case 'properties':
                  $this->properties = $elem['value'];
                  break;

          default:
                PANIC(__FILE__,__LINE__ . " elem $elem[tag]");
                break;
        }
	}
		
    function close_handler(&$handlers, $elem) {
        switch ($elem['tag']) {
          case 'provides':
          case 'name':
          case 'properties':
                // Ignore
                break;

          case 'subsetof':
                parent::close_handler($handlers, $elem);
                break;

          default:
                PANIC(__FILE__,__LINE__ . " elem $elem[tag]");
                break;
        }
    }
}



class DatabaseInfo extends XmlHandler {
    /** Version number found in the {@literal <databasedescription>} element. */
    public $version;
	
    /** The name of the database file. Together with {@link #propertiesName} this uniquely
     * identifies a DatabaseInfo object. */
    public $databaseName;

    /** The name of the properties file. Together with {@link #databaseName} this uniquely
     * identifies a DatabaseInfo object. */
    public $propertiesName; // Name of properties file

    /** Database version. */
    public $databaseVersion = "Unknown"; //$NON-NLS-1$

    /** The name of the Emdros object that represents the amount of text displayed in a single
     * question. This is typically "sentence".
     */
    public $granularity;

    /** The name of the Emdros feature that represents the actual text to display. */
    public $surfaceFeature;

    /** The name of the Emdros object that has the feature in {@link #surfaceFeature}. This Emdros
     * object must correspond to a single monad.
     */
    public $objHasSurface;

    /** The name of the Emdros feature that represents a string to display after the {@link #surfaceFeature}. */
    public $suffixFeature;

    /** The {@link CharSet} used by the database. */
    public $charSet;

    /** {@link ObjectSetting}s associated with Emdros objects in this database. */
    public $objectSettings; //ChronologyMap<String, ObjectSetting> 

    /** The universe hierarchy levels used with this database. */
    public $universeHierarchy;//Vector<UHTriple> 

    /** Set to the basic URL of the associated picture database, if any (e.g. "http://dbilinux.dbi.edu/picdb") */
    public $picDb;

    /** The list of sentence grammar information displayable in this database. */
    //public List<SentenceGrammar> sentenceGrammarList;
    public $sentencegrammar = array();

    public $subsetOf = null;  // TODO: Not used now
    
	/**
	 * Serialising constructor.
	 */
    function __construct($elem) {
        parent::__construct($elem);

		$this->version = intval($elem['attributes']['version']);
	}


    /** Finalizes the construction of {@link #sentenceGrammarList} by adding the bottom and top
     * level of the hierarchy. */
	private function completeSentenceGrammarList() {
		// Add top level:
        // Construct pseudo element:
        $pselem = array('attributes'=>array('object'=>$this->granularity),
                        'tag'=>null,
                        'level'=>null,
                        'value'=>null);
		$this->sentencegrammar[] = new SentenceGrammar($pselem);
	}


    function open_handler(&$handlers, $elem) {
        switch ($elem['tag']) {
          case 'dbname':
                $this->databaseName = $elem['value'];

                if (strncmp($this->databaseName, 'WIVU', strlen('WIVU'))===0)
                    $this->useSofPasuq = true; // TODO: Move to dbxml file

                break;

          case 'version':
                $this->databaseVersion = $elem['value'];
                break;

          case 'properties':
                $this->propertiesName = $elem['value'];
                break;

		  case 'picdb':
                $this->picDb = $elem['value'];
                break;

		  case 'granularity':
                $this->granularity = $elem['value'];
                break;

		  case 'surfacefeature':
                $this->surfaceFeature = $elem['value'];
                break;

		  case 'surfaceobj':
                $this->objHasSurface = $elem['value'];
                break;

		  case 'suffixfeature':
                $this->suffixFeature = isset($elem['value']) ? $elem['value'] : null;
                break;

		  case 'charset':
                $this->charSet = $elem['value'];
                break;

          case 'subsetof':
                $ss = 
                $this->subsetOf = new SubsetOf($elem);
                $handlers[] = $this->subsetOf;
                break;

		  case 'execmode':
		  case 'mqlcommand':
		  case 'mqlhost':
		  case 'mqlhostpath':
		  case 'mqlport':
          case 'disabled':
		  case 'dbid':
                // Ignore
                break;

          case 'objectsetting':
                $os = new ObjectSetting($elem);
                $this->objectSettings[$elem['attributes']['object']] = $os;
                $handlers[] = $os;
                break;

          case 'sentencegrammar':
                $sg = new SentenceGrammar($elem);
                $this->sentencegrammar[] = $sg;
                $handlers[] = $sg;
                break;

          case 'universelevel':
                $this->universeHierarchy[] = array('type'=>$elem['attributes']['object'],
                                                   'feat'=>$elem['attributes']['feature']);
                break;

          default:
                PANIC(__FILE__,__LINE__ . " elem $elem[tag]");
                break;
        }
	}
    
    function close_handler(&$handlers, $elem) {
        switch ($elem['tag']) {
          case 'dbname':
          case 'version':
          case 'properties':
		  case 'picdb':
		  case 'granularity':
		  case 'surfacefeature':
		  case 'surfaceobj':
		  case 'suffixfeature':
		  case 'charset':
		  case 'execmode':
		  case 'mqlcommand':
		  case 'mqlhost':
		  case 'mqlhostpath':
		  case 'mqlport':
          case 'disabled':
		  case 'dbid':
          case 'universelevel':
                // Ignore
                break;

          case 'databasedescription':
                if (!isset($this->propertiesName))
                    $this->propertiesName = $this->databaseName;
                $this->completeSentenceGrammarList();
                parent::close_handler($handlers, $elem);
                break;

          default:
                PANIC(__FILE__,__LINE__ . " elem $elem[tag]");
                break;
        }
    }
}

class ObjectSetting extends XmlHandler {
    /**
     * Constructs a new {@code ObjectSetting} object.
     */
    function __construct($elem) {
        parent::__construct($elem);
    }

    function open_handler(&$handlers, $elem) {
        switch ($elem['tag']) {
          case 'featuresetting':
                if (!isset($this->featuresetting))
                    $this->featuresetting = array();
                $fs =  new FeatureSetting($elem);
                $this->featuresetting[$elem['attributes']['name']] = $fs;
                $handlers[] = $fs;
                break;

          case 'mayselect':
                if ($elem['value']==='true')
                    $this->mayselect = true;
                break;

          case 'additionalfeatures':
                if (!isset($this->additionalfeatures))
                    $this->additionalfeatures = array();
                $this->additionalfeatures[] = $elem['value'];
                break;

          default:
                PANIC(__FILE__,__LINE__ . " elem $elem[tag]");
                break;
        }
	}
    
    function close_handler(&$handlers, $elem) {
        switch ($elem['tag']) {
          case 'featuresetting':
          case 'mayselect':
          case 'additionalfeatures':
                // Ignore
                break;

          case 'objectsetting':
                parent::close_handler($handlers, $elem);
                break;

          default:
                PANIC(__FILE__,__LINE__ . " elem $elem[tag]");
                break;
        }
	}
}

class FeatureSetting extends XmlHandler {
    /**
     * Constructs a new {@code FeatureSetting} object.
     */
    function __construct($elem) {
        parent::__construct($elem);

        if (isset($elem['attributes']['default']) &&
            ($elem['attributes']['default']==='yes' || $elem['attributes']['default']==='true'))
            $this->isDefault = true;
        
    }

    function open_handler(&$handlers, $elem) {
        switch ($elem['tag']) {
          case 'ignoreselect':
                if ($elem['value']==='true')
                    $this->ignoreSelect = true;
                break;

          case 'ignoreshowrequest':
                if ($elem['value']==='true')
                    $this->ignoreShowRequest = true;
                break;

          case 'hideword':
                if ($elem['value']==='true')
                    $this->hideWord = true;
                break;

          case 'hidevalue':
                if (!isset($this->hideValues))
                    $this->hideValues = array();
                $this->hideValues[] = $elem['value'];
                break;

          case 'othervalue':
                if (!isset($this->otherValues))
                    $this->otherValues = array();
                $this->otherValues[] = $elem['value'];
                break;

          case 'isrange':
                if ($elem['value']==='true')
                    $this->isRange = true;
                break;

          case 'matchregexp':
                $this->matchregexp = '/^' . str_replace('%s','{0}',$elem['value']) . '$/i';
                break;

          case 'alternateshowrequest':
                $this->alternateshowrequestDb = $elem['attributes']['db'];
                $this->alternateshowrequestSql = $elem['value'];
                break;

          case 'foreign':
                if ($elem['value']==='true')
                    $this->foreignText = true;
                if ($elem['value']==='transliterated')
                    $this->transliteratedText = true;
                break;

          case 'color':
                // TODO: Add color
                break;

          default:
                PANIC(__FILE__,__LINE__ . " elem $elem[tag]");
                break;
        }
	}
		
    function close_handler(&$handlers, $elem) {
        switch ($elem['tag']) {
          case 'ignoreselect':
          case 'ignoreshowrequest':
          case 'hideword':
          case 'hidevalue':
          case 'othervalue':
          case 'isrange':
          case 'matchregexp':
          case 'alternateshowrequest':
          case 'foreign':
          case 'color':
                // Ignore
                break;

          case 'featuresetting':
                parent::close_handler($handlers, $elem);
                break;

          default:
                PANIC(__FILE__,__LINE__ . " elem $elem[tag]");
                break;
        }
    }
}


class GrammarGroup extends XmlHandler {
    public $mytype;
    public $name;
    public $items = array();


    /**
     * Constructs a new {@code GrammarGroup} object.
     */
    public function __construct(array $elem) {
        parent::__construct($elem);
        $this->mytype = 'GrammarGroup';
        $this->name = $elem['attributes']['name'];
    }


    function open_handler(&$handlers, $elem) {
        switch ($elem['tag']) {
          case 'grammarfeature':
                $gf =  new GrammarFeature($elem);
                $this->items[] = $gf;
                $handlers[] = $gf;
                break;

          case 'grammargroup':
                $gg =  new GrammarGroup($elem);
                $this->items[] = $gg;
                $handlers[] = $gg;
                break;

          case 'grammarmetafeature':
                $gf =  new GrammarMetaFeature($elem);
                $this->items[] = $gf;
                $handlers[] = $gf;
                break;

          default:
                PANIC(__FILE__,__LINE__ . " elem $elem[tag]");
                break;
        }
	}

    function close_handler(&$handlers, $elem) {
        switch ($elem['tag']) {
          case 'grammargroup':
                parent::close_handler($handlers, $elem);
                break;

          default:
                PANIC(__FILE__,__LINE__ . " elem $elem[tag]");
                break;
        }
    }
}

class GrammarFeature extends XmlHandler {
    public $mytype;

    /**
     * Constructs a new {@code GrammarFeature} object.
     */
    public function __construct(array $elem) {
        parent::__construct($elem);
        $this->mytype = 'GrammarFeature';
        $this->name = $elem['attributes']['name'];
    }

    function open_handler(&$handlers, $elem) {
        switch ($elem['tag']) {
          case 'color':
                if (!isset($this->coloring))
                    $this->coloring = array();
                $this->coloring[$elem['attributes']['name']] = array(intval($elem['attributes']['r']),
                                                                     intval($elem['attributes']['g']),
                                                                     intval($elem['attributes']['b']));
                break;

          default:
                PANIC(__FILE__,__LINE__ . " elem $elem[tag]");
                break;
        }
	}

    function close_handler(&$handlers, $elem) {
        switch ($elem['tag']) {
          case 'color':
                // Ignore
                break;

          case 'grammarfeature':
                parent::close_handler($handlers, $elem);
                break;

          default:
                PANIC(__FILE__,__LINE__ . " elem $elem[tag]");
                break;
        }
    }
}

class GrammarMetaFeature extends XmlHandler  {
    public $mytype;

    /**
     * Constructs a new {@code GrammarMetaFeature} object.
     */
    public function __construct(array $elem) {
        parent::__construct($elem);
        $this->mytype = 'GrammarMetaFeature';
        $this->name = $elem['attributes']['name'];
    }

    function open_handler(&$handlers, $elem) {
        switch ($elem['tag']) {
          case 'grammarsubfeature':
                $this->items[] = array('mytype'=>'GrammarSubFeature',
                                       'name'=>$elem['attributes']['name']);
                break;

          default:
                PANIC(__FILE__,__LINE__ . " elem $elem[tag]");
                break;
        }
	}

    function close_handler(&$handlers, $elem) {
        switch ($elem['tag']) {
          case 'grammarsubfeature':
                // Ignore
                break;

          case 'grammarmetafeature':
                parent::close_handler($handlers, $elem);
                break;

          default:
                PANIC(__FILE__,__LINE__ . " elem $elem[tag]");
                break;
        }
    }
}

class SentenceGrammar extends XmlHandler {
    public $mytype;

    /**
     * Constructs a new {@code SentenceGrammar} object.
     */
    public function __construct(array $elem) {
        parent::__construct($elem);
        $this->mytype = 'SentenceGrammar';
        $this->objType = $elem['attributes']['object'];
    }


    function open_handler(&$handlers, $elem) {
        switch ($elem['tag']) {
          case 'grammarfeature':
                $gf =  new GrammarFeature($elem);
                $this->items[] = $gf;
                $handlers[] = $gf;
                break;

          case 'grammargroup':
                $gg =  new GrammarGroup($elem);
                $this->items[] = $gg;
                $handlers[] = $gg;
                break;

          case 'grammarmetafeature':
                $gf =  new GrammarMetaFeature($elem);
                $this->items[] = $gf;
                $handlers[] = $gf;
                break;

          default:
                PANIC(__FILE__,__LINE__ . " elem $elem[tag]");
                break;
        }
	}

    function close_handler(&$handlers, $elem) {
        switch ($elem['tag']) {
          case 'sentencegrammar':
                parent::close_handler($handlers, $elem);
                break;

          default:
                PANIC(__FILE__,__LINE__ . " elem $elem[tag]");
                break;
        }
	}
}


if ($_SERVER['argc']!==2) {
    print "Usage: php dbxml2json.php <DBXML-file>\n";
    die;
}


$contents = @file_get_contents($_SERVER['argv'][1])
    or die ("Failed opening file {$_SERVER['argv'][1]}:\nError was '$php_errormsg'\n");

$decoded_xml = harvest($contents);
print json_encode($decoded_xml);


?>