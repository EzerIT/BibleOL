<?php

class Mod_translate extends CI_Model {
    private $grammar_edit_array; // Used for caching grammar localization information
    private $grammar_edit_array_main; // Common information
    private $grammar_show_array;
    private $grammar_comment_array;
    private $grammar_db;
    private $grammar_comment_full;
    
    private $if_langs;
    private $lexicon_langs;

    private $dbs = array('ETCBC4',
                         'ETCBC4-translit',
                         'nestle1904');

    private $min_tally = 5;
    
    public function __construct() {
        parent::__construct();
        $this->load->database();

        // Not running from command line. This means we are not doing a migration
        $this->lang->load('users', $this->language);

        $this->if_langs = array('en' => $this->lang->line('english'),
                                'da' => $this->lang->line('danish'),
                                'de' => $this->lang->line('german'),
                                'fr' => $this->lang->line('french'),
                                'nl' => $this->lang->line('dutch'),
                                'pt' => $this->lang->line('portuguese'),
                                'es' => $this->lang->line('spanish'),
                                'zh-simp' => $this->lang->line('simp_chinese'),
                                'zh-trad' => $this->lang->line('trad_chinese'),
                                'am' => $this->lang->line('amharic'),
            );

        
        $this->load->helper(array('directory','translation'));

        $this->lexicon_langs = array();
        $allf = directory_map('db',1);

        foreach ($allf as $af) {
            if (self::endswith($af, '.glosslang.json')) {
                $glosslang = json_decode($this->read_or_throw("db/$af"));

                foreach ($glosslang->from as $src_lang) {
                    $this->lexicon_langs[$src_lang] = array();
                    foreach ($glosslang->to as $dst_lang) {
                        $this->lexicon_langs[$src_lang][$dst_lang] = $this->lang->line(Language::$dst_lang_abbrev[$dst_lang]);
                    }
                }
            }
        }
    }

    /// Utility function which checks if one string ends with another string.
    /// @param $haystack String in which to search.
    /// @param $needle String to search for.
    /// @return True if $haystack ends with $needle, false otherwise.
    private static function endswith(string $haystack, string $needle) {
        return substr($haystack, -strlen($needle))===$needle;
    }

    /// Reads all the contents of a file.
    /// @param $filename The name of the file to read
    /// @return The content of the file.
    /// @throws DataException if the file cannot be read.
    private function read_or_throw(string $filename) {
        $data = file_get_contents($filename);
        if ($data===false)
            throw new DataException("Missing file: $filename");
        return $data;
    }

        
    public function get_all_if_languages() {
        return $this->if_langs;
    }

    public function get_all_db() {
        return $this->dbs;
    }

    public function get_all_lexicon_langs() {
        return $this->lexicon_langs;
    }

    public function get_if_lines_part(string $lang_edit, string $lang_show, string $textgroup, int $limit, int $offset, string $orderby, string $sortorder) {
        $lang_edit_table = MY_Lang::create_variant_lang_table($lang_edit);
        if (!$lang_edit_table)
            $lang_edit_table = "language_{$lang_edit}";

        $query = $this->db->select("c.symbolic_name, comment, c.use_textarea, s.text text_show, e.text text_edit")
            ->from('language_comment c')
            ->join("language_$lang_show s", "s.symbolic_name=c.symbolic_name AND s.textgroup=c.textgroup",'left')
            ->join("$lang_edit_table e", "e.symbolic_name=c.symbolic_name AND e.textgroup=c.textgroup",'left')
            ->where('c.textgroup',$textgroup)
            ->order_by($orderby,$sortorder)->limit($limit,$offset)->get();
        return $query->result();
    }

    // The language_comment table is the canonical list of symbolic_name values
    public function count_if_lines(string $textgroup) {
        $query = $this->db->select('count(*) as count')->where('textgroup',$textgroup)->get("language_comment");
        return $query->row()->count;
    }

    public function get_textgroup_list() {
        $query = $this->db->distinct()->select('textgroup')->get('language_comment');
        $textgroups = $query->result();
        $res = array();
        
        foreach ($textgroups as $tg)
            $res[] = $tg->textgroup;

        return $res;
    }

    public function get_if_untranslated(string $lang_edit) {
        $query = $this->db->select("c.symbolic_name, c.textgroup")
            ->from('language_comment c')
            ->join("language_$lang_edit e", "e.symbolic_name=c.symbolic_name AND e.textgroup=c.textgroup",'left')
            ->where('e.text IS NULL')
            ->order_by('textgroup,symbolic_name')->get();
        return $query->result_array();
    }

