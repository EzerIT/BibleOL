<?php

class Migration_Exam_mode extends CI_Migration {
    public function __construct() {
        parent::__construct();

        $CI =& get_instance();
        $CI->language = 'english';
    }

    public function up() {

        if ($this->db->table_exists('exam')) {
            echo "Exam table already exists\n";
            return;
        }

        echo "Add column 'tot_questions' to {$this->db->dbprefix}sta_quiz\n";
        $this->dbforge->add_column('sta_quiz', ['tot_questions' => ['type' => 'INT',
                                                                    'null' => false,
                                                                    'default' => 0,
                                                                    'comment' => 'Number of questions actualy answered by the user',
                                                                    'after' => 'grading']]);


        echo "Create {$this->db->dbprefix}exam\n";

        $this->dbforge->add_field(['id'            => ['type' => 'INT',         'null' => false, 'auto_increment' => true,
                                                       'comment' => 'Exam ID'],
                                   'exam_name'     => ['type' => 'VARCHAR(45)', 'null' => false],
                                   'ownerid'       => ['type' => 'INT',         'null' => false,
                                                       'comment' => 'ID of the owner of the exercise (from the Users table)'],
                                   'examcode'      => ['type' => 'TEXT',        'null' => true,
                                                       'comment' => 'The actual XML text of the Exam template. Going forward, '
                                                                 .'we may create more columns to capture elemnts of the XML code '
                                                                 .'that need to be used all the time to make decoding the XML text '
                                                                 .'unnecessary in most cases.'],
                                   'examcodehash'  => ['type' => 'TEXT',        'null' => false,
                                                       'comment' => 'A hash value of the examcode field. It can be used to '
                                                                 .'speed up the comparison of the examcode field from different '
                                                                 .'entries in this table: If the examcodehash values are different, '
                                                                 .'then the examcode values will also be different.']]);
        $this->dbforge->add_key('id', true);
        $this->dbforge->create_table('exam',['comment' => 'Each entry on this table defines a new exam to be performed.']);
        
        // Foreign key on classid in userclass table
        if (!$this->db->query("ALTER TABLE {$this->db->dbprefix}exam ADD FOREIGN KEY (ownerid) REFERENCES {$this->db->dbprefix}user(id) ON DELETE CASCADE ON UPDATE CASCADE"))
            echo "    ERROR: Foregin key (ownerid) on {$this->db->dbprefix}exam failed\n";
        else
            echo "    Foregin key (ownerid) on {$this->db->dbprefix}exam is OK\n";
        
        
        echo "Create {$this->db->dbprefix}exam_active\n";
        $this->dbforge->add_field(['id'              => ['type' => 'INT',         'null'    => false, 'auto_increment' => true],
                                   'exam_name'       => ['type' => 'VARCHAR(45)', 'null'    => false],
                                   'class_id'        => ['type' => 'INT',         'null'    => false],
                                   'exam_start_time' => ['type' => 'INT',         'null'    => false],
                                   'exam_end_time'   => ['type' => 'INT',         'null'    => false],
                                   'exam_length'     => ['type' => 'INT',         'default' => null],
                                   'exam_id'         => ['type' => 'INT',         'default' => null],
                                   'instance_name'   => ['type' => 'VARCHAR(45)', 'null'    => false]]);
        $this->dbforge->add_key('id', true);
        $this->dbforge->create_table('exam_active');

        echo "Create {$this->db->dbprefix}exam_finished\n";
        $this->dbforge->add_field(['id'           => ['type' => 'INT', 'null' => false, 'auto_increment' => true],
                                   'userid'       => ['type' => 'INT', 'null' => false],
                                   'activeexamid' => ['type' => 'INT', 'null' => false]]);
        $this->dbforge->add_key('id', true);
        $this->dbforge->create_table('exam_finished');


        echo "Create {$this->db->dbprefix}exam_results\n";
        $this->dbforge->add_field(['id'           => ['type' => 'INT', 'null' => false, 'auto_increment' => true],
                                   'userid'       => ['type' => 'INT', 'default' => null],
                                   'activeexamid' => ['type' => 'INT', 'default' => null],
                                   'quizid'       => ['type' => 'INT', 'default' => null],
                                   'quiztemplid'  => ['type' => 'INT', 'default' => null]]);
        $this->dbforge->add_key('id', true);
        $this->dbforge->create_table('exam_results');

        echo "Create {$this->db->dbprefix}exam_status\n";
        $this->dbforge->add_field(['id'           => ['type' => 'INT', 'null' => false, 'auto_increment' => true],
                                   'userid'       => ['type' => 'INT', 'null' => false],
                                   'activeexamid' => ['type' => 'INT', 'null' => false],
                                   'start_time'   => ['type' => 'INT', 'null' => false],
                                   'deadline'     => ['type' => 'INT', 'null' => false,
                                                      'comment' => 'Time that the exam will not be accessible anymore. '
                                                                .'This will equal either the end time of the exam or '
                                                                .'the start_time + duration, whichever happens first.']]);
        $this->dbforge->add_key('id', true);
        $this->dbforge->create_table('exam_status');

        
        echo "NOTE: Remember to add configuration variable 'exams_per_page' to myapp/config/ol.php\n";
   }

    public function down()
    {
        echo "<pre>Downgrade not possible</pre>";
    }
}

