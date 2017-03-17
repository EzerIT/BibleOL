<?php



class Convert_wivu {
    static private $books = array(
        "Genesis"       => "Genesis",
        "Exodus"        => "Exodus",
        "Leviticus"     => "Leviticus",
        "Numbers"       => "Numeri",
        "Deuteronomy"   => "Deuteronomium",
        "Joshua"        => "Josua",
        "Judges"        => "Judices",
        "Ruth"          => "Ruth",
        "I_Samuel"      => "Samuel_I",
        "II_Samuel"     => "Samuel_II",
        "I_Kings"       => "Reges_I",
        "II_Kings"      => "Reges_II",
        "I_Chronicles"  => "Chronica_I",
        "II_Chronicles" => "Chronica_II",
        "Ezra"          => "Esra",
        "Nehemiah"      => "Nehemia",
        "Esther"        => "Esther",
        "Job"           => "Iob",
        "Psalms"        => "Psalmi",
        "Proverbs"      => "Proverbia",
        "Ecclesiastes"  => "Ecclesiastes",
        "Canticles"     => "Canticum",
        "Isaiah"        => "Jesaia",
        "Jeremiah"      => "Jeremia",
        "Lamentations"  => "Threni",
        "Ezekiel"       => "Ezechiel",
        "Daniel"        => "Daniel",
        "Hosea"         => "Hosea",
        "Joel"          => "Joel",
        "Amos"          => "Amos",
        "Obadiah"       => "Obadia",
        "Jonah"         => "Jona",
        "Micah"         => "Micha",
        "Nahum"         => "Nahum",
        "Habakkuk"      => "Habakuk",
        "Zephaniah"     => "Zephania",
        "Haggai"        => "Haggai",
        "Zechariah"     => "Sacharia",
        "Malachi"       => "Maleachi"
        );

    static private $featureNames = array(
        "word" => array(
            "visual" => "visual",
            "text_utf8" => "text_utf8",
            "text_plain_utf8" => "text_cons_utf8",
            "text_nocant_utf8" => "text_nocant_utf8",
            "text_translit" => "text_translit",
            "text_nopunct_translit" => "text_nopunct_translit",
            "language" => "language",
            "part_of_speech" => "sp",
            "phrase_dependent_part_of_speech" => "pdp",
            "vocalized_lexeme_utf8" => "vocalized_lexeme_utf8",
            "vocalized_lexeme_plain_utf8" => "vocalized_lexeme_cons_utf8",
            "vocalized_lexeme_translit" => "vocalized_lexeme_translit",
            "gloss" => "english",
            "frequency_rank" => "frequency_rank",
            "lexical_set" => "ls",
            // "noun_type" => Not supported
            // "pronoun_type" => Not supported
            "stem" => "vs",
            "tense" => "vt",
            "state" => "st",
            "person" => "ps",
            "gender" => "gn",
            "number" => "nu",
            "suffix_person" => "suffix_person",
            "suffix_gender" => "suffix_gender",
            "suffix_number" => "suffix_number",
            "graphical_root_formation_utf8" => "g_vbs_utf8",
            "graphical_root_formation_plain_utf8" => "g_vbs_cons_utf8",
            "graphical_preformative_utf8" => "g_pfm_utf8",
            "graphical_preformative_plain_utf8" => "g_pfm_cons_utf8",
            "graphical_verbal_ending_utf8" => "g_vbe_utf8",
            "graphical_verbal_ending_plain_utf8" => "g_vbe_cons_utf8",
            "graphical_nominal_ending_utf8" => "g_nme_utf8",
            "graphical_nominal_ending_plain_utf8" => "g_nme_cons_utf8",
            "graphical_pron_suffix_utf8" => "g_prs_utf8",
            "graphical_pron_suffix_plain_utf8" => "g_prs_cons_utf8",
            "graphical_pron_suffix_nopunct_translit" => "g_prs_translit",
            // "graphical_locative_utf8" =>  Not supported - subsumed in g_uvf_utf8
            // "graphical_locative_plain_utf8" => Not supported - subsumed in g_uvf_cons_utf8
            "graphical_pron_suffix_translit" => "g_prs_translit"
            // "locative" => Not supported - subsumed in uvf
            ),
        "subphrase" => array(
            "visual" => "visual",
            "subphrase_type" => "rela"
            // "subphrase_kind" => Not supported
            ),
        "phrase" => array(
            "visual" => "visual",
            "determination" => "det",
            "phrase_type" => "typ",
            "phrase_function" => "function"
            ),
        "clause" => array(
            "visual" => "visual",
            // "levels_of_embedding" => Not supported
            "text_type" => "txt",
            "clause_type" => "typ",
            "clause_constituent_relation" => "rela",
            "domain" => "domain"
            // "embedding_domain" => Not supported
            )
        );

