<?php

class Migration_Teacheraccess extends CI_Migration {

    // Add grading field to sta_quiz table

    public function __construct() {
        parent::__construct();

		$CI =& get_instance();
        $CI->language = 'english';
    }

	public function up() {
        $this->dbforge->add_column('userclass', array('access' => array('type' => 'TINYINT(1)',
                                                                        'null' => false,
                                                                        'default' => 0)));
        echo "Access field added to userclass\n";
   }

	public function down()
	{
        echo "<pre>Downgrade not possible</pre>";
	}
}
