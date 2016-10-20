<?php

class Migration_Translatedb extends CI_Migration {

    // Additions allowing users to sign up for an account, and for automatic account deletion

    public function __construct() {
        parent::__construct();

		$CI =& get_instance();
        $CI->language = 'english';
    }

	public function up() {
        echo "TODO: Remove the following comment in the source code\n\n";
//        $this->dbforge->add_column('user', array('istranslator' => array('type' => 'TINYINT(1)', 'default' => '0')));
//        echo "istranslator field added to user table\n";
        
        global $application_folder;
        
        $this->load->helper('directory');
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
                    $toinsert[] = array('textgroup' => $short_file,
                                        'symbolic_name' => $key,
                                        'text' => $text);
                    $allkeys[$short_file][$key] = true;
                    if ($short_langname=='en')
                        $has_lf[$key] = strpos($text, "\n")!==false;
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
                                        'has_lf' => array('type' => 'TINYINT(1)')
                                      ));
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('language_comment');

        foreach ($allkeys as $short_file => $keys) {
            foreach ($keys as $key => $value) {
                $toinsert[] = array('textgroup' => $short_file,
                                    'symbolic_name' => $key,
                                    'has_lf' => isset($has_lf[$key]) && $has_lf[$key]);
            }
        }
        $this->db->insert_batch('language_comment', $toinsert);

        echo "Language tables created\n";
        die;
   }

	public function down()
	{
        echo "<pre>Downgrade not possible</pre>";
	}
}