    public function update_if_lines(string $lang_edit, string $textgroup, array $post) {
        if (!empty($_SESSION['variant']))
            $lang_edit2 = $lang_edit . '_' . $_SESSION['variant'];  // Append variant to language abbreviation
        else
            $lang_edit2 = $lang_edit;

        foreach ($post as $key => $value) {
            $deleted = false; // Has variant version been deleted?
            
            if (substr($key,0,6)==='modif-' && $value=="true") {
                $key2 = substr($key,6);
                $text = trim($this->security->xss_clean($post[$key2]));

                if (!empty($_SESSION['variant'])) {
                    $query = $this->db->select('text')    // Get global version of text
                        ->where('textgroup',$textgroup)
                        ->where('symbolic_name',$key2)
                        ->get("language_{$lang_edit}");

                    $row = $query->row();
                    if (empty($text) || ($row && $row->text==$text)) {
                        // Variant is empty or equal to global, delete variant
                        $this->db
                            ->where('textgroup',$textgroup)
                            ->where('symbolic_name',$key2)
                            ->delete("language_{$lang_edit2}");
                        $deleted = true;
                    }
                }

                if (!$deleted) {
                    if ($this->db->from("language_{$lang_edit2}")
                        ->where('textgroup',$textgroup)->where('symbolic_name', $key2)
                        ->count_all_results() == 0) {
                        // A record does not exist, insert one.
                        $this->db->insert("language_{$lang_edit2}", array('textgroup' => $textgroup,
                                                                          'symbolic_name' => $key2,
                                                                          'text' => $this->security->xss_clean($post[$key2])));
                    }
                    else {
                        // Update existing record
                        $this->db->where('textgroup',$textgroup)->where('symbolic_name', $key2)
                            ->update("language_{$lang_edit2}", array('text' => $this->security->xss_clean($post[$key2])));
                    }
                }
            }
        }
    }

    private function build_grammar_array(string $prefix, array $src, array &$dst) {
        foreach ($src as $k => $v) {
            $key = $prefix==="" ? $k : "$prefix.$k";
            if (is_array($v))
                $this->build_grammar_array($key, $v, $dst);
            else
                $dst[$key] = $v;
        }
    }

    private function build_grammar_json(array $src) {
        $json = array();
        
        foreach ($src as $key => $value) {
            $keys = explode('.',$key);
            $target = &$json;
    
            foreach ($keys as $k) {
                if (!isset($target[$k]))
                    $target[$k] = array();
                $target = &$target[$k];
            }
            
            $target = $value; //$type=='n' ? intval($value) : $value;
        }
        return json_encode($json);
    }

    public function get_grammargroup_list(string $db) {
        $this->grammar_db = $db;
        $query = $this->db->select('json')->where('db',$db)->where('lang','comment')->get('db_localize');
        $this->grammar_comment_full = json_decode($query->row()->json, true);

        $res = array('info');
        
        foreach ($this->grammar_comment_full as $l1 => $v1) {
            if (is_array($v1))
                foreach ($v1 as $l2 => $v2) {
                    $res[] = "$l1.$l2";
                }
        }

        return $res;
    }

    private function recursive_count(array $a) {
        $count = 0;
        foreach ($a as $v) {
            if (is_array($v))
                $count += $this->recursive_count($v);
            else
                ++$count;
        }
        return $count;
    }
          
    private function count_non_array(array $a) {
        $count = 0;
        foreach ($a as $v) {
            if (!is_array($v))
                ++$count;
        }
        return $count;
    }
          
    
    public function count_grammar_lines(string $grammargroup) {
        if ($grammargroup=='info')
            return $this->count_non_array($this->grammar_comment_full);
        else {
            list($l1, $l2) = explode('.', $grammargroup);
            return $this->recursive_count($this->grammar_comment_full[$l1][$l2]);
        }
    }
        
    private function get_l10n_and_build(string $lang, &$dst, string $variant=null) {
        $row = $this->db->select('json')->where('db',$this->grammar_db)->where('lang',$lang)
            ->get($variant ? "db_localize_$variant" : 'db_localize')
            ->row();

        if ($row)
            $l10n = $row->json;
        else
            $l10n = '[]';

        $dst = array();
        $this->build_grammar_array('', json_decode($l10n,true), $dst);
    }

    public function get_grammar_untranslated(string $lang_edit) {
        $res = array();
        
        foreach ($this->grammar_comment_array as $k => $v) {
            if (!isset($this->grammar_edit_array[$k]) || $this->grammar_edit_array[$k]==='') {
                if (strpos($k, '.')===false)
                    $res[] = array('grammargroup' => 'info', 'symbolic_name' => $k);
                else {
                    if (preg_match('/([^.]*\.[^.]*)\.(.*)/', $k, $matches)) {
                        list($all,$group,$name) = $matches;
                        $res[] = array('grammargroup' => $group, 'symbolic_name' => $name);
                    }
                    else
                        throw new DataException("Illegal key found in grammar comment for $lang_edit");
                }
            }
        }
        return $res;
    }

