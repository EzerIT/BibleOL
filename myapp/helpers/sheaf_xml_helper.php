<?php

class XmlMqlResults extends XmlHandler {
    private $crop;

    function __construct($tagname, $attributes) {
        parent::__construct($tagname, $attributes);
        $this->crop = new Crop;
    }

    public function get_crop() {
        return $this->crop;
    }

    function open_handler(&$handlers, $tagname, $attributes) {
        switch ($tagname) {
          case 'mql_result':
                $mr = new XmlMqlResult($tagname, $attributes);
                $this->crop->add_crop($mr->getTableOrSheaf());
                $handlers[] = $mr;
                break;

          default:
                PANIC(__FILE__,__LINE__);
                break;
        }
    }

    function close_handler(&$handlers, $tagname) {
        assert($tagname=='mql_results');
        parent::close_handler($handlers, $tagname);
    }
}


class XmlMqlResult extends XmlHandler {
    private $mql_progress; // ignored
    private $status; // ignored
    private $error;
    private $warning; // ignored

    private $tableOrSheaf;

    function __construct($tagname, $attributes) {
        parent::__construct($tagname, $attributes);
        $this->tableOrSheaf = new TableOrSheaf;
    }

    public function getTableOrSheaf() {
        return $this->tableOrSheaf;
    }

    function open_handler(&$handlers, $tagname, $attributes) {
        switch ($tagname) {
          case 'mql_progress':
          case 'status':
          case 'warning':
                // Ignore
                break;

          case 'error':
                $this->error = new XmlMqlError($tagname, $attributes);
                $handlers[] = $this->error;
                break;

          case 'table':
                $xmltab = new XmlTable($tagname, $attributes);
                $this->tableOrSheaf->set_table($xmltab->get_oltable());
                $handlers[] = $xmltab;
                break;

          case 'sheaf':
          case 'flatsheaf':
                $shf = new XmlSheaf($tagname, $attributes);
                $this->tableOrSheaf->set_sheaf($shf->getOlSheaf());
                $handlers[] = $shf;
                break;
                
          default:
                PANIC(__FILE__,__LINE__ . ' ' . $tagname);
                break;
        }
    }

    function close_handler(&$handlers, $tagname) {
        switch ($tagname) {
          case 'mql_progress':
          case 'status':
          case 'warning':
                // Ignore
                break;

          case 'mql_result':
                parent::close_handler($handlers, $tagname);
                break;
        }
    }
}

class XmlMqlError extends XmlHandler {
    public $source;
    public $db_message;
    public $compiler_message;

    function open_handler(&$handlers, $tagname, $attributes) {
        switch ($tagname) {
          case 'error_source':
                $this->source = $attributes['source']; // 'compiler' or 'db'
                break;

          case 'error_stage':
                // Ignore
                break;

          case 'error_message_db':
                if ($this->source==='db') {
                    $this->setthis = &$this->db_message;
                    $this->setthis_type = SetThisType::SET;
                }
                break;

          case 'error_message_compiler':
                if ($this->source==='compiler')  {
                    $this->setthis = &$this->compiler_message;
                    $this->setthis_type = SetThisType::SET;
                }
                break;

          default:
                PANIC(__FILE__,__LINE__ . ' ' . $tagname);
                break;
        }
    }

    function close_handler(&$handlers, $tagname) {
        switch ($tagname) {
          case 'error_source':
          case 'error_stage':
          case 'error_message_db':
          case 'error_message_compiler':
                // Ignore
                break;

          case 'error':
                throw new MqlException($this->db_message, $this->compiler_message);
                // parent::close_handler($handlers, $tagname);
                break;

          default:
                PANIC(__FILE__,__LINE__ . ' ' . $tagname);
                break;
        }
    }
}
  
class XmlTable extends XmlHandler {
    private $olTable;

    private $header = array();
    private $row = array();

    private $currentrow;

    public function __construct($tagname, $attributes) {
        parent::__construct($tagname, $attributes);
        $this->olTable = new OlTable;
    }

    public function get_oltable() {
        return $this->olTable;
    }

    function open_handler(&$handlers, $tagname, $attributes) {
        switch ($tagname) {
          case 'theader':
          case 'theadercolumn':
          case 'ttype':
                // Ignore
                break;

          case 'tcaption':
                $this->setthis = &$this->header;
                $this->setthis_type = SetThisType::PUSH;
                break;

          case 'trow':
                $this->currentrow = array();
                break;

          case 'tcolumn':
                $this->setthis = &$this->currentrow;
                $this->setthis_type = SetThisType::PUSH;
                break;

          default:
                PANIC(__FILE__,__LINE__);
                break;
        }
    }

    function close_handler(&$handlers, $tagname) {
        switch ($tagname) {
          case 'theadercolumn':
          case 'ttype':
          case 'tcaption':
          case 'tcolumn':
                // Ignore
                break;

          case 'theader':
                $this->olTable->set_header($this->header);
                break;

          case 'trow':
                $this->olTable->add_row($this->currentrow);
                $this->currentrow = null;
                break;

          case 'table':
                parent::close_handler($handlers, $tagname);
                break;
        }
    }
} 


class XmlMonadSet extends XmlHandler {
    private $olMonadSet;

