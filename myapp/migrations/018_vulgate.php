<?php

class Migration_Vulgate extends CI_Migration {
    // Change name of icons in bol_heb_urls

    public function __construct() {
        parent::__construct();
        
		$CI =& get_instance();
        $CI->language = 'english';
    }

	public function up() {
		$this->dbforge->add_column('translation_languages', array('latinlex_enabled' => array('type'=>'tinyint(1)',
                                                                                              'null' => false,
                                                                                              'default' => 0)));
        die;
    }
    
	public function down()
	{
        echo "<pre>Downgrade not possible</pre>";
	}
}