    public function get_grammar_lines_part(string $lang_edit, string $lang_show, string $grammargroup) {
        $this->load->helper('varset');

        if (!empty($_SESSION['variant']))
            $this->get_l10n_and_build($lang_edit, $this->grammar_edit_array, $_SESSION['variant']);
        else
            $this->get_l10n_and_build($lang_edit, $this->grammar_edit_array);
        
        $this->get_l10n_and_build($lang_show, $this->grammar_show_array);
        $this->get_l10n_and_build('comment', $this->grammar_comment_array);

        // Sanity check
        $count = count($this->grammar_show_array);

        if (empty($_SESSION['variant'])) {
            if ($count != count($this->grammar_edit_array) ||
                count(array_diff_key($this->grammar_edit_array,$this->grammar_show_array))!=0)
                throw new DataException("Localization information for language $lang_edit is incompatible with language $lang_show");
        }

        if ($count != count($this->grammar_comment_array) ||
            count(array_diff_key($this->grammar_show_array,$this->grammar_comment_array))!=0)
            throw new DataException("Localization information for language $lang_show is incompatible with comments information");

        $res = array();

        foreach ($this->grammar_comment_array as $key => $comment) {
            if (($grammargroup=='info' && strpos($key, '.')===false) ||
                (substr($key, 0, strlen($grammargroup))===$grammargroup && $key[strlen($grammargroup)]=='.')) {
                $data = new stdClass;
                $data->symbolic_name = $key;
                $data->symbolic_name_dash = preg_replace('/\./','--',$key); // Required because the symbolic
                                                                            // name is used as an attribute
                                                                            // in HTML and . is not allowed
                                                                            // there
                $data->text_show = $this->grammar_show_array[$key];
                $data->text_edit = set_or_default($this->grammar_edit_array[$key], '');
                $data->comment = $comment;
                $res[] = $data;
            }
        }
        return $res;
    }

    public function update_grammar_lines(string $lang_edit, string $db, array $post) {
        $this->grammar_db = $db;

        if (!empty($_SESSION['variant'])) {
            $this->get_l10n_and_build($lang_edit, $this->grammar_edit_array, $_SESSION['variant']);
            $this->get_l10n_and_build($lang_edit, $this->grammar_edit_array_main);
        }
        else
            $this->get_l10n_and_build($lang_edit, $this->grammar_edit_array);

        $this->get_l10n_and_build('comment', $this->grammar_comment_array);

        foreach ($post as $key => $value) {
            $deleted = false; // Has variant version been deleted?

            if (substr($key,0,6)==='modif-' && $value=="true") {
                $key2 = substr($key,6);                    // Remove 'modif-'
                $key3 = preg_replace('/--/', '.', $key2);  // Change '--' to '.'

                if (!isset($this->grammar_comment_array[$key3]))
                    throw new DataException("Unknown key: $key3");

                $newval = trim($this->security->xss_clean($post[$key2]));

                if (!empty($_SESSION['variant'])) {
                    if (empty($newval) || $newval==$this->grammar_edit_array_main[$key3]) {
                        // Variant is empty or equal to global, delete variant
                        unset($this->grammar_edit_array[$key3]);
                        $deleted = true;
                    }
                }

                if (!$deleted)
                    $this->grammar_edit_array[$key3] =
                        substr($this->grammar_comment_array[$key3],0,5)=='f:num'
                        ? intval($newval)
                        : $newval;
            }
        }

        $table = !empty($_SESSION['variant']) ? "db_localize_{$_SESSION['variant']}" : 'db_localize';
        
        if ($this->db
            ->from($table)
            ->where('db',$db)->where('lang',$lang_edit)
            ->count_all_results() == 0) {
            // A record does not exist, insert one.
            $this->db->insert($table, array('db' => $db,
                                            'lang' => $lang_edit,
                                            'json' => $this->build_grammar_json($this->grammar_edit_array)));
        }
        else {
            // Update existing record
            $this->db->where('db',$db)->where('lang',$lang_edit)
                ->update($table,
                         array('json' => $this->build_grammar_json($this->grammar_edit_array)) );
        }
    }

