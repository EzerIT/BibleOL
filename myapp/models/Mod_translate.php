<?php

class Mod_translate extends CI_Model {
    private $grammar_edit_array; // Used for caching grammar localization information
    private $grammar_show_array;
    private $grammar_comment_array;
    private $grammar_edit_ordered;
    private $grammar_show_ordered;
    private $grammar_comment_ordered;
    private $grammar_db;
    
    private $if_langs = array('en' => 'English',
                              'da' => 'Danish',
                              'pt' => 'Portuguese',
                              'es' => 'Spanish',
                              'zh-simp' => 'Chinese (simplified)',
                              'zh-trad' => 'Chinese (traditional)');

    private $dbs = array('ETCBC4',
                         'ETCBC4-translit',
                         'nestle1904');

    private $lexicon_langs = array('heb' => array('en' => 'English', 'de' => 'German'),
                                   'aram' => array('en' => 'English', 'de' => 'German'),
                                   'greek' => array('en' => 'English'));
    
    public function __construct() {
        parent::__construct();
        $this->load->database();
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
        $updates = array();
        
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

    function printit(string $prefix, array $a)
    {
        foreach ($a as $k => $v) {
            $key = $prefix==="" ? $k : "$prefix.$k";
            if (is_array($v))
                printit($key, $v);
            elseif (is_string($v))
                echo "$key:s:$v\n";
            else
                echo "$key:n:$v\n";
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

    
    public function count_grammar_lines(string $db, string $lang_edit, string $lang_show) {
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
    
    public function get_grammar_lines_part(integer $limit, integer $offset) {
        $res = array();
        $endpos = min($offset + $limit, count($this->grammar_show_array));
        
        for ($i=$offset; $i<$endpos; ++$i) {
            $data = new stdClass;
            $key = $this->grammar_edit_ordered[$i];
            $data->symbolic_name = preg_replace('/\./','--',$key); // Required because the symbolic
                                                                   // name is used as an attribute
                                                                   // in HTML and . is not allowed
                                                                   // there
            $data->text_show = $this->grammar_show_array[$key];
            $data->text_edit = $this->grammar_edit_array[$key];
            $data->comment = $this->grammar_comment_array[$key];
            $res[] = $data;
        }
        return $res;
    }

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

        $updates = array();
        
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
                $query = $this->db->select('lex,vs,vocalized_lexeme_utf8 lexeme,firstref,s.gloss text_show, e.gloss text_edit,c.id lex_id')
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
                $query = $this->db->select('strongs,strongs_unreliable,lemma lexeme,firstref,s.gloss text_show, e.gloss text_edit,c.id lex_id')
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

    public function get_frequent_glosses(string $src_lang, string $lang_edit, string $lang_show, integer $gloss_count) {
        switch ($src_lang) {
          case 'heb':
          case 'aram':
                $query = $this->db->select('lex,vs,vocalized_lexeme_utf8 lexeme,firstref,s.gloss text_show, e.gloss text_edit,tally,c.id lex_id')
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
                $query = $this->db->select('strongs,strongs_unreliable,lemma lexeme,firstref,s.gloss text_show, e.gloss text_edit,c.id lex_id')
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

  }