<?php

function src_lang_short2long(string $srclang) {
    switch ($srclang) {
      case 'heb':   return 'Hebrew';
      case 'aram':  return 'Aramaic';
      case 'greek': return 'greek';
    }
    throw new DataException($this->lang->line('illegal_lang_code'));
  }
    

function get_available_translations() {
    return get_instance()->db->get('translation_languages')->result();
}

function get_if_translations() {
    return get_instance()->db->where('iface_enabled',true)->get('translation_languages')->result();
}

function get_heblex_translations() {
    return get_instance()->db->where('heblex_enabled',true)->get('translation_languages')->result();
}

function get_greeklex_translations() {
    return get_instance()->db->where('greeklex_enabled',true)->get('translation_languages')->result();
}

// Return an array where the langauge code is the index into the array
function make_code_index(array $langs) {
    $result = array();
    foreach ($langs as $l)
        $result[$l->abb] = $l;
    return $result;
}