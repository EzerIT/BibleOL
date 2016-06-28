<?php

class Migration_Grading extends CI_Migration {

    // Add grading field to sta_quiz table

    public function __construct() {
        parent::__construct();

		$CI =& get_instance();
        $CI->language = 'english';
    }

	public function up() {
        $this->dbforge->add_column('sta_quiz', array('grading' => array('type' => 'TINYINT(4)',
                                                                        'null' => true,
                                                                        'default' => null)));
   }

	public function down()
	{
        echo "<pre>Downgrade not possible</pre>";
	}
}