    public function get_glosses(string $src_lang, string $lang_edit, string $lang_show, string $from, string $to) {
        if (!empty($_SESSION['variant']))
            $lang_edit .= '_' . $_SESSION['variant'];  // Append variant to language abbreviation

        switch ($src_lang) {
          case 'heb':
          case 'aram':
                $src_lexicon = $src_lang=='heb' ? 'Hebrew' : 'Aramaic';

                $query = $this->db->select('lex,vs,CONCAT(vocalized_lexeme_utf8," ",roman) lexeme,firstbook,firstchapter,firstverse,s.gloss text_show, e.gloss text_edit,tally,c.id lex_id')
                    ->from("lexicon_{$src_lexicon} c")
                    ->join("lexicon_{$src_lexicon}_{$lang_show} s", 's.lex_id=c.id','left')
                    ->join("lexicon_{$src_lexicon}_{$lang_edit} e", 'e.lex_id=c.id','left')
                    ->where('sortorder >=',$from)
                    ->where('sortorder <',$to)
                    ->order_by('sortorder,roman')
                    ->get();
                break;

          case 'greek':
                $query = $this->db->select('strongs,strongs_unreliable,lemma lexeme,firstbook,firstchapter,firstverse,s.gloss text_show, e.gloss text_edit,tally,c.id lex_id')
                    ->from('lexicon_greek c')
                    ->join("lexicon_greek_$lang_show s", 's.lex_id=c.id','left')
                    ->join("lexicon_greek_$lang_edit e", 'e.lex_id=c.id','left')
                    ->where('sortorder >=',$from)
                    ->where('sortorder <',$to)
                    ->order_by('sortorder')
                    ->get();
                break;
        }
                
        return $query->result();
    }

    public function update_glosses(string $src_lang, string $dst_lang, array $post) {
        switch ($src_lang) {
          case 'heb':
                $table = "lexicon_Hebrew_{$dst_lang}";
                break;

          case 'aram':
                $table = "lexicon_Aramaic_{$dst_lang}";
                break;

          case 'greek':
                $table = "lexicon_greek_{$dst_lang}";
                break;

          default:
                throw new DataException($this->lang->line('illegal_lang_code'));
        }

        if (!empty($_SESSION['variant']))
            $table2 = $table . '_' . $_SESSION['variant'];  // Append variant to language abbreviation
        else
            $table2 = $table;

        foreach ($post as $key => $value) {
            $deleted = false; // Has variant version been deleted?
 
            if (substr($key,0,6)==='modif-' && $value=="true") {
                $key2 = substr($key,6);
                $text = trim($this->security->xss_clean($post[$key2]));

                $update = array('lex_id' => $key2, 'gloss' => $post[$key2]);
        
                if (!empty($_SESSION['variant'])) {
                    $query = $this->db->select('gloss')    // Get global version of gloss
                        ->where('lex_id',$key2)
                        ->get($table);

                    $row = $query->row();
                    if (empty($text) || ($row && $row->gloss==$text)) {
                        // Variant is empty or equal to global, delete variant
                        $this->db
                            ->where('lex_id',$key2)
                            ->delete($table2);
                        $deleted = true;
                    }
                }

                if (!$deleted) {
                    if ($this->db->from($table2)->where('lex_id',$key2)->count_all_results() == 0) {
                        // A record does not exist, insert one.
                        $this->db->insert($table2, $update);
                    }
                    else {
                        // Update existing record
                        $this->db->where('lex_id',$key2)->update($table2, $update);
                    }
                }
            }
        }
    }
    
    public function get_localized_ETCBC4() {
        $this->load->library('db_config');

        $this->db_config->init_config("ETCBC4","ETCBC4", $this->language_short, true);
        $l10n = json_decode($this->db_config->l10n_json,true);
        return array($l10n['emdrostype']['verbal_stem_t'], $l10n['universe']['reference']);
    }

    public function get_localized_nestle1904() {
        $this->load->library('db_config');

        $this->db_config->init_config("nestle1904","nestle1904", $this->language_short, true);
        $l10n = json_decode($this->db_config->l10n_json,true);
        return array(array(), $l10n['universe']['reference']);
    }


    public function get_frequent_glosses(string $src_lang, string $lang_edit, string $lang_show, int $gloss_start, int $gloss_count) {
        if (!empty($_SESSION['variant']))
            $lang_edit .= '_' . $_SESSION['variant'];  // Append variant to language abbreviation

        switch ($src_lang) {
          case 'heb':
          case 'aram':
                $src_lexicon = $src_lang=='heb' ? 'Hebrew' : 'Aramaic';

                $query = $this->db->select('lex, tally, sortorder')
                    ->from("lexicon_{$src_lexicon}")
                    ->order_by('tally DESC, sortorder ASC')
                    ->limit($gloss_count, $gloss_start)
                    ->where('tally >',$this->min_tally)
                    ->distinct()
                    ->get();

                $relevant_lex = array();
                foreach ($query->result() as $row)
                    $relevant_lex[] = '"' . $row->lex . '"';

                $query = $this->db->select('lex,vs,CONCAT(vocalized_lexeme_utf8," ",roman) lexeme,firstbook,firstchapter,firstverse,s.gloss text_show, e.gloss text_edit,tally,c.id lex_id')
                    ->from("lexicon_{$src_lexicon} c")
                    ->join("lexicon_{$src_lexicon}_{$lang_show} s", 's.lex_id=c.id','left')
                    ->join("lexicon_{$src_lexicon}_{$lang_edit} e", 'e.lex_id=c.id','left')
                    ->where("c.lex in (" . implode($relevant_lex, ',') . ")")
                    ->order_by('tally DESC, sortorder ASC')
                    ->get();

                break;

          case 'greek':
                $query = $this->db->select('strongs,strongs_unreliable,lemma lexeme,firstbook,firstchapter,firstverse,s.gloss text_show, e.gloss text_edit,tally,c.id lex_id')
                    ->from('lexicon_greek c')
                    ->join("lexicon_greek_$lang_show s", 's.lex_id=c.id','left')
                    ->join("lexicon_greek_$lang_edit e", 'e.lex_id=c.id','left')
                    ->where('tally >',$this->min_tally)
                    ->order_by('tally DESC, sortorder ASC')
                    ->limit($gloss_count, $gloss_start)
                    ->get();
                break;
        }

        return $query->result();
    }

