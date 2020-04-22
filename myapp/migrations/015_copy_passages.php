<?php

class Migration_Copy_passages extends CI_Migration {
    // Change name of icons in bol_heb_urls

    public function __construct() {
        parent::__construct();
        
		$CI =& get_instance();
        $CI->language = 'english';
    }

	public function up() {
        $names['file_manager'] = ['confirm_passage_insert', 'copy_passages', 'insert_passages', 'insert_passages_from',
                                  'wrong_database', 'files_not_changed', 'confirm_passage_insert', 'passage_copy_error',
                                  'passage_copy_ok'];
        $names['common'] = ['server_error'];
        $languages = ['en','da'];


        foreach (array_keys($names) as $textgroup) {
        
            include("myapp/language/langsrc/comment/{$textgroup}_lang.php");

            foreach ($languages as $l) {
                $lang = array();

                include("myapp/language/langsrc/$l/{$textgroup}_lang.php");
                
                $langs[$l] = $lang;
            }
        

            foreach ($names[$textgroup] as $n) {
                $this->db->where('textgroup',$textgroup)->where('symbolic_name',$n)->delete('language_comment');
            
                $this->db->insert('language_comment',array('textgroup' => $textgroup,
                                                           'symbolic_name' => $n,
                                                           'comment' => empty($comment[$n]) ? null : $comment[$n],
                                                           'format' => empty($format[$n]) ? null : $format[$n],
                                                           'use_textarea' => $use_textarea[$n]));
 
                foreach ($languages as $l) {
                    $this->db->where('textgroup',$textgroup)->where('symbolic_name',$n)->delete("language_$l");

                    $this->db->insert("language_$l",array('textgroup' => 'file_manager',
                                                          'symbolic_name' => $n,
                                                          'text' => $langs[$l][$n]));
                }
            }
        }
        
        die;
    }
    
	public function down()
	{
        echo "<pre>Downgrade not possible</pre>";
	}
}
