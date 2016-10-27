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
                                            'textgroup' => array('type'=>'TINYTEXT'),
                                            'symbolic_name' => array('type'=>'TINYTEXT'),
                                            'text' => array('type'=>'TEXT')));
            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->create_table('language_'.$short_langname);

            $toinsert = array();

            // Loop through files
            foreach ($lang_files as $file) {
                if (substr($file,-9)!=='_lang.php') {
                    echo "    Skipping file $file\n";
                    continue;
                }

                $short_file = substr($file, 0, -9); // Remove '_lang.php'
                
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

        
        $d = directory_map('db', 1); // A value of 2 allows us to recognize empty directories

        foreach ($d as $file) {
            if (preg_match('/(.*)\.(.*)\.prop.json$/', $file, $matches)) {
                list($filename,$db,$lang) = $matches;
                echo "Handling file $filename\n";
                $this->db->insert('db_localize',array('db' => $db,
                                                      'lang' => $lang,
                                                      'json' => file_get_contents('db'.DIRECTORY_SEPARATOR.$filename)));
            }
        }
    }


    public function up() {
        // $this->add_translator();

        $this->add_if_translation();

        //$this->add_grammar_translation();
        
        die;
   }

	public function down()
	{
        echo "<pre>Downgrade not possible</pre>";
	}
}
