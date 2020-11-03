var __extends = (this && this.__extends) || (function () {
    var extendStatics = function (d, b) {
        extendStatics = Object.setPrototypeOf ||
            ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
            function (d, b) { for (var p in b) if (Object.prototype.hasOwnProperty.call(b, p)) d[p] = b[p]; };
        return extendStatics(d, b);
    };
    return function (d, b) {
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
            return _super.call(this, level, "#lev" + level + "_sb_cb") || this;
        }
        BorderFollowerBox.prototype.setit = function (val) {
            if (val) {
                $(".lev" + this.level + " > .gram").removeClass('dontshowit').addClass('showit');
                $(".lev" + this.level).removeClass('dontshowborder').addClass('showborder');
            }
            else {
                $(".lev" + this.level + " > .gram").removeClass('showit').addClass('dontshowit');
                $(".lev" + this.level).removeClass('showborder').addClass('dontshowborder');
            }
        };
        return BorderFollowerBox;
    }(FollowerBox));
    util.BorderFollowerBox = BorderFollowerBox;
    var SeparateLinesFollowerBox = (function (_super) {
        __extends(SeparateLinesFollowerBox, _super);
        function SeparateLinesFollowerBox(level) {
            return _super.call(this, level, "#lev" + level + "_seplin_cb") || this;
        }
        SeparateLinesFollowerBox.prototype.setit = function (val) {
            var oldSepLin = val ? 'noseplin' : 'seplin';
            var newSepLin = val ? 'seplin' : 'noseplin';
            $(".notdummy.lev" + this.level).removeClass(oldSepLin).addClass(newSepLin);
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
var GrammarSelectionBox = (function () {
    function GrammarSelectionBox() {
        this.checkboxes = '';
        this.subgroupgrammartabs = '';
        this.subgroupgrammardivs = '';
        this.addBr = new util.AddBetween('<br>');
        this.borderBoxes = [];
        this.separateLinesBoxes = [];
    }
    GrammarSelectionBox.adjustDivLevWidth = function (level) {
        $(".showborder.lev" + level).each(function (index) {
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
                    this.subgroupgrammartabs += "<div id=\"subgrammargroup\"><ul>";
                }
                this.subgroupgrammartabs += "<li><a class=\"subgrammargroup\" href=\"#" + getHtmlAttribFriendlyName(featNameLoc) + "\"><h3>" + featNameLoc + "</h3></a></li>";
                this.subgroupgrammardivs += "<div id=\"" + getHtmlAttribFriendlyName(featNameLoc) + "\">";
                this.subgroupgrammardivs += "<div id=\"grammarbuttongroup\">";
                break;
            case WHAT.groupend:
                this.subgroupgrammardivs += '</div></div>';
                break;
            case WHAT.feature:
            case WHAT.metafeature:
                var disabled = mayShowFeature(objType, origObjType, featName, sgiObj) ? '' : 'disabled';
                if (this.hasSeenGrammarGroup) {
                    this.subgroupgrammardivs += "<div class=\"selectbutton\"><input id=\"" + objType + "_" + featName + "_cb\" type=\"checkbox\" " + disabled + "><label for=\"" + objType + "_" + featName + "_cb\">" + featNameLoc + "</label></div>";
                }
                else {
                    this.checkboxes += "<div class=\"selectbutton\"><input id=\"" + objType + "_" + featName + "_cb\" type=\"checkbox\" " + disabled + "><label for=\"" + objType + "_" + featName + "_cb\">" + featNameLoc + "</label></div>";
                }
                break;
        }
    };
    GrammarSelectionBox.prototype.makeInitCheckBoxForObj = function (level) {
        if (level == 0) {
            if (charset.isHebrew) {
                return "<div class=\"selectbutton\"><input id=\"ws_cb\" type=\"checkbox\">" +
                    ("<label for=\"ws_cb\">" + localize('word_spacing') + "</label></div>");
            }
            else
                return '';
        }
        else {
            return "<div class=\"selectbutton\"><input id=\"lev" + level + "_seplin_cb\" type=\"checkbox\">" +
                ("<label for=\"lev" + level + "_seplin_cb\">" + localize('separate_lines') + "</label></div>") +
                ("<div class=\"selectbutton\"><input id=\"lev" + level + "_sb_cb\" type=\"checkbox\">") +
                ("<label for=\"lev" + level + "_sb_cb\">" + localize('show_border') + "</label></div>");
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
            this.checkboxes += "<li><a class=\"gramtabs\" href=\"#" + getHtmlAttribFriendlyName(getObjectFriendlyName(objType)) + "\"><h3>" + getObjectFriendlyName(objType) + "</h3></a></li>";
        }
        this.checkboxes += "</ul>";
        for (var level in configuration.sentencegrammar) {
            var leveli = +level;
            if (isNaN(leveli))
                continue;
            var objType = configuration.sentencegrammar[leveli].objType;
            this.checkboxes += "<div id=\"" + getHtmlAttribFriendlyName(getObjectFriendlyName(objType)) + "\">";
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
        this.checkboxes += "<p><a class=\"btn btn-clear\" id=\"cleargrammar\" href=\"#myview\">" + localize('clear_grammar') + "</a></p>";
        return this.checkboxes;
    };
    GrammarSelectionBox.prototype.setHandlerCallback = function (whattype, objType, featName, featNameLoc, leveli) {
        var _this = this;
        if (whattype != WHAT.feature && whattype != WHAT.metafeature)
            return;
        if (leveli === 0) {
            $("#" + objType + "_" + featName + "_cb").on('change', function (e) {
                if ($(e.currentTarget).prop('checked')) {
                    if (!inQuiz) {
                        sessionStorage.setItem($(e.currentTarget).prop('id'), configuration.propertiesName);
                    }
                    $(".wordgrammar." + featName).removeClass('dontshowit').addClass('showit');
                    _this.wordSpaceBox.implicit(true);
                }
                else {
                    if (!inQuiz) {
                        sessionStorage.removeItem($(e.currentTarget).prop('id'));
                    }
                    $(".wordgrammar." + featName).removeClass('showit').addClass('dontshowit');
                    _this.wordSpaceBox.implicit(false);
                }
                for (var lev = 1; lev < configuration.maxLevels - 1; ++lev)
                    GrammarSelectionBox.adjustDivLevWidth(lev);
            });
        }
        else {
            $("#" + objType + "_" + featName + "_cb").on('change', function (e) {
                if ($(e.currentTarget).prop('checked')) {
                    if (!inQuiz) {
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
    GrammarSelectionBox.prototype.setHandlers = function () {
        var _this = this;
        var _loop_1 = function (level) {
            var leveli = +level;
            if (isNaN(leveli))
                return "continue";
            var sg = configuration.sentencegrammar[leveli];
            if (leveli === 0) {
                this_1.wordSpaceBox = new util.WordSpaceFollowerBox(leveli);
                $('#ws_cb').on('change', function (e) {
                    if ($(e.currentTarget).prop('checked')) {
                        if (!inQuiz) {
                            sessionStorage.setItem($(e.currentTarget).prop('id'), configuration.propertiesName);
                        }
                        _this.wordSpaceBox.explicit(true);
                    }
                    else {
                        if (!inQuiz) {
                            sessionStorage.removeItem($(e.currentTarget).prop('id'));
                        }
                        _this.wordSpaceBox.explicit(false);
                    }
                    for (var lev = 1; lev < configuration.maxLevels - 1; ++lev)
                        GrammarSelectionBox.adjustDivLevWidth(lev);
                });
            }
            else {
                this_1.separateLinesBoxes[leveli] = new util.SeparateLinesFollowerBox(leveli);
                $("#lev" + leveli + "_seplin_cb").on('change', leveli, function (e) {
                    if ($(e.currentTarget).prop('checked')) {
                        if (!inQuiz) {
                            sessionStorage.setItem($(e.currentTarget).prop('id'), configuration.propertiesName);
                        }
                        _this.separateLinesBoxes[e.data].explicit(true);
                    }
                    else {
                        if (!inQuiz) {
                            sessionStorage.removeItem($(e.currentTarget).prop('id'));
                        }
                        _this.separateLinesBoxes[e.data].explicit(false);
                    }
                });
                this_1.borderBoxes[leveli] = new util.BorderFollowerBox(leveli);
                $("#lev" + leveli + "_sb_cb").on('change', leveli, function (e) {
                    if ($(e.currentTarget).prop('checked')) {
                        if (!inQuiz) {
                            sessionStorage.setItem($(e.currentTarget).prop('id'), configuration.propertiesName);
                        }
                        _this.borderBoxes[e.data].explicit(true);
                    }
                    else {
                        if (!inQuiz) {
                            sessionStorage.removeItem($(e.currentTarget).prop('id'));
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
        $('input[type="checkbox"]').prop('checked', false);
        if (!inQuiz) {
            if (force) {
                for (var i in sessionStorage) {
                    if (sessionStorage[i] == configuration.propertiesName) {
                        sessionStorage.removeItem(i);
                        $('#' + i).prop('checked', false);
                        $('#' + i).trigger('change');
                    }
                }
            }
            else {
                for (var i in sessionStorage) {
                    if (sessionStorage[i] == configuration.propertiesName)
                        $('#' + i).prop('checked', true);
                }
            }
        }
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
        var tabs3 = $('#subgrammargroup').tabs({
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
            if (qd.quizFeatures.dontShow)
                text = "(" + ++DisplaySingleMonadObject.itemIndex + ")";
            else
                text = this.displayedMo.mo.features[configuration.surfaceFeature];
            text = "<em>" + text + "</em>";
            textDisplayClass = ' text-danger';
        }
        else {
            text = this.displayedMo.mo.features[configuration.surfaceFeature];
            if (!containsMonad(quizMonads, this.monad))
                textDisplayClass = ' text-muted';
        }
        var chapterstring = chapter == null ? '' : "<span class=\"chapter\">" + chapter + "</span>&#x200a;";
        var versestring = verse == null ? '' : "<span class=\"verse\">" + verse + "</span>";
        var refstring;
        if (refs === null)
            refstring = '';
        else if (refs.length === 4)
            refstring = "<a target=\"_blank\" title=\"" + localize('click_for_picture') + "\" href=\"http://resources.3bmoodle.dk/link.php?picno=" + refs[3] + "\"><img src=\"" + site_url + "images/p.png\"></a>";
        else
            refstring = "<a target=\"_blank\" title=\"" + localize('click_for_pictures') + "\" href=\"http://resources.3bmoodle.dk/img.php?book=" + refs[0] + "&chapter=" + refs[1] + "&verse=" + refs[2] + "\"><img src=\"" + site_url + "images/pblue.png\"></a>";
        var urlstring = '';
        if (urls !== null)
            for (var uix = 0; uix < urls.length; ++uix)
                urlstring += "<a target=\"_blank\" title=\"" + localize(urlTypeString[urls[uix][1]]) + "\" href=\"" + urls[uix][0] + "\"><img src=\"" + site_url + "images/" + urls[uix][1] + ".png\"></a>";
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
                    grammar += "<span class=\"wordgrammar dontshowit " + featName + " " + wordclass + "\">" + featValLoc + "</span>";
                    break;
                case WHAT.metafeature:
                    grammar += "<span class=\"wordgrammar dontshowit " + featName + " ltr\">" + featValLoc + "</span>";
                    break;
            }
        });
        var follow_space = '<span class="wordspace"> </span>';
        if (charset.isHebrew) {
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
        return $("<span class=\"textblock inline\"><span class=\"textdisplay " + (charset.foreignClass + textDisplayClass) + "\" data-idd=\"" + smo.mo.id_d + "\">" + versestring + refstring + urlstring + text + "</span>" + grammar + "</span>" + follow_space);
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
        var spanclass = "lev" + this.level + " dontshowborder noseplin";
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
                        grammar += "<span class=\"xgrammar dontshowit " + objType + "_" + featName + "\">:" + featValLoc + "</span>";
                }
            });
        }
        var jq;
        if (this.isPatriarch) {
            jq = $("<span class=\"" + spanclass + "\"></span>");
        }
        else if (this.displayedMo.mo.name == "dummy") {
            jq = $("<span class=\"" + spanclass + "\"><span class=\"nogram dontshowit\" data-idd=\"" + this.displayedMo.mo.id_d + "\" data-mix=\"0\"></span></span>");
        }
        else if (configuration.databaseName == 'ETCBC4' && this.level == 2) {
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
            jq = $("<span class=\"notdummy " + spanclass + "\">"
                + ("<span class=\"gram dontshowit\" data-idd=\"" + this.displayedMo.mo.id_d + "\" data-mix=\"" + this.mix + "\">")
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
function mayShowFeature(oType, origOtype, feat, sgiObj) {
    if (!inQuiz)
        return true;
    if (sgiObj.mytype === 'GrammarMetaFeature') {
        for (var i in sgiObj.items) {
            if (isNaN(+i))
                continue;
            if (!mayShowFeature(oType, origOtype, sgiObj.items[+i].name, sgiObj.items[+i]))
                return false;
        }
        return true;
    }
    var qf = quizdata.quizFeatures;
    for (var ix = 0, len = qf.dontShowObjects.length; ix < len; ++ix)
        if (qf.dontShowObjects[ix].content === origOtype)
            return qf.dontShowObjects[ix].show === feat;
    if (oType !== qf.objectType)
        return true;
    for (var ix = 0, len = qf.requestFeatures.length; ix < len; ++ix)
        if (qf.requestFeatures[ix].name === feat)
            return false;
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
        this.hideWord = (qd != null && qd.quizFeatures.dontShow);
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
            res += "<tr>\n                        <td colspan=\"2\" class=\"tooltiphead\">" + getObjectFriendlyName(sengram.objType) + "</td>\n                    </tr>";
        }
        if (level === 0 && !this.hideWord) {
            res += "<tr>\n                        <td>" + localize('visual') + "</td>\n                        <td class=\"bol-tooltip leftalign " + charset.foreignClass + "\">" + monob.mo.features[configuration.surfaceFeature] + "</td>\n                    </tr>";
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
    Dictionary.prototype.getSingleMonadObject = function (monad) {
        return this.singleMonads[monad];
    };
    return Dictionary;
}());
var COMPONENT_TYPE;
(function (COMPONENT_TYPE) {
    COMPONENT_TYPE[COMPONENT_TYPE["textField"] = 0] = "textField";
    COMPONENT_TYPE[COMPONENT_TYPE["textFieldWithVirtKeyboard"] = 1] = "textFieldWithVirtKeyboard";
    COMPONENT_TYPE[COMPONENT_TYPE["comboBox1"] = 2] = "comboBox1";
    COMPONENT_TYPE[COMPONENT_TYPE["comboBox2"] = 3] = "comboBox2";
    COMPONENT_TYPE[COMPONENT_TYPE["checkBoxes"] = 4] = "checkBoxes";
})(COMPONENT_TYPE || (COMPONENT_TYPE = {}));
var ComponentWithYesNo = (function () {
    function ComponentWithYesNo(elem, elemType) {
        this.elem = elem;
        this.elemType = elemType;
        this.yesIcon = $("<img src=\"" + site_url + "/images/ok.png\" alt=\"Yes\">");
        this.noIcon = $("<img src=\"" + site_url + "/images/notok.png\" alt=\"No\">");
        this.noneIcon = $("<img src=\"" + site_url + "/images/none.png\" alt=\"None\">");
    }
    ComponentWithYesNo.prototype.getJQuery = function () {
        var spn = $('<td class="combobox"></td>').append([this.yesIcon, this.noIcon, this.noneIcon, this.elem]);
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
        if (this.elemType === COMPONENT_TYPE.comboBox2)
            return $(this.elem.children()[0]);
        else
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
                "border": "solid 2px rgba(67, 176, 42, 1.0)"
            });
            this.yesIcon.show();
            this.noIcon.hide();
        }
        else {
            $(this.elem).css({
                "background-color": "rgba(195, 92, 244, 0.1)",
                "border": "solid 2px rgba(195, 92, 244, 1.0)"
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
        this.answerString = answerString;
        this.matchRegexp = matchRegexp;
        if (this.cType == COMPONENT_TYPE.checkBoxes) {
            var aString = answerString.substr(1, answerString.length - 2);
            this.answerArray = aString.split(',');
        }
    }
    Answer.prototype.showIt = function () {
        switch (this.cType) {
            case COMPONENT_TYPE.textField:
            case COMPONENT_TYPE.textFieldWithVirtKeyboard: {
                $(this.c).find('input').val(this.answerString);
                break;
            }
            case COMPONENT_TYPE.comboBox1:
            case COMPONENT_TYPE.comboBox2: {
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
    Answer.prototype.checkIt = function (fromShowIt) {
        if (fromShowIt) {
            if (!this.hasAnswered) {
                this.hasAnswered = true;
                this.firstAnswer = "*Unanswered*";
                this.firstAnswerCorrect = false;
            }
            this.comp.setYesNo(true);
        }
        else {
            var userAnswer_1;
            var isCorrect_1;
            switch (this.cType) {
                case COMPONENT_TYPE.textField:
                case COMPONENT_TYPE.textFieldWithVirtKeyboard:
                    userAnswer_1 = $(this.c).val().trim()
                        .replace(/\u03ac/g, '\u1f71')
                        .replace(/\u03ad/g, '\u1f73')
                        .replace(/\u03ae/g, '\u1f75')
                        .replace(/\u03af/g, '\u1f77')
                        .replace(/\u03cc/g, '\u1f79')
                        .replace(/\u03cd/g, '\u1f7b')
                        .replace(/\u03ce/g, '\u1f7d')
                        .replace(/\u0390/g, '\u1fd3')
                        .replace(/\u03b0/g, '\u1fe3');
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
                case COMPONENT_TYPE.comboBox1:
                case COMPONENT_TYPE.comboBox2:
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
                    userAnswer_1 = '(' + userAnswer_1.substr(0, userAnswer_1.length - 1) + ')';
                    break;
            }
            if (userAnswer_1 && !this.hasAnswered) {
                this.hasAnswered = true;
                this.firstAnswer = userAnswer_1;
                this.firstAnswerCorrect = isCorrect_1;
            }
            if (this.hasAnswered)
                this.comp.setYesNo(isCorrect_1);
        }
    };
    Answer.prototype.commitIt = function () {
        this.checkIt(false);
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
var PanelQuestion = (function () {
    function PanelQuestion(qd, dict) {
        var _this = this;
        this.vAnswers = [];
        this.question_stat = new QuestionStatistics;
        this.subQuizIndex = 0;
        this.qd = qd;
        this.sentence = dict.sentenceSetQuiz;
        var smo = dict.getSingleMonadObject(getFirst(this.sentence));
        var location_realname = '';
        this.location = smo.bcv_loc;
        for (var unix in configuration.universeHierarchy) {
            var unixi = +unix;
            if (isNaN(unixi))
                continue;
            var uniname = configuration.universeHierarchy[unixi].type;
            switch (unixi) {
                case 0:
                    location_realname += smo.bcv[unixi] + ', ';
                    break;
                case 2:
                    location_realname += ', ';
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
        var dontShow = qd.quizFeatures.dontShow;
        var showFeatures = qd.quizFeatures.showFeatures;
        var requestFeatures = qd.quizFeatures.requestFeatures;
        var oType = qd.quizFeatures.objectType;
        var featuresHere = typeinfo.obj2feat[oType];
        var qoFeatures = this.buildQuizObjectFeatureList();
        var hasForeignInput = false;
        var firstInput = 'id="firstinput"';
        var quizItemID = 0;
        this.question_stat.text = dict.generateSentenceHtml(qd);
        this.question_stat.location = location_realname;
        var questionheaders = [];
        var headInd = 0;
        if (dontShow) {
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
        var quizCardNum = 0;
        var quizActive = true;
        var quizContainer = $('div#quizcontainer');
        for (var qoid in qoFeatures) {
            if (isNaN(+qoid))
                continue;
            ++quizCardNum;
            if (quizActive === true) {
                quizContainer.append("<div class=\"quizcard\" style=\"display:block;\"><table class=\"quiztab" + qoid + "\"></table></div>");
                quizActive = false;
            }
            else {
                quizContainer.append("<div class=\"quizcard\" style=\"display:none;\"><table class=\"quiztab" + qoid + "\"></table></div>");
            }
            var quizTab = $("table.quiztab" + qoid);
            var fvals = qoFeatures[+qoid];
            if (dontShow) {
                quizTab.append('<tr>' + questionheaders[(headInd % headLen + headLen) % headLen]
                    + '<td>' + (+qoid + 1)
                    + '</td></tr>');
                ++headInd;
                this.question_stat.show_feat.values.push("" + (+qoid + 1));
            }
            for (var sfi in showFeatures) {
                if (isNaN(+sfi))
                    continue;
                var sf = showFeatures[+sfi];
                var val = fvals[sf];
                var featType = featuresHere[sf];
                var featset = getFeatureSetting(oType, sf);
                this.question_stat.show_feat.values.push(val);
                if (featType == null && sf !== 'visual')
                    alert("Unexpected (1) featType==null in panelquestion.ts; sf=\"" + sf + "\"");
                if (sf === 'visual')
                    featType = 'string';
                if (featType == 'hint') {
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
                    if (featset.otherValues && featset.otherValues.indexOf(val) !== -1)
                        val = localize('other_value');
                    else
                        val = getFeatureValueFriendlyName(featType, val, false, true);
                }
                if (val == null)
                    alert('Unexpected val==null in panelquestion.ts');
                if (featType === 'string' || featType == 'ascii') {
                    quizTab.append('<tr>'
                        + questionheaders[(headInd % headLen + headLen) % headLen]
                        + ("<td class=\"" + PanelQuestion.charclass(featset) + "\">" + (val === '' ? '-' : val) + "</td></tr>"));
                    ++headInd;
                }
                else {
                    quizTab.append('<tr>'
                        + questionheaders[(headInd % headLen + headLen) % headLen]
                        + ("<td>" + val + "</td></tr>"));
                    ++headInd;
                }
            }
            var _loop_2 = function (rfi) {
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
                        v = $('<tr>'
                            + +questionheaders[(headInd % headLen + headLen) % headLen]
                            + ("<td class=\"" + PanelQuestion.charclass(featset) + "\">" + correctAnswer + "</td>")
                            + '</tr>');
                    else {
                        var quiz_div_1 = $('<div class="quizitem"></div>');
                        var optArray = [];
                        var cwyn = new ComponentWithYesNo(quiz_div_1, COMPONENT_TYPE.comboBox2);
                        cwyn.addChangeListener();
                        for (var valix in suggestions) {
                            if (isNaN(+valix))
                                continue;
                            var s = suggestions[+valix];
                            var item = new StringWithSort(s, s);
                            var option = $('<div class="selectbutton">'
                                + ("<input type =\"radio\" id=\"" + item.getInternal() + "_" + quizItemID + "\" name=\"quizitem_" + quizItemID + "\" value=\"" + item.getInternal() + "\">")
                                + ("<label for=\"" + item.getInternal() + "_" + quizItemID + "\">" + item.getString() + "</label>")
                                + '</div>');
                            option.data('sws', item);
                            optArray.push(option);
                            if (s === correctAnswer)
                                this_2.vAnswers.push(new Answer(cwyn, item, s, null));
                        }
                        optArray.sort(function (a, b) { return StringWithSort.compare(a.data('sws'), b.data('sws')); });
                        $.each(optArray, function (ix, o) { return quiz_div_1.append(o); });
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
                    if (featset.foreignText || featset.transliteratedText) {
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
                        if (shinDot === true) {
                            answerLetters_1.push('ש' + '\u05C1');
                        }
                        if (sinDot === true) {
                            answerLetters_1.push('ש' + '\u05C2');
                        }
                        for (var index = 0; index < answerLetters_1.length; index++) {
                            var l = answerLetters_1[index];
                            switch (l) {
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
                        showLetters_1.sort();
                        var vf_1 = $("<div class=\"inputquizitem\"><input " + firstInput + " data-kbid=\"" + PanelQuestion.kbid++ + "\" type=\"text\""
                            + (" class=\"" + PanelQuestion.charclass(featset) + "\"></div>"));
                        vf_1.append("<div class=\"letterinput\"><div class=\"selectbutton delbutton\" id=\"delinputchar\"><label for=\"delinputchar\">del</label></div></div>");
                        showLetters_1.forEach(function (letter) {
                            vf_1.find('.letterinput').append("<div class=\"selectbutton inputbutton\" id=\"inputchar\"><label for=\"inputchar\" class=\"" + PanelQuestion.charclass(featset) + "\">" + letter + "</label></div>");
                        });
                        firstInput = '';
                        hasForeignInput = true;
                        cwyn = new ComponentWithYesNo(vf_1, COMPONENT_TYPE.textField);
                        cwyn.addKeypressListener();
                        v = cwyn.getJQuery();
                    }
                    else {
                        var vf = $('<div class="inputquizitem"><input type="text"></div>');
                        cwyn = new ComponentWithYesNo(vf, COMPONENT_TYPE.textField);
                        cwyn.addKeypressListener();
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
                    var selections = $('<table class="list-of"></table>');
                    var numberOfItems = swsValues.length;
                    var numberOfRows = Math.floor((numberOfItems + 2) / 3);
                    for (var r = 0; r < numberOfRows; ++r) {
                        var row = $('<tr></tr>');
                        for (var c = 0; c < 3; c++) {
                            var ix = r + c * numberOfRows;
                            if (ix < numberOfItems)
                                row.append(questionheaders[(headInd % headLen + headLen) % headLen]
                                    + '<td style="text-align:left">'
                                    + ("<input type=\"checkbox\" value=\"" + swsValues[ix].getInternal() + "\">")
                                    + swsValues[ix].getString()
                                    + '</td>');
                            else
                                row.append(questionheaders[(headInd % headLen + headLen) % headLen]
                                    + '<td></td>');
                        }
                        selections.append(row);
                    }
                    var cwyn = new ComponentWithYesNo(selections, COMPONENT_TYPE.checkBoxes);
                    cwyn.addChangeListener();
                    v = cwyn.getJQuery();
                    this_2.vAnswers.push(new Answer(cwyn, null, correctAnswer, null));
                }
                else {
                    var values = typeinfo.enum2values[featType];
                    if (values == null) {
                        v.append('<tr>'
                            + questionheaders[(headInd % headLen + headLen) % headLen]
                            + '<td>QuestionPanel.UnknType</td></tr>');
                    }
                    else {
                        var quiz_div_2 = $('<div class="quizitem"></div>');
                        var optArray = [];
                        var cwyn = new ComponentWithYesNo(quiz_div_2, COMPONENT_TYPE.comboBox1);
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
                                        + ("<input type =\"radio\" id=\"" + item.getInternal() + "_" + quizItemID + "\" name=\"quizitem_" + quizItemID + "\" value=\"" + item.getInternal() + "\">")
                                        + ("<label for=\"" + item.getInternal() + "_" + quizItemID + "\">" + item.getString() + "</label>")
                                        + '</div>');
                                    option.data('sws', item);
                                    optArray.push(option);
                                    if (correctIsOther)
                                        this_2.vAnswers.push(new Answer(cwyn, item, localize('other_value'), null));
                                }
                            }
                            else {
                                var sFriendly = getFeatureValueFriendlyName(featType, s, false, false);
                                var item = new StringWithSort(sFriendly, s);
                                var option = $('<div class="selectbutton">'
                                    + ("<input type =\"radio\" id=\"" + item.getInternal() + "_" + quizItemID + "\" name=\"quizitem_" + quizItemID + "\" value=\"" + item.getInternal() + "\">")
                                    + ("<label for=\"" + item.getInternal() + "_" + quizItemID + "\">" + item.getString() + "</label>")
                                    + '</div>');
                                option.data('sws', item);
                                optArray.push(option);
                                if (sFriendly === correctAnswerFriendly)
                                    this_2.vAnswers.push(new Answer(cwyn, item, s, null));
                            }
                        }
                        optArray.sort(function (a, b) { return StringWithSort.compare(a.data('sws'), b.data('sws')); });
                        $.each(optArray, function (ix, o) { return quiz_div_2.append(o); });
                        v = cwyn.getJQuery();
                    }
                }
                var quizRow = $('<tr></tr>');
                quizRow.append(questionheaders[(headInd % headLen + headLen) % headLen]);
                quizRow.append(v);
                quizTab.append(quizRow);
                ++headInd;
            };
            var this_2 = this;
            for (var rfi in requestFeatures) {
                _loop_2(rfi);
            }
        }
        var quizCard = $('.quizcard');
        quizCard.append('<div class="buttonlist1">'
            + "<button class=\"btn btn-quiz\" id=\"check_answer\" type=\"button\">Check answer</button>"
            + "<button class=\"btn btn-quiz\" id=\"show_answer\" type=\"button\">Show answer</button>"
            + '</div>');
        if (quizCardNum > 1) {
            quizContainer.prepend('<div class="prev-next-btn prev" id="prevsubquiz" style="visibility:hidden;">&#10094;</div>');
            quizContainer.append('<div class="prev-next-btn next" id="nextsubquiz">&#10095;</div>');
        }
        '<div class="selectbutton inputbutton" id="inputchar"><label for="inputchar">${letter}</label></div>';
        $('div#inputchar').click(function () {
            var letter = String($(this).find('label').text());
            $(this)
                .parent().siblings('input')
                .val($(this).parent().siblings('input').val() + letter);
            return false;
        });
        $('div#delinputchar').click(function () {
            var value = String($(this).parent().siblings('input').val());
            $(this)
                .parent().siblings('input')
                .val(value.slice(0, -1));
            return false;
        });
        $('#prevsubquiz').off('click');
        $('#prevsubquiz').on('click', function () {
            _this.prevNextSubQuestion(-1);
        });
        $('#nextsubquiz').off('click');
        $('#nextsubquiz').on('click', function () {
            _this.prevNextSubQuestion(1);
        });
        $('button#check_answer').off('click');
        $('button#check_answer').on('click', function () {
            for (var ai in _this.vAnswers) {
                if (isNaN(+ai))
                    continue;
                var a = _this.vAnswers[+ai];
                a.checkIt(false);
            }
            $('html, body').animate({
                scrollTop: $('#myview').offset().top - 5
            }, 50);
        });
        $('button#show_answer').off('click');
        $('button#show_answer').on('click', function () {
            for (var ai in _this.vAnswers) {
                if (isNaN(+ai))
                    continue;
                var a = _this.vAnswers[+ai];
                a.showIt();
                a.checkIt(true);
            }
            $('html, body').animate({
                scrollTop: $('#myview').offset().top - 5
            }, 50);
        });
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
        this.subQuizIndex += n;
        var i;
        var slides = $('#quizcontainer').find('.quizcard');
        if (this.subQuizIndex < 1) {
            $('#prevsubquiz').css({ "visibility": "hidden" });
        }
        ;
        if (this.subQuizIndex > 0) {
            $('#prevsubquiz').css({ "visibility": "visible" });
        }
        if (this.subQuizIndex < slides.length - 1) {
            $('#nextsubquiz').css({ "visibility": "visible" });
        }
        ;
        if (this.subQuizIndex === slides.length - 1) {
            $('#nextsubquiz').css({ "visibility": "hidden" });
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
        $('html, body').animate({
            scrollTop: $('#myview').offset().top - 5
        }, 50);
    };
    PanelQuestion.prototype.delLastCharInput = function (fieldJQuery) {
        var $value = fieldJQuery.find('input');
        $value.val('test');
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
    }
    return QuizStatistics;
}());
var Quiz = (function () {
    function Quiz(qid) {
        var _this = this;
        this.currentDictIx = -1;
        this.currentPanelQuestion = null;
        this.quiz_statistics = new QuizStatistics(qid);
        $('button#next_question').click(function () { return _this.nextQuestion(); });
        $('button#finish').click(function () { return _this.finishQuiz(true); });
        $('button#finishNoStats').click(function () { return _this.finishQuiz(false); });
    }
    Quiz.prototype.nextQuestion = function () {
        var _this = this;
        var timeBeforeHbOpen = 600000;
        var timeBeforeHbClose = 28000;
        var monitorUser = function () {
            window.clearTimeout(_this.tHbOpen);
            window.clearTimeout(_this.tHbClose);
            _this.tHbOpen = window.setTimeout(function () {
                heartbeatDialog.dialog('open');
                _this.tHbClose = window.setTimeout(function () {
                    heartbeatDialog.dialog('close');
                    $('#next_question').fadeOut();
                }, timeBeforeHbClose);
            }, timeBeforeHbOpen);
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
                            heartbeatDialog.dialog('close');
                            window.setTimeout(monitorUser, 0);
                        }
                    }]
            });
        };
        monitorUser();
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
            this.currentPanelQuestion = new PanelQuestion(quizdata, currentDict);
            if (this.currentDictIx + 1 === dictionaries.sentenceSets.length) {
                $('button#next_question').attr('disabled', 'disabled');
                $('button#finish').removeAttr('disabled');
                $('button#finishNoStats').removeAttr('disabled');
            }
            $('html, body').animate({
                scrollTop: $('#myview').offset().top - 5
            }, 50);
        }
        else
            alert('No more questions');
        util.FollowerBox.resetCheckboxCounters();
        $('.selectbutton input:enabled:checked').trigger('change');
    };
    Quiz.prototype.finishQuiz = function (gradingFlag) {
        if (quizdata.quizid == -1)
            window.location.replace(site_url + 'text/select_quiz');
        else {
            if (this.currentPanelQuestion === null)
                alert('System error: No current question panel');
            else
                this.quiz_statistics.questions.push(this.currentPanelQuestion.updateQuestionStat());
            this.quiz_statistics.grading = gradingFlag;
            $('.grammarselector').empty();
            $('#textcontainer').html('<p>' + localize('sending_statistics') + '</p>');
            $.post(site_url + 'statistics/update_stat', this.quiz_statistics)
                .done(function () { return window.location.replace(site_url + 'text/select_quiz'); })
                .fail(function (jqXHR, textStatus, errorThrow) {
                $('#textcontainer')
                    .removeClass('textcontainer-background')
                    .addClass('alert alert-danger')
                    .html("<h1>" + localize('error_response') + "</h1><p>" + errorThrow + "</p>");
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
        if (supportsProgress)
            $('div#progressbar').hide();
        else
            $('progress#progress').hide();
        quiz = new Quiz(quizdata.quizid);
        quiz.nextQuestion();
    }
    else {
        $('#cleargrammar').on('click', function () { GrammarSelectionBox.clearBoxes(true); });
        var currentDict = new Dictionary(dictionaries, 0, null);
        currentDict.generateSentenceHtml(null);
        $('.grammarselector input:enabled:checked').trigger('change');
    }
});
