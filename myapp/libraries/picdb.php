<?php

class Picdb {
    private $CI;

    public function __construct() {
        $this->CI =& get_instance();
        $this->CI->load->database();
    }
    
    // Returns an array whose 0th element is the book number (because that is what
    // picdb uses), the 1st element is the chapter, the 2nd element is the verse,
    // and the remaining elements are picture numbers.
    public function get_pics(array $bcv) {
        $query = $this->CI->db->select('booknumber,picture')
            ->where('book',$bcv[0])->where('chapter',$bcv[1])->where('verse',$bcv[2])
            ->get('bible_refs');

        $results = $query->result();
        if ($query->num_rows()>0) {
            $res = array(intval($results[0]->booknumber), $bcv[1], $bcv[2]);
            foreach ($results as $row)
                $res[] = intval($row->picture);
            return $res;
        }
        else
            return null;
    }

    /// Retrieves an array whose elements are URL references.
    /// @return An array whose elements are arrays with two members: The URL and the URL type.
    public function get_urls(array $bcv) {
        $query = $this->CI->db->select('url,type')
            ->where('book',$bcv[0])->where('chapter',$bcv[1])->where('verse',$bcv[2])
            ->get('bible_urls');

        $results = $query->result();
        if ($query->num_rows()>0) {
            $res = array();
            foreach ($results as $row)
                $res[] = array($row->url,$row->type);
            return $res;
        }
        else
            return null;
    }
  }