<?php

$comment = array();
$format = array();


class Migration_Translatedb extends CI_Migration {

    // Additions allowing users to sign up for an account, and for automatic account deletion

    public function __construct() {
        parent::__construct();

		$CI =& get_instance();
        $CI->language = 'english';

        $this->load->helper('directory');
    }
    
    private function add_translator() {
        $this->dbforge->add_column('user', array('istranslator' => array('type' => 'TINYINT(1)', 'default' => '0')));
        echo "istranslator field added to user table\n";
    }

    private function add_if_translation() {
        global $application_folder;

        $this->load->model('mod_translate');

        $dir = $application_folder.DIRECTORY_SEPARATOR.'language'.DIRECTORY_SEPARATOR.'old_lang_files';


        echo "Handle comments:\n";
        $this->mod_translate->if_phpcomment2db($dir.DIRECTORY_SEPARATOR.'comment');
        
        $d = directory_map($dir, 2); // A value of 2 allows us to recognize empty directories

        // Loop through languages
        foreach ($d as $lang_name => $lang_files) {
            if (count($lang_files)==0)
                continue;

            if (substr($lang_name,-1)!==DIRECTORY_SEPARATOR) {
                echo "Bad language name: $lang_name\n";
                die;
            }
            
            $long_langname = substr($lang_name,0,-1);
            if ($long_langname == 'comment')
                continue;
            
            switch ($long_langname) {
              case 'english':
                    $short_langname = 'en';
                    break;
              default:
                    $short_langname = $long_langname;
                    break;
            }
            
            echo "Handle language $short_langname:\n";
            $this->mod_translate->if_php2db($short_langname, $dir.DIRECTORY_SEPARATOR.$long_langname);
        }

        echo "Language tables created\n";
    }

    private function add_grammar_translation() {
        $this->dbforge->add_field(array('id' => array('type' => 'INT', 'auto_increment' => true),
                                        'db' => array('type'=>'TINYTEXT'),
                                        'lang' => array('type'=>'TINYTEXT'),
                                        'json' => array('type'=>'MEDIUMTEXT')
                                      ));
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('db_localize');

        $this->load->model('mod_translate');

        $this->mod_translate->gram_prop2db();     
    }

