<?php

/// Represents all the tables or sheaves harvested from a single MQL request.
class Crop {
    private $tableOrSheaves = array(); ///< The harvested tables or sheaves

    /// Adds an OlTable or OrSheaf to the Crop.
    /// @param $tos Table or sheaf to add.
    public function add_crop(TableOrSheaf $tos) {
        $this->tableOrSheaves[] = $tos;
    }

    /// Fetches the collection of tables or sheaves in this Crop.
    /// @return The harvested tables or sheaves
    public function get_table_or_sheaves() {
        return $this->tableOrSheaves;
    }
}
   
/// Represents a single table or sheaf harvested from an MQL request.
class TableOrSheaf {
    private $table = null; ///< The harvested OlTable, if any
    private $sheaf = null; ///< The harvested OlSheaf, if any

    /// Sets the harvested OlTable.
    /// @param $t The table to set
    public function set_table(OlTable $t) {
        $this->table = $t;
    }

    /// Fetches the harvested OlTable.
    /// @return The harvested OlTable, null if no table was harvested.
    public function get_table() {
        return $this->table;
    }

    /// Sets the harvested OlSheaf
    /// @param $s The sheaf to set
    public function set_sheaf(OlSheaf $s) {
        $this->sheaf = $s;
    }

    /// Fetches the harvested OlSheaf.
    /// @return The harvested OlSheaf, null if no table was harvested.
    public function get_sheaf() {
        return $this->sheaf;
    }
}


/// Represents a single table harvested from an MQL request. 
/// Note: The implementation works on the assumption that the header and each row always contain the
/// same number of elements.
class OlTable {
    private $header; ///< An array containing the table headers
    private $row = array(); ///< An array of arrays containing the table rows.

    /// Sets the table header.
    /// @param $h An array containing the table headers.
    public function set_header(array $h) {
        $this->header = $h;
    }

    /// Adds a row of data to the table.
    /// @param $r An array containing a single row of the table.
    function add_row(array $r) {
        $this->row[] = $r;
    }

    /// Counts the number of rows in the table.
    /// @return The number of rows in the table.
    function rows() {
        return count($this->row);
    }

    /// Counts the number of columns in the table.
    /// @return The number of columns in the table.
    function cols() {
        return count($this->header);
    }
 
    /// Fetches a single entry in the table header.
    /// @param $col The column index.
    /// @return The header for the specified column.
    function get_header($col) {
        return $this->header[$col];
    }
 
    /// Fetches a single entry in the table.
    /// @param $row The row index.
    /// @param $col The column index.
    /// @return The value at the specified row and column.
    function get_cell($row,$col) {
        return $this->row[$row][$col];
    }
}



/// Represents a single range of monads.
class MonadPair {
    public $low; ///< The low value of the monad range.
    public $high; ///< The high value of the monad range.

    /// Constructs a MonadPair object.
    /// @param $low The low value of the monad range.
    /// @param $high The high value of the monad range.
    function __construct($low,$high) {
        $this->low = intval($low);
        $this->high = intval($high);
    }
}

/// Represents a set of monads.
class OlMonadSet implements Iterator {
    public $segments = array(); ///< An array containing the MonadPair%s for this monad set

    /// For single range monad sets, this function returns the low value of the range.
    /// @return The low value of the single consitutent set of monads.
    public function low() {
        return $this->segments[0]->low;
    }

    /// For single range monad sets, this function returns the high value of the range.
    /// @return The high value of the single consitutent set of monads.
    public function high() {
        return $this->segments[0]->high;
    }

    /// A factory function that creates an OlMonadSet from a string representation of the set.
    /// @param $str A string representing a monad set, for example "{ 1, 4-8, 13 }".
    /// @return A newly created OlMonadSet.
    public static function str2MonadSet(string $str) {
        $me = new OlMonadSet();
        $components = preg_split('/\\b/', $str);

        // { 1, 4-8, 13 } becomes: "{", "1", ", ", "4", "-", "8", ", ", "13", " }"
        // Thus all the numbers have odd indexes
        
        $len = count($components);

        for ($i=1; $i<$len;) {
            $low = intval($components[$i]);
            if ($components[$i+1]==='-') {
                $high = intval($components[$i+2]);
                $me->segments[] = new MonadPair($low, $high);
                $i += 4;
            }
            else {
                $me->segments[] = new MonadPair($low, $low);
                $i += 2;
            }
        }
        
        return $me;
    }

