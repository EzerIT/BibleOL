<?php

// Extends type hinting to boolean, integer, float, string, and resource.
// Idea base on code from Daniel dot L dot Wood at Gmail dot Com (see http://php.net/manual/en/language.oop5.typehinting.php)
// and bantam at banime dot com


// This is a rewrite of the _exception_handler function found in CodeIgniter. By defining it before
// CodeIgniter does, it overwrites CodeIgniter's version.


//Typehint::initializeHandler();

function _exception_handler($severity, $message, $filepath, $line) {

    // Catch typehint errors and check if they are really errors
    if ($severity == E_RECOVERABLE_ERROR) { 
        if (preg_match('/^Argument (\d)+ passed to (?:(\w+)::)?(\w+)\(\) must be an instance of (\w+), (instance of )?(\w+) given/', $message, $match)) {
            // $match[4] is the required type
            // $match[6] is the provided type

            if ($match[4] == $match[6]) 
                return; // This was not an error

            // Multiple type choices: $match[4] is xxx__OR__yyy__OR__zzz where xxx, yyy and zzz are the allowed types
            
            $allowed_types = explode('__OR__', $match[4]);
            foreach ($allowed_types as $at)
                if ($at == $match[6])
                    return; // This was not an error
        }
    } 

    // The rest of this code is identical to the one found in CodeIgniter


    // We don't bother with "strict" notices since they tend to fill up
    // the log file with excess information that isn't normally very helpful.
    // For example, if you are running PHP 5 and you use version 4 style
    // class functions (without prefixes like "public", "private", etc.)
    // you'll get notices telling you that these have been deprecated.
    if ($severity == E_STRICT)
		{
			return;
		}

    $_error =& load_class('Exceptions', 'core');

    // Should we display the error? We'll get the current error_reporting
    // level and add its bits with the severity bits to find out.
    if (($severity & error_reporting()) == $severity)
		{
			$_error->show_php_error($severity, $message, $filepath, $line);
		}

    // Should we log the error?  No?  We're done...
    if (config_item('log_threshold') == 0)
		{
			return;
		}

    $_error->log_exception($severity, $message, $filepath, $line);
}
