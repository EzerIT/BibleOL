<?php

class Migration_Multiple_exam_attempts extends CI_Migration {

    public function __construct() {
        parent::__construct();

        $CI =& get_instance();
        $CI->language = 'english';
    }

    public function up() {

        echo "Add column 'maximum_attempts' to {$this->db->dbprefix}exam_active\n";
        $this->dbforge->add_column(
            'exam_active',
            [
                'maximum_attempts' => [
                    'type'    => 'INT',
                    'null'    => true,
                    'default' => null,
                    'comment' => 'Maximum number of attempts allowed; NULL means unlimited'
                ]
            ]
        );

        echo "Rename {$this->db->dbprefix}exam_status to {$this->db->dbprefix}exam_attempt\n";
        $this->db->query("
            RENAME TABLE {$this->db->dbprefix}exam_status
            TO {$this->db->dbprefix}exam_attempt
        ");

        echo "Add attempt_count and is_done to {$this->db->dbprefix}exam_attempt\n";
        $this->dbforge->add_column(
            'exam_attempt',
            [
                'attempt_count' => [
                    'type'    => 'INT',
                    'null'    => false,
                    'default' => 1,
                    'after'   => 'activeexamid'
                ],
                'is_done' => [
                    'type'    => 'BOOLEAN',
                    'null'    => false,
                    'default' => false
                ]
            ]
        );

        /*
         * ✅ Explicitly mark all legacy rows as attempt #1
         */
        echo "Initialize legacy exam_attempt rows with attempt_count = 1\n";
        $this->db->query("
            UPDATE {$this->db->dbprefix}exam_attempt
            SET attempt_count = 1
            WHERE attempt_count IS NULL
        ");

        echo "Drop old foreign key on {$this->db->dbprefix}exam_attempt\n";
        $this->db->query("
            ALTER TABLE {$this->db->dbprefix}exam_attempt
            DROP FOREIGN KEY bol_exam_status_ibfk_1
        ");

        echo "Add foreign keys to {$this->db->dbprefix}exam_attempt\n";

        $this->db->query("
            ALTER TABLE {$this->db->dbprefix}exam_attempt
            ADD CONSTRAINT exam_attempt_ibfk_1
            FOREIGN KEY (activeexamid)
            REFERENCES {$this->db->dbprefix}exam_active(id)
            ON UPDATE CASCADE ON DELETE RESTRICT
        ");

        $this->db->query("
            ALTER TABLE {$this->db->dbprefix}exam_attempt
            ADD CONSTRAINT exam_attempt_ibfk_2
            FOREIGN KEY (userid)
            REFERENCES {$this->db->dbprefix}user(id)
            ON UPDATE CASCADE ON DELETE CASCADE
        ");

        echo "Migrate {$this->db->dbprefix}exam_finished → exam_attempt.is_done\n";
        $this->db->query("
            UPDATE {$this->db->dbprefix}exam_attempt a
            JOIN {$this->db->dbprefix}exam_finished f
              ON a.userid = f.userid
             AND a.activeexamid = f.activeexamid
            SET a.is_done = TRUE
        ");

        echo "Drop {$this->db->dbprefix}exam_finished\n";
        $this->dbforge->drop_table('exam_finished', true);

        echo "Add unique constraint to {$this->db->dbprefix}exam_attempt\n";
        $this->db->query("
            ALTER TABLE {$this->db->dbprefix}exam_attempt
            ADD CONSTRAINT uc_user_exam_instance_attempt_count
            UNIQUE (userid, activeexamid, attempt_count)
        ");

        echo "Add attempt_id to {$this->db->dbprefix}exam_results\n";
        $this->dbforge->add_column(
            'exam_results',
            [
                'attempt_id' => [
                    'type'  => 'INT',
                    'null'  => true,
                    'after' => 'activeexamid'
                ]
            ]
        );

        echo "Populate {$this->db->dbprefix}exam_results.attempt_id\n";
        $this->db->query("
            UPDATE {$this->db->dbprefix}exam_results r
            JOIN {$this->db->dbprefix}exam_attempt a
              ON r.activeexamid = a.activeexamid
             AND r.userid = a.userid
            SET r.attempt_id = a.id
            WHERE r.attempt_id IS NULL
        ");

        echo "Make {$this->db->dbprefix}exam_results.attempt_id NOT NULL\n";
        $this->db->query("
            ALTER TABLE {$this->db->dbprefix}exam_results
            MODIFY COLUMN attempt_id INT NOT NULL
        ");

        echo "Add FK exam_results → exam_attempt\n";
        $this->db->query("
            ALTER TABLE {$this->db->dbprefix}exam_results
            ADD CONSTRAINT exam_results_ibfk_2
            FOREIGN KEY (attempt_id)
            REFERENCES {$this->db->dbprefix}exam_attempt(id)
        ");

        echo "Remove old FK and columns from {$this->db->dbprefix}exam_results\n";
        $this->db->query("
            ALTER TABLE {$this->db->dbprefix}exam_results
            DROP FOREIGN KEY exam_results_ibfk_1
        ");

        $this->dbforge->drop_column('exam_results', 'activeexamid');
        $this->dbforge->drop_column('exam_results', 'userid');
    }

    public function down()
    {
        echo "<pre>Downgrade not possible</pre>";
    }
}
