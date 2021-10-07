/**
 * $Id: vk.inc.js 753 2011-05-05 20:35:49Z wingedfox $
 *
 * Keyboard layout parset implementation
 *
 * This software is protected by patent No.2009611147 issued on 20.02.2009 by Russian Federal Service for Intellectual Property Patents and Trademarks.
 *
 * @author Ilya Lebedev
 * @copyright 2006-2011 Ilya Lebedev <ilya@lebedev.net>
 * @version $Rev: 753 $
 * @lastchange $Author: wingedfox $ $Date: 2011-05-06 00:35:49 +0400 (Fri, 06 May 2011) $
 */
var VirtualKeyboardLayout = function ($fname) {
    var self = this;

    var $SERIALIZE_USE_DOMAIN = "domain";
    var $SERIALIZE_USE_CODE   = "code";

    var $controlCodes = [0x00AD,0x0600,0x0601,0x0602,0x0603,0x06DD,0x070F,0x17B4,0x17B5,0x200B
                        ,0x200C,0x200D,0x200E,0x200F,0x202A,0x202B,0x202C,0x202D,0x202E,0x2060
                        ,0x2061,0x2062,0x2063,0x206A,0x206B,0x206C,0x206D,0x206E,0x206F];

    /**
     *  List of the chars to be not skipped in the layout
     */
    var $problemChars = ['021e','021f' // from Lakhota Standard, windows does not uppercase them properly
                        ];

    /**
     *  Keys mapping as used in the US keyboard layout
     *  it might follow in the different order to the mapping described in the KLC file
     */
    var $keymap = {
        '29' /* 'OEM_3'      */ : []                 
       ,'02' /* 1            */ : []
       ,'03' /* 2            */ : []
       ,'04' /* 3            */ : []
       ,'05' /* 4            */ : []
       ,'06' /* 5            */ : []
       ,'07' /* 6            */ : []
       ,'08' /* 7            */ : []
       ,'09' /* 8            */ : []
       ,'0a' /* 9            */ : []
       ,'0b' /* 0            */ : []
       ,'0c' /* 'OEM_MINUS'  */ : []
       ,'0d' /* 'OEM_PLUS'   */ : []
       ,'2b' /* 'OEM_5'      */ : []
       ,'10' /* 'Q'          */ : []
       ,'11' /* 'W'          */ : []
       ,'12' /* 'E'          */ : []
       ,'13' /* 'R'          */ : []
       ,'14' /* 'T'          */ : []
       ,'15' /* 'Y'          */ : []
       ,'16' /* 'U'          */ : []
       ,'17' /* 'I'          */ : []
       ,'18' /* 'O'          */ : []
       ,'19' /* 'P'          */ : []
       ,'1a' /* 'OEM_4'      */ : []
       ,'1b' /* 'OEM_6'      */ : []
       ,'1e' /* 'A'          */ : []
       ,'1f' /* 'S'          */ : []
       ,'20' /* 'D'          */ : []
       ,'21' /* 'F'          */ : []
       ,'22' /* 'G'          */ : []
       ,'23' /* 'H'          */ : []
       ,'24' /* 'J'          */ : []
       ,'25' /* 'K'          */ : []
       ,'26' /* 'L'          */ : []
       ,'27' /* 'OEM_1'      */ : []
       ,'28' /* 'OEM_7'      */ : []
       ,'2c' /* 'Z'          */ : []
       ,'2d' /* 'X'          */ : []
       ,'2e' /* 'C'          */ : []
       ,'2f' /* 'V'          */ : []
       ,'30' /* 'B'          */ : []
       ,'31' /* 'N'          */ : []
       ,'32' /* 'M'          */ : []
       ,'33' /* 'OEM_COMMA'  */ : []
       ,'34' /* 'OEM_PERIOD' */ : []
       ,'35' /* 'OEM_2'      */ : []
    };
    /**
     *  Maps column number to the mnemonic
     */
    var $colmap = ['normal'
                  ,'shift'
                  ,'ctrl'
                  ,'shift_ctrl'
                  ,null
                  ,null
                  ,'alt'
                  ,'shift_alt'
                  ,'caps'
                  ,'shift_caps'
        ];
    /**
     *  Layout name
     */
    var $name;
    /**
     *  Country code (might be not a _code_ but any string)
     */
    var $code;
    /**
     *  Language domain, as specified in the ISO-639* 
     */
    var $domain;
    /**
     *  Just a copyright string
     */
    var $copyright;
    /**
     *  Developer company
     */
    var $company;
    /**
     *  Enabled columns, regarding to the KLC enablement info
     */
    var $columns = [];
    /**
     *  List of the ligatures in the format
     *  '<key_name>' => array ('<column>' => array('<symbol1>','<symbol4>')));
     *  where <key_name> is the same as key in the {@link #keymap}
     *        <column>   column number
     *        <symbolN>  up to 4 consecutive symbols in the ligature
     */
    var $ligature = [];
    /**
     *  Deadkeys
     */
    var $deadkey = {};

    var $layoutText;

    var $root = "";
    var $addon = "/addons/";
    var $callback = "callbacks/";

    /**
     * Converts char code to UTF codepoing, if required
     *
     * @param $chr char or 4-byte hex
     * @return string UTF codepoint
     */
    var __chr2utf = function ($chr) {

        if ($chr.match(/^[\da-f]{4}$/i)) {
            return String.fromCharCode(parseInt($chr, 16));
        } else {
            return $chr;
        }
    }
    /**
     *  Callback for converting char to its code
     *
     * @param $chr char
     */
    var __char2code = function ($chr) {
        return $chr.charCodeAt(0);
    }
    /**
     *  Serializes the layout row
     *
     *  @param $data row data
     *  @param $token row id
     *  @return serialized row
     */
    var __serializeRow = function ($data, $token) {
        if ($colmap[0] == $token) {
            return $token + ":[" + addcslashes($data.map(function(a){if (!a.match(/^\[.+\]$/)) return a.charCodeAt(0); else return a}).join(',')/*.$controlCodes*/) + "]";
        } else if ($data && in_array($token, $colmap)) {
            var $str = $token + ":{";
            for (var $k=0, $dL=$data.length; $k<$dL; $k++) {
                if ($data[$k]) {
                    $data[$k] = $k + ":[" + addcslashes($data[$k].map(function(a){if (!a.match(/^\[.+\]$/)) return a.charCodeAt(0); else return a}).join(',')/*.$controlCodes*/) + "]";
                }
            }
            $str += $data.filter().join(",") + "}";
            return $data.length?$str:"";
        }
        return null;
    }
    /**
     *
     *
     *
     */
    var __serializeDeadkeys = function () {
        var $dk = []
           ,$hasDk = false
        for (var $k in $deadkey) {
            if (!$deadkey.hasOwnProperty($k)) continue;
            $hasDk = true;

            var $charr = []
               ,$v     = $deadkey[$k];

            for (var $s in $v) {
                $charr.push($s);
                $charr.push($v[$s]);
            }                                                                	
            $dk.push($k.charCodeAt(0) + ":[" + $charr.map(function(a){return a.charCodeAt(0)}).join(',') + "]");
        }
        return $hasDk?"dk:{" + $dk.join(",") + "}":"";
    }

    /**
     *  Retrieves the DEADKEY xxxx block from the layout description, parses it and appends to {@link #deadkey}
     *
     *  @param $sym symbol to look deadkeys for
     */
    var parseDeadkey = function ($sym) {
        var $char = __chr2utf($sym);

        if ($sym.length==1) {
            $sym = $sym.charCodeAt(0).hex();
        }

        if (!$deadkey[$char]) {
            // DEADKEY xxx block
            var $m = $layoutText.match(new RegExp("^DEADKEY\\s+" + $sym + "((?:.|[\\r\\n])+?)(?:^KEYNAME|^DEADKEY)","m"));
            if ($m && $m[1]) {
                var $keys = $m[1].split(/\s*[\r\n]+\s*/m)
                   ,$deadkeys = {}
                   ,store = false;
                for (var $i=0, $kL=$keys.length; $i<$kL; $i++) {
                    var $key = $keys[$i].trim();
                    if ($key) {
                        $key = $key.split(/\t+/);
                        $deadkeys[__chr2utf($key[0])] = __chr2utf($key[1]);
                        $store = true;
                    }
                }
                if ($store) {
                    $deadkey[$char] = $deadkeys;
                }
            }
        }
    }
    /**
     *  Retrieves the LIGATURE block and parses it if exists
     *
     *  @param $sym symbol to look deadkeys for
     */
    var parseLigature = function () {
        if (!$ligature.length) {
            // LIGATURE block
            var $m = $layoutText.match(/^LIGATURE(?:.|[\r\n])+?--[\r\n]+((?:.|[\r\n])+?)(?:^KEYNAME|^DEADKEY)/m);
            if ($m && $m[1]) {
                var $ligatures = $m[1].split(/[\r\n]+/);
                for (var i=0, lL=$ligatures.length; i<lL; i++) {
                    var $lig = $ligatures[i].match(/^([A-Z\d_]+)\t\t(\d+)\t+((?:[^\t]+\t)+)/m)
                       ,$key = $lig[1]
                       ,$mode = $lig[2];
                    //this seems a bit strange, but ligatures for altGr and shift+altGr
                    //are counted by a real column number, not a 'virtual' one, set in the column header
                    if ($columns[$mode]) {
                        if (!$ligature[$key]) $ligature[$key] = [];
                        $ligature[$key][$columns[$mode]] = $lig[3].split(/\t/).map(function(a){return String.fromCharCode(parseInt(a, 16));}).filter();
                    }
                }
            }
        }
    }

    /**
     *  Preprocesses layout according to the KLC 1.4 file format
     *  Retrieves only name, code, domain, copyright and company fields
     *
     *  @return void
     */
    var parseHeader = function () {
        var $m = $layoutText.match(/^.*?KBD\t[^\t]+\t"([^"]+)/m);
        $name = $m[1];
        // probably we have to skip standard comment from MSKLC like "- Custom"
