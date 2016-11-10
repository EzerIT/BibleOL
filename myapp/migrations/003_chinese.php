<?php



class Migration_Chinese extends CI_Migration {

    public function __construct() {
        parent::__construct();

		$CI =& get_instance();
        $CI->language = 'english';
    }
    

    public function up() {
        $this->dbforge->add_column('user', array('family_name_first' => array('type' => 'TINYINT(1)', 'default' => '0')));
        echo "family_name_first field added to user table\n";
   }

	public function down()
	{
        echo "<pre>Downgrade not possible</pre>";
	}
}
