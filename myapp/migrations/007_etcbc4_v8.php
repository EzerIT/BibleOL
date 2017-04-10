<?php

  /* These are important changes to the structure of ETCBC4:

        "continuation": "string",                 | "g_suffix": "string",         
        "continuation_translit": "string",        | "g_suffix_translit": "string",
        "continuation_utf8": "string",            | "g_suffix_utf8": "string",    
        "danish": "string",                       < TO BE ADDED
        "english": "string",                      < TO BE ADDED
        "german": "string",                       < TO BE ADDED
        "glossurl": "url",                        < TO BE ADDED
        "g_qere": "string",                       | "qere": "string",         
        "g_qere_translit": "string",              | "qere_translit": "string",
        "g_qere_utf8": "string",                  | "qere_utf8": "string",    
                                                  > NOW INVISIBLE       "prs_gn": "gender_t",
                                                  > NOW INVISIBLE       "prs_nu": "number_t",
                                                  > NOW INVISIBLE       "prs_ps": "person_t",
        "suffix_gender": "suffix_gender_t",       | "suffix_gender": "gender_t",
        "suffix_number": "suffix_number_t",       | "suffix_number": "number_t",
        "suffix_person": "suffix_person_t",       | "suffix_person": "person_t",
        "suffix": "string",                       < REMOVE
        "suffix_translit": "string",              < REMOVE
        "suffix_utf8": "string",                  < REMOVE
        "text_cons_utf8": "string",               < SUBSUMED IN g_word_cons_utf8
        "text_nocant_utf8": "string",             | "g_word_nocant_utf8": "string",  
        "text_nopunct_translit": "string",        | "g_word_nopunct_translit": "string", 
        "text": "string",                         < SUBSUMED IN  g_word
        "text_translit": "string",                | "g_word_translit": "string",
        "text_utf8": "string",                    < SUBSUMED IN  g_word_utf8
        "vocalized_lexeme_cons_utf8": "string",   | "g_voc_lex_cons_utf8": "string",
        "vocalized_lexeme": "string",             | "g_voc_lex": "string",      
        "vocalized_lexeme_translit": "string",    | "g_voc_lex_translit": "string", 
        "vocalized_lexeme_utf8": "string",        | "g_voc_lex_utf8": "string",     



    The types
            "suffix_gender_t",
            "suffix_number_t",
            "suffix_person_t",
    are removed.
     
    The verbal stems
                "htpo",
                "poal",
                "poel",
    are added.
     
    clause_constituent_relation_t:
            "CoVo" changed to "ReVo"
  */



$comment = array();
$format = array();


class Migration_Etcbc4_v8 extends CI_Migration {

    // Additions allowing users to sign up for an account, and for automatic account deletion

    public function __construct() {
        parent::__construct();

        $CI =& get_instance();
        $CI->language = 'english';

        $this->load->helper('directory');
    }

    // Maps a WIT-encoded character to a Latin character that enables sorting of Hebrew words using the mapped string
    static private $w2s_map = array(
        '>' => 'a',       // Aleph
        'B' => 'b',       // Bet
        'G' => 'c',       // Gimel
        'D' => 'd',       // Dalet
        'H' => 'e',       // He
        'W' => 'f',       // Waw
        'Z' => 'g',       // Zayin
        'X' => 'h',       // Khet
        'V' => 'i',       // Tet
        'J' => 'j',       // Yod
        'k' => 'k',       // Final Kaph
        'K' => 'k',       // Kaph
        'L' => 'l',       // Lamed
        'm' => 'm',       // Final Mem
        'M' => 'm',       // Mem
        'n' => 'n',       // Final Nun
        'N' => 'n',       // Nun
        'S' => 'o',       // Samek
        '<' => 'p',       // Ayin
        'p' => 'q',       // Final Pe
        'P' => 'q',       // Pe
        'y' => 'r',       // Final Tsade
        'Y' => 'r',       // Tsade
        'Q' => 's',       // Qoph
        'R' => 't',       // Resh
        '#' => 'u',       // S/hin with no dot
        'C' => 'u',       // Shin, Shin Dot
        'F' => 'u',       // Shin, Sin Dot
        'T' => 'v',       // Tav
        '=' => '',        // Homograph selector
        '_' => ' ',       // Space
        '[' => '',        // Verbal indicator
        '/' => '',        // Nominal indicator
        );

