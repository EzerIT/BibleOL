<?php

class Migration_Vulgate extends CI_Migration {
    // Change name of icons in bol_heb_urls

    public function __construct() {
        parent::__construct();
        
		$CI =& get_instance();
        $CI->language = 'english';
    }

	public function up() {
//    	$this->dbforge->add_column('translation_languages', array('latinlex_enabled' => array('type'=>'tinyint(1)',
//                                                                                              'null' => false,
//                                                                                              'default' => 0)));


		echo "Create {$this->db->dbprefix}lexicon_latin\n";
 
		$this->dbforge->add_field(['id'				=> ['type' => 'INT',		 'null' => false, 'auto_increment' => true ],
								   'lemma'			=> ['type' => 'VARCHAR(45)', 'null' => false],
								   'part_of_speech' => ['type' => 'VARCHAR(35)', 'null' => false],
								   'tally'			=> ['type' => 'INT',		 'null' => false ],
								   'sortorder'		=> ['type' => 'VARCHAR(45)', 'null' => false],
								   'firstbook'		=> ['type' => 'TINYTEXT',	 'null' => false],
								   'firstchapter'	=> ['type' => 'INT',		 'null' => false],
								   'firstverse'		=> ['type' => 'INT',		 'null' => false]]);
		$this->dbforge->add_key('id', true);
		$this->dbforge->add_key('lemma');
		$this->dbforge->add_key('part_of_speech');
		$this->dbforge->create_table('lexicon_latin');


        echo "Remember to add data to {$this->db->dbprefix}lexicon_latin\n";

        
        die;
    }
    
	public function down()
	{
        echo "<pre>Downgrade not possible</pre>";
	}
}
