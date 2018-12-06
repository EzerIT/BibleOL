<?php

class Migration_Multiple_sites extends CI_Migration {
    // Change name of icons in bol_heb_urls

    public function __construct() {
        parent::__construct();
        
		$CI =& get_instance();
        $CI->language = 'english';
    }

	public function up() {
        $this->dbforge->add_field(array('id' => array('type' => 'INT', 'auto_increment' => true),
                                        'use' => array('type'=>'VARCHAR(25)'),
                                        'site' => array('type'=>'VARCHAR(25)',
                                                        'null' => true,
                                                        'default' => null),
                                        'language' => array('type'=>'VARCHAR(15)'),
                                        'text' => array('type'=>'LONGTEXT'),
                                      ));
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('use');
        $this->dbforge->add_key('site');
        $this->dbforge->add_key('language');
        $this->dbforge->create_table("sitetext");
    }
    
	public function down()
	{
        echo "<pre>Downgrade not possible</pre>";
	}
}