    // Converts a WIT-encoded Hebrew lexeme to a string of Latin characters that enable sorting of the Hebrew word
    private function wit2sort(string $wit) {
        $out = '';
        $len = strlen($wit);
        for ($i=0; $i<$len; ++$i) {
            assert(array_key_exists($wit[$i],self::$w2s_map));
            $out .= self::$w2s_map[$wit[$i]];
        }

        return $out;
    }

    private function remove_word_from_lexicon(string $word) {
        $this->db->where('lex',$word)->delete('lexicon_Hebrew');
    }

    private function remove_word_from_urls(string $word) {
        $this->db->where('lex',$word)->where('language','Hebrew')->delete('heb_urls');
    }


    private function remove_words() {
        $to_remove = array(
            'BJT_XRWN_<LJWN/',
            'BJT_XRWN_TXTWN/',
            'DJBWN_GD/',
            'GRN_>VD/',
            'XYJ_HMNXWT/',
            'CR=/',
            'KLNH=/',
            'MN===/',
            );

        foreach ($to_remove as $r) {
            $this->remove_word_from_lexicon($r);
            $this->remove_word_from_urls($r);
        }
    }

    private function replace_word_in_lexicon(string $old, string $new, string $new_voclex) {
        $this->db->where('lex',$old)->update('lexicon_Hebrew',
                                             array('lex'=>$new,
                                                   'vocalized_lexeme_utf8' => $new_voclex,
                                                   'sortorder' => $this->wit2sort($new)));
    }

    private function replace_translation(string $lex, string $lang, string $old, string $new) {
        $query = $this->db->select('id')->where('lex',$lex)->get('lexicon_Hebrew');
        if ($query->num_rows()!=1) {
            echo "ERROR: Lex $lex is not in lexicon_Hebrew\n";
            return;
        }

        $lex_id = $query->row()->id;

        $query = $this->db->select('gloss')->where('lex_id',$lex_id)->get("lexicon_Hebrew_$lang");
        if ($query->num_rows()!=1) {
            echo "ERROR: Lex $lex is not in lexicon_Hebrew_$lang\n";
            return;
        }

        if ($query->row()->gloss!=$old) {
            echo "ERROR: Old gloss for $lex is not $old\n";
            return;
        }

        $this->db->where('lex_id',$lex_id)->update("lexicon_Hebrew_$lang",
                                                   array('gloss'=>$new));
    }
    
    private function replace_word_in_urls(string $old, string $new) {
        $this->db->where('lex',$old)->where('language','Hebrew')->update('heb_urls',array('lex'=>$new));
    }

