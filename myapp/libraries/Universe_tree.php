<?php

  // Models a Node as it is known in jstree
class Node {
    public $data;
    public $state;
    public $attr;
    public $children;
    private $ms;
    private $ident;

    // No type on $name, as it can be both a string (book name) or an integer (chapter number)
    public function __construct($name, OlMonadSet $ms, integer $hier_level, boolean $is_leaf, string $ident) {
        if ($hier_level<1 || $hier_level>count(Universe_tree::$CI->db_config->dbinfo->universeHierarchy))
            $this->data = "$name"; // Must be string
        else {
            $hier_type = Universe_tree::$CI->db_config->dbinfo->universeHierarchy[$hier_level-1]->type;
            $label = Universe_tree::$CI->loc->universe->$hier_type->_label;
            if (isset(Universe_tree::$CI->loc->universe->$hier_type->$name))
                $name = Universe_tree::$CI->loc->universe->$hier_type->$name;

            $this->data = sprintf($label,$name);
        }

        if (!$is_leaf) {
            $this->state = 'closed';
            $this->children = array();
        }
        $this->attr = array('data-ref' => $ident,
                            'data-rangelow' => $ms->low(),
							'data-rangehigh' => $ms->high(),
                            'data-lev' => $hier_level);
        $this->ms = $ms;
        $this->ident = $ident;
    }

    public function getLevel() {
        return $this->attr['data-lev'];
    }

    public function get_monadset() {
        return $this->ms;
    }

    public function add(Node $child) {
        $this->children[] = $child;
    }
   
    public function getIdent() {
        if (empty($this->ident))
            return '';
        else
            return "$this->ident:";
    }
  }


class Universe_tree {
    public static $CI;

	private $markedList;
	//private Vector<TreePath> m_savedPaths = new Vector<TreePath>();

    public $top;


	public function __construct(array $params=null) {
		self::$CI =& get_instance();
		//self::$CI->load->helper(array('file','directory'));

        if (!is_null($params)) {
            $this->markedList = $params['markedList'];
            
            $full_universe = self::$CI->mod_askemdros->fullUniverse();
            $this->top = new Node('Everything', $full_universe, 0, false, '');

            $this->addLevel($this->top);
            $this->top->state = 'open'; // Top level is open
        }
	}

    public function get_jstree() {
        return json_encode($this->top);
    }

    private static function startswith(string $haystack, string $needle) {
        return substr($haystack, 0, strlen($needle))===$needle;
    }


	private function searchMarked(string $marked) {
		if ($this->markedList==null)
			return 0;

		$markedC = "$marked:";
        foreach ($this->markedList as $id) {
            if ($id===$marked)
                return 2;
            if (self::startswith($id,$markedC))
                return 1;
        }
		return 0;
	}


    private function addlevel(Node $parent) {
        $parent_level = $parent->getLevel();
        $child_is_leaf = $parent_level+1 == count(self::$CI->db_config->dbinfo->universeHierarchy);
        $child_mss = self::$CI->mod_askemdros->getMonadsAtLevel($parent->get_monadset(), $parent_level);

        foreach ($child_mss as $feat=>$ms) {
            $child_ident = $parent->getIdent() . $feat;
            $child = new Node($feat, $ms, $parent_level+1, $child_is_leaf, $child_ident);

            $parent->add($child);

			switch ($this->searchMarked($child_ident)) {
				case 1: // Partial match
					$this->addLevel($child);
					break;
				case 2: // Full match
//					m_savedPaths.add(new TreePath(child.getPath()));
					break;
            }
        }
    }


    public function expand_level(integer $rangelow, integer $rangehigh, $ref, integer $lev) {
        $res = array();
        $ms = new OlMonadSet();
        $ms->addOne($rangelow, $rangehigh);

        $child_is_leaf = $lev == count(self::$CI->db_config->dbinfo->universeHierarchy);
        $child_mss = self::$CI->mod_askemdros->getMonadsAtLevel($ms, $lev-1);

        foreach ($child_mss as $feat=>$ms) {
            $child_ident = "$ref:$feat";
            $res[] = new Node($feat, $ms, $lev, $child_is_leaf, $child_ident);
        }
        return $res;
    }
}