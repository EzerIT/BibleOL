<?php

class Template extends XmlHandler {
    /************************************************************************************************
     * Class version for XML serialisation
     ************************************************************************************************/
    const classVersion = 6; // Version 5: Accepts <fixedquestions> element

	/************************************************************************************************
	 * Template data
	 ************************************************************************************************/

	public $desc;
	public $database;
	public $properties;
    public $selectedPaths = array();
    public $sentenceSelection;
    public $quizObjectSelection;
    public $quizFeatures;
    public $maylocate = true;  // Default value for older versions of quiz templates
    public $sentbefore = 0;  // Default value for older versions of quiz templates
    public $sentafter = 0;  // Default value for older versions of quiz templates
    public $fixedquestions = 0;  // Default value for older versions of quiz templates

	/************************************************************************************************
	 * XML writer interface
	 ************************************************************************************************/
	static public function writeAsXml($quizdata, stdClass $dbInfo=null) {
		$res = sprintf("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");
		$res .= sprintf("<questiontemplate version=\"%d\">\n", self::classVersion);

		$res .= sprintf("%2s<desc><![CDATA[%s]]></desc>\n", ' ', $quizdata->desc);

		if ($dbInfo && $dbInfo->subsetOf) {
			// We found a subset; store information about superset database
			$res .= sprintf("%2s<database>%s</database>\n", ' ', htmlspecialchars($dbInfo->subsetOf->name));
			$res .= sprintf("%2s<properties>%s</properties>\n", ' ', htmlspecialchars($dbInfo->subsetOf->properties));

			// If the exercise has a complete universe, but the database is a subset, reduce the universe to what the subset provides
			if (empty($quizdata->selectedPaths)
                || (count($quizdata->selectedPaths)==1 && empty($quizdata->selectedPaths[0]))) {
				// Complete universe is used; print subset's universe
				foreach ($dbInfo->subsetOf->provides as $s)
					$res .= sprintf("%2s<path>%s</path>\n", ' ', htmlspecialchars($s));
			}
			else {
				// Limited universe is used; print it
				foreach ($quizdata->selectedPaths as $s)
					$res .= sprintf("%2s<path>%s</path>\n", ' ', htmlspecialchars($s));
			}
		}
		else {
			// Store information about this database
			$res .= sprintf("%2s<database>%s</database>\n", ' ', htmlspecialchars($quizdata->database));
			$res .= sprintf("%2s<properties>%s</properties>\n", ' ', htmlspecialchars($quizdata->properties));

            if (empty($quizdata->selectedPaths))
				$res .= sprintf("%2s<path></path>\n", ' ');
            else 
                foreach ($quizdata->selectedPaths as $s)
                    $res .= sprintf("%2s<path>%s</path>\n", ' ', htmlspecialchars($s));
		}

        $res .= MqlData::writeAsXml($quizdata->sentenceSelection, "sentenceselection", true);
    	if (!$quizdata->sentenceSelection->useForQo)
            $res .= MqlData::writeAsXml($quizdata->quizObjectSelection, "quizobjectselection", false);
        
        $res .= QuizFeatures::writeAsXml($quizdata->quizFeatures);

        $res .= sprintf("%2s<maylocate>%s</maylocate>\n", ' ', $quizdata->maylocate ? "true" : "false");
        $res .= sprintf("%2s<sentbefore>%s</sentbefore>\n", ' ', $quizdata->sentbefore);
        $res .= sprintf("%2s<sentafter>%s</sentafter>\n", ' ', $quizdata->sentafter);
        $res .= sprintf("%2s<fixedquestions>%d</fixedquestions>\n", ' ', $quizdata->fixedquestions);
		$res .= sprintf("</questiontemplate>\n", ' ');

        return $res;
	}


	/************************************************************************************************
	 * XML reader interface
	 ************************************************************************************************/

    function open_handler(&$handlers, $tagname, $attributes) {
        switch ($tagname) {
          case 'path':
                $this->setthis = &$this->selectedPaths;
                $this->setthis_type = SetThisType::PUSH;
                break;

          case 'sentenceselection':
                $this->sentenceSelection = new MqlData($tagname, $attributes);
				$handlers[] = $this->sentenceSelection;
                break;

          case 'quizobjectselection':
                $this->quizObjectSelection = new MqlData($tagname, $attributes);
				$handlers[] = $this->quizObjectSelection;
                break;

          case 'quizfeatures':
                $this->quizFeatures = new QuizFeatures($tagname, $attributes);
                $handlers[] = $this->quizFeatures;
                break;

          case 'desc':
                $this->setthis = &$this->desc;
                $this->setthis_type = SetThisType::SET;
                break;

          case 'database':
                $this->setthis = &$this->database;
                $this->setthis_type = SetThisType::SET;
                break;

          case 'properties':
                $this->setthis = &$this->properties;
                $this->setthis_type = SetThisType::SET;
                break;

          case 'maylocate':
                $this->setthis = &$this->maylocate;
                $this->setthis_type = SetThisType::SET_BOOL;
                break;
                
          case 'sentbefore':
                $this->setthis = &$this->sentbefore;
                $this->setthis_type = SetThisType::SET_NUM;
                break;
                
          case 'sentafter':
                $this->setthis = &$this->sentafter;
                $this->setthis_type = SetThisType::SET_NUM;
                break;
                
          case 'fixedquestions':
                $this->setthis = &$this->fixedquestions;
                $this->setthis_type = SetThisType::SET_NUM;
                break;
                
          default:
                PANIC(__FILE__,__LINE__);
                break;
        }
    }

