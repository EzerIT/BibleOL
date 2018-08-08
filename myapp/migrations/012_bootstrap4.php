<?php

class Migration_Bootstrap4 extends CI_Migration {
    // Change name of icons in bol_heb_urls

    public function __construct() {
        parent::__construct();
        
		$CI =& get_instance();
        $CI->language = 'english';
    }

	public function up() {
        $this->db->where('icon','glyphicon-link')       ->update('heb_urls',array('icon' => 'l-icon-link'));
		$this->db->where('icon','glyphicon-file')		->update('heb_urls',array('icon' => 'l-icon-file'));
		$this->db->where('icon','glyphicon-music')		->update('heb_urls',array('icon' => 'l-icon-music'));
		$this->db->where('icon','glyphicon-picture')	->update('heb_urls',array('icon' => 'l-icon-picture'));
		$this->db->where('icon','glyphicon-film')		->update('heb_urls',array('icon' => 'l-icon-film'));
		$this->db->where('icon','glyphicon-volume-down')->update('heb_urls',array('icon' => 'l-icon-speaker'));
		$this->db->where('icon','glyphicon-book')		->update('heb_urls',array('icon' => 'l-icon-book'));
		$this->db->where('icon','glyphicon-globe')		->update('heb_urls',array('icon' => 'l-icon-globe'));
		$this->db->where('icon','bolicon-logos')		->update('heb_urls',array('icon' => 'l-icon-logos'));
    }
    
	public function down()
	{
        echo "<pre>Downgrade not possible</pre>";
	}
}
