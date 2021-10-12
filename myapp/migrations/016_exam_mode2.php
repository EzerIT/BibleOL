<?php

class Migration_Exam_mode2 extends CI_Migration {
    public function __construct() {
        parent::__construct();

        $CI =& get_instance();
        $CI->language = 'english';
    }

    public function up() {
        // Add miscellaneous foreign keys

        if (!$this->db->query("ALTER TABLE {$this->db->dbprefix}exam_active ADD FOREIGN KEY (exam_id) REFERENCES {$this->db->dbprefix}exam (id) ON DELETE CASCADE ON UPDATE NO ACTION"))
            echo "    ERROR: Foregin key (exam_id) on {$this->db->dbprefix}exam_active failed\n";
        else
            echo "    Foregin key (exam_id) on {$this->db->dbprefix}exam_active is OK\n";

        if (!$this->db->query("ALTER TABLE {$this->db->dbprefix}exam_finished ADD FOREIGN KEY (activeexamid) REFERENCES {$this->db->dbprefix}exam_active (id) ON DELETE CASCADE ON UPDATE NO ACTION"))
            echo "    ERROR: Foregin key (exam_id) on {$this->db->dbprefix}exam_finished failed\n";
        else
            echo "    Foregin key (exam_id) on {$this->db->dbprefix}exam_finished is OK\n";

        if (!$this->db->query("ALTER TABLE {$this->db->dbprefix}exam_results ADD FOREIGN KEY (activeexamid) REFERENCES {$this->db->dbprefix}exam_active (id) ON DELETE CASCADE ON UPDATE NO ACTION"))
            echo "    ERROR: Foregin key (exam_id) on {$this->db->dbprefix}exam_results failed\n";
        else
            echo "    Foregin key (exam_id) on {$this->db->dbprefix}exam_results is OK\n";

        if (!$this->db->query("ALTER TABLE {$this->db->dbprefix}exam_status ADD FOREIGN KEY (activeexamid) REFERENCES {$this->db->dbprefix}exam_active (id) ON DELETE CASCADE ON UPDATE NO ACTION"))
            echo "    ERROR: Foregin key (exam_id) on {$this->db->dbprefix}exam_status failed\n";
        else
            echo "    Foregin key (exam_id) on {$this->db->dbprefix}exam_status is OK\n";
   }

    public function down()
    {
        echo "<pre>Downgrade not possible</pre>";
    }
}