    private function replace_words() {
        $to_replace = array(
            // Old lex           New lex       New vocalized lexeme
            '<CTRT===/' => array('<CTRWT/',    "\xd7\xa2\xd6\xb7\xd7\xa9\xd7\x81\xd6\xb0\xd7\xaa\xd6\xbc\xd6\xb8\xd7\xa8\xd6\xb9\xd7\x95\xd7\xaa"),
            '<D>/'      => array('<DW>/',      "\xd7\xa2\xd6\xb4\xd7\x93\xd6\xbc\xd6\xb9\xd7\x95\xd7\x90"),
            '<WDD/'     => array('<DD/',       "\xd7\xa2\xd6\xb9\xd7\x93\xd6\xb5\xd7\x93"),
            '>CTMW</'   => array('>CTM</',     "\xd7\x90\xd6\xb6\xd7\xa9\xd7\x81\xd6\xb0\xd7\xaa\xd6\xbc\xd6\xb0\xd7\x9e\xd6\xb9\xd7\xa2\xd6\xb7"),
            '>DWRM/'    => array('>DRM/',      "\xd7\x90\xd6\xb2\xd7\x93\xd6\xb9\xd7\xa8\xd6\xb8\xd7\x9d"),
            '>HRWN/'    => array('>HRN/',      "\xd7\x90\xd6\xb7\xd7\x94\xd6\xb2\xd7\xa8\xd6\xb9\xd7\x9f"),
            '>L<L>/'    => array('>L<LH/',     "\xd7\x90\xd6\xb6\xd7\x9c\xd6\xb0\xd7\xa2\xd6\xb8\xd7\x9c\xd6\xb5\xd7\x94"),
            '>LJ<JNJ/'  => array('>LJ<NJ/',    "\xd7\x90\xd6\xb1\xd7\x9c\xd6\xb4\xd7\x99\xd7\xa2\xd6\xb5\xd7\xa0\xd6\xb7\xd7\x99"),
            '>WN==/'    => array('>N/',        "\xd7\x90\xd6\xb9\xd7\x9f"),
            'BJT_RXB/'  => array('BJT_RXWB/',  "\xd7\x91\xd6\xbc\xd6\xb5\xd7\x99\xd7\xaa \xd7\xa8\xd6\xb0\xd7\x97\xd6\xb9\xd7\x95\xd7\x91"),
            'BJT_XRWN/' => array('BJT_XWRWN/', "\xd7\x91\xd6\xbc\xd6\xb5\xd7\x99\xd7\xaa \xd7\x97\xd6\xb9\xd7\x95\xd7\xa8\xd6\xb9\xd7\x95\xd7\x9f"),
            'BLC>YR/'   => array('BL>CYR/',    "\xd7\x91\xd6\xbc\xd6\xb5\xd7\x9c\xd6\xb0\xd7\x90\xd7\xa9\xd7\x81\xd6\xb7\xd7\xa6\xd6\xbc\xd6\xb7\xd7\xa8"),
            'BNJMJN/'   => array('BNJMN/',     "\xd7\x91\xd6\xbc\xd6\xb4\xd7\xa0\xd6\xb0\xd7\x99\xd6\xb8\xd7\x9e\xd6\xb4\xd7\x9f"),
            'CJLW/'     => array('CLW=/',      "\xd7\xa9\xd7\x81\xd6\xb4\xd7\x9c\xd6\xb9\xd7\x95"),
            'CPM==/'    => array('CPJM/',      "\xd7\xa9\xd7\x81\xd6\xbb\xd7\xa4\xd6\xbc\xd6\xb4\xd7\x99\xd7\x9d"),
            'DJBWN/'    => array('DJBN/',      "\xd7\x93\xd6\xbc\xd6\xb4\xd7\x99\xd7\x91\xd6\xb9\xd7\x9f"),
            'DW>G/'     => array('D>G/',       "\xd7\x93\xd6\xbc\xd6\xb9\xd7\x90\xd6\xb5\xd7\x92"),
            'DWDWHW/'   => array('DDWHW/',     "\xd7\x93\xd6\xbc\xd6\xb9\xd7\x93\xd6\xb8\xd7\x95\xd6\xb8\xd7\x94\xd7\x95\xd6\xbc"),
            'FJ>WN/'    => array('FJ>N/',      "\xd7\xa9\xd7\x82\xd6\xb4\xd7\x99\xd7\x90\xd6\xb9\xd7\x9f"),
            'FRJWN/'    => array('FRJN/',      "\xd7\xa9\xd7\x82\xd6\xb4\xd7\xa8\xd6\xb0\xd7\x99\xd6\xb9\xd7\x9f"),
            'FWRQ/'     => array('FRQ==/',     "\xd7\xa9\xd7\x82\xd6\xb9\xd7\xa8\xd6\xb5\xd7\xa7"),
            'HRM=/'     => array('HRWM/',      "\xd7\x94\xd6\xb8\xd7\xa8\xd7\x95\xd6\xbc\xd7\x9d"),
            'J<ZJR/'    => array('J<ZR/',      "\xd7\x99\xd6\xb7\xd7\xa2\xd6\xb0\xd7\x96\xd6\xb5\xd7\xa8"),
            'JBC=/'     => array('JBJC=/',     "\xd7\x99\xd6\xb8\xd7\x91\xd6\xb5\xd7\x99\xd7\xa9\xd7\x81"),
            'JBC==/'    => array('JBJC==/',    "\xd7\x99\xd6\xb8\xd7\x91\xd6\xb5\xd7\x99\xd7\xa9\xd7\x81"),
            'JCJMWN=/'  => array('JCJMN/',     "\xd7\x99\xd6\xb0\xd7\xa9\xd7\x81\xd6\xb4\xd7\x99\xd7\x9e\xd6\xb9\xd7\x9f"),
            'JHWCW</'   => array('JHWC</',     "\xd7\x99\xd6\xb0\xd7\x94\xd6\xb9\xd7\x95\xd7\xa9\xd7\x81\xd6\xbb\xd7\xa2\xd6\xb7"),
            'JHWD/'     => array('JHD/',       "\xd7\x99\xd6\xb0\xd7\x94\xd7\x95\xd6\xbc\xd7\x93"),
            'JPW>/'     => array('JPW/',       "\xd7\x99\xd6\xb8\xd7\xa4\xd6\xb9\xd7\x95"),
            'JTJR/'     => array('JTR===/',    "\xd7\x99\xd6\xb7\xd7\xaa\xd6\xbc\xd6\xb4\xd7\xa8"),
            'KJLPWT/'   => array('KJLP/',      "\xd7\x9b\xd6\xbc\xd6\xb5\xd7\x99\xd7\x9c\xd6\xb8\xd7\xa3"),
            'KPRJM=/'   => array('KPJRJM=/',   "\xd7\x9b\xd6\xbc\xd6\xb0\xd7\xa4\xd6\xb4\xd7\x99\xd7\xa8\xd6\xb4\xd7\x99\xd7\x9d"),
            'KSLWT/'    => array('KSWLT/',     "\xd7\x9b\xd6\xbc\xd6\xb0\xd7\xa1\xd7\x95\xd6\xbc\xd7\x9c\xd6\xb9\xd7\xaa"),
            'MJMJN/'    => array('MJMN/',      "\xd7\x9e\xd6\xb4\xd7\x99\xd6\xbc\xd6\xb8\xd7\x9e\xd6\xb4\xd7\x9f"),
            'MR>CH/'    => array('MRCH=/',     "\xd7\x9e\xd6\xb8\xd7\xa8\xd6\xb5\xd7\xa9\xd7\x81\xd6\xb8\xd7\x94"),
            'PRZWT/'    => array('PRZH/',      "\xd7\xa4\xd6\xbc\xd6\xb0\xd7\xa8\xd6\xb8\xd7\x96\xd6\xb8\xd7\x94"),
            'PYLWT/'    => array('PYLH/',      "\xd7\xa4\xd6\xbc\xd6\xb0\xd7\xa6\xd6\xb8\xd7\x9c\xd6\xb8\xd7\x94"),
            'QDRNJT/'   => array('QDRNJT',     "\xd7\xa7\xd6\xb0\xd7\x93\xd6\xb9\xd7\xa8\xd6\xb7\xd7\xa0\xd6\xbc\xd6\xb4\xd7\x99\xd7\xaa"),
            'QR>=/'     => array('QWR>/',      "\xd7\xa7\xd6\xb9\xd7\x95\xd7\xa8\xd6\xb5\xd7\x90"),
            'QWH/'      => array('QW>/',       "\xd7\xa7\xd6\xb0\xd7\x95\xd6\xb5\xd7\x90"),
            'R>MWT=/'   => array('RMT=/',      "\xd7\xa8\xd6\xb8\xd7\x9e\xd6\xb9\xd7\xaa"),
            'TGRMH/'    => array('TWGRMH/',    "\xd7\xaa\xd6\xbc\xd6\xb9\xd7\x95\xd7\x92\xd6\xb7\xd7\xa8\xd6\xb0\xd7\x9e\xd6\xb8\xd7\x94"),
            'TPWX==/'   => array('TPX/',       "\xd7\xaa\xd6\xbc\xd6\xb7\xd7\xa4\xd6\xbc\xd6\xbb\xd7\x97\xd6\xb7"),
            'XKMNJ/'    => array('XKMWNJ/',    "\xd7\x97\xd6\xb7\xd7\x9b\xd6\xb0\xd7\x9e\xd6\xb9\xd7\x95\xd7\xa0\xd6\xb4\xd7\x99"),
            'XLWN=/'    => array('XLN=/',      "\xd7\x97\xd6\xb9\xd7\x9c\xd6\xb9\xd7\x9f"),
            'XNTWN/'    => array('XNTN/',      "\xd7\x97\xd6\xb7\xd7\xa0\xd6\xbc\xd6\xb8\xd7\xaa\xd6\xb9\xd7\x9f"),
            'XRXR=/'    => array('XRXWR/',     "\xd7\x97\xd6\xb7\xd7\xa8\xd6\xb0\xd7\x97\xd7\x95\xd6\xbc\xd7\xa8"),
            'YWPR/'     => array('YPR/',       "\xd7\xa6\xd6\xb9\xd7\xa4\xd6\xb7\xd7\xa8"),
            'YXYXWT/'   => array('YXYXH/',     "\xd7\xa6\xd6\xb7\xd7\x97\xd6\xb0\xd7\xa6\xd6\xb8\xd7\x97\xd6\xb8\xd7\x94"),
            'ZBWLWN/'   => array('ZBWLN/',     "\xd7\x96\xd6\xb0\xd7\x91\xd7\x95\xd6\xbc\xd7\x9c\xd6\xbb\xd7\x9f"),


            // Words who were changed from plural to singular:

            // Old lex         New lex     New vocalized lexeme                                                                        Old English  New English  Old German  New German
            'KRKRWT/'   => array('KRKRH/', "\xd7\x9b\xd6\xbc\xd6\xb4\xd7\xa8\xd6\xb0\xd7\x9b\xd6\xbc\xd6\xb8\xd7\xa8\xd6\xb8\xd7\x94", 'she-camels', 'she-camel', 'Kamelstuten', 'Kamelstute'),
            'MBDLWT/'   => array('MBDLH/', "\xd7\x9e\xd6\xb4\xd7\x91\xd6\xb0\xd7\x93\xd6\xbc\xd6\xb8\xd7\x9c\xd6\xb8\xd7\x94",         'separate places, enclaves', 'separate place, enclave', 'Enklaven', 'Enklave'),
            'ZJQWT/'    => array('ZJQH/',  "\xd7\x96\xd6\xb4\xd7\x99\xd7\xa7\xd6\xb8\xd7\x94",                                         'fire-arrows', 'fire-arrow', 'Brandpfeile', 'Brandpfeil'),
            'ZQJM=/'    => array('ZQ/',    "\xd7\x96\xd6\xb5\xd7\xa7",                                                                 'fire-arrows', 'fire-arrow', 'Brandpfeile', 'Brandpfeil'),
            );

        foreach ($to_replace as $old=>$new) {
            $this->replace_word_in_lexicon($old,$new[0],$new[1]);
            $this->replace_word_in_urls($old,$new[0]);

            if (isset($new[2])) {
                $this->replace_translation($new[0], 'en', $new[2], $new[3]);
                $this->replace_translation($new[0], 'de', $new[4], $new[5]);
            }
        }
    }

