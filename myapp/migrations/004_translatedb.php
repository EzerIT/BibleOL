<?php

$comment = array();
$format = array();

function setup_comments() {
    global $comment, $format;

    $comment['class']['classes'] = 'Text in title bar';
    $comment['class']['edit_class'] = 'Text in title bar';
    $comment['class']['delete_class'] = 'Confirmation dialog title';

    //======================================================================

    $comment['common']['time_format'] = 'Format specification available at <a target="_blank" href="http://php.net/manual/en/function.date.php">http://php.net/manual/en/function.date.php</a>';
    $comment['common']['date_time_format'] = 'Format specification available at <a target="_blank" href="http://php.net/manual/en/function.date.php">http://php.net/manual/en/function.date.php</a>';

    //======================================================================

    $comment['file_manager']['file_mgmt'] = 'Text in title bar';
    $comment['file_manager']['create_folder'] = 'Text in title bar';
    $comment['file_manager']['delete_folder'] = 'Text in title bar';
    $comment['file_manager']['copy_or_delete_files'] = 'Text in title bar';
    $comment['file_manager']['insert_files'] = 'Text in title bar';
    $comment['file_manager']['upload_files'] = 'Text in title bar';
    $comment['file_manager']['edit_visibility'] = 'Text in title bar';
    $comment['file_manager']['edit_visibility_button'] = 'Button text';
    $comment['file_manager']['download_exercise'] = 'Text in title bar';
    $comment['file_manager']['rename_exercise'] = 'Text in title bar';
    $comment['file_manager']['file_mgmt_description'] = 'HTML';
    $format['file_manager']['file_mgmt_description'] = 'keep_blanks';

    $comment['file_manager']['cancel_copy_title'] = 'Text in error title bar';
    $comment['file_manager']['delete_folder_button'] = 'Button text';
    $comment['file_manager']['delete_folder_confirm'] = 'Title in confirmation dialog';
    $comment['file_manager']['create_folder_button'] = 'Button text';
    $comment['file_manager']['copy_files'] = 'Title in dialog heading';
    $comment['file_manager']['move_files'] = 'Title in dialog heading';

    //======================================================================

    $comment['font']['font_settings'] = 'Text in title bar';

    //======================================================================

    $comment['form_validation']['form_validation_required'] = 'HTML';
    $comment['form_validation']['form_validation_isset'] = 'HTML';
    $comment['form_validation']['form_validation_valid_email'] = 'HTML';
    $comment['form_validation']['form_validation_valid_emails'] = 'HTML';
    $comment['form_validation']['form_validation_valid_url'] = 'HTML';
    $comment['form_validation']['form_validation_valid_ip'] = 'HTML';
    $comment['form_validation']['form_validation_min_length'] = 'HTML';
    $comment['form_validation']['form_validation_max_length'] = 'HTML';
    $comment['form_validation']['form_validation_exact_length'] = 'HTML';
    $comment['form_validation']['form_validation_alpha'] = 'HTML';
    $comment['form_validation']['form_validation_alpha_numeric'] = 'HTML';
    $comment['form_validation']['form_validation_alpha_numeric_spaces'] = 'HTML';
    $comment['form_validation']['form_validation_alpha_dash'] = 'HTML';
    $comment['form_validation']['form_validation_numeric'] = 'HTML';
    $comment['form_validation']['form_validation_is_numeric'] = 'HTML';
    $comment['form_validation']['form_validation_integer'] = 'HTML';
    $comment['form_validation']['form_validation_regex_match'] = 'HTML';
    $comment['form_validation']['form_validation_matches'] = 'HTML';
    $comment['form_validation']['form_validation_differs'] = 'HTML';
    $comment['form_validation']['form_validation_is_unique'] = 'HTML';
    $comment['form_validation']['form_validation_is_natural'] = 'HTML';
    $comment['form_validation']['form_validation_is_natural_no_zero'] = 'HTML';
    $comment['form_validation']['form_validation_decimal'] = 'HTML';
    $comment['form_validation']['form_validation_less_than'] = 'HTML';
    $comment['form_validation']['form_validation_less_than_equal_to'] = 'HTML';
    $comment['form_validation']['form_validation_greater_than'] = 'HTML';
    $comment['form_validation']['form_validation_greater_than_equal_to'] = 'HTML';
    $comment['form_validation']['form_validation_error_message_not_set'] = 'HTML';
    $comment['form_validation']['form_validation_in_list'] = 'HTML';

    //======================================================================

    $comment['intro_text']['intro_center'] = 'HTML';
    $comment['intro_text']['intro_right1'] = 'HTML';
    $comment['intro_text']['intro_right2'] = 'HTML';
    $comment['intro_text']['intro_right3'] = 'HTML';
    $format['intro_text']['intro_center'] = 'keep_blanks';
    $format['intro_text']['intro_right1'] = 'keep_blanks';
    $format['intro_text']['intro_right2'] = 'keep_blanks';
    $format['intro_text']['intro_right3'] = 'keep_blanks';

    $comment['intro_text']['welcome2'] = '%s will be replaced by the user\'s name';
    //======================================================================

    $comment['js']['use_qo_selection'] = '{0} will be replaced by MQL code';
    $comment['js']['passage_selection'] = 'Dialog box title';
    $comment['js']['feature_specification'] = 'Dialog box title';

    //======================================================================

    $comment['login']['login']                   = 'Text in title bar';
    $comment['login']['login_button']            = 'Button label';
    $comment['login']['forgotten_message']       = 'The first two %s\'s will be the user\'s first and last name';
    $format['login']['forgotten_message']       = 'keep_blanks';

    $comment['login']['email_sent_msg']          = '%s will be the user\'s email address';
    $comment['login']['cannot_reset_password']   = 'Text in title bar';

    $comment['login']['password_reset_message']  = 'The first two %s\'s will be the user\'s first and last name';
    $format['login']['password_reset_message']  = 'keep_blanks';

    $comment['login']['password_reset_sent']     = '%s will be the user\'s email address';
    $comment['login']['new_google_user'] = 'Text in title bar';
    $comment['login']['new_facebook_user'] = 'Text in title bar';
    $comment['login']['users'] = 'Text in title bar';

    $comment['login']['no_email'] = 'HTML';
    $format['login']['no_email'] = 'keep_blanks';

    //======================================================================

    $comment['owner']['change_owner_title'] = 'Text in title bar';

    //======================================================================

    $comment['privacy']['privacy_text'] = 'HTML';
    $format['privacy']['privacy_text'] = 'keep_blanks';

    //======================================================================

    $comment['statistics']['statistics_title'] = 'Text in title bar';
    $comment['statistics']['time'] = 'Date and time';

    //======================================================================

    $comment['text']['select_text'] = 'Text in title bar';
    $comment['text']['show_text'] = 'Text in title bar';
    $comment['text']['display'] = 'Button text';
    $comment['text']['quiz'] = 'Text in title bar';

    $comment['text']['select_a_passage'] = 'HTML';
    $format['text']['select_a_passage'] = 'keep_blanks';
    $comment['text']['corpus_copyright'] = 'HTML';
    $format['text']['corpus_copyright'] = 'keep_blanks';

    $comment['text']['directory'] = 'Text in title bar';
    $comment['text']['edit_quiz'] = 'Text in title bar';
    $comment['text']['specify_file_name'] = 'Title in dialog box';
    $comment['text']['overwrite'] = 'Title in dialog box';
    $comment['text']['import_from_shebanq'] = 'Title in dialog box';
    $comment['text']['mql_sentence_unit'] = 'Title in dialog box';

    //======================================================================

    $comment['urls']['select_gloss_range'] = 'HTML';
    $format['urls']['select_gloss_range'] = 'keep_blanks';

    $comment['urls']['top_glosses'] = '%d will be the number of glosses displayed';

    $comment['urls']['delete_url_confirm'] = 'HTML';

    //======================================================================

    $comment['userclass']['uc_edit_user'] = 'Text in title bar';
    $comment['userclass']['uc_edit_class'] = 'Text in title bar';
    $comment['userclass']['uc_classes'] = 'Title bar for error page';
    $comment['userclass']['enroll_in_class'] = 'Text in title bar';

    //======================================================================

    $comment['users']['display_user'] = 'Text in title bar';
    $comment['users']['edit_user'] = 'Text in title bar';
    $comment['users']['create_account'] = 'Text in title bar';
    $comment['users']['users'] = 'Text in title bar';
    $comment['users']['delete_profile'] = 'Text in title bar on pop-up dialog';
    $comment['users']['delete_user'] = 'Confirmation dialog title';

    $comment['users']['account_created_message1'] = 'The first two %s\'s will be the user\'s first and last name';
    $format['users']['account_created_message1'] = 'keep_blanks';
    $comment['users']['account_created_message2']  = 'This is part of an email message. You may want to end it with a blank line';
    $comment['users']['account_created_message2t'] = 'This is part of an email message. You may want to end it with a blank line';
    $format['users']['account_created_message2'] = 'keep_blanks';
    $format['users']['account_created_message2t'] = 'keep_blanks';

    $comment['users']['account_created_message3'] = 'The %s will be a URL';
    $format['users']['account_created_message3'] = 'keep_blanks';

    $comment['users']['account_you_created_message1'] = 'The first two %s\'s will be the user\'s first and last name';
    $format['users']['account_you_created_message1'] = 'keep_blanks';
    $comment['users']['account_you_created_message3'] = 'The %s will be a URL';
    $format['users']['account_you_created_message3'] = 'keep_blanks';

    $comment['users']['password_sent']       = '%s will be the user\'s email address';

    $comment['users']['user_profile_deleted'] = 'Text in title bar';

    $comment['users']['expiry_warning_1_message'] = 'The first two %s\'s will be the user\'s first and last name';
    $format['users']['expiry_warning_1_message'] = 'keep_blanks';
    $comment['users']['expiry_warning_2_message'] = 'The first two %s\'s will be the user\'s first and last name';
    $format['users']['expiry_warning_2_message'] = 'keep_blanks';

    $format['users']['expiry_warning_message_local'] = 'keep_blanks';
    $format['users']['expiry_warning_message_google'] = 'keep_blanks';
    $format['users']['expiry_warning_message_facebook'] = 'keep_blanks';
}



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
        global $comment, $format;

        setup_comments();
        
        $d = directory_map($application_folder.DIRECTORY_SEPARATOR.'language', 2); // A value of 2 allows us to recognize empty directories

        $allkeys = array();
        
        // Loop through languages
        foreach ($d as $lang_name => $lang_files) {
            if (count($lang_files)==0)
                continue;

            if (substr($lang_name,-1)!==DIRECTORY_SEPARATOR) {
                echo "Bad language name: $lang_name\n";
                die;
            }
            
            switch (substr($lang_name,0,-1)) {
              case 'english':
                    $short_langname = 'en';
                    break;
              default:
                    $short_langname = substr($lang_name,0,-1);
                    break;
            }
            
            echo "Handle language $short_langname:\n";
            
            $this->dbforge->add_field(array('id' => array('type' => 'INT', 'auto_increment' => true),
                                            'textgroup' => array('type'=>'VARCHAR(25)'),
                                            'symbolic_name' => array('type'=>'TINYTEXT'),
                                            'text' => array('type'=>'TEXT')));
            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->add_key('textgroup');
            $this->dbforge->create_table('language_'.$short_langname);

            $toinsert = array();

            // Loop through files
            foreach ($lang_files as $file) {
                if (substr($file,-9)!=='_lang.php') {
                    echo "    Skipping file $file\n";
                    continue;
                }

                $short_file = substr($file, 0, -9); // Remove '_lang.php'

                if ($short_file=='db' || $short_file=='email')
                    continue;
                
                if (!isset($allkeys[$short_file]))
                    $allkeys[$short_file] = array();
                
                $lang = array();
                include($application_folder.DIRECTORY_SEPARATOR.'language'.DIRECTORY_SEPARATOR.$lang_name.$file);
                
                foreach ($lang as $key => $text) {
                    if ($short_langname=='en')
                        $use_textarea[$key] = strpos($text, "\n")!==false || strlen($text)>=80;
                    
                    if (!isset($format[$short_file][$key]) || $format[$short_file][$key]!='keep_blanks')
                        $text = preg_replace('/\s+/',' ',$text); // Remove extraneous whitespace
//                      $text = preg_replace('/(\s)\s*/','\1',$text); // Remove extraneous whitespace
                    
                    $toinsert[] = array('textgroup' => $short_file,
                                        'symbolic_name' => $key,
                                        'text' => $text);
                    $allkeys[$short_file][$key] = true;
                }
            }

            $this->db->insert_batch('language_'.$short_langname, $toinsert);
        }

        // Create translation comment table
        $toinsert = array();
        $this->dbforge->add_field(array('id' => array('type' => 'INT', 'auto_increment' => true),
                                        'textgroup' => array('type'=>'TINYTEXT'),
                                        'symbolic_name' => array('type'=>'TINYTEXT'),
                                        'comment' => array('type'=>'TEXT',
                                                           'null' => true,
                                                           'default' => null),
                                        'use_textarea' => array('type' => 'TINYINT(1)')
                                      ));
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('language_comment');

        foreach ($allkeys as $short_file => $keys) {
            foreach ($keys as $key => $value) {
                $toinsert[] = array('textgroup' => $short_file,
                                    'symbolic_name' => $key,
                                    'comment' => isset($comment[$short_file][$key]) ? $comment[$short_file][$key] : null,
                                    'use_textarea' => isset($use_textarea[$key]) && $use_textarea[$key]);
            }
        }

        $this->db->insert_batch('language_comment', $toinsert);

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

//        $d = directory_map('db/property_files', 1); // A value of 2 allows us to recognize empty directories
// 
//        foreach ($d as $file) {
//            if (preg_match('/(.*)\.(.*)\.prop.pretty.json$/', $file, $matches)) {
//                list($filename,$db,$lang) = $matches;
//                echo "Handling file $filename\n";
//                $input = file_get_contents("db/property_files/$filename");
//                $props = json_decode($input);
//                assert(!is_null($props));
//                $this->db->insert('db_localize',array('db' => $db,
//                                                      'lang' => $lang,
//                                                      'json' => json_encode($props)));
//            }
//        }
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

        //$this->add_if_translation();

        $this->add_grammar_translation();
        //$this->add_hebrew_lexicons();
        //$this->add_greek_lexicon();
        die;
   }

	public function down()
	{
        echo "<pre>Downgrade not possible</pre>";
	}
}
