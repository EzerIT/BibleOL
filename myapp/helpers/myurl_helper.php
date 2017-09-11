<?php

// Build a complete GET request
function build_get(string $webpage, array $items) {
    $encoded_items = http_build_query($items, '', '&', PHP_QUERY_RFC3986);
    
    if (empty($encoded_items))
        return $webpage;
    
    return $webpage . '?' . $encoded_items;
}

//// Retrieve from $_GET
//function get_get(string $index, $default=null) {
//    if (is_int($default)) {
//        if (isset($_GET[$index]))
//            return (int)rawurldecode($_GET[$index]);
//        else
//            return $default;
//    }
//    else {
//        if (isset($_GET[$index]))
//            return rawurldecode($_GET[$index]);
//        else
//            return $default;
//    }
//}
