<?php

class Language {

    // The key is the abbreviation of a destination language
    // The value is the Bible OL internal symbol for that language
    public static $dst_lang_abbrev = array(
        'en' => 'english',
        'da' => 'danish',
        'de' => 'german',
        'nl' => 'dutch',
        'pt' => 'portuguese',
        'es' => 'spanish',
        'zh-simp' => 'simp_chinese',
        'zh-trad' => 'trad_chinese',
    );

    // The key is the abbreviation of a source language
    // The value is the Bible OL internal symbol for that language
    public static $src_lang_abbrev = array(
        'heb' => 'hebrew',
        'aram' => 'aramaic',
        'greek' => 'greek',
    );
  }