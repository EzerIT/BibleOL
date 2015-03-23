<?php

// Return a value for the $field. If a value has been posted (and rejected by validation), the
// posted value is returned, otherewise $default_val is returned.

function get_radio_setting(string $field, string $default_val)
{
    $OBJ =& _get_validation_object();

    if ($OBJ === false) {
        if (!isset($_POST[$field]))
            return $default_val;

        $field_val = $_POST[$field];
    }
    else
        $field_val = $OBJ->get_radio_setting($field, $default_val);

    assert('!is_array($field_val)');
    return $field_val;
}
