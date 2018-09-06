<?php

class Language {

    // The key is the abbreviation of a destination language
    // The value is the Bible OL internal symbol for that language
    public static $dst_lang_abbrev = array(
        'en' => 'english',
        'da' => 'danish',
        'de' => 'german',
        'fr' => 'french',
        'nl' => 'dutch',
        'pt' => 'portuguese',
        'es' => 'spanish',
        'sw' => 'swahili',
        'zh-simp' => 'simp_chinese',
        'zh-trad' => 'trad_chinese',
    );

    // NOTE: When new lexicon languages are added to $dst_lang_abbrev, they must also be added
    // to styles/ol_zh.less (under .showit .wordgrammar) if they use Latin script.


    // The key is the abbreviation of a source language
    // The value is the Bible OL internal symbol for that language
    public static $src_lang_abbrev = array(
        'heb' => 'hebrew',
        'aram' => 'aramaic',
        'greek' => 'greek',
    );
  }