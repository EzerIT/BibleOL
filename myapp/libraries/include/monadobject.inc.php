<?php

/// A MonadObject contains information about a text component found in an Emdros database. This is
/// an abstract class; it is always instantiated as a SingleMonadObject or a MultipleMonadObject. A
/// SingleMonadObject represents a text component at the lowest level, corresponding to a single
/// monad in an Emdros database. This is typically a single word. A MultipleMonadObject represents a
/// text component at a higher level, corresponding to multiple monads in an Emdros database. This
/// is typically a phrase or a clause.
///
/// MonadObject%s are ordered in a hierarchy: A clause may contain several phrases, and each
/// phrase may contain several words. This hierarchy is maintained through the #$parent
/// and #$children fields.
abstract class MonadObject {
    public    $mo;            ///< Emdros data about this text component. (Type: OlMatchedObject)
    private   $parent;        ///< The parent of this text component
    private   $children;      ///< The children of this text component. (Type: List<MonadObject>)
    public    $children_idds; ///< The ID_Ds of the children

    /// Creates a MonadObject.
    /// @param $mo Emdros data about this text component.
    /// @param $has_children True if creating a MultipleMonadObject, false if creating a SingleMonadObject.
    public function __construct(OlMatchedObject $mo, boolean $has_children) {
        $this->mo = $mo;
        if ($has_children) {
            $this->children = array();
            $this->children_idds = array();
        }
        else {
            $this->children = null;
            $this->children_idds = null;
        }
    }

    /// Fetches the parent MonadObject.
    /// @return The parent MonadObject.
    public function get_parent() {
        return $this->parent;
    }

    /// Adds a child MonadObject.
    /// $param $child The child MonadObject
    public function add_child(MonadObject $child) {
        assert('$child->parent===null'); // Ensures that the tree is properly constructed

        $this->children[] = $child;
        $this->children_idds[] = $child->get_id_d();
        $child->parent = $this;
    }

    /// Fetches the OlMatchedObject that corresponds to this MonadObject.
    /// @return The OlMatchedObject that corresponds to this MonadObject.
    public function get_mo() {
        return $this->mo;
    }

    /// Fetches the Emdros ID_D that corresponds to this MonadObject.
    /// @return The Emdors ID_D that corresponds to this MonadObject.
    public function get_id_d() {
        return $this->mo->get_id_d();
    }

    /// Fetches the value of a particular Emdros feature.
    /// @param $name The feature name.
    /// @return The feature value.
    public function get_feature(string $name) {
        return $this->mo->get_feature($name);
    }

    /// Fetches the set of monads described by this MonadObject.
    /// @return The set of monads for this object.
    public function get_monadset() {
        return $this->mo->get_monadset();
    }

    /// Determines if this object is a subset of another MonadObject.
    /// @param $mo Another MonadObject.
    /// @return True if the monad set represented by this MonadObject is a subset of the
    /// monad set represented by the parameter $mo.
    public abstract function contained_in(MonadObject $mo);
}


/// A SingleMonadObject is a MonadObject that represents a text component at the lowest
/// level, corresponding to a single monad in an Emdros database. This is typically a single word.
class SingleMonadObject extends MonadObject {
    private $monad;                ///< The monad number
    public $text;                 ///< The actual word. (The _surface_ in Emdros terms.)
    public $suffix;               ///< The character(s) that follow the word when displaying a full text.
                                   ///  This is typically a space character.
    public $bcv = array();        ///< An array containing book, chapter, and verse information. Index will be the same
                                   ///  as in DatabaseInfo::universeHierarchy.
    public $sameAsNext = array(); ///< An array of booleans indicating if this monad belongs to the same book, chapter,
                                   ///  or verse, respectively as the previous monad.
    public $sameAsPrev = array(); ///< An array of booleans indicating if this monad belongs to the same book, chapter,
                                   ///  or verse, respectively as the next monad.
    public $pics;                 ///< Bible picture references from the resource database
    public $urls;                 ///< Bible URL references from the resource database

