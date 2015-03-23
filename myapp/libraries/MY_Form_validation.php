<?php

class MY_Form_validation extends CI_Form_validation {

    // Return a value for the $field. If a value has been posted (and rejected by validation), the
    // posted value is returned, otherewise $default_val is returned.

    public function get_radio_setting(string $field, string $default_val) {
        if (!isset($this->_field_data[$field]) or !isset($this->_field_data[$field]['postdata']))
            return $default_val;

        return $this->_field_data[$field]['postdata'];
    }
}