    public function getOlMonadSet() {
        return $this->olMonadSet;
    }

    function __construct($tagname, $attributes) {
        parent::__construct($tagname, $attributes);
        $this->olMonadSet = new OlMonadSet();

    }

    function open_handler(&$handlers, $tagname, $attributes) {
        switch ($tagname) {
          case 'mse':
                $this->olMonadSet->addOne($attributes['first'],$attributes['last']);
                break;

          default:
                PANIC(__FILE__,__LINE__);
                break;
        }
    }

    function close_handler(&$handlers, $tagname) {
        switch ($tagname) {
          case 'mse':
                break;

          case 'monad_set':
                parent::close_handler($handlers, $tagname);
                break;
        }
    }
}

class XmlMatchedObject extends XmlHandler {
    private $olMatchedObject;
    private $features;

    function __construct($tagname, $attributes) {
        parent::__construct($tagname, $attributes);
        $this->olMatchedObject = new OlMatchedObject(intval($attributes['id_d']), $attributes['object_type_name']);
    }

    public function getOlMatchedObject() {
        return $this->olMatchedObject;
    }

    function open_handler(&$handlers, $tagname, $attributes) {
        switch ($tagname) {
          case 'features':
                // IGNORE
                break;

          case 'feature':
                $this->setthis = &$this->features[$attributes['feature_name']];
                if ($attributes['type']==='integer')
                    $this->setthis_type = SetThisType::SET_NUM;
                else {
                    $this->setthis_type = SetThisType::SET_STRING;
                    $this->setthis = ''; // We are going to append to this
                }
                break;
                
          case 'monad_set':
                $ms =  new XmlMonadSet($tagname, $attributes);
                $this->olMatchedObject->set_monadset($ms->getOlMonadSet());
                $handlers[] = $ms;
                break;

          case 'sheaf':
                $shf = new XmlSheaf($tagname, $attributes);
                $this->olMatchedObject->set_sheaf($shf->getOlSheaf());
                $handlers[] = $shf;
                break;

          default:
                PANIC(__FILE__,__LINE__);
                break;
        }
    }

    function close_handler(&$handlers, $tagname) {
        switch ($tagname) {
          case 'feature':
          case 'features':
                // IGNORE
                break;

          case 'matched_object':
                $this->olMatchedObject->set_features($this->features);
                $this->olMatchedObject->clear_sheaf_if_empty();
                parent::close_handler($handlers, $tagname);
                break;

          default:
                PANIC(__FILE__,__LINE__);
        }
    }
}

class XmlStraw extends XmlHandler {
    private $olStraw;

    function __construct($tagname, $attributes) {
        parent::__construct($tagname, $attributes);
        $this->olStraw = new OlStraw();
    }

    public function getOlStraw() {
        return $this->olStraw;
    }

    function open_handler(&$handlers, $tagname, $attributes) {
        switch ($tagname) {
          case 'matched_object':
                $mo = new XmlMatchedObject($tagname, $attributes);
                $this->olStraw->add_matched_object($mo->getOlMatchedObject());
                $handlers[] = $mo;
                break;

          default:
                PANIC(__FILE__,__LINE__);
                break;
        }
    }

    function close_handler(&$handlers, $tagname) {
        assert($tagname=='straw' || $tagname=='flatstraw');
        parent::close_handler($handlers, $tagname);
    }
}

class XmlSheaf extends XmlHandler {
    private $olSheaf;
    private $sheaf_counter = 0;

    function __construct($tagname, $attributes) {
        parent::__construct($tagname, $attributes);
        $this->olSheaf = new OlSheaf();
    }

    public function getOlSheaf() {
        return $this->olSheaf;
    }

    function open_handler(&$handlers, $tagname, $attributes) {
        global $quick_harvest;
        if ($quick_harvest) {
            // Quick harvest. This means storing the monadsets directly in the top OlSheaf object

            switch ($tagname) {
              case 'straw':
              case 'matched_object':
                    // Ignore
                    break;

              case 'sheaf':
                    ++$this->sheaf_counter;
                    break;

              case 'monad_set':
                    $ms = new XmlMonadSet($tagname, $attributes);
                    $this->olSheaf->add_monadset($ms->getOlMonadSet());
                    $handlers[] = $ms;
                    break;

              default:
                    PANIC(__FILE__,__LINE__);
                    break;
            }
        }
        else {
            switch ($tagname) {
              case 'straw':
              case 'flatstraw':
                    $str = new XmlStraw($tagname, $attributes);
                    $this->olSheaf->add_straw($str->getOlStraw());
                    $handlers[] = $str;
                    break;

              default:
                    PANIC(__FILE__,__LINE__);
                    break;
            }
        }
    }

    function close_handler(&$handlers, $tagname) {
        global $quick_harvest;
        if ($quick_harvest) {
            switch ($tagname) {
              case 'straw':
              case 'matched_object':
                    // Ignore
                    break;

              case 'sheaf':
                    if ($this->sheaf_counter-- == 0) 
                        parent::close_handler($handlers, $tagname);
                    break;

              default:
                    PANIC(__FILE__,__LINE__);
                    break;
            }
        }
        else {
            assert($tagname=='sheaf' || $tagname=='flatsheaf');
            parent::close_handler($handlers, $tagname);
        }
    }
}

    
