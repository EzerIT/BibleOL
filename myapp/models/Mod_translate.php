TODO: Revert at textarea
TODO: Post textarea

<?php

class Mod_translate extends CI_Model {
    private $langs = array('en' => 'English',
                           'da' => 'Danish',
                           'pt' => 'Portuguese',
                           'es' => 'Spanish');

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function get_all_languages() {
        return $this->langs;
    }

    public function get_if_lines_part(string $lang_edit, string $lang_show, string $textgroup, integer $limit, integer $offset, string $orderby, string $sortorder) {
        $query = $this->db->select("c.symbolic_name, comment, c.has_lf, s.text text_show, e.text text_edit")
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
  }