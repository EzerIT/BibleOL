<?php

  // Copyright © 2013 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk

class TypeInfo {
    public $objTypes = array();

    private function setObjTypes(Mql $mqle) {
        $emdros_data = $mqle->exec("SELECT OBJECT TYPES GOqxqxqx");
        $objTypeTable = $emdros_data[0]->get_table();
        $rows = $objTypeTable->rows();
        for ($i=0; $i<$rows; ++$i)
            $this->objTypes[] = $objTypeTable->get_cell($i,0);
    }

    public $obj2feat = array();
    // Maps each object type to its feature names and feature types
    // For example "word" -> ("case"->"case_t"),("gender"->"gender_t"),...

    private function setObj2feat(Mql $mqle) {
        $getFeaturesCmd = '';

        foreach ($this->objTypes as $ot)
            $getFeaturesCmd .= "SELECT FEATURES FROM [$ot] GOqxqxqx\n";

        $emdros_data = $mqle->exec($getFeaturesCmd);

        $size = count($emdros_data);

        if ($size!=count($this->objTypes))
            throw new Exception("Exepected ".count($this->objTypes)." tables but got $size");
            
        for ($objIx=0; $objIx<$size; ++$objIx) {
            $tab = $emdros_data[$objIx]->get_table();
            
            $rows = $tab->rows();
            $features = array();
            for ($i=0; $i<$rows; ++$i)
                $features[$tab->get_cell($i,0)] = $tab->get_cell($i,1);
             
            $this->obj2feat[$this->objTypes[$objIx]] = $features;
        }
    }

    public $enumTypes = array();

    private function setEnumType(Mql $mqle) {
		$emdros_data = $mqle->exec("SELECT ENUMERATIONS GOqxqxqx");
        $enumsTable = $emdros_data[0]->get_table();
        $rows = $enumsTable->rows();
        for ($i=0; $i<$rows; ++$i)
            $this->enumTypes[] = $enumsTable->get_cell($i,0);
    }

    public $enum2values = array();
    // Maps each feature type to its possible values
	// For example "case_t" --> "nominative","accusative",...

    private function setEnum2values(Mql $mqle) {
        $getEnumCmd = '';
 
        foreach ($this->enumTypes as $en)
            $getEnumCmd .= "SELECT ENUMERATION CONSTANTS FROM $en GOqxqxqx\n";

        $emdros_data = $mqle->exec($getEnumCmd);

        $size = count($emdros_data);

        if ($size!=count($this->enumTypes))
            throw new Exception("Expected ".count($this->objTypes)." got $size");

        for ($enumIx=0; $enumIx<$size; ++$enumIx) {
            $tab = $emdros_data[$enumIx]->get_table();

            $rows = $tab->rows();
            $values = array();
            for ($i=0; $i<$rows; ++$i)
                $values[] = $tab->get_cell($i,0);
            
            $this->enum2values[$this->enumTypes[$enumIx]] = $values;
        }

    }

    private function construct_from_mql(Mql $mqle) {
        $this->setObjTypes($mqle);
		$this->setObj2feat($mqle);
        $this->setEnumType($mqle);
        $this->setEnum2values($mqle);
    }

    // Usage:
    //    new TypeInfo(null); -- Generates new typeinfo from contents of database
    //    new TypeInfo('jsonstring');    -- Genereates typeinfo from contents of JSON string

    function __construct(string $typeinfo=null) {
        if (!$typeinfo) {
            // Construct from MQL
            $CI =& get_instance();
            $this->construct_from_mql($CI->mql);
        }
        else {
            // Construct from $typeinfo
            $val = json_decode($typeinfo);
            $this->objTypes = $val->objTypes;
            $this->obj2feat = $val->obj2feat;
            $this->enumTypes = $val->enumTypes;
            $this->enum2values = $val->enum2values;
        }
    }
}

?>