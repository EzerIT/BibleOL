<?php
  // This is intended to use by the CLI only.


class Ctrl_maketypeinfo extends CI_Controller {
    public function __construct() {
        parent::__construct();

        if (!is_cli()) {
            echo '<pre>This command can only be run from the command line</pre>';
            die;
        }
    }

	public function index() {
		if ($this->uri->total_segments()!==3) {
			print "Usage: php index.php maketypeinfo index <databasename>\n";
			die;
		}

        $this->config->load('ol');
        $this->load->helper(array('xmlhandler','sheaf'));
        $this->load->library('db_config');

        $this->load->driver('mql',array('db' => 'db/'.$this->uri->segment(3),
                                        'driver' => $this->config->item('mql_driver')));


        try {
            print json_encode(new TypeInfo(null));
        }
        catch (MqlException $e) {
            if (!empty($e->db_error)) 
                print "MQL database error:\n$e->db_error";
            else
                print "MQL compiler error:\n$e->compiler_error";
        }

    }
  }
