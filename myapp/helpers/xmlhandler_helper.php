<?php

class SetThisType {
    const DONT_SET = 0;
    const SET = 1;
    const PUSH = 2;
    const SET_BOOL = 3;
    const PUSH_REQUEST_FEATURES = 4;
    const PUSH_REQUESTDD_FEATURES = 5;
    const SET_STRING = 6;  // Escapes HTML
    const SET_NUM = 7;
    const PUSH_NUM = 8;
    const PUSH_APPEND = 9;
    const PUSH_CONTINUE = 10;
}

class XmlHandler {
    private $name;
    protected $att;

    private $top;
    private $hasLoadedHelper = false;


    protected $setthis;
	protected $setthis_type = SetThisType::DONT_SET;
    protected $setthis_attribs = null;

    function get_top() {
        return $this->top;
    }

    function __construct($tagname, $attributes) {
        $this->name = $tagname;
        $this->att = $attributes;
    }

    function open_handler(&$handlers, $tagname, $attributes) {
        switch ($tagname) {
          case 'mql_results':
                if (!$this->hasLoadedHelper) {
                    $CI =& get_instance();
                    $CI->load->helper('sheaf_xml');
                    $this->hasLoadedHelper = true;
                }

                $this->top = new XmlMqlResults($tagname, $attributes);
                $handlers[] = $this->top;
                break;

		  case 'questiontemplate':
				$this->top = new Template($tagname, $attributes);
				$handlers[] = $this->top;
				break;
        
          default:
                PANIC(__FILE__,__LINE__);
                break;
        }
    }

    public function content_handler(&$handlers, string $content) {
        switch ($this->setthis_type) {
          case SetThisType::SET:
                $this->setthis .= $content;
                break;

          case SetThisType::PUSH:
                if (is_null($this->setthis_attribs))
                    $this->setthis[] = $content;
                else {
                    $this->setthis_attribs['content'] = $content;
                    $this->setthis[] = $this->setthis_attribs;
                    $this->setthis_attribs = null;
                }
                $this->setthis_type = SetThisType::DONT_SET;
                break;

          case SetThisType::PUSH_APPEND:
                $this->setthis[] = $content;
                $this->setthis_type = SetThisType::PUSH_CONTINUE;
                break;

          case SetThisType::PUSH_CONTINUE:
                $this->setthis[count($this->setthis)-1] .= $content;
                break;

          case SetThisType::PUSH_NUM:
                $this->setthis[] = intval($content);
                $this->setthis_type = SetThisType::DONT_SET;
                break;

          case SetThisType::SET_BOOL:
                $this->setthis = $content==='true';
                break;

          case SetThisType::PUSH_REQUEST_FEATURES:
                $obj = new stdClass;
                $obj->name = $content;
                $obj->usedropdown = false;
                $obj->hideFeatures = !empty($this->hideFeatures) ? explode(',',$this->hideFeatures) : null;
                $this->hideFeatures = null;
                $this->setthis[] = $obj;
                break;

          case SetThisType::PUSH_REQUESTDD_FEATURES:
                $obj = new stdClass;
                $obj->name = $content;
                $obj->usedropdown = true;
                $this->setthis[] = $obj;
                break;

          case SetThisType::SET_STRING:
                $this->setthis .= is_null($content) ? '' : $content;
                break;

          case SetThisType::SET_NUM:
                $this->setthis = intval($content);
                break;
        }
        //$this->setthis_type = SetThisType::DONT_SET;
    }

    public function close_handler(&$handlers, $tagname) {
        array_pop($handlers);
    }
}


$accept = false;

function tagStart($parser, $tagname, $attributes = NULL) {
    global $xml_handlers, $accept;
    $h = end($xml_handlers);

    $accept = true;
    $h->open_handler($xml_handlers, $tagname, $attributes);
}

function tagEnd($parser, $tagname) {
    global $xml_handlers, $accept;
    $h = end($xml_handlers);

    $accept = false;
    $h->close_handler($xml_handlers, $tagname);
}

function tagContent($parser, string $content) {
    global $xml_handlers, $accept;
    if (!$accept)
        return;

    $h = end($xml_handlers);
    $h->content_handler($xml_handlers, $content);
}



function harvest($xml, $q_harvest=false) {
    global $quick_harvest;
    $quick_harvest = $q_harvest;

    $parser = xml_parser_create();

    xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8");
    xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
    xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 0);
    xml_set_element_handler($parser, 'tagStart', 'tagEnd');
    xml_set_character_data_handler($parser, 'tagContent');

    global $xml_handlers;
    $xml_handlers = array();
    $tophandler = new XmlHandler(null, null); // Top level handler
    $xml_handlers[] = $tophandler;

    xml_parse($parser, trim($xml));

    xml_parser_free($parser);

    return $tophandler->get_top();
}



function PANIC($f,$s) {
    echo "PANIC: $f line $s\n";
    die;
}