    public function get_number_glosses(string $src_lang) {
        switch ($src_lang) {
          case 'heb':
          case 'aram':
                $src_lexicon = $src_lang=='heb' ? 'Hebrew' : 'Aramaic';

                $query = $this->db->select('COUNT(DISTINCT `lex`) c')
                    ->where('tally >',$this->min_tally)
                    ->get("lexicon_{$src_lexicon}");
                break;

          case 'greek':
                $query = $this->db->select('COUNT(`lemma`) c')
                    ->where('tally >',$this->min_tally)
                    ->get("lexicon_greek");
                break;
        }
        return $query->row()->c;
    }

    // $lang may be 'comment', or it may contain '_<variant>'
    public function if_db2php(string $lang, string $dest) {
        $this->load->helper('file');
        @mkdir("$dest/$lang"); // Fails if directory exists, but that's OK

        $query = $this->db->distinct()->select('textgroup')->get("language_$lang");

        foreach ($query->result() as $row) {
            $ofile = fopen("$dest/$lang/{$row->textgroup}_lang.php","w");
            if (fwrite($ofile, "<?php\n\n")===false)
                die("Cannot write to file \"$dest/$lang/{$row->textgroup}_lang.php\"\n");
                
            $query2 = $this->db->where('textgroup',$row->textgroup)->order_by('symbolic_name')->get("language_$lang");

            if ($lang=='comment') {
                foreach ($query2->result() as $row2) {
                    $text = preg_replace(array('/"/',
                                               "/\r/",
                                               "/\n$/",
                                               "/\n/"),
                                         array('\\"',
                                               '',
                                               '\\n',
                                               "\\n\"\n        . \""),
                                         $row2->comment);
                    if (fwrite($ofile, '$comment[\'' . $row2->symbolic_name . '\'] = "' . $text . "\";\n")===false ||
                        fwrite($ofile, '$format[\'' . $row2->symbolic_name . '\'] = "' . $row2->format . "\";\n")===false ||
                        fwrite($ofile, '$use_textarea[\'' . $row2->symbolic_name . '\'] = ' . ($row2->use_textarea?'true':'false') . ";\n\n")===false)
                        die("Cannot write to file \"$dest/$lang/{$row->textgroup}_lang.php\"\n");
                }
            }
            else {
                foreach ($query2->result() as $row2) {
                    $text = preg_replace(array('/"/',
                                               "/\r/",
                                               "/\n$/",
                                               "/\n/"),
                                         array('\\"',
                                               '',
                                               '\\n',
                                               "\\n\"\n        . \""),
                                         $row2->text);
                    if (fwrite($ofile, '$lang[\'' . $row2->symbolic_name . '\'] = "' . $text . "\";\n")===false)
                        die("Cannot write to file \"$dest/$lang/{$row->textgroup}_lang.php\"\n");
                }
            }
            fclose($ofile);
        }
    }

