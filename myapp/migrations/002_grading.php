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
        echo "Grading field added to sta_quiz\n";

        $this->dbforge->add_field(array('id' => array('type' => 'INT', 'auto_increment' => true),
                                        'lex' => array('type' => 'TINYTEXT'),
                                        'language' => array('type' => "enum('Hebrew', 'Aramaic')"),
                                        'url' => array('type' => 'TEXT'),
                                        'icon' => array('type' => 'TINYTEXT')));
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('heb_urls');
        echo "Table heb_urls added\n";
   }

	public function down()
	{
        echo "<pre>Downgrade not possible</pre>";
	}
}
