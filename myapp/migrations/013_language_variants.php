<?php

class Migration_Language_variants extends CI_Migration {
    // Change name of icons in bol_heb_urls

    public function __construct() {
        parent::__construct();
        
		$CI =& get_instance();
        $CI->language = 'english';
    }

	public function up() {
		$this->dbforge->add_column('user', array('prefvariant' => array('type'=>'tinytext',
                                                                        'null' => true,
                                                                        'default' => null)));
    }
    
	public function down()
	{
        echo "<pre>Downgrade not possible</pre>";
	}
}