    /// Adds a range of monads to the OlMonadSet.
    /// @param $low The low value of the monad range.
    /// @param $high The high value of the monad range.
    public function addOne($low, $high) {
        $this->segments[] = new MonadPair($low, $high);
        $this->consolidate();
    }


    /// Creates the union of this OlMonadSet with another OlMonadSet. If there are overlaps between
    /// the two sets, the resulting union set is consolidated so that overlaps will only appear once.
    /// @param $mset The OlMonadSet to add to this OlMonadSet.
    public function addSet(OlMonadSet $mset) {
        foreach ($mset->segments as $ms)
            $this->segments[] = $ms;
        $this->consolidate();
    }


    /// Creates the union of this OlMonadSet with another OlMonadSet. In contrast to the addSet()
    /// function, this function does not remove overlaps between the two sets and can therefore only
    /// be sure when the caller is 100% sure that there is no overlap.
    /// @param $mset The OlMonadSet to add to this OlMonadSet.
    public function addSetNoConsolidate(OlMonadSet $mset) {
        foreach ($mset->segments as $ms)
            $this->segments[] = $ms;
    }


    /// Removes overlaps between the monad ranges in this OlMonadSet.
    private function consolidate() {
        $restart = true;

        // Every time we modify segments, we rerun this loop
        while ($restart) {
            $restart = false;
            $size = count($this->segments);

            for ($i1=0; $i1<$size && !$restart; ++$i1) {
                for ($i2=$i1+1; $i2<$size && !$restart; ++$i2) {
                    $p1 = $this->segments[$i1];
                    $p2 = $this->segments[$i2];

                    if ($p1->high+1 < $p2->low || $p2->high+1 < $p1->low) {
                        // This case:
                        //           111111 222222
                        // and this case:
                        //           222222 111111
                        continue;
                    }
            
                    $restart = true;

                    if ($p1->low <= $p2->low) {
                        // This case:
                        //           1111....
                        //             2222....
                        // and this case:
                        //           1111....
                        //           2222....
                        if ($p1->high >= $p2->high) {
                            // This case:
                            //           ....1111
                            //         ....2222
                            // and this case:
                            //           ....1111
                            //           ....2222
                            array_splice($this->segments,$i2,1); // Remove element with index $i2
                        }
                        else {
                            // This case:
                            //           ....1111
                            //             ....2222
                            array_splice($this->segments,$i2,1);  // Must be removed before $i1
                            array_splice($this->segments,$i1,1);
                            $this->segments[] = new MonadPair($p1->low, $p2->high);
                        }
                    }
                    else {
                        // This case:
                        //             1111....
                        //           2222....
                        if ($p2->high >= $p1->high) {
                            // This case:
                            //         ....1111
                            //           ....2222
                            // and this case:
                            //           ....1111
                            //           ....2222
                            array_splice($this->segments,$i1,1); // Remove element with index $i1
                        }
                        else {
                            // This case:
                            //           ....1111
                            //         ....2222
                            array_splice($this->segments,$i2,1);  // Must be removed before $i1
                            array_splice($this->segments,$i1,1);
                            $this->segments[] = new MonadPair($p2->low, $p1->high);
                        }
                    }
                }
            }
        }

        sort($this->segments);
    }


    /// Fetches the number of monads in this OlMonadSet.
    /// @return The number of monads in the set.
    public function size() {
        $count = 0;
        foreach ($this->segments as $mp)
            $count += $mp->high-$mp->low+1;

        return $count;
    }

    /// Retrieves the single monad in a one-monad set.
    /// @return The monad.
    /// @throws DataException if there is more than one monad in the set.
    public function getSingleInteger() {
        if (count($this->segments)==1) {
            $p = $this->segments[0];
            if ($p->low == $p->high)
                return $p->low;
        }

        throw new DataException("Object is not a single monad");
    }