    function close_handler(&$handlers, $tagname) {
        switch ($tagname) {
          case 'path':
          case 'desc':
          case 'database':
          case 'properties':
          case 'maylocate':
          case 'sentbefore':
          case 'sentafter':
          case 'fixedquestions':
                // Ignore
                break;
                
          case 'questiontemplate':
                if ($this->sentenceSelection->useForQo && !isset($this->quizObjectSelection))
                    $this->quizObjectSelection = clone $this->sentenceSelection;
                if (!isset($this->properties))
                    $this->properties = $this->database;

                parent::close_handler($handlers, $tagname);
                break;

          default:
                PANIC(__FILE__,__LINE__);
                break;
        }
    }
}

class MqlData extends XmlHandler {
	/************************************************************************************************
	 * Class version for XML serialisation
	 ************************************************************************************************/
    const classVersion = 1;


	/************************************************************************************************
	 * Template data
	 ************************************************************************************************/
	public $object;
	public $mql;
	public $featHand;
	public $useForQo; // Only relevant for sentence selection data

	/************************************************************************************************
	 * Methods and auxiliary fields
	 ************************************************************************************************/

    public function __clone() {
        $this->featHand = clone $this->featHand;
    }

	/************************************************************************************************
	 * XML writer interface
	 ************************************************************************************************/

	static public function writeAsXml($selection, string $element, bool $isSentenceSelector) {
		$res = sprintf("%2s<%s version=\"%d\">\n", ' ', $element, self::classVersion);

		$res .= sprintf("%4s<questionobject>%s</questionobject>\n", ' ', htmlspecialchars($selection->object));
		if ($selection->mql!=null)
			$res .= sprintf("%4s<mql>%s</mql>\n", ' ', htmlspecialchars($selection->mql));
		else
            $res .= FeatureHandlerList::writeAsXml($selection->featHand);

		if ($isSentenceSelector)
			$res .= sprintf("%4s<useforquizobjects>%s</useforquizobjects>\n", ' ', $selection->useForQo ? "true" : "false");

		$res .= sprintf("%2s</%s>\n", ' ', $element);

        return $res;
	}


	/************************************************************************************************
	 * XML reader interface
	 ************************************************************************************************/

    function open_handler(&$handlers, $tagname, $attributes) {
        switch ($tagname) {
          case 'featurehandlers':
                $this->featHand = new FeatureHandlerList($tagname, $attributes);
                $handlers[] = $this->featHand;
                break;

          case 'questionobject':
                $this->setthis = &$this->object;
                $this->setthis_type = SetThisType::SET;
                break;

          case 'mql':
                $this->setthis = &$this->mql;
                $this->setthis_type = SetThisType::SET;
                break;

          case 'useforquizobjects':
                $this->setthis = &$this->useForQo;
                $this->setthis_type = SetThisType::SET_BOOL;
                break;

          default:
                PANIC(__FILE__,__LINE__);
                break;
        }
    }

    function close_handler(&$handlers, $tagname) {
        switch ($tagname) {
          case 'questionobject':
          case 'mql':
          case 'useforquizobjects':
                // Ignore
                break;

          case 'sentenceselection':
          case 'quizobjectselection':
                parent::close_handler($handlers, $tagname);
                break;

          default:
                PANIC(__FILE__,__LINE__);
                break;
        }
    }
}

class FeatureHandlerList extends XmlHandler {
	/************************************************************************************************
	 * Class version for XML serialisation
	 ************************************************************************************************/
    /** Maximum acceptable version number in the {@literal <featurehandlers>} element. */
	const classVersion = 3;  // Version 3: <qerefeature> added


	/************************************************************************************************
	 * Template data
	 ************************************************************************************************/
    /** The collection of feature handlers. */
    public $vhand = array();


	/************************************************************************************************
	 * Methods and auxiliary fields
	 ************************************************************************************************/
    /** Make a deep copy of the {@code FeatureHandlerList} object. */
    public function __clone() {
        foreach ($this->vhand as $i=>$fh)
            $this->vhand[$i] = clone $fh;
    }


    /** Converts the {@code FeatureHandlerList} to an MQL expression.
     * @return An MQL representation of the collection of feature selectors.
     */
    public function __toString() {
        $first = true;

        $sb = '';

        foreach ($this->vhand as $fh) {
            if ($fh->hasValues()) {
                if ($first)
                    $first = false;
                else
                    $sb .= ' AND ';
                $sb .= $fh;
            }
        }
        return $sb;
    }