    static private $valueNames = array(
        "part_of_speech_t" => array(
            "adjective" => "adjv",
            "adverb" => "advb",
            "article" => "art",
            "conjunction" => "conj",
            "interjection" => "intj",
            "interrogative" => "inrg",
            "negative" => "nega",
            "none" => "REMOVE_THIS",
            "noun" => "subs", // Or "nmpr"
            "preposition" => "prep",
            "pronoun" => "prps", // Or "prde" or "prin"
            "verb" => "verb"),
        "gender_t" => array(
            "none" => "NA",
            "unknown" => "unknown",
            "masculine" => "m",
            "feminine" => "f"
            ),
        "number_t" => array(
            "none" => "NA",
            "singular" => "sg",
            "dual" => "du",
            "plural" => "pl",
            "unknown" => "unknown"
            ),
        "person_t" => array(
            "none" => "NA",
            "first_person" => "p1",
            "second_person" => "p2",
            "third_person" => "p3",
            "unknown" => "unknown"
            ),
        "suffix_number_t" => array(
            "none" => "NA",
            "plural" => "pl",
            "singular" => "sg"
            ),
        "suffix_person_t" => array(
            "none" => "NA",
            "first_person" => "p1",
            "second_person" => "p2",
            "third_person" => "p3"
            // "unknown" => Not supported
            ),
        "suffix_gender_t" => array(
            "none" => "NA",
            "common" => "c",
            "masculine" => "m",
            "feminine" => "f"
            ),
        "state_t" => array(
            "none" => "NA",
            "absolute" => "a",
            "construct" => "c",
            "determined" => "e",
            "unknown" => "unknown"
            ),
        "verbal_tense_t" => array(
            "imperative" => "impv",
            "imperfect" => "impf",
            "infinitive_absolute" => "infa",
            "infinitive_construct" => "infc",
            "none" => "NA",
            "participle" => "ptca",
            "passive_participle" => "ptcp",
            "perfect" => "perf",
            "wayyiqtol" => "wayq",
            "weqatal" => "REMOVE_THIS",
            "weyiqtol" => "weyq"
            ),
        "verbal_stem_t" => array(
            "none" => "NA",
            "afel" => "afel",
            "etpaal" => "etpa",
            "etpeel" => "etpe",
            "hafel" => "haf",
            "hifil" => "hif",
            "hishtafal" => "hsht",
            "hitpaal" => "htpa",
            "hitpael" => "hit",
            "hitpeel" => "htpe",
            "hofal" => "hof",
            "hotpaal" => "hotp",
            "nifal" => "nif",
            "nitpael" => "nit",
            "pael" => "pael",
            "peal" => "peal",
            "peil" => "peil",
            "piel" => "piel",
            "pual" => "pual",
            "qal" => "qal",
            "shafel" => "shaf",
            "tifal" => "tif"
            ),
        "lexical_set_t" => array(
            "Absent_lexical_set" => "none",
            "Cardinal" => "card",
            "Distributive_noun" => "nmdi",
            "Gentilic" => "gntl",
            "Noun_of_existence" => "nmcp",
            "Noun_of_multitude" => "mult",
            "Ordinal" => "ordn",
            // "Peoples_name" => Not supported
            // "Persons_name" => Not supported
            "Potential_adverb" => "padv",
            "Potential_preposition" => "ppre",
            // "Topographical_name" => Not supported
            "Verb_of_existence" => "vbcp",
            "Verb_of_quotation" => "quot"
            ),
        "phrase_type_t" => array(
            "AdjP" => "AdjP",
            "AdvP" => "AdvP",
            "CP" => "CP",
            "DPrP" => "DPrP",
            "IPrP" => "IPrP",
            "InjP" => "InjP",
            "InrP" => "InrP",
            "NP" => "NP",
            "NegP" => "NegP",
            "PP" => "PP",
            "PPrP" => "PPrP",
            "PrNP" => "PrNP",
            "VP" => "VP"
            ),
        "phrase_function_t" => array(
            "Adju" => "Adju",
            "Cmpl" => "Cmpl",
            "Conj" => "Conj",
            "ExsS" => "ExsS",
            "Exst" => "Exst",
            "Frnt" => "Frnt",
            "IntS" => "IntS",
            "Intj" => "Intj",
            // "IrpC" => Not supported
            // "IrpO" => Not supported
            // "IrpP" => Not supported
            // "IrpS" => Not supported
            "Loca" => "Loca",
            "ModS" => "ModS",
            "Modi" => "Modi",
            // "NegS" => Not supported
            "Nega" => "Nega",
            "Objc" => "Objc",
            "PreC" => "PreC",
            "PreO" => "PreO",
            "PreS" => "PreS",
            "Pred" => "Pred",
            // "PtSp" => Not supported
            "PtcO" => "PtcO",
            "Ques" => "Ques",
            "Rela" => "Rela",
            "Subj" => "Subj",
            "Supp" => "Supp",
            "Time" => "Time",
            "Unkn" => "Unkn",
            "Voct" => "Voct",
            // "none" => Not supported
            ),
        "clause_type_t" => array(
            "AjCl" => "AjCl",
            "CPen" => "CPen",
            "Ellp" => "Ellp",
            // "Impv" => Not supported
            "InfA" => "InfA",
            "InfC" => "InfC",
            "MSyn" => "MSyn",
            "NmCl" => "NmCl",
            // "NullQtl" => Not supported
            // "NullYqt" => Not supported
            // "PtcA" => Not supported
            "PtcP" => "Ptcp",
            "Unkn" => "Unkn",
            "Voct" => "Voct",
            // "WQtl" => Not supported
            "WXQt" => "WXQt",
            "WXYq" => "WXYq",
            "Way0" => "Way0",
            "WayX" => "WayX",
            // "Wey0" => Not supported
            // "WeyX" => Not supported
            "WxQt" => "WxQt",
            "WxYq" => "WXYq",
            "XQtl" => "XQtl",
            "XYqt" => "XYqt"
            // "XxQt" => Not supported
            // "XxYq" => Not supported
            // "none" => Not supported
            // "xQtl" => Not supported
            // "xYqt" => Not supported
            ),
        "subphrase_relation_t" => array(
            "ADJ" => "adj",
            "ATR" => "atr",
            "DEM" => "dem",
            "MOD" => "mod",
            "PAR" => "par",
            "REG" => "rec",
            "adj" => "adj",
            "atr" => "atr",
            "dem" => "dem",
            "mod" => "mod",
            "par" => "par",
            "rec" => "rec"
            )




        );

