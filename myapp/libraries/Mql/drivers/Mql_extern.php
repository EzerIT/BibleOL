<?php

class Mql_extern extends CI_Driver {
    private $command_line = '/usr/local/bin/mql --xml';
    private $tmpfname; 
    private $descriptorspec;

    public function __destruct() {
        @unlink($this->tmpfname);
        @unlink("{$this->tmpfname}.err");
    }        

    public function init() {
        $this->tmpfname = tempnam(sys_get_temp_dir(), 'mql');
        $this->descriptorspec = array(
            0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
            1 => array("file", $this->tmpfname, "w"),  // stdout is a temporary file. We cannot use a
                                                      // pipe (at least not on Windows) because it
                                                      // blocks on huge amounts of data and
                                                      // ublocking doesn't work
            2 => array("file", "{$this->tmpfname}.err", 'w')
            );
    }

    public function exec($mql_cmd, $quick_harvest=false) {
        $process = proc_open($this->command_line,
                             $this->descriptorspec, $pipes);

        if (is_resource($process)) {
            // $pipes[0] is now a writeable handle connected to child stdin

            fwrite($pipes[0],"USE DATABASE '$this->emdros_db' WITH KEY \"xxxxxxxxxx xxxxxxxxxx xxxxxxxxxx xxxxxxxxxx xxxxxxxxxx xxxxxxxxxx xxxxxxxxxx xxxxxxxxxx\" GO\n");
            fwrite($pipes[0], preg_replace("/GOqxqxqx/","GO",$mql_cmd));
            fclose($pipes[0]);

            // Call proc_close() in order to avoid a deadlock
            $return_value = proc_close($process);

            $contents = file_get_contents($this->tmpfname);

            $mqlresults = harvest($contents, $quick_harvest);
            return array_slice($mqlresults->get_crop()->get_table_or_sheaves(),1); // First item is result of "USE DATABASE" command
        }
    }
  }