//        $name = array_shift(preg_split("/\\s-\\s/",$m[1]));

        $m = $layoutText.match(/^LOCALENAME\t"(\w+)-(\w+)/m);
        if ($m && $m.length) {
            $code = $m[2];
            $domain = mb_strtoupper($m[1]);
        } else {
            $code = "US";
            $domain = "EN";
        }

        $m = $layoutText.match(/^COMPANY\t"([^"]*)/m);
        $company = $m[1];

        $m = $layoutText.match(/^COPYRIGHT\t"([^"]+)/m);
        $copyright = $m[1];
        if (/ilya lebedev/i.test($copyright)) $copyright = "";
    }

    /**
     *  Makes the complete KLC parse, except done in the {@link #preParse}
     *
     *  @return void
     */
    var parse = function () {
        // available columns
        var $m = $layoutText.match(/^\/\/(SC[^\r\n]+)/m);
        $columns = $m[1].split(/\t/).slice(3);

        parseLigature();

        // LAYOUT block
        // String format: ScanCode \t KeyId \t CapsFlag \t NormalKey \t ShiftKey       \t (and so on)
        // [SGCaps]:      -1       \t -1    \t 0        \t SGCapsKey \t ShiftSGCapsKey
        var $strings = $layoutText.match(/^LAYOUT(?:.|[\r\n])+?--[\r\n]+((?:.|[\r\n])+?)(?:^DEADKEY|^LIGATURE|^KEYNAME)/m)[1]
                                  .split(/[\r\n]+/).map(function(a){ return a.split(/\t+/);});

        // Column values
        // 0 - normal key state
        // 1 - Shift
        // 2 - Ctrl 
        // 3 - Shift+Ctrl
        // 4 - AltGr (Shift+Ctrl)
        // 5 - Shift+AltGr (Shift+Ctrl+Alt)
        // 6 - SGCaps
        // 7 - Shift+SGCaps
        for (var $i=0, $sL=$strings.length; $i<$sL; $i++) {
            var $sgcaps  = 'SGCap' == $strings[$i][2]
               ,$shiftlock = 1 == $strings[$i][2]
               ,$keyCode = $strings[$i][0]
               ,$keyName = $strings[$i][1]
               ,$string  = $strings[$i];
            if ($keymap[$keyCode]) {
                $lKey    = $keymap[$keyCode];
                for (var $z=0, $cL=$columns.length; $z<$cL; $z++) {
                    var $v = $columns[$z]
                       ,$sym = $string[$z+3].trim()
                       ,$col = $colmap[$v]
                       ,$res;
                    if ('-1' == $sym) {
                       continue;
                    } else if ('%%' == $sym) {
                       $res = $ligature[$keyName][$v];
                    } else if ($sym.match(/@$/)) {
                       $sym = $sym.replace('@','');
                       $res = ["\x03",__chr2utf($sym)];
                       parseDeadkey($sym);
                    } else {
                       $res = __chr2utf($sym);
                    }
                    $lKey[$col] = $res;
                    if ($shiftlock) {
                        if (0 == $z) {
                            $lKey[$colmap[9]] = $res;
                        } else if (1 == $z) {
                            $lKey[$colmap[8]] = $res;
                        }
                    }
                }
                if ($sgcaps) {
                    $string = $strings[$i+1];
                    if ($string[3] && -1 == $string[3].indexOf("//")) {
                         $lKey[$colmap[8]] = __chr2utf($string[3]);
                    }
                    if ($string[4] && -1 == $string[4].indexOf("//")) {
                         $lKey[$colmap[9]] = __chr2utf($string[4]);
                    }
                }
            }
            if ($sgcaps) {
                $i++;
            }
        }
    }
    
    /**
     *  Parses layout and returns structured data describing it
     *
     *  @return array
     */
    self.getParsedLayout = function () {
        parse();

        var $VK = {'name'    : $name
                  ,'code'    : $code
                  ,'domain'  : $domain
                  ,'copy'    : $copyright
                  ,'company' : $company
                  ,'keys'    : $keymap
                  ,'dk'      : $deadkey
                  ,'cbk'     : self.getCallback()
                  ,'addon'   : self.getAddon()
                  };
        return $VK;
    }

    /**
     *  @return string with addon file path
     */
    self.getAddon = function () {
        $add = $root + $addon + $code + '.js';
        if (!file_exists($add)) {
            $add = '';
        }
        return $add;
    }

    /**
     *  @return string with callback file path
     */
    self.getCallback = function () {
        $add = $root + $addon + $callback + $fname.replace(/.+[\/\\]+(.+)\.klc$/i,"$1.js");
        if (file_exists($add)) {
            return $add;
        }
        return '';
    }

    /**
     *  @return layout name
     */
    self.getName = function () {
        return $name;
    }

    /**
     *  @return layout code
     */
    self.getCode = function () {
        return $code;
    }

    /**
     *  @return layout domain
     */
    self.getDomain = function () {
        return $domain;
    }

    /**
     *  @return layout domain
     */
    self.getCopyright = function () {
        return $copyright;
    }

    /**
     *  Serializes layout in the following way
     *  1) ligatures are encoded as substrings surrounded with 0x01
     *  2) 'normal' key state is 47-item length array with the "empty" keys shown as 0x02
     *  3) 'shift', 'alt', 'shift_alt', 'caps', 'shift_caps' are the objects with 'diffs' against
     *     the 'normal' keys
     *  4) deadkeys are stored as the hash of string with all available pairs of the source and target symbols
     *
     *  @param $type one of: SERIALIZE_USE_CODE or SERIALIZE_USE_DOMAIN
     */
    self.serialize = function ($type) {
        var $dom = mb_strtoupper($domain)
           ,$_code;
        switch ($type) {
            case "lng" :
                $_code = $dom==$code?$code
                                    :$dom+'-'+$code;
                break;
            case "domain" :
                $_code = $dom;
                break;
            default:
                return "";
        }

        parse();


        var $anc  = []
           ,$asc  = []
           ,$aac  = []
           ,$asac = []
           ,$acc  = []
           ,$ascc = []
           ,$i_anc = $i_asc = $i_aac = $i_asac = $i_acc = $i_ascc = 0;

        for (var $k in $keymap) {

            var $v   = $keymap[$k]
               ,$nc  = $v['normal']
               ,$sc  = $v['shift']
               ,$ac  = $v['alt']
               ,$sac = $v['shift_alt']
               ,$cc  = $v['caps']
               ,$scc = $v['shift_caps'];

            if (!$nc) {
                // key not exists
                $nc = String.fromCharCode(0x02);
            } else if ($nc instanceof Array) {
                // ligature
                $nc = "[" + $nc.map(__char2code).join(",") + "]";
            }
            if ($sc instanceof Array) {
                // ligature
                $sc = "[" + $sc.map(__char2code).join(",") + "]";
            }
            if ($ac instanceof Array) {
                // ligature
                $ac = "[" + $ac.map(__char2code).join(",") + "]";
            }
            if ($sac instanceof Array) {
                // ligature
                $sac = "[" + $sac.map(__char2code).join(",") + "]";
            }
            if ($cc instanceof Array) {
                // ligature
                $cc = "[" + $cc.map(__char2code).join(",") + "]";
            }
            if ($scc instanceof Array) {
                // ligature
                $scc = "[" + $scc.map(__char2code).join(",") + "]";
            }

            // fill the things
            $anc[$i_anc++] = $nc;

            //shift
            if (is_string($sc) && ($sc.match(/^\[3,/) || in_array($sc, $problemChars) || mb_strtoupper($sc) != mb_strtoupper($nc))) {
                if (!$asc[$i_asc]) $asc[$i_asc] = [];
                $asc[$i_asc].push($sc);
            } else {
                // key not exists
                $i_asc = $i_anc;
            }
            // alt
            if (is_string($ac) && ($ac.match(/^\[3,/) || in_array($ac, $problemChars) || mb_strtoupper($ac) != mb_strtoupper($nc))) {
                if (!$aac[$i_aac]) $aac[$i_aac] = [];
                $aac[$i_aac].push($ac);
            } else {
                // key not exists
                $i_aac = $i_anc;
            }
            // shift+alt
            if (is_string($sac) && ($sac.match(/^\[3,/) || (in_array($sac, $problemChars) || mb_strtoupper($sac) != mb_strtoupper($ac)))) {
                if (!$asac[$i_asac]) $asac[$i_asac] = [];
                $asac[$i_asac].push($sac);
            } else {
                // key not exists
                $i_asac = $i_anc;
            }
            // caps
            if (is_string($cc) && (in_array($cc, $problemChars) || mb_strtoupper($cc) != mb_strtoupper($nc))) {
                if (!$acc[$i_acc]) $acc[$i_acc] = [];
                $acc[$i_acc].push($cc);
            } else {
                // key not exists
                $i_acc = $i_anc;
            }
            // shift+caps
            if (is_string($scc) && (in_array($scc, $problemChars) || mb_strtoupper($scc) != mb_strtoupper($cc))) {
                if (!$ascc[$i_ascc]) $ascc[$i_ascc] = [];
                $ascc[$i_ascc].push($scc);
            } else {
                // key not exists
                $i_ascc = $i_anc;
            }                                             	

        }

        $serArr = ["code:'" + addcslashes($_code) + "'"
                  ,"name:'" + addcslashes($name) + "'"
                  ,__serializeRow($anc, $colmap[0])
                  ,__serializeRow($asc, $colmap[1])
                  ,__serializeRow($aac, $colmap[6])
                  ,__serializeRow($asac, $colmap[7])
                  ,__serializeRow($acc, $colmap[8])
                  ,__serializeRow($ascc, $colmap[9])
                  ,__serializeDeadkeys()
                  ];

        $add = self.getCallback();
        if ($add) {
            $serArr.push("cbk:" + file_get_contents($add).trim());
        }

        return "{" + $serArr.filter().join("\n,") + "}";
    }

    ;(function(){
	$controlCodes = $controlCodes.join("");
	$problemChars = $problemChars.map(function(a) {return String.fromCharCode(parseInt(a, 16))});

	$root = $fname.replace(/\\/g,'/');
        $root = $root.replace(/(\/)[^\/]+\.klc$/i,'');

        $layoutText = IOsys.readString($fname);

        parseHeader();
    })();
}

/**
 *  Emulates php's one
 */
function file_get_contents(fname) {
//    return IOsys.readString(fname);
    var f = IOsys.GetFile(fname);
    return f.OpenAsTextStream(1,-2).Read(f.Size())
}
/**
 *  Emulates php's one
 */
function file_exists(fname) {
    return IOsys.FileExists(fname);
}
/**
 *  Emulates php's one
 */
function addcslashes (str) {
    return str.replace(/(['\\])/g,"\$1");
}
/**
 *  Emulates php's one
 */
function in_array ($needle, $haystack) {
    return $haystack.indexOf($needle) > -1
}
/**
 *  Emulates php's one
 */
function is_string ($v) {
    return "string"==typeof $v;
}
/**
 *  Emulates php's one
 */
function mb_strtoupper ($str) {
    if ($str) return $str.toUpperCase();
}
