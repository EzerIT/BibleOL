<?php


class Suggest_answers {
    static private $database_handles = array();

    /** Retrieves a collection of values to suggest as part of a multiple choice question. The
     * correct value is guaranteed to be among the values suggested.
     * @param $sqlCommand The SQL command that finds the values. Taken from the
     * &lt;alternateshowrequest&gt; element of a .dbxml file.
     * @param $param1 A parameter to substitute in {@code sqlCommand}.
     * @param $correct The correct value for the feature.
     * @param $lower_limit If the number of possible answers is less than this value, the method returns null.
     * @param $upper_limit If the number of possible answers is greater than this value, the method
     * a random subset of the legal values; however, the correct answer is guaranteed to be among
     * the values returned.
     * @return An array of values to suggest as part of a multiple choice question.
     */

    static public function findSuggestions(string $database, string $sqlCommand, string $param1, /*TODO: More than one param?*/
                                    string $correct, integer $lower_limit, integer $upper_limit) {

        if (empty($correct))
            $correct = '-';

        if (!isset(self::$database_handles[$database])) {
            $CI =& get_instance();
            self::$database_handles[$database] = $CI->load->database(array('database' => 'db/' . $database,
                                                                           'dbdriver' => 'sqlite3',
                                                                           'dbprefix' => '',
                                                                           'pconnect' => FALSE,
                                                                           'db_debug' => TRUE,
                                                                           'cache_on' => FALSE,
                                                                           'cachedir' => '',
                                                                           'char_set' => 'utf8',
                                                                           'dbcollat' => 'utf8_general_ci'),
                                                                     true);
        }

        $query = self::$database_handles[$database]->query(sprintf($sqlCommand,$param1));
        
        $results = array();

        foreach ($query->result() as $row)
            $results[] = empty(current($row)) ? '-' : current($row);

        if (count($results)<$lower_limit)
            return null;

        while (count($results)>$upper_limit) {
            $toremove = mt_rand(0, count($results)-1);
            if ($results[$toremove]!==$correct) // Make sure we don't remove correct answer
                array_splice($results,$toremove,1);
        }

        return $results;
    }
  }