var __extends = (this && this.__extends) || (function () {
    var extendStatics = function (d, b) {
        extendStatics = Object.setPrototypeOf ||
            ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
            function (d, b) { for (var p in b) if (Object.prototype.hasOwnProperty.call(b, p)) d[p] = b[p]; };
        return extendStatics(d, b);
    };
    return function (d, b) {
        if (typeof b !== "function" && b !== null)
            throw new TypeError("Class extends value " + String(b) + " is not a constructor or null");
        extendStatics(d, b);
        function __() { this.constructor = d; }
        d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
    };
})();
String.prototype.format = function () {
    var args = arguments;
    return this.replace(/{(\d+)}/g, function (match, num) {
        return typeof args[num] != 'undefined'
            ? args[num]
            : match;
    });
};
if (!String.prototype.trim) {
    String.prototype.trim = function () {
        return this.replace(/^\s+|\s+$/g, '');
    };
}
var util;
(function (util) {
    var FollowerBox = (function () {
        function FollowerBox(level, idstring) {
            this.level = level;
            this.idstring = idstring;
            this.is_explicit = false;
            this.count = 0;
            this.addToResetChain();
        }
        FollowerBox.prototype.addToResetChain = function () {
            FollowerBox.resetChain.push(this);
        };
        FollowerBox.resetCheckboxCounters = function () {
            for (var i in this.resetChain) {
                if (isNaN(+i))
                    continue;
                this.resetChain[i].count = 0;
            }
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
        FollowerBox.resetChain = [];
        return FollowerBox;
    }());
    util.FollowerBox = FollowerBox;
    var BorderFollowerBox = (function (_super) {
        __extends(BorderFollowerBox, _super);
        function BorderFollowerBox(level) {
            return _super.call(this, level, "#lev".concat(level, "_sb_cb")) || this;
        }
        BorderFollowerBox.prototype.setit = function (val) {
            if (val) {
                $(".lev".concat(this.level, " > .gram")).removeClass('dontshowit').addClass('showit');
                $(".lev".concat(this.level)).removeClass('dontshowborder').addClass('showborder');
            }
            else {
                $(".lev".concat(this.level, " > .gram")).removeClass('showit').addClass('dontshowit');
                $(".lev".concat(this.level)).removeClass('showborder').addClass('dontshowborder');
            }
        };
        return BorderFollowerBox;
    }(FollowerBox));
    util.BorderFollowerBox = BorderFollowerBox;
    var SeparateLinesFollowerBox = (function (_super) {
        __extends(SeparateLinesFollowerBox, _super);
        function SeparateLinesFollowerBox(level) {
            return _super.call(this, level, "#lev".concat(level, "_seplin_cb")) || this;
        }
        SeparateLinesFollowerBox.prototype.setit = function (val) {
            var oldSepLin = val ? 'noseplin' : 'seplin';
            var newSepLin = val ? 'seplin' : 'noseplin';
            $(".notdummy.lev".concat(this.level)).removeClass(oldSepLin).addClass(newSepLin);
        };
        return SeparateLinesFollowerBox;
    }(FollowerBox));
    util.SeparateLinesFollowerBox = SeparateLinesFollowerBox;
    var WordSpaceFollowerBox = (function (_super) {
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
    var AddBetween = (function () {
        function AddBetween(separator) {
            this.separator = separator;
            this.first = true;
        }
        AddBetween.prototype.getStr = function () {
            if (this.first) {
                this.first = false;
                return '';
            }
            else
                return this.separator;
        };
        AddBetween.prototype.reset = function () {
            this.first = true;
        };
        return AddBetween;
    }());
    util.AddBetween = AddBetween;
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
})(util || (util = {}));
function getObjectSetting(otype) {
    return configuration.objectSettings[otype];
}
function getFeatureSetting(otype, feature) {
    if (feature === 'visual') {
        otype = configuration.objHasSurface;
        feature = configuration.surfaceFeature;
    }
    var io = feature.indexOf('_TYPE_');
    if (io != -1)
        feature = feature.substr(0, io);
    return getObjectSetting(otype).featuresetting[feature];
}
function addMethods(obj, classname, param) {
    for (var f in classname.prototype) {
        if (f === 'constructor')
            continue;
        obj[f] = classname.prototype[f];
    }
    obj.pseudoConstructor && obj.pseudoConstructor(param);
}
function addMethodsSgi(sgi, param) {
    addMethods(sgi, eval(sgi.mytype), param);
    if (sgi.items) {
        for (var i in sgi.items) {
            if (isNaN(+i))
                continue;
            addMethodsSgi(sgi.items[+i], param);
        }
    }
}
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
    GrammarGroup.prototype.walkFeatureNames = function (objType, callback) {
        callback(WHAT.groupstart, objType, objType, this.name, l10n.grammargroup[objType][this.name], this);
        for (var i in this.items) {
            if (isNaN(+i))
                continue;
            this.items[+i].walkFeatureNames(objType, callback);
        }
        callback(WHAT.groupend, objType, objType, this.name, null, this);
    };
    GrammarGroup.prototype.walkFeatureValues = function (monob, mix, objType, abbrev, callback) {
        callback(WHAT.groupstart, objType, objType, this.name, null, this);
        for (var i in this.items) {
            if (isNaN(+i))
                continue;
            this.items[+i].walkFeatureValues(monob, mix, objType, abbrev, callback);
        }
        callback(WHAT.groupend, objType, objType, this.name, null, this);
    };
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
}());
var GrammarSubFeature = (function () {
    function GrammarSubFeature() {
    }
    GrammarSubFeature.prototype.getFeatValPart = function (monob, objType) {
        return l10n.grammarsubfeature[objType][this.name][monob.mo.features[this.name]];
    };
    GrammarSubFeature.prototype.containsFeature = function (f) {
        return this.name === f;
    };
    return GrammarSubFeature;
}());
var SentenceGrammar = (function (_super) {
    __extends(SentenceGrammar, _super);
    function SentenceGrammar() {
        return _super !== null && _super.apply(this, arguments) || this;
    }
    SentenceGrammar.prototype.walkFeatureNames = function (objType, callback) {
        for (var i in this.items) {
            if (isNaN(+i))
                continue;
            this.items[+i].walkFeatureNames(objType, callback);
        }
    };
    SentenceGrammar.prototype.walkFeatureValues = function (monob, mix, objType, abbrev, callback) {
        for (var i in this.items) {
            if (isNaN(+i))
                continue;
            this.items[+i].walkFeatureValues(monob, mix, objType, abbrev, callback);
        }
    };
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
}(GrammarGroup));
var GrammarMetaFeature = (function () {
    function GrammarMetaFeature() {
    }
    GrammarMetaFeature.prototype.walkFeatureNames = function (objType, callback) {
        callback(WHAT.metafeature, objType, objType, this.name, l10n.grammarmetafeature[objType][this.name], this);
    };
    GrammarMetaFeature.prototype.walkFeatureValues = function (monob, mix, objType, abbrev, callback) {
        var res = '';
        for (var i in this.items) {
            if (isNaN(+i))
                continue;
            res += this.items[+i].getFeatValPart(monob, objType);
        }
        callback(WHAT.metafeature, objType, objType, this.name, res, this);
    };
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
}());
var GrammarFeature = (function () {
    function GrammarFeature() {
    }
    GrammarFeature.prototype.pseudoConstructor = function (objType) {
        var io = this.name.indexOf(':');
        if (io != -1) {
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
    GrammarFeature.prototype.walkFeatureNames = function (objType, callback) {
        var locname = l10n.grammarfeature && l10n.grammarfeature[this.realObjectType] && l10n.grammarfeature[this.realObjectType][this.realFeatureName]
            ? l10n.grammarfeature[this.realObjectType][this.realFeatureName]
            : l10n.emdrosobject[this.realObjectType][this.realFeatureName];
        callback(WHAT.feature, this.realObjectType, objType, this.realFeatureName, locname, this);
    };
    GrammarFeature.prototype.icon2class = function (icon) {
        return l_icon_map[icon] ? l_icon_map[icon] : l_icon_map['l-icon-default'];
    };
    GrammarFeature.prototype.walkFeatureValues = function (monob, mix, objType, abbrev, callback) {
        var featType = typeinfo.obj2feat[this.realObjectType][this.realFeatureName];
        var io = this.realFeatureName.indexOf('_TYPE_');
        var realRealFeatureName = io == -1 ? this.realFeatureName : this.realFeatureName.substr(0, io);
        var res = this.isSubObj
            ? monob.subobjects[mix][0].features[realRealFeatureName]
            : (monob.mo.features ? monob.mo.features[realRealFeatureName] : '');
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
                    var res2 = '';
                    for (var i = 0; i < res.length; ++i)
                        res2 += "<a style=\"padding-right:1px;padding-left:1px;\" href=\"".concat(res[i]['url'], "\" target=\"_blank\">")
                            + "<span class=\"".concat(this.icon2class(res[i]['icon']), "\" aria-hidden=\"true\"></span></a>");
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
    GrammarFeature.prototype.containsFeature = function (f) {
        return this.name === f;
    };
    return GrammarFeature;
}());
function getSentenceGrammarFor(oType) {
    for (var i = 0; i < configuration.sentencegrammar.length; ++i)
        if (configuration.sentencegrammar[i].objType === oType)
            return configuration.sentencegrammar[i];
    return null;
}
function getSessionValue() {
    var sessionValue;
    try {
        sessionValue = JSON.parse(sessionStorage.getItem(configuration.propertiesName));
    }
    catch (e) {
        sessionValue = {};
    }
    if (!sessionValue)
        sessionValue = {};
    return sessionValue;
}
function setSessionValue(sessionValue) {
    sessionStorage.setItem(configuration.propertiesName, JSON.stringify(sessionValue));
}
function setOneSessionValue(key, value) {
    var sessionValue = getSessionValue();
    sessionValue[key] = value;
    setSessionValue(sessionValue);
}
var GrammarSelectionBox = (function () {
    function GrammarSelectionBox() {
        this.checkboxes = '';
        this.subgroupgrammartabs = '';
        this.subgroupgrammardivs = '';
        this.addBr = new util.AddBetween('<br>');
        this.borderBoxes = [];
        this.separateLinesBoxes = [];
        this.seenFreqRank = false;
    }
    GrammarSelectionBox.adjustDivLevWidth = function (level) {
        $(".showborder.lev".concat(level)).each(function (index) {
            $(this).css('width', 'auto');
            var w = $(this).find('> .gram').width();
            if ($(this).width() < w + 10)
                $(this).width(w + 10);
        });
    };
    GrammarSelectionBox.prototype.generatorCallback = function (whattype, objType, origObjType, featName, featNameLoc, sgiObj) {
        switch (whattype) {
            case WHAT.groupstart:
                if (!this.hasSeenGrammarGroup) {
                    this.hasSeenGrammarGroup = true;
                    this.subgroupgrammartabs += "<div id=\"grammargroup\"><ul>";
                }
                this.subgroupgrammartabs += "<li><a class=\"grammargroup\" href=\"#".concat(getHtmlAttribFriendlyName(featName), "\"><h3>").concat(featNameLoc, "</h3></a></li>");
                this.subgroupgrammardivs += "<div id=\"".concat(getHtmlAttribFriendlyName(featName), "\">");
                this.subgroupgrammardivs += "<div id=\"grammarbuttongroup\">";
                this.addBr.reset();
                break;
            case WHAT.groupend:
                this.subgroupgrammardivs += '</div>';
                if (this.seenFreqRank && !inQuiz) {
                    this.subgroupgrammardivs += "<div class=\"color-limit\"><span class=\"color-limit-prompt\">".concat(localize('word_frequency_color_limit'), "</span><input id=\"color-limit\" type=\"number\" style=\"width:5em\"></div>");
                    this.seenFreqRank = false;
                }
                this.subgroupgrammardivs += '</div>';
                break;
            case WHAT.feature:
            case WHAT.metafeature:
                if (objType === "clause_atom" && featName === "code_TYPE_text") {
                    var disabled_test = mayShowFeature(objType, origObjType, featName, sgiObj, true) ? '' : 'disabled';
                }
                var disabled = mayShowFeature(objType, origObjType, featName, sgiObj) ? '' : 'disabled';
                if (this.hasSeenGrammarGroup) {
                    if (objType === "word" && featName === "frequency_rank")
                        this.seenFreqRank = true;
                    this.subgroupgrammardivs += "<div class=\"selectbutton\"><input id=\"".concat(objType, "_").concat(featName, "_cb\" type=\"checkbox\" ").concat(disabled, "><label class=\"").concat(disabled, "\" for=\"").concat(objType, "_").concat(featName, "_cb\">").concat(featNameLoc, "</label></div>");
                }
                else {
                    if (objType === "clause_atom" && featName === "code_TYPE_text") {
                        console.log('disabled: ', disabled);
                    }
                    this.checkboxes += "<div class=\"selectbutton\"><input id=\"".concat(objType, "_").concat(featName, "_cb\" type=\"checkbox\" ").concat(disabled, "><label class=\"").concat(disabled, "\" for=\"").concat(objType, "_").concat(featName, "_cb\">").concat(featNameLoc, "</label></div>");
                }
                break;
        }
    };
    GrammarSelectionBox.prototype.makeInitCheckBoxForObj = function (level) {
        if (level == 0) {
            if (charset.isHebrew) {
                return "<div class=\"selectbutton\"><input id=\"ws_cb\" type=\"checkbox\">" +
                    "<label for=\"ws_cb\">".concat(localize('word_spacing'), "</label></div>");
            }
            else
                return '';
        }
        else {
            return "<div class=\"selectbutton\"><input id=\"lev".concat(level, "_seplin_cb\" type=\"checkbox\">") +
                "<label for=\"lev".concat(level, "_seplin_cb\">").concat(localize('separate_lines'), "</label></div>") +
                "<div class=\"selectbutton\"><input id=\"lev".concat(level, "_sb_cb\" type=\"checkbox\">") +
                "<label for=\"lev".concat(level, "_sb_cb\">").concat(localize('show_border'), "</label></div>");
        }
    };
    GrammarSelectionBox.prototype.generateHtml = function () {
        var _this = this;
        this.checkboxes += "<ul>";
        for (var level in configuration.sentencegrammar) {
            var leveli = +level;
            if (isNaN(leveli))
                continue;
            var objType = configuration.sentencegrammar[leveli].objType;
            this.checkboxes += "<li><a class=\"gramtabs\" href=\"#".concat(getHtmlAttribFriendlyName(objType), "\"><h3>").concat(getObjectFriendlyName(objType), "</h3></a></li>");
        }
        this.checkboxes += "</ul>";
        for (var level in configuration.sentencegrammar) {
            var leveli = +level;
            if (isNaN(leveli))
                continue;
            var objType = configuration.sentencegrammar[leveli].objType;
            this.checkboxes += "<div id=\"".concat(getHtmlAttribFriendlyName(objType), "\">");
            this.checkboxes += "<div class=\"objectlevel\">";
            this.checkboxes += "<div id=\"grammarbuttongroup\">";
            this.checkboxes += this.makeInitCheckBoxForObj(leveli);
            this.hasSeenGrammarGroup = false;
            configuration.sentencegrammar[leveli]
                .walkFeatureNames(objType, function (whattype, objType, origObjType, featName, featNameLoc, sgiObj) { return _this.generatorCallback(whattype, objType, origObjType, featName, featNameLoc, sgiObj); });
            if (this.hasSeenGrammarGroup)
                this.checkboxes += '</div>';
            this.checkboxes += this.subgroupgrammartabs + '</ul>' + this.subgroupgrammardivs + '</div>';
            this.subgroupgrammartabs = '';
            this.subgroupgrammardivs = '';
            this.checkboxes += '</div></div>';
        }
        this.checkboxes += "<button class=\"btn btn-clear\" id=\"cleargrammar\">".concat(localize('clear_grammar'), "</button>");
        return this.checkboxes;
    };
    GrammarSelectionBox.prototype.setHandlerCallback = function (whattype, objType, featName, featNameLoc, leveli) {
        var _this = this;
        if (whattype != WHAT.feature && whattype != WHAT.metafeature)
            return;
        if (leveli === 0) {
            $("#".concat(objType, "_").concat(featName, "_cb")).change(function (e) {
                var isManual = [];
                for (var _i = 1; _i < arguments.length; _i++) {
                    isManual[_i - 1] = arguments[_i];
                }
                if ($(e.currentTarget).prop('checked')) {
                    if (!inQuiz && isManual[0] != 'manual') {
                        setOneSessionValue($(e.currentTarget).prop('id'), true);
                    }
                    $(".wordgrammar.".concat(featName)).removeClass('dontshowit').addClass('showit');
                    _this.wordSpaceBox.implicit(true);
                }
                else {
                    if (!inQuiz && isManual[0] != 'manual') {
                        setOneSessionValue($(e.currentTarget).prop('id'), false);
                    }
                    $(".wordgrammar.".concat(featName)).removeClass('showit').addClass('dontshowit');
                    _this.wordSpaceBox.implicit(false);
                }
                for (var lev = 1; lev < configuration.maxLevels - 1; ++lev)
                    GrammarSelectionBox.adjustDivLevWidth(lev);
            });
        }
        else {
            $("#".concat(objType, "_").concat(featName, "_cb")).change(function (e) {
                var isManual = [];
                for (var _i = 1; _i < arguments.length; _i++) {
                    isManual[_i - 1] = arguments[_i];
                }
                if ($(e.currentTarget).prop('checked')) {
                    if (!inQuiz && isManual[0] != 'manual') {
                        setOneSessionValue($(e.currentTarget).prop('id'), true);
                    }
                    $(".xgrammar.".concat(objType, "_").concat(featName)).removeClass('dontshowit').addClass('showit');
                    if (configuration.databaseName == 'ETCBC4' && leveli == 2 && objType == "clause_atom" && featName == "tab") {
                        _this.separateLinesBoxes[leveli].implicit(true);
                        $('.lev2').css(charset.isRtl ? 'padding-right' : 'padding-left', indentation_width + 'px').css('text-indent', -indentation_width + 'px');
                    }
                    else
                        _this.borderBoxes[leveli].implicit(true);
                }
                else {
                    if (!inQuiz && isManual[0] != 'manual') {
                        setOneSessionValue($(e.currentTarget).prop('id'), false);
                    }
                    $(".xgrammar.".concat(objType, "_").concat(featName)).removeClass('showit').addClass('dontshowit');
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
    GrammarSelectionBox.prototype.setHandlers = function () {
        var _this = this;
        var _loop_1 = function (level) {
            var leveli = +level;
            if (isNaN(leveli))
                return "continue";
            var sg = configuration.sentencegrammar[leveli];
            if (leveli === 0) {
                this_1.wordSpaceBox = new util.WordSpaceFollowerBox(leveli);
                $('#ws_cb').change(function (e) {
                    var isManual = [];
                    for (var _i = 1; _i < arguments.length; _i++) {
                        isManual[_i - 1] = arguments[_i];
                    }
                    if ($(e.currentTarget).prop('checked')) {
                        if (!inQuiz && isManual[0] != 'manual') {
                            setOneSessionValue($(e.currentTarget).prop('id'), true);
                        }
                        _this.wordSpaceBox.explicit(true);
                    }
                    else {
                        if (!inQuiz && isManual[0] != 'manual') {
                            setOneSessionValue($(e.currentTarget).prop('id'), false);
                        }
                        _this.wordSpaceBox.explicit(false);
                    }
                    for (var lev = 1; lev < configuration.maxLevels - 1; ++lev)
                        GrammarSelectionBox.adjustDivLevWidth(lev);
                });
            }
            else {
                this_1.separateLinesBoxes[leveli] = new util.SeparateLinesFollowerBox(leveli);
                $("#lev".concat(leveli, "_seplin_cb")).change(leveli, function (e) {
                    var isManual = [];
                    for (var _i = 1; _i < arguments.length; _i++) {
                        isManual[_i - 1] = arguments[_i];
                    }
                    if ($(e.currentTarget).prop('checked')) {
                        if (!inQuiz && isManual[0] != 'manual') {
                            setOneSessionValue($(e.currentTarget).prop('id'), true);
                        }
                        _this.separateLinesBoxes[e.data].explicit(true);
                    }
                    else {
                        if (!inQuiz && isManual[0] != 'manual') {
                            setOneSessionValue($(e.currentTarget).prop('id'), false);
                        }
                        _this.separateLinesBoxes[e.data].explicit(false);
                    }
                });
                this_1.borderBoxes[leveli] = new util.BorderFollowerBox(leveli);
                $("#lev".concat(leveli, "_sb_cb")).change(leveli, function (e) {
                    var isManual = [];
                    for (var _i = 1; _i < arguments.length; _i++) {
                        isManual[_i - 1] = arguments[_i];
                    }
                    if ($(e.currentTarget).prop('checked')) {
                        if (!inQuiz && isManual[0] != 'manual') {
                            setOneSessionValue($(e.currentTarget).prop('id'), true);
                        }
                        _this.borderBoxes[e.data].explicit(true);
                    }
                    else {
                        if (!inQuiz && isManual[0] != 'manual') {
                            setOneSessionValue($(e.currentTarget).prop('id'), false);
                        }
                        _this.borderBoxes[e.data].explicit(false);
                    }
                    GrammarSelectionBox.adjustDivLevWidth(e.data);
                });
            }
            sg.walkFeatureNames(sg.objType, function (whattype, objType, origObjType, featName, featNameLoc, sgiObj) { return _this.setHandlerCallback(whattype, objType, featName, featNameLoc, leveli); });
        };
        var this_1 = this;
        for (var level in configuration.sentencegrammar) {
            _loop_1(level);
        }
    };
    GrammarSelectionBox.clearBoxes = function (force) {
        if (!inQuiz) {
            var sessionValue = getSessionValue();
            if (force) {
                for (var i in sessionValue) {
                    if (i === 'color-limit')
                        $('#color-limit').val(9999).trigger('change', 'manual');
                    else if (sessionValue[i]) {
                        $('#' + i).prop('checked', false).trigger('change', 'manual');
                    }
                }
                sessionStorage.removeItem(configuration.propertiesName);
            }
            else {
                $('#color-limit').val(9999);
                for (var i in sessionValue) {
                    if (i === 'color-limit')
                        $('#color-limit').val(sessionValue[i]);
                    else
                        $('#' + i).prop('checked', sessionValue[i]);
                }
            }
        }
        else {
            if (force) {
                var IDs_1 = [];
                $('#grammarbuttongroup .selectbutton input:checked').each(function () { IDs_1.push($(this).attr('id')); });
                for (var i in IDs_1) {
                    $('#' + IDs_1[i]).prop('checked', false);
                    $('#' + IDs_1[i]).trigger('change', 'manual');
                }
            }
        }
    };
    GrammarSelectionBox.setColorizeHandler = function () {
        var colorizeFunction = function (event) {
            var isManual = [];
            for (var _i = 1; _i < arguments.length; _i++) {
                isManual[_i - 1] = arguments[_i];
            }
            var collim = +$('#color-limit').val();
            $('.textdisplay').each(function () {
                $(this).toggleClass('colorized', +$(this).siblings('.frequency_rank').text() > collim);
            });
            if (isManual[0] != 'manual')
                setOneSessionValue('color-limit', collim);
        };
        $('#color-limit').change(colorizeFunction);
        var timeoutId = 0;
        $('#color-limit').keyup(function () {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(colorizeFunction, 20);
        });
    };
    GrammarSelectionBox.buildGrammarAccordion = function () {
        var tabs1 = $('#myview').tabs({
            heightStyle: 'content',
            collapsible: true
        });
        var tabs2 = $('#gramtabs').tabs({
            heightStyle: 'content',
            collapsible: true
        });
        var tabs3 = $('#grammargroup').tabs({
            heightStyle: 'content',
            collapsible: true
        });
        var max_width = 'auto';
        tabs1.tabs('option', 'active', false);
        tabs2.tabs('option', 'active', false);
        tabs3.tabs('option', 'active', false);
        return max_width;
    };
    return GrammarSelectionBox;
}());
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
            case 'latin':
                this.foreignClass = 'latin';
                this.isHebrew = false;
                this.isRtl = false;
                break;
        }
    }
    return Charset;
}());
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
function containsMonad(ms, monad) {
    for (var i in ms.segments) {
        if (isNaN(+i))
            continue;
        var mp = ms.segments[+i];
        if (monad >= mp.low && monad <= mp.high)
            return true;
    }
    return false;
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
var urlTypeString = {
    'u': 'click_for_web_site',
    'v': 'click_for_video',
    'd': 'click_for_document'
};
var DisplayMonadObject = (function () {
    function DisplayMonadObject(mo, objType, level) {
        this.displayedMo = mo;
        if (mo.displayers == undefined)
            mo.displayers = [this];
        else
            mo.displayers.push(this);
        this.objType = objType;
        this.level = level;
    }
    DisplayMonadObject.prototype.containedIn = function (mo) {
        return this.range.low >= mo.range.low && this.range.high <= mo.range.high;
    };
    return DisplayMonadObject;
}());
var DisplaySingleMonadObject = (function (_super) {
    __extends(DisplaySingleMonadObject, _super);
    function DisplaySingleMonadObject(smo, objType, inQuiz) {
        var _this = _super.call(this, smo, objType, 0) || this;
        _this.inQuiz = inQuiz;
        _this.monad = smo.mo.monadset.segments[0].low;
        _this.range = { low: _this.monad, high: _this.monad };
        _this.mix = 0;
        return _this;
    }
    DisplaySingleMonadObject.prototype.generateHtml = function (qd, sentenceTextArr, quizMonads) {
        var smo = this.displayedMo;
        var uhSize = smo.bcv.length;
        var chapter = null;
        var verse = null;
        var refs = null;
        var urls = null;
        if (uhSize != 0) {
            if (!this.inQuiz) {
                if (uhSize != smo.sameAsPrev.length)
                    throw 'BAD2';
                if (uhSize != smo.sameAsNext.length)
                    throw 'BAD3';
                document.title = l10n.universe['book'][smo.bcv[0]] + ' ' + smo.bcv[1];
                $('#tabs-background h2').html(document.title);
                for (var i = 0; i < uhSize; ++i) {
                    if (!smo.sameAsPrev[i]) {
                        if (i == 1) {
                            chapter = smo.bcv[i];
                        }
                        else if (i == 2) {
                            verse = smo.bcv[i];
                            refs = smo.pics;
                            urls = smo.urls;
                        }
                    }
                }
            }
        }
        var text;
        var textDisplayClass = '';
        if (qd && qd.monad2Id[this.monad] && containsMonad(quizMonads, this.monad)) {
            if (qd.quizFeatures.hideWord)
                text = "(".concat(++DisplaySingleMonadObject.itemIndex, ")");
            else
                text = this.displayedMo.mo.features[configuration.surfaceFeature];
            text = "<em>".concat(text, "</em>");
            textDisplayClass = ' text-danger';
        }
        else {
            text = this.displayedMo.mo.features[configuration.surfaceFeature];
            if (!containsMonad(quizMonads, this.monad))
                textDisplayClass = ' text-muted';
        }
        var chapterstring = chapter == null ? '' : "<span class=\"chapter\">".concat(chapter, "</span>&#x200a;");
        var versestring = verse == null ? '' : "<span class=\"verse\">".concat(verse, "</span>");
        var refstring;
        if (refs === null)
            refstring = '';
        else if (refs.length === 4)
            refstring = "<a target=\"_blank\" title=\"".concat(localize('click_for_picture'), "\" href=\"https://resources.learner.bible/link.php?picno=").concat(refs[3], "\"><img src=\"").concat(site_url, "images/p.png\"></a>");
        else
            refstring = "<a target=\"_blank\" title=\"".concat(localize('click_for_pictures'), "\" href=\"https://resources.learner.bible/img.php?book=").concat(refs[0], "&chapter=").concat(refs[1], "&verse=").concat(refs[2], "\"><img src=\"").concat(site_url, "images/pblue.png\"></a>");
        var urlstring = '';
        if (urls !== null)
            for (var uix = 0; uix < urls.length; ++uix)
                urlstring += "<a target=\"_blank\" title=\"".concat(localize(urlTypeString[urls[uix][1]]), "\" href=\"").concat(urls[uix][0], "\"><img src=\"").concat(site_url, "images/").concat(urls[uix][1], ".png\"></a>");
        var grammar = '';
        configuration.sentencegrammar[0]
            .walkFeatureValues(smo, 0, this.objType, false, function (whattype, objType, origObjType, featName, featValLoc) {
            switch (whattype) {
                case WHAT.feature:
                    var wordclass = void 0;
                    var fs = getFeatureSetting(objType, featName);
                    if (fs.foreignText)
                        wordclass = charset.foreignClass;
                    else if (fs.transliteratedText)
                        wordclass = charset.transliteratedClass;
                    else if (fs.isGloss && featName != 'zh-Hans' && featName != 'zh-Hant')
                        wordclass = 'tenpoint ltr';
                    else
                        wordclass = 'ltr';
                    if ((configuration.databaseName == "ETCBC4" && fs.isGloss)
                        || (configuration.databaseName == "nestle1904" && (featName == "swahili" || featName == "danish" || featName == "portuguese"))
                        || ((configuration.databaseName == "jvulgate" || configuration.databaseName == "VC") && (featName == "swahili" || featName == "danish"))) {
                        featValLoc = featValLoc.replace(/(&[gl]t);/, '$1Q')
                            .replace(/([^,;(]+).*/, '$1')
                            .replace(/(&[gl]t)Q/, '$1;');
                    }
                    grammar += "<span class=\"wordgrammar dontshowit ".concat(featName, " ").concat(wordclass, "\">").concat(featValLoc, "</span>");
                    break;
                case WHAT.metafeature:
                    grammar += "<span class=\"wordgrammar dontshowit ".concat(featName, " ltr\">").concat(featValLoc, "</span>");
                    break;
            }
        });
        var follow_space = '<span class="wordspace"> </span>';
        if (configuration.suffixFeature) {
            var suffix = smo.mo.features[configuration.suffixFeature];
            text += suffix;
            if (suffix === '' || suffix === '-' || suffix === '\u05be') {
                follow_space = '';
                textDisplayClass += suffix === '' ? ' cont cont1' : ' contx cont1';
                sentenceTextArr[0] += text;
            }
            else
                sentenceTextArr[0] += text + ' ';
        }
        else
            sentenceTextArr[0] += text + ' ';
        return $("<span class=\"textblock inline\"><span class=\"textdisplay ".concat(charset.foreignClass + textDisplayClass, "\" data-idd=\"").concat(smo.mo.id_d, "\">").concat(versestring).concat(refstring).concat(urlstring).concat(text, "</span>").concat(grammar, "</span>").concat(follow_space));
    };
    return DisplaySingleMonadObject;
}(DisplayMonadObject));
var DisplayMultipleMonadObject = (function (_super) {
    __extends(DisplayMultipleMonadObject, _super);
    function DisplayMultipleMonadObject(mmo, objType, level, monadSet, monadix, hasPredecessor, hasSuccessor) {
        var _this = _super.call(this, mmo, objType, level) || this;
        if (arguments.length == 7) {
            _this.isPatriarch = false;
            _this.range = monadSet;
            _this.mix = monadix;
            _this.children = [];
            _this.hasPredecessor = hasPredecessor;
            _this.hasSuccessor = hasSuccessor;
            _this.borderTitle = getObjectFriendlyName(objType);
        }
        else {
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
    DisplayMultipleMonadObject.prototype.generateHtml = function (qd, sentenceTextArr, quizMonads) {
        var spanclass = "lev".concat(this.level, " dontshowborder noseplin");
        if (this.hasPredecessor)
            spanclass += ' hasp';
        if (this.hasSuccessor)
            spanclass += ' hass';
        var grammar = '';
        var indent = 0;
        if (configuration.sentencegrammar[this.level]) {
            configuration.sentencegrammar[this.level]
                .walkFeatureValues(this.displayedMo, this.mix, this.objType, true, function (whattype, objType, origObjType, featName, featValLoc) {
                if (whattype == WHAT.feature || whattype == WHAT.metafeature) {
                    if (configuration.databaseName == 'ETCBC4' && objType == "clause_atom" && featName == "tab")
                        indent = +featValLoc;
                    else
                        grammar += "<span class=\"xgrammar dontshowit ".concat(objType, "_").concat(featName, "\">:").concat(featValLoc, "</span>");
                }
            });
        }
        var jq;
        if (this.isPatriarch) {
            jq = $("<span class=\"".concat(spanclass, "\"></span>"));
        }
        else if (this.displayedMo.mo.name == "dummy") {
            jq = $("<span class=\"".concat(spanclass, "\"><span class=\"nogram dontshowit\" data-idd=\"").concat(this.displayedMo.mo.id_d, "\" data-mix=\"0\"></span></span>"));
        }
        else if (configuration.databaseName == 'ETCBC4' && this.level == 2) {
            jq = $("<span class=\"notdummy ".concat(spanclass, "\">")
                + "<span class=\"gram dontshowit\" data-idd=\"".concat(this.displayedMo.mo.id_d, "\" data-mix=\"").concat(this.mix, "\">")
                + getObjectShortFriendlyName(this.objType)
                + grammar
                + '</span>'
                + "<span class=\"xgrammar clause_atom_tab dontshowit indentation\" data-indent=\"".concat(indent, "\">")
                + '</span>'
                + '</span>');
        }
        else {
            jq = $("<span class=\"notdummy ".concat(spanclass, "\">")
                + "<span class=\"gram dontshowit\" data-idd=\"".concat(this.displayedMo.mo.id_d, "\" data-mix=\"").concat(this.mix, "\">")
                + getObjectShortFriendlyName(this.objType)
                + grammar
                + '</span>'
                + '</span>');
        }
        for (var ch in this.children) {
            if (isNaN(+ch))
                continue;
            jq.append(this.children[ch].generateHtml(qd, sentenceTextArr, quizMonads));
        }
        return jq;
    };
    return DisplayMultipleMonadObject;
}(DisplayMonadObject));
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
function getFeatureValueFriendlyName(featureType, value, abbrev, doStripSort) {
    if (abbrev && l10n.emdrostype[featureType + '_abbrev'] !== undefined)
        return doStripSort
            ? StringWithSort.stripSortIndex(l10n.emdrostype[featureType + '_abbrev'][value])
            : l10n.emdrostype[featureType + '_abbrev'][value];
    if (featureType.substr(0, 8) === 'list of ') {
        featureType = featureType.substr(8);
        value = value.substr(1, value.length - 2);
        if (value.length == 0)
            return doStripSort
                ? StringWithSort.stripSortIndex(l10n.emdrostype[featureType]['NA'])
                : l10n.emdrostype[featureType]['NA'];
        var verb_classes = value.split(',');
        var localized_verb_classes = [];
        for (var ix in verb_classes) {
            if (isNaN(+ix))
                continue;
            localized_verb_classes.push(doStripSort
                ? StringWithSort.stripSortIndex(l10n.emdrostype[featureType][verb_classes[ix]])
                : l10n.emdrostype[featureType][verb_classes[ix]]);
        }
        localized_verb_classes.sort();
        return localized_verb_classes.join(', ');
    }
    return doStripSort
        ? StringWithSort.stripSortIndex(l10n.emdrostype[featureType][value])
        : l10n.emdrostype[featureType][value];
}
function getFeatureValueOtherFormat(otype, featureName, value) {
    var table = l10n.emdrosobject[otype][featureName + '_VALUES'];
    for (var ix = 0; ix < table.length; ++ix)
        if (table[ix].first <= value && table[ix].last >= value)
            return table[ix].text;
    return '?';
}
function getHtmlAttribFriendlyName(str) {
    return str.split(' ').join('_');
}
function localize(s) {
    var str = l10n_js[s];
    return str === undefined ? '??' + s + '??' : str;
}
function mayShowFeature(oType, origOtype, feat, sgiObj, debug) {
    if (debug === void 0) { debug = false; }
    if (!inQuiz)
        return true;
    var qf = quizdata.quizFeatures;
    function isDontShowFeature() {
        for (var _i = 0, _a = qf.dontShowFeatures; _i < _a.length; _i++) {
            var dsf = _a[_i];
            if (dsf === feat) {
                console.log('feat: ', feat);
                console.log('qf.dontShowFeatures: ', qf.dontShowFeatures);
                console.log('isDontShowFeature: ', dsf);
                console.log('------------------------------------');
                return true;
            }
        }
        return false;
    }
    function isDontShowObject() {
        for (var _i = 0, _a = qf.dontShowObjects; _i < _a.length; _i++) {
            var dso = _a[_i];
            if (dso.content === origOtype)
                return true;
        }
        return false;
    }
    if (isDontShowFeature()) {
        return false;
    }
    if (sgiObj.mytype === 'GrammarMetaFeature' && !isDontShowObject()) {
        for (var _i = 0, _a = sgiObj.items; _i < _a.length; _i++) {
            var it = _a[_i];
            if (!mayShowFeature(oType, origOtype, it.name, it))
                return false;
        }
        return true;
    }
    var regex_feat = new RegExp((sgiObj.mytype === 'GrammarFeature' && getFeatureSetting(oType, feat).isGloss !== undefined)
        ? '\\bglosses\\b'
        : "\\b".concat(feat, "\\b"));
    for (var _b = 0, _c = qf.dontShowObjects; _b < _c.length; _b++) {
        var dso = _c[_b];
        if (dso.content === origOtype)
            return dso.show !== undefined && Boolean(dso.show.match(regex_feat));
    }
    if (oType !== qf.objectType)
        return true;
    for (var _d = 0, _e = qf.requestFeatures; _d < _e.length; _d++) {
        var rf = _e[_d];
        if (rf.name === feat)
            return false;
    }
    return qf.dontShowFeatures.indexOf(feat) === -1;
}
var Dictionary = (function () {
    function Dictionary(dictif, index, qd) {
        this.monads = [];
        this.level = [];
        this.singleMonads = [];
        this.dispMonadObjects = [];
        this.sentenceSet = dictif.sentenceSets[index];
        this.sentenceSetQuiz = dictif.sentenceSetsQuiz == null ? this.sentenceSet : dictif.sentenceSetsQuiz[index];
        this.monadObjects1 = dictif.monadObjects[index];
        this.bookTitle = dictif.bookTitle;
        this.hideWord = (qd != null && qd.quizFeatures.hideWord);
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
            var parent_1 = this.monads[+i];
            for (var i2 in parent_1.children_idds) {
                if (isNaN(+i2))
                    continue;
                var child_idd = parent_1.children_idds[+i2];
                this.monads[child_idd].parent = parent_1;
            }
        }
        this.dispMonadObjects.push([]);
        for (var se in this.singleMonads) {
            if (isNaN(+se))
                continue;
            this.dispMonadObjects[0].push(new DisplaySingleMonadObject(this.singleMonads[+se], configuration.sentencegrammar[0].objType, qd != null));
        }
        for (var lev = 1; lev < configuration.maxLevels; ++lev) {
            var ldmo = [];
            this.dispMonadObjects.push(ldmo);
            if (lev < configuration.maxLevels - 1) {
                for (var i in this.monadObjects1[lev]) {
                    if (isNaN(+i))
                        continue;
                    var monadObject = this.monadObjects1[lev][parseInt(i)];
                    var segCount = monadObject.mo.monadset.segments.length;
                    for (var mix = 0; mix < segCount; ++mix) {
                        var mp = monadObject.mo.monadset.segments[mix];
                        ldmo.push(new DisplayMultipleMonadObject(monadObject, configuration.sentencegrammar[lev].objType, lev, mp, mix, mix > 0, mix < segCount - 1));
                    }
                }
                ldmo.sort(function (a, b) {
                    return a.range.low - b.range.low;
                });
            }
            else {
                var monadObject = this.monadObjects1[lev][0];
                ldmo.push(new DisplayMultipleMonadObject(monadObject, 'Patriarch', lev, monadObject.mo.monadset));
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
                        if (childDmo.parent != undefined)
                            throw 'BAD1';
                        childDmo.parent = parentDmo;
                        parentDmo.children.push(childDmo);
                    }
                }
            }
        }
    }
    Dictionary.prototype.hoverForGrammar = function () {
        var thisDict = this;
        if (useTooltip) {
            $(document).tooltip({
                items: "[data-idd]",
                disabled: false,
                content: function () { return thisDict.toolTipFunc(this, true)[0]; }
            });
        }
        else {
            $("[data-idd]")
                .hover(function () {
                var scrTop = $(window).scrollTop();
                var qcTop = $('#textcontainer').offset().top;
                $('.grammardisplay')
                    .html(thisDict.toolTipFunc(this, true)[0])
                    .css('top', Math.max(0, scrTop - qcTop + 5))
                    .outerWidth($('#grammardisplaycontainer').outerWidth() - 25)
                    .show();
            }, function () {
                $('.grammardisplay').hide();
            });
        }
    };
    Dictionary.prototype.dontHoverForGrammar = function () {
        if (useTooltip)
            $(document).tooltip({ items: "[data-idd]", disabled: true });
        else
            $("[data-idd]").off("mouseenter mouseleave");
    };
    Dictionary.prototype.clickForGrammar = function () {
        var _this = this;
        $("[data-idd]").on('click', function (event) {
            var _a = _this.toolTipFunc(event.currentTarget, false), contents = _a[0], heading = _a[1];
            $('#grammar-info-label').html(heading);
            $('#grammar-info-body').html(contents);
            $('#grammar-info-dialog').modal('show');
        });
    };
    Dictionary.handleDisplaySize = function (thisDict) {
        switch (resizer.getWindowSize()) {
            case 'xs':
            case 'sm':
                thisDict.dontHoverForGrammar();
                break;
            default:
                thisDict.hoverForGrammar();
                break;
        }
    };
    Dictionary.boxes = function (num, minnum, maxnum) {
        var s = '';
        var numspaces = num < 10 ? num : num - 1;
        for (var i = minnum; i < numspaces; ++i)
            s += '\u00a0';
        s += num;
        for (var i = num; i <= maxnum; ++i)
            s += '\u25aa';
        return s;
    };
    Dictionary.prototype.generateSentenceHtml = function (qd) {
        DisplaySingleMonadObject.itemIndex = 0;
        var sentenceTextArr = [''];
        $('#textarea').append(this.dispMonadObjects[this.dispMonadObjects.length - 1][0].generateHtml(qd, sentenceTextArr, this.sentenceSetQuiz));
        if (configuration.databaseName == 'ETCBC4') {
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
            $('#textarea').append('<div class="indentation" id="testwidth"></div>');
            var tw = $('#testwidth');
            tw.html(Dictionary.boxes(minindent_1, minindent_1, maxindent_1) + '&nbsp;&nbsp;');
            indentation_width = tw.width() + 1;
            all_c_a_t.each(function (index, el) {
                var indent = +$(el).attr('data-indent');
                $(el).html(Dictionary.boxes(indent, minindent_1, maxindent_1) + '&nbsp;&nbsp;');
            });
        }
        resizer.addResizeListener(Dictionary.handleDisplaySize, this, 'xyzzy');
        Dictionary.handleDisplaySize(this);
        this.clickForGrammar();
        return sentenceTextArr[0];
    };
    Dictionary.prototype.toolTipFunc = function (x_this, set_head) {
        var monob = this.monads[+($(x_this).attr("data-idd"))];
        var level = this.level[+($(x_this).attr("data-idd"))];
        var mix = +$(x_this).attr("data-mix");
        var sengram = configuration.sentencegrammar[level];
        var res = '<table>';
        if (set_head) {
            res += "<tr>\n                        <td colspan=\"2\" class=\"tooltiphead\">".concat(getObjectFriendlyName(sengram.objType), "</td>\n                    </tr>");
        }
        if (level === 0 && !this.hideWord) {
            res += "<tr>\n                        <td>".concat(localize('visual'), "</td>\n                        <td class=\"bol-tooltip leftalign ").concat(charset.foreignClass, "\">").concat(monob.mo.features[configuration.surfaceFeature], "</td>\n                    </tr>");
        }
        var map = {};
        sengram.walkFeatureNames(sengram.objType, function (whattype, objType, origObjType, featName, featNameLoc, sgiObj) {
            if (whattype == WHAT.feature || whattype == WHAT.metafeature)
                if (!mayShowFeature(objType, origObjType, featName, sgiObj))
                    return;
            if (whattype == WHAT.feature || whattype == WHAT.metafeature || whattype == WHAT.groupstart)
                map[featName] = featNameLoc;
        });
        sengram.walkFeatureValues(monob, mix, sengram.objType, false, function (whattype, objType, origObjType, featName, featValLoc, sgiObj) {
            switch (whattype) {
                case WHAT.feature:
                    if (mayShowFeature(objType, origObjType, featName, sgiObj)) {
                        var wordclass = void 0;
                        var fs = getFeatureSetting(objType, featName);
                        if (featValLoc === '-')
                            wordclass = '';
                        else if (fs.foreignText)
                            wordclass = charset.foreignClass;
                        else if (fs.transliteratedText)
                            wordclass = charset.transliteratedClass;
                        else
                            wordclass = '';
                        res += "<tr>\n                                                           <td>".concat(map[featName], "</td>\n                                                           <td class=\"bol-tooltip leftalign ").concat(wordclass, "\">").concat(featValLoc, "</td>\n                                                       </tr>");
                    }
                    break;
                case WHAT.metafeature:
                    if (mayShowFeature(objType, origObjType, featName, sgiObj)) {
                        res += "<tr>\n                                                           <td>".concat(map[featName], "</td>\n                                                           <td class=\"bol-tooltip leftalign\">").concat(featValLoc, "</td>\n                                                       </tr>");
                    }
                    break;
                case WHAT.groupstart:
                    res += "<tr>\n                                                       <td><b>".concat(map[featName], ":</b></td>\n                                                       <td class=\"leftalign\"></td>\n                                                   </tr>");
                    break;
            }
        });
        return [res + '</table>', getObjectFriendlyName(sengram.objType)];
    };
    Dictionary.prototype.getSingleMonadObject = function (monad) {
        return this.singleMonads[monad];
    };
    return Dictionary;
}());
var COMPONENT_TYPE;
(function (COMPONENT_TYPE) {
    COMPONENT_TYPE[COMPONENT_TYPE["textField"] = 0] = "textField";
    COMPONENT_TYPE[COMPONENT_TYPE["textFieldWithVirtKeyboard"] = 1] = "textFieldWithVirtKeyboard";
    COMPONENT_TYPE[COMPONENT_TYPE["textFieldForeign"] = 2] = "textFieldForeign";
    COMPONENT_TYPE[COMPONENT_TYPE["comboBox"] = 3] = "comboBox";
    COMPONENT_TYPE[COMPONENT_TYPE["checkBoxes"] = 4] = "checkBoxes";
})(COMPONENT_TYPE || (COMPONENT_TYPE = {}));
var ComponentWithYesNo = (function () {
    function ComponentWithYesNo(elem, elemType) {
        this.elem = elem;
        this.elemType = elemType;
        this.yesIcon = $("<img src=\"".concat(site_url, "/images/ok.png\" alt=\"Yes\">"));
        this.noIcon = $("<img src=\"".concat(site_url, "/images/notok.png\" alt=\"No\">"));
        this.noneIcon = $("<img src=\"".concat(site_url, "/images/none.png\" alt=\"None\">"));
    }
    ComponentWithYesNo.prototype.getJQuery = function () {
        var spn = $('<td class="qbox"></td>').append([this.yesIcon, this.noIcon, this.noneIcon, this.elem]);
        this.setNone();
        return spn;
    };
    ComponentWithYesNo.prototype.monitorChange = function (elem) {
        clearInterval(ComponentWithYesNo.intervalHandler);
        if (ComponentWithYesNo.lastMonitored !== elem.data('kbid')) {
            ComponentWithYesNo.monitorOrigVal = elem.val();
            ComponentWithYesNo.lastMonitored = elem.data('kbid');
        }
        function timedfun(elem2, me) {
            return function () {
                var s = elem2.val();
                if (s !== ComponentWithYesNo.monitorOrigVal) {
                    ComponentWithYesNo.monitorOrigVal = s;
                    me.setNone();
                }
            };
        }
        ComponentWithYesNo.intervalHandler = setInterval(timedfun(elem, this), 500);
    };
    ComponentWithYesNo.prototype.addChangeListener = function () {
        var _this = this;
        this.elem.on('change', function () { return _this.setNone(); });
    };
    ComponentWithYesNo.prototype.addKeypressListener = function () {
        var _this = this;
        this.elem.on('input', function (e1) { return _this.setNone(); });
        if (this.elemType === COMPONENT_TYPE.textFieldWithVirtKeyboard) {
            this.elem.on('focus', function (e) { return _this.monitorChange($(e.currentTarget)); });
        }
    };
    ComponentWithYesNo.prototype.getComp = function () {
        return this.elem;
    };
    ComponentWithYesNo.prototype.getCompType = function () {
        return this.elemType;
    };
    ComponentWithYesNo.prototype.setYesNo = function (yes) {
        if (ComponentWithYesNo.lastMonitored === this.elem.data('kbid'))
            ComponentWithYesNo.monitorOrigVal = this.elem.val();
        if (yes) {
            $(this.elem).css({
                "background-color": "rgba(67, 176, 42, 0.1)",
                "outline": "solid 2px rgba(67, 176, 42, 1.0)"
            });
            this.yesIcon.show();
            this.noIcon.hide();
        }
        else {
            $(this.elem).css({
                "background-color": "rgba(195, 92, 244, 0.1)",
                "outline": "solid 2px rgba(195, 92, 244, 1.0)"
            });
            this.yesIcon.hide();
            this.noIcon.show();
        }
        this.noneIcon.hide();
    };
    ComponentWithYesNo.prototype.setNone = function () {
        this.yesIcon.hide();
        this.noIcon.hide();
        this.noneIcon.show();
        this.elem.css({
            "background-color": "",
            "outline": ""
        });
    };
    return ComponentWithYesNo;
}());
var Answer = (function () {
    function Answer(comp, answerSws, answerString, matchRegexp) {
        this.hasAnswered = false;
        this.comp = comp;
        this.c = comp.getComp();
        this.cType = comp.getCompType();
        this.answerSws = answerSws;
        this.answerString = answerString.normalize('NFC');
        this.matchRegexp = matchRegexp;
        if (this.cType == COMPONENT_TYPE.checkBoxes) {
            if (this.answerString[0] == "(") {
                var aString = answerString.substr(1, answerString.length - 2);
                this.answerArray = aString.split(',');
            }
            else {
                this.answerArray = new Array(this.answerString);
            }
        }
    }
    Answer.prototype.showIt = function () {
        switch (this.cType) {
            case COMPONENT_TYPE.textField:
            case COMPONENT_TYPE.textFieldWithVirtKeyboard: {
                $(this.c).find('input').val(this.answerString);
                break;
            }
            case COMPONENT_TYPE.textFieldForeign: {
                $(this.c).find('.inputshow').text(this.answerString);
                break;
            }
            case COMPONENT_TYPE.comboBox: {
                var correctAnswer_1 = this['answerSws']['internal'];
                var radios = $(this.c).find('input');
                radios.each(function () {
                    var value = $(this).attr('value');
                    if (value === correctAnswer_1) {
                        $(this).prop('checked', true);
                    }
                });
                break;
            }
            case COMPONENT_TYPE.checkBoxes: {
                var inputs = $(this.c).find('input');
                var xthis_1 = this;
                inputs.each(function () {
                    var value = $(this).attr('value');
                    $(this).prop('checked', xthis_1.answerArray.indexOf(value) != -1);
                });
                break;
            }
        }
    };
    Answer.prototype.checkIt = function (fromShowIt, displayIt) {
        if (fromShowIt) {
            if (!this.hasAnswered) {
                this.hasAnswered = true;
                this.firstAnswer = "*Unanswered*";
                this.firstAnswerCorrect = false;
            }
            if (displayIt)
                this.comp.setYesNo(true);
        }
        else {
            var userAnswer_1;
            var isCorrect_1;
            switch (this.cType) {
                case COMPONENT_TYPE.textField:
                case COMPONENT_TYPE.textFieldForeign:
                case COMPONENT_TYPE.textFieldWithVirtKeyboard:
                    if (this.cType == COMPONENT_TYPE.textFieldForeign)
                        userAnswer_1 = $(this.c).find('.inputshow').text();
                    else
                        userAnswer_1 = $(this.c).find('input').val();
                    userAnswer_1 = userAnswer_1.normalize('NFC')
                        .trim()
                        .replace(/  +/g, ' ');
                    if (this.matchRegexp == null) {
                        isCorrect_1 = userAnswer_1 == this.answerString;
                        if (!isCorrect_1)
                            isCorrect_1 = this.answerString === '-' && userAnswer_1 === '\u05be';
                    }
                    else {
                        var re = eval(this.matchRegexp.format(userAnswer_1.replace(/[-[\]{}()*+?.,\\^$|#\s]/g, "\\$&")));
                        isCorrect_1 = this.answerString.match(re) !== null;
                    }
                    break;
                case COMPONENT_TYPE.comboBox:
                    var selectedOption = $(this.c).find("input:checked");
                    if (selectedOption.attr('value') != null) {
                        var userAnswerSws = $(this.c).find("input:checked").parent().data('sws');
                        isCorrect_1 = userAnswerSws === this.answerSws;
                        userAnswer_1 = userAnswerSws.getInternal();
                    }
                    break;
                case COMPONENT_TYPE.checkBoxes:
                    var inputs = $(this.c).find('input');
                    var xthis_2 = this;
                    isCorrect_1 = true;
                    userAnswer_1 = '';
                    inputs.each(function () {
                        var value = $(this).attr('value');
                        if ($(this).prop('checked')) {
                            userAnswer_1 += value + ',';
                            isCorrect_1 = isCorrect_1 && xthis_2.answerArray.indexOf(value) != -1;
                        }
                        else
                            isCorrect_1 = isCorrect_1 && xthis_2.answerArray.indexOf(value) == -1;
                    });
                    if (userAnswer_1 !== '') {
                        userAnswer_1 = '(' + userAnswer_1.substr(0, userAnswer_1.length - 1) + ')';
                    }
                    break;
            }
            if (userAnswer_1 && !this.hasAnswered) {
                this.hasAnswered = true;
                this.firstAnswer = userAnswer_1;
                this.firstAnswerCorrect = isCorrect_1;
            }
            if (this.hasAnswered && displayIt)
                this.comp.setYesNo(isCorrect_1);
        }
    };
    Answer.prototype.commitIt = function () {
        this.checkIt(false, false);
        if (!this.hasAnswered) {
            this.hasAnswered = true;
            this.firstAnswer = "*Unanswered*";
            this.firstAnswerCorrect = false;
        }
    };
    Answer.prototype.usersAnswer = function () {
        return this.firstAnswer;
    };
    Answer.prototype.usersAnswerWasCorrect = function () {
        return this.firstAnswerCorrect;
    };
    Answer.prototype.correctAnswer = function () {
        return this.answerString;
    };
    return Answer;
}());
var Foreign2Shortcut = (function () {
    function Foreign2Shortcut() {
    }
    Foreign2Shortcut.init = function () {
        switch (configuration.charSet) {
            case 'hebrew':
                Foreign2Shortcut.map['א'] = '>';
                Foreign2Shortcut.map['ב'] = 'b';
                Foreign2Shortcut.map['ג'] = 'g';
                Foreign2Shortcut.map['ד'] = 'd';
                Foreign2Shortcut.map['ה'] = 'h';
                Foreign2Shortcut.map['ו'] = 'w';
                Foreign2Shortcut.map['ז'] = 'z';
                Foreign2Shortcut.map['ח'] = 'x';
                Foreign2Shortcut.map['ט'] = 'v';
                Foreign2Shortcut.map['י'] = 'j';
                Foreign2Shortcut.map['ך'] = 'K';
                Foreign2Shortcut.map['כ'] = 'k';
                Foreign2Shortcut.map['ל'] = 'l';
                Foreign2Shortcut.map['ם'] = 'M';
                Foreign2Shortcut.map['מ'] = 'm';
                Foreign2Shortcut.map['ן'] = 'N';
                Foreign2Shortcut.map['נ'] = 'n';
                Foreign2Shortcut.map['ס'] = 's';
                Foreign2Shortcut.map['ע'] = '<';
                Foreign2Shortcut.map['ף'] = 'P';
                Foreign2Shortcut.map['פ'] = 'p';
                Foreign2Shortcut.map['ץ'] = 'Y';
                Foreign2Shortcut.map['צ'] = 'y';
                Foreign2Shortcut.map['ק'] = 'q';
                Foreign2Shortcut.map['ר'] = 'r';
                Foreign2Shortcut.map['שׁ'] = 'c';
                Foreign2Shortcut.map['שׂ'] = 'f';
                Foreign2Shortcut.map['ש'] = '#';
                Foreign2Shortcut.map['ת'] = 't';
                Foreign2Shortcut.map['־'] = '&';
                Foreign2Shortcut.map['ֿ'] = '2';
                Foreign2Shortcut.map['ּ'] = '.';
                Foreign2Shortcut.map['ֽ'] = '$';
                Foreign2Shortcut.map['ְ'] = ':';
                Foreign2Shortcut.map['ֳ'] = '+';
                Foreign2Shortcut.map['ֲ'] = 'A';
                Foreign2Shortcut.map['ֱ'] = 'E';
                Foreign2Shortcut.map['ֵ'] = '1';
                Foreign2Shortcut.map['ָ'] = '@';
                Foreign2Shortcut.map['ַ'] = 'a';
                Foreign2Shortcut.map['ֶ'] = 'e';
                Foreign2Shortcut.map['ִ'] = 'I';
                Foreign2Shortcut.map['ֹ'] = 'o';
                Foreign2Shortcut.map['ֻ'] = 'u';
                Foreign2Shortcut.map['-'] = '-';
                break;
            case "greek":
                Foreign2Shortcut.map['α'] = 'a';
                Foreign2Shortcut.map['β'] = 'b';
                Foreign2Shortcut.map['γ'] = 'g';
                Foreign2Shortcut.map['δ'] = 'd';
                Foreign2Shortcut.map['ε'] = 'e';
                Foreign2Shortcut.map['ζ'] = 'z';
                Foreign2Shortcut.map['η'] = 'h';
                Foreign2Shortcut.map['θ'] = 'q';
                Foreign2Shortcut.map['ι'] = 'i';
                Foreign2Shortcut.map['κ'] = 'k';
                Foreign2Shortcut.map['λ'] = 'l';
                Foreign2Shortcut.map['μ'] = 'm';
                Foreign2Shortcut.map['ν'] = 'n';
                Foreign2Shortcut.map['ξ'] = 'x';
                Foreign2Shortcut.map['ο'] = 'o';
                Foreign2Shortcut.map['π'] = 'p';
                Foreign2Shortcut.map['ρ'] = 'r';
                Foreign2Shortcut.map['ς'] = 'c';
                Foreign2Shortcut.map['σ'] = 's';
                Foreign2Shortcut.map['τ'] = 't';
                Foreign2Shortcut.map['υ'] = 'u';
                Foreign2Shortcut.map['φ'] = 'f';
                Foreign2Shortcut.map['χ'] = 'j';
                Foreign2Shortcut.map['ψ'] = 'q';
                Foreign2Shortcut.map['ω'] = 'w';
                Foreign2Shortcut.map['Α'] = 'A';
                Foreign2Shortcut.map['Β'] = 'B';
                Foreign2Shortcut.map['Γ'] = 'G';
                Foreign2Shortcut.map['Δ'] = 'D';
                Foreign2Shortcut.map['Ε'] = 'E';
                Foreign2Shortcut.map['Ζ'] = 'Z';
                Foreign2Shortcut.map['Η'] = 'H';
                Foreign2Shortcut.map['Θ'] = 'Q';
                Foreign2Shortcut.map['Ι'] = 'I';
                Foreign2Shortcut.map['Κ'] = 'K';
                Foreign2Shortcut.map['Λ'] = 'L';
                Foreign2Shortcut.map['Μ'] = 'M';
                Foreign2Shortcut.map['Ν'] = 'N';
                Foreign2Shortcut.map['Ξ'] = 'X';
                Foreign2Shortcut.map['Ο'] = 'O';
                Foreign2Shortcut.map['Π'] = 'P';
                Foreign2Shortcut.map['Ρ'] = 'R';
                Foreign2Shortcut.map['Σ'] = 'S';
                Foreign2Shortcut.map['Τ'] = 'T';
                Foreign2Shortcut.map['Υ'] = 'U';
                Foreign2Shortcut.map['Φ'] = 'F';
                Foreign2Shortcut.map['Χ'] = 'J';
                Foreign2Shortcut.map['Ψ'] = 'Q';
                Foreign2Shortcut.map['Ω'] = 'W';
                break;
            case "transliterated_hebrew":
                for (var a = 97; a < 123; ++a)
                    Foreign2Shortcut.map[String.fromCharCode(a)] = String.fromCharCode(a);
                Foreign2Shortcut.map['ʔ'] = '>';
                Foreign2Shortcut.map['ʕ'] = '<';
                break;
        }
    };
    Foreign2Shortcut.get = function (letter) {
        if (Foreign2Shortcut.map[letter])
            return Foreign2Shortcut.map[letter];
        else
            return '?';
    };
    Foreign2Shortcut.map = {};
    return Foreign2Shortcut;
}());
var KeyTable = (function () {
    function KeyTable() {
        this.elements = [];
        this.actions = [];
        this.focus = [];
    }
    KeyTable.prototype.add = function (card, row, letter, id, action) {
        if (!this.elements[card])
            this.elements[card] = [];
        if (!this.elements[card][row])
            this.elements[card][row] = new Object;
        if (!this.elements[card][row][letter])
            this.elements[card][row][letter] = [];
        this.elements[card][row][letter].push(id);
        if (!this.actions[card])
            this.actions[card] = [];
        this.actions[card][row] = action;
    };
    KeyTable.prototype.addfocus = function (card, row, id) {
        if (!this.focus[card])
            this.focus[card] = [];
        this.focus[card][row] = id;
    };
    KeyTable.prototype.get_element = function (card, row, letter) {
        if (!this.elements[card] || !this.elements[card][row])
            return null;
        return this.elements[card][row][letter];
    };
    KeyTable.prototype.get_action = function (card, row) {
        if (!this.actions[card])
            return null;
        return this.actions[card][row];
    };
    KeyTable.prototype.get_focus = function (card, row) {
        if (!this.focus[card])
            return null;
        return this.focus[card][row];
    };
    return KeyTable;
}());
var Cursor = (function () {
    function Cursor() {
    }
    Cursor.init = function (minrow, maxrow) {
        Cursor.minrow = minrow;
        Cursor.maxrow = maxrow;
        Cursor.card = 0;
        Cursor.row = minrow;
    };
    Cursor.hide = function () {
        $("#ptr_".concat(Cursor.card, "_").concat(Cursor.row)).hide();
    };
    Cursor.show = function (force) {
        if (force === void 0) { force = false; }
        $("#ptr_".concat(Cursor.card, "_").concat(Cursor.row)).show();
        if (force)
            return;
        var questiontop = $('#quizdesc').offset().top;
        var scrollToPos;
        var top = $("#row_".concat(Cursor.card, "_").concat(Cursor.row)).offset().top;
        var bottom = top + $("#row_".concat(Cursor.card, "_").concat(Cursor.row)).height() + 10;
        if (bottom - window.scrollY >= window.innerHeight || top - window.scrollY < 0) {
            if (questiontop + window.innerHeight >= bottom)
                scrollToPos = questiontop;
            else
                scrollToPos = bottom - window.innerHeight;
        }
        if ($("#keyinp_".concat(Cursor.card, "_").concat(Cursor.row)).length) {
            $("#keyinp_".concat(Cursor.card, "_").concat(Cursor.row)).focus();
            $('body').unbind('keydown');
        }
        $('html, body').animate({
            scrollTop: scrollToPos
        }, 50);
    };
    Cursor.set = function (c, r, force) {
        if (c === void 0) { c = 0; }
        if (r === void 0) { r = Cursor.minrow; }
        if (force === void 0) { force = false; }
        Cursor.hide();
        Cursor.card = c;
        Cursor.row = r;
        Cursor.show(force);
    };
    Cursor.prevNextItem = function (n) {
        if (n > 0) {
            if (Cursor.row + n < Cursor.maxrow) {
                Cursor.set(Cursor.card, Cursor.row + n);
                return true;
            }
            else
                return false;
        }
        else {
            if (Cursor.row + n >= Cursor.minrow) {
                Cursor.set(Cursor.card, Cursor.row + n);
                return true;
            }
            else
                return false;
        }
    };
    return Cursor;
}());
var PanelQuestion = (function () {
    function PanelQuestion(qd, dict, exam_mode) {
        var _this = this;
        this.vAnswers = [];
        this.answersPerCard = [];
        this.question_stat = new QuestionStatistics;
        this.subQuizIndex = 0;
        this.keytable = new KeyTable;
        this.keyinps = [];
        this.body_keydown = function (event) {
            var pq = event.data;
            var ctrl = event.ctrlKey || event.metaKey;
            if (event.key === "PageDown")
                $('#nextsubquiz:visible').click();
            else if (event.key === "PageUp")
                $('#prevsubquiz:visible').click();
            else if (event.key === "ArrowDown" && !ctrl)
                Cursor.prevNextItem(1);
            else if (event.key === "ArrowUp")
                Cursor.prevNextItem(-1);
            else if (event.key === "ArrowDown" && ctrl)
                $('#next_question:visible:enabled').click();
            else if (event.key === "g" && ctrl)
                $('#check_answer').click();
            else if (event.key === "j" && ctrl)
                $('#show_answer').click();
            else if (event.key === "s" && ctrl) {
                $('.shortcut').toggle();
            }
            else if (!ctrl) {
                var ids = pq.keytable.get_element(Cursor.card, Cursor.row, event.key);
                if (ids) {
                    switch (pq.keytable.get_action(Cursor.card, Cursor.row)) {
                        case 1:
                            if (ids.length > 1) {
                                for (var i in ids) {
                                    if (isNaN(+i))
                                        continue;
                                    if ($("#".concat(ids[i])).prop('checked')) {
                                        var i1 = +i + 1;
                                        if (i1 == ids.length)
                                            i1 = 0;
                                        $("#".concat(ids[i1])).prop('checked', true);
                                        $("#".concat(ids[i1])).change();
                                        return false;
                                    }
                                }
                            }
                            $("#".concat(ids[0])).prop('checked', true);
                            $("#".concat(ids[0])).change();
                            break;
                        case 2:
                            $("#".concat(ids[0])).click();
                            $("#".concat(ids[0])).change();
                            break;
                        case 3:
                            $("#".concat(ids[0])).prop('checked', !$("#".concat(ids[0])).prop('checked'));
                            $("#".concat(ids[0])).change();
                    }
                }
                else
                    return true;
            }
            else
                return true;
            return false;
        };
        this.textfield_keydown = function (event) {
            var pq = event.data;
            var ctrl = event.ctrlKey || event.metaKey;
            if (event.key === "ArrowDown" && !ctrl) {
                if (Cursor.prevNextItem(1))
                    $(event.target).blur();
                return false;
            }
            else if (event.key === "ArrowUp") {
                if (Cursor.prevNextItem(-1))
                    $(event.target).blur();
                return false;
            }
            else if (event.key === "PageDown") {
                if ($('#nextsubquiz').is(':visible')) {
                    $(event.target).blur();
                    $('#nextsubquiz:visible').click();
                }
                return false;
            }
            else if (event.key === "PageUp") {
                if ($('#prevsubquiz').is(':visible')) {
                    $(event.target).blur();
                    $('#prevsubquiz:visible').click();
                }
                return false;
            }
            else if (event.key === "ArrowDown" && ctrl) {
                $('#next_question:visible:enabled').click();
                return false;
            }
            else if (event.key === "g" && ctrl) {
                $('#check_answer').click();
                return false;
            }
            else if (event.key === "j" && ctrl) {
                $('#show_answer').click();
                return false;
            }
            else if (event.key === "s" && ctrl) {
                $('.shortcut').toggle();
                return false;
            }
            return true;
        };
        this.qd = qd;
        this.sentence = dict.sentenceSetQuiz;
        Foreign2Shortcut.init();
        this.location_info(dict);
        this.question_stat.text = dict.generateSentenceHtml(qd);
        var hideWord = qd.quizFeatures.hideWord;
        var showFeatures = qd.quizFeatures.showFeatures;
        var requestFeatures = qd.quizFeatures.requestFeatures;
        var oType = qd.quizFeatures.objectType;
        var featuresHere = typeinfo.obj2feat[oType];
        var qoFeatures = this.buildQuizObjectFeatureList();
        var hasForeignInput = false;
        var quizItemID = 0;
        var questionheaders = [];
        var headInd = 0;
        if (hideWord) {
            questionheaders.push('<th>' + localize('item_number') + '</th>');
            this.question_stat.show_feat.names.push('item_number');
        }
        for (var sfi in showFeatures) {
            if (isNaN(+sfi))
                continue;
            questionheaders.push('<th>' + getFeatureFriendlyName(oType, showFeatures[sfi]) + '</th>');
            this.question_stat.show_feat.names.push(showFeatures[sfi]);
        }
        for (var sfi in requestFeatures) {
            if (isNaN(+sfi))
                continue;
            questionheaders.push('<th>' + getFeatureFriendlyName(oType, requestFeatures[sfi].name) + '</th>');
            this.question_stat.req_feat.names.push(requestFeatures[sfi].name);
        }
        var headLen = questionheaders.length;
        var quizCardNum = qoFeatures.length;
        var quizContainer = $('div#quizcontainer');
        this.subQuizMax = quizCardNum;
        var _loop_2 = function (qoid) {
            if (isNaN(+qoid))
                return "continue";
            if (headInd >= headLen)
                headInd -= headLen;
            var quizCard_1 = +qoid === 0
                ? $('<div class="quizcard" style="display:block"></div>')
                : $('<div class="quizcard" style="display:none"></div>');
            var quizTab = $('<table class="quiztab"></table>');
            quizCard_1.append(quizTab);
            quizContainer.append(quizCard_1);
            var fvals = qoFeatures[+qoid];
            if (hideWord) {
                quizTab.append("<tr><td>&nbsp;</td>".concat(questionheaders[headInd], "<td>").concat(+qoid + 1, "</td></tr>"));
                ++headInd;
                this_2.question_stat.show_feat.values.push("" + (+qoid + 1));
            }
            for (var sfi in showFeatures) {
                if (isNaN(+sfi))
                    continue;
                var sf = showFeatures[+sfi];
                var val = fvals[sf];
                var featType = featuresHere[sf];
                var featset = getFeatureSetting(oType, sf);
                this_2.question_stat.show_feat.values.push(val);
                if (featType == null && sf !== 'visual')
                    alert("Unexpected (1) featType==null in panelquestion.ts; sf=\"".concat(sf, "\""));
                if (sf === 'visual')
                    featType = 'string';
                if (featType == 'hint') {
                    var sp = val.split(/([,=≠])/);
                    if (sp.length == 3) {
                        val = getFeatureFriendlyName(oType, sp[0]) + sp[1] +
                            getFeatureValueFriendlyName(featuresHere[sp[0]], sp[2], false, true);
                    }
                    else if (sp.length == 7) {
                        val = getFeatureFriendlyName(oType, sp[0])
                            + sp[1]
                            + getFeatureValueFriendlyName(featuresHere[sp[0]], sp[2], false, true)
                            + ", "
                            + getFeatureFriendlyName(oType, sp[4])
                            + sp[5]
                            + getFeatureValueFriendlyName(featuresHere[sp[4]], sp[6], false, true);
                    }
                    else if (val === '*')
                        val = '-';
                }
                else if (featType !== 'string' && featType !== 'ascii' && featType !== 'integer') {
                    if (featset.otherValues && featset.otherValues.indexOf(val) !== -1)
                        val = localize('other_value');
                    else
                        val = getFeatureValueFriendlyName(featType, val, false, true);
                }
                if (val == null)
                    alert('Unexpected val==null in panelquestion.ts');
                if (featType === 'string' || featType == 'ascii') {
                    quizTab.append("<tr><td>&nbsp;</td>".concat(questionheaders[headInd])
                        + "<td class=\"".concat(PanelQuestion.charclass(featset), "\">").concat(val === '' ? '-' : val, "</td></tr>"));
                    ++headInd;
                }
                else {
                    quizTab.append("<tr><td>&nbsp;</td>".concat(questionheaders[headInd], "<td>").concat(val, "</td></tr>"));
                    ++headInd;
                }
            }
            Cursor.init(headInd, headLen);
            var _loop_3 = function (rfi) {
                if (isNaN(+rfi))
                    return "continue";
                var rf = requestFeatures[+rfi].name;
                var usedropdown = requestFeatures[+rfi].usedropdown;
                var hideFeatures = requestFeatures[+rfi].hideFeatures;
                var correctAnswer = fvals[rf];
                var featType = featuresHere[rf];
                var featset = getFeatureSetting(oType, rf);
                var v = null;
                ++quizItemID;
                if (correctAnswer == null)
                    alert('Unexpected correctAnswer==null in panelquestion.ts');
                if (correctAnswer === '')
                    correctAnswer = '-';
                if (featType == null && rf !== 'visual')
                    alert('Unexpected (2) featType==null in panelquestion.ts');
                if (rf === 'visual')
                    featType = 'string';
                if (featset.alternateshowrequestDb != null && usedropdown) {
                    var suggestions = fvals[rf + '!suggest!'];
                    if (suggestions == null)
                        v = $("<td class=\"".concat(PanelQuestion.charclass(featset), "\">").concat(correctAnswer, "</td></tr>"));
                    else {
                        var quiz_div_1 = $('<div class="quizitem"></div>');
                        var optArray = [];
                        var cwyn = new ComponentWithYesNo(quiz_div_1, COMPONENT_TYPE.comboBox);
                        var charSetClass = configuration.charSet == 'transliterated_hebrew' ? 'hebrew_translit' : configuration.charSet;
                        cwyn.addChangeListener();
                        for (var valix in suggestions) {
                            if (isNaN(+valix))
                                continue;
                            var s = suggestions[+valix];
                            var item = new StringWithSort(s, s);
                            var option = $('<div class="selectbutton multiple_choice">'
                                + "<input type=\"radio\" id=\"".concat(item.getInternal(), "_").concat(quizItemID, "\" name=\"quizitem_").concat(quizItemID, "\" value=\"").concat(item.getInternal(), "\">")
                                + "<label class=\"".concat(charSetClass, "\" for=\"").concat(item.getInternal(), "_").concat(quizItemID, "\">").concat(item.getString(), "<span class=\"shortcut multichoice\"></span></label>")
                                + '</div>');
                            option
                                .data('sws', item)
                                .data('id', "".concat(item.getInternal(), "_").concat(quizItemID));
                            optArray.push(option);
                            if (s === correctAnswer)
                                this_2.vAnswers.push(new Answer(cwyn, item, s, null));
                        }
                        optArray.sort(function (a, b) { return StringWithSort.compare(a.data('sws'), b.data('sws')); });
                        $.each(optArray, function (ix, o) {
                            quiz_div_1.append(o);
                            var sc = String.fromCharCode(ix + 97);
                            o.find('.shortcut').text(sc);
                            _this.keytable.add(+qoid, headInd, sc, o.data('id'), 1);
                        });
                        v = cwyn.getJQuery();
                    }
                }
                else if (featType === 'string' || featType === 'ascii') {
                    var cwyn = void 0;
                    var trimmedAnswer = correctAnswer.trim()
                        .replace(/&lt;/g, '<')
                        .replace(/&gt;/g, '>')
                        .replace(/&quot;/g, '"')
                        .replace(/&amp;/g, '&');
                    if (configuration.charSet !== 'latin' && (featset.foreignText || featset.transliteratedText)) {
                        var answerArray = trimmedAnswer.split("");
                        var answerLetters_1 = [];
                        var additionalCons = [];
                        var additionalVowels = [];
                        var answerLettersRandom = void 0;
                        var showLetters_1 = [];
                        $.each(answerArray, function (i, el) {
                            if ($.inArray(el, answerLetters_1) === -1)
                                answerLetters_1.push(el);
                        });
                        var shinDot = false;
                        var sinDot = false;
                        for (var i = 0; i < answerLetters_1.length; i++) {
                            if (answerLetters_1[i] === 'ש') {
                                answerLetters_1.splice(i, 1);
                                break;
                            }
                        }
                        for (var i = 0; i < answerLetters_1.length; i++) {
                            if (answerLetters_1[i] === '\u05C1') {
                                answerLetters_1.splice(i, 1);
                                shinDot = true;
                                break;
                            }
                        }
                        for (var i = 0; i < answerLetters_1.length; i++) {
                            if (answerLetters_1[i] === '\u05C2') {
                                answerLetters_1.splice(i, 1);
                                sinDot = true;
                                break;
                            }
                        }
                        if (shinDot) {
                            answerLetters_1.push('ש' + '\u05C1');
                        }
                        if (sinDot) {
                            answerLetters_1.push('ש' + '\u05C2');
                        }
                        for (var index = 0; index < answerLetters_1.length; index++) {
                            var l = answerLetters_1[index];
                            switch (l) {
                                case '-':
                                    if (configuration.charSet === 'hebrew') {
                                        additionalCons.push('י');
                                        additionalCons.push('ם');
                                        additionalCons.push('מ');
                                        additionalCons.push('ך');
                                        additionalCons.push('ת');
                                        additionalVowels.push('ְ');
                                        additionalVowels.push('ֵ');
                                        additionalVowels.push('ָ');
                                        additionalVowels.push('ַ');
                                        additionalVowels.push('ֶ');
                                        additionalVowels.push('ִ');
                                        additionalVowels.push('ֹ');
                                        additionalVowels.push('ֻ');
                                    }
                                    break;
                                case 'א':
                                    additionalCons.push('ע');
                                    break;
                                case 'ב':
                                    additionalCons.push('כ');
                                    break;
                                case 'ד':
                                    additionalCons.push('ר');
                                    additionalCons.push('ה');
                                    break;
                                case 'ח':
                                    additionalCons.push('ה');
                                    additionalCons.push('ת');
                                    break;
                                case 'ט':
                                    additionalCons.push('ת');
                                    break;
                                case 'ו':
                                    additionalCons.push('י');
                                    additionalCons.push('ז');
                                    break;
                                case 'י':
                                    additionalCons.push('ו');
                                    break;
                                case 'ק':
                                    additionalCons.push('כ');
                                    break;
                                case 'כ':
                                    additionalCons.push('ק');
                                    additionalCons.push('ב');
                                    break;
                                case 'ר':
                                    additionalCons.push('ד');
                                    additionalCons.push('ה');
                                    break;
                                case 'ש' + '\u05C1':
                                    additionalCons.push('ש' + '\u05C2');
                                    break;
                                case 'ש' + '\u05C2':
                                    additionalCons.push('ש' + '\u05C1');
                                    break;
                                case 'ת':
                                    additionalCons.push('ט');
                                    additionalCons.push('ע');
                                    break;
                                case 'ך':
                                    additionalCons.push('כ');
                                    additionalCons.push('ו');
                                    additionalVowels.push('\u05B9');
                                    break;
                                case 'ף':
                                    additionalCons.push('פ');
                                    additionalCons.push('ך');
                                    break;
                                case 'ץ':
                                    additionalCons.push('צ');
                                    break;
                                case 'ם':
                                    additionalCons.push('מ');
                                    additionalCons.push('ן');
                                    break;
                                case 'ן':
                                    additionalCons.push('נ');
                                    additionalCons.push('ם');
                                    break;
                                case '\u05B8':
                                    additionalVowels.push('\u05B8');
                                    additionalVowels.push('\u05B3');
                                    break;
                                case '\u05B3':
                                    additionalVowels.push('\u05B0');
                                    additionalVowels.push('\u05B8');
                                    break;
                                case '\u05B7':
                                    additionalVowels.push('\u05B8');
                                    additionalVowels.push('\u05B2');
                                    break;
                                case '\u05B2':
                                    additionalVowels.push('\u05B0');
                                    additionalVowels.push('\u05B7');
                                    break;
                                case '\u05B0':
                                    additionalVowels.push('\u05B2');
                                    additionalVowels.push('\u05B1');
                                    additionalVowels.push('\u05B3');
                                    break;
                                case '\u05B5':
                                    additionalVowels.push('\u05B6');
                                    break;
                                case '\u05B6':
                                    additionalVowels.push('\u05B5');
                                    additionalVowels.push('\u05B1');
                                    break;
                                case '\u05B6':
                                    additionalVowels.push('\u05B0');
                                    additionalVowels.push('\u05B6');
                                    break;
                                case '\u05B9':
                                    additionalVowels.push('\u05BB');
                                    break;
                                case '\u05BB':
                                    additionalVowels.push('\u05B9');
                                    break;
                                case 'β':
                                    additionalCons.push('δ');
                                    break;
                                case 'γ':
                                    additionalCons.push('κ');
                                    break;
                                case 'δ':
                                    additionalCons.push('β');
                                    break;
                                case 'ζ':
                                    additionalCons.push('ξ');
                                    break;
                                case 'θ':
                                    additionalCons.push('τ');
                                    break;
                                case 'κ':
                                    additionalCons.push('γ');
                                    break;
                                case 'λ':
                                    additionalCons.push('μ');
                                    break;
                                case 'μ':
                                    additionalCons.push('ν');
                                    break;
                                case 'ν':
                                    additionalCons.push('μ');
                                    break;
                                case 'ξ':
                                    additionalCons.push('ζ');
                                    break;
                                case 'π':
                                    additionalCons.push('ψ');
                                    break;
                                case 'ρ':
                                    additionalCons.push('λ');
                                    break;
                                case 'σ':
                                    additionalCons.push('ς');
                                    break;
                                case 'ς':
                                    additionalCons.push('σ');
                                    break;
                                case 'τ':
                                    additionalCons.push('θ');
                                    break;
                                case 'φ':
                                    additionalCons.push('θ');
                                    break;
                                case 'χ':
                                    break;
                                case 'ψ':
                                    additionalCons.push('π');
                                    break;
                                case 'α':
                                    additionalVowels.push('η');
                                    additionalVowels.push('ε');
                                    break;
                                case 'ε':
                                    additionalVowels.push('ι');
                                    break;
                                case 'η':
                                    additionalVowels.push('ε');
                                    break;
                                case 'ι':
                                    additionalVowels.push('ε');
                                    break;
                                case 'υ':
                                    additionalVowels.push('η');
                                    break;
                                case 'ο':
                                    additionalVowels.push('η');
                                    additionalVowels.push('ω');
                                    break;
                                case 'ω':
                                    additionalVowels.push('ο');
                                    break;
                            }
                        }
                        additionalCons = additionalCons.sort(function () {
                            return .5 - Math.random();
                        });
                        additionalVowels = additionalVowels.sort(function () {
                            return .5 - Math.random();
                        });
                        answerLettersRandom = answerLetters_1.concat(additionalCons.slice(0, 3))
                            .concat(additionalVowels.slice(0, 3));
                        $.each(answerLettersRandom, function (i, el) {
                            if ($.inArray(el, showLetters_1) === -1)
                                showLetters_1.push(el);
                        });
                        if (configuration.charSet === 'hebrew' && $.inArray('-', showLetters_1) === -1)
                            showLetters_1.push('-');
                        showLetters_1.sort();
                        var vf = $("<div class=\"inputquizitem\"><span class=\"inputshow ".concat(PanelQuestion.charclass(featset), "\"></div>"));
                        var letterinput_1 = $('<div class="letterinput"></div>');
                        vf.append(letterinput_1);
                        if (charset.isRtl)
                            letterinput_1.append("<div class=\"delbutton\" id=\"bs_".concat(quizItemID, "\">&rarr;</div>"));
                        else
                            letterinput_1.append("<div class=\"delbutton\" id=\"bs_".concat(quizItemID, "\">&larr;</div>"));
                        this_2.keytable.add(+qoid, headInd, 'Backspace', "bs_".concat(quizItemID), 2);
                        showLetters_1.forEach(function (letter, i) {
                            var sc = Foreign2Shortcut.get(letter);
                            if (sc != '?') {
                                var sc_id = 'sc' + sc.charCodeAt(0);
                                letterinput_1.append("<div class=\"inputbutton ".concat(PanelQuestion.charclass(featset), "\" id=\"").concat(sc_id, "_").concat(quizItemID, "\" data-letter=\"").concat(letter, "\">").concat(letter, "<span class=\"shortcut keybutton\">").concat(sc, "</span></div>"));
                                _this.keytable.add(+qoid, headInd, sc, "".concat(sc_id, "_").concat(quizItemID), 2);
                            }
                            else
                                letterinput_1.append("<div class=\"inputbutton ".concat(PanelQuestion.charclass(featset), "\" data-letter=\"").concat(letter, "\">").concat(letter, "</div>"));
                        });
                        hasForeignInput = true;
                        cwyn = new ComponentWithYesNo(vf, COMPONENT_TYPE.textFieldForeign);
                        cwyn.addKeypressListener();
                        cwyn.addChangeListener();
                        v = cwyn.getJQuery();
                    }
                    else {
                        var vf = $("<div class=\"inputquizitem\"><input id=\"keyinp_".concat(+qoid, "_").concat(headInd, "\" data-qoid=\"").concat(+qoid, "\" data-row=\"").concat(headInd, "\" autocomplete=\"off\" type=\"text\"></div>"));
                        this_2.keyinps.push("keyinp_".concat(+qoid, "_").concat(headInd));
                        cwyn = new ComponentWithYesNo(vf, COMPONENT_TYPE.textField);
                        cwyn.addKeypressListener();
                        cwyn.addChangeListener();
                        v = cwyn.getJQuery();
                    }
                    this_2.vAnswers.push(new Answer(cwyn, null, trimmedAnswer, featset.matchregexp));
                }
                else if (featType === 'integer') {
                    var intf = $('<input type="number">');
                    var cwyn = new ComponentWithYesNo(intf, COMPONENT_TYPE.textField);
                    cwyn.addKeypressListener();
                    v = cwyn.getJQuery();
                    this_2.vAnswers.push(new Answer(cwyn, null, correctAnswer, null));
                }
                else if (featType.substr(0, 8) === 'list of ') {
                    var subFeatType = featType.substr(8);
                    var values = typeinfo.enum2values[subFeatType];
                    var swsValues = [];
                    for (var i = 0, len = values.length; i < len; ++i)
                        swsValues.push(new StringWithSort(getFeatureValueFriendlyName(subFeatType, values[i], false, false), values[i]));
                    swsValues.sort(function (a, b) { return StringWithSort.compare(a, b); });
                    swsValues.push(new StringWithSort("<i>".concat(localize('none_of_these'), "</i>"), 'none_of_these'));
                    var selections = $('<table class="list-of"></table>');
                    selections.append("<tr><td colspan=\"3\">".concat(localize('select_1_or_more'), "</td></tr>"));
                    var numberOfItems = swsValues.length;
                    var numberOfRows = Math.floor((numberOfItems + 2) / 3);
                    for (var r = 0; r < numberOfRows; ++r) {
                        var row = $('<tr></tr>');
                        for (var c = 0; c < 3; c++) {
                            var ix = r + c * numberOfRows;
                            if (ix < numberOfItems) {
                                var sc = String.fromCharCode(ix + 97);
                                row.append('<td style="text-align:left"><div class="selectbutton">'
                                    + "<input type=\"checkbox\" id=\"".concat(swsValues[ix].getInternal(), "_").concat(quizItemID, "\" value=\"").concat(swsValues[ix].getInternal(), "\">")
                                    + "<label for=\"".concat(swsValues[ix].getInternal(), "_").concat(quizItemID, "\">").concat(swsValues[ix].getString(), "<span class=\"shortcut multioption\">").concat(sc, "</span></label>")
                                    + '</div></td>');
                                this_2.keytable.add(+qoid, headInd, sc, "".concat(swsValues[ix].getInternal(), "_").concat(quizItemID), 3);
                            }
                            else
                                row.append('<td></td>');
                        }
                        selections.append(row);
                    }
                    var cwyn = new ComponentWithYesNo(selections, COMPONENT_TYPE.checkBoxes);
                    cwyn.addChangeListener();
                    v = cwyn.getJQuery();
                    if (correctAnswer === '()')
                        correctAnswer = '(none_of_these)';
                    this_2.vAnswers.push(new Answer(cwyn, null, correctAnswer, null));
                }
                else {
                    var values = typeinfo.enum2values[featType];
                    if (values == null) {
                        v.append('<tr><td>&nbsp;</td>'
                            + questionheaders[headInd]
                            + '<td>QuestionPanel.UnknType</td></tr>');
                    }
                    else {
                        var quiz_div_2 = $('<div class="quizitem"></div>');
                        var optArray = [];
                        var cwyn = new ComponentWithYesNo(quiz_div_2, COMPONENT_TYPE.comboBox);
                        cwyn.addChangeListener();
                        var correctAnswerFriendly = getFeatureValueFriendlyName(featType, correctAnswer, false, false);
                        var hasAddedOther = false;
                        var correctIsOther = featset.otherValues && featset.otherValues.indexOf(correctAnswer) !== -1 ||
                            hideFeatures && hideFeatures.indexOf(correctAnswer) !== -1;
                        for (var valix in values) {
                            if (isNaN(+valix))
                                continue;
                            var s = values[+valix];
                            if (featset.hideValues && featset.hideValues.indexOf(s) !== -1)
                                continue;
                            if (featset.otherValues && featset.otherValues.indexOf(s) !== -1 ||
                                hideFeatures && hideFeatures.indexOf(s) !== -1) {
                                if (!hasAddedOther) {
                                    hasAddedOther = true;
                                    var item = new StringWithSort('#1000 ' + localize('other_value'), 'othervalue');
                                    var option = $('<div class="selectbutton">'
                                        + "<input type=\"radio\" id=\"".concat(item.getInternal(), "_").concat(quizItemID, "\" name=\"quizitem_").concat(quizItemID, "\" value=\"").concat(item.getInternal(), "\">")
                                        + "<label for=\"".concat(item.getInternal(), "_").concat(quizItemID, "\">").concat(item.getString(), "</label>")
                                        + '</div>');
                                    option
                                        .data('sws', item)
                                        .data('char', item.getString()[0].toLowerCase())
                                        .data('id', "".concat(item.getInternal(), "_").concat(quizItemID));
                                    optArray.push(option);
                                    if (correctIsOther)
                                        this_2.vAnswers.push(new Answer(cwyn, item, localize('other_value'), null));
                                }
                            }
                            else {
                                var sFriendly = getFeatureValueFriendlyName(featType, s, false, false);
                                var item = new StringWithSort(sFriendly, s);
                                var option = $('<div class="selectbutton">'
                                    + "<input type=\"radio\" id=\"".concat(item.getInternal(), "_").concat(quizItemID, "\" name=\"quizitem_").concat(quizItemID, "\" value=\"").concat(item.getInternal(), "\">")
                                    + "<label for=\"".concat(item.getInternal(), "_").concat(quizItemID, "\">").concat(item.getString(), "</label>")
                                    + '</div>');
                                option
                                    .data('sws', item)
                                    .data('char', item.getString()[0].toLowerCase())
                                    .data('id', "".concat(item.getInternal(), "_").concat(quizItemID));
                                optArray.push(option);
                                if (sFriendly === correctAnswerFriendly)
                                    this_2.vAnswers.push(new Answer(cwyn, item, s, null));
                            }
                        }
                        optArray.sort(function (a, b) { return StringWithSort.compare(a.data('sws'), b.data('sws')); });
                        $.each(optArray, function (ix, o) {
                            quiz_div_2.append(o);
                            _this.keytable.add(+qoid, headInd, o.data('char'), o.data('id'), 1);
                        });
                        v = cwyn.getJQuery();
                    }
                }
                var quizRow = $("<tr id=\"row_".concat(+qoid, "_").concat(headInd, "\"></tr>"));
                quizRow.append("<td><span style=\"display:none\" id=\"ptr_".concat(+qoid, "_").concat(headInd, "\">&gt;</span></td>"));
                quizRow.append(questionheaders[headInd]);
                quizRow.append(v);
                quizTab.append(quizRow);
                ++headInd;
            };
            for (var rfi in requestFeatures) {
                _loop_3(rfi);
            }
            this_2.answersPerCard.push(this_2.vAnswers.length);
            Cursor.set();
        };
        var this_2 = this;
        for (var qoid in qoFeatures) {
            _loop_2(qoid);
        }
        for (var _i = 0, _a = this.keyinps; _i < _a.length; _i++) {
            var keyi = _a[_i];
            $("#".concat(keyi))
                .keydown(this, this.textfield_keydown)
                .focus(function (event) { return Cursor.set($(event.target).data('qoid'), $(event.target).data('row'), true); })
                .blur(function () { return $('body').unbind('keydown').keydown(_this, _this.body_keydown); });
        }
        $('body')
            .unbind('keydown')
            .keydown(this, this.body_keydown);
        this.subQuizMax = quizCardNum;
        var quizCard = $('.quizcard');
        if (!exam_mode) {
            quizCard.append('<div class="buttonlist1">'
                + "<button class=\"btn btn-quiz\" id=\"check_answer\" type=\"button\">".concat(localize('check_answer'), "</button>")
                + "<button class=\"btn btn-quiz\" id=\"show_answer\" type=\"button\">".concat(localize('show_answer'), "</button>")
                + '</div>');
        }
        if (quizCardNum > 1) {
            var prevsubquiz = $('<div class="prev-next-btn prev" id="prevsubquiz">&#10094;</div>');
            quizContainer.prepend(prevsubquiz);
            prevsubquiz.hide();
            quizContainer.append('<div class="prev-next-btn next" id="nextsubquiz">&#10095;</div>');
            $('button#next_question').hide();
            $('button#finish').hide();
            $('button#finishNoStats').hide();
        }
        $('div.inputbutton').click(function () {
            var letter = $(this).data('letter');
            $(this)
                .parent().siblings('span')
                .text($(this).parent().siblings('span').text() + letter);
            $(this).change();
            return false;
        });
        $('div.delbutton').click(function () {
            var value = String($(this).parent().siblings('span').text());
            $(this)
                .parent().siblings('span')
                .text(value.slice(0, -1));
            return false;
        });
        $('#prevsubquiz').off('click');
        $('#prevsubquiz').on('click', function () { return _this.prevNextSubQuestion(-1); });
        $('#nextsubquiz').off('click');
        $('#nextsubquiz').on('click', function () { return _this.prevNextSubQuestion(1); });
        $('button#check_answer').off('click');
        $('button#check_answer').on('click', function () {
            var firstAns = _this.subQuizIndex == 0 ? 0 : _this.answersPerCard[_this.subQuizIndex - 1];
            var lastAns = _this.answersPerCard[_this.subQuizIndex];
            for (var aix = firstAns; aix < lastAns; ++aix) {
                var a = _this.vAnswers[aix];
                a.checkIt(false, true);
            }
        });
        $('button#show_answer').off('click');
        $('button#show_answer').on('click', function () {
            var firstAns = _this.subQuizIndex == 0 ? 0 : _this.answersPerCard[_this.subQuizIndex - 1];
            var lastAns = _this.answersPerCard[_this.subQuizIndex];
            for (var aix = firstAns; aix < lastAns; ++aix) {
                var a = _this.vAnswers[+aix];
                a.showIt();
                a.checkIt(true, true);
            }
        });
        switch (resizer.getWindowSize()) {
            case 'lg':
            case 'xl':
                $('.shortcut').show();
                break;
            default:
                $('.shortcut').hide();
                break;
        }
        this.question_stat.start_time = Math.round((new Date()).getTime() / 1000);
    }
    PanelQuestion.charclass = function (featset) {
        return featset.foreignText ? charset.foreignClass
            : featset.transliteratedText ? charset.transliteratedClass : '';
    };
    PanelQuestion.prototype.updateQuestionStat = function () {
        this.question_stat.end_time = Math.round((new Date()).getTime() / 1000);
        for (var i = 0, len = this.vAnswers.length; i < len; ++i) {
            var ans = this.vAnswers[i];
            ans.commitIt();
            this.question_stat.req_feat.correct_answer.push(ans.correctAnswer());
            this.question_stat.req_feat.users_answer.push(ans.usersAnswer());
            this.question_stat.req_feat.users_answer_was_correct.push(ans.usersAnswerWasCorrect());
        }
        return this.question_stat;
    };
    PanelQuestion.prototype.buildQuizObjectFeatureList = function () {
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
    PanelQuestion.prototype.prevNextSubQuestion = function (n) {
        if (this.subQuizIndex + n >= 0 && this.subQuizIndex + n < this.subQuizMax) {
            this.subQuizIndex += n;
        }
        var i;
        var slides = $('#quizcontainer').find('.quizcard');
        if (this.subQuizIndex < 1)
            $('#prevsubquiz').hide();
        else
            $('#prevsubquiz').show();
        if (this.subQuizIndex < slides.length - 1) {
            $('#nextsubquiz').show();
            $('button#next_question').hide();
            $('button#finish').hide();
            $('button#finishNoStats').hide();
        }
        ;
        if (this.subQuizIndex === slides.length - 1) {
            $('#nextsubquiz').hide();
            $('button#next_question').show();
            $('button#finish').show();
            $('button#finishNoStats').show();
        }
        ;
        for (i = 0; i < slides.length; i++) {
            if (i === this.subQuizIndex) {
                slides.slice(i).css({ "display": "block" });
            }
            else {
                slides.slice(i).css({ "display": "none" });
            }
        }
        Cursor.set(this.subQuizIndex);
    };
    PanelQuestion.prototype.location_info = function (dict) {
        var smo = dict.getSingleMonadObject(getFirst(this.sentence));
        this.location = smo.bcv_loc;
        this.question_stat.location = '';
        for (var unix in configuration.universeHierarchy) {
            var unixi = +unix;
            if (isNaN(unixi))
                continue;
            this.question_stat.location += smo.bcv[unixi] + (unixi != 2 ? ', ' : '');
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
    };
    PanelQuestion.kbid = 1;
    return PanelQuestion;
}());
var StringWithSort = (function () {
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
    StringWithSort.prototype.getInternal = function () {
        return this.internal;
    };
    StringWithSort.prototype.getString = function () {
        return this.str;
    };
    StringWithSort.stripSortIndex = function (s) {
        return (s.length > 0 && s.charAt(0) === '#')
            ? s.substring(s.indexOf(' ') + 1)
            : s;
    };
    StringWithSort.compare = function (sws1, sws2) {
        if (sws1.sort == -1 || sws2.sort == -1 || sws1.sort == sws2.sort) {
            if (sws1.internal === 'othervalue')
                return 1;
            if (sws2.internal === 'othervalue')
                return -1;
            var s1 = sws1.str.toLowerCase();
            var s2 = sws2.str.toLowerCase();
            return s1 < s2 ? -1 : s1 > s2 ? 1 : 0;
        }
        else
            return sws1.sort < sws2.sort ? -1 : 1;
    };
    return StringWithSort;
}());
var ShowFeatStatistics = (function () {
    function ShowFeatStatistics() {
        this.names = [];
        this.values = [];
    }
    return ShowFeatStatistics;
}());
var ReqFeatStatistics = (function () {
    function ReqFeatStatistics() {
        this.names = [];
        this.correct_answer = [];
        this.users_answer = [];
        this.users_answer_was_correct = [];
    }
    return ReqFeatStatistics;
}());
var QuestionStatistics = (function () {
    function QuestionStatistics() {
        this.show_feat = new ShowFeatStatistics();
        this.req_feat = new ReqFeatStatistics();
    }
    return QuestionStatistics;
}());
var QuizStatistics = (function () {
    function QuizStatistics(quizid) {
        this.quizid = quizid;
        this.questions = [];
        this.question_count = Object.keys(quizdata.id2FeatVal).length;
    }
    return QuizStatistics;
}());
var Quiz = (function () {
    function Quiz(qid, inExam) {
        var _this = this;
        this.currentDictIx = -1;
        this.currentPanelQuestion = null;
        this.quiz_statistics = new QuizStatistics(qid);
        this.exam_mode = inExam;
        $('button#next_question').on('click', function () { return _this.nextQuestion(false); });
        $('button#finish').on('click', function () { return _this.finishQuiz(true); });
        $('button#finishNoStats').on('click', function () { return _this.finishQuiz(false); });
    }
    Quiz.prototype.nextQuestion = function (first) {
        if (this.currentPanelQuestion !== null)
            this.quiz_statistics.questions.push(this.currentPanelQuestion.updateQuestionStat());
        else if (quizdata.fixedquestions > 0) {
            $('button#finish').attr('disabled', 'disabled');
            $('button#finishNoStats').attr('disabled', 'disabled');
        }
        if (++this.currentDictIx < dictionaries.sentenceSets.length) {
            $('#textarea').empty();
            $('#quizcontainer').empty();
            $('.quizcard').empty();
            var currentDict = new Dictionary(dictionaries, this.currentDictIx, quizdata);
            $('#quizdesc').html(quizdata.desc);
            $('#quizdesc').find('a').attr('target', '_blank');
            if (supportsProgress)
                $('progress#progress').attr('value', this.currentDictIx + 1).attr('max', dictionaries.sentenceSets.length);
            else
                $('div#progressbar').progressbar({ value: this.currentDictIx + 1, max: dictionaries.sentenceSets.length });
            $('#progresstext').html((this.currentDictIx + 1) + '/' + dictionaries.sentenceSets.length);
            this.currentPanelQuestion = new PanelQuestion(quizdata, currentDict, this.exam_mode);
            if (this.currentDictIx + 1 === dictionaries.sentenceSets.length) {
                $('button#next_question').attr('disabled', 'disabled');
                $('button#finish').removeAttr('disabled');
                $('button#finishNoStats').removeAttr('disabled');
            }
        }
        else
            alert('No more questions');
        util.FollowerBox.resetCheckboxCounters();
        $('#grammarbuttongroup input:enabled:checked').trigger('change');
        $('html, body').animate({
            scrollTop: first ? 0 : $('#myview').offset().top - 5
        }, 50);
    };
    Quiz.prototype.finishQuiz = function (gradingFlag) {
        var _this = this;
        if (quizdata.quizid == -1) {
            if (this.exam_mode)
                window.location.replace(site_url + 'exam/active_exams');
            else
                window.location.replace(site_url + 'text/select_quiz');
        }
        else {
            if (this.currentPanelQuestion === null)
                alert('System error: No current question panel');
            else
                this.quiz_statistics.questions.push(this.currentPanelQuestion.updateQuestionStat());
            this.quiz_statistics.grading = gradingFlag;
            $('#textcontainer').html('<p>' + localize('sending_statistics') + '</p>');
            $.post(site_url + 'statistics/update_stat', this.quiz_statistics)
                .done(function () {
                if (!_this.exam_mode)
                    window.location.replace(site_url + 'text/select_quiz');
                else {
                    $.get(site_url + 'statistics/update_exam_quiz_stat?examid=' + $('#exam_id').html() + '&quizid=' + $('#quiz_id').html() + '&exercise_lst=' + $('#exercise_lst').html())
                        .done(function () {
                        if ($('#exercise_lst').html()) {
                            var exercise_lst = $('#exercise_lst').html().split("~");
                            var next_quiz = exercise_lst.shift();
                            window.location.replace(site_url + 'text/show_quiz?quiz=' + next_quiz + '&count=10&examid=' + $('#exam_id').html() + '&exercise_lst=' + exercise_lst.join("~"));
                        }
                        else
                            window.location.replace(site_url + 'exams/exam_done');
                    })
                        .fail(function (jqXHR, textStatus, errorThrow) {
                        $('#textcontainer')
                            .removeClass('textcontainer-background')
                            .addClass('alert alert-danger')
                            .html("<h1>".concat(localize('error_response'), "</h1><p>").concat(errorThrow, "</p>"));
                    });
                }
            })
                .fail(function (jqXHR, textStatus, errorThrow) {
                $('#textcontainer')
                    .removeClass('textcontainer-background')
                    .addClass('alert alert-danger')
                    .html("<h1>".concat(localize('error_response'), "</h1><p>").concat(errorThrow, "</p>"));
            });
        }
    };
    return Quiz;
}());
var resizer;
(function (resizer) {
    function getWindowSize() {
        return $('.device-sizer:visible').attr('data-size');
    }
    resizer.getWindowSize = getWindowSize;
    function sizeIs(siz) {
        return $('.device-is-' + siz).is(':visible');
    }
    resizer.sizeIs = sizeIs;
    var timers = {};
    function addResizeListener(callback, data, uniqueId) {
        $(window).resize(function () {
            if (timers[uniqueId])
                clearTimeout(timers[uniqueId]);
            timers[uniqueId] = setTimeout(function () { return callback(data); }, 500);
        });
    }
    resizer.addResizeListener = addResizeListener;
    $(function () {
        $('body')
            .append("<div class=\"d-block            d-sm-none device-is-xs device-sizer\" data-size=\"xs\"></div>")
            .append("<div class=\"d-none  d-sm-block d-md-none device-is-sm device-sizer\" data-size=\"sm\"></div>")
            .append("<div class=\"d-none  d-md-block d-lg-none device-is-md device-sizer\" data-size=\"md\"></div>")
            .append("<div class=\"d-none  d-lg-block d-xl-none device-is-lg device-sizer\" data-size=\"lg\"></div>")
            .append("<div class=\"d-none  d-xl-block           device-is-xl device-sizer\" data-size=\"xl\"></div>");
    });
})(resizer || (resizer = {}));
var supportsProgress;
var charset;
var inQuiz;
var quiz;
var accordion_width;
var indentation_width;
$(function () {
    inQuiz = $('#quizcontainer').length > 0;
    var x = document.createElement('progress');
    supportsProgress = x.max != undefined;
    configuration.maxLevels = configuration.sentencegrammar.length + 1;
    charset = new Charset(configuration.charSet);
    $('#textarea').addClass(charset.isRtl ? 'rtl' : 'ltr');
    for (var i in configuration.sentencegrammar) {
        if (isNaN(+i))
            continue;
        addMethodsSgi(configuration.sentencegrammar[+i], configuration.sentencegrammar[+i].objType);
    }
    var generateCheckboxes = new GrammarSelectionBox();
    $('#gramtabs').append(generateCheckboxes.generateHtml());
    generateCheckboxes.setHandlers();
    GrammarSelectionBox.clearBoxes(false);
    accordion_width = GrammarSelectionBox.buildGrammarAccordion();
    if (inQuiz) {
        $('#cleargrammar').on('click', function () { GrammarSelectionBox.clearBoxes(true); });
        if (supportsProgress)
            $('div#progressbar').hide();
        else
            $('progress#progress').hide();
        quiz = new Quiz(quizdata.quizid, $('#exam_id').length > 0);
        quiz.nextQuestion(true);
        $('#gramtabs .selectbutton input:enabled:checked').trigger('change', 'manual');
    }
    else {
        $('#cleargrammar').on('click', function () { GrammarSelectionBox.clearBoxes(true); });
        var currentDict = new Dictionary(dictionaries, 0, null);
        currentDict.generateSentenceHtml(null);
        GrammarSelectionBox.setColorizeHandler();
        $('#gramtabs .selectbutton input:enabled:checked').trigger('change', 'manual');
        $('#color-limit').trigger('change', 'manual');
    }
});
