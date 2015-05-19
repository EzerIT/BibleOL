// -*- js -*-
/* 2013 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk */

String.prototype.format = function () {
    var args = arguments;
    return this.replace(/{(\d+)}/g, function (match, num) {
        return typeof args[num] != 'undefined' ? args[num] : match;
    });
};

// Modern browsers have trim()
if (!String.prototype.trim) {
    // This browser doesn't have trim()
    String.prototype.trim = function () {
        return this.replace(/^\s+|\s+$/g, '');
    };
}

var util;
(function (util) {
    function mydump(arr, level, maxlevel) {
        if (typeof level === "undefined") { level = 0; }
        if (typeof maxlevel === "undefined") { maxlevel = 5; }
        var dumped_text = '';
        var level_padding = '';
        for (var j = 0; j < level + 1; j++)
            level_padding += '    ';

        if (typeof (arr) == 'object') {
            for (var item in arr) {
                var value = arr[item];

                if (typeof (value) == 'object') {
                    dumped_text += level_padding + "'" + item + "' ...\n";
                    if (level < maxlevel)
                        dumped_text += mydump(value, level + 1, maxlevel);
                    else
                        dumped_text += level_padding + "MAX LEVEL\n";
                } else {
                    dumped_text += level_padding + "'" + item + "' => \"" + value + "\"\n";
                }
            }
        } else {
            dumped_text = "===>" + arr + "<===(" + typeof (arr) + ")";
        }
        return dumped_text;
    }
    util.mydump = mydump;

    // TODO: Make generic, when available in TypeScript
    var Pair = (function () {
        function Pair(first, second) {
            this.first = first;
            this.second = second;
        }
        return Pair;
    })();
    util.Pair = Pair;

    var setwordsp = false;
    var forceWsCount = 0;
    var forceWideCount = 0;

    var setborder = [];
    var forceBorderCount = [];

    function resetCheckboxCounters() {
        forceWsCount = 0;
        forceWideCount = 0;
        forceBorderCount = [];
    }
    util.resetCheckboxCounters = resetCheckboxCounters;

    function explicitWordSpace(val) {
        setwordsp = val;
        setWordSpace(val);
    }
    util.explicitWordSpace = explicitWordSpace;

    function setWordSpace(val) {
        if (val) {
            $('.cont').removeClass('cont1');
            $('.cont').addClass('cont2');
            $('.contx').removeClass('cont1');
            $('.contx').addClass('cont2x');
        } else {
            $('.cont').removeClass('cont2');
            $('.cont').addClass('cont1');
            $('.contx').removeClass('cont2x');
            $('.contx').addClass('cont1');
        }
    }

    function forceWordSpace(val) {
        if (val)
            ++forceWsCount;
        else
            --forceWsCount;

        if (val && forceWsCount == 1) {
            $('#ws_cb').prop('disabled', true);
            $('#ws_cb').prop('checked', true);
            setWordSpace(true);
        } else if (!val && forceWsCount == 0) {
            $('#ws_cb').prop('disabled', false);
            $('#ws_cb').prop('checked', setwordsp);
            setWordSpace(setwordsp);
        }
    }
    util.forceWordSpace = forceWordSpace;

    function forceWide(val) {
        if (val)
            ++forceWideCount;
        else
            --forceWideCount;

        if (val && forceWideCount == 1) {
            $('.textblock').css('margin-left', '30px').removeClass('inline').addClass('inlineblock');
        } else if (!val && forceWideCount == 0) {
            $('.textblock').css('margin-left', '0').removeClass('inlineblock').addClass('inline');
            ;
        }
    }
    util.forceWide = forceWide;

    function explicitBorder(val, level) {
        setborder[level] = val;
        showBorder(val, level);
    }
    util.explicitBorder = explicitBorder;

    function showBorder(val, level) {
        var classN = 'lev' + level;
        var noClassN = 'nolev' + level;

        if (val) {
            $('.' + noClassN + '> .gram').removeClass('dontshowit').addClass('showit'); //css('display','inline-block');
            $('.' + noClassN).addClass(classN);
            $('.' + noClassN).removeClass(noClassN);
        } else {
            $('.' + classN + '> .gram').removeClass('showit').addClass('dontshowit'); //css('display','none');
            $('.' + classN).addClass(noClassN);
            $('.' + classN).removeClass(classN);
        }
    }
    util.showBorder = showBorder;

    function separateLines(val, level) {
        var oldSepLin = val ? 'noseplin' : 'seplin';
        var newSepLin = val ? 'seplin' : 'noseplin';

        $('.notdummy.nolev' + level).removeClass(oldSepLin).addClass(newSepLin);
        $('.notdummy.lev' + level).removeClass(oldSepLin).addClass(newSepLin);
    }
    util.separateLines = separateLines;

    function forceBorder(val, level) {
        if (val)
            forceBorderCount[level] ? ++forceBorderCount[level] : forceBorderCount[level] = 1;
        else
            --forceBorderCount[level];

        var cbid = '#lev{0}_sb_cb'.format(level);

        if (val && forceBorderCount[level] == 1) {
            $(cbid).prop('disabled', true);
            $(cbid).prop('checked', true);
            showBorder(true, level);
        } else if (!val && forceBorderCount[level] == 0) {
            $(cbid).prop('disabled', false);
            $(cbid).prop('checked', setborder[level] === true);
            showBorder(setborder[level] === true, level);
        }
    }
    util.forceBorder = forceBorder;

    var AddBetween = (function () {
        function AddBetween(text) {
            this.text = text;
            this.first = true;
        }
        AddBetween.prototype.getStr = function () {
            if (this.first) {
                this.first = false;
                return '';
            } else
                return this.text;
        };

        AddBetween.prototype.reset = function () {
            this.first = true;
        };
        return AddBetween;
    })();
    util.AddBetween = AddBetween;
})(util || (util = {}));
// -*- js -*-
/// <reference path="jquery/jquery.d.ts" />

function getObjectSetting(otype) {
    return configuration.objectSettings[otype];
}

function getFeatureSetting(otype, feature) {
    // Handle the pseudo-feature
    if (feature === 'visual') {
        otype = configuration.objHasSurface;
        feature = configuration.surfaceFeature;
    }
    return getObjectSetting(otype).featuresetting[feature];
}
// -*- js -*-
var __extends = this.__extends || function (d, b) {
    for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p];
    function __() { this.constructor = d; }
    __.prototype = b.prototype;
    d.prototype = new __();
};
/* 2013 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk */
var WHAT;
(function (WHAT) {
    WHAT[WHAT["feature"] = 0] = "feature";
    WHAT[WHAT["metafeature"] = 1] = "metafeature";
    WHAT[WHAT["groupstart"] = 2] = "groupstart";
    WHAT[WHAT["groupend"] = 3] = "groupend";
})(WHAT || (WHAT = {}));

var GrammarGroup = (function () {
    function GrammarGroup() {
    }
    GrammarGroup.prototype.getFeatName = function (objType, callback) {
        callback(2 /* groupstart */, objType, this.name, l10n.grammargroup[objType][this.name], this);
        for (var i in this.items) {
            if (isNaN(+i))
                continue;
            this.items[+i].getFeatName(objType, callback);
        }
        callback(3 /* groupend */, objType, this.name, null, this);
    };

    GrammarGroup.prototype.getFeatVal = function (monob, objType, abbrev, callback) {
        callback(2 /* groupstart */, objType, this.name, null, null, this);
        for (var i in this.items) {
            if (isNaN(+i))
                continue;
            this.items[+i].getFeatVal(monob, objType, abbrev, callback);
        }
        callback(3 /* groupend */, objType, this.name, null, null, this);
    };

    /** Do the children of this object identify the specified feature?
    * @param f The name of the feature to look for.
    * @return True if the specified feature matches this object.
    */
    GrammarGroup.prototype.containsFeature = function (f) {
        for (var i in this.items) {
            if (isNaN(+i))
                continue;
            if (this.items[+i].containsFeature(f))
                return true;
        }
        return false;
    };
    return GrammarGroup;
})();

var GrammarSubFeature = (function () {
    function GrammarSubFeature() {
    }
    GrammarSubFeature.prototype.getFeatValPart = function (monob, objType) {
        return l10n.grammarsubfeature[objType][this.name][monob.mo.features[this.name]];
    };

    /** Does this object identify the specified feature?
    * @param f The name of the feature to look for.
    * @return True if the specified feature matches this object.
    */
    GrammarSubFeature.prototype.containsFeature = function (f) {
        return this.name === f;
    };
    return GrammarSubFeature;
})();

var SentenceGrammar = (function (_super) {
    __extends(SentenceGrammar, _super);
    function SentenceGrammar() {
        _super.apply(this, arguments);
    }
    SentenceGrammar.prototype.getFeatName = function (objType, callback) {
        for (var i in this.items) {
            if (isNaN(+i))
                continue;
            this.items[+i].getFeatName(objType, callback);
        }
    };

    SentenceGrammar.prototype.getFeatVal = function (monob, objType, abbrev, callback) {
        for (var i in this.items) {
            if (isNaN(+i))
                continue;
            this.items[+i].getFeatVal(monob, objType, abbrev, callback);
        }
    };

    /** Do the children of this object identify the specified feature?
    * @param f The name of the feature to look for.
    * @return True if the specified feature matches this object.
    */
    SentenceGrammar.prototype.containsFeature = function (f) {
        for (var i in this.items) {
            if (isNaN(+i))
                continue;
            if (this.items[+i].containsFeature(f))
                return true;
        }
        return false;
    };
    return SentenceGrammar;
})(GrammarGroup);