    private function add_hebrew_lexicons() {
        $hand = fopen('db/allheb.txt','r');
 
        $allheb = array();
        
        while (($line = fgets($hand)) !== false) {
            $fields = explode("\t",substr($line,0,-1));
            $allheb[$fields[0].'+'.$fields[1].'+'.$fields[2]] = array($fields[3],$fields[4],$fields[5]);
        }


        $this->dbforge->add_field(array('id' => array('type' => 'INT', 'auto_increment' => true),
                                        'lex' => array('type'=>'VARCHAR(25)'),
                                        'vs' => array('type'=>'VARCHAR(8)'),
                                        'language' => array('type' => "enum('Hebrew', 'Aramaic')"),
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
        $this->dbforge->create_table('lexicon_heb');
 
        $this->dbforge->add_field(array('id' => array('type' => 'INT', 'auto_increment' => true),
                                        'lex_id' => array('type'=>'INT',
                                                          'null' => true),
                                        'gloss' => array('type'=>'TEXT')
                                      ));
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('lex_id');
        $this->dbforge->create_table('lexicon_heb_en');
        
        if (!$this->db->query("ALTER TABLE {PRE}lexicon_heb_en ADD FOREIGN KEY lexid (lex_id) REFERENCES {PRE}lexicon_heb(id) ON DELETE SET NULL ON UPDATE CASCADE"))
            echo "    ERROR: Foreign key (lex_id) on lexicon_heb_en failed\n";

        $this->dbforge->add_field(array('id' => array('type' => 'INT', 'auto_increment' => true),
                                        'lex_id' => array('type'=>'INT',
                                                          'null' => true),
                                        'gloss' => array('type'=>'TEXT')
                                      ));
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('lex_id');
        $this->dbforge->create_table('lexicon_heb_de');

        if (!$this->db->query("ALTER TABLE {PRE}lexicon_heb_de ADD FOREIGN KEY lexid (lex_id) REFERENCES {PRE}lexicon_heb(id) ON DELETE SET NULL ON UPDATE CASCADE"))
            echo "    ERROR: Foreign key (lex_id) on lexicon_heb_de failed\n";

        $dbh = $this->load->database(array('database' => 'db/glossdb_hebrew.db',
                                           'dbdriver' => 'sqlite3',
                                           'dbprefix' => '',
                                           'pconnect' => FALSE,
                                           'db_debug' => TRUE,
                                           'cache_on' => FALSE,
                                           'cachedir' => '',
                                           'char_set' => 'utf8',
                                           'dbcollat' => 'utf8_general_ci'),
                                     true);

        $query_en = $dbh
            ->order_by('id')
            ->get('heb_en');
        
        $query_de = $dbh
            ->order_by('id')
            ->get('heb_de');

        $toinsert_heb = array();
        $toinsert_heb_en = array();
        $toinsert_heb_de = array();
        
        $row_de = $query_de->first_row();
        foreach ($query_en->result() as $row_en) {
            assert($row_en->id===$row_de->id);
            assert($row_en->lex===$row_de->lex);
            assert($row_en->vs===$row_de->vs);
            assert($row_en->language===$row_de->language);

            $allheb_key = "$row_en->lex+$row_en->vs+$row_en->language";
            assert(isset($allheb[$allheb_key]));
            list($book,$chapter,$verse) = $allheb[$allheb_key];
            
            $toinsert_heb[] = array('id' => $row_en->id,
                                    'lex' => $row_en->lex,
                                    'vs' => $row_en->vs,
                                    'language' => $row_en->language,
                                    'tally' => $row_en->tally,
                                    'vocalized_lexeme_utf8' => $row_en->vocalized_lexeme_utf8,
                                    'sortorder' => $row_en->sortorder,
                                    'firstbook' => $book,
                                    'firstchapter' => $chapter,
                                    'firstverse' => $verse);
            $toinsert_heb_en[] = array('lex_id' => $row_en->id,
                                       'gloss' => $row_en->english);
            $toinsert_heb_de[] = array('lex_id' => $row_de->id,
                                       'gloss' => $row_de->german);

            $row_de = $query_de->next_row();

        }   

        echo "Inserting lexicon_heb\n";
        $this->db->insert_batch('lexicon_heb', $toinsert_heb);

        echo "Inserting lexicon_heb_en\n";
        $this->db->insert_batch('lexicon_heb_en', $toinsert_heb_en);

        echo "Inserting lexicon_heb_de\n";
        $this->db->insert_batch('lexicon_heb_de', $toinsert_heb_de);
    }

    private function add_greek_lexicon() {
        $this->dbforge->add_field(array('id' => array('type' => 'INT', 'auto_increment' => true),
                                        'strongs' => array('type'=>'INT'),
                                        'strongs_unreliable' => array('type'=>'TINYINT(1)'),
                                        'lemma' => array('type'=>'VARCHAR(50)'),
                                        'tally' => array('type'=>'INT'),
                                        'sortorder' => array('type'=>'TINYTEXT'),
                                        'firstbook' => array('type'=>'TINYTEXT'),
                                        'firstchapter' => array('type'=>'INT'),
                                        'firstverse' => array('type'=>'INT')
                                      ));
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('strongs');
        $this->dbforge->add_key('strongs_unreliable');
        $this->dbforge->add_key('lemma');
        $this->dbforge->create_table('lexicon_greek');

        $hand = fopen('db/allgreek.txt','r');
 
        $toinsert_greek = array();
        
        while (($line = fgets($hand)) !== false) {
            $fields = explode("\t",substr($line,0,-1));
 
            $toinsert_greek[] = array('strongs' => $fields[0],
                                      'strongs_unreliable' => $fields[1],
                                      'lemma' => $fields[2],
                                      'tally' => $fields[7],
                                      'sortorder' => $fields[3],
                                      'firstbook' => $fields[4],
                                      'firstchapter' => $fields[5],
                                      'firstverse' => $fields[6]);
        }
 
        echo "Inserting lexicon_greek\n";
        $this->db->insert_batch('lexicon_greek', $toinsert_greek);


        
        $this->dbforge->add_field(array('id' => array('type' => 'INT', 'auto_increment' => true),
                                        'lex_id' => array('type'=>'INT',
                                                          'null' => true),
                                        'gloss' => array('type'=>'TEXT')
                                      ));
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('lex_id');
        $this->dbforge->create_table('lexicon_greek_en');

        if (!$this->db->query("ALTER TABLE {PRE}lexicon_greek_en ADD FOREIGN KEY lexid (lex_id) REFERENCES {PRE}lexicon_greek(id) ON DELETE SET NULL ON UPDATE CASCADE"))
            echo "    ERROR: Foreign key (lex_id) on lexicon_greek_en failed\n";
 

        $dbh = $this->load->database(array('database' => 'db/glossdb_greek.db',
                                           'dbdriver' => 'sqlite3',
                                           'dbprefix' => '',
                                           'pconnect' => FALSE,
                                           'db_debug' => TRUE,
                                           'cache_on' => FALSE,
                                           'cachedir' => '',
                                           'char_set' => 'utf8',
                                           'dbcollat' => 'utf8_general_ci'),
                                     true);

        $toinsert_greek_en = array();
        $query = $this->db->get('lexicon_greek');
        foreach ($query->result() as $row_gr) {
            $query_en = $dbh->where('strongs',$row_gr->strongs)->get('greek_en');
            $row_en = $query_en->row();
            $toinsert_greek_en[] = array('lex_id' => $row_gr->id,
                                         'gloss' => $row_en->english);
        }
        echo "Inserting lexicon_greek_en\n";
        $this->db->insert_batch('lexicon_greek_en', $toinsert_greek_en);
    }

    public function up() {
        // $this->add_translator();

        $this->add_if_translation();

        //$this->add_grammar_translation();
        //$this->add_hebrew_lexicons();
        //$this->add_greek_lexicon();
        die;
   }

	public function down()
	{
        echo "<pre>Downgrade not possible</pre>";
	}
}