	/************************************************************************************************
	 * XML writer interface
	 ************************************************************************************************/
    /** Writes the feature handlers as an XML string.
     * @param ps The output stream to which to write the XML string.
     */
	static public function writeAsXml($featureHandlerList) {
		$res = sprintf("%4s<featurehandlers version=\"%d\">\n", ' ', self::classVersion);

        foreach ($featureHandlerList->vhand as $fh) {
            switch ($fh->type) {
              case 'stringfeature':
                    $res .= StringFeatureHandler::writeAsXml($fh);
                    break;

              case 'integerfeature':
                    $res .= IntegerFeatureHandler::writeAsXml($fh);
                    break;

              case 'rangeintegerfeature':
                    $res .= RangeIntegerFeatureHandler::writeAsXml($fh);
                    break;

              case 'enumfeature':
                    $res .= EnumFeatureHandler::writeAsXml($fh);
                    break;

              case 'enumlistfeature':
                    $res .= EnumListFeatureHandler::writeAsXml($fh);
                    break;
                    
              case 'qerefeature':
                    $res .= QereFeatureHandler::writeAsXml($fh);
                    break;
            }
        }

		$res .= sprintf("%4s</featurehandlers>\n", ' ');

        return $res;
	}

	/************************************************************************************************
	 * XML reader interface
	 ************************************************************************************************/

    /** Called when an XML start element is parsed.
     * @param uri Not used.
     * @param name Not used.
     * @param qName The name of the XML element.
     * @param atts The attributes attached to the element.
     * @throws SAXException if the element is unknown.
     */
    function open_handler(&$handlers, $tagname, $attributes) {
        switch ($tagname) {
          case 'stringfeature':
                $fh = new StringFeatureHandler($tagname, $attributes);
                $handlers[] = $fh;
                $this->vhand[] = $fh;
                break;

          case 'integerfeature':
                $fh = new IntegerFeatureHandler($tagname, $attributes);
                $handlers[] = $fh;
                $this->vhand[] = $fh;
                break;

          case 'rangeintegerfeature':
                $fh = new RangeIntegerFeatureHandler($tagname, $attributes);
                $handlers[] = $fh;
                $this->vhand[] = $fh;
                break;

          case 'enumfeature':
                $fh = new EnumFeatureHandler($tagname, $attributes);
                $handlers[] = $fh;
                $this->vhand[] = $fh;
                break;

          case 'enumlistfeature':
                $fh = new EnumListFeatureHandler($tagname, $attributes);
                $handlers[] = $fh;
                $this->vhand[] = $fh;
                break;

          case 'qerefeature':
                $fh = new QereFeatureHandler($tagname, $attributes);
                $handlers[] = $fh;
                $this->vhand[] = $fh;
                break;

          default:
                PANIC(__FILE__,__LINE__);
                break;
        }
    }

    function close_handler(&$handlers, $tagname) {
        switch ($tagname) {
          case 'featurehandlers':
                parent::close_handler($handlers, $tagname);
                break;

          default:
                PANIC(__FILE__,__LINE__);
                break;
        }
    }
}

abstract class FeatureHandler extends  XmlHandler {
    /************************************************************************************************
	 * Template data
	 ************************************************************************************************/

    /** Feature name. */
    public $name;

    /** Comparison operator to use when comparing features to specified values. */
    public $comparator; // Comparison operator


	/************************************************************************************************
	 * Methods, and auxiliary fields
	 ************************************************************************************************/

    /** Gets the feature comparator as a string.
     * @return "=", "<>" or "~".
     */
	protected function getComparator() {
		switch ($this->comparator) {
		  case 'equals': return '=';
		  case 'differs': return '<>';
		  case 'matches': return '~';
		}
		return '';
	}

    /** Gets the boolean operator to use when stringing expressions together.
     * <p>
     * Equality and match comparisons are strung together with logical or.<br/>
     * Inequality comparisons are strung together with logical and.
     * @return "OR" or "AND" surrounded by spaces.
     */
	protected function getJoiner() {
		switch ($this->comparator) {
		  case 'equals': return ' OR ';
		  case 'differs': return ' AND ';
		  case 'matches': return ' OR ';
		}
		return '';
	}


    /** Tests if there are any values associated with the selector.
     * @return True if there are any values associated with the selector.
     */
    abstract public function hasValues();
}

class StringFeatureHandler extends FeatureHandler {
	/************************************************************************************************
	 * Class version for XML serialisation
	 ************************************************************************************************/
    /** Maximum acceptable version number in the {@literal <stringfeature>} element. */
    const classVersion = 1;


	/************************************************************************************************
	 * Template data
	 ************************************************************************************************/
    /** Values against which the feature will be compared. */
	public $values = array();	  // Feature values
    public $type = 'stringfeature';


	/************************************************************************************************
	 * Methods, and auxiliary fields
	 ************************************************************************************************/

    /** Tests if there are any values in {@link #m_values}.
     * @return True if there are any values associated with the selector.
     */
	public function hasValues() {
        return count($this->values)>0;
	}