    // $src is the source directory where the php files are found.
    // $short_langname may contain '_<variant>'.
    public function if_php2db(string $short_langname, string $src) {
        $this->load->helper('directory');
        $this->load->dbforge();
        $this->dbforge->drop_table('language_'.$short_langname,true);
                
        $this->dbforge->add_field(array('id' => array('type' => 'INT', 'auto_increment' => true),
                                        'textgroup' => array('type'=>'VARCHAR(25)'),
                                        'symbolic_name' => array('type'=>'TINYTEXT'),
                                        'text' => array('type'=>'TEXT')));
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('textgroup');
        $this->dbforge->create_table('language_'.$short_langname);

        $d = directory_map($src, 1);

        foreach ($d as $file) {
            // Loop through files

            if (!preg_match('/_lang.php$/', $file))
                continue;

            $short_file = substr($file, 0, -9); // Remove '_lang.php'
            if ($short_file=='db' || $short_file=='email')
                continue;

            $comment_query = $this->db->where('textgroup',$short_file)->get('language_comment');
            $format = array();
            foreach ($comment_query->result() as $row)
                $format[$row->symbolic_name] = is_null($row->format) ? "" : $row->format;
            
            $toinsert = array();
            $toinsertcomment = array();
                
            $lang = array();
            include($src.DIRECTORY_SEPARATOR.$file);
                
            foreach ($lang as $key => $text) {
                if (!isset($format[$key])) {
                    echo "Key $key missing from textgroup $short_file in comment table -- I will add it\n";
                    $toinsertcomment[] = array('textgroup' => $short_file,
                                               'symbolic_name' => $key,
                                               'comment' => null,
                                               'format' => null,
                                               'use_textarea' => 0);
                    $format[$key] = '';
                }
                    
                if ($format[$key]!='keep_blanks')
                    $text = preg_replace('/\s+/',' ',$text); // Remove extraneous whitespace
                    
                $toinsert[] = array('textgroup' => $short_file,
                                    'symbolic_name' => $key,
                                    'text' => $text);
            }

            if (!empty($toinsert))
                $this->db->insert_batch('language_'.$short_langname, $toinsert);

            if (!empty($toinsertcomment)) 
                $this->db->insert_batch('language_comment', $toinsertcomment);
        }
    }

    public function if_phpcomment2db(string $src) {
        $this->load->helper('directory');
        $this->load->dbforge();
        $this->dbforge->drop_table('language_comment',true);
                
        $this->dbforge->add_field(array('id' => array('type' => 'INT', 'auto_increment' => true),
                                        'textgroup' => array('type'=>'TINYTEXT'),
                                        'symbolic_name' => array('type'=>'TINYTEXT'),
                                        'comment' => array('type'=>'TEXT',
                                                           'null' => true,
                                                           'default' => null),
                                        'format' => array('type'=>'TINYTEXT',
                                                           'null' => true,
                                                           'default' => null),
                                        'use_textarea' => array('type' => 'TINYINT(1)')
                                      ));
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('language_comment');

        $d = directory_map($src, 1);

        foreach ($d as $file) {
            // Loop through files

            if (!preg_match('/_lang.php$/', $file))
                continue;

            $short_file = substr($file, 0, -9); // Remove '_lang.php'
            if ($short_file=='db' || $short_file=='email')
                continue;

            $toinsert = array();
                
            $comment = array();
            $format = array();
            $use_textarea = array();
            include($src.DIRECTORY_SEPARATOR.$file);
                
            foreach ($comment as $key => $text) {
                $toinsert[] = array('textgroup' => $short_file,
                                    'symbolic_name' => $key,
                                    'comment' => empty($comment[$key]) ? null : $comment[$key],
                                    'format' => empty($format[$key]) ? null : $format[$key],
                                    'use_textarea' => $use_textarea[$key]);
            }

            $this->db->insert_batch('language_comment', $toinsert);
        }
    }
        
    public function gram_db2prop(string $dest, string $variant=null) {
        $this->load->helper('file');

        $query = $this->db->get($variant ? "db_localize_{$variant}" : 'db_localize');

        foreach ($query->result() as $row) {
            $fulllang = $variant ? "{$row->lang}_{$variant}" : $row->lang;
            if (!write_file("{$dest}/{$row->db}.{$fulllang}.prop.pretty.json",
                            json_encode(json_decode($row->json), JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) . "\n"))
                die("Cannot write to file \"{$dest}/{$row->db}.{$fulllang}.prop.pretty.json\"\n");
        }
    }
    
    public function gram_prop2db(string $src, string $variant=null) {
        $this->load->helper('directory');
        $d = directory_map($src, 1);

        $varstring = $variant ? "_{$variant}" : '';
        $table = $variant ? "db_localize_{$variant}" : 'db_localize';

        foreach ($d as $file) {
            if (preg_match('/(.*)\.([^_]*)' . $varstring . '\.prop.pretty.json$/', $file, $matches)) {
                list($filename,$db,$lang) = $matches;
                echo "Handling file $filename - ";
                $input = file_get_contents("$src/$filename");
                $props = json_decode($input);
                if (is_null($props))
                    die("Error in JSON input\n");


                $query = $this->db->where('db',$db)->where('lang',$lang)->get($table);

                if ($query->num_rows() == 0) {
                    // A record does not exist, insert one.
                    $this->db->insert($table,array('db' => $db,
                                                   'lang' => $lang,
                                                   'json' => json_encode($props)));
                    echo "inserted +++++\n";
                }
                else {
                    // A record exists, update it if it changes
                    $encoded_json = json_encode($props);
                        
                    $row = $query->row();
                    if ($row->json != $encoded_json) {
                        $this->db->where('id',$row->id)->update($table,array('json' => $encoded_json));
                        echo "updated +++++\n";
                    }
                    else
                        echo "unchanged\n";
                }
            }
        }
    }

