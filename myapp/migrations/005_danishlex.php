<?php

$comment = array();
$format = array();


class Migration_Danishlex extends CI_Migration {

    // Additions allowing users to sign up for an account, and for automatic account deletion

    public function __construct() {
        parent::__construct();

		$CI =& get_instance();
        $CI->language = 'english';
    }
    

    private function add_hebrew_da() {
        $this->dbforge->add_field(array('id' => array('type' => 'INT', 'auto_increment' => true),
                                        'lex_id' => array('type'=>'INT',
                                                          'null' => true),
                                        'gloss' => array('type'=>'TEXT')
                                      ));
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('lex_id');
        $this->dbforge->create_table('lexicon_heb_da');

        if (!$this->db->query("ALTER TABLE {PRE}lexicon_heb_da ADD FOREIGN KEY lexid (lex_id) REFERENCES {PRE}lexicon_heb(id) ON DELETE SET NULL ON UPDATE CASCADE"))
            echo "    ERROR: Foreign key (lex_id) on lexicon_heb_da failed\n";

        $toinsert = array();

        $query_en = $this->db->get('lexicon_heb_en');
        foreach ($query_en->result() as $row_en)
            $toinsert[] = array('lex_id' => $row_en->id,
                                'gloss' => '*');

        echo "Inserting lexicon_heb_da\n";
        $this->db->insert_batch('lexicon_heb_da', $toinsert);
    }



    public function up() {
        $this->add_hebrew_da();
   }

	public function down()
	{
        echo "<pre>Downgrade not possible</pre>";
	}
}