    /// Checks if this OlMonadSet contains a specified monad.
    /// @param $m The monad to look for.
    /// @return True if the OlMonadSet contains the specified monad.
    public function containsMonad($m) {
        foreach ($this->segments as $pc)
            if ($m>=$pc->low && $m<=$pc->high)
                return true;

        return false;
    }

    /// Checks if this OlMonadSet is a superset of another OlMonadSet.
    /// @param $m The OlMonadSet to loo for.
    /// @return True if this OlMonadSet is a superset of the OlMonadSet given as parameter.
    public function containsMonadSet(OlMonadSet $m) {
        foreach ($m->segments as $pe) {
            $found_pe = false;
            foreach ($this->segments as $pc) {
                if ($pe->low>=$pc->low && $pe->high<=$pc->high) {
                    $found_pe = true;
                    break;
                }
            }
            if (!$found_pe)
                return false;
        }
        return true;
    }



    // Iterator interface:

    private $last = PHP_INT_MAX; ///< The current monad
    private $current_pair = null; ///< The MonadPair for the current iteration loop

    /// Part of the Iterator interface.
    public function current() {
        return $this->last;
    }

    /// Part of the Iterator interface.
    public function key() {
        return $this->last;
    }

    /// Part of the Iterator interface.
    public function next() {
        assert($this->current_pair!=null);
        assert($this->last!=PHP_INT_MAX);

        // Are there any more values in current_pair?
        if ($this->last+1 <= $this->current_pair->high)
            ++$this->last;
        else {
            // Find the next pair, if any.
            // Next candidate value is at least 2 larger than last. If it had been
            // only 1 larger, it would have been part of the previous current_pair.

            $newcand = $this->last+2; // Next candidate value
            $min = PHP_INT_MAX;
            foreach ($this->segments as $p) {
                if ($p->low>=$newcand && $p->low<=$min) {
                    $min = $p->low;
                    $this->current_pair = $p;
                }
            }

            if ($min==PHP_INT_MAX)
                $this->current_pair = null;

            $this->last = $min;
        }
    }

    /// Part of the Iterator interface.
    public function valid() {
        return $this->last!=PHP_INT_MAX;
    }

    /// Part of the Iterator interface.
    public function rewind() {
        $min=PHP_INT_MAX;

        foreach ($this->segments as $p) {
            if ($p->low<=$min) {
                $min = $p->low;
                $this->current_pair = $p;
            }
        }

        if ($min==PHP_INT_MAX)
            $this->current_pair = null;

        $this->last = $min;
    }


    /// Converts this object to a string such as "{ 1, 3-8, 15 }".
    /// @return The converted string.
    public function __toString() {
        $first = true;
        $s = "{ ";
    
        foreach ($this->segments as $p) {
            if ($first)
                $first = false;
            else
                $s .= ", ";
            
            if ($p->low==$p->high)
                $s .= $p->low;
            else
                $s .= "$p->low-$p->high";
        }

        return "$s }";
    }
}

/// Represents a single monad object harvested from an MQL request.
class OlMatchedObject {
    public $id_d;     ///< The ID_D of the matched object 
    public $name;     ///< The name of the matched object type
    public $monadset; ///< The set of monads of the matched object 
    public $features; ///< The features of the matched object 
    public $sheaf;    ///< The subordinate sheaf, if any, of the matched object 


    /// Constructs an OlMatchedObject object.
    /// @param $id_d The Emdros ID_D of the matched object.
    /// @param $name The name of the type of the matched object.
    public function __construct(int $id_d, string $name) {
        $this->id_d = $id_d;
        $this->name = $name;
    }

    /// Removes the subordinate sheaf if it is empty.
    public function clear_sheaf_if_empty() {
        if (isset($this->sheaf) and $this->sheaf->isEmpty())
            $this->sheaf = null;
    }

    /// Sets the subordinate sheaf.
    /// @param $shf OlSheaf to set.
    public function set_sheaf(OlSheaf $shf=null) {
        $this->sheaf = $shf;
    }