    /** Converts the feature selector to an MQL expression.
     * @return An MQL representation of the feature selector.
     */
	public function __toString() {
		$comparator = $this->getComparator();

		$stringValues = array();
        foreach ($this->values as $v)
            $stringValues[] = "$this->name $comparator \"$v\"";

		if (count($stringValues)===1)
			return $stringValues[0];

		return '(' . implode($this->getJoiner(), $stringValues) . ')';
	}

	/************************************************************************************************
	 * XML writer interface
	 ************************************************************************************************/
    /** Writes the feature handler as an XML string.
     * @param ps The output stream to which to write the XML string.
     */
	static public function writeAsXml($featureHandler) {
		// Check if there are any values set at all
		$empty = true;
        foreach ($featureHandler->values as $v)
            $empty = empty($v) ? $empty : false;

		if ($empty)
			return '';

		$res = sprintf("%6s<stringfeature version=\"%d\">\n", ' ', self::classVersion);

		$res .= sprintf("%8s<name>%s</name>\n", ' ', htmlspecialchars($featureHandler->name));
        $res .= sprintf("%8s<comparator>%s</comparator>\n", ' ', $featureHandler->comparator);

        foreach ($featureHandler->values as $v)
			if (!empty($v))
				$res .= sprintf("%8s<value>%s</value>\n", ' ', htmlspecialchars($v));

		$res .= sprintf("%6s</stringfeature>\n", ' ');

        return $res;
	}


	/************************************************************************************************
	 * XML reader interface
	 ************************************************************************************************/

    /** Called when an XML start element is parsed.
     * @param uri Not used.
     * @param name Not used.
     * @param qName The name of the XML element.
     * @param atts The attributes attached to the element.
     * @throws SAXException if the element is unknown.
     */
    function open_handler(&$handlers, $tagname, $attributes) {
        switch ($tagname) {
          case 'name':
                $this->setthis = &$this->name;
                $this->setthis_type = SetThisType::SET;
                break;

          case 'comparator':
                $this->setthis = &$this->comparator;
                $this->setthis_type = SetThisType::SET;
                break;

          case 'value':
                $this->setthis = &$this->values;
                $this->setthis_type = SetThisType::PUSH_APPEND;
                break;

          default:
                PANIC(__FILE__,__LINE__);
                break;
        }
	}

    function close_handler(&$handlers, $tagname) {
        switch ($tagname) {
          case 'name':
          case 'comparator':
                // Ignore
                break;

          case 'value':
                $this->setthis_type = SetThisType::DONT_SET;
                break;

          case 'stringfeature':
                parent::close_handler($handlers, $tagname);
                break;

          default:
                PANIC(__FILE__,__LINE__);
                break;
        }
    }
}

class IntegerFeatureHandler extends FeatureHandler {
	/************************************************************************************************
	 * Class version for XML serialisation
	 ************************************************************************************************/
    /** Maximum acceptable version number in the {@literal <integerfeature>} element. */
    const classVersion = 1;


	/************************************************************************************************
	 * Template data
	 ************************************************************************************************/
    /** Values against which the feature will be compared. */
	public $values = array();	  // Feature values
    public $type = 'integerfeature';


	/************************************************************************************************
	 * Methods, and auxiliary fields
	 ************************************************************************************************/

	/** Tests if there are any values in {@link #m_values}.
	 * @return True if there are any values associated with the selector.
	 */
	public function hasValues() {
        return count($this->values)>0;
	}

    /** Converts the feature selector to an MQL expression.
     * @return An MQL representation of the feature selector.
     */

	public function __toString() {
		$counter = 0;
		$onlyValue = 0;	// If there is only one value, this is it

        foreach ($this->values as $v) {
            ++$counter;
            $onlyValue = $v;
        }

		if ($counter===1)
			return $this->name . $this->getComparator() . $onlyValue;


		return ($this->comparator=='differs' ? 'NOT ' : '')
            . "$this->m_name IN (" . implode(',', $this->values) . ')';
	}

	/************************************************************************************************
	 * XML writer interface
	 ************************************************************************************************/
	/** Writes the feature handler as an XML string.
	 * @param ps The output stream to which to write the XML string.
	 */
	static public function writeAsXml($featureHandler) {
		// Check if there are any values set at all
		$empty = true;
        foreach ($featureHandler->values as $v)
            $empty = empty($v) ? $empty : false;

		if ($empty)
			return '';

		$res = sprintf("%6s<integerfeature version=\"%d\">\n", ' ', self::classVersion);

		$res .= sprintf("%8s<name>%s</name>\n", ' ', htmlspecialchars($featureHandler->name));
        $res .= sprintf("%8s<comparator>%s</comparator>\n", ' ', $featureHandler->comparator);

        foreach ($featureHandler->values as $v)
			if (!empty($v))
				$res .= sprintf("%8s<value>%s</value>\n", ' ', htmlspecialchars($v));

		$res .= sprintf("%6s</integerfeature>\n", ' ');

        return $res;
	}


	/************************************************************************************************
	 * XML reader interface
	 ************************************************************************************************/

