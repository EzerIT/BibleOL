var __extends = (this && this.__extends) || (function () {
    var extendStatics = Object.setPrototypeOf ||
        ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
        function (d, b) { for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p]; };
    return function (d, b) {
        extendStatics(d, b);
        function __() { this.constructor = d; }
        d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
    };
})();
// -*- js -*-
/* 2013 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk */
String.prototype.format = function () {
    var args = arguments;
    return this.replace(/{(\d+)}/g, function (match, num) {
        return typeof args[num] != 'undefined'
            ? args[num]
            : match;
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
    // A followerBox handles a checkbox that can either be set explicitly or implicitly. "Show border"
    // is an example of this. The user case request "Show border" explicitly by clicking its checkbox,
    // or implictly by choosing to display a feature.
    var FollowerBox = /** @class */ (function () {
        function FollowerBox(level, idstring) {
            this.level = level;
            this.idstring = idstring;
            this.is_explicit = false;
            this.resetCount();
            addToResetChain(this);
        }
        FollowerBox.prototype.resetCount = function () {
            this.count = 0;
        };
        FollowerBox.prototype.explicit = function (val) {
            this.is_explicit = val;
            this.setit(val);
        };
        FollowerBox.prototype.implicit = function (val) {
            if (val)
                ++this.count;
            else
                --this.count;
            if (val && this.count == 1) {
                $(this.idstring).prop('disabled', true);
                $(this.idstring).prop('checked', true);
                this.setit(true);
            }
            else if (!val && this.count == 0) {
                $(this.idstring).prop('disabled', false);
                $(this.idstring).prop('checked', this.is_explicit);
                this.setit(this.is_explicit);
            }
        };
        return FollowerBox;
    }());
    util.FollowerBox = FollowerBox;
    var BorderFollowerBox = /** @class */ (function (_super) {
        __extends(BorderFollowerBox, _super);
        function BorderFollowerBox(level) {
            return _super.call(this, level, '#lev{0}_sb_cb'.format(level)) || this;
        }
        BorderFollowerBox.prototype.setit = function (val) {
            if (val) {
                $('.lev' + this.level + '> .gram').removeClass('dontshowit').addClass('showit');
                $('.lev' + this.level).removeClass('dontshowborder').addClass('showborder');
            }
            else {
                $('.lev' + this.level + '> .gram').removeClass('showit').addClass('dontshowit');
                $('.lev' + this.level).removeClass('showborder').addClass('dontshowborder');
            }
        };
        return BorderFollowerBox;
    }(FollowerBox));
    util.BorderFollowerBox = BorderFollowerBox;
    var SeparateLinesFollowerBox = /** @class */ (function (_super) {
        __extends(SeparateLinesFollowerBox, _super);
        function SeparateLinesFollowerBox(level) {
            return _super.call(this, level, '#lev{0}_seplin_cb'.format(level)) || this;
        }
        SeparateLinesFollowerBox.prototype.setit = function (val) {
            var oldSepLin = val ? 'noseplin' : 'seplin';
            var newSepLin = val ? 'seplin' : 'noseplin';
            $('.notdummy.lev' + this.level).removeClass(oldSepLin).addClass(newSepLin);
        };
        return SeparateLinesFollowerBox;
    }(FollowerBox));
    util.SeparateLinesFollowerBox = SeparateLinesFollowerBox;
    var WordSpaceFollowerBox = /** @class */ (function (_super) {
        __extends(WordSpaceFollowerBox, _super);
        function WordSpaceFollowerBox(level) {
            return _super.call(this, level, '#ws_cb') || this;
        }
        WordSpaceFollowerBox.prototype.implicit = function (val) {
            _super.prototype.implicit.call(this, val);
            if (val && this.count == 1) {
                $('.textblock').css('margin-left', '30px').removeClass('inline').addClass('inlineblock');
            }
            else if (!val && this.count == 0) {
                $('.textblock').css('margin-left', '0').removeClass('inlineblock').addClass('inline');
                ;
            }
        };
        WordSpaceFollowerBox.prototype.setit = function (val) {
            if (val) {
                $('.cont').removeClass('cont1');
                $('.cont').addClass('cont2');
                $('.contx').removeClass('cont1');
                $('.contx').addClass('cont2x');
            }
            else {
                $('.cont').removeClass('cont2');
                $('.cont').addClass('cont1');
                $('.contx').removeClass('cont2x');
                $('.contx').addClass('cont1');
            }
        };
        return WordSpaceFollowerBox;
    }(FollowerBox));
    util.WordSpaceFollowerBox = WordSpaceFollowerBox;
    function mydump(arr, level, maxlevel) {
        if (level === void 0) { level = 0; }
        if (maxlevel === void 0) { maxlevel = 5; }
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
                }
                else {
                    dumped_text += level_padding + "'" + item + "' => \"" + value + "\"\n";
                }
            }
        }
        else {
            dumped_text = "===>" + arr + "<===(" + typeof (arr) + ")";
        }
        return dumped_text;
    }
    util.mydump = mydump;
    var resetChain = [];
    function addToResetChain(fb) {
        resetChain.push(fb);
    }
    function resetCheckboxCounters() {
        for (var i in resetChain) {
            if (isNaN(+i))
                continue; // Not numeric
            resetChain[i].resetCount();
        }
    }
    util.resetCheckboxCounters = resetCheckboxCounters;
    var AddBetween = /** @class */ (function () {
        function AddBetween(text) {
            this.text = text;
            this.first = true;
        }
        AddBetween.prototype.getStr = function () {
            if (this.first) {
                this.first = false;
                return '';
            }
            else
                return this.text;
        };
        AddBetween.prototype.reset = function () {
            this.first = true;
        };
        return AddBetween;
    }());
    util.AddBetween = AddBetween;
})(util || (util = {}));
// -*- js -*-
function getObjectSetting(otype) {
    return configuration.objectSettings[otype];
}
function getFeatureSetting(otype, feature) {
    // Handle the pseudo-feature
    if (feature === 'visual') {
        otype = configuration.objHasSurface;
        feature = configuration.surfaceFeature;
    }
    var io = feature.indexOf('_TYPE_'); // Separates feature from format
    if (io != -1)
        // This is a feature with a special format (which is not used here)
        return getObjectSetting(otype).featuresetting[feature.substr(0, io)];
    else
        return getObjectSetting(otype).featuresetting[feature];
}
// -*- js -*-
// Copyright © 2018 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk */
// The code here handles walking through the "sentencegrammar" part of the configuration object.
//
// Before using the classes in this file, the function addMethodsSgi() must called to enhace the
// configuration object with polymorhic functions, turning the contents of "sentencegrammar" into
// objects of various types.
//
// For example, addMethodsSgi() will turn this part of the configuration object:
//     {
//         "mytype": "GrammarFeature",
//         "name": "g_lex_utf8"
//     }
// into an object of class GrammarFeature with appropriate polymorphic functions added (such as
// walkFeatureNames, walkFeatureValues etc.)
//****************************************************************************************************
// addMethods function
//
// Takes method from a class and adds them to an object. Thereafter calls the method
// pseudoConstructor() if it exists.
//
// Parameters:
//     obj: The object to which methods should be added.
//     classname: The class from which methods should be taken.
//     param: The argument to pseudoConstructor().
//
function addMethods(obj, classname, param) {
    // Copy all methods except constructor
    for (var f in classname.prototype) {
        if (f === 'constructor')
            continue;
        obj[f] = classname.prototype[f];
    }
    obj.pseudoConstructor && obj.pseudoConstructor(param); // Call pseudoConstructor if it exists
}
//****************************************************************************************************
// addMethodsSgi function
//
// Recursively enhances a part of the configuration object to be a polymorphic object of the class
// specified by the mytype field of the configuration object.
//
// Parameters:
//     sgi: The part of the configuration object that is to be enhanced.
//     param: The argument to pseudoConstructor() for the enhanced classes.
//
function addMethodsSgi(sgi, param) {
    addMethods(sgi, eval(sgi.mytype), param); // sgi.mytype is the name of the subclass
    // Do the same with all members of the items array
    if (sgi.items) {
        for (var i in sgi.items) {
            if (isNaN(+i))
                continue; // Not numeric
            addMethodsSgi(sgi.items[+i], param);
        }
    }
}
// The WHAT enum is used to identify various stages when walking through a configuration object.
var WHAT;
(function (WHAT) {
    WHAT[WHAT["feature"] = 0] = "feature";
    WHAT[WHAT["metafeature"] = 1] = "metafeature";
    WHAT[WHAT["groupstart"] = 2] = "groupstart";
    WHAT[WHAT["groupend"] = 3] = "groupend";
})(WHAT || (WHAT = {}));
//****************************************************************************************************
// GrammarGroup class
//
// A GrammarGroup groups GrammarFeatures and GrammarMetaFeatures into logical units, such as
// "Features that describe the lexeme" or "Features that describe the morphology".
//
var GrammarGroup = /** @class */ (function () {
    function GrammarGroup() {
    }
    //------------------------------------------------------------------------------------------
    // walkFeatureNames method
    //
    // See description under SentenceGrammarItem
    //
    GrammarGroup.prototype.walkFeatureNames = function (objType, callback) {
        callback(WHAT.groupstart, objType, objType, this.name, l10n.grammargroup[objType][this.name], this);
        for (var i in this.items) {
            if (isNaN(+i))
                continue; // Not numeric
            this.items[+i].walkFeatureNames(objType, callback);
        }
        callback(WHAT.groupend, objType, objType, this.name, null, this);
    };
    //------------------------------------------------------------------------------------------
    // walkFeatureValues method
    //
    // See description under SentenceGrammarItem
    //
    GrammarGroup.prototype.walkFeatureValues = function (monob, mix, objType, abbrev, callback) {
        callback(WHAT.groupstart, objType, objType, this.name, null, this);
        for (var i in this.items) {
            if (isNaN(+i))
                continue; // Not numeric
            this.items[+i].walkFeatureValues(monob, mix, objType, abbrev, callback);
        }
        callback(WHAT.groupend, objType, objType, this.name, null, this);
    };
    //------------------------------------------------------------------------------------------
    // containsFeature method
    //
    // See description under SentenceGrammarItem
    //
    GrammarGroup.prototype.containsFeature = function (f) {
        for (var i in this.items) {
            if (isNaN(+i))
                continue; // Not numeric
            if (this.items[+i].containsFeature(f))
                return true;
        }
        return false;
    };
    return GrammarGroup;
}());
//****************************************************************************************************
// GrammarSubFeature class
//
// A GrammarSubFeature is a member of a GrammarMetaFeature.
//
var GrammarSubFeature = /** @class */ (function () {
    function GrammarSubFeature() {
    }
    //------------------------------------------------------------------------------------------
    // getFeatValPart method
    //
    // Retrieve the localized feature value.
    //
    // Parameters:
    //     monob: The MonadObject containing the value we are retrieving.
    //     objType: The type of the grammar object (word, phrase, etc.)
    //
    GrammarSubFeature.prototype.getFeatValPart = function (monob, objType) {
        return l10n.grammarsubfeature[objType][this.name][monob.mo.features[this.name]];
    };
    //------------------------------------------------------------------------------------------
    // containsFeature method
    //
    // See description under SentenceGrammarItem
    //
    GrammarSubFeature.prototype.containsFeature = function (f) {
        return this.name === f;
    };
    return GrammarSubFeature;
}());
//****************************************************************************************************
// SentenceGrammar class
//
// A SentenceGrammar groups GrammarGroups, GrammarFeatures and GrammarMetaFeatures for a single
// Emdros object type, such as word or phrase.
//
//
var SentenceGrammar = /** @class */ (function (_super) {
    __extends(SentenceGrammar, _super);
    function SentenceGrammar() {
        return _super !== null && _super.apply(this, arguments) || this;
    }
    //------------------------------------------------------------------------------------------
    // walkFeatureNames method
    //
    // See description under SentenceGrammarItem
    //
    SentenceGrammar.prototype.walkFeatureNames = function (objType, callback) {
        for (var i in this.items) {
            if (isNaN(+i))
                continue; // Not numeric
            this.items[+i].walkFeatureNames(objType, callback);
        }
    };
    //------------------------------------------------------------------------------------------
    // walkFeatureValues method
    //
    // See description under SentenceGrammarItem
    //
    SentenceGrammar.prototype.walkFeatureValues = function (monob, mix, objType, abbrev, callback) {
        for (var i in this.items) {
            if (isNaN(+i))
                continue; // Not numeric
            this.items[+i].walkFeatureValues(monob, mix, objType, abbrev, callback);
        }
    };
    //------------------------------------------------------------------------------------------
    // containsFeature method
    //
    // See description under SentenceGrammarItem
    //
    SentenceGrammar.prototype.containsFeature = function (f) {
        for (var i in this.items) {
            if (isNaN(+i))
                continue; // Not numeric
            if (this.items[+i].containsFeature(f))
                return true;
        }
        return false;
    };
    return SentenceGrammar;
}(GrammarGroup));
//****************************************************************************************************
// GrammarMetaFeature class
//
// A GrammarMetaFeature describes a combined value of a number of Emdros features (for example,
// person, gender, and number).
//
var GrammarMetaFeature = /** @class */ (function () {
    function GrammarMetaFeature() {
    }
    //------------------------------------------------------------------------------------------
    // walkFeatureNames method
    //
    // See description under SentenceGrammarItem
    //
    GrammarMetaFeature.prototype.walkFeatureNames = function (objType, callback) {
        callback(WHAT.metafeature, objType, objType, this.name, l10n.grammarmetafeature[objType][this.name], this);
    };
    //------------------------------------------------------------------------------------------
    // walkFeatureValues method
    //
    // See description under SentenceGrammarItem
    //
    GrammarMetaFeature.prototype.walkFeatureValues = function (monob, mix, objType, abbrev, callback) {
        var res = '';
        for (var i in this.items) {
            if (isNaN(+i))
                continue; // Not numeric
            res += this.items[+i].getFeatValPart(monob, objType);
        }
        callback(WHAT.metafeature, objType, objType, this.name, res, this);
    };
    //------------------------------------------------------------------------------------------
    // containsFeature method
    //
    // See description under SentenceGrammarItem
    //
    GrammarMetaFeature.prototype.containsFeature = function (f) {
        for (var i in this.items) {
            if (isNaN(+i))
                continue; // Not numeric
            if (this.items[+i].containsFeature(f))
                return true;
        }
        return false;
    };
    return GrammarMetaFeature;
}());
//****************************************************************************************************
// GrammarFeature class
//
// A GrammarFeature describes a feature of an Emdros object.
//
var GrammarFeature = /** @class */ (function () {
    function GrammarFeature() {
    }
    //------------------------------------------------------------------------------------------
    // pseudoConstructor method
    //
    // Sets information about whether this is a subobject feature (see above) or not.
    //
    GrammarFeature.prototype.pseudoConstructor = function (objType) {
        var io = this.name.indexOf(':');
        if (io != -1) {
            // This is a feature of a sub-object
            this.isSubObj = true;
            this.realObjectType = this.name.substr(0, io);
            this.realFeatureName = this.name.substr(io + 1);
        }
        else {
            this.isSubObj = false;
            this.realObjectType = objType;
            this.realFeatureName = this.name;
        }
    };
    //------------------------------------------------------------------------------------------
    // walkFeatureNames method
    //
    // See description under SentenceGrammarItem.
    //
    GrammarFeature.prototype.walkFeatureNames = function (objType, callback) {
        // Normally localized feature names are found in l10n.emdrosobject, but occasionally special
        // translations exists that are to be used in the grammal selection box. These special
        // translations are found in l10n.grammarfeature.
        var locname = l10n.grammarfeature && l10n.grammarfeature[this.realObjectType] && l10n.grammarfeature[this.realObjectType][this.realFeatureName]
            ? l10n.grammarfeature[this.realObjectType][this.realFeatureName]
            : l10n.emdrosobject[this.realObjectType][this.realFeatureName];
        callback(WHAT.feature, this.realObjectType, objType, this.realFeatureName, locname, this);
    };
    //------------------------------------------------------------------------------------------
    // icon2class method
    //
    // Converts the name of an icon to the required HTML element classes.
    //
    // Parameter:
    //     icon: The name of the icon.
    // Returns:
    //     The HTML element class string.
    //
    GrammarFeature.prototype.icon2class = function (icon) {
        if (icon.substr(0, 10) === 'glyphicon-')
            return 'glyphicon ' + icon;
        if (icon.substr(0, 8) === 'bolicon-')
            return 'bolicon ' + icon;
        return '';
    };
    //------------------------------------------------------------------------------------------
    // walkFeatureValues method
    //
    // See description under SentenceGrammarItem.
    //
    GrammarFeature.prototype.walkFeatureValues = function (monob, mix, objType, abbrev, callback) {
        var featType = typeinfo.obj2feat[this.realObjectType][this.realFeatureName];
        // Normally featType will contain the type of the feature. However, the feature name can
        // contain the string _TYPE_, in which case the an alternative type is used.
        // The variable realRealFeature name is set to the part of the feature name that comes before _TYPE_.
        var io = this.realFeatureName.indexOf('_TYPE_'); // Separates feature from format
        var realRealFeatureName = io == -1 ? this.realFeatureName : this.realFeatureName.substr(0, io);
        var res = this.isSubObj
            ? monob.subobjects[mix][0].features[realRealFeatureName]
            : (monob.mo.features ? monob.mo.features[realRealFeatureName] : ''); // Empty for dummy objects
        switch (featType) {
            case 'string':
            case 'ascii':
                if (res === '')
                    res = '-';
                break;
            case 'url':
                if (res.length == 0)
                    res = '-';
                else {
                    // Assume res is an array, where each element is an array of two elements
                    var res2 = '';
                    for (var i = 0; i < res.length; ++i)
                        res2 += "<a style=\"padding-right:1px;padding-left:1px;\" href=\"" + res[i]['url'] + "\" target=\"_blank\">"
                            + ("<span class=\"" + this.icon2class(res[i]['icon']) + "\" aria-hidden=\"true\"></span></a>");
                    res = res2;
                }
                break;
            case 'integer':
                break;
            default:
                if (io == -1) {
                    if (res !== '')
                        res = getFeatureValueFriendlyName(featType, res, abbrev, true);
                }
                else {
                    res = getFeatureValueOtherFormat(this.realObjectType, this.realFeatureName, +res);
                }
                break;
        }
        callback(WHAT.feature, this.realObjectType, objType, this.realFeatureName, res, this);
    };
    //------------------------------------------------------------------------------------------
    // containsFeature method
    //
    // See description under SentenceGrammarItem.
    //
    GrammarFeature.prototype.containsFeature = function (f) {
        return this.name === f;
    };
    return GrammarFeature;
}());
//****************************************************************************************************
// getSentenceGrammarFor function
//
// Retrieves from the configuraion structure the SentenceGrammar object that describes a particular
// Emdros object type.
//
// Parameter:
//     oType: The name of the Emdros object.
// Returns:
//     The corresponding SentenceGrammar object.
//
function getSentenceGrammarFor(oType) {
    for (var i = 0; i < configuration.sentencegrammar.length; ++i)
        if (configuration.sentencegrammar[i].objType === oType)
            return configuration.sentencegrammar[i];
    return null;
}
// -*- js -*-
// Copyright © 2018 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk
//****************************************************************************************************
// About the IDs of HTML elements in the grammar selection box:
//
// The grammar selection box has the id "gramselect".
//
// A checkbox for Emdros object OOO feature FFF has the id "OOO_FFF_cb".
//
// The checkbox for "word spacing" (Hebrew only) has the id "ws_cb".
//
// A checkbox for "separate lines" for an Emdros object at level LLL (where, for example, 1 means
// phrase, 2 means clause, etc.) has the id "levLLL_seplin_cb".
//
// A checkbox for "show borders" for an Emdros object at level LLL (where, for example, 1 means
// phrase, 2 means clause, etc.) has the id "levLLL_sb_cb".
//
// The "Clear grammar" button has the id "cleargrammar".
//
//****************************************************************************************************
//****************************************************************************************************
// GrammarSelectionBox class
//
// This singleton class creates HTML for the contents of the grammar selection box.
//
var GrammarSelectionBox = /** @class */ (function () {
    function GrammarSelectionBox() {
        this.checkboxes = ''; // Holds the generated HTML
        this.addBr = new util.AddBetween('<br>'); // AddBetween object to insert <br>
        this.borderBoxes = []; // Handles checkbox for "show borders"
        this.separateLinesBoxes = []; // Handles checkbox for "separate lines"
    }
    //****************************************************************************************************
    // adjustDivLevWidth method
    //
    // Ensures that the width of a <span class="levX"> is at least as wide as the <span
    // class="gram"> holding its grammar information.
    //
    // Parameter:
    //    level Object level (word=0, phrase=1, etc.)
    //
    GrammarSelectionBox.adjustDivLevWidth = function (level) {
        $(".showborder.lev" + level).each(function (index) {
            $(this).css('width', 'auto'); // Give div natural width
            var w = $(this).find('> .gram').width();
            if ($(this).width() < w)
                $(this).width(w); // Set width of div to width of information
        });
    };
    //------------------------------------------------------------------------------------------
    // generatorCallback method
    //
    // This function is called repeatedly from the walkFeatureNames() method of the
    // SentenceGrammarItem interface. It generates HTML code that goes into the grammar selection
    // box. The HTML code is appended to this.checkboxes.
    //
    // Parameters:
    //    whattype: Identification of the current point in the walkthrough.
    //    objType: The type of the current grammar object.
    //    origObjType: The original type of the current grammar object. (This can be different from
    //                 objType when, for example, a feature under "clause" has the name "clause_atom:tab".)
    //    featName: The name of the feature.
    //    featNameLoc: Localized feature name.
    //    sgiObj: The current SentenceGrammarItem object (always 'this').
    //
    GrammarSelectionBox.prototype.generatorCallback = function (whattype, objType, origObjType, featName, featNameLoc, sgiObj) {
        switch (whattype) {
            case WHAT.groupstart:
                if (!this.hasSeenGrammarGroup) {
                    this.hasSeenGrammarGroup = true;
                    this.checkboxes += '<div class="subgrammargroup">';
                }
                this.checkboxes += "<div class=\"grammargroup\"><h2>" + featNameLoc + "</h2><div>";
                this.addBr.reset();
                break;
            case WHAT.groupend:
                this.checkboxes += '</div></div>';
                break;
            case WHAT.feature:
            case WHAT.metafeature:
                var disabled = mayShowFeature(objType, origObjType, featName, sgiObj) ? '' : 'disabled';
                this.checkboxes += this.addBr.getStr() + "<input id=\"" + objType + "_" + featName + "_cb\" type=\"checkbox\" " + disabled + ">" + featNameLoc;
                break;
        }
    };
    //------------------------------------------------------------------------------------------
    // makeInitCheckBoxForObj method
    //
    // Creates initial checkboxes for features related to objects (word, phrase, clause, etc.).
    // The initial checkboxes are
    //     for words: Word spacing (Hebrew only)
    //     for other objects: Separate lines, show border.
    //
    // Parameter:
    //     level: Object level (word=0, phrase=1, etc.)
    // Returns HTML for creating a checkbox.
    //
    GrammarSelectionBox.prototype.makeInitCheckBoxForObj = function (level) {
        if (level == 0) {
            // Object is word
            if (charset.isHebrew) {
                return this.addBr.getStr()
                    + ("<input id=\"ws_cb\" type=\"checkbox\">" + localize('word_spacing') + "</span>");
            }
            else
                return '';
        }
        else {
            // Object is phrase, clause etc.
            return this.addBr.getStr()
                + ("<input id=\"lev" + level + "_seplin_cb\" type=\"checkbox\">" + localize('separate_lines') + "</span>")
                + '<br>'
                + ("<input id=\"lev" + level + "_sb_cb\" type=\"checkbox\">" + localize('show_border') + "</span>");
        }
    };
    //------------------------------------------------------------------------------------------
    // generateHtml method
    //
    // This is the main method of the class. It generates the HTML that displays the contents of the
    // grammar selection box.
    //
    // Returns:
    //     HTML code.
    //
    GrammarSelectionBox.prototype.generateHtml = function () {
        var _this = this;
        // Loop through 'word', 'phrase', 'clause', 'sentence' or the like
        for (var level in configuration.sentencegrammar) {
            var leveli = +level;
            if (isNaN(leveli))
                continue; // Not numeric
            var objType = configuration.sentencegrammar[leveli].objType; // objType is 'word', 'phrase' etc.
            this.addBr.reset();
            this.checkboxes += "<div class=\"objectlevel\"><h1>" + getObjectFriendlyName(objType) + "</h1><div>";
            this.checkboxes += this.makeInitCheckBoxForObj(leveli);
            /// TO DO: This only works if the grammargroups are not intermixed with grammarfeatures.
            this.hasSeenGrammarGroup = false;
            configuration.sentencegrammar[leveli]
                .walkFeatureNames(objType, function (whattype, objType, origObjType, featName, featNameLoc, sgiObj) { return _this.generatorCallback(whattype, objType, origObjType, featName, featNameLoc, sgiObj); });
            if (this.hasSeenGrammarGroup)
                this.checkboxes += '</div>';
            this.checkboxes += '</div></div>';
        }
        return this.checkboxes;
    };
    //------------------------------------------------------------------------------------------
    // setHandlerCallback method
    //
    // This function is called repeatedly from the walkFeatureNames() method of the
    // SentenceGrammarItem interface. It sets up event handlers for checking/unchecking of
    // checkboxes in the grammar selection box.
    //
    // Parameters:
    //    whattype: Identification of the current point in the walkthrough.
    //    objType: The type of the current grammar object.
    //    featName: The name of the feature.
    //    featNameLoc: Localized feature name.
    //    leveli: The sentence grammar index (0 for word, 1 for phrase, etc.)
    //
    GrammarSelectionBox.prototype.setHandlerCallback = function (whattype, objType, featName, featNameLoc, leveli) {
        var _this = this;
        if (whattype != WHAT.feature && whattype != WHAT.metafeature)
            return;
        if (leveli === 0) { // Handling of words
            $("#" + objType + "_" + featName + "_cb").on('change', function (e) {
                if ($(e.currentTarget).prop('checked')) {
                    if (!inQuiz) {
                        // Save setting in browser
                        sessionStorage.setItem($(e.currentTarget).prop('id'), configuration.propertiesName);
                    }
                    $(".wordgrammar." + featName).removeClass('dontshowit').addClass('showit');
                    _this.wordSpaceBox.implicit(true);
                }
                else {
                    if (!inQuiz) {
                        // Remove setting from browser
                        sessionStorage.removeItem($(e.currentTarget).prop('id'));
                    }
                    $(".wordgrammar." + featName).removeClass('showit').addClass('dontshowit');
                    _this.wordSpaceBox.implicit(false);
                }
                for (var lev = 1; lev < configuration.maxLevels - 1; ++lev)
                    GrammarSelectionBox.adjustDivLevWidth(lev);
            });
        }
        else { // Handling of clause, phrase, etc.
            $("#" + objType + "_" + featName + "_cb").on('change', function (e) {
                if ($(e.currentTarget).prop('checked')) {
                    if (!inQuiz) {
                        // Save setting in browser
                        sessionStorage.setItem($(e.currentTarget).prop('id'), configuration.propertiesName);
                    }
                    $(".xgrammar." + objType + "_" + featName).removeClass('dontshowit').addClass('showit');
                    if (configuration.databaseName == 'ETCBC4' && leveli == 2 && objType == "clause_atom" && featName == "tab") {
                        _this.separateLinesBoxes[leveli].implicit(true);
                        $('.lev2').css(charset.isRtl ? 'padding-right' : 'padding-left', indentation_width + 'px').css('text-indent', -indentation_width + 'px');
                    }
                    else
                        _this.borderBoxes[leveli].implicit(true);
                }
                else {
                    if (!inQuiz) {
                        // Remove setting from browser
                        sessionStorage.removeItem($(e.currentTarget).prop('id'));
                    }
                    $(".xgrammar." + objType + "_" + featName).removeClass('showit').addClass('dontshowit');
                    if (configuration.databaseName == 'ETCBC4' && leveli == 2 && objType == "clause_atom" && featName == "tab") {
                        _this.separateLinesBoxes[leveli].implicit(false);
                        $('.lev2').css(charset.isRtl ? 'padding-right' : 'padding-left', '0').css('text-indent', '0');
                    }
                    else
                        _this.borderBoxes[leveli].implicit(false);
                }
                GrammarSelectionBox.adjustDivLevWidth(leveli);
            });
        }
    };
    //------------------------------------------------------------------------------------------
    // setHandlers method
    //
    // Sets up event handlers for checking/unchecking of checkboxes in the grammar selection box.
    // This function itself handles the "word spacing", "separate lines", and "show border"
    // checkboxes; it then calls walkFeatureNames() to handle the other checkboxes.
    //
    GrammarSelectionBox.prototype.setHandlers = function () {
        var _this = this;
        var _loop_1 = function (level) {
            var leveli = +level;
            if (isNaN(leveli))
                return "continue"; // Not numeric
            var sg = configuration.sentencegrammar[leveli];
            if (leveli === 0) { // Handling of words
                // Set change handler for the checkbox for "word spacing".
                // Although only Hebrew uses a word spacing checkbox, the mechanism is also used by Greek,
                // because we use it to set up the inline-blocks for word grammar information.
                this_1.wordSpaceBox = new util.WordSpaceFollowerBox(leveli);
                // Only Hebrew has a #ws_cb
                $('#ws_cb').on('change', function (e) {
                    if ($(e.currentTarget).prop('checked')) {
                        if (!inQuiz) {
                            // Save setting in browser
                            sessionStorage.setItem($(e.currentTarget).prop('id'), configuration.propertiesName);
                        }
                        _this.wordSpaceBox.explicit(true);
                    }
                    else {
                        if (!inQuiz) {
                            // Remove setting from browser
                            sessionStorage.removeItem($(e.currentTarget).prop('id'));
                        }
                        _this.wordSpaceBox.explicit(false);
                    }
                    for (var lev = 1; lev < configuration.maxLevels - 1; ++lev)
                        GrammarSelectionBox.adjustDivLevWidth(lev);
                });
            }
            else { // Handling of clause, phrase, etc.
                // Set change handlers for the checkboxes for "separate lines" and "show border".
                this_1.separateLinesBoxes[leveli] = new util.SeparateLinesFollowerBox(leveli);
                $("#lev" + leveli + "_seplin_cb").on('change', leveli, function (e) {
                    if ($(e.currentTarget).prop('checked')) {
                        if (!inQuiz) {
                            // Save setting in browser
                            sessionStorage.setItem($(e.currentTarget).prop('id'), configuration.propertiesName);
                        }
                        _this.separateLinesBoxes[e.data].explicit(true);
                    }
                    else {
                        if (!inQuiz) {
                            // Remove setting from browser
                            sessionStorage.removeItem($(e.currentTarget).prop('id'));
                        }
                        _this.separateLinesBoxes[e.data].explicit(false);
                    }
                });
                this_1.borderBoxes[leveli] = new util.BorderFollowerBox(leveli);
                $("#lev" + leveli + "_sb_cb").on('change', leveli, function (e) {
                    if ($(e.currentTarget).prop('checked')) {
                        if (!inQuiz) {
                            // Save setting in browser
                            sessionStorage.setItem($(e.currentTarget).prop('id'), configuration.propertiesName);
                        }
                        _this.borderBoxes[e.data].explicit(true);
                    }
                    else {
                        if (!inQuiz) {
                            // Remove setting from browser
                            sessionStorage.removeItem($(e.currentTarget).prop('id'));
                        }
                        _this.borderBoxes[e.data].explicit(false);
                    }
                    GrammarSelectionBox.adjustDivLevWidth(e.data);
                });
            }
            // Handle the remaining checkboxes:
            sg.walkFeatureNames(sg.objType, function (whattype, objType, origObjType, featName, featNameLoc, sgiObj) { return _this.setHandlerCallback(whattype, objType, featName, featNameLoc, leveli); });
        };
        var this_1 = this;
        for (var level in configuration.sentencegrammar) {
            _loop_1(level);
        }
    };
    //------------------------------------------------------------------------------------------
    // clearBoxes method
    //
    // If we are not running a quiz and 'force' is false, the method checks or unchecks the
    // checkboxes in the grammar selection box according to the values stored in the browser.
    //
    // If we are not running a quiz and 'force' is true, the method unchecks all checkboxes and
    // updates the browser information accordingly.
    //
    // If we are running a quiz, all checkboxes are unchecked, but the browser information is not
    // updated.
    //
    // Parameter:
    //     force: False means set checkboxes according to information in browser.
    //            True means uncheck all checkboxes.
    //
    GrammarSelectionBox.clearBoxes = function (force) {
        $('input[type="checkbox"]').prop('checked', false); // Uncheck all checkboxes
        if (!inQuiz) {
            if (force) {
                // Remove all information about selected grammar items
                for (var i in sessionStorage) {
                    if (sessionStorage[i] == configuration.propertiesName) {
                        sessionStorage.removeItem(i);
                        $('#' + i).prop('checked', false);
                        $('#' + i).trigger('change');
                    }
                }
            }
            else {
                // Enforce selected grammar items
                for (var i in sessionStorage) {
                    if (sessionStorage[i] == configuration.propertiesName)
                        $('#' + i).prop('checked', true);
                }
            }
        }
    };
    //****************************************************************************************************
    // buildGrammarAccordion method
    //
    // Builds accordion for grammar selector.
    //
    // Returns:
    //     The width of the accordion
    //
    GrammarSelectionBox.buildGrammarAccordion = function () {
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
    };
    return GrammarSelectionBox;
}());
// -*- js -*-
// Copyright © 2018 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk
// Character set management
//****************************************************************************************************
// Charset class
//
// This class handles characteristics of the current character set.
//
var Charset = /** @class */ (function () {
    //------------------------------------------------------------------------------------------
    // Constructor method
    //
    // Initializes the members of this object.
    //
    // Parameter:
    //     The character set from the configuration variable.
    //
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
}());
// -*- js -*-
// Copyright © 2018 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk
//****************************************************************************************************
// getFirst function
//
// Retrieves the lowest monad in a MonadSet.
//
// Parameter:
//     ms: MonadSet to search.
// Returns:
//     The lowest monad in the monad set
//
function getFirst(ms) {
    var first = 1000000000;
    for (var pci in ms.segments) {
        if (isNaN(+pci))
            continue; // Not numeric
        var pc = ms.segments[+pci];
        if (pc.low < first)
            first = pc.low;
    }
    return first;
}
//****************************************************************************************************
// getSingleInteger function
//
// Retrieves the single monad from a MonadSet containing only one monad.
//
// Parameter:
//     ms: MonadSet to search.
// Returns:
//     The only monad in the monad set
//
function getSingleInteger(ms) {
    if (ms.segments.length === 1) {
        var p = ms.segments[0];
        if (p.low === p.high)
            return p.low;
    }
    throw 'MonadSet.ObjNotSingleMonad';
}
//****************************************************************************************************
// getMonadArray function
//
// Returns an array containing all the monads in a MonadSet.
//
// Parameter:
//     ms: MonadSet from which monads are taken.
// Returns:
//     An array containing all the monads of the MonadSet.
//
function getMonadArray(ms) {
    var res = [];
    for (var i in ms.segments) {
        if (isNaN(+i))
            continue; // Not numeric
        var mp = ms.segments[+i];
        for (var j = mp.low; j <= mp.high; ++j)
            res.push(j);
    }
    return res;
}
// -*- js -*-
// Copyright © 2018 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk
// Classes that represent display information about Emdros objects.
//****************************************************************************************************
// About the HTML elements displaying text and grammar
//****************************************************************************************************
//
// Words (lowest level Emdros objects):
//     The Emdros object is represented by a <span> element with the classes "textblock" and
//     "inline". Within this <span> element a number of other <span> elements are found:
//      
//         * One <span> element represents the word itself. It has the class "textdisplay" and a class
//           representing the character set of the text. Additionally it may have classes representing
//           Hebrew optional word spacing (see follow_class in the generateHtml method of the
//           DisplaySingleMonadObject class below). The element also has a 'data-idd' attribute, whose
//           value is the id_d of the object in the Emdros database.
//
//         * A number of <span> elements represent grammar features of the word. These elements have
//           the class "wordgrammar". Additionally, they may have these classes:
//               - "showit" / "dontshowit": Controls if the word feature is shown or not.
//               - The name of the feature.
//               - "hebrew" / "hebrew_translit" / "greek" / "latin" / "ltr": Identify the character set
//                 of the feature.
//
// Emdros objects above the word level (such as phrase or clause)...
//     ...have a level (for example, 1 for phrase, 2 for clause etc.).
//     ...may be split into non-contiguous segments, numbered from 0 and up.
//     ...may or may not be displayable. The Patriach element is not displayable, and not all
//        Greek words belong to a displayable clause1 or a clause2.
//
//     The Emdros object is represented by a <span> element with the class "lev#", where # is the
//     level number. Additionally it may have these classes:
//         - "nodummy": If the object is displayable.
//         - "showborder" / "dontshowborder": Controls if the border around the object is shown or not.
//         - "seplin" / "noseplin": Controls if the object is shown on a separate line or not.
//         - "hasp": If the object is split and has a predecessor.
//         - "hass": If the object is split and has a successor.
//
//     In the border, the Emdros object type may be shown in a <span> element. This element has a
//     'data-idd' attribute, whose value is the id_d of the object in the Emdros database, and a
//     'data-mix' attribute, whose value is the number of the current object segment. The <span>
//     element has class "gram" or "nogram", depending on whether the object is displayable or not.
//     Additionally, it has one of these classes:
//         - "showit" / "dontshowit": Controls if the feature is shown or not.
//
//     Following the name of the object type, the border may show features of the Emdros object in a
//     <span> element with class "xgrammar". Additionally, it has these classes:
//         - "showit" / "dontshowit": Controls if the feature is shown or not.
//         - Object type, underscore, feature name (for example "clause_kind").
//         - "indentation": If this feature should be displayed as a Hebrew clause indentation.
//****************************************************************************************************
// Maps URL type to non-localized hypterlink title
var urlTypeString = {
    'u': 'click_for_web_site',
    'v': 'click_for_video',
    'd': 'click_for_document'
};
//****************************************************************************************************
// DisplayMonadObject class
//
// This class represents the display information about a Emdros object. It is linked to a
// MonadObject which contains the features of the Emdros object.
//
// A DisplayMonadObject is either a DisplaySingleMonadObject (if the Emdros object is a word) or a
// DisplayMultipleMonadObject (if the Emdros objects is a phrase, clause, etc.).
//
var DisplayMonadObject = /** @class */ (function () {
    //------------------------------------------------------------------------------------------
    // Constructor method
    //
    // Paramters:
    //     mo: The MonadObject displayed (perhaps in part) by this DisplayMonadObject.
    //     objType: The Emdros object type represented by this DisplayMonadObject.
    //     level: The level of the object. (0=word, 1=phrase, etc.)
    //    
    function DisplayMonadObject(mo, objType, level) {
        // Link this DisplayMonadObject with the MonadObject it displays
        this.displayedMo = mo;
        if (mo.displayers == undefined)
            mo.displayers = [this];
        else
            mo.displayers.push(this);
        this.objType = objType;
        this.level = level;
    }
    //------------------------------------------------------------------------------------------
    // containedIn method
    //
    // Determines if this object is a subset of another DisplayMonadObject.
    //
    // Parameters:
    //     mo: Another DisplayMonadObject.
    // Returns
    //     True if the monad set represented by this DisplayMonadObject is a subset of the
    //     monad set represented by the parameter 'mo'.
    //
    DisplayMonadObject.prototype.containedIn = function (mo) {
        return this.range.low >= mo.range.low && this.range.high <= mo.range.high;
    };
    return DisplayMonadObject;
}());
//****************************************************************************************************
// DisplaySingleMonadObject class
//
// A DisplaySingleMonadObject is a DisplayMonadObject that can display a text component at the
// lowest level, corresponding to a single monad in an Emdros database. This is typically a single
// word.
//
// Hebrew is a special case here. In most languages, words are separated by spaces. In Hebrew, that
// is not necessarily the case. The Hebrew Bible starts with the word "bereshit" ("in the
// beginning"), but this is actually two words: "be" ("in") and "reshit" ("beginning"). When this
// program shows the text without annotation, the words are strung together ("bereshit"), but when
// annotation is included, the words are split ("be- reshit").
// 
var DisplaySingleMonadObject = /** @class */ (function (_super) {
    __extends(DisplaySingleMonadObject, _super);
    //------------------------------------------------------------------------------------------
    // Constructor method
    //
    // Paramters:
    //     smo: The SingleMonadObject displayed by this DisplaySingleMonadObject.
    //     objType: The Emdros object type represented by this DisplaySingleMonadObject.
    //     inQuiz: True if we are displaying an exercise.
    //    
    function DisplaySingleMonadObject(smo, objType, inQuiz) {
        var _this = _super.call(this, smo, objType, 0) || this;
        _this.inQuiz = inQuiz;
        _this.monad = smo.mo.monadset.segments[0].low;
        _this.range = { low: _this.monad, high: _this.monad };
        _this.mix = 0;
        return _this;
    }
    //---------------------------------------------------------------------------------------------------
    // generateHtml method
    //
    // See description under DisplayMonadObject.
    //
    DisplaySingleMonadObject.prototype.generateHtml = function (qd, sentenceTextArr) {
        var smo = this.displayedMo; // The SingleMonadObject being displayed by this DisplaySingleMonadObject
        var uhSize = smo.bcv.length; // The size of the hierarchy book/chapter/verse. This is currently always 3
        var chapter = null; // Current chapter, set if the current word is the first word of a chapter
        var verse = null; // Current verse, set if the current word is the first word of a verse
        // For displaying link icons (only set on the first word in a verse):
        var refs = null; // Any picture database references associated with the current verse
        var urls = null; // Any URLs associated with the current verse
        if (uhSize != 0) {
            // Sanity check:
            if (uhSize != smo.sameAsPrev.length)
                throw 'BAD2';
            if (uhSize != smo.sameAsNext.length)
                throw 'BAD3';
            // If this is not an exercise, add book, chapter, and verse
            if (!this.inQuiz) {
                document.title = l10n.universe['book'][smo.bcv[0]] + ' ' + smo.bcv[1]; // Text in title bar
                $('#textcontainer h1').html(document.title); // Text in page heading
                for (var i = 0; i < uhSize; ++i) {
                    if (!smo.sameAsPrev[i]) {
                        if (i == 1) {
                            // The current word is the first word in a chapter
                            chapter = smo.bcv[i];
                        }
                        else if (i == 2) {
                            // The current word is the first word in a verse
                            verse = smo.bcv[i];
                            refs = smo.pics;
                            urls = smo.urls;
                        }
                    }
                }
            }
        }
        var text; // The text to display for the current word
        if (qd && qd.monad2Id[this.monad]) {
            // This is a quiz object
            if (qd.quizFeatures.dontShow)
                text = "(" + ++DisplaySingleMonadObject.itemIndex + ")";
            else
                text = this.displayedMo.mo.features[configuration.surfaceFeature];
            text = "<em>" + text + "</em>";
        }
        else
            text = this.displayedMo.mo.features[configuration.surfaceFeature];
        // Representation of chapter and verse number:
        var chapterstring = chapter == null ? '' : "<span class=\"chapter\">" + chapter + "</span>&#x200a;"; // Currently not used
        var versestring = verse == null ? '' : "<span class=\"verse\">" + verse + "</span>";
        var refstring; // String of icons representing pictures
        if (refs === null)
            refstring = '';
        else if (refs.length === 4) // Only one reference
            refstring = "<a target=\"_blank\" title=\"" + localize('click_for_picture') + "\" href=\"http://resources.3bmoodle.dk/link.php?picno=" + refs[3] + "\"><img src=\"" + site_url + "images/p.png\"></a>";
        else // More than one reference
            refstring = "<a target=\"_blank\" title=\"" + localize('click_for_pictures') + "\" href=\"http://resources.3bmoodle.dk/img.php?book=" + refs[0] + "&chapter=" + refs[1] + "&verse=" + refs[2] + "\"><img src=\"" + site_url + "images/pblue.png\"></a>";
        var urlstring = ''; // String of icons representing URLs
        if (urls !== null)
            for (var uix = 0; uix < urls.length; ++uix)
                urlstring += "<a target=\"_blank\" title=\"" + localize(urlTypeString[urls[uix][1]]) + "\" href=\"" + urls[uix][0] + "\"><img src=\"" + site_url + "images/" + urls[uix][1] + ".png\"></a>";
        var grammar = ''; // Will hold the interlinear grammar information
        configuration.sentencegrammar[0]
            .walkFeatureValues(smo, 0, this.objType, false, function (whattype, objType, origObjType, featName, featValLoc) {
            switch (whattype) {
                case WHAT.feature:
                    var wordclass = void 0; // The class attribute of an HTML element
                    var fs = getFeatureSetting(objType, featName);
                    if (fs.foreignText)
                        wordclass = charset.foreignClass;
                    else if (fs.transliteratedText)
                        wordclass = charset.transliteratedClass;
                    else
                        wordclass = 'ltr';
                    // For Spanish, English and German in ETCBC4, display only the first gloss
                    if (configuration.databaseName == "ETCBC4"
                        && (featName == "english" || featName == "spanish" || featName == "german")) {
                        featValLoc = featValLoc.replace(/(&[gl]t);/, '$1Q') // Remove ';' from "&gt;" and "&lt;" 
                            .replace(/([^,;(]+).*/, '$1') // Remove everything after ',' or ';' or '('
                            .replace(/(&[gl]t)Q/, '$1;'); // Reinsert ';' in "&gt;" and "&lt;" 
                    }
                    grammar += "<span class=\"wordgrammar dontshowit " + featName + " " + wordclass + "\">" + featValLoc + "</span>";
                    break;
                case WHAT.metafeature:
                    grammar += "<span class=\"wordgrammar dontshowit " + featName + " ltr\">" + featValLoc + "</span>";
                    break;
            }
        });
        var follow_space = '<span class="wordspace"> </span>'; // Enables line wrapping
        var follow_class = '';
        if (charset.isHebrew) {
            var suffix = smo.mo.features[configuration.suffixFeature];
            text += suffix;
            if (suffix === '' || suffix === '-' || suffix === '\u05be' /* maqaf */) {
                follow_space = ''; // Prevents line wrapping
                // Enable optional word spacing. This is handled by the util.WordSpaceFollowerBox class.
                // CSS class 'cont' identifies words concatenated to the next word.
                // CSS class 'contx' identifies words linked to the next word with a hypen/maqaf.
                // CSS class 'cont1' means "do not add word spacing".
                // CSS class 'cont2' means "add a hyphen and word spacing".
                // CSS class 'cont2x' means "add word spacing" (a hyphen/maqaf is already present).
                follow_class = suffix === '' ? ' cont cont1' : ' contx cont1';
                sentenceTextArr[0] += text;
            }
            else
                sentenceTextArr[0] += text + ' ';
        }
        else
            sentenceTextArr[0] += text + ' ';
        return $("<span class=\"textblock inline\"><span class=\"textdisplay " + (charset.foreignClass + follow_class) + "\" data-idd=\"" + smo.mo.id_d + "\">" + versestring + refstring + urlstring + text + "</span>" + grammar + "</span>" + follow_space);
    };
    return DisplaySingleMonadObject;
}(DisplayMonadObject));
//****************************************************************************************************
// DisplayMultipleMonadObject class
//
// A DisplayMultipleMonadObject is a DisplayMonadObject that can display a text component above the
// word level, such as a clause. However, a DisplayMultipleMonadObject always represents a
// contiguous set of monads, so if the clause is split, two or more DisplayMultipleMonadObjects will
// be required, and their fields 'hasPredecessor' and 'hasSuccessor' will be set to represent that
// fact.
//
// A DisplayMultipleMonadObject may be displayed with a border around it. If it has a predecessor or
// or a successor, a side border will be missing.
// 
var DisplayMultipleMonadObject = /** @class */ (function (_super) {
    __extends(DisplayMultipleMonadObject, _super);
    //------------------------------------------------------------------------------------------
    // Constructor method
    //
    // The implementation of the above overloaded constructors.
    //
    function DisplayMultipleMonadObject(mmo, objType, level, monadSet, monadix, hasPredecessor, hasSuccessor) {
        var _this = _super.call(this, mmo, objType, level) || this;
        if (arguments.length == 7) {
            // Non-patriarch
            _this.isPatriarch = false;
            _this.range = monadSet;
            _this.mix = monadix;
            _this.children = [];
            _this.hasPredecessor = hasPredecessor;
            _this.hasSuccessor = hasSuccessor;
            _this.borderTitle = getObjectFriendlyName(objType);
        }
        else {
            // Patriarch
            _this.isPatriarch = true;
            var mseg = monadSet.segments;
            _this.range = { low: mseg[0].low,
                high: mseg[mseg.length - 1].high };
            _this.mix = 0;
            _this.children = [];
            _this.hasPredecessor = false;
            _this.hasSuccessor = false;
        }
        return _this;
    }
    //---------------------------------------------------------------------------------------------------
    // generateHtml method
    //
    // See description under DisplayMonadObject.
    //
    DisplayMultipleMonadObject.prototype.generateHtml = function (qd, sentenceTextArr) {
        var spanclass = "lev" + this.level + " dontshowborder noseplin"; // The class of the <span> element containing this object
        if (this.hasPredecessor)
            spanclass += ' hasp';
        if (this.hasSuccessor)
            spanclass += ' hass';
        var grammar = ''; // The class off the <span> element containing grammar information
        var indent = 0; // The current indentation level (for Hebrew clauses)
        if (configuration.sentencegrammar[this.level]) {
            // Generate the <span> elements for the features of this Emdros object
            configuration.sentencegrammar[this.level]
                .walkFeatureValues(this.displayedMo, this.mix, this.objType, true, function (whattype, objType, origObjType, featName, featValLoc) {
                if (whattype == WHAT.feature || whattype == WHAT.metafeature) {
                    if (configuration.databaseName == 'ETCBC4' && objType == "clause_atom" && featName == "tab")
                        indent = +featValLoc;
                    else
                        grammar += "<span class=\"xgrammar dontshowit " + objType + "_" + featName + "\">:" + featValLoc + "</span>";
                }
            });
        }
        var jq; // The resulting HTMl is built in this JQuery object
        if (this.isPatriarch) {
            // The patriarch object (topmost level) is not displayable
            jq = $("<span class=\"" + spanclass + "\"></span>");
        }
        else if (this.displayedMo.mo.name == "dummy") {
            // We have an object that is not part of the hierarchy (frequent with Greek "δὲ").
            // Such an object is not displayable.
            jq = $("<span class=\"" + spanclass + "\"><span class=\"nogram dontshowit\" data-idd=\"" + this.displayedMo.mo.id_d + "\" data-mix=\"0\"></span></span>");
        }
        else if (configuration.databaseName == 'ETCBC4' && this.level == 2) {
            // Special case: Add indentation information to Hebrew clauses.
            // Note that the indentation <span class="xgrammar...> element is added at a less deep HTML level
            // than the other <span class="xgrammar...> elements.
            // (Don't use multi-line `strings` here - we don't want whitespace between the HTML elements.)
            jq = $("<span class=\"notdummy " + spanclass + "\">"
                + ("<span class=\"gram dontshowit\" data-idd=\"" + this.displayedMo.mo.id_d + "\" data-mix=\"" + this.mix + "\">")
                + getObjectShortFriendlyName(this.objType)
                + grammar
                + '</span>'
                + ("<span class=\"xgrammar clause_atom_tab dontshowit indentation\" data-indent=\"" + indent + "\">")
                + '</span>'
                + '</span>');
        }
        else {
            // Normal case: We have a displayable object
            // (Don't use multi-line `strings` here - we don't want whitespace between the HTML elements.)
            jq = $("<span class=\"notdummy " + spanclass + "\">"
                + ("<span class=\"gram dontshowit\" data-idd=\"" + this.displayedMo.mo.id_d + "\" data-mix=\"" + this.mix + "\">")
                + getObjectShortFriendlyName(this.objType)
                + grammar
                + '</span>'
                + '</span>');
        }
        // Generate HTML for Emdros objects at lower levels
        for (var ch in this.children) {
            if (isNaN(+ch))
                continue; // Not numeric
            jq.append(this.children[ch].generateHtml(qd, sentenceTextArr));
        }
        return jq;
    };
    return DisplayMultipleMonadObject;
}(DisplayMonadObject));
// -*- js -*-
// Copyright © 2018 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk
//****************************************************************************************************
// getObjectFriendlyName function
//
// Retrieves the localized name of an Emdros object type.
//
// Param:
//     otype: The Emdros object type.
// Returns:
//     The localized name for the Emdros object type.
//
function getObjectFriendlyName(otype) {
    if (otype === 'Patriarch') // Shouldn't happen
        return otype;
    var fn = l10n.emdrosobject[otype]._objname;
    return fn ? fn : otype;
}
//****************************************************************************************************
// getObjectShortFriendlyName function
//
// Retrieves the abbreviated localized name of an Emdros object type.
//
// Param:
//     otype: The Emdros object type.
// Returns:
//     The abreviated localized name for the Emdros object type, if it exists. Otherwise the
//     unabbreviated localized name is returned.
//
function getObjectShortFriendlyName(otype) {
    if (l10n.emdrosobject[otype + '_abbrev'] === undefined)
        return getObjectFriendlyName(otype);
    else
        return l10n.emdrosobject[otype + '_abbrev']._objname;
}
//****************************************************************************************************
// getFeatureFriendlyName function
//
// Retrieves the localized name of an Emdros object feature.
//
// Param:
//     otype: The Emdros object type.
//     feature: The Emdros object feature.
// Returns:
//     The localized name for the Emdros object feature.
//
function getFeatureFriendlyName(otype, feature) {
    if (feature === 'visual')
        return localize('visual');
    var fn = l10n.emdrosobject[otype][feature];
    return fn ? fn : feature;
}
//****************************************************************************************************
// getFeatureValueFriendlyName function
//
// Retrieves the localized name of an Emdros object feature value.
//
// Param:
//     featureType: The Emdros object feature type.
//     value: The Emdros object feature value.
//     abbrev: True if the function should return an abbreviated name, if one exists.
//     doStripSort: True if an optional sort index should be removed from the localized value.
// Returns:
//     The localized name for the Emdros object feature value.
//
function getFeatureValueFriendlyName(featureType, value, abbrev, doStripSort) {
    if (abbrev && l10n.emdrostype[featureType + '_abbrev'] !== undefined)
        // TODO: We assume there is no "list of " types here
        return doStripSort
            ? StringWithSort.stripSortIndex(l10n.emdrostype[featureType + '_abbrev'][value])
            : l10n.emdrostype[featureType + '_abbrev'][value];
    // TODO: For now, we handle "list of ..." here.
    // Currently, "list of ..." is only used with Hebrew verb classes.
    // The correctness of this code must be reconsidered if "list of ..." is used for other features.
    if (featureType.substr(0, 8) === 'list of ') {
        featureType = featureType.substr(8); // Remove "list of "
        value = value.substr(1, value.length - 2); // Remove parenteses
        if (value.length == 0)
            return doStripSort
                ? StringWithSort.stripSortIndex(l10n.emdrostype[featureType]['NA'])
                : l10n.emdrostype[featureType]['NA'];
        var verb_classes = value.split(','); // Turn the list of values into an array
        var localized_verb_classes = []; // Localized values will be stored here
        for (var ix in verb_classes) {
            if (isNaN(+ix))
                continue; // Not numeric
            localized_verb_classes.push(doStripSort
                ? StringWithSort.stripSortIndex(l10n.emdrostype[featureType][verb_classes[ix]])
                : l10n.emdrostype[featureType][verb_classes[ix]]);
        }
        localized_verb_classes.sort();
        return localized_verb_classes.join(', '); // Turn the array of localized values into a string
    }
    return doStripSort
        ? StringWithSort.stripSortIndex(l10n.emdrostype[featureType][value])
        : l10n.emdrostype[featureType][value];
}
//****************************************************************************************************
// getFeatureValueOtherFormat function
//
// Retrieves the localized name of a range containing an Emdros object feature value.
//
// Param:
//     otype: The Emdros object type
//     featureName: The Emdros object feature.
//     value: The Emdros object feature value.
// Returns:
//     The localized name for the range containing the Emdros object feature value.
//
function getFeatureValueOtherFormat(otype, featureName, value) {
    var table = l10n.emdrosobject[otype][featureName + '_VALUES'];
    for (var ix = 0; ix < table.length; ++ix)
        if (table[ix].first <= value && table[ix].last >= value)
            return table[ix].text;
    return '?';
}
// -*- js -*-
// Copyright © 2018 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk
//****************************************************************************************************
// localize function
//
// Looks up a localized string.
//
// Parameter:
//     s: The key for the string.
// Returns:
//     The localized value of s.
//
function localize(s) {
    var str = l10n_js[s];
    return str === undefined ? '??' + s + '??' : str;
}
// -*- js -*-
// Copyright © 2018 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk
//****************************************************************************************************
// mayShowFeature function
//
// Determines if the client code is allowed to display a given feature.
//
// When not running an exercise, all features may be displayed, but in an exercise, features that
// the student should provide plus features that are marked as don't show may not be displayed.
//
// Parameters:
//     oType: The type of the current grammar object.
//     origOtype: The original type of the current grammar object. (This can be different from
//                oType when, for example, a feature under "clause" has the name "clause_atom:tab".)
//     feat: The name of the object feature.
//     sigObj: The SentenceGrammarItem containing the feature.
// Returns:
//     True, if the specified feature may be displayed.
//
function mayShowFeature(oType, origOtype, feat, sgiObj) {
    if (!inQuiz)
        return true;
    if (sgiObj.mytype === 'GrammarMetaFeature') {
        // GrammarMetaFeatures are comprised of several features. All of them must be displayable
        // for the meta feature to be displayable.
        for (var i in sgiObj.items) {
            if (isNaN(+i))
                continue; // Not numeric
            if (!mayShowFeature(oType, origOtype, sgiObj.items[+i].name, sgiObj.items[+i]))
                return false;
        }
        return true;
    }
    var qf = quizdata.quizFeatures;
    // Emdros object types in dontShowObjects may not be displayed (except for a feature explicitly marked "show")
    for (var ix = 0, len = qf.dontShowObjects.length; ix < len; ++ix)
        if (qf.dontShowObjects[ix].content === origOtype) // origOtype is a 'dontShowObject'...
            return qf.dontShowObjects[ix].show === feat; // ...so we only show it if it is in the "show" attribute
    // The object type was not in dontShowObjects. If it is not the sentence unit of the exercise,
    // we may display it.
    if (oType !== qf.objectType)
        return true;
    // For the sentence unit of the quiz, request featues must not be displayed
    for (var ix = 0, len = qf.requestFeatures.length; ix < len; ++ix)
        if (qf.requestFeatures[ix].name === feat)
            return false;
    // Don't-show features must not be displayed
    return qf.dontShowFeatures.indexOf(feat) === -1;
}
// -*- js -*-
// Copyright © 2018 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk
//****************************************************************************************************
// About the relationship between the MonadObject and DisplayMonadObject classes
//****************************************************************************************************
//
// The server code places information about Emdros objects in the 'monadObjects' field of the
// DictionaryIf interface. Each element in the 'monadObjects' field is an object of class MonadObject.
// The MonadObjects are ordered in a hierarchy using the 'children_idds' field of the MonadObject.
//
// When the client code calls the constructor of the Dictionary class below, it adds a 'parent'
// field to the MonadObject, resulting in a doubly linked hierarchy.
//
// The constructor also creates a parallel hierachy of DisplayMonadObjects, which contain
// information about how the Emdros objects are displayed in HTML.
//
// The MonadObject class has two subclasses, SingleMonadObject (which represents a word) and
// MultipleMonadObject (which represents a phrase or clause etc.). Similarly, the DisplayMonadObject
// class has two subclasses, DisplaySingleMonadObject and DisplayMultipleMonadObject. Although the
// two sets of classes are releated, there is a difference in the way split Emdros objects are
// handled. (A split Emdros object is, for example, a clause which contains another clause.) A split
// Emdros object is handled by one MultipleMonadObject but by several DisplayMultipleMonadObjects,
// one for each segment of the split Emdros object.
//
// When the constructor below creates the hierarchy of DisplayMonadObjects, the MonadObjects
// will be tied to the DisplayMonadObjects thus:
//    The 'displayMo' field of DisplayMonadObject contains the corresponding MonadObject.
//    The 'displayers' field of MonadObject is an array containing the corresponding DisplayMonadObjects.
//
//****************************************************************************************************
//****************************************************************************************************
// Dictionary class
//
// This class is responsible for interpreting the contents of the 'dictionaries' variable and
// generating the corresponding HTML code.
//
var Dictionary = /** @class */ (function () {
    //------------------------------------------------------------------------------------------
    // Constructor method
    //
    // Parameters:
    //     dictif: The DictionaryIf object to interpret.
    //     index: The entry in dictif.sentenceSets and dictif.monadObjects to interpret.
    //     qd: Quiz information if we are generating an exercise, otherwise null.
    //
    function Dictionary(dictif, index, qd) {
        this.monads = []; // Maps id_d => monad object
        this.level = []; // Maps id_d => object level
        this.singleMonads = []; // Maps monad number => SingleMonadObject (words only)
        this.dispMonadObjects = []; // The Emdros objects to display.
        // Save local copy of relevant information
        this.sentenceSet = dictif.sentenceSets[index];
        this.monadObjects1 = dictif.monadObjects[index];
        this.bookTitle = dictif.bookTitle;
        this.hideWord = (qd != null && qd.quizFeatures.dontShow);
        // Generate the 'singleMonads', 'monads' and 'level' maps.
        for (var level in this.monadObjects1) {
            var leveli = +level;
            if (isNaN(leveli))
                continue; // Not numeric
            // leveli is 0 for word, 1 for phrase, etc. (or something similar depending on the database)
            for (var i in this.monadObjects1[leveli]) {
                if (isNaN(+i))
                    continue; // Not numeric
                var item = this.monadObjects1[leveli][+i]; // A single Emdros object
                if (leveli === 0)
                    this.singleMonads[getSingleInteger(item.mo.monadset)] = item;
                this.monads[item.mo.id_d] = item;
                this.level[item.mo.id_d] = leveli;
            }
        }
        // Bind parents and children of the MonadObject hierarchy
        for (var i in this.monads) {
            if (isNaN(+i))
                continue; // Not numeric
            var parent_1 = this.monads[+i];
            for (var i2 in parent_1.children_idds) {
                if (isNaN(+i2))
                    continue; // Not numeric
                var child_idd = parent_1.children_idds[+i2];
                this.monads[child_idd].parent = parent_1;
            }
        }
        ///////////////////////////
        // Create display hierarchy
        // Single monads (i.e. words)
        this.dispMonadObjects.push([]);
        for (var se in this.singleMonads) {
            if (isNaN(+se))
                continue; // Not numeric
            // singleMonads is sparsely populated
            this.dispMonadObjects[0].push(new DisplaySingleMonadObject(this.singleMonads[+se], configuration.sentencegrammar[0].objType, qd != null));
        }
        // Multiple monads (i.e. phrases, clauses, etc.)
        for (var lev = 1; lev < configuration.maxLevels; ++lev) {
            var ldmo = []; // The Emdros objects at level 'lev'
            this.dispMonadObjects.push(ldmo);
            if (lev < configuration.maxLevels - 1) { // Not top level
                for (var i in this.monadObjects1[lev]) {
                    if (isNaN(+i))
                        continue; // Not numeric
                    var monadObject = this.monadObjects1[lev][parseInt(i)]; // The current object
                    // Split object into contiguous segments
                    var segCount = monadObject.mo.monadset.segments.length;
                    for (var mix = 0; mix < segCount; ++mix) {
                        var mp = monadObject.mo.monadset.segments[mix];
                        ldmo.push(new DisplayMultipleMonadObject(monadObject, configuration.sentencegrammar[lev].objType, lev, mp, mix, mix > 0, mix < segCount - 1));
                    }
                }
                // Sort ldmo in monad order
                ldmo.sort(function (a, b) {
                    return a.range.low - b.range.low;
                });
            }
            else { // Top level
                // At the top level there is always only one DisplayMultipleMonadObject
                var monadObject = this.monadObjects1[lev][0];
                ldmo.push(new DisplayMultipleMonadObject(monadObject, 'Patriarch', // The pseudo-name of the top-level object
                lev, monadObject.mo.monadset));
            }
        }
        /////////////////////////////////////////////////////////
        // Construct child-parent linkage for DisplayMonadObjects
        for (var lev = 1; lev < configuration.maxLevels; ++lev) {
            // Find constituent MonadObjects
            // Loop through monads at level lev
            for (var parentDmoIx in this.dispMonadObjects[lev]) {
                if (isNaN(+parentDmoIx))
                    continue; // Not numeric
                var parentDmo = this.dispMonadObjects[lev][+parentDmoIx];
                // Loop through mondads at child level
                for (var childDmoIx in this.dispMonadObjects[lev - 1]) {
                    if (isNaN(+childDmoIx))
                        continue; // Not numeric
                    var childDmo = this.dispMonadObjects[lev - 1][+childDmoIx];
                    if (childDmo.containedIn(parentDmo)) {
                        // We found a child
                        if (childDmo.parent != undefined)
                            throw 'BAD1'; // Ensures that the tree is properly constructed
                        childDmo.parent = parentDmo;
                        parentDmo.children.push(childDmo);
                    }
                }
            }
        }
    }
    //------------------------------------------------------------------------------------------
    // hoverForGrammar method
    //
    // Associate a grammar information box with each Emdros object. The grammar information box is
    // displayed when the mouse hovers over the object.
    //
    // This function is called only if the display size is large enough to have room for a grammar
    // information box.
    //
    Dictionary.prototype.hoverForGrammar = function () {
        var thisDict = this;
        // All display objects are identified with a "data-idd" attribute in the displaying HTML element.
        if (useTooltip) {
            // Use the tooltip function of JQuery UI.
            $(document).tooltip({
                items: "[data-idd]",
                disabled: false,
                content: function () { return thisDict.toolTipFunc(this, true)[0]; }
            });
        }
        else {
            // Poplulate the <div class="grammardisplay"> element with grammar information when the
            // mouse hovers over a displayed object.
            $("[data-idd]")
                .hover(function () {
                // Calculate vertical position of '.grammardisplay'.
                // It should be placed at least 20px from top of window but not higher
                // than '#textcontainer'
                var scrTop = $(window).scrollTop();
                var qcTop = $('#textcontainer').offset().top;
                $('.grammardisplay')
                    .html(thisDict.toolTipFunc(this, true)[0])
                    .css('top', Math.max(0, scrTop - qcTop + 5))
                    .outerWidth($('#grammardisplaycontainer').outerWidth() - 25) // 25px is a littel more than margin-right
                    .show();
            }, function () {
                $('.grammardisplay').hide();
            });
        }
    };
    //------------------------------------------------------------------------------------------
    // dontHoverForGrammar method
    //
    // Disassociate a grammar information box with each Emdros object.
    //
    // This function is called if the display size is too narrow to have room for a grammar
    // information box.
    //
    Dictionary.prototype.dontHoverForGrammar = function () {
        // All display objects are identified with a "data-idd" attribute in the displaying HTML element.
        if (useTooltip)
            $(document).tooltip({ items: "[data-idd]", disabled: true });
        else
            $("[data-idd]").off("mouseenter mouseleave");
    };
    //------------------------------------------------------------------------------------------
    // clickForGrammar method
    //
    // Associate a popup grammar information box with each Emdros object. The popup grammar
    // information box is displayed when the user clicks on the object.
    //
    Dictionary.prototype.clickForGrammar = function () {
        // All display objects are identified with a "data-idd" attribute in the displaying HTML element.
        var _this = this;
        $("[data-idd]").on('click', function (event) {
            var _a = _this.toolTipFunc(event.currentTarget, false), contents = _a[0], heading = _a[1];
            $('#grammar-info-label').html(heading);
            $('#grammar-info-body').html(contents);
            $('#grammar-info-dialog').modal('show');
        });
    };
    //------------------------------------------------------------------------------------------
    // handleDisplaySize static method
    //
    // This function is called when the display size changes. It enables or disables the grammar
    // information box depending on the display size.
    //
    // Parameter:
    //     thisDict: The current Dictionary object
    //
    Dictionary.handleDisplaySize = function (thisDict) {
        switch (resizer.getWindowSize()) {
            case 'xs':
                thisDict.dontHoverForGrammar(); // Disable grammar information box
                break;
            default:
                thisDict.hoverForGrammar(); // Enable grammar information box
                break;
        }
    };
    //------------------------------------------------------------------------------------------
    // boxes static method
    //
    // Generates a string with an appropriate number of small squares (Unicode character 25AA (▪))
    // to indicate indentation size. This is currently only used for displaying indentation of
    // clauses in the ETCBC4 database.
    //
    // Parameters:
    //     num: The current indentation.
    //     minnum: The minimum indentitation in the current text.
    //     maxnum: The maximum indentitation in the current text.
    // Returns:
    //     A string containing the appropriate number of squares.
    //
    Dictionary.boxes = function (num, minnum, maxnum) {
        var s = '';
        var numspaces = num < 10 ? num : num - 1; // If num has two digits, we write one space less
        for (var i = minnum; i < numspaces; ++i)
            s += '\u00a0'; // Unicode NO-BREAK SPACE
        s += num;
        for (var i = num; i <= maxnum; ++i)
            s += '\u25aa'; // Unicode BLACK SMALL SQUARE
        return s;
    };
    //------------------------------------------------------------------------------------------
    // generateSentenceHtml method
    //
    // Generates HTML code to display the current text and its grammar information. The text is
    // stored in the <div id="textarea"> HTML element and is also the return value from this
    // function. The return value is used if we are generating an exercise, in which case the text
    // must be stored as part of the exercise statistics.
    //
    // Parameter:
    //     qd: Data for the current exercise. Null, if we are not generating an exercise. This data
    //         is used to control what grammar information to hide.
    // Returns:
    //     HTML code for displaying text.
    //
    Dictionary.prototype.generateSentenceHtml = function (qd) {
        DisplaySingleMonadObject.itemIndex = 0; // Used in exercises where numbers replace text
        var sentenceTextArr = ['']; // The text is build in element [0] of this array. An
        // array is used because we want to use this variable
        // is a call-by-reference parameter.
        // Call DisplayMonadObject.generateHtml() on the top-most Emdros object (the 'Patriarch')
        $('#textarea').append(this.dispMonadObjects[this.dispMonadObjects.length - 1][0].generateHtml(qd, sentenceTextArr));
        if (configuration.databaseName == 'ETCBC4') {
            // Generate indentation information
            // Calculate the min and max indentation by looping trough all HTML elements with indentation information
            var minindent_1;
            var maxindent_1;
            var all_c_a_t = $('#textarea').find('.xgrammar.clause_atom_tab');
            all_c_a_t.each(function (index, el) {
                var indent = +$(el).attr('data-indent');
                if (index == 0)
                    minindent_1 = maxindent_1 = indent;
                else {
                    if (indent < minindent_1)
                        minindent_1 = indent;
                    if (indent > maxindent_1)
                        maxindent_1 = indent;
                }
            });
            // Calculate width of indentation indicators
            $('#textarea').append('<div class="indentation" id="testwidth"></div>');
            var tw = $('#testwidth');
            tw.html(Dictionary.boxes(minindent_1, minindent_1, maxindent_1) + '&nbsp;&nbsp;');
            indentation_width = tw.width() + 1;
            // Set indentation indicators
            all_c_a_t.each(function (index, el) {
                var indent = +$(el).attr('data-indent');
                $(el).html(Dictionary.boxes(indent, minindent_1, maxindent_1) + '&nbsp;&nbsp;');
            });
        }
        // Set up handlers for display size changes (this also takes care of mouse hovering events)
        // and for mouse clicks
        resizer.addResizeListener(Dictionary.handleDisplaySize, this, 'xyzzy');
        Dictionary.handleDisplaySize(this);
        this.clickForGrammar();
        return sentenceTextArr[0];
    };
    //------------------------------------------------------------------------------------------
    // toolTipFunc method
    //
    // Generates HTML for the grammar information box.
    //
    // Patameters:
    //     x_this: The HTML element that triggered the displaying of the grammar information box.
    //     set_head: True if a header line should be generated. This parameter is true for mouse
    //               hover events, false for click events.
    // Returns:
    //     A tuple of two strings. The first string is the HTML for the grammar information box
    //     contents, the second string is the heading of the popup box, if any.
    //
    Dictionary.prototype.toolTipFunc = function (x_this, set_head) {
        // x_this identifies the HTML element that was clicked or hovered over. It has the attribute
        // 'data-idd', which contains the Emdros id_d, and the attribute 'data-mix' which identifies
        // the part of a multi-part object (such as a split clause).
        var monob = this.monads[+($(x_this).attr("data-idd"))]; // Current MonadObject
        var level = this.level[+($(x_this).attr("data-idd"))]; // Current level (0=word, 1=phrase, etc.)
        var mix = +$(x_this).attr("data-mix"); // Current part of multi-part object
        var sengram = configuration.sentencegrammar[level]; // Sentence grammar information for the current level
        var res = '<table>'; // Will contain the resulting HTML
        if (set_head) {
            res += "<tr>\n                        <td colspan=\"2\" class=\"tooltiphead\">" + getObjectFriendlyName(sengram.objType) + "</td>\n                    </tr>";
        }
        if (level === 0 && !this.hideWord) { // Word level and we're not hiding the text
            res += "<tr>\n                        <td>" + localize('visual') + "</td>\n                        <td class=\"bol-tooltip leftalign " + charset.foreignClass + "\">" + monob.mo.features[configuration.surfaceFeature] + "</td>\n                    </tr>";
        }
        var map = []; // Maps feature name => localized feature name
        // Popualate 'map':
        sengram.walkFeatureNames(sengram.objType, function (whattype, objType, origObjType, featName, featNameLoc, sgiObj) {
            if (whattype == WHAT.feature || whattype == WHAT.metafeature)
                if (!mayShowFeature(objType, origObjType, featName, sgiObj))
                    return;
            if (whattype == WHAT.feature || whattype == WHAT.metafeature || whattype == WHAT.groupstart)
                map[featName] = featNameLoc;
        });
        // Generate HTML for each feature of the object
        sengram.walkFeatureValues(monob, mix, sengram.objType, false, function (whattype, objType, origObjType, featName, featValLoc, sgiObj) {
            switch (whattype) {
                case WHAT.feature:
                    if (mayShowFeature(objType, origObjType, featName, sgiObj)) {
                        var wordclass = void 0; // HTML element class for displaying current feature value
                        var fs = getFeatureSetting(objType, featName);
                        if (featValLoc === '-')
                            wordclass = '';
                        else if (fs.foreignText)
                            wordclass = charset.foreignClass;
                        else if (fs.transliteratedText)
                            wordclass = charset.transliteratedClass;
                        else
                            wordclass = '';
                        res += "<tr>\n                                                           <td>" + map[featName] + "</td>\n                                                           <td class=\"bol-tooltip leftalign " + wordclass + "\">" + featValLoc + "</td>\n                                                       </tr>";
                    }
                    break;
                case WHAT.metafeature:
                    if (mayShowFeature(objType, origObjType, featName, sgiObj)) {
                        res += "<tr>\n                                                           <td>" + map[featName] + "</td>\n                                                           <td class=\"bol-tooltip leftalign\">" + featValLoc + "</td>\n                                                       </tr>";
                    }
                    break;
                case WHAT.groupstart:
                    res += "<tr>\n                                                       <td><b>" + map[featName] + ":</b></td>\n                                                       <td class=\"leftalign\"></td>\n                                                   </tr>";
                    break;
            }
        });
        return [res + '</table>', getObjectFriendlyName(sengram.objType)];
    };
    //------------------------------------------------------------------------------------------
    // getSingleMonadObject method
    //
    // Returns the SingleMonadObject (i.e. word) identified by a monad.
    //
    // Parameter:
    //     monad: The monad identifying the Emdros word object.
    // Returns:
    //     The SingleMonadObject associated with the specified monad.
    //
    Dictionary.prototype.getSingleMonadObject = function (monad) {
        return this.singleMonads[monad];
    };
    return Dictionary;
}());
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
    COMPONENT_TYPE[COMPONENT_TYPE["checkBoxes"] = 5] = "checkBoxes";
})(COMPONENT_TYPE || (COMPONENT_TYPE = {}));
var ComponentWithYesNo = /** @class */ (function () {
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
        if (ComponentWithYesNo.lastMonitored !== elem.data('kbid')) { // A new element has focus
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
        this.elem.on('change', function () { return _this.setNone(); });
    };
    // Check for keypress and paste event
    ComponentWithYesNo.prototype.addKeypressListener = function () {
        var _this = this;
        // TODO: Can all of this be changed to on('input', ...)?
        this.elem.on('paste cut', function (e1) { return _this.setNone(); });
        // Note: Firefox sends keypress event on arrows and CTRL-C, Chrome and IE do not
        this.elem.on('keypress', function (e1) { return _this.setNone(); })
            .on('keydown', function (e2) {
            if (e2.keyCode == 8 /* Backspace */ || e2.keyCode == 46 /* Del */)
                _this.elem.trigger('keypress');
        }); /* Ensure that backspace and del trigger keypress - they don't normally on Chrome */
        if (this.elemType === COMPONENT_TYPE.textFieldWithVirtKeyboard) {
            // We must do continuous polling of changes
            this.elem.on('focus', function (e) { return ComponentWithYesNo.monitorChange($(e.currentTarget), _this); });
        }
    };
    /// Gets the contained component.
    /// @return The component displayed with this object.
    ComponentWithYesNo.prototype.getComp = function () {
        if (this.elemType === COMPONENT_TYPE.comboBox2)
            return $(this.elem.children()[0]); // A comboBox2 is a <div> containing a <select>. We return the <select>.
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
        }
        else {
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
}());
// -*- js -*-
/* 2013 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk */
/// @file
/// @brief Contains the Answer class.
/// This class represents an answer to a single feature request in the current question.
var Answer = /** @class */ (function () {
    /** Constructs an Answer object.
     * @param comp The feature request component.
     * @param answerSws The correct answer.
     * @param answerString The correct answer as a String.
     * @param matchRegexp The regular expression used to find a match, null if none is used.
     */
    function Answer(comp, answerSws, answerString, matchRegexp) {
        this.hasAnswered = false; ///< Has the user answered this question?
        this.comp = comp;
        this.c = comp.getComp();
        this.cType = comp.getCompType();
        this.answerSws = answerSws;
        this.answerString = answerString;
        this.matchRegexp = matchRegexp;
        if (this.cType == COMPONENT_TYPE.checkBoxes) {
            var aString = answerString.substr(1, answerString.length - 2); // Remove surrounding '(' and ')'
            this.answerArray = aString.split(',');
        }
    }
    /// Displays the correct answer.
    Answer.prototype.showIt = function () {
        switch (this.cType) {
            case COMPONENT_TYPE.textField:
            case COMPONENT_TYPE.textFieldWithVirtKeyboard:
                $(this.c).val(this.answerString);
                break;
            case COMPONENT_TYPE.translatedField:
                /// @todo ((TranslatedField)this.c).setText(this.answerString);
                break;
            case COMPONENT_TYPE.comboBox1:
            case COMPONENT_TYPE.comboBox2:
                $(this.c).val(this.answerSws.getInternal()).prop('selected', true);
                break;
            case COMPONENT_TYPE.checkBoxes:
                var inputs = $(this.c).find('input');
                var xthis = this;
                inputs.each(function () {
                    var value = $(this).attr('value');
                    $(this).prop('checked', xthis.answerArray.indexOf(value) != -1);
                });
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
        }
        else {
            // The question panel contains the user's answers.
            // Where answers are provided, their correctness is logged.
            var userAnswer;
            var isCorrect;
            switch (this.cType) {
                case COMPONENT_TYPE.textField:
                case COMPONENT_TYPE.textFieldWithVirtKeyboard:
                    // TODO: Use Three_ET.dbInfo.charSet.converter.normalize (relevant only in Greek)
                    userAnswer = $(this.c).val().trim()
                        //.replace(/\u003b/g, '\u037e')  // SEMICOLON -> GREEK QUESTION MARK
                        //.replace(/\u00b7/g, '\u0387')  // MIDDLE DOT -> GREEK ANO TELEIA
                        .replace(/\u03ac/g, '\u1f71') // GREEK SMALL LETTER ALPHA WITH TONOS -> OXIA
                        .replace(/\u03ad/g, '\u1f73') // GREEK SMALL LETTER EPSILON WITH TONOS -> OXIA
                        .replace(/\u03ae/g, '\u1f75') // GREEK SMALL LETTER ETA WITH TONOS -> OXIA
                        .replace(/\u03af/g, '\u1f77') // GREEK SMALL LETTER IOTA WITH TONOS -> OXIA
                        .replace(/\u03cc/g, '\u1f79') // GREEK SMALL LETTER OMICRON WITH TONOS -> OXIA
                        .replace(/\u03cd/g, '\u1f7b') // GREEK SMALL LETTER UPSILON WITH TONOS -> OXIA
                        .replace(/\u03ce/g, '\u1f7d') // GREEK SMALL LETTER OMEGA WITH TONOS -> OXIA
                        .replace(/\u0390/g, '\u1fd3') // GREEK SMALL LETTER IOTA WITH DIALYTIKA AND TONOS -> OXIA
                        .replace(/\u03b0/g, '\u1fe3'); // GREEK SMALL LETTER UPSILON WITH DIALYTIKA AND TONOS -> OXIA
                    if (this.matchRegexp == null) {
                        isCorrect = userAnswer == this.answerString; // Not === for one may be a number
                        if (!isCorrect)
                            isCorrect = this.answerString === '-' && userAnswer === '\u05be'; // Accept Maqaf instead of hyphen
                    }
                    else {
                        // Escape all special characters in the user's answer
                        var re = eval(this.matchRegexp.format(userAnswer.replace(/[-[\]{}()*+?.,\\^$|#\s]/g, "\\$&")));
                        isCorrect = this.answerString.match(re) !== null;
                    }
                    break;
                case COMPONENT_TYPE.translatedField:
                    userAnswer = $(this.c).val().trim();
                    if (this.matchRegexp == null)
                        isCorrect = userAnswer == this.answerString; // Not === for one may be a number
                    else {
                        // Escape all special characters in the user's answer
                        var re = eval(this.matchRegexp.format(userAnswer.replace(/[-[\]{}()*+?.,\\^$|#\s]/g, "\\$&")));
                        isCorrect = this.answerString.match(re) !== null;
                    }
                    break;
                case COMPONENT_TYPE.comboBox1:
                case COMPONENT_TYPE.comboBox2:
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
                case COMPONENT_TYPE.checkBoxes:
                    var inputs = $(this.c).find('input');
                    var xthis = this;
                    isCorrect = true;
                    userAnswer = '';
                    inputs.each(function () {
                        var value = $(this).attr('value');
                        if ($(this).prop('checked')) {
                            userAnswer += value + ',';
                            isCorrect = isCorrect && xthis.answerArray.indexOf(value) != -1;
                        }
                        else
                            isCorrect = isCorrect && xthis.answerArray.indexOf(value) == -1;
                    });
                    userAnswer = '(' + userAnswer.substr(0, userAnswer.length - 1) + ')';
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
}());
// -*- js -*-
// Copyright © 2018 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk
// This code handles displaying a single question of an exercise.
/// <reference path="componentwithyesno.ts" />
/// <reference path="answer.ts" />
//****************************************************************************************************
// PanelQuestion class
//
// This class represents a single question (with multiple question items) of an exercise.
//
var PanelQuestion = /** @class */ (function () {
    //------------------------------------------------------------------------------------------
    // constructor method
    //
    // Parameter:
    //     qd: The information required to generate a exercise.
    //     dict: The collection of Emdros objects for this question.
    //
    function PanelQuestion(qd, dict) {
        var _this = this;
        this.vAnswers = []; // The correct answer for each question item
        this.question_stat = new QuestionStatistics; // Answer statistics
        this.qd = qd;
        this.sentence = dict.sentenceSet;
        ////////////////////////////////////////////////////////////////////
        // Calculate the Bible reference (the 'location') for this sentence.
        // We base the location on the first monad in the sentence.
        var smo = dict.getSingleMonadObject(getFirst(this.sentence));
        var location_realname = ''; // Unlocalized
        this.location = smo.bcv_loc; // Localized
        for (var unix in configuration.universeHierarchy) {
            var unixi = +unix;
            if (isNaN(unixi))
                continue; // Not numeric
            var uniname = configuration.universeHierarchy[unixi].type;
            switch (unixi) {
                case 0:
                    location_realname += smo.bcv[unixi] + ', ';
                    break;
                case 2:
                    location_realname += ', ';
                // Fall through
                case 1:
                    location_realname += smo.bcv[unixi];
                    break;
            }
        }
        if (this.qd.maylocate) {
            $('input#locate_cb').on('click', null, this.location, function (e) {
                if ($(this).prop('checked'))
                    $('.location').html(e.data);
                else
                    $('.location').html('');
            });
        }
        else
            $('#locate_choice').hide();
        if ($('#locate_cb').prop('checked'))
            $('.location').html(this.location);
        ///////////////////////////////////
        // Generate table of question items
        // Cache a few variables for easy access
        var dontShow = qd.quizFeatures.dontShow;
        var showFeatures = qd.quizFeatures.showFeatures;
        var requestFeatures = qd.quizFeatures.requestFeatures;
        var oType = qd.quizFeatures.objectType;
        // Save question text and location for statistics
        this.question_stat.text = dict.generateSentenceHtml(qd);
        this.question_stat.location = location_realname;
        // Create heading for table of question items
        var colcount = 0; // Number of columns in <table> containing question items
        if (dontShow) {
            $('#quiztabhead').append('<th>' + localize('item_number') + '</th>');
            this.question_stat.show_feat.names.push('item_number');
            ++colcount;
        }
        for (var sfi in showFeatures) {
            if (isNaN(+sfi))
                continue; // Not numeric
            $('#quiztabhead').append('<th>' + getFeatureFriendlyName(oType, showFeatures[sfi]) + '</th>');
            this.question_stat.show_feat.names.push(showFeatures[sfi]); // Save feature name for statistics
            ++colcount;
        }
        for (var sfi in requestFeatures) {
            if (isNaN(+sfi))
                continue; // Not numeric
            $('#quiztabhead').append('<th>' + getFeatureFriendlyName(oType, requestFeatures[sfi].name) + '</th>');
            this.question_stat.req_feat.names.push(requestFeatures[sfi].name); // Save feature name for statistics
            ++colcount;
        }
        // Create table entries for each question item
        var featuresHere = typeinfo.obj2feat[oType]; // Maps feature name => feature type
        var qoFeatures = this.buildQuizObjectFeatureList(); // Feature/value pairs for each question object
        var hasForeignInput = false; // Do we need a virtual keyboard?
        var firstInput = 'id="firstinput"'; // ID of <input> to receive virtual keyboard focus
        // Loop through all the quiz objects
        for (var qoid in qoFeatures) {
            if (isNaN(+qoid))
                continue; // Not numeric
            var currentRow = $('<tr></tr>'); // Current question item
            var fvals = qoFeatures[+qoid]; // Feature/value pairs for current quiz object
            if (dontShow) {
                currentRow.append('<td>' + (+qoid + 1) + '</td>'); // Item number
                this.question_stat.show_feat.values.push("" + (+qoid + 1)); // Save feature value for statistics
            }
            ////////////////////////////////
            // Loop through display features
            for (var sfi in showFeatures) {
                if (isNaN(+sfi))
                    continue; // Not numeric
                var sf = showFeatures[+sfi]; // Feature name
                var val = fvals[sf]; // Feature value
                var featType = featuresHere[sf]; // Feature type
                var featset = getFeatureSetting(oType, sf); // Feature configuration
                this.question_stat.show_feat.values.push(val); // Save feature value for statistics
                if (featType == null && sf !== 'visual')
                    alert("Unexpected (1) featType==null in panelquestion.ts; sf=\"" + sf + "\"");
                if (sf === 'visual')
                    featType = 'string';
                if (featType == 'hint') {
                    // The feature value looks like this:
                    // "featurename=value" or "featurename=value,featurename=value"
                    var sp = val.split(/[,=]/);
                    if (sp.length == 2) {
                        val = getFeatureFriendlyName(oType, sp[0]) + "=" +
                            getFeatureValueFriendlyName(featuresHere[sp[0]], sp[1], false, true);
                    }
                    else if (sp.length == 4) {
                        val = getFeatureFriendlyName(oType, sp[0])
                            + "="
                            + getFeatureValueFriendlyName(featuresHere[sp[0]], sp[1], false, true)
                            + ", "
                            + getFeatureFriendlyName(oType, sp[2])
                            + "="
                            + getFeatureValueFriendlyName(featuresHere[sp[2]], sp[3], false, true);
                    }
                    else if (val === '*')
                        val = '-';
                }
                else if (featType !== 'string' && featType !== 'ascii' && featType !== 'integer') {
                    // This is an enumeration feature type
                    // Replace val with the appropriate friendly name or "Other value"
                    if (featset.otherValues && featset.otherValues.indexOf(val) !== -1)
                        val = localize('other_value');
                    else
                        val = getFeatureValueFriendlyName(featType, val, false, true);
                }
                if (val == null)
                    alert('Unexpected val==null in panelquestion.ts');
                if (featType === 'string' || featType == 'ascii')
                    currentRow.append("<td class=\"" + PanelQuestion.charclass(featset) + "\">" + (val === '' ? '-' : val) + "</td>");
                else
                    currentRow.append("<td>" + val + "</td>");
            }
            var _loop_2 = function (rfi) {
                if (isNaN(+rfi))
                    return "continue"; // Not numeric
                var rf = requestFeatures[+rfi].name; // Feature name
                var usedropdown = requestFeatures[+rfi].usedropdown; // Use multiple choice?
                var correctAnswer = fvals[rf]; // Feature value (i.e., the correct answer)
                var featType = featuresHere[rf]; // Feature type
                var featset = getFeatureSetting(oType, rf); // Feature configuration
                var v = null; // Component to hold the data entry field or a message
                if (correctAnswer == null)
                    alert('Unexpected correctAnswer==null in panelquestion.ts');
                if (correctAnswer === '')
                    correctAnswer = '-'; // Indicates empty answer
                if (featType == null && rf !== 'visual')
                    alert('Unexpected (2) featType==null in panelquestion.ts');
                if (rf === 'visual')
                    featType = 'string';
                // The layout of the feature request depends on the type of the feature:
                if (featset.alternateshowrequestDb != null && usedropdown) {
                    // Multiple choice question item
                    var suggestions = fvals[rf + '!suggest!']; // Values to choose between
                    if (suggestions == null) // No suggestions, just display the answer
                        v = $("<td class=\"" + PanelQuestion.charclass(featset) + "\">" + correctAnswer + "</td>");
                    else {
                        // Create this HTML structure in the variable v:            From these variables
                        // <span ...>                                                cwyn
                        //   <img ...>                                               cwyn
                        //   <img ...>                                               cwyn
                        //   <img ...>                                               cwyn
                        //   <div class="styled-select">                             mc_div
                        //     <select class="..." style="direction:ltr">            mc_select
                        //       <option value="NoValueGiven"></option>              
                        //       <option value="VAL1" class="...">VAL1</option>      optArray[0]
                        //       <option value="VAL2" class="...">VAL2</option>      optArray[1]
                        //       ...                                                 
                        //     </select>                                             mc_select
                        //   </div>                                                  mc_div
                        // </span>                                                   cwyn
                        var mc_div = $('<div class="styled-select"></div>');
                        // direction:ltr forces left alignment of options (though not on Firefox)
                        var mc_select_1 = $("<select class=\"" + PanelQuestion.charclass(featset) + "\" style=\"direction:ltr\">");
                        mc_div.append(mc_select_1);
                        var optArray = []; // All the multiple choice options
                        var cwyn = new ComponentWithYesNo(mc_div, COMPONENT_TYPE.comboBox2); // Result indicator
                        cwyn.addChangeListener();
                        mc_select_1.append('<option value="NoValueGiven"></option>'); // Empty default choice
                        for (var valix in suggestions) {
                            if (isNaN(+valix))
                                continue; // Not numeric
                            // We use a StringWithSort object to handle the option strings. This may
                            // seem unnecessary in this case, but it means that comboboxes can be
                            // handled in a uniform manner.
                            var s = suggestions[+valix]; // Current suggestion
                            var item = new StringWithSort(s, s); // StringWithSort holding the current suggestion
                            var option = $("<option value=\"" + s + "\" class=\"" + PanelQuestion.charclass(featset) + "\">" + s + "</option>");
                            option.data('sws', item); // Associate the answer string with the <option> element
                            optArray.push(option);
                            if (s === correctAnswer)
                                this_2.vAnswers.push(new Answer(cwyn, item, s, null));
                        }
                        // Sort the options alphabetically
                        optArray.sort(function (a, b) { return StringWithSort.compare(a.data('sws'), b.data('sws')); });
                        // Append optArray to mc_select
                        $.each(optArray, function (ix, o) { return mc_select_1.append(o); });
                        v = cwyn.appendMeTo($('<td></td>'));
                    }
                }
                else if (featType === 'string' || featType === 'ascii') {
                    // Create this HTML structure in the variable v:      From these variables
                    // <span ...>                                          cwyn
                    //   <img ...>                                         cwyn
                    //   <img ...>                                         cwyn
                    //   <img ...>                                         cwyn
                    //   <input type="text" ...>                           vf
                    // </span>                                             cwyn
                    var cwyn = void 0;
                    if (featset.foreignText || featset.transliteratedText) {
                        var vf = $("<input " + firstInput + " data-kbid=\"" + PanelQuestion.kbid++ + "\" type=\"text\" size=\"20\""
                            + (" class=\"" + PanelQuestion.charclass(featset) + "\"")
                            + (" onfocus=\"$('#virtualkbid').appendTo('#row" + (+qoid + 1) + "');VirtualKeyboard.attachInput(this)\">"));
                        firstInput = '';
                        hasForeignInput = true;
                        cwyn = new ComponentWithYesNo(vf, COMPONENT_TYPE.textFieldWithVirtKeyboard);
                    }
                    else {
                        var vf = $('<input type="text" size="20">');
                        cwyn = new ComponentWithYesNo(vf, COMPONENT_TYPE.textField);
                    }
                    cwyn.addKeypressListener();
                    v = cwyn.appendMeTo($('<td></td>'));
                    var trimmedAnswer = correctAnswer.trim()
                        .replace(/&lt;/g, '<')
                        .replace(/&gt;/g, '>')
                        .replace(/&quot;/g, '"')
                        .replace(/&amp;/g, '&'); // Unescape HTML characters in correctAnswer
                    this_2.vAnswers.push(new Answer(cwyn, null, trimmedAnswer, featset.matchregexp));
                }
                else if (featType === 'integer') {
                    // Create this HTML structure in the variable v:      From these variables
                    // <span ...>                                          cwyn
                    //   <img ...>                                         cwyn
                    //   <img ...>                                         cwyn
                    //   <img ...>                                         cwyn
                    //   <input type="number" ...>                         intf
                    // </span>                                             cwyn
                    var intf = $('<input type="number">');
                    var cwyn = new ComponentWithYesNo(intf, COMPONENT_TYPE.textField);
                    cwyn.addKeypressListener();
                    v = cwyn.appendMeTo($('<td></td>'));
                    this_2.vAnswers.push(new Answer(cwyn, null, correctAnswer, null));
                }
                else if (featType.substr(0, 8) === 'list of ') {
                    var subFeatType = featType.substr(8); // Remove "list of "
                    var values = typeinfo.enum2values[subFeatType]; // Possible Emdros feature values
                    var swsValues = []; // StringWithSort equivalents of feature values
                    // Create StringWithSort objects for every feature value
                    for (var i = 0, len = values.length; i < len; ++i)
                        swsValues.push(new StringWithSort(getFeatureValueFriendlyName(subFeatType, values[i], false, false), values[i]));
                    // Sort the values using the optional sorting index in the value strings
                    swsValues.sort(function (a, b) { return StringWithSort.compare(a, b); });
                    // Create this HTML structure in the variable v:      From these variables
                    // <span ...>                                          cwyn
                    //   <img ...>                                         cwyn
                    //   <img ...>                                         cwyn
                    //   <img ...>                                         cwyn
                    //   <table class="list-of">                           selections
                    //     <tr>                                            row
                    //       <td style="text-align:left">
                    //         <input type="checkbox"...>VAL1
                    //       </td>
                    //       <td style="text-align:left">
                    //         <input type="checkbox"...>VAL2
                    //       </td>
                    //       <td style="text-align:left">
                    //         <input type="checkbox"...>VAL3
                    //       </td>
                    //     </tr>                                           row
                    //     ...
                    //   </table>                                          table
                    // </span>                                             cwyn
                    var selections = $('<table class="list-of"></table>');
                    // Arrange in three columns
                    var numberOfItems = swsValues.length; // Number of values
                    var numberOfRows = Math.floor((numberOfItems + 2) / 3); // Number of rows with 3 values each
                    for (var r = 0; r < numberOfRows; ++r) {
                        var row = $('<tr></tr>');
                        for (var c = 0; c < 3; c++) {
                            var ix = r + c * numberOfRows;
                            if (ix < numberOfItems)
                                row.append('<td style="text-align:left">'
                                    + ("<input type=\"checkbox\" value=\"" + swsValues[ix].getInternal() + "\">")
                                    + swsValues[ix].getString()
                                    + '</td>');
                            else
                                row.append('<td></td>');
                        }
                        selections.append(row);
                    }
                    var cwyn = new ComponentWithYesNo(selections, COMPONENT_TYPE.checkBoxes);
                    cwyn.addChangeListener();
                    v = cwyn.appendMeTo($('<td></td>'));
                    this_2.vAnswers.push(new Answer(cwyn, null, correctAnswer, null));
                }
                else {
                    // This is an enumeration feature type, get the collection of possible values
                    var values = typeinfo.enum2values[featType]; // Possible Emdros feature values
                    if (values == null)
                        v = $('<td>QuestionPanel.UnknType</td>');
                    else {
                        // This will be a multiple choice question
                        // Create this HTML structure in the variable v:          From these variables
                        // <span ...>                                              cwyn
                        //   <img ...>                                             cwyn
                        //   <img ...>                                             cwyn
                        //   <img ...>                                             cwyn
                        //   <select class="..." style="direction:ltr">            mc_select
                        //     <option value="NoValueGiven"></option>
                        //     <option value="VAL1" class="...">VAL1</option>      optArray[0]
                        //     <option value="VAL2" class="...">VAL2</option>      optArray[1]
                        //     ...
                        //   </select>                                             mc_select
                        // </span>                                                 cwyn
                        var mc_select_2 = $('<select></select>');
                        var optArray = []; // All the multiple choice options
                        var cwyn = new ComponentWithYesNo(mc_select_2, COMPONENT_TYPE.comboBox1); // Result indicator
                        cwyn.addChangeListener();
                        mc_select_2.append('<option value="NoValueGiven"></option>'); // Empty default choice
                        var correctAnswerFriendly = getFeatureValueFriendlyName(featType, correctAnswer, false, false);
                        var hasAddedOther = false;
                        var correctIsOther = featset.otherValues && featset.otherValues.indexOf(correctAnswer) !== -1;
                        // Loop though all possible values and add the appropriate localized name
                        // or "Other value" to the combo box
                        for (var valix in values) {
                            if (isNaN(+valix))
                                continue; // Not numeric
                            var s = values[+valix]; // Feature value under consideration
                            if (featset.hideValues && featset.hideValues.indexOf(s) !== -1)
                                continue; // Don't show the value s
                            if (featset.otherValues && featset.otherValues.indexOf(s) !== -1) {
                                // The value s is one of the values that make up 'Other value'
                                if (!hasAddedOther) {
                                    hasAddedOther = true;
                                    var item = new StringWithSort('#1000 ' + localize('other_value'), 'othervalue');
                                    var option = $("<option value=\"" + item.getInternal() + "\">" + item.getString() + "</option>");
                                    option.data('sws', item); // Associate the answer string with the <option> element
                                    optArray.push(option);
                                    if (correctIsOther)
                                        this_2.vAnswers.push(new Answer(cwyn, item, localize('other_value'), null));
                                }
                            }
                            else {
                                var sFriendly = getFeatureValueFriendlyName(featType, s, false, false); // Localized value of s
                                var item = new StringWithSort(sFriendly, s); // StringWithSort holding the value s
                                var option = $("<option value=\"" + item.getInternal() + "\">" + item.getString() + "</option>");
                                option.data('sws', item); // Associate the answer string with the <option> element
                                optArray.push(option);
                                if (sFriendly === correctAnswerFriendly) // s is the correct answer
                                    this_2.vAnswers.push(new Answer(cwyn, item, s, null));
                            }
                        }
                        // Sort the options using the optional sorting index in the value strings
                        optArray.sort(function (a, b) { return StringWithSort.compare(a.data('sws'), b.data('sws')); });
                        // Append optArray to mc_select
                        $.each(optArray, function (ix, o) { return mc_select_2.append(o); });
                        v = cwyn.appendMeTo($('<td></td>'));
                    }
                }
                currentRow.append(v);
            };
            var this_2 = this;
            ////////////////////////////////
            // Loop through request features
            for (var rfi in requestFeatures) {
                _loop_2(rfi);
            }
            // currentrow now contains a question item. Set in in '#quiztab'.
            $('#quiztab').append(currentRow);
            if (hasForeignInput) // Add row for virtual keyboard
                $('#quiztab').append("<tr><td colspan=\"" + colcount + "\" id=\"row" + (+qoid + 1) + "\" style=\"text-align:right;\"></td></tr>");
        }
        // Add "Check answer" button
        $('button#check_answer').off('click'); // Remove old handler
        $('button#check_answer').on('click', function () {
            for (var ai in _this.vAnswers) {
                if (isNaN(+ai))
                    continue; // Not numeric
                var a = _this.vAnswers[+ai];
                a.checkIt(false);
            }
        });
        // Add "Show answer" button
        $('button#show_answer').off('click'); // Remove old handler
        $('button#show_answer').on('click', function () {
            for (var ai in _this.vAnswers) {
                if (isNaN(+ai))
                    continue; // Not numeric
                var a = _this.vAnswers[+ai];
                a.showIt();
                a.checkIt(true);
            }
        });
        this.question_stat.start_time = Math.round((new Date()).getTime() / 1000); // Start time for statistcs
    }
    //------------------------------------------------------------------------------------------
    // charclass static method
    //
    // Determines the appropriate CSS class for a given feature.
    //
    // Parameter:
    //     featset: FeatureSetting from the configuration variable.
    // Returns:
    //     The appropriate CSS class for the feature.
    //
    PanelQuestion.charclass = function (featset) {
        return featset.foreignText ? charset.foreignClass
            : featset.transliteratedText ? charset.transliteratedClass : '';
    };
    //------------------------------------------------------------------------------------------
    // updateQuestionStat method
    //
    // Updates the private question statistics with information about the student's answers and
    // returns the statistics.
    //
    // Parameter:
    //     gradingFlag: May the statistics be used for grading the student?
    // Returns:
    //     The question statistics.
    //
    PanelQuestion.prototype.updateQuestionStat = function (gradingFlag) {
        this.question_stat.end_time = Math.round((new Date()).getTime() / 1000);
        for (var i = 0, len = this.vAnswers.length; i < len; ++i) {
            var ans = this.vAnswers[i];
            ans.commitIt(); // Check answer correctness and identify unanswered questions
            this.question_stat.req_feat.correct_answer.push(ans.correctAnswer());
            this.question_stat.req_feat.users_answer.push(ans.usersAnswer());
            this.question_stat.req_feat.users_answer_was_correct.push(ans.usersAnswerWasCorrect());
        }
        this.question_stat.grading = +gradingFlag; // Convert GradingFlag to a number
        return this.question_stat;
    };
    //------------------------------------------------------------------------------------------
    // buildQuizObjectFeatureList method
    //
    // Creates a list of feature=>value maps holding the features for each question object.
    //
    // Returns:
    //     A list of feature/value pairs for each question object.
    //
    PanelQuestion.prototype.buildQuizObjectFeatureList = function () {
        var qoFeatures = []; // The feature/value pairs for each question object
        var hasSeen = []; // Maps id_d => true if the id_d has been seen. (An id_d
        // can occur several times; for example, the id_d of a
        // clause may occur for each monad within the clause.)
        var allmonads = getMonadArray(this.sentence); // All monads in the sentence
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
    PanelQuestion.kbid = 1; // Input field identification for virtual keyboard
    return PanelQuestion;
}());
// -*- js -*-
/* 2013 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk */
/// @file
/// @brief Contains the StringWithSort class.
/// Represents a string with a sort index. Strings with sort indexes are specified in the properties
/// files in as, for example, "#8 Foobar", where 8 is the sort index and "Foobar" is the string. The
/// sort index will be used when ordering objects. Objects of this class can also be constructed from
/// strings without a sort index, in which case comparison reverts to lexical case-free comparison.
/// @see #StringWithSort::compare
var StringWithSort = /** @class */ (function () {
    /// Constructs a StringWithSort object with an internal name.
    /// @param s String with an optional sort index. This string may take the form "#X SSS" where X
    /// is a non-negative integer and SSS is the string proper; in this case X will be used as the
    /// sort index. Alternatively this parameter may be specified as a string without a sort index,
    /// in which case the sort index will be set to -1 and comparison will be lexical.
    /// @param internal The internal (that is, languague independent) name for this value.
    function StringWithSort(s, internal) {
        if (internal === void 0) { internal = null; }
        if (s.length > 0 && s.charAt(0) === '#') {
            var sp = s.indexOf(' ');
            this.sort = +s.substring(1, sp);
            this.str = s.substring(sp + 1);
        }
        else {
            this.sort = -1;
            this.str = s;
        }
        this.internal = internal;
    }
    /// Extracts the proper string part of a string with an optional sort index.
    /// @param s String with an optional sort index.
    /// @return The proper string part.
    StringWithSort.stripSortIndex = function (s) {
        return (s.length > 0 && s.charAt(0) === '#')
            ? s.substring(s.indexOf(' ') + 1)
            : s;
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
        }
        else
            return sws1.sort < sws2.sort ? -1 : 1; // Note: No zero here because equality is handled above
    };
    return StringWithSort;
}());
// -*- js -*-
var ShowFeatStatistics = /** @class */ (function () {
    function ShowFeatStatistics() {
        this.names = [];
        this.values = [];
    }
    return ShowFeatStatistics;
}());
var ReqFeatStatistics = /** @class */ (function () {
    function ReqFeatStatistics() {
        this.names = [];
        this.correct_answer = [];
        this.users_answer = [];
        this.users_answer_was_correct = [];
    }
    return ReqFeatStatistics;
}());
var QuestionStatistics = /** @class */ (function () {
    function QuestionStatistics() {
        this.show_feat = new ShowFeatStatistics();
        this.req_feat = new ReqFeatStatistics();
    }
    return QuestionStatistics;
}());
var QuizStatistics = /** @class */ (function () {
    function QuizStatistics(qid) {
        this.questions = [];
        this.quizid = qid;
    }
    return QuizStatistics;
}());
// -*- js -*-
/* 2013 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk */
/// <reference path="statistics.ts" />
var tOutInner;
var tOutOuter;
var tDialog;
var Quiz = /** @class */ (function () {
    function Quiz(qid) {
        var _this = this;
        this.currentDictIx = -1; ///< Current index in the array of dictionaries provided by the server
        this.currentPanelQuestion = null;
        this.xx = 8;
        this.quiz_statistics = new QuizStatistics(qid);
        $('#quiztab').append('<tr id="quiztabhead"></tr>');
        $('button#next_question').click(function () { return _this.nextQuestion(); });
        $('button#finish').click(function () { return _this.finishQuiz(); });
        $('button#finishNoStats').click(function () { return _this.finishQuizNoStats(); });
    }
    /// Replaces the current quiz question with the next one, if any.
    Quiz.prototype.nextQuestion = function () {
        var timeouter = 600000;
        var timeinner = 600000;
        var timeoutinner = 28000;
        var fun1 = function () {
            window.clearTimeout(tOutOuter);
            window.clearTimeout(tOutInner);
            window.clearTimeout(tDialog);
            tOutInner = window.setTimeout(function () {
                heartbeatDialog.dialog('open');
                tDialog = window.setTimeout(function () {
                    heartbeatDialog.dialog('close');
                    $('#next_question').fadeOut();
                    window.clearTimeout(tOutOuter);
                    window.clearTimeout(tOutInner);
                    return;
                }, timeoutinner);
            }, timeinner);
            var heartbeatDialog = $('<div></div>')
                .html(localize('done_practicing'))
                .dialog({
                autoOpen: false,
                title: localize('stop_practicing'),
                resizable: true,
                show: 'fade',
                hide: 'explode',
                height: 140,
                modal: true,
                buttons: [{
                        text: localize('go_on'),
                        click: function () {
                            $(this).dialog('close');
                            window.clearTimeout(tOutOuter);
                            window.clearTimeout(tOutInner);
                            window.clearTimeout(tDialog);
                            tOutOuter = window.setTimeout(fun1, timeouter);
                            return;
                        }
                    }]
            });
        };
        fun1();
        if (this.currentPanelQuestion !== null)
            // Update statistics
            this.quiz_statistics.questions.push(this.currentPanelQuestion.updateQuestionStat(true));
        if (++this.currentDictIx < dictionaries.sentenceSets.length) {
            $('#virtualkbid').appendTo('#virtualkbcontainer'); // Move the keyboard back to its initial position
            $('#textarea').empty();
            $('#quiztab').empty();
            $('#quiztab').append('<tr id="quiztabhead"></tr>');
            var currentDict = new Dictionary(dictionaries, this.currentDictIx, quizdata);
            $('#quizdesc').html(quizdata.desc);
            $('#quizdesc').find('a').attr('target', '_blank'); // Force all hyperlinks to open new browser tab
            if (supportsProgress)
                $('progress#progress').attr('value', this.currentDictIx + 1).attr('max', dictionaries.sentenceSets.length);
            else
                $('div#progressbar').progressbar({ value: this.currentDictIx + 1, max: dictionaries.sentenceSets.length });
            $('#progresstext').html((this.currentDictIx + 1) + '/' + dictionaries.sentenceSets.length);
            this.currentPanelQuestion = new PanelQuestion(quizdata, currentDict);
            if (this.currentDictIx + 1 === dictionaries.sentenceSets.length)
                $('button#next_question').attr('disabled', 'disabled');
            if (quizdata.quizFeatures.useVirtualKeyboard &&
                // TODO: This should not be needed:
                (charset.keyboardName === 'IL' || charset.keyboardName === 'GR')) {
                VirtualKeyboard.setVisibleLayoutCodes([charset.keyboardName]);
                VirtualKeyboard.toggle('firstinput', 'virtualkbid');
            }
        }
        else
            alert('No more questions');
        util.resetCheckboxCounters();
        $('.grammarselector input:enabled:checked').trigger('change'); // Make sure grammar is displayed for relevant checkboxes
    };
    Quiz.prototype.finishQuiz = function () {
        if (quizdata.quizid == -1) // User not logged in
            window.location.replace(site_url + 'text/select_quiz'); // Go to quiz selection
        else {
            if (this.currentPanelQuestion === null)
                alert('System error: No current question panel');
            else
                this.quiz_statistics.questions.push(this.currentPanelQuestion.updateQuestionStat(true));
            // Send statistics to server
            $('.grammarselector').empty();
            $('#textcontainer').html('<p>' + localize('sending_statistics') + '</p>');
            $.post(site_url + 'statistics/update_stat', this.quiz_statistics)
                .done(function () { return window.location.replace(site_url + 'text/select_quiz'); }) // Go to quiz selection
                .fail(function (jqXHR, textStatus, errorThrow) {
                $('#textcontainer')
                    .removeClass('textcontainer-background')
                    .addClass('alert alert-danger')
                    .html('<h1>' + localize('error_response') + '</h1><p>{0}</p>'.format(errorThrow));
            });
        }
    };
    Quiz.prototype.finishQuizNoStats = function () {
        if (quizdata.quizid == -1) // User not logged in
            window.location.replace(site_url + 'text/select_quiz'); // Go to quiz selection
        else {
            if (this.currentPanelQuestion === null)
                alert('System error: No current question panel');
            else
                this.quiz_statistics.questions.push(this.currentPanelQuestion.updateQuestionStat(false));
            // Send statistics to server
            $('.grammarselector').empty();
            $('#textcontainer').html('<p>' + localize('sending_statistics') + '</p>');
            $.post(site_url + 'statistics/update_stat', this.quiz_statistics)
                .done(function () { return window.location.replace(site_url + 'text/select_quiz'); }) // Go to quiz selection
                .fail(function (jqXHR, textStatus, errorThrow) {
                $('#textcontainer')
                    .removeClass('textcontainer-background')
                    .addClass('alert alert-danger')
                    .html('<h1>' + localize('error_response') + '</h1><p>{0}</p>'.format(errorThrow));
            });
        }
    };
    return Quiz;
}());
// -*- js -*-
/* 2015 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk */
// The idea for some of the following code is taken from
// http://stackoverflow.com/questions/18575582/how-to-detect-responsive-breakpoints-of-twitter-bootstrap-3-using-javascript
// This code provides information about Bootstrap's current concept of the window size
var resizer;
(function (resizer) {
    // Returns the current window size, either 'xs', 'sm', 'md', or 'lg'.
    function getWindowSize() {
        return $('.device-sizer:visible').attr('data-size');
    }
    resizer.getWindowSize = getWindowSize;
    // Checks if the current window size is siz (which is either 'xs', 'sm', 'md', or 'lg')
    function sizeIs(siz) {
        return $('.device-is-' + siz).is(':visible');
    }
    resizer.sizeIs = sizeIs;
    var timers = {};
    // Specify a function that listens for window resizings.
    // Use a separate uniqueId for each instance that listens for a resize.
    function addResizeListener(callback, data, uniqueId) {
        // When the window resizes, wait for 500 ms and if no further resize occurs, call the callback function
        $(window).resize(function () {
            // If the window was recently resized, restart the timer, otherwize start the timer
            if (timers[uniqueId])
                clearTimeout(timers[uniqueId]);
            timers[uniqueId] = setTimeout(function () { return callback(data); }, 500); // Call callback in 500 ms
        });
    }
    resizer.addResizeListener = addResizeListener;
    $(function () {
        // Insert size detectors before </body>
        var sizes = ['xs', 'sm', 'md', 'lg'];
        for (var i = 0; i < sizes.length; ++i)
            $('body').append('<div class="visible-{0}-block device-is-{0} device-sizer" data-size="{0}"></div>'
                .format(sizes[i]));
    });
})(resizer || (resizer = {}));
// -*- js -*-
// Copyright © 2018 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk
/// <reference path="util.ts" />
/// <reference path="configuration.ts" />
/// <reference path="sentencegrammar.ts" />
/// <reference path="grammarselectionbox.ts" />
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
/// <reference path="resizer.ts" />
var supportsProgress; // Does the browser support <progress>?
var charset; // Current character set
var inQuiz; // Are we displaying a quiz?
var quiz; // Current quiz
var accordion_width; // Width of the grammar selector accordions
var indentation_width; // Width of indentation of ETCBC4 clause atoms
//****************************************************************************************************
// Main code executed when the page has been loaded.
$(function () {
    inQuiz = $('#quiztab').length > 0;
    // Does the browser support <progress>?
    // (Use two statements because jquery.d.ts does not recognize .max)
    var x = document.createElement('progress');
    supportsProgress = x.max != undefined; // Thanks to http://lab.festiz.com/progressbartest/index.htm
    configuration.maxLevels = configuration.sentencegrammar.length + 1; // Include patriarch level
    // Set up CSS classes for text.
    charset = new Charset(configuration.charSet);
    $('#textarea').addClass(charset.isRtl ? 'rtl' : 'ltr');
    // Add polymorphic function to the contents of configuration.sentencegrammar
    for (var i in configuration.sentencegrammar) {
        if (isNaN(+i))
            continue; // Not numeric
        addMethodsSgi(configuration.sentencegrammar[+i], configuration.sentencegrammar[+i].objType);
    }
    // Create HTML the grammar selection box
    var generateCheckboxes = new GrammarSelectionBox();
    $('#gramselect').append(generateCheckboxes.generateHtml());
    generateCheckboxes.setHandlers();
    GrammarSelectionBox.clearBoxes(false);
    accordion_width = GrammarSelectionBox.buildGrammarAccordion();
    if (inQuiz) {
        if (supportsProgress)
            $('div#progressbar').hide();
        else
            $('progress#progress').hide();
        // Run the exercise
        quiz = new Quiz(quizdata.quizid);
        quiz.nextQuestion();
    }
    else {
        // Display text
        $('#cleargrammar').on('click', function () { GrammarSelectionBox.clearBoxes(true); });
        // Generate the text to display
        var currentDict = new Dictionary(dictionaries, 0, null);
        currentDict.generateSentenceHtml(null);
        $('.grammarselector input:enabled:checked').trigger('change'); // Make sure the relevant features are displayed
    }
});
