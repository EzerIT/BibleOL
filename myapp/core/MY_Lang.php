<?php

class MY_Lang extends CI_Lang {
    private $secondary_lang = array(); // For handling multiple languages

	public function __construct()
	{
        parent::__construct();
	}


	public function load($langfile = '', $idiom = '', $return = FALSE, $add_suffix = TRUE, $alt_path = '')
	{
        if ($idiom == '') {
            $CI =& get_instance();
            if (isset($CI->language))
                $idiom = $CI->language;
        }

        return parent::load($langfile, $idiom, $return, $add_suffix, $alt_path);
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
        if ($idiom=='en' || $idiom=='none')
            $idiom = 'english';

        include(APPPATH . "/language/$idiom/{$langfile}_lang.php");
        $this->secondary_lang = array_merge($this->secondary_lang, $lang);
        unset($lang);
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