<?php

class Migration_Usermgmt extends CI_Migration {

    // Additions allowing users to sign up for an account, and for automatic account deletion

    public function __construct() {
        parent::__construct();

		$CI =& get_instance();
        $CI->language = 'english';
    }

	public function up() {
        $with_userid = array('sta_displayfeature','sta_question','sta_quiz','sta_quiztemplate',
                             'sta_requestfeature','sta_universe','userclass');
        $with_user_id = array('personal_font','userconfig');

        echo "Check for foreign key consistency...\n";
 
        foreach ($with_userid as $tab) {
            $query = $this->db->select('userid,user.id')->join($tab, 'user.id=userid', 'right')
                ->where('user.id IS NULL')->distinct()->get('user');
 
            if ($query->num_rows()>0) {
                $baduid=array();
                echo "    WARNING: Table {$this->db->dbprefix}$tab refers to these unknown user IDs: ";
                foreach ($query->result() as $row) {
                    echo $row->userid," ";
                    $baduid[] = $row->userid;
                }
                foreach ($baduid as $bu)
                    $this->db->where('userid', $bu)->delete($tab);
 
                echo "\n    They have been deleted\n";
            }
            else
                echo "    Table {$this->db->dbprefix}$tab is OK\n";
        }
 
        foreach ($with_user_id as $tab) {
            $query = $this->db->select('user_id,user.id')->join($tab, 'user.id=user_id', 'right')
                ->where('user.id IS NULL')->distinct()->get('user');
 
            if ($query->num_rows()>0) {
                $baduid=array();
                echo "    WARNING: Table {$this->db->dbprefix}$tab refers to these unknown user IDs: ";
                foreach ($query->result() as $row) {
                    echo $row->user_id," ";
                    $baduid[] = $row->user_id;
                }
                foreach ($baduid as $bu)
                    $this->db->where('user_id', $bu)->delete($tab);
 
                echo "\n    They have been deleted\n";
            }
            else
                echo "    Table {$this->db->dbprefix}$tab is OK\n";
        }
 
 
        // Set table engines to InnoDB (required to support foreign keys)

        $tables = $this->db->list_tables();
      
        foreach ($tables as $table) {
            echo $table, ": ";
            if ($this->db->query("ALTER TABLE $table ENGINE=InnoDB"))
                echo "Engine changed\n";
            else
                echo "Engine not changed\n";
        }


        echo "Updating user table stage 1...\n";
 
        $this->dbforge->add_column('user', array('oauth2_login' => array('type' => 'TINYTEXT',
                                                                         'null' => true)));
 
        $this->db->where('google_login',1)->update('user',array('oauth2_login' => 'google'));


        echo "Updating user table stage 2...\n";

        $this->dbforge->drop_column('user', 'may_see_wivu');
        $this->dbforge->drop_column('user', 'google_login');
 
        $this->dbforge->add_column('user', array('created_time' => array('type' => 'INT', 'default' => '0'),
                                                 'last_login' => array('type' => 'INT', 'default' => '0'),
                                                 'warning_sent' => array('type' => 'INT', 'default' => '0'),
                                                 // warning_sent:
                                                 // 0: No warning mail sent
                                                 // 1: First warning about deleting account sent
                                                 // 2: Second warning about deleting account sent
                                                 'isteacher' => array('type' => 'TINYINT(1)', 'default' => '0'),
                                                 'preflang' => array('type' => 'TINYTEXT')));
 
        $this->db->update('user',array('preflang' => 'none'));

        $this->db->where('isadmin',1)->update('user',array('isteacher' => 1));

        $query = $this->db->select('userid,max(start) maxstart')->group_by('userid')->get('sta_quiz');
 
        $default_last_login = time()-9*30*24*3600; // 9 months ago

        foreach ($query->result() as $row) {
            if ($row->maxstart >= $default_last_login)
                // User ran an exercise recently
                $this->db->where('id',$row->userid)->update('user',array('last_login' => $row->maxstart));
        }


        // Users who have not run an exercise are given a login time of 9 months ago
        $this->db->where('last_login',0)->update('user',array('last_login' => $default_last_login));

        echo "Updating class table...\n";
        $this->dbforge->add_column('class', array('ownerid' => array('type'=>'INT', 'default' => '0')));

        echo "Set foreign keys...\n";

        $this->db->swap_pre = 'Prefix_';

        foreach ($with_userid as $tab) {
            if (!$this->db->query("ALTER TABLE Prefix_$tab ADD FOREIGN KEY ui (userid) REFERENCES Prefix_user(id) ON DELETE CASCADE ON UPDATE CASCADE"))
                echo "    ERROR: Foregin key (userid) on $tab failed\n";
            else
                echo "    Foregin key (userid) on $tab is OK\n";
        }

        foreach ($with_user_id as $tab) {
            if (!$this->db->query("ALTER TABLE Prefix_$tab ADD FOREIGN KEY ui (user_id) REFERENCES Prefix_user(id) ON DELETE CASCADE ON UPDATE CASCADE"))
                echo "    ERROR: Foregin key (user_id) on $tab failed\n";
            else
                echo "    Foregin key (user_id) on $tab is OK\n";
        }


        // Foreign key on classid in userclass table
        if (!$this->db->query("ALTER TABLE Prefix_userclass ADD FOREIGN KEY ci (classid) REFERENCES Prefix_class(id) ON DELETE CASCADE ON UPDATE CASCADE"))
            echo "    ERROR: Foregin key (classid) on userclass failed\n";
        else
            echo "    Foregin key (classid) on userclass is OK\n";
        

        // NOTE: There is no foreign key on user_id in the font table. The reason is that user_id=0 in that
        // table is used to denote a global configuration. Therfore entries in the font table must be deleted
        // manually when a user is deleted.


        $this->dbforge->add_field(array('id' => array('type' => 'INT', 'auto_increment' => true),
										'pathname' => array('type'=>'TEXT'),
                                        'ownerid' => array('type'=>'INT')));
        // ownerid==0 means not owned by anybody and only administrator can manage.
        // ownerid is not a foreign key. If a user is deleted his/her exercises should have ownerid set to zero.
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('exerciseowner');

        // Add existing exercises to this table

        $this->load->model('mod_quizpath');

        $added = array();
        $deleted = array();
        $this->mod_quizpath->fix_exerciseowner($added, $deleted);

        if (count($added)>0) {
            echo "ADDED to exerciseowner table\n";
            foreach ($added as $a)
                echo "    ",$a,"\n";
        }

        if (count($deleted)>0) {
            echo "DELETED from exerciseowner table\n";
            foreach ($deleted as $a)
                echo "    ",$a,"\n";
        }

        echo "Table 'exerciseowner' created\n";
   }

	public function down()
	{
        echo "<pre>Downgrade not possible</pre>";
	}
}