    private function lex_common(string $src_lang, string &$src_language, array &$header_array) {
        // Creates $header_array as an array of arrays, where the first value is the Emdros name for
        // a verbal stem (or null), and the second value is the English heading for a column in the
        // CSV file
        switch ($src_lang) {
          case 'heb':
                $src_language = 'Hebrew';
          $header_array = array(array(null, 'Occurrences'),
                                array(null, 'lex'),
                                array(null, 'Lexeme'),
                                array('NA', 'None'),
                                array('qal', 'Qal'),
                                array('nif', 'Nifal'),
                                array('piel', 'Piel'),
                                array('pual', 'Pual'),
                                array('hit', 'Hitpael'),
                                array('hif', 'Hifil'),
                                array('hof', 'Hofal'),
                                array('hsht', 'Hishtafal'),
                                array('pasq', 'Passive Qal'),
                                array('etpa', 'Etpaal'),
                                array('nit', 'Nitpael'),
                                array('hotp', 'Hotpaal'),
                                array('tif', 'Tifal'),
                                array('htpo', 'Hitpoal'),
                                array('poal', 'Poal'),
                                array('poel', 'Poel'));
                break;

          case 'aram':
                $src_language = 'Aramaic';
                $header_array = array(array(null, 'Occurrences'),
                                      array(null, 'lex'),
                                      array(null, 'Lexeme'),
                                      array('NA', 'None'),
                                      array('peal', 'Peal'),
                                      array('peil', 'Peil'),
                                      array('pael', 'Pael'),
                                      array('haf', 'Hafel'),
                                      array('afel', 'Afel'),
                                      array('shaf', 'Shafel'),
                                      array('hof', 'Hofal'),
                                      array('htpe', 'Hitpeel'),
                                      array('htpa', 'Hitpaal'),
                                      array('hsht', 'Hishtafal'),
                                      array('etpe', 'Etpeel'),
                                      array('etpa', 'Etpaal'));
                break;

          case 'greek':
                $src_language = 'greek';
                $header_array = array(array(null, 'Occurrences'),
                                      array(null, 'Lexeme'),
                                      array(null, "Strong's number"),
                                      array(null, "Strong's unreliable?"),
                                      array(null, 'Gloss'));
                break;

          default:
                throw new DataException($this->lang->line('illegal_source_language'));
        }
    }
    
    public function empty_lex(string $src_lang, string $dst_lang, string $variant) {
        return $this->db->select('id')->get("lexicon_{$src_lang}_{$dst_lang}_{$variant}")->num_rows()==0;
    }

