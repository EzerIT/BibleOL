<?php

class MY_Lang extends CI_Lang {
	function __construct()
	{
        parent::__construct();
	}


	function load($langfile = '', $idiom = '', $return = FALSE, $add_suffix = TRUE, $alt_path = '')
	{
        if ($idiom == '') {
            $CI =& get_instance();
            if (isset($CI->language))
                $idiom = $CI->language;
        }

        return parent::load($langfile, $idiom, $return, $add_suffix, $alt_path);
    }

	function line($line = '')
    {
        $txt = parent::line($line);
        if ($txt === false)
            return "??$line??";
        else
            return $txt;
    }
  }