<?php

class Migration_Exam_mode_3 extends CI_Migration {
    public function __construct() {
        parent::__construct();

        $CI =& get_instance();
        $CI->language = 'english';
    }

    public function up() {

        echo "Add column 'archived' to {$this->db->dbprefix}exam\n";

        $this->dbforge->add_column('exam', ['archived' => [ 'type' => 'INT',
                                                            'default' => 0,
                                                            'after' => 'examcodehash']]);
   }

    public function down()
    {
        echo "<pre>Downgrade not possible</pre>";
    }
}

