<?php

class Migration_Gdpr extends CI_Migration {

    // Add grading field to sta_quiz table

    public function __construct() {
        parent::__construct();

		$CI =& get_instance();
        $CI->language = 'english';
    }

	public function up() {
        $this->dbforge->add_column('user', array('accept_policy' => array('type' => 'INT',
                                                                          'null' => false,
                                                                          'default' => 0),
                                                 'policy_lang'   => array('type' => 'TINYTEXT',
                                                                          'null' => true,
                                                                          'default' => null),
                                                 'acc_code'      => array('type' => 'TINYTEXT',
                                                                          'null' => true,
                                                                          'default' => null),
                                                 'acc_code_time' =>  array('type' => 'INT',
                                                                           'null' => false,
                                                                           'default' => 0)));
                                                                     
        echo "Fields added to user\n";
    }

	public function down()
	{
        echo "<pre>Downgrade not possible</pre>";
	}
}