    public function download_lex(string $src_lang, string $dst_lang, string $variant=null) {
        $src_language="";
        $header_array=array();
        $this->lex_common($src_lang, $src_language, $header_array);

        if (!array_key_exists($dst_lang, $this->lexicon_langs[$src_lang]))
            throw new DataException($this->lang->line('illegal_target_language'));


        $result = '';
        
        switch ($src_lang) {
          case 'heb':
          case 'aram':
                // Find names of used verbal stems
                $query = $this->db->select('vs')->distinct()
                    ->where('vs <>','NA')
                    ->order_by('vs')
                    ->get("lexicon_{$src_language}");

                $stems = array('NA');
        
                foreach ($query->result() as $row)
                    $stems[] = $row->vs;

                $stem_sort_order = array();
                foreach ($header_array as $h) {
                    $result .= '"' . $h[1] . '",';
                    if (!is_null($h[0]))
                        $stem_sort_order[] = $h[0];
                }

                $result[strlen($result)-1] = "\n"; // Replace final , with \n
                       
                // We make very specific assumptions about the available stems:
                assert(count($stems)===count($stem_sort_order),"Stem count mismatch");
                foreach ($stems as $st)
                    assert(in_array($st,$stem_sort_order),"Missing stem $st");

                if ($variant) {
                    $query = $this->db->select('lex,tally,vs,CONCAT(vocalized_lexeme_utf8," ",roman) lexeme,gloss')
                        ->from("lexicon_{$src_language} c")
                        ->join("lexicon_{$src_language}_{$dst_lang}_{$variant}", 'lex_id=c.id')
                        ->order_by('sortorder,lex,vs')
                        ->get();
                }
                else {
                    $query = $this->db->select('lex,tally,vs,CONCAT(vocalized_lexeme_utf8," ",roman) lexeme,gloss')
                        ->from("lexicon_{$src_language} c")
                        ->join("lexicon_{$src_language}_{$dst_lang}", 'lex_id=c.id','left')
                        ->order_by('sortorder,lex,vs')
                        ->get();
                }
                
                $res = array();
                foreach ($query->result() as $row) {
                    if (!isset($res[$row->lex]))
                        $res[$row->lex] = array('tally' => $row->tally,
                                                'lexeme' => $row->lexeme,
                                                'glosses' => array());
                    $res[$row->lex]['glosses'][$row->vs] = $row->gloss;
                }
                
                foreach ($res as $lex => $r) {
                    $result .=
                        $r['tally'] .
                        ',"' . $lex . '"' .
                        ',"' . $r['lexeme'] . '"';
                    
                    foreach ($stem_sort_order as $st) {
                        if (isset($r['glosses'][$st]))
                            $result .= ',"' . str_replace('"', '""', $r['glosses'][$st]) . '"';
                        else
                            $result .= ',';
                    }
                    $result .= "\n";
                }
                break;

          case 'greek':
                foreach ($header_array as $h)
                    $result .= '"' . $h[1] . '",';
                $result[strlen($result)-1] = "\n"; // Replace final , with \n

                if ($variant) {
                    $query = $this->db->select('tally,lemma,strongs,strongs_unreliable,gloss')
                        ->from("lexicon_{$src_language} c")
                        ->join("lexicon_{$src_language}_{$dst_lang}_{$variant}", 'lex_id=c.id')
                        ->order_by('sortorder,strongs,strongs_unreliable')
                        ->get();
                }
                else {
                    $query = $this->db->select('tally,lemma,strongs,strongs_unreliable,gloss')
                        ->from("lexicon_{$src_language} c")
                        ->join("lexicon_{$src_language}_{$dst_lang}", 'lex_id=c.id','left')
                        ->order_by('sortorder,strongs,strongs_unreliable')
                        ->get();
                }

                foreach ($query->result() as $row) {
                    $result .=
                        $row->tally .
                        ',"' . $row->lemma . '"' .
                        ',' . $row->strongs .
                        ',' . ($row->strongs_unreliable ? '"yes"' : '"no"')  .
                        ',"' . str_replace('"', '""', $row->gloss) . '"' .
                        "\n";
                }                    
                break;
                
        }
        return $result;
    }

    public function import_lex(string $src_lang, string $dst_lang, string $csv_file, string $variant=null) {
        $this->load->helper('create_lexicon_helper');

        $src_language="";
        $header_array=array();
        $this->lex_common($src_lang, $src_language, $header_array);

        if (!array_key_exists($dst_lang, $this->lexicon_langs[$src_lang]))
            throw new DataException($this->lang->line('illegal_target_language'));

        $h = @fopen($csv_file, 'r');
        if ($h===false)
            throw new DataException("Cannot open file '$csv_file'");

        $record = fgetcsv($h);
        assert(count($record)==count($header_array),"Wrong number of fields in header");
        for ($i=0; $i<count($record); ++$i)
            assert($record[$i]==$header_array[$i][1],"Illegal field '$record[$i]' in header");

        create_lexicon_table($src_language, $dst_lang, $variant);
        
        $toinsert = array();

        $header_count = count($header_array);
        for ($i=0; $i<$header_count; ++$i)
            if (!is_null($header_array[$i][0])) {
                $first_stem = $i;
                break;
            }

        // The verbal stems (including 'NA') are found at indexes $first_stem to $header_count-1
        
        while (($record = fgetcsv($h)) !== false) {
            switch ($src_lang) {
              case 'heb':
              case 'aram':
                    for ($hix=$first_stem; $hix<$header_count; ++$hix) {
                        if (empty($record[$hix]))
                            continue;
                        
                        $query = $this->db
                            ->select('id')
                            ->where('lex', $record[1])
                            ->where('vs', $header_array[$hix][0])
                            ->get("lexicon_{$src_language}");

                        $row = $query->row();
                        assert(!is_null($row));
                        $toinsert[] = array('lex_id' => $row->id,
                                            'gloss' => $record[$hix]);
                    }
                    break;

              case 'greek':
                    $query = $this->db
                        ->select('id,lemma')
                        ->where('strongs', $record[2])
                        ->where('strongs_unreliable', $record[3]=='yes')
                        ->get("lexicon_{$src_language}");
            
                    foreach ($query->result() as $row) {
                        // The following comparison is required because of duplicate uses of Strong's number.
                        // PHP's comparison is stricter than MySQL's, therefore the
                        // check is done here rather than in the SQL statement
                        if ($row->lemma == $record[1]) 
                            $toinsert[] = array('lex_id' => $row->id,
                                                'gloss' => $record[4]);
                    }
                    break;
            }
        }

        if (!empty($toinsert))
            $this->db->insert_batch($variant ? "lexicon_{$src_language}_{$dst_lang}_{$variant}"
                                             : "lexicon_{$src_language}_{$dst_lang}",
                                    $toinsert);
    } 
  }