    private function replace_stem_in_lexicon(string $lex, string $oldstem, string $newstem) {
        $this->db->where('lex',$lex)->where('vs',$oldstem)->update('lexicon_Hebrew',array('vs'=>$newstem));
    }

    private function replace_stem() {
        $to_replace = array(
            //    Lex     Old stem  New stem
            array('CFH[', 'piel',   'poel'), 
            array('CPV[', 'piel',   'poel'), 
            array('CRC[', 'pual',   'poal'), 
            array('N>Y[', 'hit ',   'htpo'), 
            array('ZRM[', 'piel',   'poel'),
            );

        foreach ($to_replace as $r)
            $this->replace_stem_in_lexicon($r[0],$r[1],$r[2]);
    }

    private function add_in_lexicon(string $lex, string $stem, string $voclex, integer $tally,
                                    string $book, integer $chap, integer $verse,
                                    string $english, string $german) {
        $this->db->insert('lexicon_Hebrew',
                          array(
                              'lex' => $lex,
                              'vs' => $stem,
                              'tally' => $tally,
                              'vocalized_lexeme_utf8' => $voclex,
                              'sortorder' => $this->wit2sort($lex),
                              'firstbook' => $book,
                              'firstchapter' => $chap,
                              'firstverse' => $verse));

        $lex_id = $this->db->insert_id();
        $this->db->insert('lexicon_Hebrew_en', array('lex_id' => $lex_id, 'gloss' => $english));
        $this->db->insert('lexicon_Hebrew_de', array('lex_id' => $lex_id, 'gloss' => $german));
        $this->db->insert('lexicon_Hebrew_da', array('lex_id' => $lex_id, 'gloss' => '*'));
    }

