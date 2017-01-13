<?php

class Migration_Newtranslations extends CI_Migration {

    // Additions allowing users to sign up for an account, and for automatic account deletion

    public function __construct() {
        parent::__construct();

		$CI =& get_instance();
        $CI->language = 'english';
    }
    
    public function up() {
        $this->db->query("INSERT INTO {PRE}language_comment(textgroup,symbolic_name,use_textarea) VALUES"
                         . "('translate','malformed_url',0),"
                         . "('translate','illegal_source_language',0),"
                         . "('translate','illegal_target_language',0)");
    }

	public function down()
	{
        echo "<pre>Downgrade not possible</pre>";
	}
}
