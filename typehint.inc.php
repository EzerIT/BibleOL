<?php

// Extends type hinting to boolean, integer, float, string, and resource.
// Idea base on code from Daniel dot L dot Wood at Gmail dot Com (see http://php.net/manual/en/language.oop5.typehinting.php)
// and bantam at banime dot com


// This is a rewrite of the _error_handler function found in CodeIgniter. By defining it before
// CodeIgniter does, it overwrites CodeIgniter's version.


//Typehint::initializeHandler();

function _error_handler($severity, $message, $filepath, $line)
{
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

    $is_error = (((E_ERROR | E_COMPILE_ERROR | E_CORE_ERROR | E_USER_ERROR) & $severity) === $severity);

    // When an error occurred, set the status header to '500 Internal Server Error'
    // to indicate to the client something went wrong.
    // This can't be done within the $_error->show_php_error method because
    // it is only called when the display_errors flag is set (which isn't usually
    // the case in a production environment) or when errors are ignored because
    // they are above the error_reporting threshold.
    if ($is_error)
		{
			set_status_header(500);
		}

    // Should we ignore the error? We'll get the current error_reporting
    // level and add its bits with the severity bits to find out.
    if (($severity & error_reporting()) !== $severity)
		{
			return;
		}

    $_error =& load_class('Exceptions', 'core');
    $_error->log_exception($severity, $message, $filepath, $line);

    // Should we display the error?
    if (str_ireplace(array('off', 'none', 'no', 'false', 'null'), '', ini_get('display_errors')))
		{
			$_error->show_php_error($severity, $message, $filepath, $line);
		}

    // If the error is fatal, the execution of the script should be stopped because
    // errors can't be recovered from. Halting the script conforms with PHP's
    // default error handling. See http://www.php.net/manual/en/errorfunc.constants.php
    if ($is_error)
		{
			exit(1); // EXIT_ERROR
		}
}
