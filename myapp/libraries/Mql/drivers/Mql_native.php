<?php

if (version_compare(PHP_VERSION, '7.0', '>=')) {
    if (file_exists('/usr/local/lib/x86_64-linux-gnu/emdros/EmdrosPHP7.php'))
        require_once('/usr/local/lib/x86_64-linux-gnu/emdros/EmdrosPHP7.php');
    else
        require_once('/usr/local/lib/emdros/EmdrosPHP7.php');
}
else
    require_once('/usr/local/lib/emdros/EmdrosPHP.php');

class Mql_native extends CI_Driver {
    private $emdros_env;
    public $mql_list = '';
    public $mql_results;

    public function init() {
        $this->emdros_env = new EmdrosEnv(kOKConsole,
                                          kCSUTF8,
                                          "", // hostname, e.g., localhost
                                          "", // database user, e.g, emdf or ulrikp
                                          "0000000000 0000000000 0000000000 0000000000 0000000000 0000000000 0000000000 0000000000", // password
                                          $this->emdros_db, // database name
                                          kSQLite3); // database kind
    }

    public function exec(string $mql_cmd, $quick_harvest=false) {
        //echo "<pre>MQLnative:&gt;$mql_cmd&lt;MQL</pre>\n";
        $this->mql_list .= "$mql_cmd\n";

		$this->mql_results = array();

        $commands = preg_split("/\\bGOqxqxqx\\b/",$mql_cmd,-1,PREG_SPLIT_NO_EMPTY);

        foreach ($commands as $cmd) {
           if (strlen(trim($cmd))===0)
                continue; // $cmd contains only whitespace

            $arrResult = $this->emdros_env->executeString($cmd . ' GO', false, false);
            $bDBOK = $arrResult[0];
            $bCompilerOK = $arrResult[1];

            if (!$bDBOK)
                throw new MqlException($this->emdros_env->getDBError(), null);
            if (!$bCompilerOK)
                throw new MqlException(null, $this->emdros_env->getCompilerError());

			$this->mql_results[] = $this->emdros_env->takeOverResult();
		}

        if (!$quick_harvest) {
            // Normal harvest of sheaves or tables

            $crop = array(); // Vector<TableOrSheaf>
            foreach ($this->mql_results as $mqr) {
                $item = new TableOrSheaf();
                if ($mqr->isSheaf())
                    $item->set_sheaf($this->makeSheaf($mqr->getSheaf()));
                elseif ($mqr->isFlatSheaf())
                    $item->set_sheaf($this->makeSheaf($mqr->getFlatSheaf()));
                elseif ($mqr->isTable())
                    $item->set_table($this->makeTable($mqr->getTable()));
                $crop[] = $item;
            }
        }
        else {
            // Quick harvest. This means storing the monadsets directly in the top OlSheaf object
            $crop = array(); // Vector<Sheaf>
            foreach ($this->mql_results as $mqr) {
                assert($mqr->isSheaf());

                $item = new TableOrSheaf();

                $item->set_sheaf(new OlSheaf());

                $emdrosSheaf = $mqr->getSheaf();
                $shci = $emdrosSheaf->const_iterator();
                while ($shci->hasNext()) {
                    $emdrosStraw = $shci->next();
                    $strci = $emdrosStraw->const_iterator();
                    while ($strci->hasNext()) {
                        $emdrosMo = $strci->next();
                        $item->get_sheaf()->add_monadset(OlMonadSet::str2MonadSet($emdrosMo->getMonads()->toString()));
                    }
                }
                $crop[] = $item;
            }
		}

        return $crop;
    }

    private function makeSheaf(/* Sheaf OR FlatSheaf OR null */ $emdrosSheaf) {
        if (is_null($emdrosSheaf))
            return null;

        $shf = new OlSheaf();

        $sci = $emdrosSheaf->const_iterator();
        while ($sci->hasNext())
            $shf->add_straw($this->makeStraw($sci->next()));

        return $shf;
    }


    private function makeStraw(/* Straw OR FlatStraw */ $emdrosStraw) {
        $str = new OlStraw(null,null);

        $sci = $emdrosStraw->const_iterator();
        while ($sci->hasNext())
            $str->add_matched_object($this->makeMatchedObject($sci->next()));

        return $str;
    }

    private function makeMatchedObject(MatchedObject $emdrosMo) {
        $mob = new OlMatchedObject($emdrosMo->getID_D(), $emdrosMo->getObjectTypeName());

        $mob->set_sheaf($this->makeSheaf($emdrosMo->getSheaf()));
        $mob->set_monadset(OlMonadSet::str2MonadSet($emdrosMo->getMonads()->toString()));

        $featureListVector = $emdrosMo->getFeatureList()->getAsVector();
		for ($i=0; $i<$featureListVector->size(); ++$i) {
            $featureName = $featureListVector->get($i);
            $featureValue = $emdrosMo->getFeatureAsString($i);
            $mob->set_feature($featureName, $featureValue);
		}	
		
		return $mob;
    }

	// Note: This factory cannot set OlTable::$header
    private function makeTable(Table $emdrosTable) {
        $table = new OlTable(null,null);

        $ti = $emdrosTable->iterator();
        while ($ti->hasNext()) {
            $currentrow = array();
            $tr_object = new TableRow($ti->next());
            $tri = $tr_object->iterator();
            while ($tri->hasNext())
                $currentrow[] = $tri->next();

            $table->add_row($currentrow);
        }
		return $table;
    }
  }