    static private $typeinfo;

    static private function convert_selection(&$selection, string $filename) {
        if (isset($selection->mql)) {
            fwrite(STDERR, "$filename: Exercize contains MQL\n");
//            die(1);
        }
        else foreach ($selection->featHand->vhand as &$handler) {
            $name2 = self::$featureNames[$selection->object][$handler->name];
            if (empty($name2)) {
                fwrite(STDERR, "$filename: Unknown handler name translated from: $handler->name\n");
                die(2);
            }
            else
                $handler->name = $name2;

            switch ($handler->type) {
              case 'enumfeature':
                    foreach ($handler->values as $ix=>&$value) {
                        $type = self::$typeinfo->obj2feat->{$selection->object}->{$handler->name};
                        $v = self::$valueNames[$type][$value];
                        if ($v=="REMOVE_THIS") {
                            if (count($handler->values)==1) {
                                fwrite(STDERR, "$filename: Cannot remove single value: $value\n");
                                die;
                            }
                            fwrite(STDERR, "$filename: Removing: $value\n");
                            unset($handler->values[$ix]);
                        }
                        else
                            $value = $v;
                    }
                    break;
              case 'stringfeature':
              case 'rangeintegerfeature':
                    break; // No operation
              default:
                    fwrite(STDERR, "$filename: Unknown handler type: $handler->type\n");
                    die(3);
            }
        }
    }



    static public function convert($data, string $filename) {
        if ($data->database !== 'WIVU') {
            fwrite(STDERR, "$filename: Database is not WIVU\n");
            die(4);
        }
        $data->database = 'ETCBC4';

        switch ($data->properties) {
          case 'WIVU':   $data->properties = 'ETCBC4'; break;
          case 'WIVU-a': $data->properties = 'ETCBC4'; break;
          case 'WIVU-b': $data->properties = 'ETCBC4-translit'; break;
          default:
            fwrite(STDERR, "$filename: Unknown 'properties': $data->properties\n");
            die(5);
        }


        $typeinfo_json = @file_get_contents("db/ETCBC4.typeinfo.json") or die ("Failed opening file \"db/ETCBC4.typeinfo.json\":\nError was '$php_errormsg'\n");
        self::$typeinfo = json_decode($typeinfo_json);


        $newpaths = array();
        foreach ($data->selectedPaths as $p) {
            $split_p = explode(':',$p);
            $num = count($split_p);

            $path = self::$books[$split_p[0]];
            for ($i=1; $i<$num; ++$i)
                $path .= ':' . $split_p[$i];

            $newpaths[] = $path;
        }
        $data->selectedPaths = $newpaths;

        self::convert_selection($data->sentenceSelection, $filename);

        if (!$data->sentenceSelection->useForQo) {
            self::convert_selection($data->quizObjectSelection, $filename);
            $qoobj = $data->quizObjectSelection->object;
        }
        else
            $qoobj = $data->sentenceSelection->object;

        foreach ($data->quizFeatures->showFeatures as &$f)
            $f = self::$featureNames[$qoobj][$f];

        foreach ($data->quizFeatures->requestFeatures as &$f)
            $f->name = self::$featureNames[$qoobj][$f->name];

        foreach ($data->quizFeatures->dontShowFeatures as &$f)
            $f = self::$featureNames[$qoobj][$f];


        //print_r($data);

        return $data;
    }
  }