    /// Fetches the subordinate sheaf, if any.
    /// @return The subordinate sheaf, if any.
    public function get_sheaf() {
        return $this->sheaf;
    }

    /// Sets the monad set for this matched object.
    /// @param $ms The OlMonadSet to set.
    public function set_monadset(OlMonadSet $ms) {
        $this->monadset = $ms;
    }
 
    /// Fetches the monad set for this matched object.
    /// @return The monad set for this matched object.
    public function get_monadset() {
        return $this->monadset;
    }

    /// Sets the features for this matched object.
    /// @param $f An associative array of name=>value pairs for the features of this matched object.
    public function set_features(array $f=null) {
        if (is_null($f))
            $this->features = null;
        else {
            foreach ($f as $name => $val)
                $this->features[$name] = htmlspecialchars($val);
        }
    }

    /// Fetches the features for this matched object.
    /// @return An associative array of name=>value pairs for the features of this matched object.
    public function get_features() {
        return $this->features;
    }
    
    /// Sets a single feature for this matched object.
    /// @param $name The name of the feature.
    /// @param $value The value of the feature.
    public function set_feature(string $name, string $value) {
        return $this->features[$name] = htmlspecialchars($value);
    }

    /// Gets a single feature for this matched object.
    /// @param $name The name of the feature.
    /// @return The value of the feature with the specified name.
    public function get_feature(string $name) {
        return $this->features[$name];
    }

    /// Fetches the Emdros ID_D value of this matched object.
    /// @return The ID_D value.
    public function get_id_d() {
        return $this->id_d;
    }
}


/// Represents a single straw harvested from an MQL request.
class OlStraw {
    private $matched_objects = array(); ///< The OlMatchedObject%s that are part of this straw

    /// Adds a matched object to this straw.
    /// @param $mo The OlMatchedObject to add.
    public function add_matched_object(OlMatchedObject $mo) {
        $this->matched_objects[] = $mo;
    }

    /// Fetches the matched objects for this straw.
    /// @return An array of OlMatchedObject%s that make up this straw.
    public function get_matched_objects() {
        return $this->matched_objects;
    }

    /// Fetches the first matched object for this straw.
    /// @return The first OlMatchedObject%s in this straw.
    public function get_first_matched_object() {
        return $this->matched_objects[0];
    }
}


/// Represents a single sheaf harvested from an MQL request.
class OlSheaf {
    private $straws = array(); ///< The OlStraw%s that make up this sheaf
    private $monadset; ///< The OlMonadSet%s that make up this sheaf (for quick harvest only)

    /// Adds a single straw to this sheaf.
    /// @param $str The OlStraw to add to the sheaf.
    public function add_straw(OlStraw $str) {
        $this->straws[] = $str;
    }

    /// Fetches the straws that make up this sheaf.
    /// @return An array of OlStraw%s that make up this sheaf.
    public function get_straws() {
        return $this->straws;
    }

    /// Fetches the first straw in this sheaf.
    /// @return The first OlStraw in this sheaf.
    public function get_first_straw() {
        return $this->straws[0];
    }

    /// Fetches the number of straws in this sheaf.
    /// @return The number of OlStraw%s in this sheaf.
    public function number_of_straws() {
        return count($this->straws);
    }

    /// Adds a single set of monads to this sheaf (during quick harvesting only).
    /// @param $ms The OlMonadSet to add to the sheaf.
    public function add_monadset(OlMonadSet $ms) {
        $this->monadset[] = $ms;
    }

    /// Fetches the sets of monads that make up this sheaf (for quick harvest only).
    /// @return An array of OlMonadSet%s that make up this sheaf.
    public function get_monadset() {
        return $this->monadset;
    }

    /// Checks if this sheaf contains quickly harvested monad sets?
    /// @return True if this sheaf contains OlMonadSet%s.
    public function has_monadset() {
        return isset($this->monadset);
    }

    /// Checks if this sheaf is empty, i.e., contains neither straws nor monad sets.
    /// @return True if the sheaf is empty.
    function isEmpty() {
        return empty($this->straws) && empty($this->monadset);
    }
}

    