    private function add_words() {
        $to_add = array(
            //    Lex       Stem    Vocalized lexeme                                                            Tally  Book/Chap/Verse    English        German
            array('WCNJ/',  'NA',   "\xd7\x95\xd6\xb7\xd7\xa9\xd7\x81\xd6\xb0\xd7\xa0\xd6\xb4\xd7\x99",         1,     'Chronica_I',6,13, 'Vashni',      'Waschni'),      
            array('>BJCJ/', 'NA',   "\xd7\x90\xd6\xb2\xd7\x91\xd6\xb4\xd7\x99\xd7\xa9\xd7\x81\xd6\xb7\xd7\x99", 19,    'Samuel_I',26,6,   'Abishai',     'Abischai'),     
            array('PRT=/',  'NA',   "\xd7\xa4\xd6\xbc\xd6\xb9\xd7\xa8\xd6\xb8\xd7\xaa",                         2,     'Genesis',49,22,   'tamarisk?',   'Tamariske?'),   
            array('TNJM/',  'NA',   "\xd7\xaa\xd6\xbc\xd6\xb7\xd7\xa0\xd6\xbc\xd6\xb4\xd7\x99\xd7\x9d",         2,     'Ezechiel',29,3,   'sea-monster', 'Seeungeheuer'), 
            array('XJL==[', 'qal',  "\xd7\x97\xd7\x99\xd7\x9c",                                                 5,     'Genesis',8,10,    'wait',        'warten'),       
            array('XJL==[', 'hit',  "\xd7\x97\xd7\x99\xd7\x9c",                                                 5,     'Psalmi',37,7,     'wait',        'warten'),       
            array('XJL==[', 'piel', "\xd7\x97\xd7\x99\xd7\x9c",                                                 5,     'Iob',35,14,       'wait',        'warten'),       
            );

        foreach ($to_add as $ta) {
            $this->add_in_lexicon($ta[0],$ta[1],$ta[2],$ta[3],$ta[4],$ta[5],$ta[6],$ta[7],$ta[8]);
        }
    }

