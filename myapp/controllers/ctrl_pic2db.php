<?php
  // This is intended to use by the CLI only.


class Ctrl_pic2db extends CI_Controller {
    private $tmpfname; 
    
    private $book_number_from_picdb = array(
        'Genesis' => 1,
        'Exodus' => 2,
        'Leviticus' => 3,
        'Numeri' => 4,
        'Deuteronomium' => 5,
        'Josua' => 6,
        'Judices' => 7,
        'Ruth' => 8,
        'Samuel_I' => 9,
        'Samuel_II' => 10,
        'Reges_I' => 11,
        'Reges_II' => 12,
        'Chronica_I' => 13,
        'Chronica_II' => 14,
        'Esra' => 15,
        'Nehemia' => 16,
        'Esther' => 17,
        'Iob' => 18,
        'Psalmi' => 19,
        'Proverbia' => 20,
        'Ecclesiastes' => 21,
        'Canticum' => 22,
        'Jesaia' => 23,
        'Jeremia' => 24,
        'Threni' => 25,
        'Ezechiel' => 26,
        'Daniel' => 27,
        'Hosea' => 28,
        'Joel' => 29,
        'Amos' => 30,
        'Obadia' => 31,
        'Jona' => 32,
        'Micha' => 33,
        'Nahum' => 34,
        'Habakuk' => 35,
        'Zephania' => 36,
        'Haggai' => 37,
        'Sacharia' => 38,
        'Maleachi' => 39,
        );


    public function __construct() {
        parent::__construct();

        if (!$this->input->is_cli_request()) {
            echo '<pre>This command can only be run from the command line</pre>';
            die;
        }
    }

    public function __destruct() {
        @unlink($this->tmpfname.'a');
        @unlink($this->tmpfname.'b');
    }        

	public function index() {
        $this->tmpfname = tempnam(sys_get_temp_dir(), 'resource');

        $ch = curl_init("http://resources.3bmoodle.dk/jsonrefs.php");
		$fp = fopen($this->tmpfname.'a', "w");

		curl_setopt($ch, CURLOPT_FILE, $fp);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		 
		curl_exec($ch);
		curl_close($ch);
		fclose($fp);

        $this->load->helper('file');
        $data = read_file($this->tmpfname.'a');
        $biblerefs = json_decode($data);

        $ch = curl_init("http://resources.3bmoodle.dk/jsonurls.php");
		$fp = fopen($this->tmpfname.'b', "w");

		curl_setopt($ch, CURLOPT_FILE, $fp);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		 
		curl_exec($ch);
		curl_close($ch);
		fclose($fp);

        $this->load->helper('file');
        $data = read_file($this->tmpfname.'b');
        $bibleurls = json_decode($data);

		$this->load->database();
		$this->load->dbforge();
 
		$this->dbforge->drop_table('bible_refs');
 
		$this->dbforge->add_field(array('id' => array('type' => 'INT', 'auto_increment' => true),
										'book' => array('type'=>'VARCHAR', 'constraint'=>32),
                                        'booknumber' => array('type'=>'INT'),
										'chapter' => array('type'=>'INT'),
										'verse' => array('type'=>'INT'),
										'picture' => array('type'=>'INT')));
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('bible_refs');

		$this->dbforge->drop_table('bible_urls');
 
		$this->dbforge->add_field(array('id' => array('type' => 'INT', 'auto_increment' => true),
										'book' => array('type'=>'VARCHAR', 'constraint'=>32),
                                        'booknumber' => array('type'=>'INT'),
										'chapter' => array('type'=>'INT'),
										'verse' => array('type'=>'INT'),
										'url' => array('type'=>'TINYTEXT'),
                                        'type' => array('type'=>'CHAR')));
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('bible_urls');

        $batch = array();
        foreach ($biblerefs as $src => $pic_arr) {
            list($book,$chapter,$verse) = explode(':',$src);
            $pic_arr_unique = array_unique($pic_arr);
            foreach ($pic_arr_unique as $picture)
                $batch[] = array('book' => $book,
                                 'booknumber' => $this->book_number_from_picdb[$book],
                                 'chapter' => $chapter,
                                 'verse' => $verse,
                                 'picture' => $picture);
        }
        if (count($batch)>0)
            $this->db->insert_batch('bible_refs',$batch);

        $batch = array();
        foreach ($bibleurls as $src => $url_arr) {
            list($book,$chapter,$verse) = explode(':',$src);
            foreach ($url_arr as $urltype)
                $batch[] = array('book' => $book,
                                 'booknumber' => $this->book_number_from_picdb[$book],
                                 'chapter' => $chapter,
                                 'verse' => $verse,
                                 'url' => $urltype[0],
                                 'type' => $urltype[1]);
        }
        if (count($batch)>0)
            $this->db->insert_batch('bible_urls',$batch);
        echo "Done\n";
    }


  }