    /// Fetches the actual word. (The _surface_ in Emdros terms.)
    /// @return The textual representation of this word.
    public function get_text() {
        return $this->text;
    }

    /// Fetches the word suffix.
    /// @return The word suffix.
    public function get_suffix() {
        return $this->suffix;
    }

    /// Adds book name, chapter number, or verse number information about this word.
    /// @param $x The book name, chapter number, or verse number.
    public function add_bcv(string__OR__integer $x) {
        $this->bcv[] = $x;
    }

    /// Fetches book name, chapter number, and verse number for this word.
    /// @return An array containing book name, chapter number, and verse number.
    public function get_bcv() {
        return $this->bcv;
    }

    /// Sets information about whether the book, chapter, or verse of this word is the same as the
    /// book, chapter, or verse of the previous word.
    /// @param $b True if the book, chapter, or verse of this word is the same as the
    /// book, chapter, or verse of the previous word.
    public function add_sameAsPrev(boolean $b) {
        $this->sameAsPrev[] = $b;
    }

    /// Sets information about whether the book, chapter, or verse of this word is the same as the
    /// book, chapter, or verse of the next word.
    /// @param $b True if the book, chapter, or verse of this word is the same as the
    /// book, chapter, or verse of the next word.
    public function add_sameAsNext(boolean $b) {
        $this->sameAsNext[] = $b;
    }

    /// Sets information about the pictures in the resource database that are relevant for the verse containing this word.
    /// @param $p An array whose 0th element is the book number, the 1st element is the chapter, the 2nd element is the verse,
    /// and the remaining elements are picture numbers.
    public function set_pics(array__OR__null $p) {
        $this->pics = $p;
    }

    /// Sets information about the URLs from the resource database that are relevant for the verse containing this word.
    /// @param $u An array whose elements are arrays with two members: The URL and the URL type.
    public function set_urls(array__OR__null $u) {
        $this->urls = $u;
    }

    /// Creates a SingleMonadObject.
    /// @param $mo Emdros data about this text component.
    public function __construct(OlMatchedObject $mo) {
        parent::__construct($mo,false);

        $this->monad = $mo->get_monadset()->getSingleInteger();

        $CI =& get_instance();
        $dbinfo = $CI->db_config->dbinfo;

        $this->text = $this->get_feature($dbinfo->surfaceFeature);
        $this->suffix = empty($dbinfo->suffixFeature) ? ' ' : $this->get_feature($dbinfo->suffixFeature);
    }

    /// Determines if this object is a subset of another MonadObject.
    /// @param $mo Another MonadObject.
    /// @return True if the monad set represented by this MonadObject is a subset of the
    /// monad set represented by the parameter $mo.
    public function contained_in(MonadObject $mo) {
        return $mo->mo->get_monadset()->containsMonad($this->monad);
    }

    /// Compares this SingleMonadObject with a specified object for order. The monad number is used for the
    /// comparison.
    /// @param $o The object to be compared to. 
    /// @return A negative integer, zero, or a positive integer as the monad number of this object is
    /// less than, equal to, or greater than the monad number of the specified object.
    public function compareTo(SingleMonadObject $o) {
        return $this->monad - $o->monad;
    }
}

/// A MultipleMonadObject is a MonadObject that represents a text component at levels
/// above the lowest level. Objects of this class correspond to multiple monads in an Emdros
/// database. They typically represent a phrase, a clause, or a sentence.
class MultipleMonadObject extends MonadObject {
    /// Creates a MultipleMonadObject.
    /// @param $mo Emdros data about this text component.
    public function __construct(OlMatchedObject $mo) {
        parent::__construct($mo,true);
    }

    /// Determines if this object is a subset of another MonadObject.
    /// @param $mo Another MonadObject.
    /// @return True if the monad set represented by this MonadObject is a subset of the
    /// monad set represented by the parameter $mo.
	public function contained_in(MonadObject $mo) {
        return $mo->mo->get_monadset()->containsMonadSet($this->mo->get_monadset());
    }
}
