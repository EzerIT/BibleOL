<?php

// Tests if a variable is set to 'on'
function is_set_on(&$v) {
    return isset($v) && $v=='on';
}

// Returns $v if set, otherwise returns $default
function set_or_default(&$v, $default) {
    return isset($v) ? $v : $default;
}

function composedir($dir, $path) {
    return empty($dir) ? $path : "$dir/$path";
}