    function open_handler(&$handlers, $tagname, $attributes) {
        switch ($tagname) {
          case 'name':
                $this->setthis = &$this->name;
                $this->setthis_type = SetThisType::SET;
                break;

          case 'comparator':
                $this->setthis = &$this->comparator;
                $this->setthis_type = SetThisType::SET;
                break;

          case 'value':
                $this->setthis = &$this->values;
                $this->setthis_type = SetThisType::PUSH_NUM;
                break;

          default:
                PANIC(__FILE__,__LINE__);
                break;
        }
	}

    function close_handler(&$handlers, $tagname) {
        switch ($tagname) {
          case 'name':
          case 'comparator':
          case 'value':
                // Ignore
                break;

          case 'integerfeature':
                parent::close_handler($handlers, $tagname);
                break;

          default:
                PANIC(__FILE__,__LINE__);
                break;
        }
    }
}

class RangeIntegerFeatureHandler extends FeatureHandler {
	/************************************************************************************************
	 * Class version for XML serialisation
	 ************************************************************************************************/
    /** Maximum acceptable version number in the {@literal <rangeintegerfeature>} element. */
    const classVersion = 1;


	/************************************************************************************************
	 * Template data
	 ************************************************************************************************/
    /** Values against which the feature will be compared. */
	public $value_low, $value_high;

    /** Indicates if the corresponding value is set. */
	private $isSet_low=false, $isSet_high=false;

    public $type = 'rangeintegerfeature';

	/************************************************************************************************
	 * Methods, and auxiliary fields
	 ************************************************************************************************/

    /** Tests if a value is set.
     * @return True if there are any values associated with the selector.
     */
	public function hasValues() {
		return $this->isSet_low || $this->isSet_high;
	}

    /** Converts the feature selector to an MQL expression.
     * @return An MQL representation of the feature selector.
     */
	public function __toString() {
		if ($this->isSet_low) {
			if ($this->isSet_high)
				return "($this->name>=$this->value_low AND $this->name<=$this->value_high)";
			else
				return "$this->name>=$this->value_low";
		}
		else {
			if ($this->isSet_high)
				return "$this->name<=$this->value_high";
			else
				return '';
		}
	}

	/************************************************************************************************
	 * XML writer interface
	 ************************************************************************************************/
    /** Writes the feature handler as an XML string.
     * @param ps The output stream to which to write the XML string.
     */
	static public function writeAsXml($featureHandler) {
		if (empty($featureHandler->value_low) && empty($featureHandler->value_high))
			return '';

        $res = sprintf("%6s<rangeintegerfeature version=\"%d\">\n", ' ', self::classVersion);

		$res .= sprintf("%8s<name>%s</name>\n", ' ', htmlspecialchars($featureHandler->name));
		if (!empty($featureHandler->value_low))
			$res .= sprintf("%8s<valuelow>%d</valuelow>\n", ' ', $featureHandler->value_low);
		if (!empty($featureHandler->value_high))
			$res .= sprintf("%8s<valuehigh>%d</valuehigh>\n", ' ', $featureHandler->value_high);
		$res .= sprintf("%6s</rangeintegerfeature>\n", ' ');

        return $res;
	}


	/************************************************************************************************
	 * XML reader interface
	 ************************************************************************************************/

    function open_handler(&$handlers, $tagname, $attributes) {
        switch ($tagname) {
          case 'name':
                $this->setthis = &$this->name;
                $this->setthis_type = SetThisType::SET;
                break;

          case 'comparator':
                $this->setthis = &$this->comparator;
                $this->setthis_type = SetThisType::SET;
                break;

          case 'valuehigh':
                $this->isSet_high = true;
                $this->setthis = &$this->value_high;
                $this->setthis_type = SetThisType::SET_NUM;
                break;

          case 'valuelow':
                $this->isSet_low = true;
                $this->setthis = &$this->value_low;
                $this->setthis_type = SetThisType::SET_NUM;
                break;

          default:
                PANIC(__FILE__,__LINE__);
                break;
        }
	}

    function close_handler(&$handlers, $tagname) {
        switch ($tagname) {
          case 'name':
          case 'comparator':
          case 'valuelow':
          case 'valuehigh':
                // Ignore
                break;

          case 'rangeintegerfeature':
                parent::close_handler($handlers, $tagname);
                break;

          default:
                PANIC(__FILE__,__LINE__);
                break;
        }
    }
}

class EnumFeatureHandler extends FeatureHandler {
	/************************************************************************************************
	 * Class version for XML serialisation
	 ************************************************************************************************/

    /** Maximum acceptable version number in the {@literal <enumfeature>} element. */
    const classVersion = 1;


	/************************************************************************************************
	 * Template data
	 ************************************************************************************************/

    /** Values against which the feature will be compared. */
	public $values = array();	  // Feature values
    public $type = 'enumfeature';


	/************************************************************************************************
	 * Methods, and auxiliary fields
	 ************************************************************************************************/

