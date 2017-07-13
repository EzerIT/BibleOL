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


    private function add_roman()
    {
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

    private function fix_final_et_al()
    {
        foreach (array('Hebrew','Aramaic') as $src_lang) {
            $query = $this->db->select('id,lex,vocalized_lexeme_utf8')->get('lexicon_'.$src_lang);
            foreach ($query->result() as $row) {
                if ($row->lex=='KLNH/' && $row->vocalized_lexeme_utf8=="\xd7\x9b\xd6\xbc\xd6\xb7\xd7\x9c\xd6\xb0\xd7\xa0\xd6\xb6\xd7\x94")
                    $vlu = "\xd7\x9b\xd6\xbc\xd6\xb7\xd7\x9c\xd6\xb0\xd7\xa0\xd6\xb5\xd7\x94";
                elseif ($row->lex=='JRJXW/' && $row->vocalized_lexeme_utf8=="\xd7\x99\xd6\xb0\xd7\xa8\xd6\xb4\xd7\x99\xd7\x97\xd6\xb9\xd7\x95")
                    $vlu = "\xd7\x99\xd6\xb0\xd7\xa8\xd6\xb4\xd7\x97\xd6\xb9\xd7\x95";
                else
                    $vlu = str_replace(array("\xd7\x9b ",
                                             "\xd7\x9e ",
                                             "\xd7\xa0 ",
                                             "\xd7\xa4 ",
                                             "\xd7\xa6 ",
                                             "\xd7\x9b\xd6\xb0 "),
                                       array("\xd7\x9a ",
                                             "\xd7\x9d ",
                                             "\xd7\x9f ",
                                             "\xd7\xa3 ",
                                             "\xd7\xa5 ",
                                             "\xd7\x9a\xd6\xb0 "),
                                       $row->vocalized_lexeme_utf8);
                if ($vlu!=$row->vocalized_lexeme_utf8)
                    $this->db->where('id',$row->id)->update('lexicon_'.$src_lang, array('vocalized_lexeme_utf8'=>$vlu));
            }
        }

        
        
    }
    
    public function up() {
        $this->add_roman();
        $this->fix_final_et_al();
    }

    public function down()
    {
        echo "<pre>Downgrade not possible</pre>";
    }
}