var GrammarMetaFeature = (function () {
    function GrammarMetaFeature() {
    }
    GrammarMetaFeature.prototype.getFeatName = function (objType, callback) {
        callback(1 /* metafeature */, objType, this.name, l10n.grammarmetafeature[objType][this.name], this);
    };

    GrammarMetaFeature.prototype.getFeatVal = function (monob, objType, abbrev, callback) {
        var res = '';
        for (var i in this.items) {
            if (isNaN(+i))
                continue;
            res += this.items[+i].getFeatValPart(monob, objType);
        }

        callback(1 /* metafeature */, objType, this.name, null, res, this);
    };

    /** Do the children of this object identify the specified feature?
    * @param f The name of the feature to look for.
    * @return True if the specified feature matches this object.
    */
    GrammarMetaFeature.prototype.containsFeature = function (f) {
        for (var i in this.items) {
            if (isNaN(+i))
                continue;
            if (this.items[+i].containsFeature(f))
                return true;
        }
        return false;
    };
    return GrammarMetaFeature;
})();

var GrammarFeature = (function () {
    function GrammarFeature() {
        this.coloring = {};
    }
    GrammarFeature.prototype.getFeatName = function (objType, callback) {
        var locname = l10n.grammarfeature && l10n.grammarfeature[objType] && l10n.grammarfeature[objType][this.name] ? l10n.grammarfeature[objType][this.name] : l10n.emdrosobject[objType][this.name];

        callback(0 /* feature */, objType, this.name, locname, this);
    };

    GrammarFeature.prototype.getFeatVal = function (monob, objType, abbrev, callback) {
        var featType = typeinfo.obj2feat[objType][this.name];
        var res1 = monob.mo.features ? monob.mo.features[this.name] : '';
        var res = res1;

        switch (featType) {
            case 'string':
            case 'ascii':
                if (res === '')
                    res = '-';
                break;

            case 'integer':
                break;

            default:
                if (res !== '')
                    res = StringWithSort.stripSortIndex(getFeatureValueFriendlyName(featType, res, abbrev));
                break;
        }
        callback(0 /* feature */, objType, this.name, res1, res, this);
    };

    /** Does this object identify the specified feature?
    * @param f The name of the feature to look for.
    * @return True if the specified feature matches this object.
    */
    GrammarFeature.prototype.containsFeature = function (f) {
        return this.name === f;
    };
    return GrammarFeature;
})();

function getSentenceGrammarFor(oType) {
    for (var i = 0; i < configuration.sentencegrammar.length; ++i)
        if (configuration.sentencegrammar[i].objType === oType)
            return configuration.sentencegrammar[i];

    return null;
}

// This function copies all fields from the source to the destination
function copyFields(dst, src) {
    for (var f in src)
        dst[f] = src[f];
}

// This function adds relevant methods to a data object of the specified class
function addMethods(obj, classname) {
    for (var f in classname.prototype) {
        if (f === 'constructor')
            continue;
        obj[f] = classname.prototype[f];
    }
}

// This function adds relevant methods to a data object of a specific SentenceGrammarItem subtype
function addMethodsSgi(sgi) {
    addMethods(sgi, eval(sgi.mytype)); // sgi.mytype is the name of the subclass, generated by the server

    // Do the same with all members of the items array
    if (sgi.items) {
        for (var i in sgi.items) {
            if (isNaN(+i))
                continue;
            addMethodsSgi(sgi.items[+i]);
        }
    }
}
// -*- js -*-
/* 2013 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk */
/// @file
/// @brief Characteristics of the current character set
var Charset = (function () {
    function Charset(cs) {
        switch (cs) {
            case 'hebrew':
                this.foreignClass = 'hebrew';
                this.transliteratedClass = 'hebrew_translit';
                this.isHebrew = true;
                this.isRtl = true;
                this.keyboardName = 'IL';
                break;
            case 'transliterated_hebrew':
                this.foreignClass = 'hebrew_translit';
                this.transliteratedClass = 'hebrew';
                this.isHebrew = true;
                this.isRtl = false;
                this.keyboardName = 'TRHE';
                break;
            case 'greek':
                this.foreignClass = 'greek';
                this.isHebrew = false;
                this.isRtl = false;
                this.keyboardName = 'GR';
                break;
            default:
                this.foreignClass = 'latin';
                this.transliteratedClass = 'latin';
                this.isHebrew = false;
                this.isRtl = false;
                break;
        }
    }
    return Charset;
})();
// -*- js -*-
/* 2013 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk */

function getFirst(ms) {
    var first = 1000000000;
    for (var pci in ms.segments) {
        if (isNaN(+pci))
            continue;

        var pc = ms.segments[+pci];
        if (pc.low < first)
            first = pc.low;
    }
    return first;
}

function getSingleInteger(ms) {
    if (ms.segments.length === 1) {
        var p = ms.segments[0];
        if (p.low === p.high)
            return p.low;
    }

    throw 'MonadSet.ObjNotSingleMonad';
}

function getMonadArray(ms) {
    var res = [];

    for (var i in ms.segments) {
        if (isNaN(+i))
            continue;

        var mp = ms.segments[+i];
        for (var j = mp.low; j <= mp.high; ++j)
            res.push(j);
    }
    return res;
}
// -*- js -*-
/* 2013 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk */
var urlTypeString = {
    'u': 'click_for_web_site',
    'v': 'click_for_video',
    'd': 'click_for_document'
};

var DisplayMonadObject = (function () {
    /** Creates a {@code DisplayMonadObject}. This includes creating the display panel and popup.
    * @param mo The {@link MonadObject} displayed (perhaps in part) by this {@code DisplayMonadObject}.
    * @param objType The Emdros object type represented by this {@code DisplayMonadObject}.
    * @param level The level of the object.
    */
    function DisplayMonadObject(mo, objType, level) {
        this.uniqId = ++DisplayMonadObject.uniqIdStatic;
        this.displayedMo = mo;

        if (mo.displayers == undefined)
            mo.displayers = [this];
        else
            mo.displayers.push(this);

        this.objType = objType;
        this.level = level;
    }
    DisplayMonadObject.prototype.generateHtml = function (qd, sentenceTextArr) {
        alert('Abstract function generateHtml called');
        return null;
    };

    /** Determines if this object is a subset of another {@code DisplayMonadObject}.
    * @param mo Another {@code DisplayMonadObject}.
    * @return True if the monad set represented by this {@code DisplayMonadObject} is a subset of the
    * monad set represented by the parameter {@code mo}.
    */
    DisplayMonadObject.prototype.containedIn = function (mo) {
        return this.range.low >= mo.range.low && this.range.high <= mo.range.high;
    };
    DisplayMonadObject.uniqIdStatic = 0;
    return DisplayMonadObject;
})();

/** A {@code DisplaySingleMonadObject} is a {@code DisplayMonadObject} that can display a text
* component at the lowest level, corresponding to a single monad in an Emdros database. This is
* typically a single word.
* <p>
* Hebrew is a special case here. In most languages, words are separated by spaces. In Hebrew, that
* is not necessarily the case. The Hebrew Bible starts with the word <i>bereshit</i> ("in the
* beginning"), but this is actually two words: <i>be</i> ("in") and <i>reshit</i> ("beginning"). When this
* program shows the text without annotation, the words are strung together (<i>bereshit</i>), but when
* annotation is included, the words are split (<i>be-&nbsp;reshit</i>).
*/
var DisplaySingleMonadObject = (function (_super) {
    __extends(DisplaySingleMonadObject, _super);
    /** Creates a {@code DisplaySingleMonadObject}. This includes setting up a mouse listener that
    * highlights enclosing phrase and clause frames. Note that this constructor does not set the
    * text; that is done by a subsequent call to {@link #setText(String,String,Font,Color)}.
    * @param smo The {@link SingleMonadObject} displayed by this {@code DisplaySingleMonadObject}.
    * @param objType The Emdros object type represented by this {@code DisplaySingleMonadObject}.
    * @param inQuiz Is this part of a quiz (in which case we must not display chapter and verse).
    */
    function DisplaySingleMonadObject(smo, objType, inQuiz) {
        _super.call(this, smo, objType, 0);
        this.inQuiz = inQuiz;
        this.monad = smo.mo.monadset.segments[0].low;
        this.range = { low: this.monad, high: this.monad };
    }
    DisplaySingleMonadObject.prototype.generateHtml = function (qd, sentenceTextArr) {
        var smo = this.displayedMo;
        var uhSize = smo.bcv.length;
        var chapter = null;
        var verse = null;
        var appendSofPasuq = false;
        var refs = null;
        var urls = null;

        if (uhSize != 0) {
            if (uhSize != smo.sameAsPrev.length)
                throw 'BAD2';
            if (uhSize != smo.sameAsNext.length)
                throw 'BAD3';

            // If this is not a quiz, add book, chapter, and verse, plus sof pasuq, if needed
            if (!this.inQuiz) {
                document.title = l10n.universe['book'][smo.bcv[0]];
                $('#textcontainer h1').html(document.title);

                for (var i = 0; i < uhSize; ++i) {
                    if (!smo.sameAsPrev[i]) {
                        if (i == 1)
                            chapter = smo.bcv[i];
                        else if (i == 2) {
                            verse = smo.bcv[i];
                            refs = smo.pics;
                            urls = smo.urls;
                        }
                    }
                    if (!smo.sameAsNext[i]) {
                        if (i == 2)
                            appendSofPasuq = true;
                    }
                }
            }
        }

        var text;
        var id_d = qd ? qd.monad2Id[this.monad] : null;
        if (id_d) {
            // This is a quiz object
            if (qd.quizFeatures.dontShow)
                text = '({0})'.format(++DisplaySingleMonadObject.itemIndex);
            else
                text = this.displayedMo.mo.features[configuration.surfaceFeature];
            text = '<em>' + text + '</em>';
        } else {
            text = this.displayedMo.mo.features[configuration.surfaceFeature];
            if (configuration.useSofPasuq && appendSofPasuq)
                text += charset.isRtl ? DisplaySingleMonadObject.sof_pasuq : ':';
        }

        var chapterstring = chapter == null ? '' : '<span class="chapter">{0}</span>&#x200a;'.format(chapter);
        var versestring = verse == null ? '' : '<span class="verse">{0}</span>'.format(verse);
        var refstring;

        if (refs === null)
            refstring = '';
        else if (refs.length === 4)
            refstring = '<a target="_blank" title="{2}" href="http://resources.3bmoodle.dk/link.php?picno={0}"><img src="{1}images/p.png"></a>'.format(refs[3], site_url, localize('click_for_picture'));
        else
            refstring = '<a target="_blank" title="{4}" href="http://resources.3bmoodle.dk/img.php?book={0}&chapter={1}&verse={2}"><img src="{3}images/pblue.png"></a>'.format(refs[0], refs[1], refs[2], site_url, localize('click_for_pictures'));

        var urlstring = '';
        if (urls !== null) {
            var len = urls.length;
            for (var uix = 0; uix < urls.length; ++uix) {
                urlstring += '<a target="_blank" title="{0}" href="{1}"><img src="{2}images/{3}.png"></a>'.format(localize(urlTypeString[urls[uix][1]]), urls[uix][0], site_url, urls[uix][1]);
            }
        }
        var grammar = '';
        configuration.sentencegrammar[0].getFeatVal(smo, this.objType, false, function (whattype, objType, featName, featVal, featValLoc) {
            switch (whattype) {
                case 0 /* feature */:
                    var wordclass;
                    var fs = getFeatureSetting(objType, featName);
                    if (fs.foreignText)
                        wordclass = charset.foreignClass;
                    else if (fs.transliteratedText)
                        wordclass = charset.transliteratedClass;
                    else
                        wordclass = 'ltr';
                    grammar += '<span class="wordgrammar dontshowit {0} {2}">{1}</span>'.format(featName, featValLoc, wordclass);
                    break;

                case 1 /* metafeature */:
                    grammar += '<span class="wordgrammar dontshowit {0} ltr">{1}</span>'.format(featName, featValLoc);
                    break;
            }
        });

        var follow_space = '<span class="wordspace"> </span>';
        var follow_class = '';

        if (charset.isHebrew) {
            var suffix = smo.mo.features[configuration.suffixFeature];
            if (suffix === '' || suffix === '-' || suffix === '\u05be') {
                follow_space = ''; // Prevents line wrapping
                follow_class = suffix === '' ? ' cont cont1' : ' contx cont1';
                text += suffix;
                sentenceTextArr[0] += text;
            } else
                sentenceTextArr[0] += text + ' ';
        } else
            sentenceTextArr[0] += text + ' ';

        return $('<span class="textblock inline"><span class="textdisplay {0}" data-idd="{1}">{2}{3}{4}{5}{6}</span>{7}</span>{8}'.format(charset.foreignClass + follow_class, smo.mo.id_d, chapterstring, versestring, refstring, urlstring, text, grammar, follow_space));
    };
    DisplaySingleMonadObject.sof_pasuq = '&#x05c3;';
    return DisplaySingleMonadObject;
})(DisplayMonadObject);