	/** Tests if there are any values in {@link #m_values}.
	 * @return True if there are any values associated with the selector.
	 */
	public function hasValues() {
		return count($this->values)>0;
	}

 
	/** Converts the feature selector to an MQL expression.
	 * @return An MQL representation of the feature selector.
	 */
	public function __toString() {
		return ($this->comparator=='differs' ? 'NOT ' : '')
			. "$this->name IN (" . implode(',', $this->values) . ')';
	}

	/************************************************************************************************
	 * XML writer interface
	 ************************************************************************************************/
	/** Writes the feature handler as an XML string.
	 * @param ps The output stream to which to write the XML string.
	 */

	static public function writeAsXml($featureHandler) {
		if (empty($featureHandler->values))
			return '';
 
		$res = sprintf("%6s<enumfeature version=\"%d\">\n", ' ', self::classVersion);
		$res .= sprintf("%8s<name>%s</name>\n", ' ', htmlspecialchars($featureHandler->name));
        $res .= sprintf("%8s<comparator>%s</comparator>\n", ' ', $featureHandler->comparator);

		foreach ($featureHandler->values as $s)
			$res .= sprintf("%8s<value>%s</value>\n", ' ', htmlspecialchars($s));
		$res .= sprintf("%6s</enumfeature>\n", ' ');

        return $res;
	}
 
 
	/************************************************************************************************
	 * XML reader interface
	 ************************************************************************************************/

    function open_handler(&$handlers, $tagname, $attributes) {
        switch ($tagname) {
          case 'name':
                $this->setthis = &$this->name;
                $this->setthis_type = SetThisType::SET;
                break;

          case 'comparator':
                $this->setthis = &$this->comparator;
                $this->setthis_type = SetThisType::SET;
                break;

          case 'value':
                $this->setthis = &$this->values;
                $this->setthis_type = SetThisType::PUSH;
                break;

          default:
                PANIC(__FILE__,__LINE__);
                break;
        }
	}

    function close_handler(&$handlers, $tagname) {
        switch ($tagname) {
          case 'name':
          case 'comparator':
          case 'value':
                // Ignore
                break;

          case 'enumfeature':
                parent::close_handler($handlers, $tagname);
                break;

          default:
                PANIC(__FILE__,__LINE__);
                break;
        }
    }
}

class EnumListFeatureHandler extends FeatureHandler {
	/************************************************************************************************
	 * Class version for XML serialisation
	 ************************************************************************************************/

    /** Maximum acceptable version number in the {@literal <enumlistfeature>} element. */
    const classVersion = 1;


	/************************************************************************************************
	 * Template data
	 ************************************************************************************************/

    /** The collection of listvalues handlers. */
    public $listvalues = array();

    public $type = 'enumlistfeature';


	/************************************************************************************************
	 * Methods, and auxiliary fields
	 ************************************************************************************************/

    /** Make a deep copy of the {@code FeatureHandlerList} object. */
    public function __clone() {
        foreach ($this->listvalues as $i=>$lv)
            $this->listvalues[$i] = clone $lv;
    }

	/** Tests if there are any values in {@link #m_values}.
	 * @return True if there are any values associated with the selector.
	 */
	public function hasValues() {
        foreach ($this->listvalues as $lv)
            if ($lv->hasValues())
                return true;
        return false;
	}

 
	/** Converts the feature selector to an MQL expression.
	 * @return An MQL representation of the feature selector.
	 */
	public function __toString() {
        $first = true;

        $sb = '(';

        foreach ($this->listvalues as $lv) {
            if ($lv->hasValues()) {
                if ($first)
                    $first = false;
                else
                    $sb .= ' OR ';
                $sb .= $lv;
            }
        }
        return $sb . ')';
    }

	/************************************************************************************************
	 * XML writer interface
	 ************************************************************************************************/
	/** Writes the feature handler as an XML string.
	 * @param ps The output stream to which to write the XML string.
	 */

	static public function writeAsXml($featureHandler) {
		if (empty($featureHandler->listvalues))
			return '';
 
		$res = sprintf("%6s<enumlistfeature version=\"%d\">\n", ' ', self::classVersion);
		$res .= sprintf("%8s<name>%s</name>\n", ' ', htmlspecialchars($featureHandler->name));

		foreach ($featureHandler->listvalues as $lv)
            $res .= ListValuesHandler::writeAsXml($lv);

		$res .= sprintf("%6s</enumlistfeature>\n", ' ');

        return $res;
	}
 
 
	/************************************************************************************************
	 * XML reader interface
	 ************************************************************************************************/

    function open_handler(&$handlers, $tagname, $attributes) {
        switch ($tagname) {
          case 'name':
                $this->setthis = &$this->name;
                $this->setthis_type = SetThisType::SET;
                break;

          case 'listvalues':
                $lv = new ListValuesHandler($tagname, $attributes, $this);
                $handlers[] = $lv;
                $this->listvalues[] = $lv;
                break;

          default:
                PANIC(__FILE__,__LINE__);
                break;
        }
	}

    function close_handler(&$handlers, $tagname) {
        switch ($tagname) {
          case 'name':
          case 'listvalues':
                // Ignore
                break;

          case 'enumlistfeature':
                parent::close_handler($handlers, $tagname);
                break;

          default:
                PANIC(__FILE__,__LINE__);
                break;
        }
    }
}

