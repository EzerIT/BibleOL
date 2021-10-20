<?php

class Migration_Language_packs extends CI_Migration {

    public function __construct() {
        parent::__construct();
        
		$CI =& get_instance();
        $CI->language = 'english';
    }

	public function up() {
        // Create and populate 'translation_languages' table
        
		echo "Create '{$this->db->dbprefix}translation_languages'\n";
		$this->dbforge->add_field(array('id' => array('type' => 'INT', 'auto_increment' => true),			// Unique ID
										'abb' => array('type'=>'VARCHAR(20)', 'null' => false),				// Abbreviation
										'internal' => array('type'=>'TINYTEXT', 'null' => false),			// Key for PHP localization
										'native' => array('type'=>'TINYTEXT', 'null' => false),				// The native name of the language
										'iface_enabled' => array('type'=>'TINYINT(1)', 'null' => false),	// Interface translation enabled
										'heblex_enabled' => array('type'=>'TINYINT(1)', 'null' => false),	// Translation of Hebrew/Aramaic lexicon enabled
										'greeklex_enabled' => array('type'=>'TINYINT(1)', 'null' => false), // Translation of Greek lexicon enabled
									  ));
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->add_key('abb');
		$this->dbforge->create_table('translation_languages');


        $langnames = array('da'      => array('danish',       'Dansk'),
                           'en'		 => array('english',	  'English'),
                           'de'		 => array('german',		  'Deutsch'),
                           'fr'		 => array('french',		  'Français'),
                           'nl'		 => array('dutch',		  'Nederlands'),
                           'pt'		 => array('portuguese',	  'Português'),
                           'es'		 => array('spanish',	  'Español'),
                           'zh-Hans' => array('simp_chinese', '中文（简体）'),
                           'zh-Hant' => array('trad_chinese', '中文（繁體）'),
                           'am'		 => array('amharic',	  'አማርኛ'),
                           'sw'      => array('swahili',      'Kiswahili'));

        
        foreach ($langnames as $abb => $lang)
            $this->db->insert('translation_languages', array('abb'              => $abb,
                                                             'internal'         => $lang[0],
                                                             'native'           => $lang[1],
                                                             'iface_enabled'    => false,
                                                             'heblex_enabled'   => false,
                                                             'greeklex_enabled' => false));

        foreach (array('am', 'da', 'de', 'en', 'es', 'fr', 'nl', 'pt', 'zh-Hans', 'zh-Hant') as $iflang)
            $this->db->where('abb',$iflang)->update('translation_languages',array('iface_enabled' => true));
            
        foreach (array('am', 'da', 'de', 'en', 'es', 'nl', 'sw') as $heblang)
            $this->db->where('abb',$heblang)->update('translation_languages',array('heblex_enabled' => true));

        foreach (array('am', 'en', 'nl', 'sw') as $greeklang)
            $this->db->where('abb',$greeklang)->update('translation_languages',array('greeklex_enabled' => true));

        
 
		// Rename 'language_zh-simp' to 'language_zh-Hans'
		// Rename 'language_zh-trad' to 'language_zh-Hant'
 
		$alltables = $this->db->list_tables(true);
		foreach ($alltables as $table) {
			if (preg_match("/^{$this->db->dbprefix}language_zh-([^_]+)(_.+)?$/", $table, $matches)) {
 
				// $matches[1]: 'simp' or 'trad'
				// $matches[2]: Optional variant
 
				switch ($matches[1]) {
				  case 'simp':
						if (isset($matches[2])) {
							echo "Rename {$this->db->dbprefix}language_zh-$matches[1]$matches[2] to {$this->db->dbprefix}language_zh-Hans$matches[2]\n";
							$this->dbforge->rename_table("language_zh-$matches[1]$matches[2]", "language_zh-Hans$matches[2]");
						}
						else  {
							echo "Rename {$this->db->dbprefix}language_zh-$matches[1] to {$this->db->dbprefix}language_zh-Hans\n";
							$this->dbforge->rename_table("language_zh-$matches[1]", "language_zh-Hans");
						}
						break;
				  case 'trad':
						if (isset($matches[2])) {
							echo "Rename {$this->db->dbprefix}language_zh-$matches[1]$matches[2] to {$this->db->dbprefix}language_zh-Hant$matches[2]\n";
							$this->dbforge->rename_table("language_zh-$matches[1]$matches[2]", "language_zh-Hant$matches[2]");
						}
						else  {
							echo "Rename {$this->db->dbprefix}language_zh-$matches[1] to {$this->db->dbprefix}language_zh-Hant\n";
							$this->dbforge->rename_table("language_zh-$matches[1]", "language_zh-Hant");
						}
						break;
				}
			}
		}

        
		// Change 'zh-simp' entry in db_localize to 'zh-Hans'
		// Change 'zh-trad' entry in db_localize to 'zh-Hant'
 
		echo "Rename 'zh-simp' and 'zh-trad' in {$this->db->dbprefix}db_localize\n";
		
		$this->db->where('lang','zh-simp')->update('db_localize',array('lang' => 'zh-Hans'));
		$this->db->where('lang','zh-trad')->update('db_localize',array('lang' => 'zh-Hant'));
    }
    
	public function down()
	{
        echo "<pre>Downgrade not possible</pre>";
	}
}
