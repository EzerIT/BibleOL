<?php

$comment = array();
$format = array();


class Migration_Split_heb extends CI_Migration {

    // Additions allowing users to sign up for an account, and for automatic account deletion

    public function __construct() {
        parent::__construct();

		$CI =& get_instance();
        $CI->language = 'english';

        $this->load->helper('directory');
    }
    
    private function split_lexicons() {
        foreach (array('Hebrew','Aramaic') as $srclang) {
            $this->dbforge->add_field(array('id' => array('type' => 'INT', 'auto_increment' => true),
                                            'lex' => array('type'=>'VARCHAR(25)'),
                                            'vs' => array('type'=>'VARCHAR(8)'),
                                            'tally' => array('type'=>'INT'),
                                            'vocalized_lexeme_utf8' => array('type'=>'TINYTEXT'),
                                            'sortorder' => array('type'=>'TINYTEXT'),
                                            'firstbook' => array('type'=>'TINYTEXT'),
                                            'firstchapter' => array('type'=>'INT'),
                                            'firstverse' => array('type'=>'INT')
                                          ));
            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->add_key('lex');
            $this->dbforge->add_key('vs');
            $this->dbforge->add_key('language');
            $this->dbforge->create_table("lexicon_{$srclang}");

            foreach (array('en','de','da') as $dstlang) {
                $this->dbforge->add_field(array('id' => array('type' => 'INT', 'auto_increment' => true),
                                                'lex_id' => array('type'=>'INT',
                                                                  'null' => true),
                                                'gloss' => array('type'=>'TEXT')
                                              ));
                $this->dbforge->add_key('id', TRUE);
                $this->dbforge->add_key('lex_id');
                $this->dbforge->create_table("lexicon_{$srclang}_{$dstlang}");
        
                if (!$this->db->query("ALTER TABLE {PRE}lexicon_{$srclang}_{$dstlang} ADD FOREIGN KEY lexid (lex_id) REFERENCES {PRE}lexicon_{$srclang}(id) ON DELETE SET NULL ON UPDATE CASCADE"))
                    echo "    ERROR: Foreign key (lex_id) on lexicon_{$srclang}_{$dstlang} failed\n";

            }
        }

        $toinsert_heb = array();
        $toinsert_aram = array();
        $query = $this->db->get('lexicon_heb');
        $hebid = array();
        $aramid = array();
        foreach ($query->result() as $row) {
            if ($row->language=='Hebrew') {
                $hebid[] = $row->id;
                $toinsert_heb[] = array('id' => $row->id,
                                        'lex' => $row->lex,
                                        'vs' => $row->vs,
                                        'tally' => $row->tally,
                                        'vocalized_lexeme_utf8' => $row->vocalized_lexeme_utf8,
                                        'sortorder' => $row->sortorder,
                                        'firstbook' => $row->firstbook,
                                        'firstchapter' => $row->firstchapter,
                                        'firstverse' => $row->firstverse);
            }
            else {
                $aramid[] = $row->id;
                $toinsert_aram[] = array('id' => $row->id,
                                        'lex' => $row->lex,
                                         'vs' => $row->vs,
                                         'tally' => $row->tally,
                                         'vocalized_lexeme_utf8' => $row->vocalized_lexeme_utf8,
                                         'sortorder' => $row->sortorder,
                                         'firstbook' => $row->firstbook,
                                         'firstchapter' => $row->firstchapter,
                                         'firstverse' => $row->firstverse);
            }
        }

        echo "Populating lexicon_Hebrew\n";
        $this->db->insert_batch('lexicon_Hebrew', $toinsert_heb);

        echo "Populating lexicon_Aramaic\n";
        $this->db->insert_batch('lexicon_Aramaic', $toinsert_aram);

        foreach (array('en','de','da') as $dstlang) {
            $toinsert_heb = array();
            $toinsert_aram = array();
            $query = $this->db->get("lexicon_heb_{$dstlang}");
            foreach ($query->result() as $row) {
                if (in_array($row->lex_id, $hebid))
                    $toinsert_heb[] = array('lex_id' => $row->lex_id,
                                            'gloss' => $row->gloss);
                elseif (in_array($row->lex_id, $aramid))
                    $toinsert_aram[] = array('lex_id' => $row->lex_id,
                                             'gloss' => $row->gloss);
                else
                    die("Bad lexid: $row->lex_id. Dstlang=$dstlang\n");
            }
            
            echo "Populating lexicon_Hebrew_{$dstlang}\n";
            $this->db->insert_batch("lexicon_Hebrew_{$dstlang}", $toinsert_heb);

            echo "Populating lexicon_Aramaic_{$dstlang}\n";
            $this->db->insert_batch("lexicon_Aramaic_{$dstlang}", $toinsert_aram);
        }

        foreach (array('heb','aram') as $srclang) {
            foreach (array('en','de','da') as $dstlang) {
                echo "Dropping table lexicon_{$srclang}_{$dstlang}\n";
                $this->dbforge->drop_table("lexicon_{$srclang}_{$dstlang}",true);
            }
            echo "Dropping table lexicon_{$srclang}\n";
            $this->dbforge->drop_table("lexicon_{$srclang}",true);
        }
    }



    public function up() {
        $this->split_lexicons();
   }

	public function down()
	{
        echo "<pre>Downgrade not possible</pre>";
	}
}