// TODO: Fix this
var Color = (function () {
    function Color(a, b, c) {
    }
    return Color;
})();

var DisplayMultipleMonadObject = (function (_super) {
    __extends(DisplayMultipleMonadObject, _super);
    // Implementation of the overloaded constructors
    function DisplayMultipleMonadObject(mmo, objType, level, monadSet, hasPredecessor, hasSuccessor) {
        _super.call(this, mmo, objType, level);

        if (arguments.length == 6) {
            // Non-patriarch
            this.isPatriarch = false;
            this.range = monadSet;
            this.children = [];

            this.hasPredecessor = hasPredecessor;
            this.hasSuccessor = hasSuccessor;
            this.borderTitle = getObjectFriendlyName(objType);

            this.myColors = DisplayMultipleMonadObject.frameColors[(level - 1) % DisplayMultipleMonadObject.frameColors.length];
        } else {
            // Patriarch
            this.isPatriarch = true;

            this.range = { low: monadSet.segments[0].low, high: monadSet.segments[monadSet.segments.length - 1].high };
            this.children = [];

            this.hasPredecessor = false;
            this.hasSuccessor = false;
        }
    }
    DisplayMultipleMonadObject.prototype.generateHtml = function (qd, sentenceTextArr) {
        var spanclass = 'nolev{0} noseplin'.format(this.level);
        if (this.hasPredecessor)
            spanclass += ' hasp';
        if (this.hasSuccessor)
            spanclass += ' hass';

        var grammar = '';

        if (configuration.sentencegrammar[this.level])
            configuration.sentencegrammar[this.level].getFeatVal(this.displayedMo, this.objType, true, function (whattype, objType, featName, featVal, featValLoc) {
                if (whattype == 0 /* feature */ || whattype == 1 /* metafeature */)
                    grammar += '<span class="xgrammar dontshowit {0}_{1}">:{2}</span>'.format(objType, featName, featValLoc);
            });

        var jq;
        if (this.isPatriarch)
            jq = $('<span class="{0}"></span>'.format(spanclass));
        else {
            if (this.displayedMo.mo.name == "dummy")
                jq = $('<span class="{0}"><span class="nogram dontshowit" data-idd="{1}"></span></span>'.format(spanclass, this.displayedMo.mo.id_d));
            else
                jq = $('<span class="notdummy {0}"><span class="gram dontshowit" data-idd="{1}">{2}{3}</span></span>'.format(spanclass, this.displayedMo.mo.id_d, getObjectShortFriendlyName(this.objType), grammar));
        }

        for (var ch in this.children) {
            if (isNaN(+ch))
                continue;
            jq.append(this.children[ch].generateHtml(qd, sentenceTextArr));
        }

        return jq;
    };
    DisplayMultipleMonadObject.frameColors = [
        new util.Pair(new Color(0.000, 0.27, 0.98), new Color(0.000, 0.98, 0.71)),
        new util.Pair(new Color(0.667, 0.27, 0.98), new Color(0.667, 0.98, 0.71)),
        new util.Pair(new Color(0.39, 0.27, 0.98), new Color(0.39, 0.98, 0.71))];
    return DisplayMultipleMonadObject;
})(DisplayMonadObject);
// -*- js -*-
/* 2013 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk */

function getObjectFriendlyName(otype) {
    if (otype === 'Patriarch')
        return otype;
    var fn = l10n.emdrosobject[otype]._objname;
    return fn ? fn : otype;
}

function getObjectShortFriendlyName(otype) {
    if (l10n.emdrosobject[otype + '_abbrev'] === undefined)
        return getObjectFriendlyName(otype);
    else
        return l10n.emdrosobject[otype + '_abbrev']._objname;
}

function getFeatureFriendlyName(otype, feature) {
    if (feature === 'visual')
        return localize('visual');

    var fn = l10n.emdrosobject[otype][feature];
    return fn ? fn : feature;
}

function getFeatureValueFriendlyName(featureType, value, abbrev) {
    if (abbrev && l10n.emdrostype[featureType + '_abbrev'] !== undefined)
        // TODO: We assume there is no "list of " types here
        return l10n.emdrostype[featureType + '_abbrev'][value];

    // TODO: For now we handle "list of ..." here. Is this OK with all the other locations where this is used?
    if (featureType.substr(0, 8) === 'list of ') {
        featureType = featureType.substr(8); // Remove "list of "
        value = value.substr(1, value.length - 2); // Remove parenteses
        if (value.length == 0)
            return l10n.emdrostype[featureType]['NA'];

        var verb_classes = value.split(',');
        var localized_verb_classes = [];

        for (var ix in verb_classes)
            localized_verb_classes.push(l10n.emdrostype[featureType][verb_classes[+ix]]);

        localized_verb_classes.sort();
        return localized_verb_classes.join(', ');
    }

    return l10n.emdrostype[featureType][value];
}
// -*- js -*-
/* 2013 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk */

function localize(s) {
    var str = l10n_js[s];

    return str === undefined ? '??' + s + '??' : str;
}
// -*- js -*-


function mayShowFeature(oType, feat, sgiObj) {
    var inQuiz = $('#quiztab').length > 0;

    if (!inQuiz)
        return true;

    if (sgiObj.mytype === 'GrammarMetaFeature') {
        for (var i in sgiObj.items) {
            if (isNaN(+i))
                continue;
            if (!mayShowFeature(oType, sgiObj.items[+i].name, sgiObj.items[+i]))
                return false;
        }
        return true;
    }

    var qf = quizdata.quizFeatures;

    if (oType !== qf.objectType)
        return true;

    for (var ix = 0, len = qf.requestFeatures.length; ix < len; ++ix)
        if (qf.requestFeatures[ix].name === feat)
            return false;

    return qf.dontShowFeatures.indexOf(feat) === -1;
}
// -*- js -*-
/* 2013 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk */

var Dictionary = (function () {
    function Dictionary(dictif, index, inQuiz) {
        this.monads = [];
        this.level = [];
        this.singleMonads = [];
        this.dispMonadObjects = [];
        this.sentenceSet = dictif.sentenceSets[index];
        this.monadObjects1 = dictif.monadObjects[index];
        this.bookTitle = dictif.bookTitle;

        for (var level in this.monadObjects1) {
            var leveli = +level;
            if (isNaN(leveli))
                continue;

            for (var i in this.monadObjects1[leveli]) {
                if (isNaN(+i))
                    continue;

                var item = this.monadObjects1[leveli][+i];
                if (leveli === 0)
                    this.singleMonads[getSingleInteger(item.mo.monadset)] = item;
                this.monads[item.mo.id_d] = item;
                this.level[item.mo.id_d] = leveli;
            }
        }

        for (var i in this.monads) {
            if (isNaN(+i))
                continue;

            var parent = this.monads[+i];
            for (var i2 in parent.children_idds) {
                if (isNaN(+i2))
                    continue;

                var child_idd = parent.children_idds[+i2];
                this.monads[child_idd].parent = parent;
            }
        }

        // Create display hierarchy
        // Single monads
        var objType = configuration.sentencegrammar[0].objType;
        this.dispMonadObjects.push([]);

        for (var se in this.singleMonads) {
            if (isNaN(+se))
                continue;

            var dmo = new DisplaySingleMonadObject(this.singleMonads[+se], objType, inQuiz);
            this.dispMonadObjects[0].push(dmo);
            // Do we need this?: dispSingleMonads[se] = dmo;
        }

        for (var lev = 1; lev < configuration.maxLevels; ++lev) {
            var ldmo = [];

            this.dispMonadObjects.push(ldmo);

            if (lev < configuration.maxLevels - 1)
                objType = configuration.sentencegrammar[lev].objType;
            else
                objType = 'Patriarch'; //$NON-NLS-1$

            if (lev < configuration.maxLevels - 1) {
                for (var i in this.monadObjects1[lev]) {
                    if (isNaN(+i))
                        continue;

                    var monadObject = this.monadObjects1[lev][parseInt(i)];

                    // Split object into contiguous segments
                    var segCount = monadObject.mo.monadset.segments.length;

                    for (var mix = 0; mix < segCount; ++mix) {
                        var mp = monadObject.mo.monadset.segments[mix];
                        ldmo.push(new DisplayMultipleMonadObject(monadObject, objType, lev, mp, mix > 0, mix < segCount - 1));
                    }
                }

                ldmo.sort(function (a, b) {
                    // Sort in monad order
                    return a.range.low - b.range.low;
                });
            } else {
                // At the top level there is always only one DisplayMultipleMonadObject
                var monadObject = this.monadObjects1[lev][0];
                ldmo.push(new DisplayMultipleMonadObject(monadObject, objType, lev, monadObject.mo.monadset));
            }
        }

        for (var lev = 1; lev < configuration.maxLevels; ++lev) {
            for (var parentDmoIx in this.dispMonadObjects[lev]) {
                if (isNaN(+parentDmoIx))
                    continue;

                var parentDmo = this.dispMonadObjects[lev][+parentDmoIx];

                for (var childDmoIx in this.dispMonadObjects[lev - 1]) {
                    if (isNaN(+childDmoIx))
                        continue;

                    var childDmo = this.dispMonadObjects[lev - 1][+childDmoIx];
                    if (childDmo.containedIn(parentDmo)) {
                        // We found a child
                        if (childDmo.parent != undefined)
                            throw 'BAD1';
                        childDmo.parent = parentDmo;
                        parentDmo.children.push(childDmo);
                    }
                }
            }
        }
    }
    Dictionary.prototype.generateSentenceHtml = function (qd) {
        DisplaySingleMonadObject.itemIndex = 0;
        var sentenceTextArr = [''];
        $('#textarea').append(this.dispMonadObjects[this.dispMonadObjects.length - 1][0].generateHtml(qd, sentenceTextArr));

        var thisDict = this;

        var toolTipFunc = function (x_this) {
            var monob = thisDict.monads[+($(x_this).attr("data-idd"))];
            var level = thisDict.level[+($(x_this).attr("data-idd"))];
            var sengram = configuration.sentencegrammar[level];

            var res = '<table>';

            res += '<tr><td colspan="2" class="tooltiphead">{0}</td></tr>'.format(getObjectFriendlyName(sengram.objType));

            if (level === 0 && (!qd || !qd.quizFeatures.dontShow))
                res += '<tr><td>{2}</td><td class="tooltip leftalign {0}">{1}</td></tr>'.format(charset.foreignClass, monob.mo.features[configuration.surfaceFeature], localize('visual'));

            var map = [];

            sengram.getFeatName(sengram.objType, function (whattype, objType, featName, featNameLoc, sgiObj) {
                if (whattype == 0 /* feature */ || whattype == 1 /* metafeature */)
                    if (!mayShowFeature(objType, featName, sgiObj))
                        return;

                if (whattype == 0 /* feature */ || whattype == 1 /* metafeature */ || whattype == 2 /* groupstart */)
                    map[featName] = featNameLoc;
            });

            sengram.getFeatVal(monob, sengram.objType, false, function (whattype, objType, featName, featVal, featValLoc, sgiObj) {
                switch (whattype) {
                    case 0 /* feature */:
                        if (mayShowFeature(objType, featName, sgiObj)) {
                            var wordclass;
                            var fs = getFeatureSetting(objType, featName);
                            if (fs.foreignText)
                                wordclass = charset.foreignClass;
                            else if (fs.transliteratedText)
                                wordclass = charset.transliteratedClass;
                            else
                                wordclass = '';
                            res += '<tr><td>{0}</td><td class="tooltip leftalign {2}">{1}</td></tr>'.format(map[featName], featValLoc, featValLoc === '-' ? '' : wordclass);
                        }
                        break;

                    case 1 /* metafeature */:
                        if (mayShowFeature(objType, featName, sgiObj))
                            res += '<tr><td>{0}</td><td class="tooltip leftalign">{1}</td></tr>'.format(map[featName], featValLoc);
                        break;

                    case 2 /* groupstart */:
                        res += '<tr><td><b>{0}:</b></td><td class="leftalign"></td></tr>'.format(map[featName]);
                        break;
                }
            });

            return res + '</table>';
        };

        if (useTooltip) {
            $(document).tooltip({ items: "[data-idd]", content: function () {
                    return toolTipFunc(this);
                } });
        } else {
            $("[data-idd]").hover(function () {
                // Calculate vertical position of '.grammardisplay'.
                // It should be placed at least 20px from top of window but not higher
                // than '#textcontainer'
                var scrTop = $(window).scrollTop();
                var qcTop = $('#textcontainer').offset().top;
                $('.grammardisplay').html(toolTipFunc(this)).css('top', Math.max(qcTop, scrTop + 20)).show();
            }, function () {
                $('.grammardisplay').hide();
            });
        }
        return sentenceTextArr[0];
    };

    Dictionary.prototype.showattrs = function (idd) {
        var monob = this.monads[idd];

        for (var level = 0; level < configuration.maxLevels - 1; ++level) {
            configuration.sentencegrammar[level].getFeatVal(monob, configuration.sentencegrammar[level].objType, false, function (whattype, objType, featName, featVal, featValLoc, sgiObj) {
                if (whattype == 0 /* feature */ || whattype == 1 /* metafeature */)
                    $('#{0}_{1}_show'.format(objType, featName)).html(featValLoc);
            });
            monob = monob.parent;
        }
    };

    Dictionary.prototype.getSingleMonadObject = function (monad) {
        return this.singleMonads[monad];
    };
    return Dictionary;
})();
// -*- js -*-
/// @file
/// @brief Contains the ComponentWithYesNo class and the COMPONENT_TYPE enum.
/// The types of input field that can be associated with a correct/wrong indication.
var COMPONENT_TYPE;
(function (COMPONENT_TYPE) {
    COMPONENT_TYPE[COMPONENT_TYPE["textField"] = 0] = "textField";
    COMPONENT_TYPE[COMPONENT_TYPE["textFieldWithVirtKeyboard"] = 1] = "textFieldWithVirtKeyboard";
    COMPONENT_TYPE[COMPONENT_TYPE["translatedField"] = 2] = "translatedField";
    COMPONENT_TYPE[COMPONENT_TYPE["comboBox1"] = 3] = "comboBox1";
    COMPONENT_TYPE[COMPONENT_TYPE["comboBox2"] = 4] = "comboBox2";
})(COMPONENT_TYPE || (COMPONENT_TYPE = {}));

