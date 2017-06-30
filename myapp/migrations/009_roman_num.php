<?php

  /* Add Roman numerals to vocalized_lexeme_utf8 where there are multiple identical words. */



class Migration_Roman_num extends CI_Migration {

    public function __construct() {
        parent::__construct();

        $CI =& get_instance();
        $CI->language = 'english';

        $this->load->helper('directory');
    }

    private $roman    = array('I', 'II', 'III', 'IV',  'V',    'VI',    'VII',    'VIII',    'IX');
    private $selector = array('',  '=',  '==',  '===', '====', '=====', '======', '=======', '========');



    
    public function up() {
        foreach (array('Hebrew','Aramaic') as $src_lang) {
            $this->dbforge->add_column('lexicon_'.$src_lang, array('roman' => array('type' => 'VARCHAR(5)',
                                                                                    'default' => '',
                                                                                    'after' => 'vocalized_lexeme_utf8')));

            $set_of_lex = array();
            $query = $this->db->select('id,lex')->like('lex','=')->get('lexicon_'.$src_lang);
            foreach ($query->result() as $row)
                $set_of_lex[preg_replace('/=+/','',$row->lex)] = true;

            $count_selector = count($this->selector);

            foreach ($set_of_lex as $lex => $ignore) {
                // Before stripping '=', $lex ends in '=[' or '=/' or '='
                $suffix = $lex[strlen($lex)-1];
                if ($suffix!='[' && $suffix!='/') {
                    $suffix = '';
                    $naked_lex = $lex;
                }
                else
                    $naked_lex = substr($lex,0,-1);


                echo $lex,' ';
            
                for ($i=0; $i<$count_selector; ++$i) {
                    $query2 = $this->db
                        ->select('lex,vocalized_lexeme_utf8')
                        ->where('lex', $naked_lex . $this->selector[$i] . $suffix)
                        ->get('lexicon_'.$src_lang);
                    foreach ($query2->result() as $row2) {
                        $this->db->where('lex',$naked_lex . $this->selector[$i] . $suffix)
                            ->set(array('roman' => $this->roman[$i]))
                            ->update('lexicon_'.$src_lang);
                    }
                }
            }
            echo "\n";
        }
    }

    public function down()
    {
        echo "<pre>Downgrade not possible</pre>";
    }
}
