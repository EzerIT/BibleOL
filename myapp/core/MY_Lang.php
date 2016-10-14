<?php

class MY_Lang extends CI_Lang {
    private $secondary_lang = array(); // For handling multiple languages

	public function __construct()
	{
        parent::__construct();
	}

    // This is a copy of the load function from CI_Lang. It is included here unchanged except the
    // that it does not print an error if the language file is not found.
	private function parent_load($langfile, $idiom = '', $return = FALSE, $add_suffix = TRUE, $alt_path = '')
	{
		if (is_array($langfile))
		{
			foreach ($langfile as $value)
			{
				$this->load($value, $idiom, $return, $add_suffix, $alt_path);
			}

			return;
		}

		$langfile = str_replace('.php', '', $langfile);

		if ($add_suffix === TRUE)
		{
			$langfile = preg_replace('/_lang$/', '', $langfile).'_lang';
		}

		$langfile .= '.php';

		if (empty($idiom) OR ! preg_match('/^[a-z_-]+$/i', $idiom))
		{
			$config =& get_config();
			$idiom = empty($config['language']) ? 'english' : $config['language'];
		}

		if ($return === FALSE && isset($this->is_loaded[$langfile]) && $this->is_loaded[$langfile] === $idiom)
		{
			return;
		}

		// Load the base file, so any others found can override it
		$basepath = BASEPATH.'language/'.$idiom.'/'.$langfile;
		if (($found = file_exists($basepath)) === TRUE)
		{
			include($basepath);
		}

		// Do we have an alternative path to look in?
		if ($alt_path !== '')
		{
			$alt_path .= 'language/'.$idiom.'/'.$langfile;
			if (file_exists($alt_path))
			{
				include($alt_path);
				$found = TRUE;
			}
		}
		else
		{
			foreach (get_instance()->load->get_package_paths(TRUE) as $package_path)
			{
				$package_path .= 'language/'.$idiom.'/'.$langfile;
				if ($basepath !== $package_path && file_exists($package_path))
				{
					include($package_path);
					$found = TRUE;
					break;
				}
			}
		}

        // The following lines have been removed with respect to the original
		// if ($found !== TRUE)
		// {
		//     show_error('Unable to load the requested language file: language/'.$idiom.'/'.$langfile);
		// }

		if ( ! isset($lang) OR ! is_array($lang))
		{
			log_message('error', 'Language file contains no data: language/'.$idiom.'/'.$langfile);

			if ($return === TRUE)
			{
				return array();
			}
			return;
		}

		if ($return === TRUE)
		{
			return $lang;
		}

		$this->is_loaded[$langfile] = $idiom;
		$this->language = array_merge($this->language, $lang);

		log_message('info', 'Language file loaded: language/'.$idiom.'/'.$langfile);
		return TRUE;
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

        if (empty($lang))
            // Load from file instead of database
            $lang = $this->parent_load($langfile, $idiom=='en' ? 'english' : $idiom, true, $add_suffix, $alt_path);

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