class QereFeatureHandler extends FeatureHandler {
	/************************************************************************************************
	 * Class version for XML serialisation
	 ************************************************************************************************/
    /** Maximum acceptable version number in the {@literal <qerefeature>} element. */
    const classVersion = 1;


	/************************************************************************************************
	 * Template data
	 ************************************************************************************************/
    /** Values against which the feature will be compared. */
	public $omit = false;	  // Feature value
    public $type = 'qerefeature';


	/************************************************************************************************
	 * Methods, and auxiliary fields
	 ************************************************************************************************/

    /** Tests if there are any values in {@link #m_values}.
     * @return True if there are any values associated with the selector.
     */
	public function hasValues() {
        return $this->omit;
	}

    /** Converts the feature selector to an MQL expression.
     * @return An MQL representation of the feature selector.
     */
	public function __toString() {
        if ($this->omit)
            return "($this->name='' AND g_word_translit<>'HÎʔ')";
        else
            return '';
	}

	/************************************************************************************************
	 * XML writer interface
	 ************************************************************************************************/
    /** Returns the feature handler as an XML string.
     */
	static public function writeAsXml($featureHandler) {
        if ($featureHandler->omit)
            return sprintf("%6s<qerefeature version=\"%d\">\n", ' ', self::classVersion)
             . sprintf("%8s<name>%s</name>\n", ' ', htmlspecialchars($featureHandler->name))
			 . sprintf("%8s<value>true</value>\n", ' ')
			 . sprintf("%6s</qerefeature>\n", ' ');
        else
            return '';
	}


	/************************************************************************************************
	 * XML reader interface
	 ************************************************************************************************/

    /** Called when an XML start element is parsed.
     * @param uri Not used.
     * @param name Not used.
     * @param qName The name of the XML element.
     * @param atts The attributes attached to the element.
     * @throws SAXException if the element is unknown.
     */
    function open_handler(&$handlers, $tagname, $attributes) {
        switch ($tagname) {
          case 'name':
                $this->setthis = &$this->name;
                $this->setthis_type = SetThisType::SET;
                break;

          case 'value':
                $this->setthis = &$this->omit;
                $this->setthis_type = SetThisType::SET_BOOL;
                break;

          default:
                PANIC(__FILE__,__LINE__);
                break;
        }
	}

    function close_handler(&$handlers, $tagname) {
        switch ($tagname) {
          case 'name':
                // Ignore
                break;

          case 'value':
                $this->setthis_type = SetThisType::DONT_SET;
                break;

          case 'qerefeature':
                parent::close_handler($handlers, $tagname);
                break;

          default:
                PANIC(__FILE__,__LINE__);
                break;
        }
    }
}

class ListValuesHandler extends XmlHandler {
	/************************************************************************************************
	 * Class version for XML serialisation
	 ************************************************************************************************/

    /** Maximum acceptable version number in the {@literal <enumlistfeature>} element. */
    const classVersion = 1;


	/************************************************************************************************
	 * Template data
	 ************************************************************************************************/

    /** Values against which the feature will be compared. */
	public $yes_values = array();	  // Feature values
	public $no_values = array();	  // Feature values
    public $type = 'listvalues';

    private $my_parent; // Parent EnumListFeatureHandler (used for fetching feature name)


	/************************************************************************************************
	 * Methods, and auxiliary fields
	 ************************************************************************************************/

    function __construct($tagname, $attributes, $parent) {
        parent::__construct($tagname, $attributes);
        
        $this->my_parent = $parent;
    }


	/** Tests if there are any values in {@link #m_values}.
	 * @return True if there are any values associated with the selector.
	 */
	public function hasValues() {
		return count($this->yes_values) + count($this->no_values) > 0;
	}

 
	/** Converts the feature selector to an MQL expression.
	 * @return An MQL representation of the feature selector.
	 */
	public function __toString() {
		$stringValues = array();
        foreach ($this->yes_values as $v)
            $stringValues[] = "{$this->my_parent->name} HAS $v";

        foreach ($this->no_values as $v)
            $stringValues[] = "NOT {$this->my_parent->name} HAS $v";

		if (count($stringValues)===1)
			return $stringValues[0];

		return '(' . implode(' AND ', $stringValues) . ')';
	}

	/************************************************************************************************
	 * XML writer interface
	 ************************************************************************************************/
	/** Writes the feature handler as an XML string.
	 * @param ps The output stream to which to write the XML string.
	 */

    static public function writeAsXml($featureHandler) {
        // We cannot call hasValues because $featureHandler is a stdClass object, not a
        // ListValuesHandler object
		if (count($featureHandler->yes_values) + count($featureHandler->no_values) === 0)
            return "";

		$res = sprintf("%8s<listvalues version=\"%d\">\n", ' ', self::classVersion);
		foreach ($featureHandler->yes_values as $s)
			$res .= sprintf("%10s<yes>%s</yes>\n", ' ', htmlspecialchars($s));
		foreach ($featureHandler->no_values as $s)
			$res .= sprintf("%10s<no>%s</no>\n", ' ', htmlspecialchars($s));
		$res .= sprintf("%8s</listvalues>\n", ' ');

        return $res;
	}
 
 
	/************************************************************************************************
	 * XML reader interface
	 ************************************************************************************************/

