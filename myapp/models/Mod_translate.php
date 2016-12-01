<?php

class Mod_translate extends CI_Model {
    private $grammar_edit_array; // Used for caching grammar localization information
    private $grammar_show_array;
    private $grammar_comment_array;
    private $grammar_edit_ordered;
    private $grammar_show_ordered;
    private $grammar_comment_ordered;
    private $grammar_db;
    private $grammar_comment_full;
    
    private $if_langs;
    private $lexicon_langs;

    private $dbs = array('ETCBC4',
                         'ETCBC4-translit',
                         'nestle1904');

    
    public function __construct() {
        parent::__construct();
        $this->load->database();

        if (!is_cli()) {
            // Not running from command line. This means we are not doing a migration
            $this->lang->load('users', $this->language);

            $this->if_langs = array('en' => $this->lang->line('english'),
                                    'da' => $this->lang->line('danish'),
                                    'pt' => $this->lang->line('portuguese'),
                                    'es' => $this->lang->line('spanish'),
                                    'zh-simp' => $this->lang->line('simp_chinese'),
                                    'zh-trad' => $this->lang->line('trad_chinese'));

            $this->lexicon_langs = array('heb' => array('en' => $this->lang->line('english'),
                                                        'de' => $this->lang->line('german')),
                                         'aram' => array('en' => $this->lang->line('english'),
                                                         'de' => $this->lang->line('german')),
                                         'greek' => array('en' => $this->lang->line('english')));
        }
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

    public function get_if_lines_part(string $lang_edit, string $lang_show, string $textgroup, integer $limit, integer $offset, string $orderby, string $sortorder) {
        $query = $this->db->select("c.symbolic_name, comment, c.use_textarea, s.text text_show, e.text text_edit")
            ->from('language_comment c')
            ->join("language_$lang_show s", "s.symbolic_name=c.symbolic_name AND s.textgroup=c.textgroup",'left')
            ->join("language_$lang_edit e", "e.symbolic_name=c.symbolic_name AND e.textgroup=c.textgroup",'left')
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
        return $query->result();
    }

    public function update_if_lines(string $lang_edit, string $textgroup, array $post) {
        foreach ($post as $key => $value) {
            if (substr($key,0,6)==='modif-' && $value=="true") {
                $key2 = substr($key,6);
                if ($this->db->from("language_$lang_edit")
                    ->where('textgroup',$textgroup)->where('symbolic_name', $key2)
                    ->count_all_results() == 0) {
                    // A record does not exist, insert one.
                    $this->db->insert("language_$lang_edit", array('textgroup' => $textgroup,
                                                                   'symbolic_name' => $key2,
                                                                   'text' => $this->security->xss_clean($post[$key2])));
                }
                else {
                    // Update existing record
                    $this->db->where('textgroup',$textgroup)->where('symbolic_name', $key2)
                        ->update("language_$lang_edit", array('text' => $this->security->xss_clean($post[$key2])));
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
        

    public function get_grammar_lines_part(string $lang_edit, string $lang_show, string $grammargroup) {
        $this->count_grammar_lines_TODO_change($this->grammar_db, $lang_edit, $lang_show);

        $res = array();

        foreach ($this->grammar_comment_array as $key => $comment) {
            if (($grammargroup=='info' && strpos($key, '.')===false) ||
                substr($key, 0, strlen($grammargroup))===$grammargroup) {
                $data = new stdClass;
                $data->symbolic_name = $key;
                $data->symbolic_name_dash = preg_replace('/\./','--',$key); // Required because the symbolic
                                                                            // name is used as an attribute
                                                                            // in HTML and . is not allowed
                                                                            // there
                $data->text_show = $this->grammar_show_array[$key];
                $data->text_edit = $this->grammar_edit_array[$key];
                $data->comment = $comment;
                $res[] = $data;
            }
        }
        return $res;
    }


    
    
    public function count_grammar_lines_TODO_change(string $db, string $lang_edit, string $lang_show) {
        $this->grammar_db = $db;
        
        $query = $this->db->select('json')->where('db',$db)->where('lang',$lang_edit)->get('db_localize');
        $l10n_edit = $query->row()->json;

        $query = $this->db->select('json')->where('db',$db)->where('lang',$lang_show)->get('db_localize');
        $l10n_show = $query->row()->json;

        $query = $this->db->select('json')->where('db',$db)->where('lang','comment')->get('db_localize');
        $l10n_comment = $query->row()->json;

        $this->grammar_edit_array = array();
        $this->grammar_show_array = array();
        $this->grammar_comment_array = array();
        $this->build_grammar_array('', json_decode($l10n_edit,true), $this->grammar_edit_array);
        $this->build_grammar_array('', json_decode($l10n_show,true), $this->grammar_show_array);
        $this->build_grammar_array('', json_decode($l10n_comment,true), $this->grammar_comment_array);

        // Sanity check
        $count = count($this->grammar_edit_array);
        if ($count != count($this->grammar_show_array))
            throw new DataException("Localization information for language $lang_edit is incompatible with language $lang_show");
        if ($count != count($this->grammar_comment_array))
            throw new DataException("Localization information for language $lang_edit is incompatible with comments information");

        $this->grammar_edit_ordered = array();
        $this->grammar_show_ordered = array();
        $this->grammar_comment_ordered = array();

        foreach ($this->grammar_edit_array as $k => $v)
            $this->grammar_edit_ordered[] = $k;

        foreach ($this->grammar_show_array as $k => $v)
            $this->grammar_show_ordered[] = $k;

        foreach ($this->grammar_comment_array as $k => $v)
            $this->grammar_comment_ordered[] = $k;

        assert($count == count($this->grammar_edit_ordered));
        assert($count == count($this->grammar_show_ordered));
        assert($count == count($this->grammar_comment_ordered));
        
        for ($i=0; $i<$count; ++$i) {
            if ($this->grammar_edit_ordered[$i]!=$this->grammar_show_ordered[$i])
                throw new DataException("Localization information for language $lang_edit is incompatible with language $lang_show");
            if ($this->grammar_edit_ordered[$i]!=$this->grammar_comment_ordered[$i])
                throw new DataException("Localization information for language $lang_edit is incompatible with comments information");
        }
        
        return $count;
    }
    
//    public function get_grammar_lines_part(integer $limit, integer $offset) {
//        $res = array();
//        $endpos = min($offset + $limit, count($this->grammar_show_array));
//        
//        for ($i=$offset; $i<$endpos; ++$i) {
//            $data = new stdClass;
//            $key = $this->grammar_edit_ordered[$i];
//            $data->symbolic_name = $key;
//            $data->symbolic_name_dash = preg_replace('/\./','--',$key); // Required because the symbolic
//                                                                        // name is used as an attribute
//                                                                        // in HTML and . is not allowed
//                                                                        // there
//            $data->text_show = $this->grammar_show_array[$key];
//            $data->text_edit = $this->grammar_edit_array[$key];
//            $data->comment = $this->grammar_comment_array[$key];
//            $res[] = $data;
//        }
//        return $res;
//    }

    public function update_grammar_lines(string $lang_edit, string $db, array $post) {
        $this->grammar_db = $db;
        
        $query = $this->db->select('json')->where('db',$db)->where('lang',$lang_edit)->get('db_localize');
        $l10n_edit = $query->row()->json;

        $query = $this->db->select('json')->where('db',$db)->where('lang','comment')->get('db_localize');
        $l10n_comment = $query->row()->json;

        $this->grammar_edit_array = array();
        $this->grammar_comment_array = array();
        $this->build_grammar_array('', json_decode($l10n_edit,true), $this->grammar_edit_array);
        $this->build_grammar_array('', json_decode($l10n_comment,true), $this->grammar_comment_array);

        foreach ($post as $key => $value) {
            if (substr($key,0,6)==='modif-' && $value=="true") {
                $key2 = substr($key,6);                    // Remove 'modif-'
                $key3 = preg_replace('/--/', '.', $key2);  // Change '--' to '.'

                if (!isset($this->grammar_edit_array[$key3]))
                    throw new DataException("Unknown key: $key3");

                $newval = $this->security->xss_clean($post[$key2]);
                $this->grammar_edit_array[$key3] =
                    substr($this->grammar_comment_array[$key3],0,5)=='f:num'
                    ? intval($newval)
                    : $newval;
            }
        }

        $this->db->where('db',$db)->where('lang',$lang_edit)
            ->update('db_localize', array('json' => $this->build_grammar_json($this->grammar_edit_array)) );
    }

    public function count_lexicon_lines(string $src_lang) {
        switch ($src_lang) {
          case 'Hebrew':
          case 'Aramaic':
                $query = $this->db->select('count(*) as count')->where('language',$src_lang)->get('lexicon_heb');
                break;
                
          case 'Greek':
                $query = $this->db->select('count(*) as count')->get('lexicon_greek');
                break;
        }
        return $query->row()->count;
    }

    public function get_glosses(string $src_lang, string $lang_edit, string $lang_show, string $from, string $to) {
        switch ($src_lang) {
          case 'heb':
          case 'aram':
                $query = $this->db->select('lex,vs,vocalized_lexeme_utf8 lexeme,firstbook,firstchapter,firstverse,s.gloss text_show, e.gloss text_edit,c.id lex_id')
                    ->from('lexicon_heb c')
                    ->join("lexicon_heb_$lang_show s", 's.lex_id=c.id','left')
                    ->join("lexicon_heb_$lang_edit e", 'e.lex_id=c.id','left')
                    ->where('language',$src_lang=='heb' ? 'Hebrew' : 'Aramaic')
                    ->where('sortorder >=',$from)
                    ->where('sortorder <',$to)
                    ->order_by('sortorder')
                    ->get();
                break;

          case 'greek':
                $query = $this->db->select('strongs,strongs_unreliable,lemma lexeme,firstbook,firstchapter,firstverse,s.gloss text_show, e.gloss text_edit,c.id lex_id')
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
          case 'aram':
                $table = "lexicon_heb_{$dst_lang}";
                break;

          case 'greek':
                $table = "lexicon_greek_{$dst_lang}";
                break;

          default:
                throw new DataException($this->lang->line('illegal_lang_code'));
        }

        foreach ($post as $key => $value) {
            if (substr($key,0,6)==='modif-' && $value=="true") {
                $key2 = substr($key,6);

                $update = array('lex_id' => $key2, 'gloss' => $post[$key2]);
        
                if ($this->db->from($table)->where('lex_id',$key2)->count_all_results() == 0)
                    // A record does not exist, insert one.
                    $this->db->insert($table, $update);
                else {
                    // Update existing record
                    $this->db->where('lex_id',$key2)->update($table, $update);
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

    public function get_frequent_glosses(string $src_lang, string $lang_edit, string $lang_show, integer $gloss_count) {
        switch ($src_lang) {
          case 'heb':
          case 'aram':
                $query = $this->db->select('lex,vs,vocalized_lexeme_utf8 lexeme,firstbook,firstchapter,firstverse,s.gloss text_show, e.gloss text_edit,tally,c.id lex_id')
                    ->from('lexicon_heb c')
                    ->join("lexicon_heb_$lang_show s", 's.lex_id=c.id','left')
                    ->join("lexicon_heb_$lang_edit e", 'e.lex_id=c.id','left')
                    ->where('language',$src_lang=='heb' ? 'Hebrew' : 'Aramaic')
                    ->order_by('tally','DESC')
                    ->limit(2*$gloss_count)
                    ->get();

                
                $last_lex = '';
                $result = array();
                foreach ($query->result() as $row) {
                    // Stop when we have enough lexemes and we reach a distinct lexeme with a low tally
                    if ($row->lex!==$last_lex && count($result)>=$gloss_count && $tally > $row->tally)
                        break;

                    $tally = $row->tally;
                    $result[] = $row;
                    $last_lex = $row->lex;
                }
                assert(count($result)>=$gloss_count); // To ensure that the SQL LIMIT is high enough
                break;

          case 'greek':
                $query = $this->db->select('strongs,strongs_unreliable,lemma lexeme,firstbook,firstchapter,firstverse,s.gloss text_show, e.gloss text_edit,c.id lex_id')
                    ->from('lexicon_greek c')
                    ->join("lexicon_greek_$lang_show s", 's.lex_id=c.id','left')
                    ->join("lexicon_greek_$lang_edit e", 'e.lex_id=c.id','left')
                    ->order_by('tally','DESC')
                    ->limit($gloss_count)
                    ->get();

                $result = $query->result();
                break;
        }

        return $result;
        
    }

    // $lang may be 'comment'
    function if_db2php(string $lang, string $dest) {
        $this->load->helper('file');
        @mkdir("$dest/$lang"); // Fails if directory exists, but that's OK

        $query = $this->db->distinct()->select('textgroup')->get("language_$lang");

        foreach ($query->result() as $row) {
            $ofile = fopen("$dest/$lang/{$row->textgroup}_lang.php","w");
            if (fwrite($ofile, "<?php\n\n")===false)
                die("Cannot write to file \"$dest/$lang/{$row->textgroup}_lang.php\"\n");
                
            $query2 = $this->db->where('textgroup',$row->textgroup)->get("language_$lang");

            if ($lang=='comment') {
                foreach ($query2->result() as $row2) {
                    $text = preg_replace(array('/"/',
                                               "/\n$/",
                                               "/\n/"),
                                         array('\\"',
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
                                               "/\n$/",
                                               "/\n/"),
                                         array('\\"',
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

    // $src is the source directory where the php files are found
    function if_php2db(string $short_langname, string $src) {
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
                
            $lang = array();
            include($src.DIRECTORY_SEPARATOR.$file);
                
            foreach ($lang as $key => $text) {
                if (!isset($format[$key]))
                    die("Key $key missing from textgroup $short_file in comment table\n");
                    
                if ($format[$key]!='keep_blanks')
                    $text = preg_replace('/\s+/',' ',$text); // Remove extraneous whitespace
                    
                $toinsert[] = array('textgroup' => $short_file,
                                    'symbolic_name' => $key,
                                    'text' => $text);
            }

            $this->db->insert_batch('language_'.$short_langname, $toinsert);
        }
    }

    function if_phpcomment2db(string $src) {
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
        
    function gram_db2prop(string $dest) {
        $this->load->helper('file');
        $query = $this->db->get('db_localize');

        foreach ($query->result() as $row) {
            if (!write_file("$dest/$row->db.$row->lang.prop.pretty.json",
                            json_encode(json_decode($row->json), JSON_PRETTY_PRINT)))
                die("Cannot write to file \"$dest/$row->db.$row->lang.prop.pretty.json\"\n");
        }
    }
    
    function gram_prop2db() {
        $this->load->helper('directory');
        $d = directory_map('db/property_files', 1); // A value of 2 allows us to recognize empty directories

        foreach ($d as $file) {
            if (preg_match('/(.*)\.(.*)\.prop.pretty.json$/', $file, $matches)) {
                list($filename,$db,$lang) = $matches;
                echo "Handling file $filename - ";
                $input = file_get_contents("db/property_files/$filename");
                $props = json_decode($input);
                if (is_null($props))
                    die("Error in JSON input\n");


                $query = $this->db->where('db',$db)->where('lang',$lang)->get('db_localize');

                if ($query->num_rows() == 0) {
                    // A record does not exist, insert one.
                    $this->db->insert('db_localize',array('db' => $db,
                                                          'lang' => $lang,
                                                          'json' => json_encode($props)));
                    echo "inserted +++++\n";
                }
                else {
                    // A record exists, update it if it changes
                    $encoded_json = json_encode($props);
                        
                    $row = $query->row();
                    if ($row->json != $encoded_json) {
                        $this->db->where('id',$row->id)->update('db_localize',array('json' => $encoded_json));
                        echo "updated +++++\n";
                    }
                    else
                        echo "unchanged\n";
                }
            }
        }
    }
  }