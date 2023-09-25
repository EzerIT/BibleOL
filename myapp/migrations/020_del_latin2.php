<?php

class Migration_Del_latin2 extends CI_Migration {
    // Change name of icons in bol_heb_urls

    public function __construct() {
        parent::__construct();
        
		$CI =& get_instance();
        $CI->language = 'english';
    }

	public function up() {
        $dbprefix = $this->db->dbprefix;
        $this->db->set_dbprefix('');
        
		
		echo "Drop column latin2lex_enabled from {$dbprefix}translation_languages\n";
		$this->dbforge->drop_column("{$dbprefix}translation_languages", 'latin2lex_enabled');
 
		$tables=$this->db->list_tables(true);


		foreach ($tables as $tab) {
			if (preg_match("/{$dbprefix}lexicon_latin2_/",$tab)) {
				echo "Drop table $tab\n";
				$this->dbforge->drop_table($tab);
			}
 
			if (preg_match("/{$dbprefix}db_localize/",$tab)) {
				echo "Delete latin2 entries from $tab\n";
				$this->db->delete($tab,array('db' => 'VC'));
			}
 
 
			if (preg_match("/{$dbprefix}language_/",$tab)) {
				echo "Delete latin2 entries from $tab\n";
				$this->db->delete($tab,array('symbolic_name' => 'latin2_glosses'));
				$this->db->delete($tab,array('symbolic_name' => 'lang_latin2'));
			}
		}
 
		echo "Drop table {$dbprefix}lexicon_latin2\n";
		$this->dbforge->drop_table("{$dbprefix}lexicon_latin2");
        $this->db->set_dbprefix($dbprefix);
    }
    
	public function down()
	{
        echo "<pre>Downgrade not possible</pre>";
	}
}