    function open_handler(&$handlers, $tagname, $attributes) {
        switch ($tagname) {
          case 'yes':
                $this->setthis = &$this->yes_values;
                $this->setthis_type = SetThisType::PUSH;
                break;

          case 'no':
                $this->setthis = &$this->no_values;
                $this->setthis_type = SetThisType::PUSH;
                break;

          default:
                PANIC(__FILE__,__LINE__);
                break;
        }
	}

    function close_handler(&$handlers, $tagname) {
        switch ($tagname) {
          case 'yes':
          case 'no':
                // Ignore
                break;

          case 'listvalues':
                parent::close_handler($handlers, $tagname);
                break;

          default:
                PANIC(__FILE__,__LINE__);
                break;
        }
    }
}


class QuizFeatures extends XmlHandler {
    /************************************************************************************************
     * Class version for XML serialisation
     ************************************************************************************************/
    const classVersion = 5; // Version 5 is identical to version 4, except that other parts of the code
                            // now accept multiple show="..." values in <dontshowobject>.
                            // Version 4 added "dontshowobject"
                            // Version 3 added "dontshow"
                            // Version 2 added "requestdd"

	/************************************************************************************************
	 * Template data
	 ************************************************************************************************/
	public $showFeatures = array();
	public $requestFeatures = array();
	public $dontShowFeatures = array();
	public $dontShowObjects = array();

    public $hideFeatures; // Value of "hidefeatures" attribute in a <request> element
    
    /************************************************************************************************
	 * XML writer interface
	 ************************************************************************************************/
    static public function writeAsXml($quizfeatures) {
		$res = sprintf("%2s<quizfeatures version=\"%d\">\n", ' ', self::classVersion);

        foreach ($quizfeatures->showFeatures as $s)
			$res .= sprintf("%4s<show>%s</show>\n", ' ', htmlspecialchars($s));

        foreach ($quizfeatures->requestFeatures as $s) {
            if ($s->usedropdown)
				$res .= sprintf("%4s<requestdd>%s</requestdd>\n", ' ', htmlspecialchars($s->name));
			else if (!empty($s->hideFeatures))
				$res .= sprintf("%4s<request hidefeatures=\"%s\">%s</request>\n", ' ', implode(',',$s->hideFeatures), htmlspecialchars($s->name));
            else
				$res .= sprintf("%4s<request>%s</request>\n", ' ', htmlspecialchars($s->name));
        }

        foreach ($quizfeatures->dontShowFeatures as $s)
			$res .= sprintf("%4s<dontshow>%s</dontshow>\n", ' ', htmlspecialchars($s));

        foreach ($quizfeatures->dontShowObjects as $s) {
            if (isset($s->show))
                $res .= sprintf("%4s<dontshowobject show=\"%s\">%s</dontshowobject>\n", ' ', htmlspecialchars($s->show), htmlspecialchars($s->content));
            else
                $res .= sprintf("%4s<dontshowobject>%s</dontshowobject>\n", ' ', htmlspecialchars($s->content));
        }
        
		$res .= sprintf("%2s</quizfeatures>\n", ' ');

        return $res;
	}


	/************************************************************************************************
	 * XML reader interface
	 ************************************************************************************************/

    function open_handler(&$handlers, $tagname, $attributes) {
        switch ($tagname) {
          case 'show':
                $this->setthis = &$this->showFeatures;
                $this->setthis_type = SetThisType::PUSH;
                break;

          case 'request':
                $this->setthis = &$this->requestFeatures;
                $this->setthis_type = SetThisType::PUSH_REQUEST_FEATURES;
                $this->hideFeatures = isset($attributes['hidefeatures']) ? $attributes['hidefeatures'] : null;
                break;

          case 'requestdd':
                $this->setthis = &$this->requestFeatures;
                $this->setthis_type = SetThisType::PUSH_REQUESTDD_FEATURES;
                break;

          case 'dontshow':
                $this->setthis = &$this->dontShowFeatures;
                $this->setthis_type = SetThisType::PUSH;
                break;

          case 'dontshowobject':
                $this->setthis = &$this->dontShowObjects;
                $this->setthis_type = SetThisType::PUSH;
                $this->setthis_attribs = $attributes;
                break;

          default:
                PANIC(__FILE__,__LINE__);
                break;
        }
    }
    function close_handler(&$handlers, $tagname) {
        switch ($tagname) {
          case 'show':
          case 'request':
          case 'requestdd':
          case 'dontshow':
          case 'dontshowobject':
                // Ignore
                break;

          case 'quizfeatures':
                parent::close_handler($handlers, $tagname);
                break;

          default:
                PANIC(__FILE__,__LINE__);
                break;
        }
    }
}