var ComponentWithYesNo = (function () {
    /// Creates a ComponentWithYesNo containing a specified component.
    /// @param elem The component to display.
    /// @param elemType The type of the component to display.
    function ComponentWithYesNo(elem, elemType) {
        this.elem = elem;
        this.elemType = elemType;
        this.yesIcon = $('<img src="' + site_url + '/images/ok.png" alt="Yes">');
        this.noIcon = $('<img src="' + site_url + '/images/notok.png" alt="No">');
        this.noneIcon = $('<img src="' + site_url + '/images/none.png" alt="None">');
    }
    ComponentWithYesNo.prototype.appendMeTo = function (dest) {
        var spn = $('<span style="white-space:nowrap;"></span>').append(this.yesIcon).append(this.noIcon).append(this.noneIcon).append(this.elem);
        dest.append(spn);
        this.setNone();
        return dest;
    };

    ComponentWithYesNo.monitorChange = function (elem, me) {
        clearInterval(ComponentWithYesNo.intervalHandler);

        if (ComponentWithYesNo.lastMonitored !== elem.data('kbid')) {
            ComponentWithYesNo.monitorOrigVal = elem.val();
            ComponentWithYesNo.lastMonitored = elem.data('kbid');
        }

        // Closure around polling function
        function timedfun(elem2, me2) {
            return function () {
                var s = elem2.val();
                if (s !== ComponentWithYesNo.monitorOrigVal) {
                    ComponentWithYesNo.monitorOrigVal = s;
                    me2.setNone();
                }
            };
        }

        ComponentWithYesNo.intervalHandler = setInterval(timedfun(elem, me), 500);
    };

    ComponentWithYesNo.prototype.addChangeListener = function () {
        var _this = this;
        this.elem.on('change', function () {
            return _this.setNone();
        });
    };

    // Check for keypress and paste event
    ComponentWithYesNo.prototype.addKeypressListener = function () {
        var _this = this;
        // TODO: Can all of this be changed to on('input', ...)?
        this.elem.on('paste cut', function (e1) {
            return _this.setNone();
        });

        // Note: Firefox sends keypress event on arrows and CTRL-C, Chrome and IE do not
        this.elem.on('keypress', function (e1) {
            return _this.setNone();
        }).on('keydown', function (e2) {
            if (e2.keyCode == 8 || e2.keyCode == 46)
                _this.elem.trigger('keypress');
        }); /* Ensure that backspace and del trigger keypress - they don't normally on Chrome */

        if (this.elemType === 1 /* textFieldWithVirtKeyboard */) {
            // We must do continuous polling of changes
            this.elem.on('focus', function (e) {
                return ComponentWithYesNo.monitorChange($(e.currentTarget), _this);
            });
        }
    };

    /// Gets the contained component.
    /// @return The component displayed with this object.
    ComponentWithYesNo.prototype.getComp = function () {
        if (this.elemType === 4 /* comboBox2 */)
            return $(this.elem.children()[0]);
        else
            return this.elem;
    };

    /// Gets the type of the component.
    /// @return The type of the component.
    ComponentWithYesNo.prototype.getCompType = function () {
        return this.elemType;
    };

    /// Sets the icon to indicate the correctness of an answer.
    /// @param yes Is the answer correct?
    ComponentWithYesNo.prototype.setYesNo = function (yes) {
        if (ComponentWithYesNo.lastMonitored === this.elem.data('kbid'))
            ComponentWithYesNo.monitorOrigVal = this.elem.val(); // Lest the polling detects the change and removes the yes/no mark

        if (yes) {
            this.yesIcon.show();
            this.noIcon.hide();
        } else {
            this.yesIcon.hide();
            this.noIcon.show();
        }
        this.noneIcon.hide();
    };

    /// Displays an empty icon.
    ComponentWithYesNo.prototype.setNone = function () {
        this.yesIcon.hide();
        this.noIcon.hide();
        this.noneIcon.show();
    };
    return ComponentWithYesNo;
})();
// -*- js -*-
/* 2013 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk */
/// @file
/// @brief Contains the Answer class.
/// This class represents an answer to a single feature request in the current question.
var Answer = (function () {
    /** Constructs an Answer object.
    * @param comp The feature request component.
    * @param answerSws The correct answer.
    * @param answerString The correct answer as a String.
    * @param matchRegexp The regular expression used to find a match, null if none is used.
    */
    function Answer(comp, answerSws, answerString, matchRegexp) {
        this.hasAnswered = false;
        this.comp = comp;
        this.c = comp.getComp();
        this.cType = comp.getCompType();
        this.answerSws = answerSws;
        this.answerString = answerString;
        this.matchRegexp = matchRegexp;
    }
    /// Displays the correct answer.
    Answer.prototype.showIt = function () {
        switch (this.cType) {
            case 0 /* textField */:
            case 1 /* textFieldWithVirtKeyboard */:
                $(this.c).val(this.answerString);
                break;
            case 2 /* translatedField */:
                break;
            case 3 /* comboBox1 */:
            case 4 /* comboBox2 */:
                $(this.c).val(this.answerSws.getInternal()).prop('selected', true);
                break;
        }
    };

    /// Compares the content of the feature request component with the correct answer and sets
    /// the Yes/No mark accordingly.
    /// @param fromShowIt True if this call comes from the user pressing "Show answer".
    Answer.prototype.checkIt = function (fromShowIt) {
        if (fromShowIt) {
            // The question panel now shows the correct answers, but they were not
            // necessarily provided by the user. If the user has not committed an answer to
            // this question, mark the question as unanswered.
            if (!this.hasAnswered) {
                this.hasAnswered = true;
                this.firstAnswer = "*Unanswered*";
                this.firstAnswerCorrect = false;
            }
            this.comp.setYesNo(true);
        } else {
            // The question panel contains the user's answers.
            // Where answers are provided, their correctness is logged.
            var userAnswer;
            var isCorrect;

            switch (this.cType) {
                case 0 /* textField */:
                case 1 /* textFieldWithVirtKeyboard */:
                    // TODO: Use Three_ET.dbInfo.charSet.converter.normalize (relevant only in Greek)
                    userAnswer = $(this.c).val().trim().replace(/\u03ac/g, '\u1f71').replace(/\u03ad/g, '\u1f73').replace(/\u03ae/g, '\u1f75').replace(/\u03af/g, '\u1f77').replace(/\u03cc/g, '\u1f79').replace(/\u03cd/g, '\u1f7b').replace(/\u03ce/g, '\u1f7d').replace(/\u0390/g, '\u1fd3').replace(/\u03b0/g, '\u1fe3'); // GREEK SMALL LETTER UPSILON WITH DIALYTIKA AND TONOS -> OXIA

                    if (this.matchRegexp == null) {
                        isCorrect = userAnswer == this.answerString; // Not === for one may be a number
                        if (!isCorrect)
                            isCorrect = this.answerString === '-' && userAnswer === '\u05be'; // Accept Maqaf instead of hyphen
                    } else {
                        // Escape all special characters in the user's answer
                        var re = eval(this.matchRegexp.format(userAnswer.replace(/[-[\]{}()*+?.,\\^$|#\s]/g, "\\$&")));
                        isCorrect = this.answerString.match(re) !== null;
                    }
                    break;

                case 2 /* translatedField */:
                    userAnswer = $(this.c).val().trim();
                    if (this.matchRegexp == null)
                        isCorrect = userAnswer == this.answerString; // Not === for one may be a number
                    else {
                        // Escape all special characters in the user's answer
                        var re = eval(this.matchRegexp.format(userAnswer.replace(/[-[\]{}()*+?.,\\^$|#\s]/g, "\\$&")));
                        isCorrect = this.answerString.match(re) !== null;
                    }
                    break;

                case 3 /* comboBox1 */:
                case 4 /* comboBox2 */:
                    // Note: At this point we use the intenal (language independent) name for the value.
                    // This is necessary in order to produce language indenpendent statistics. However,
                    // this will not work correctly if there are duplicate values in the friendly names.
                    // At present, this is only the case with the Winchester database.
                    var selectedOption = $(this.c).find(":selected");
                    if (selectedOption.attr('value') !== 'NoValueGiven') {
                        var userAnswerSws = $(this.c).find(":selected").data('sws');

                        isCorrect = userAnswerSws === this.answerSws;
                        userAnswer = userAnswerSws.getInternal();
                    }
                    break;
            }
            if (userAnswer && !this.hasAnswered) {
                this.hasAnswered = true;
                this.firstAnswer = userAnswer;
                this.firstAnswerCorrect = isCorrect;
            }
            if (this.hasAnswered)
                this.comp.setYesNo(isCorrect);
        }
    };

    /** This function is called for each question when the question panel is being closed.
    * If a question is unanswered, it will be marked as such.
    */
    Answer.prototype.commitIt = function () {
        this.checkIt(false);
        if (!this.hasAnswered) {
            this.hasAnswered = true;
            this.firstAnswer = "*Unanswered*";
            this.firstAnswerCorrect = false;
        }
    };

    /** Gets the user's first answer to this question.
    * @return The user's answer.
    */
    Answer.prototype.usersAnswer = function () {
        return this.firstAnswer;
    };

    /** Was the user's first answer to this question correct?
    * @return True if the user's first answer to this question was correct
    */
    Answer.prototype.usersAnswerWasCorrect = function () {
        return this.firstAnswerCorrect;
    };

    /** Gets the correct answer as a string.
    * @return The correct answer as a string.
    */
    Answer.prototype.correctAnswer = function () {
        return this.answerString;
    };
    return Answer;
})();
// -*- js -*-
/// <reference path="componentwithyesno.ts" />
/// <reference path="answer.ts" />
function charclass(featset, charset) {
    return featset.foreignText ? charset.foreignClass : featset.transliteratedText ? charset.transliteratedClass : '';
}

var PanelQuestion = (function () {
    //public generateSentenceText() : string {
    // FOR MOODLE
    //}
    /**
    * Constructs a {@code PanelQuestion} that is to be part of a {@link PanelGeneratedQuestionSet}
    * or {@link PanelContinuousQuestions} panel.
    * @param qd The information required to generate a quiz.
    * @param generator True if this is called as part of a question set generation for Moodle
    */
    function PanelQuestion(qd, dict, generator) {
        var _this = this;
        /** The correct answer for each question. */
        this.vAnswers = [];
        this.question_stat = new QuestionStatistics;
        this.qd = qd;
        this.sentence = dict.sentenceSet;

        // We base the location on the first monad in the sentence
        var smo = dict.getSingleMonadObject(getFirst(this.sentence));
        var location_realname = '';
        this.location = ''; // Localized
        for (var unix in configuration.universeHierarchy) {
            var unixi = +unix;
            if (isNaN(unixi))
                continue;

            var uniname = configuration.universeHierarchy[unixi].type;

            switch (unixi) {
                case 0:
                    this.location += l10n.universe[uniname][smo.bcv[unixi]] + ' ';
                    location_realname += smo.bcv[unixi] + ', ';
                    break;
                case 2:
                    this.location += ':';
                    location_realname += ', ';

                case 1:
                    this.location += smo.bcv[unixi];
                    location_realname += smo.bcv[unixi];
                    break;
            }
        }

        $('input#locate_cb').on('click', null, this.location, function (e) {
            if ($(this).prop('checked'))
                $('.location').html(e.data);
            else
                $('.location').html('');
        });

        if ($('#locate_cb').prop('checked'))
            $('.location').html(this.location);

        // Optimisations:
        var dontShow = qd.quizFeatures.dontShow;
        var showFeatures = qd.quizFeatures.showFeatures;
        var requestFeatures = qd.quizFeatures.requestFeatures;
        var oType = qd.quizFeatures.objectType;

        if (generator) {
            // TODO: Quiz generator for Moodle not yet implementd
        } else {
            this.question_stat.text = dict.generateSentenceHtml(qd);
            this.question_stat.location = location_realname;
        }

        var colcount = 0;

        if (dontShow) {
            $('#quiztabhead').append('<th>Item number</th>');
            this.question_stat.show_feat.names.push('item_number');
            ++colcount;
        }

        for (var sfi in showFeatures) {
            if (isNaN(+sfi))
                continue;

            $('#quiztabhead').append('<th>' + getFeatureFriendlyName(oType, showFeatures[sfi]) + '</th>');
            this.question_stat.show_feat.names.push(showFeatures[sfi]);
            ++colcount;
        }

        for (var sfi in requestFeatures) {
            if (isNaN(+sfi))
                continue;

            $('#quiztabhead').append('<th>' + getFeatureFriendlyName(oType, requestFeatures[sfi].name) + '</th>');
            this.question_stat.req_feat.names.push(requestFeatures[sfi].name);
            ++colcount;
        }

        // The requested object type can have these features. This maps feature name to feature type.
        var featuresHere = typeinfo.obj2feat[oType];

        // qoFeatures holds the feature/value pairs for each question object
        var qoFeatures = this.buildQuizObjectFeatureList();

        var hasForeignInput = false;
        var firstInput = 'id="firstinput"';

        for (var qoid in qoFeatures) {
            if (isNaN(+qoid))
                continue;

            var currentRow = $('<tr></tr>');
            var mm = qoFeatures[+qoid];

            if (dontShow) {
                currentRow.append('<td>' + (+qoid + 1) + '</td>');
                this.question_stat.show_feat.values.push("" + (+qoid + 1));
            }

            for (var sfi in showFeatures) {
                if (isNaN(+sfi))
                    continue;

                var sf = showFeatures[+sfi];
                var val = mm[sf];
                var featType = featuresHere[sf];
                var featset = getFeatureSetting(oType, sf);

                this.question_stat.show_feat.values.push(val);

                if (featType == null && sf !== 'visual')
                    alert('Unexpected (1) featType==null in panelquestion.ts; sf="' + sf + '"');

                if (sf === 'visual')
                    featType = 'string';

                if (featType !== 'string' && featType !== 'ascii' && featType !== 'integer') {
                    // This is an enumeration feature type
                    // Replace val with the appropriate friendly name or "Other value"
                    if (featset.otherValues && featset.otherValues.indexOf(val) !== -1)
                        val = localize('other_value');
                    else
                        val = StringWithSort.stripSortIndex(getFeatureValueFriendlyName(featType, val, false));
                }

                if (val == null)
                    alert('Unexpected val==null in panelquestion.ts');

                if ((featType === 'string' || featType == 'ascii'))
                    currentRow.append('<td class="{0}">{1}</td>'.format(charclass(featset, charset), val === '' ? '-' : val));
                else
                    currentRow.append('<td>' + val + '</td>');
            }

            for (var rfi in requestFeatures) {
                if (isNaN(+rfi))
                    continue;

                var rf = requestFeatures[+rfi].name;
                var usedropdown = requestFeatures[+rfi].usedropdown;

                var correctAnswer = mm[rf];
                var v = null;
                if (correctAnswer == null)
                    alert('Unexpected correctAnswer==null in panelquestion.ts');
                if (correctAnswer === '')
                    correctAnswer = '-'; // Indicates empty answer

                if (correctAnswer != null) {
                    var featType = featuresHere[rf];
                    var featset = getFeatureSetting(oType, rf);

                    if (featType == null && rf !== 'visual')
                        alert('Unexpected (2) featType==null in panelquestion.ts');
                    if (rf === 'visual')
                        featType = 'string';

                    if (featset.alternateshowrequestDb != null && usedropdown) {
                        var suggestions = mm[rf + '!suggest!'];
                        if (suggestions == null)
                            v = $('<td class="{0}">{1}</td>'.format(charclass(featset, charset), correctAnswer));
                        else {
                            // This will be a multiple choice question
                            var selectdiv = $('<div class="styled-select"></div>');

                            // direction:ltr forces left alignment of options (though not on Firefox)
                            var jcb = $('<select class="{0}" style="direction:ltr">'.format(charclass(featset, charset)));

                            selectdiv.append(jcb);
                            var optArray = [];
                            var cwyn = new ComponentWithYesNo(selectdiv, 4 /* comboBox2 */);
                            cwyn.addChangeListener();

                            jcb.append('<option value="NoValueGiven"></option>'); // Empty default choice

                            for (var valix in suggestions) {
                                if (isNaN(+valix))
                                    continue;

                                var s = suggestions[+valix];
                                var item = new StringWithSort(s, s);
                                var option = $('<option value="{0}" class="{1}">{2}</option>'.format(item.getInternal(), charclass(featset, charset), item.getString()));
                                option.data('sws', item);
                                optArray.push(option);
                                if (s === correctAnswer)
                                    this.vAnswers.push(new Answer(cwyn, item, s, null));
                            }
                            optArray.sort(function (a, b) {
                                return StringWithSort.compare(a.data('sws'), b.data('sws'));
                            });
                            $.each(optArray, function (ix, o) {
                                return jcb.append(o);
                            });

                            v = cwyn.appendMeTo($('<td></td>'));
                        }
                    } else if (featType === 'string' || featType === 'ascii') {
                        var cwyn;
                        if (featset.foreignText || featset.transliteratedText) {
                            var vf = $('<input {0} data-kbid="{1}" type="text" size="20" class="{2}" onfocus="$(\'#virtualkbid\').appendTo(\'#row{3}\');VirtualKeyboard.attachInput(this)">'.format(firstInput, PanelQuestion.kbid++, charclass(featset, charset), +qoid + 1));
                            firstInput = '';
                            hasForeignInput = true;
                            cwyn = new ComponentWithYesNo(vf, 1 /* textFieldWithVirtKeyboard */);
                        } else {
                            var vf = $('<input type="text" size="20">');
                            cwyn = new ComponentWithYesNo(vf, 0 /* textField */);
                        }
                        cwyn.addKeypressListener();
                        v = cwyn.appendMeTo($('<td></td>'));

                        var trimmedAnswer = correctAnswer.trim().replace(/&lt;/g, '<').replace(/&gt;/g, '>');
                        this.vAnswers.push(new Answer(cwyn, null, trimmedAnswer, featset.matchregexp));
                    } else if (featType === 'integer') {
                        var intf = $('<input type="number">');
                        var cwyn = new ComponentWithYesNo(intf, 0 /* textField */);
                        cwyn.addKeypressListener();
                        v = cwyn.appendMeTo($('<td></td>'));
                        this.vAnswers.push(new Answer(cwyn, null, correctAnswer, null));
                    } else {
                        // This is an enumeration feature type, get the collection of possible values
                        var values = typeinfo.enum2values[featType];
                        if (values == null)
                            v = $('<td>QuestionPanel.UnknType</td>');
                        else {
                            // This will be a multiple choice question
                            var jcb = $('<select></select>');
                            var optArray = [];
                            var cwyn = new ComponentWithYesNo(jcb, 3 /* comboBox1 */);
                            cwyn.addChangeListener();

                            jcb.append('<option value="NoValueGiven"></option>'); // Empty default choice

                            var correctAnswerFriendly = getFeatureValueFriendlyName(featType, correctAnswer, false);

                            var hasAddedOther = false;
                            var correctIsOther = featset.otherValues && featset.otherValues.indexOf(correctAnswer) !== -1;

                            for (var valix in values) {
                                if (isNaN(+valix))
                                    continue;

                                var s = values[+valix];
                                if (featset.hideValues && featset.hideValues.indexOf(s) !== -1)
                                    continue;

                                // TODO: if (Three_ET.dbInfo.isDupFeatureValueFriendlyNameA(featType, s))  - Westminster
                                // TODO:     continue;
                                if (featset.otherValues && featset.otherValues.indexOf(s) !== -1) {
                                    if (!hasAddedOther) {
                                        hasAddedOther = true;
                                        var item = new StringWithSort('#1000 ' + localize('other_value'), 'othervalue');
                                        var option = $('<option value="{0}">{1}</option>'.format(item.getInternal(), item.getString()));
                                        option.data('sws', item);
                                        optArray.push(option);
                                        if (correctIsOther)
                                            this.vAnswers.push(new Answer(cwyn, item, localize('other_value'), null));
                                    }
                                } else {
                                    var sFriendly = getFeatureValueFriendlyName(featType, s, false);
                                    var item = new StringWithSort(sFriendly, s);
                                    var option = $('<option value="{0}">{1}</option>'.format(item.getInternal(), item.getString()));
                                    option.data('sws', item);
                                    optArray.push(option);
                                    if (sFriendly === correctAnswerFriendly)
                                        this.vAnswers.push(new Answer(cwyn, item, s, null));
                                }
                            }
                            optArray.sort(function (a, b) {
                                return StringWithSort.compare(a.data('sws'), b.data('sws'));
                            });
                            $.each(optArray, function (ix, o) {
                                return jcb.append(o);
                            });
                            v = cwyn.appendMeTo($('<td></td>'));
                        }
                    }
                } else {
                    alert('Unexpected correctAnswer==null');
                    v = $('<td>WHAT?</td>'); // TODO: When can this happen?
                }

                currentRow.append(v);
            }
            $('#quiztab').append(currentRow);
            if (hasForeignInput)
                $('#quiztab').append('<tr><td colspan="{0}" id="row{1}" style="text-align:right;"></td></tr>'.format(colcount, +qoid + 1));
        }

        $('#quiztab').width($('#textcontainer').width()); // Initial table width

        // Resize '#quiztab' when main window is resized
        $(window).resize(function () {
            return $('#quiztab').width($('#textcontainer').width());
        });

        // Add "Check answer" button
        $('button#check_answer').off('click'); // Remove old handler
        $('button#check_answer').on('click', function () {
            for (var ai in _this.vAnswers) {
                if (isNaN(+ai))
                    continue;

                var a = _this.vAnswers[+ai];
                a.checkIt(false);
            }
        });

        // Add "Show answer" button
        $('button#show_answer').off('click'); // Remove old handler
        $('button#show_answer').on('click', function () {
            for (var ai in _this.vAnswers) {
                if (isNaN(+ai))
                    continue;

                var a = _this.vAnswers[+ai];
                a.showIt();
                a.checkIt(true);
            }
        });

        this.question_stat.start_time = Math.round((new Date()).getTime() / 1000);
    }
    PanelQuestion.prototype.updateQuestionStat = function () {
        this.question_stat.end_time = Math.round((new Date()).getTime() / 1000);

        this.commitAll();
        for (var i = 0, len = this.vAnswers.length; i < len; ++i) {
            var ans = this.vAnswers[i];
            this.question_stat.req_feat.correct_answer.push(ans.correctAnswer());
            this.question_stat.req_feat.users_answer.push(ans.usersAnswer());
            this.question_stat.req_feat.users_answer_was_correct.push(ans.usersAnswerWasCorrect());
        }
        return this.question_stat;
    };

    /** Gets the question name.
    * @return The question name.
    */
    //public String getQName() {
    //    return m_qName.getText();
    //}
    /** Gets the question title.
    * @return The question title.
    */
    //public String getQTitle() {
    //    return m_qTitle.getText();
    //}
    /** Creates a list of feature=&gt;value maps holding the features for each question object.
    * @return A list of feature/value pairs for each question object.
    */
    PanelQuestion.prototype.buildQuizObjectFeatureList = function () {
        // qoFeatures holds the feature/value pairs for each question object
        var qoFeatures = [];
        var hasSeen = [];

        var allmonads = getMonadArray(this.sentence);
        for (var i = 0, len = allmonads.length; i < len; ++i) {
            var id_d = this.qd.monad2Id[allmonads[i]];
            if (id_d) {
                if (!hasSeen[id_d]) {
                    qoFeatures.push(this.qd.id2FeatVal[id_d]);
                    hasSeen[id_d] = true;
                }
            }
        }
        return qoFeatures;
    };

    /** This function is called when the question panel is being closed.
    * Unanswered questions will be marked as such.
    */
    PanelQuestion.prototype.commitAll = function () {
        for (var i = 0, len = this.vAnswers.length; i < len; ++i)
            this.vAnswers[i].commitIt();
    };
    PanelQuestion.kbid = 1;
    return PanelQuestion;
})();
// -*- js -*-
/* 2013 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk */
/// @file
/// @brief Contains the StringWithSort class.
/// Represents a string with a sort index. Strings with sort indexes are specified in the properties
/// files in as, for example, "#8 Foobar", where 8 is the sort index and "Foobar" is the string. The
/// sort index will be used when ordering objects. Objects of this class can also be constructed from
/// strings without a sort index, in which case comparison reverts to lexical case-free comparison.
/// @see #StringWithSort::compare
var StringWithSort = (function () {
    /// Constructs a StringWithSort object with an internal name.
    /// @param s String with an optional sort index. This string may take the form "#X SSS" where X
    /// is a non-negative integer and SSS is the string proper; in this case X will be used as the
    /// sort index. Alternatively this parameter may be specified as a string without a sort index,
    /// in which case the sort index will be set to -1 and comparison will be lexical.
    /// @param internal The internal (that is, languague independent) name for this value.
    function StringWithSort(s, internal) {
        if (typeof internal === "undefined") { internal = null; }
        if (s.length > 0 && s.charAt(0) === '#') {
            var sp = s.indexOf(' ');
            this.sort = +s.substring(1, sp);
            this.str = s.substring(sp + 1);
        } else {
            this.sort = -1;
            this.str = s;
        }
        this.internal = internal;
    }
    /// Extracts the proper string part of a string with an optional sort index.
    /// @param s String with an optional sort index.
    /// @return The proper string part.
    StringWithSort.stripSortIndex = function (s) {
        return (s.length > 0 && s.charAt(0) === '#') ? s.substring(s.indexOf(' ') + 1) : s;
    };

    /// Gets the internal value.
    /// @return The internal (that is, language independent) value. This may be null.
    StringWithSort.prototype.getInternal = function () {
        return this.internal;
    };

    /// Gets the string proper.
    /// @return The string with the sort index removed.
    StringWithSort.prototype.getString = function () {
        return this.str;
    };

    /// Compares two StringWithSort objects. If the two objects have different sort indexes, the
    /// sort index will be used for ordering. If the two objects have the same sort index or if one
    /// of the sort indexes is -1, lexcial case insensitive ordering will be used.
    /// @param sws1 The first object to be compared.
    /// @param sws2 The second object to be compared.
    /// @return -1, 0, or 1, depending on whether sws1 is less than, equal to, or greater than
    /// sws2.
    StringWithSort.compare = function (sws1, sws2) {
        if (sws1.sort == -1 || sws2.sort == -1 || sws1.sort == sws2.sort) {
            // Use lexical comparison
            var s1 = sws1.str.toLowerCase();
            var s2 = sws2.str.toLowerCase();
            return s1 < s2 ? -1 : s1 > s2 ? 1 : 0;
        } else
            return sws1.sort < sws2.sort ? -1 : 1;
    };
    return StringWithSort;
})();
// -*- js -*-
var ShowFeatStatistics = (function () {
    function ShowFeatStatistics() {
        this.names = [];
        this.values = [];
    }
    return ShowFeatStatistics;
})();

var ReqFeatStatistics = (function () {
    function ReqFeatStatistics() {
        this.names = [];
        this.correct_answer = [];
        this.users_answer = [];
        this.users_answer_was_correct = [];
    }
    return ReqFeatStatistics;
})();

var QuestionStatistics = (function () {
    function QuestionStatistics() {
        this.show_feat = new ShowFeatStatistics();
        this.req_feat = new ReqFeatStatistics();
    }
    return QuestionStatistics;
})();

var QuizStatistics = (function () {
    function QuizStatistics(qid) {
        this.questions = [];
        this.quizid = qid;
    }
    return QuizStatistics;
})();
// -*- js -*-
/* 2013 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk */
/// <reference path="statistics.ts" />

var Quiz = (function () {
    function Quiz(qid) {
        var _this = this;
        this.currentDictIx = -1;
        this.currentPanelQuestion = null;
        this.xx = 8;
        this.quiz_statistics = new QuizStatistics(qid);

        $('#quiztab').append('<tr id="quiztabhead"></tr>');
        $('button#next_question').click(function () {
            return _this.nextQuestion();
        });
        $('button#finish').click(function () {
            return _this.finishQuiz();
        });
    }
    /// Replaces the current quiz question with the next one, if any.
    Quiz.prototype.nextQuestion = function () {
        if (this.currentPanelQuestion !== null)
            // Update statistics
            this.quiz_statistics.questions.push(this.currentPanelQuestion.updateQuestionStat());

        if (++this.currentDictIx < dictionaries.sentenceSets.length) {
            $('#virtualkbid').appendTo('#virtualkbcontainer'); // Move the keyboard back to its initial position
            $('#textarea').empty();
            $('#quiztab').empty();
            $('#quiztab').append('<tr id="quiztabhead"></tr>');

            var currentDict = new Dictionary(dictionaries, this.currentDictIx, true);

            $('#quizdesc').html(quizdata.desc);
            $('#quizdesc').find('a').attr('target', '_blank'); // Force all hyperlinks to open new browser tab
            if (supportsProgress)
                $('progress#progress').attr('value', this.currentDictIx + 1).attr('max', dictionaries.sentenceSets.length);
            else
                $('div#progressbar').progressbar({ value: this.currentDictIx + 1, max: dictionaries.sentenceSets.length });
            $('#progresstext').html((this.currentDictIx + 1) + '/' + dictionaries.sentenceSets.length);

            this.currentPanelQuestion = new PanelQuestion(quizdata, currentDict, false);

            if (this.currentDictIx + 1 === dictionaries.sentenceSets.length)
                $('button#next_question').attr('disabled', 'disabled');

            if (quizdata.quizFeatures.useVirtualKeyboard && (charset.keyboardName === 'IL' || charset.keyboardName === 'GR')) {
                VirtualKeyboard.setVisibleLayoutCodes([charset.keyboardName]);
                VirtualKeyboard.toggle('firstinput', 'virtualkbid');
            }
        } else
            alert('No more questions');

        util.resetCheckboxCounters();
        $('.grammarselector input:enabled:checked').trigger('change'); // Make sure grammar is displayed for relevant checkboxes
    };

    Quiz.prototype.finishQuiz = function () {
        if (quizdata.quizid == -1)
            window.location.replace(site_url + 'text/select_quiz'); // Go to quiz selection
        else {
            if (this.currentPanelQuestion === null)
                alert('System error: No current question panel');
            else
                this.quiz_statistics.questions.push(this.currentPanelQuestion.updateQuestionStat());

            // Send statistics to server
            $('.grammarselector').empty();
            $('#textcontainer').html('<p>' + localize('sending_statistics') + '</p>');

            $.post(site_url + 'statistics/update_stat', this.quiz_statistics).done(function () {
                return window.location.replace(site_url + 'text/select_quiz');
            }).fail(function (jqXHR, textStatus, errorThrow) {
                $('#textcontainer').html('<div class="error"><h1>' + localize('error_response') + '</h1><p>{0}</p></div>'.format(errorThrow));
            });
        }
    };
    return Quiz;
})();
// -*- js -*-
/* 2013 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk */
/// <reference path="jquery/jquery.d.ts" />
/// <reference path="jqueryui/jqueryui.d.ts" />
/// <reference path="util.ts" />
/// <reference path="configuration.ts" />
/// <reference path="sentencegrammar.ts" />
/// <reference path="charset.ts" />
/// <reference path="monadobject.ts" />
/// <reference path="displaymonadobject.ts" />
/// <reference path="localization.ts" />
/// <reference path="localization_general.ts" />
/// <reference path="quizdata.ts" />
/// <reference path="dictionary.ts" />
/// <reference path="panelquestion.ts" />
/// <reference path="stringwithsort.ts" />
/// <reference path="quiz.ts" />

var supportsProgress;

var charset;

var quiz;

/// Ensures that the width of a &lt;span class="levX"&gt; is at least as wide as the &lt;span
/// class="gram"&gt; holding its grammar information.
/// @param[in] level Object level (word=0, phrase=1, etc.)
function adjustDivLevWidth(level) {
    $('.lev' + level).each(function (index) {
        $(this).css('width', 'auto'); // Give div natural width

        var w = $(this).find('> .gram').width();
        if ($(this).width() < w)
            $(this).width(w); // Set width of div to width of information
    });
}

// Creates HTML for checkboxes that select what grammar to display
var GenerateCheckboxes = (function () {
    function GenerateCheckboxes() {
        this.checkboxes = '';
        this.addBr = new util.AddBetween('<br>');
    }
    GenerateCheckboxes.prototype.generatorCallback = function (whattype, objType, featName, featNameLoc, sgiObj) {
        switch (whattype) {
            case 2 /* groupstart */:
                if (!this.hasSeenGrammarGroup) {
                    this.hasSeenGrammarGroup = true;
                    this.checkboxes += '<div class="subgrammargroup">';
                }
                this.checkboxes += '<div class="grammargroup"><h2>{0}</h2><div>'.format(featNameLoc);
                this.addBr.reset();
                break;

            case 3 /* groupend */:
                this.checkboxes += '</div></div>';
                break;

            case 0 /* feature */:
            case 1 /* metafeature */:
                if (mayShowFeature(objType, featName, sgiObj)) {
                    this.checkboxes += '{0}<input id="{1}_{2}_cb" type="checkbox">{3}'.format(this.addBr.getStr(), objType, featName, featNameLoc);

                    var wordclass;
                    if (whattype === 0 /* feature */ && getFeatureSetting(objType, featName).foreignText)
                        wordclass = charset.foreignClass;
                    else if (whattype === 0 /* feature */ && getFeatureSetting(objType, featName).transliteratedText)
                        wordclass = charset.transliteratedClass;
                    else
                        wordclass = 'latin';
                } else
                    this.checkboxes += '{0}<input id="{1}_{2}_cb" type="checkbox" disabled>{3}'.format(this.addBr.getStr(), objType, featName, featNameLoc);
                break;
        }
    };

    /// Creates checkboxes related to objects (word, phrase, clause, etc.).
    /// @param level Object level (word=0, phrase=1, etc.)
    /// @return HTML for creating a checkbox
    GenerateCheckboxes.prototype.makeCheckBoxForObj = function (level) {
        if (level == 0) {
            // Object is word
            if (charset.isHebrew)
                return '{0}<input id="ws_cb" type="checkbox">{1}</span>'.format(this.addBr.getStr(), localize('word_spacing'));
            else
                return '';
        } else
            return '{0}<input id="lev{1}_seplin_cb" type="checkbox">{2}</span><br><input id="lev{1}_sb_cb" type="checkbox">{3}</span>'.format(this.addBr.getStr(), level, localize('separate_lines'), localize('show_border'));
    };

    GenerateCheckboxes.prototype.generateHtml = function () {
        var _this = this;
        for (var level in configuration.sentencegrammar) {
            var leveli = +level;
            if (isNaN(leveli))
                continue;

            var objType = configuration.sentencegrammar[leveli].objType;

            this.addBr.reset();

            this.checkboxes += '<div class="objectlevel"><h1>' + getObjectFriendlyName(objType) + '</h1><div>';
            this.checkboxes += this.makeCheckBoxForObj(leveli);

            /// @todo This works if only one &lt;div class="objectlevel"&gt; has any &lt;div class="grammargroup"&gt; children
            /// and the grammargroups are not intermixed with grammarfeatures
            this.hasSeenGrammarGroup = false;

            configuration.sentencegrammar[leveli].getFeatName(configuration.sentencegrammar[leveli].objType, function (whattype, objType, featName, featNameLoc, sgiObj) {
                return _this.generatorCallback(whattype, objType, featName, featNameLoc, sgiObj);
            });

            if (this.hasSeenGrammarGroup)
                this.checkboxes += '</div>';

            this.checkboxes += '</div></div>';
        }

        return this.checkboxes;
    };

    GenerateCheckboxes.prototype.setHandlerCallback = function (whattype, objType, featName, featNameLoc, leveli) {
        if (whattype != 0 /* feature */ && whattype != 1 /* metafeature */)
            return;

        if (leveli === 0) {
            // Handling of words
            $('#{0}_{1}_cb'.format(objType, featName)).change(function () {
                if ($(this).prop('checked')) {
                    $('.wordgrammar.{0}'.format(featName)).removeClass('dontshowit').addClass('showit');
                    util.forceWide(true);
                    util.forceWordSpace(true);
                } else {
                    $('.wordgrammar.{0}'.format(featName)).removeClass('showit').addClass('dontshowit');
                    util.forceWide(false);
                    util.forceWordSpace(false);
                }

                for (var lev = 1; lev < configuration.maxLevels - 1; ++lev)
                    adjustDivLevWidth(lev);
            });
        } else {
            // Handling of clause, phrase, etc.
            $('#{0}_{1}_cb'.format(objType, featName)).change(function () {
                if ($(this).prop('checked')) {
                    $('.xgrammar.{0}_{1}'.format(objType, featName)).removeClass('dontshowit').addClass('showit');
                    util.forceBorder(true, leveli);
                } else {
                    $('.xgrammar.{0}_{1}'.format(objType, featName)).removeClass('showit').addClass('dontshowit');
                    util.forceBorder(false, leveli);
                }

                adjustDivLevWidth(leveli);
            });
        }
    };

    // Set up handling of checkboxes
    GenerateCheckboxes.prototype.setHandlers = function () {
        var _this = this;
        for (var level in configuration.sentencegrammar) {
            var leveli = +level;
            if (isNaN(leveli))
                continue;

            var sg = configuration.sentencegrammar[leveli];

            if (leveli === 0) {
                if (charset.isHebrew) {
                    $('#ws_cb').change(function () {
                        util.explicitWordSpace($(this).prop('checked'));

                        for (var lev = 1; lev < configuration.maxLevels - 1; ++lev)
                            adjustDivLevWidth(lev);
                    });
                }
            } else {
                $('#lev{0}_seplin_cb'.format(leveli)).change(leveli, function (e) {
                    util.separateLines($(e.currentTarget).prop('checked'), e.data);
                    adjustDivLevWidth(e.data);
                });
                $('#lev{0}_sb_cb'.format(leveli)).change(leveli, function (e) {
                    util.explicitBorder($(e.currentTarget).prop('checked'), e.data);
                    adjustDivLevWidth(e.data);
                });
            }

            sg.getFeatName(sg.objType, function (whattype, objType, featName, featNameLoc) {
                return _this.setHandlerCallback(whattype, objType, featName, featNameLoc, leveli);
            });
        }
    };

    GenerateCheckboxes.prototype.clearBoxes = function () {
        $('input[type="checkbox"]').prop('checked', false);
    };
    return GenerateCheckboxes;
})();

// Build accordion for grammar selector.
// Returns its width
function buildGrammarAccordion() {
    var acc1 = $('#gramselect').accordion({ heightStyle: 'content', collapsible: true, header: 'h1' });
    var acc2 = $('.subgrammargroup').accordion({ heightStyle: 'content', collapsible: true, header: 'h2' });

    /// @todo Does this work if there are multiple '.subgrammargroup' divs?
    var max_width = 0;
    for (var j = 0; j < acc2.find('h2').length; ++j) {
        acc2.accordion('option', 'active', j);
        if (acc2.width() > max_width)
            max_width = acc2.width();
    }
    acc2.accordion('option', 'active', false); // No active item
    acc2.width(max_width * 1.05); // I don't know why I have to add 5% here

    max_width = 0;
    for (var j = 0; j < acc1.find('h1').length; ++j) {
        acc1.accordion('option', 'active', j);
        if (acc1.width() > max_width)
            max_width = acc1.width();
    }
    acc1.accordion('option', 'active', false);
    acc1.width(max_width);

    return max_width;
}

/// Main code executed when the page has been loaded.
$(function () {
    // Does the browser support <progress>?
    // (Use two statements because jquery.d.ts does not recognize .max)
    var x = document.createElement('progress');
    supportsProgress = x.max != undefined; // Thanks to http://lab.festiz.com/progressbartest/index.htm

    configuration.maxLevels = configuration.sentencegrammar.length + 1; // Include patriarch level

    // Set up CSS classes for text.
    charset = new Charset(configuration.charSet);
    $('#textarea').addClass(charset.isRtl ? 'rtl' : 'ltr');

    for (var i in configuration.sentencegrammar) {
        if (isNaN(+i))
            continue;
        addMethodsSgi(configuration.sentencegrammar[+i]);
    }

    // Create HTML for checkboxes that select what grammar to display
    var generateCheckboxes = new GenerateCheckboxes();
    $('#gramselect').append(generateCheckboxes.generateHtml());
    generateCheckboxes.setHandlers();
    generateCheckboxes.clearBoxes();

    var accordion_width = buildGrammarAccordion();

    $('#textcontainer').css('margin-left', accordion_width + 10);
    if (useTooltip)
        $('#textcontainer').css('margin-right', 0);
    else
        $('#textcontainer').css('margin-right', $('.grammardisplay').width() + 10);

    var inQuiz = $('#quiztab').length > 0;
    if (inQuiz) {
        if (supportsProgress)
            $('div#progressbar').hide();
        else
            $('progress#progress').hide();

        quiz = new Quiz(quizdata.quizid);
        quiz.nextQuestion();
    } else {
        // Display text
        var currentDict = new Dictionary(dictionaries, 0, false);
        currentDict.generateSentenceHtml(null);
    }
});
