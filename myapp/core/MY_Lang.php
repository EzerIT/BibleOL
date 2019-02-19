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

    // Returns site-specific language table, or null for master site
    private function create_site_lang_table(string $idiom) {
        $CI =& get_instance();

        if (!empty($CI->config->item('url_variant'))) {
            // Not master site
            
            $site_lang_table = 'language_' . $idiom . '_' . $CI->config->item('url_variant');
            // Create database for organizational site if it does not exist
            if (!$CI->db->table_exists($site_lang_table)) {
                $CI->load->dbforge();
                $CI->dbforge->add_field(array('id' => array('type' => 'INT', 'auto_increment' => true),
                                              'textgroup' => array('type'=>'VARCHAR(25)'),
                                              'symbolic_name' => array('type'=>'TINYTEXT'),
                                              'text' => array('type'=>'TEXT')));
                $CI->dbforge->add_key('id', TRUE);
                $CI->dbforge->add_key('textgroup');
                $CI->dbforge->create_table($site_lang_table);
            }
            return $site_lang_table;
        }
        else 
            return null;
    }
    
    // Load from speicific language database without looking elsewhere. Merge into $lang
    private function load_from_db_specific(string $langfile, string $idiom, bool $org_site, array &$lang) {
        $CI =& get_instance();

        if ($org_site) {
            $site_lang_table = $this->create_site_lang_table($idiom); // Make sure table exists
            if (empty($site_lang_table))
                return; // We are running on master site

            $query = $CI->db->where('textgroup',$langfile)->get($site_lang_table);
        }
        else
            $query = $CI->db->where('textgroup',$langfile)->get('language_'.$idiom);
        
        $strings = $query->result();
        foreach ($strings as $s)
            $lang[$s->symbolic_name] = $s->text;
    }
    
    private function load_from_db($langfile, string $idiom, bool $return, bool $add_suffix /*ignored*/, string $alt_path /*ignored*/)
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

        $lang = array();

        // Read master site table
        $this->load_from_db_specific($langfile, $idiom, false, $lang);

        // Read organizational site table
        $this->load_from_db_specific($langfile, $idiom, true, $lang);


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

    public function load_secondary(string $langfile, string $idiom)
    {
        if ($idiom=='english' || $idiom=='none')
            $idiom = 'en';

        $lang = array();

        // Read English strings from master site
        $this->load_from_db_specific($langfile, 'en', false, $lang);
        
        // Read localized strings from master site
        if ($idiom!='en')
            $this->load_from_db_specific($langfile, $idiom, false, $lang);

        // Read English strings from organizational site
        $this->load_from_db_specific($langfile, 'en', true, $lang);
        
        // Read localized strings from organizational site
        if ($idiom!='en')
            $this->load_from_db_specific($langfile, $idiom, true, $lang);

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