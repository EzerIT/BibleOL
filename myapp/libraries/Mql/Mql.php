<?php

class MqlException extends Exception {
    public $db_error;
    public $compiler_error;

    public function __construct($db_error, $compiler_error) {
        parent::__construct("MQL error");
        $this->db_error = $db_error;
        $this->compiler_error = $compiler_error;
    }
}



class Mql extends CI_Driver_Library {
	protected $valid_drivers	= array('extern','native');
    public $emdros_db;
    public $mql_list = '';
    private $driver;

    function __construct(array $params) {
        $this->emdros_db = $params['db'];
        $this->driver = $this->{$params['driver']}; // Loads driver object
        $this->driver->init(); // This cannot be done in the driver's constructor because it has no
                               // parameters and doesn't know its parent
    }

    public function exec($mql_cmd, $quick_harvest=false) {
        //echo "<pre>MQL&gt;$mql_cmd&lt;MQL</pre>";
        //echo "MQL: $mql_cmd\n";
        $this->mql_list .= "$mql_cmd\n";

        return $this->driver->exec($mql_cmd, $quick_harvest);
    }
}
 
