<?php
class Ctrl_migrate extends CI_Controller {
    public function __construct() {
        parent::__construct();

        if (is_cli()) {
            $this->load->database();
        }

        $this->load->library('migration');
    }

    public function index() {
        if (!is_cli()) {
            echo '<pre>This command can only be run from the command line</pre>';
            die;
        }

		if ($this->uri->total_segments()!==1) {
			print "Usage: php index.php migrate\n";
			die;
		}

        $this->config->load('ol');

        $this->load->library('migration');
        $ver = $this->migration->current();

        if ($ver===true)
            echo "Already upgraded\n";
        elseif ($this->migration->error_string())
            echo "Upgrade error: ",$this->migration->error_string(),"\n";
        else
            echo "Upgraded to version $ver\n";
    }
  }
