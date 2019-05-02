<?php

// Creates a database table for a lexicon.
// Parameters:
//   $src_language    The source language (e.g. "Hebrew", "Aramaic", "greek")
//   $dst_lang        The abbreviated destination language (e.g. "en", "da")
//   $variant         The variant name. Null if creating a main
//   $keep            If the database table already exists, should we keep it?

function create_lexicon_table(string $src_language, string $dst_lang, string $variant=null, bool $keep=false) {
    $CI =& get_instance();
    
    $table_name = $variant ? "lexicon_{$src_language}_{$dst_lang}_{$variant}" : "lexicon_{$src_language}_{$dst_lang}";
    
    if ($keep && $CI->db->table_exists($table_name))
        return;
        
    $CI->load->dbforge();
    $CI->dbforge->drop_table($table_name,true);
 
    $CI->dbforge->add_field(array('id' => array('type' => 'INT', 'auto_increment' => true),
                                    'lex_id' => array('type'=>'INT',
                                                      'null' => true),
                                    'gloss' => array('type'=>'TEXT')
                                  ));
    $CI->dbforge->add_key('id', TRUE);
    $CI->dbforge->add_key('lex_id');
    $CI->dbforge->create_table($table_name);
        
    $CI->db->query("ALTER TABLE {PRE}{$table_name} ADD FOREIGN KEY (lex_id) REFERENCES {PRE}lexicon_{$src_language}(id) ON DELETE SET NULL ON UPDATE CASCADE");
  }
    