    private function copy_stem_in_lexicon(string $lex, string $oldstem,
                                          string $oldbook, integer $oldchap, integer $oldverse,
                                          string $newstem,
                                          string $newbook, integer $newchap, integer $newverse) {
        $query = $this->db->where('lex',$lex)->where('vs',$oldstem)->get('lexicon_Hebrew');
        if ($query->num_rows()!=1) {
            echo "ERROR: Lex $lex stem $oldstem is not in lexicon_Hebrew\n";
            return;
        }

        $row = $query->row();
        $old_lex_id = $row->id;
        $tally = $row->tally;
        $voclex = $row->vocalized_lexeme_utf8;
        $sortorder = $row->sortorder;
        
        // Update first b/c/v for old stem
        $this->db->where('id',$old_lex_id)->update('lexicon_Hebrew',
                                                   array(
                                                       'firstbook' => $oldbook,
                                                       'firstchapter' => $oldchap,
                                                       'firstverse' => $oldverse));

        // Create entry for new stem
        $this->db->insert('lexicon_Hebrew',
                          array(
                              'lex' => $lex,
                              'vs' => $newstem,
                              'tally' => $tally,
                              'vocalized_lexeme_utf8' => $voclex,
                              'sortorder' => $sortorder,
                              'firstbook' => $newbook,
                              'firstchapter' => $newchap,
                              'firstverse' => $newverse));

        $new_lex_id = $this->db->insert_id();



        
        foreach (array('en','de','da') as $lang) {
            // Fetch gloss from old stem
            $query = $this->db->select('gloss')->where('lex_id',$old_lex_id)->get("lexicon_Hebrew_$lang");
            if ($query->num_rows()!=1) {
                echo "ERROR: Lex $lex stem $oldstem is not in lexicon_Hebrew_$lang\n";
                return;
            }
            $gloss = $query->row()->gloss;

            // Insert gloss into new stem
            $this->db->insert("lexicon_Hebrew_$lang", array('lex_id' => $new_lex_id, 'gloss' => $gloss));
        }
    }
    
    
    private function copy_stems() {
        $to_copy = array(
            //    Lex     Old stem  B/c/v               New stem  B/c/v 
            array('G<C[', 'hit',    'Samuel_II',22,8,  'htpo',    'Jeremia',25,16 ),
            array('CRC[', 'piel',   'Psalmi',52,7,     'poel',    'Jesaia',40,24),
            array('JD<[', 'pual',   'Reges_II',10,11,  'poal',    'Samuel_I',21,3),
            array('S<R[', 'piel',   'Sacharia',7,14,   'poel',    'Hosea',13,3),
            );
        
        foreach ($to_copy as $tc) {
            $this->copy_stem_in_lexicon($tc[0],$tc[1],$tc[2],$tc[3],$tc[4],$tc[5],$tc[6],$tc[7],$tc[8]);
        }
    }