class Convert_etcbc4_v7 {
    static private $featureNames = array(
        "word" => array(
            "text" => "g_word",
            "text_nopunct_translit" => "g_word_nopunct_translit",
            "text_translit" => "g_word_translit",
            "text_utf8" => "g_word_utf8",
            "text_cons_utf8" => "g_word_cons_utf8",
            "text_nocant_utf8" => "g_word_nocant_utf8",

            "vocalized_lexeme" => "g_voc_lex",
            "vocalized_lexeme_cons_utf8" => "g_voc_lex_cons_utf8",
            "vocalized_lexeme_translit" => "g_voc_lex_translit",
            "vocalized_lexeme_utf8" => "g_voc_lex_utf8",

            "g_qere" => "qere",
            "g_qere_translit" => "qere_translit",
            "g_qere_utf8" => "qere_utf8",
            ),
        );

    static private $valueNames = array(
        "gender_t" => array(
            "c" => "REMOVE_THIS",
            ),

        "clause_constituent_relation_t" => array(
            "CoVo" => "ReVo",
            ),
        );

    static private $typeinfo;

    static private function convert_selection(&$selection, string $filename) {
        if (isset($selection->mql)) {
            fwrite(STDERR, "$filename: Exercize contains MQL\n");
//            die(1);
        }
        else foreach ($selection->featHand->vhand as &$handler) {
            if (isset(self::$featureNames[$selection->object][$handler->name]))
                $handler->name = self::$featureNames[$selection->object][$handler->name];

            switch ($handler->type) {
              case 'enumfeature':
                    foreach ($handler->values as $ix=>&$value) {
                        $type = self::$typeinfo->obj2feat->{$selection->object}->{$handler->name};
                        if (isset(self::$valueNames[$type][$value])) {
                            $v = self::$valueNames[$type][$value];
                            if ($v=="REMOVE_THIS") {
                                if (count($handler->values)==1) {
                                    fwrite(STDERR, "$filename: Cannot remove single value: $value\n");
                                    die;
                                }
                                fwrite(STDERR, "$filename: Removing: $value\n");
                                unset($handler->values[$ix]);
                            }
                            else
                                $value = $v;
                        }
                    }
                    break;
              case 'stringfeature':
              case 'rangeintegerfeature':
              case 'enumlistfeature':
                    break; // No operation
              default:
                    fwrite(STDERR, "$filename: Unknown handler type: $handler->type\n");
                    die(3);
            }
        }
    }

    static public function convert($data, string $filename) {
        if ($data->database !== 'ETCBC4') {
            fwrite(STDERR, "$filename: Database is not ETCBC4\n");
            die(4);
        }

        $typeinfo_json = @file_get_contents("db/ETCBC4.typeinfo.json") or die ("Failed opening file \"db/ETCBC4.typeinfo.json\":\nError was '$php_errormsg'\n");
        self::$typeinfo = json_decode($typeinfo_json);


        self::convert_selection($data->sentenceSelection, $filename);

        if (!$data->sentenceSelection->useForQo) {
            self::convert_selection($data->quizObjectSelection, $filename);
            $qoobj = $data->quizObjectSelection->object;
        }
        else
            $qoobj = $data->sentenceSelection->object;

        foreach ($data->quizFeatures->showFeatures as &$f)
            if (isset(self::$featureNames[$qoobj][$f]))
                $f = self::$featureNames[$qoobj][$f];

        foreach ($data->quizFeatures->requestFeatures as &$f)
            if (isset(self::$featureNames[$qoobj][$f->name]))
                $f->name = self::$featureNames[$qoobj][$f->name];

        foreach ($data->quizFeatures->dontShowFeatures as &$f)
            if (isset(self::$featureNames[$qoobj][$f]))
                $f = self::$featureNames[$qoobj][$f];


        return $data;
    }
  }
