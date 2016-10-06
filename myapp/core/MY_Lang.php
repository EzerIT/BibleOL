<?php

class MY_Lang extends CI_Lang {
    private $secondary_lang = array(); // For handling multiple languages

	public function __construct()
	{
        parent::__construct();
	}

    private function load_from_db(array__OR__string $langfile, string $idiom, boolean $return, boolean $add_suffix /*ignored*/, string $alt_path /*ignored*/)
    {
        if ($idiom==='english')
            $idiom = 'en';
        
		if (is_array($langfile)) {
            assert(!$return);
            
			foreach ($langfile as $value) {
				$this->load_from_db($value, $idiom, $return, $add_suffix, $alt_path);
			}
			return;
		}

        $CI =& get_instance();
        $query = $CI->db->where('filename',$langfile)->get('language_'.$idiom);
        $strings = $query->result();

        $lang = array();
        foreach ($strings as $s)
            $lang[$s->key] = $s->text;
            
		if ($return)
			return $lang;

		$this->is_loaded[$langfile] = $idiom;
		$this->language = array_merge($this->language, $lang);
    }

	public function load($langfile, $idiom = '', $return = FALSE, $add_suffix = TRUE, $alt_path = '')
	{
		if ($idiom == '') {  // This may happen if load() is called from CodeIgniter
			$CI =& get_instance();
			if (isset($CI->language))
				$idiom = $CI->language;
		}

        if ($return) {
            if ($idiom!='english')
                $l1 = $this->load_from_db($langfile, 'english', true, $add_suffix, $alt_path); // For fallback strings
            else
                $l1 = array();

            $l2 = $this->load_from_db($langfile, $idiom, $return, $add_suffix, $alt_path);
            return array_merge($l1,$l2);
        }
        else {
            if ($idiom!='english')
                $this->load_from_db($langfile, 'english', false, $add_suffix, $alt_path); // For fallback strings

            $this->load_from_db($langfile, $idiom, false, $add_suffix, $alt_path);
        }
    }

	public function line($line, $log_errors = TRUE)
    {
        $txt = parent::line($line, $log_errors);
        if ($txt === false)
            return "??$line??";
        else
            return $txt;
    }

    public function load_secondary($langfile, $idiom)
    {
        if ($idiom=='english' || $idiom=='none')
            $idiom = 'en';

        $CI =& get_instance();
        
        $lang = array();

        if ($idiom!='en') {
            $query = $CI->db->where('filename',$langfile)->get('language_en');  // For fallback strings
            $strings = $query->result();
            
            foreach ($strings as $s)
                $lang[$s->key] = $s->text;
        }

        $query = $CI->db->where('filename',$langfile)->get('language_'.$idiom);
        $strings = $query->result();

        $lang = array();
        foreach ($strings as $s)
            $lang[$s->key] = $s->text;
            
        $this->secondary_lang = array_merge($this->secondary_lang, $lang);
    }

    public function clear_secondary()
    {
        $this->secondary_lang = array();
    }

    public function line_secondary($line)
    {
        if (!isset($this->secondary_lang[$line]))
            return "??$line??";
        else
            return $this->secondary_lang[$line];
    }

  }