    private function update_statistics()
    {
    //select * from pl_sta_quiztemplate
    //join pl_sta_quiz on pl_sta_quiztemplate.id=pl_sta_quiz.templid
    //join pl_sta_question on pl_sta_question.quizid=pl_sta_quiz.id
    //join pl_sta_displayfeature on pl_sta_question.id=pl_sta_displayfeature.questid
    //where name='visual' and dbname='ETCBC4';

        $word_feat = array(
            "text" => "g_word",
            "text_nopunct_translit" => "g_word_nopunct_translit",
            "text_translit" => "g_word_translit",
            "text_utf8" => "g_word_utf8",
            "text_cons_utf8" => "g_word_cons_utf8",
            "text_nocant_utf8" => "g_word_nocant_utf8",

            "vocalized_lexeme" => "g_voc_lex",
            "vocalized_lexeme_cons_utf8" => "g_voc_lex_cons_utf8",
            "vocalized_lexeme_translit" => "g_voc_lex_translit",
            "vocalized_lexeme_utf8" => "g_voc_lex_utf8",

            "g_qere" => "qere",
            "g_qere_translit" => "qere_translit",
            "g_qere_utf8" => "qere_utf8",
            );


        foreach (array('sta_displayfeature','sta_requestfeature') as $feature) {
            echo "Updating $feature\n";

            $query = $this->db->select('feat.id,feat.name')
                ->from('sta_quiztemplate qt')
                ->join('sta_quiz qz', 'qz.templid=qt.id')
                ->join('sta_question quest', 'quest.quizid=qz.id')
                ->join("$feature feat", 'feat.questid=quest.id')
                ->where('qt.dbname="ETCBC4"')
                ->where('qt.qoname="word"')
                ->get();

            foreach ($query->result() as $row) {
                if (array_key_exists($row->name,$word_feat)) {
                    $this->db->where('id',$row->id)->update($feature, array('name'=>$word_feat[$row->name]));
                    //echo "updated $feature $row->id: $row->name => ",$word_feat[$row->name],"\n";
                }
            }
        }
    }

    
    public function up() {
        $this->remove_words();
        $this->replace_words();
        $this->replace_stem();
        $this->add_words();
        $this->copy_stems();
        $this->update_statistics();
   }

    public function down()
    {
        echo "<pre>Downgrade not possible</pre>";
    }
}
