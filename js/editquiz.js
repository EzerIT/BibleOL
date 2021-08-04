var __extends = (this && this.__extends) || (function () {
    var extendStatics = function (d, b) {
        extendStatics = Object.setPrototypeOf ||
            ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
            function (d, b) { for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p]; };
        return extendStatics(d, b);
    };
    return function (d, b) {
        extendStatics(d, b);
        function __() { this.constructor = d; }
        d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
    };
})();
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
var FeatureHandler = (function () {
    function FeatureHandler(type, name) {
        this.type = type;
        this.name = name;
        this.comparator = 'equals';
    }
    FeatureHandler.prototype.normalize = function () {
    };
    FeatureHandler.prototype.getComparator = function () {
        switch (this.comparator) {
            case 'equals': return '=';
            case 'differs': return '<>';
            case 'matches': return '~';
        }
        return '';
    };
    FeatureHandler.prototype.getJoiner = function () {
        switch (this.comparator) {
            case 'equals': return ' OR ';
            case 'differs': return ' AND ';
            case 'matches': return ' OR ';
        }
        return '';
    };
    return FeatureHandler;
}());
var StringFeatureHandler = (function (_super) {
    __extends(StringFeatureHandler, _super);
    function StringFeatureHandler(name) {
        var _this = _super.call(this, 'stringfeature', name) || this;
        _this.values = [];
        _this.normalize();
        return _this;
    }
    StringFeatureHandler.prototype.normalize = function () {
        if (this.values.length < 1)
            this.values.push(null);
    };
    StringFeatureHandler.prototype.setValue = function (index, val) {
        this.values[index] = val;
    };
    StringFeatureHandler.prototype.removeValue = function (index) {
        this.values[index] = null;
    };
    StringFeatureHandler.prototype.hasValues = function () {
        for (var i = 0; i < this.values.length; ++i)
            if (this.values[i] !== null)
                return true;
        return false;
    };
    StringFeatureHandler.prototype.toMql = function () {
        var comparator = this.getComparator();
        var values = [];
        for (var i = 0; i < this.values.length; ++i)
            if (this.values[i] !== null)
                values.push(this.name + comparator + '"' + this.values[i] + '"');
        if (values.length === 1)
            return values[0];
        return '(' + values.join(this.getJoiner()) + ')';
    };
    return StringFeatureHandler;
}(FeatureHandler));
var IntegerFeatureHandler = (function (_super) {
    __extends(IntegerFeatureHandler, _super);
    function IntegerFeatureHandler(name) {
        var _this = _super.call(this, 'integerfeature', name) || this;
        _this.values = [];
        _this.normalize();
        return _this;
    }
    IntegerFeatureHandler.prototype.normalize = function () {
        while (this.values.length < 4)
            this.values.push(null);
    };
    IntegerFeatureHandler.prototype.setValue = function (index, val) {
        this.values[index] = val;
    };
    IntegerFeatureHandler.prototype.removeValue = function (index) {
        this.values[index] = null;
    };
    IntegerFeatureHandler.prototype.hasValues = function () {
        for (var i = 0; i < this.values.length; ++i)
            if (this.values[i] !== null)
                return true;
        return false;
    };
    IntegerFeatureHandler.prototype.toMql = function () {
        var values = [];
        for (var i = 0; i < this.values.length; ++i)
            if (this.values[i] !== null)
                values.push(this.values[i]);
        if (values.length === 1)
            return this.name + this.getComparator() + values[0];
        else
            return (this.comparator === 'differs' ? 'NOT ' : '')
                + this.name + ' IN (' + values.join(',') + ')';
    };
    return IntegerFeatureHandler;
}(FeatureHandler));
var RangeIntegerFeatureHandler = (function (_super) {
    __extends(RangeIntegerFeatureHandler, _super);
    function RangeIntegerFeatureHandler(name) {
        return _super.call(this, 'rangeintegerfeature', name) || this;
    }
    RangeIntegerFeatureHandler.prototype.set_low_high = function (index, val) {
        switch (index) {
            case 'value_low':
                this.value_low = val;
                break;
            case 'value_high':
                this.value_high = val;
                break;
            default:
                throw 'Illegal index in access_low_high';
        }
    };
    RangeIntegerFeatureHandler.prototype.isSetLow = function () {
        return this.value_low !== null && this.value_low !== undefined;
    };
    RangeIntegerFeatureHandler.prototype.isSetHigh = function () {
        return this.value_high !== null && this.value_high !== undefined;
    };
    RangeIntegerFeatureHandler.prototype.hasValues = function () {
        return this.isSetLow() || this.isSetHigh();
    };
    RangeIntegerFeatureHandler.prototype.toMql = function () {
        if (this.isSetLow()) {
            if (this.isSetHigh())
                return '(' + this.name + '>=' + this.value_low + ' AND ' + this.name + '<=' + this.value_high + ')';
            else
                return this.name + '>=' + this.value_low;
        }
        else {
            if (this.isSetHigh())
                return this.name + '<=' + this.value_high;
            else
                return '';
        }
    };
    return RangeIntegerFeatureHandler;
}(FeatureHandler));
var EnumFeatureHandler = (function (_super) {
    __extends(EnumFeatureHandler, _super);
    function EnumFeatureHandler(name) {
        var _this = _super.call(this, 'enumfeature', name) || this;
        _this.values = [];
        return _this;
    }
    EnumFeatureHandler.prototype.addValue = function (val) {
        this.values.push(val);
    };
    EnumFeatureHandler.prototype.removeValue = function (val) {
        var index = this.values.indexOf(val);
        if (index > -1)
            this.values.splice(index, 1);
    };
    EnumFeatureHandler.prototype.hasValues = function () {
        return this.values.length > 0;
    };
    EnumFeatureHandler.prototype.toMql = function () {
        return (this.comparator === 'differs' ? 'NOT ' : '')
            + this.name + ' IN (' + this.values.join(',') + ')';
    };
    return EnumFeatureHandler;
}(FeatureHandler));
var EnumListFeatureHandler = (function (_super) {
    __extends(EnumListFeatureHandler, _super);
    function EnumListFeatureHandler(name) {
        var _this = _super.call(this, 'enumlistfeature', name) || this;
        _this.listvalues = [];
        _this.normalize();
        return _this;
    }
    EnumListFeatureHandler.prototype.normalize = function () {
        while (this.listvalues.length < 4)
            this.listvalues.push(new ListValuesHandler());
    };
    EnumListFeatureHandler.prototype.hasValues = function () {
        for (var i = 0; i < this.listvalues.length; ++i)
            if (this.listvalues[i].hasValues())
                return true;
        return false;
    };
    EnumListFeatureHandler.prototype.toMql = function () {
        if (this.listvalues.length > 0) {
            var sb = '(';
            var first = true;
            for (var i = 0; i < this.listvalues.length; ++i) {
                var lvh = this.listvalues[i];
                if (lvh.hasValues()) {
                    if (first)
                        first = false;
                    else
                        sb += ' OR ';
                    sb += lvh.toMql(this.name);
                }
            }
            return sb + ')';
        }
        else
            return '';
    };
    return EnumListFeatureHandler;
}(FeatureHandler));
var QereFeatureHandler = (function (_super) {
    __extends(QereFeatureHandler, _super);
    function QereFeatureHandler(name) {
        var _this = _super.call(this, 'qerefeature', name) || this;
        _this.omit = false;
        return _this;
    }
    QereFeatureHandler.prototype.setValue = function (val) {
        this.omit = val;
    };
    QereFeatureHandler.prototype.hasValues = function () {
        return this.omit;
    };
    QereFeatureHandler.prototype.toMql = function () {
        if (this.omit)
            return "(" + this.name + "='' AND g_word_translit<>'HÎʔ')";
        else
            return '';
    };
    return QereFeatureHandler;
}(FeatureHandler));
var ListValuesHandler = (function () {
    function ListValuesHandler() {
        this.type = 'listvalues';
        this.yes_values = [];
        this.no_values = [];
    }
    ListValuesHandler.prototype.modifyValue = function (name, val) {
        var yes_index = this.yes_values.indexOf(name);
        var no_index = this.no_values.indexOf(name);
        switch (val) {
            case 'yes':
                if (yes_index == -1)
                    this.yes_values.push(name);
                if (no_index > -1)
                    this.no_values.splice(no_index, 1);
                break;
            case 'no':
                if (no_index == -1)
                    this.no_values.push(name);
                if (yes_index > -1)
                    this.yes_values.splice(yes_index, 1);
                break;
            case 'dontcare':
                if (no_index > -1)
                    this.no_values.splice(no_index, 1);
                if (yes_index > -1)
                    this.yes_values.splice(yes_index, 1);
                break;
        }
    };
    ListValuesHandler.prototype.hasValues = function () {
        return this.yes_values.length + this.no_values.length > 0;
    };
    ListValuesHandler.prototype.toMql = function (featName) {
        var stringValues = [];
        for (var ix = 0; ix < this.yes_values.length; ++ix)
            stringValues.push(featName + " HAS " + this.yes_values[+ix]);
        for (var ix = 0; ix < this.no_values.length; ++ix)
            stringValues.push("NOT " + featName + " HAS " + this.no_values[+ix]);
        if (stringValues.length === 1)
            return stringValues[0];
        return '(' + stringValues.join(' AND ') + ')';
    };
    return ListValuesHandler;
}());
var PanelTemplMql = (function () {
    function PanelTemplMql(initialMd, name_prefix) {
        var _this = this;
        this.initialMd = initialMd;
        this.name_prefix = name_prefix;
        this.fname2fh = {};
        if (initialMd.featHand) {
            for (var i = 0; i < initialMd.featHand.vhand.length; ++i) {
                var vh = initialMd.featHand.vhand[i];
                switch (vh.type) {
                    case 'enumfeature':
                        addMethods(vh, EnumFeatureHandler, null);
                        break;
                    case 'enumlistfeature':
                        addMethods(vh, EnumListFeatureHandler, null);
                        var elfh = vh;
                        for (var j = 0; j < elfh.listvalues.length; ++j)
                            addMethods(elfh.listvalues[j], ListValuesHandler, null);
                        break;
                    case 'stringfeature':
                        addMethods(vh, StringFeatureHandler, null);
                        break;
                    case 'integerfeature':
                        addMethods(vh, IntegerFeatureHandler, null);
                        break;
                    case 'rangeintegerfeature':
                        addMethods(vh, RangeIntegerFeatureHandler, null);
                        break;
                    case 'qerefeature':
                        addMethods(vh, QereFeatureHandler, null);
                        break;
                }
                vh.normalize();
                this.fname2fh[vh.name] = vh;
            }
        }
        this.fpan = $("<div id=\"" + this.name_prefix + "_fpan\"></div>");
        this.rbMql = $("<input type=\"radio\" name=\"" + this.name_prefix + "_usemql\" value=\"yes\">");
        this.rbMql.click(function () {
            if (_this.rbMql.is(':checked'))
                _this.switchToMql(true);
        });
        this.rbFriendly = $("<input type=\"radio\" name=\"" + this.name_prefix + "_usemql\" value=\"no\">");
        this.rbFriendly.click(function () {
            if (_this.rbFriendly.is(':checked')) {
                _this.switchToMql(false);
                _this.updateMql();
            }
        });
        this.mqlText = $("<textarea id=\"" + this.name_prefix + "_mqltext\" cols=\"45\" rows=\"2\">");
        this.featureCombo = $('<select></select>');
        this.featureCombo.on('change', function () {
            _this.currentBox.hide();
            _this.currentBox = _this.groups[_this.featureCombo.val()];
            _this.currentBox.show();
        });
        this.objectTypeCombo = $('<select></select>');
        var selObject = (initialMd != null && initialMd.object != null) ? initialMd.object : configuration.objHasSurface;
        for (var s in configuration.objectSettings) {
            if (configuration.objectSettings[s].mayselect) {
                this.objectTypeCombo.append("<option value=\"" + s + "\""
                    + (s === selObject ? ' selected="selected"' : '')
                    + (s === configuration.objHasSurface ? ' data-reset="yes"' : '')
                    + (">" + getObjectFriendlyName(s) + "</option>"));
            }
        }
        this.objectTypeCombo.on('change', function () {
            $('#virtualkbid').appendTo('#virtualkbcontainer');
            _this.fpan.html("<div id=\"" + _this.name_prefix + "_fpan\"></div>");
            _this.currentBox = null;
            _this.featureCombo.html('<select></select>');
            _this.objectSelectionUpdated(_this.objectTypeCombo.val(), null);
            _this.updateMql();
        });
        this.clear = $('<button id="clear_button" type="button">' + localize('clear_button') + '</button>');
        this.clear.click(function () {
            $('#virtualkbid').appendTo('#virtualkbcontainer');
            _this.rbFriendly.prop('checked', true);
            _this.objectTypeCombo.find(':selected').prop('selected', false);
            _this.objectTypeCombo.find('[data-reset]').prop('selected', true);
            _this.fpan.html("<div id=\"" + _this.name_prefix + "_fpan\"></div>");
            _this.currentBox = null;
            _this.featureCombo.html('<select></select>');
            _this.objectSelectionUpdated(configuration.objHasSurface, null);
            _this.updateMql();
            _this.switchToMql(false);
        });
    }
    PanelTemplMql.prototype.finish_construct = function () {
        if (this.initialMd == null) {
            this.rbFriendly.prop('checked', true);
            this.objectSelectionUpdated(configuration.objHasSurface, null);
            this.updateMql();
            this.switchToMql(false);
        }
        else if (this.initialMd.mql != null) {
            this.rbMql.prop('checked', true);
            this.mqlText.html(this.initialMd.mql);
            if (this.initialMd.featHand)
                this.objectSelectionUpdated(this.initialMd.object, this.fname2fh);
            else
                this.objectSelectionUpdated(this.initialMd.object, null);
            this.switchToMql(true);
        }
        else {
            this.rbFriendly.prop('checked', true);
            this.objectSelectionUpdated(this.initialMd.object, this.fname2fh);
            this.updateMql();
            this.switchToMql(false);
        }
        this.txtEntry = this.getMql();
    };
    PanelTemplMql.prototype.setMql = function (s) {
        this.mqlText.val(s);
    };
    PanelTemplMql.prototype.getMql = function () {
        return this.mqlText.val();
    };
    PanelTemplMql.prototype.monitorChange = function (elem, sfh, i) {
        var _this = this;
        clearInterval(this.intervalHandler);
        if (this.lastMonitored !== elem.attr('id')) {
            this.monitorOrigVal = elem.val();
            this.lastMonitored = elem.attr('id');
        }
        this.intervalHandler = setInterval(function () {
            var s = elem.val();
            if (s !== _this.monitorOrigVal) {
                _this.monitorOrigVal = s;
                if (s.length === 0)
                    sfh.removeValue(i);
                else
                    sfh.setValue(i, s);
                _this.updateMql();
            }
        }, 500);
    };
    PanelTemplMql.prototype.stringTextModifiedListener = function (e) {
        var s = $(e.currentTarget).val();
        var sfh = e.data.sfh;
        if (s.length === 0)
            sfh.removeValue(e.data.i);
        else
            sfh.setValue(e.data.i, s);
        this.updateMql();
    };
    PanelTemplMql.prototype.integerTextModifiedListener = function (e) {
        var s = $(e.currentTarget).val();
        var ifh = e.data.ifh;
        $('#' + e.data.err_id).html('');
        if (s.length === 0)
            ifh.removeValue(e.data.i);
        else {
            if (s.match(/\D/g) !== null)
                $('#' + e.data.err_id).html(localize('not_integer'));
            else
                ifh.setValue(e.data.i, +s);
        }
        this.updateMql();
    };
    PanelTemplMql.prototype.rangeIntegerTextModifiedListener = function (e) {
        var s = $(e.currentTarget).val();
        var rfh = e.data.rfh;
        $('#' + e.data.err_id).html('');
        if (s.length === 0)
            rfh.set_low_high(e.data.i, null);
        else {
            if (s.match(/\D/g) !== null)
                $('#' + e.data.err_id).html(localize('not_integer'));
            else
                rfh.set_low_high(e.data.i, +s);
        }
        this.updateMql();
    };
    PanelTemplMql.prototype.objectSelectionUpdated = function (otype, fhs) {
        this.handlers = [];
        this.groups = {};
        for (var key in getObjectSetting(otype).featuresetting) {
            var valueType = typeinfo.obj2feat[otype][key];
            var featset = getFeatureSetting(otype, key);
            if (featset.ignoreSelect)
                continue;
            var group = $('<div></div>');
            this.groups[key] = group;
            var selectString = void 0;
            if (featset.isDefault) {
                group.show();
                this.currentBox = group;
                selectString = 'selected="selected"';
            }
            else {
                group.hide();
                selectString = '';
            }
            this.featureCombo.append("<option value=\"" + key + "\" " + selectString + ">" + getFeatureFriendlyName(otype, key) + "</option>");
            switch (valueType) {
                case 'integer':
                    if (featset.isRange)
                        this.generateIntegerRangePanel(key, fhs);
                    else
                        this.generateIntegerPanel(key, fhs);
                    break;
                case 'ascii':
                case 'string':
                    if (key === Qere.feature())
                        this.generateQerePanel(key, fhs);
                    else
                        this.generateStringPanel(key, fhs, featset.foreignText);
                    break;
                default:
                    if (valueType.substr(0, 8) === 'list of ')
                        this.generateListOfPanel(key, fhs, valueType.substr(8), otype);
                    else
                        this.generateEnumPanel(key, fhs, featset, valueType);
                    break;
            }
            this.fpan.append(group);
        }
        this.populateFeatureTab(otype);
    };
    PanelTemplMql.prototype.generateIntegerRangePanel = function (key, fhs) {
        var rfh = (fhs && fhs[key]) ? fhs[key] : new RangeIntegerFeatureHandler(key);
        var group2 = $('<table></table>');
        var rowLow = $('<tr></tr>');
        var rowHigh = $('<tr></tr>');
        var cellLab;
        var cellInput;
        var cellErr;
        var jtf;
        jtf = $('<input type="text" size="8">');
        if (rfh.isSetLow())
            jtf.val(String(rfh.value_low));
        var err_id = "err_" + key + "_low";
        jtf.on('keyup', null, { rfh: rfh, i: 'value_low', err_id: err_id }, $.proxy(this.rangeIntegerTextModifiedListener, this));
        cellLab = $('<td>' + localize('low_value_prompt') + '</td>');
        cellInput = $('<td></td>');
        cellInput.append(jtf);
        cellErr = $("<td id=\"" + err_id + "\"></td>");
        rowLow.append(cellLab, cellInput, cellErr);
        jtf = $('<input type="text" size="8">');
        if (rfh.isSetHigh())
            jtf.val(String(rfh.value_high));
        err_id = "err_" + key + "_high";
        jtf.on('keyup', null, { rfh: rfh, i: 'value_high', err_id: err_id }, $.proxy(this.rangeIntegerTextModifiedListener, this));
        cellLab = $('<td>' + localize('high_value_prompt') + '</td>');
        cellInput = $('<td></td>');
        cellInput.append(jtf);
        cellErr = $("<td id=\"" + err_id + "\"></td>");
        rowHigh.append(cellLab, cellInput, cellErr);
        group2.append(rowLow, rowHigh);
        this.groups[key].append(group2);
        this.handlers.push(rfh);
    };
    PanelTemplMql.prototype.generateIntegerPanel = function (key, fhs) {
        var _this = this;
        var ifh = (fhs && fhs[key]) ? fhs[key] : new IntegerFeatureHandler(key);
        var butEquals = $("<input type=\"radio\" name=\"" + this.name_prefix + "_" + key + "_comp\" value=\"equals\">");
        var butDiffers = $("<input type=\"radio\" name=\"" + this.name_prefix + "_" + key + "_comp\" value=\"differs\">");
        switch (ifh.comparator) {
            case 'equals':
                butEquals.prop('checked', true);
                break;
            case 'differs':
                butDiffers.prop('checked', true);
                break;
        }
        var sel = $('<span></span>');
        sel.append(butEquals, "=", butDiffers, "&#x2260;");
        this.groups[key].append(sel);
        sel.on('click', ifh, function (e) {
            var v = $(e.target).val();
            switch (v) {
                case 'equals':
                case 'differs':
                    e.data.comparator = v;
                    _this.updateMql();
                    break;
            }
        });
        var group2 = $('<table></table>');
        for (var i = 0; i < ifh.values.length; ++i) {
            var jtf = $('<input type="text" size="8">');
            if (ifh.values[i])
                jtf.val(String(ifh.values[i]));
            var err_id = "err_" + key + "_" + i;
            jtf.on('keyup', null, { ifh: ifh, i: i, err_id: err_id }, $.proxy(this.integerTextModifiedListener, this));
            var row = $('<tr></tr>');
            var cell = $('<td></td>');
            cell.append(jtf);
            row.append(cell);
            row.append("<td id=\"" + err_id + "\"></td>");
            group2.append(row);
        }
        this.groups[key].append(group2);
        this.handlers.push(ifh);
    };
    PanelTemplMql.prototype.addOneStringValue = function (key, group2, sfh, isForeign, i) {
        var _this = this;
        if (i == -1) {
            i = sfh.values.length;
            sfh.values.push(null);
        }
        var jtf = isForeign
            ? $("<input class=\"" + charset.foreignClass + "\" type=\"text\" size=\"20\" id=\"" + this.name_prefix + "_" + key + "_input" + (+i + 1) + "\">")
            : $('<input type="text" size="20">');
        if (sfh.values[i])
            jtf.val(sfh.values[i]);
        var kbdRowId;
        if (isForeign) {
            kbdRowId = this.name_prefix + "_" + key + "_row" + (+i + 1);
            jtf.on('focus', null, { kbdRowId: kbdRowId, sfh: sfh, i: i }, function (e) {
                $('#virtualkbid').appendTo('#' + e.data.kbdRowId);
                VirtualKeyboard.attachInput(e.currentTarget);
                _this.monitorChange($(e.currentTarget), e.data.sfh, e.data.i);
            });
        }
        jtf.on('keyup', null, { sfh: sfh, i: i }, $.proxy(this.stringTextModifiedListener, this));
        var row = $('<tr></tr>');
        var cell = $('<td></td>');
        cell.append(jtf);
        row.append(cell);
        group2.append(row);
        if (isForeign)
            group2.append("<tr><td id=\"" + kbdRowId + "\" style=\"text-align:right;\"></td></tr>");
    };
    PanelTemplMql.prototype.generateStringPanel = function (key, fhs, isForeign) {
        var _this = this;
        var sfh = (fhs && fhs[key]) ? fhs[key] : new StringFeatureHandler(key);
        var butEquals = $("<input type=\"radio\" name=\"" + this.name_prefix + "_" + key + "_comp\" value=\"equals\">");
        var butDiffers = $("<input type=\"radio\" name=\"" + this.name_prefix + "_" + key + "_comp\" value=\"differs\">");
        var butMatches = $("<input type=\"radio\" name=\"" + this.name_prefix + "_" + key + "_comp\" value=\"matches\">");
        switch (sfh.comparator) {
            case 'equals':
                butEquals.prop('checked', true);
                break;
            case 'differs':
                butDiffers.prop('checked', true);
                break;
            case 'matches':
                butMatches.prop('checked', true);
                break;
        }
        var sel = $('<span></span>');
        sel.append(butEquals, '=', butDiffers, '&#x2260;', butMatches, '~');
        this.groups[key].append(sel);
        sel.on('click', sfh, function (e) {
            var v = $(e.target).val();
            switch (v) {
                case 'equals':
                case 'differs':
                case 'matches':
                    e.data.comparator = v;
                    _this.updateMql();
                    break;
            }
        });
        var group2 = $('<table></table>');
        for (var i = 0; i < sfh.values.length; ++i) {
            this.addOneStringValue(key, group2, sfh, isForeign, i);
        }
        this.groups[key].append(group2);
        this.handlers.push(sfh);
        var addEntry = $('<button type="button">' + localize('add_entry_button') + '</button>');
        this.groups[key].append(addEntry);
        addEntry.click(function () {
            _this.addOneStringValue(key, group2, sfh, isForeign, -1);
        });
    };
    PanelTemplMql.prototype.generateQerePanel = function (key, fhs) {
        var _this = this;
        var qfh = (fhs && fhs[key]) ? fhs[key] : new QereFeatureHandler(key);
        var butOmitqere = $("<input type=\"checkbox\" name=\"" + this.name_prefix + "_" + key + "_sel\" value=\"omit\">");
        if (qfh.omit)
            butOmitqere.prop('checked', true);
        var sel = $('<span></span>');
        sel.append(butOmitqere, localize('omit_qere'));
        this.groups[key].append(sel);
        sel.on('click', qfh, function (e) {
            var target = $(e.target);
            e.data.setValue(target.prop('checked'));
            _this.updateMql();
        });
        this.handlers.push(qfh);
    };
    PanelTemplMql.prototype.generateListOfPanel = function (key, fhs, stripped_valueType, otype) {
        var _this = this;
        var enumValues = typeinfo.enum2values[stripped_valueType];
        if (!enumValues) {
            console.log('Unknown valueType', "list of " + stripped_valueType);
        }
        var elfh = (fhs && fhs[key]) ? fhs[key] : new EnumListFeatureHandler(key);
        var group_tabs = $("<div id=\"list_tabs_" + key + "\"></div>");
        var group_ul = $('<ul></ul>');
        group_tabs.append(group_ul);
        var tab_labels = [localize('1st_choice'),
            localize('2nd_choice'),
            localize('3rd_choice'),
            localize('4th_choice')];
        for (var tabno = 0; tabno < 4; ++tabno) {
            group_ul.append("<li><a href=\"#tab_" + key + "_" + tabno + "\">" + tab_labels[tabno] + "</a></li>");
            var lv = elfh.listvalues[tabno];
            var tab_contents = $("<div id=\"tab_" + key + "_" + tabno + "\"></div>");
            var vc_choice = new PanelForOneVcChoice(enumValues, stripped_valueType, this.name_prefix + "_" + otype + "_" + key + "_" + tabno, lv);
            tab_contents.append(vc_choice.getPanel());
            group_tabs.append(tab_contents);
            tab_contents.on('click', lv, function (e) {
                var target = $(e.target);
                if (target.attr('type') === 'radio') {
                    e.data.modifyValue(target.attr('data-name'), target.attr('value'));
                    _this.updateMql();
                }
            });
        }
        this.groups[key].append(group_tabs).tabs();
        this.handlers.push(elfh);
    };
    PanelTemplMql.prototype.generateEnumPanel = function (key, fhs, featset, valueType) {
        var _this = this;
        var enumValues = typeinfo.enum2values[valueType];
        if (!enumValues) {
            console.log('Unknown valueType', valueType);
            return;
        }
        var efh = (fhs && fhs[key]) ? fhs[key] : new EnumFeatureHandler(key);
        var butEquals = $("<input type=\"radio\" name=\"" + this.name_prefix + "_" + key + "_comp\" value=\"equals\">");
        var butDiffers = $("<input type=\"radio\" name=\"" + this.name_prefix + "_" + key + "_comp\" value=\"differs\">");
        switch (efh.comparator) {
            case 'equals':
                butEquals.prop('checked', true);
                break;
            case 'differs':
                butDiffers.prop('checked', true);
                break;
        }
        var sel = $('<span></span>');
        sel.append(butEquals, '=', butDiffers, '&#x2260;');
        this.groups[key].append(sel);
        sel.on('click', efh, function (e) {
            var v = $(e.target).val();
            switch (v) {
                case 'equals':
                case 'differs':
                    e.data.comparator = v;
                    _this.updateMql();
                    break;
            }
        });
        var checkBoxes = [];
        for (var i = 0; i < enumValues.length; ++i) {
            var s = enumValues[i];
            var hv = featset.hideValues;
            var ov = featset.otherValues;
            if ((hv && hv.indexOf(s) !== -1) || ((ov && ov.indexOf(s) !== -1)))
                continue;
            var scb = new SortingCheckBox(this.name_prefix + '_' + key, s, getFeatureValueFriendlyName(valueType, s, false, false));
            scb.setSelected(efh.values && efh.values.indexOf(s) !== -1);
            checkBoxes.push(scb);
        }
        checkBoxes.sort(function (a, b) { return StringWithSort.compare(a.getSws(), b.getSws()); });
        var columns = checkBoxes.length > 12 ? 3 :
            checkBoxes.length > 4 ? 2 : 1;
        var rows = Math.ceil(checkBoxes.length / columns);
        var group2 = $('<table></table>');
        for (var r = 0; r < rows; ++r) {
            var row = $('<tr></tr>');
            for (var c = 0; c < columns; ++c) {
                var cell = $('<td></td>');
                if (c * rows + r < checkBoxes.length)
                    cell.append(checkBoxes[c * rows + r].getJQuery());
                row.append(cell);
            }
            group2.append(row);
        }
        group2.on('click', efh, function (e) {
            var target = $(e.target);
            if (target.attr('type') === 'checkbox') {
                if (target.prop('checked'))
                    e.data.addValue(target.attr('value'));
                else
                    e.data.removeValue(target.attr('value'));
                _this.updateMql();
            }
        });
        this.groups[key].append(group2);
        this.handlers.push(efh);
    };
    PanelTemplMql.prototype.getOtype = function () {
        return this.objectTypeCombo.val();
    };
    PanelTemplMql.prototype.setOtype = function (otype) {
        this.objectTypeCombo.val(otype);
        this.objectTypeCombo.change();
    };
    PanelTemplMql.prototype.setUsemql = function () {
        this.rbMql.prop('checked', true);
        this.rbMql.click();
    };
    PanelTemplMql.prototype.isDirty = function () {
        return this.getMql() !== this.txtEntry;
    };
    PanelTemplMql.prototype.makeMql = function () {
        var sb = '';
        if (this.handlers) {
            var abet = new util.AddBetween(' AND ');
            for (var i = 0; i < this.handlers.length; ++i) {
                var fh = this.handlers[i];
                if (fh.hasValues())
                    sb += abet.getStr() + fh.toMql();
            }
        }
        return sb;
    };
    PanelTemplMql.prototype.updateMql = function () {
        this.setMql(this.makeMql());
    };
    PanelTemplMql.prototype.getInfo = function () {
        var res = {
            object: this.getOtype(),
            mql: null,
            featHand: { vhand: null },
            useForQo: this.getUseForQo()
        };
        if (this.rbMql.prop('checked'))
            res.mql = this.getMql();
        else {
            res.featHand.vhand = [];
            for (var i = 0; i < this.handlers.length; ++i) {
                var fh = this.handlers[i];
                if (fh.hasValues())
                    res.featHand.vhand.push(fh);
            }
        }
        return res;
    };
    return PanelTemplMql;
}());
var PanelTemplSentenceSelector = (function (_super) {
    __extends(PanelTemplSentenceSelector, _super);
    function PanelTemplSentenceSelector(initialMd, templTabs, where, qoselTab, featureTab) {
        var _this = _super.call(this, initialMd, 'sensel') || this;
        _this.templTabs = templTabs;
        _this.qoselTab = qoselTab;
        _this.featureTab = featureTab;
        _this.cbUseForQo = $('<input type="checkbox" name="useforqol">');
        _this.cbUseForQoLabel = $("<span>" + localize('use_for_qosel') + "</span>");
        _this.questObjTypeLab = $("<span>" + localize('sentence_unit_type_prompt') + "</span>");
        _this.featSelLab = $("<span>" + localize('feature_prompt') + "</span>");
        _this.importShebanq = $("<button type=\"button\">" + localize('import_shebanq') + "</button>");
        _this.dirty = false;
        _this.cbUseForQo.click(function () {
            if (_this.cbUseForQo.is(':checked'))
                _this.templTabs.tabs('disable', 3);
            else
                _this.templTabs.tabs('enable', 3);
            _this.populateFeatureTab(null);
            _this.dirty = true;
        });
        _this.rbMqlLabel = $("<span>" + localize('mql_qosel_prompt') + "</span>");
        _this.rbFriendlyLabel = $("<span>" + localize('friendly_featsel_prompt') + "</span>");
        _this.doLayout(where);
        if (_this.initialMd == null || _this.initialMd.useForQo) {
            _this.cbUseForQo.prop('checked', true);
            _this.templTabs.tabs('disable', 3);
        }
        else {
            _this.cbUseForQo.prop('checked', false);
            _this.templTabs.tabs('enable', 3);
        }
        _this.importShebanq.click(import_from_shebanq);
        _this.finish_construct();
        return _this;
    }
    PanelTemplSentenceSelector.prototype.switchToMql = function (useMql) {
        this.mqlText.prop('disabled', !useMql);
        this.objectTypeCombo.prop('disabled', useMql);
        this.featureCombo.prop('disabled', useMql);
        this.cbUseForQo.prop('disabled', useMql);
        if (useMql) {
            this.questObjTypeLab.addClass('disabled');
            this.featSelLab.addClass('disabled');
            this.cbUseForQoLabel.addClass('disabled');
            this.cbUseForQo.prop('checked', false);
            this.templTabs.tabs('enable', 3);
            this.importShebanq.prop('disabled', false);
        }
        else {
            this.questObjTypeLab.removeClass('disabled');
            this.featSelLab.removeClass('disabled');
            this.cbUseForQoLabel.removeClass('disabled');
            this.importShebanq.prop('disabled', true);
        }
        if (this.currentBox) {
            if (useMql)
                this.currentBox.hide();
            else
                this.currentBox.show();
        }
        this.populateFeatureTab(null);
    };
    PanelTemplSentenceSelector.prototype.makeMql = function () {
        return "[" + this.getOtype() + " NORETRIEVE " + _super.prototype.makeMql.call(this) + "]";
    };
    PanelTemplSentenceSelector.prototype.getUseForQo = function () {
        return this.cbUseForQo.prop('checked');
    };
    PanelTemplSentenceSelector.prototype.isDirty = function () {
        return _super.prototype.isDirty.call(this) || this.dirty;
    };
    PanelTemplSentenceSelector.prototype.doLayout = function (where) {
        var table = $('<table></table>');
        var row;
        var cell;
        row = $('<tr></tr>');
        cell = $('<td colspan="2"></td>');
        cell.append(this.cbUseForQo, this.cbUseForQoLabel);
        row.append(cell);
        table.append(row);
        row = $('<tr></tr>');
        cell = $('<td></td>');
        cell.append(this.rbMql, this.rbMqlLabel);
        row.append(cell);
        cell = $('<td></td>');
        cell.append(this.mqlText);
        row.append(cell);
        table.append(row);
        row = $('<tr></tr>');
        cell = $('<td></td>');
        row.append(cell);
        cell = $('<td></td>');
        cell.append(this.importShebanq);
        row.append(cell);
        table.append(row);
        row = $('<tr></tr>');
        cell = $('<td colspan="2"></td>');
        cell.append(this.rbFriendly, this.rbFriendlyLabel);
        row.append(cell);
        table.append(row);
        row = $('<tr></tr>');
        cell = $('<td></td>');
        cell.append(this.questObjTypeLab);
        row.append(cell);
        cell = $('<td></td>');
        cell.append(this.objectTypeCombo);
        row.append(cell);
        table.append(row);
        row = $('<tr></tr>');
        cell = $('<td></td>');
        cell.append(this.featSelLab);
        row.append(cell);
        cell = $('<td></td>');
        cell.append(this.featureCombo);
        row.append(cell);
        table.append(row);
        row = $('<tr></tr>');
        cell = $('<td id="clearbuttoncell"></td>');
        cell.append(this.clear);
        row.append(cell);
        cell = $('<td></td>');
        cell.append(this.fpan);
        row.append(cell);
        table.append(row);
        where.append(table);
    };
    PanelTemplSentenceSelector.prototype.populateFeatureTab = function (otype) {
        if (this.cbUseForQo.prop('checked')) {
            if (otype === null)
                otype = this.getOtype();
            this.featureTab.populate(otype);
        }
        else
            this.qoselTab.populateFeatureTab(null);
    };
    return PanelTemplSentenceSelector;
}(PanelTemplMql));
var PanelTemplQuizObjectSelector = (function (_super) {
    __extends(PanelTemplQuizObjectSelector, _super);
    function PanelTemplQuizObjectSelector(md, where, featureTab) {
        var _this = _super.call(this, md, 'qosel') || this;
        _this.featSelLab = $("<span>" + localize('feature_prompt') + "</span>");
        _this.featureTab = featureTab;
        _this.rbMqlLabel = $("<span>" + localize('mql_featsel_prompt') + "</span>");
        _this.rbFriendlyLabel = $("<span>" + localize('friendly_featsel_prompt') + "</span>");
        _this.doLayout(where);
        _this.finish_construct();
        return _this;
    }
    PanelTemplQuizObjectSelector.prototype.switchToMql = function (useMql) {
        this.mqlText.prop('disabled', !useMql);
        this.featureCombo.prop('disabled', useMql);
        if (useMql)
            this.featSelLab.addClass('disabled');
        else
            this.featSelLab.removeClass('disabled');
        if (this.currentBox) {
            if (useMql)
                this.currentBox.hide();
            else
                this.currentBox.show();
        }
    };
    PanelTemplQuizObjectSelector.prototype.doLayout = function (where) {
        var table = $('<table></table>');
        var row;
        var cell;
        row = $('<tr></tr>');
        cell = $("<td>" + localize('sentence_unit_type_prompt') + "</td>");
        row.append(cell);
        cell = $('<td></td>');
        cell.append(this.objectTypeCombo);
        row.append(cell);
        table.append(row);
        row = $('<tr></tr>');
        cell = $('<td></td>');
        cell.append(this.rbMql, this.rbMqlLabel);
        row.append(cell);
        cell = $('<td></td>');
        cell.append(this.mqlText);
        row.append(cell);
        table.append(row);
        row = $('<tr></tr>');
        cell = $('<td colspan="2"></td>');
        cell.append(this.rbFriendly, this.rbFriendlyLabel);
        row.append(cell);
        table.append(row);
        row = $('<tr></tr>');
        cell = $('<td></td>');
        cell.append(this.featSelLab);
        row.append(cell);
        cell = $('<td></td>');
        cell.append(this.featureCombo);
        row.append(cell);
        table.append(row);
        row = $('<tr></tr>');
        cell = $('<td id="clearbuttoncell"></td>');
        cell.append(this.clear);
        row.append(cell);
        cell = $('<td></td>');
        cell.append(this.fpan);
        row.append(cell);
        table.append(row);
        where.append(table);
    };
    PanelTemplQuizObjectSelector.prototype.getUseForQo = function () {
        return false;
    };
    PanelTemplQuizObjectSelector.prototype.populateFeatureTab = function (otype) {
        if (otype === null)
            otype = this.getOtype();
        this.featureTab.populate(otype);
    };
    return PanelTemplQuizObjectSelector;
}(PanelTemplMql));
var Qere = (function () {
    function Qere() {
    }
    Qere.database_has_qere = function () {
        return configuration.databaseName === Qere.dbName;
    };
    Qere.otype_has_qere = function (otype) {
        return otype === Qere.dbOtype;
    };
    Qere.otype = function () {
        return Qere.dbOtype;
    };
    Qere.feature = function () {
        if (configuration.propertiesName === "ETCBC4")
            return "qere_utf8";
        if (configuration.propertiesName === "ETCBC4-translit")
            return "qere_translit";
        return null;
    };
    Qere.dbName = 'ETCBC4';
    Qere.dbOtype = 'word';
    return Qere;
}());
var ButtonSelection;
(function (ButtonSelection) {
    ButtonSelection[ButtonSelection["SHOW"] = 0] = "SHOW";
    ButtonSelection[ButtonSelection["REQUEST"] = 1] = "REQUEST";
    ButtonSelection[ButtonSelection["REQUEST_DROPDOWN"] = 2] = "REQUEST_DROPDOWN";
    ButtonSelection[ButtonSelection["DONT_CARE"] = 3] = "DONT_CARE";
    ButtonSelection[ButtonSelection["DONT_SHOW"] = 4] = "DONT_SHOW";
    ButtonSelection[ButtonSelection["SHOW_QERE"] = 5] = "SHOW_QERE";
})(ButtonSelection || (ButtonSelection = {}));
;
var ButtonsAndLabel = (function () {
    function ButtonsAndLabel(lab, featName, otype, select, hideFeatures, useDropDown, canShow, canRequest, canDisplayGrammar, canShowQere) {
        var _this = this;
        this.featName = featName;
        this.hideFeatures = hideFeatures;
        this.useDropDown = useDropDown;
        this.canShow = canShow;
        this.canRequest = canRequest;
        this.canDisplayGrammar = canDisplayGrammar;
        this.canShowQere = canShowQere;
        this.showFeat = canShow ? $("<input type=\"radio\" name=\"feat_" + otype + "_" + featName + "\" value=\"show\">") : $('<span></span>');
        this.reqFeat = canRequest ? $("<input type=\"radio\" name=\"feat_" + otype + "_" + featName + "\" value=\"request\">") : $('<span></span>');
        this.dcFeat = $("<input type=\"radio\" name=\"feat_" + otype + "_" + featName + "\" value=\"dontcare\">");
        this.dontShowFeat = canDisplayGrammar ? $("<input type=\"radio\" name=\"feat_" + otype + "_" + featName + "\" value=\"dontshowfeat\">") : $('<span></span>');
        this.showQere = canShowQere ? $("<input type=\"radio\" name=\"feat_" + otype + "_" + featName + "\" value=\"showqere\">") : $('<span></span>');
        this.feat = $("<span>" + lab + "</span>");
        this.limitter = $('<span></span>');
        switch (select) {
            case ButtonSelection.SHOW:
                this.showFeat.prop('checked', true);
                break;
            case ButtonSelection.REQUEST:
            case ButtonSelection.REQUEST_DROPDOWN:
                this.reqFeat.prop('checked', true);
                break;
            case ButtonSelection.DONT_CARE:
                this.dcFeat.prop('checked', true);
                break;
            case ButtonSelection.DONT_SHOW:
                this.dontShowFeat.prop('checked', true);
                break;
            case ButtonSelection.SHOW_QERE:
                this.showQere.prop('checked', true);
                break;
        }
        if (useDropDown) {
            this.ddCheck = $("<input type=\"checkbox\" name=\"dd_" + otype + "_" + featName + "\">");
            this.ddCheck.prop('checked', select != ButtonSelection.REQUEST);
        }
        else if (canShowQere) {
            this.ddCheck = this.showQere;
        }
        else
            this.ddCheck = $('<span></span>');
        if (canRequest) {
            if (useDropDown) {
                this.ddCheck.prop('disabled', !this.reqFeat.prop('checked'));
                if (canShow)
                    this.showFeat.click(function () { return _this.ddCheck.prop('disabled', true); });
                this.reqFeat.click(function () { return _this.ddCheck.prop('disabled', false); });
                this.dcFeat.click(function () { return _this.ddCheck.prop('disabled', true); });
                if (canDisplayGrammar)
                    this.dontShowFeat.click(function () { return _this.ddCheck.prop('disabled', true); });
            }
        }
        var valueType = typeinfo.obj2feat[otype][featName];
        if (typeinfo.enumTypes.indexOf(valueType) != -1) {
            if (canRequest) {
                var limitButton_1 = $("<a href=\"#\" style=\"color:white\" class=\"badge\"></a>");
                var updateBadge_1 = function () {
                    if (!hideFeatures || hideFeatures.length == 0)
                        limitButton_1.removeClass('badge-danger').addClass('badge-success').html(localize('unlimited'));
                    else
                        limitButton_1.removeClass('badge-success').addClass('badge-danger').html(localize('limited'));
                };
                updateBadge_1();
                limitButton_1.click(function () {
                    var ld = new LimitDialog(valueType, getFeatureSetting(otype, featName), hideFeatures, function (newHideFeatures) {
                        _this.hideFeatures = hideFeatures = newHideFeatures;
                        updateBadge_1();
                    });
                });
                var removeit = function () { return _this.limitter.empty(); };
                this.reqFeat.change(function () { return _this.limitter.append(limitButton_1); });
                if (select === ButtonSelection.REQUEST)
                    this.reqFeat.change();
                this.dcFeat.change(removeit);
                if (canShow)
                    this.showFeat.change(removeit);
                if (canDisplayGrammar)
                    this.dontShowFeat.change(removeit);
            }
        }
    }
    ButtonsAndLabel.prototype.getRow = function () {
        var row = $('<tr></tr>');
        var cell;
        cell = $('<td></td>').append(this.showFeat);
        row.append(cell);
        cell = $('<td></td>').append(this.reqFeat);
        row.append(cell);
        cell = $('<td></td>').append(this.dcFeat);
        row.append(cell);
        cell = $('<td></td>').append(this.dontShowFeat);
        row.append(cell);
        cell = $('<td></td>').append(this.ddCheck);
        row.append(cell);
        cell = $('<td class="leftalign"></td>').append(this.feat);
        row.append(cell);
        cell = $('<td></td>').append(this.limitter);
        row.append(cell);
        return row;
    };
    ButtonsAndLabel.prototype.isSelected = function (button) {
        switch (button) {
            case ButtonSelection.SHOW:
                return this.canShow && this.showFeat.prop('checked');
            case ButtonSelection.REQUEST:
                return this.canRequest && this.reqFeat.prop('checked');
            case ButtonSelection.REQUEST_DROPDOWN:
                return this.useDropDown && this.ddCheck.prop('checked');
            case ButtonSelection.DONT_CARE:
                return this.dcFeat.prop('checked');
            case ButtonSelection.DONT_SHOW:
                return this.canDisplayGrammar && this.dontShowFeat.prop('checked');
            case ButtonSelection.SHOW_QERE:
                return this.canShowQere && this.showQere.prop('checked');
        }
    };
    ButtonsAndLabel.prototype.getHideFeatures = function () {
        return this.hideFeatures;
    };
    ButtonsAndLabel.prototype.getFeatName = function () {
        return this.featName;
    };
    return ButtonsAndLabel;
}());
var LimitDialog = (function () {
    function LimitDialog(valueType, featset, hideFeatures, callback) {
        var _this = this;
        this.callback = callback;
        var butSetAll = $("<a class=\"badge badge-success\" style=\"margin:0 5px 5px 0\" href=\"#\">" + localize('set_all') + "</a>");
        var butClearAll = $("<a class=\"badge badge-success\" style=\"margin:0 5px 5px 0\" href=\"#\">" + localize('clear_all') + "</a>");
        butSetAll.click(function () { return $('input[type=checkbox][name=hideFeatures]').prop('checked', true); });
        butClearAll.click(function () { return $('input[type=checkbox][name=hideFeatures]').prop('checked', false); });
        var setclear = $('<div></div>');
        setclear.append(butSetAll).append(butClearAll);
        if (configuration.databaseName == 'ETCBC4' && valueType == 'verbal_stem_t') {
            var butHebrew = $("<a class=\"badge badge-success\" style=\"margin:0 5px 5px 0\" href=\"#\">" + localize('set_hebrew') + "</a>");
            var butAramaic = $("<a class=\"badge badge-success\" style=\"margin:0 5px 5px 0\" href=\"#\">" + localize('set_aramaic') + "</a>");
            var hebrewStems_1 = ['NA', 'etpa', 'hif', 'hit', 'hof', 'hotp', 'hsht', 'htpo', 'nif', 'nit',
                'pasq', 'piel', 'poal', 'poel', 'pual', 'qal', 'tif'];
            var aramaicStems_1 = ['NA', 'afel', 'etpa', 'etpe', 'haf ', 'hof ', 'hsht', 'htpa', 'htpe',
                'pael', 'peal', 'peil', 'shaf'];
            butHebrew.click(function () {
                $('input[type=checkbox][name=hideFeatures]').prop('checked', false);
                for (var i = 0; i < hebrewStems_1.length; ++i)
                    $('input[type=checkbox][name=hideFeatures][value=' + hebrewStems_1[i] + ']').prop('checked', true);
            });
            butAramaic.click(function () {
                $('input[type=checkbox][name=hideFeatures]').prop('checked', false);
                for (var i = 0; i < aramaicStems_1.length; ++i)
                    $('input[type=checkbox][name=hideFeatures][value=' + aramaicStems_1[i] + ']').prop('checked', true);
            });
            setclear = setclear.add($('<div></div>').append(butHebrew).append(butAramaic));
        }
        var enumValues = typeinfo.enum2values[valueType];
        var checkBoxes = [];
        for (var i = 0; i < enumValues.length; ++i) {
            var s = enumValues[i];
            var hv = featset.hideValues;
            var ov = featset.otherValues;
            if ((hv && hv.indexOf(s) !== -1) || ((ov && ov.indexOf(s) !== -1)))
                continue;
            var scb = new SortingCheckBox('hideFeatures', s, getFeatureValueFriendlyName(valueType, s, false, false));
            scb.setSelected(!hideFeatures || hideFeatures.indexOf(s) === -1);
            checkBoxes.push(scb);
        }
        checkBoxes.sort(function (a, b) { return StringWithSort.compare(a.getSws(), b.getSws()); });
        var columns = checkBoxes.length > 12 ? 3 :
            checkBoxes.length > 4 ? 2 : 1;
        var rows = Math.ceil(checkBoxes.length / columns);
        var table = $('<table></table>');
        for (var r = 0; r < rows; ++r) {
            var row = $('<tr></tr>');
            for (var c = 0; c < columns; ++c) {
                var cell = $('<td></td>');
                if (c * rows + r < checkBoxes.length)
                    cell.append(checkBoxes[c * rows + r].getJQuery());
                row.append(cell);
            }
            table.append(row);
        }
        $('#feature-limit-body').empty().append(setclear).append(table);
        $('#feature-limit-dialog-save').off('click').on('click', function () { return _this.saveButtonAction(); });
        $('#feature-limit-dialog').modal('show');
    }
    LimitDialog.prototype.saveButtonAction = function () {
        var hideFeatures = [];
        $('input[type=checkbox][name=hideFeatures]:not(:checked)').each(function () {
            hideFeatures.push($(this).val());
        });
        $('#feature-limit-dialog-save').off('click');
        this.callback(hideFeatures);
        $('#feature-limit-dialog').modal('hide');
    };
    return LimitDialog;
}());
var PanelForOneOtype = (function () {
    function PanelForOneOtype(otype, ptqf) {
        this.allBAL = [];
        this.panel = $('<table class="striped featuretable"></table>');
        var useSavedFeatures = otype === ptqf.initialOtype;
        this.panel.append('<tr>'
            + ("<th>" + localize('show') + "</th>")
            + ("<th>" + localize('request') + "</th>")
            + ("<th>" + localize('dont_care') + "</th>")
            + ("<th>" + localize('dont_show') + "</th>")
            + ("<th>" + localize('multiple_choice') + "</th>")
            + ("<th class=\"leftalign\">" + localize('feature') + "</th>")
            + '<th></th>'
            + '</tr>');
        this.visualBAL = new ButtonsAndLabel(localize('visual'), 'visual', otype, useSavedFeatures ? ptqf.getSelector('visual') : ButtonSelection.DONT_CARE, null, configuration.objHasSurface === otype && !!getFeatureSetting(otype, configuration.surfaceFeature).alternateshowrequestSql, true, configuration.objHasSurface === otype, false, false);
        this.panel.append(this.visualBAL.getRow());
        var hasSurfaceFeature = otype === configuration.objHasSurface;
        var sg = getSentenceGrammarFor(otype);
        var keylist = [];
        for (var featName in getObjectSetting(otype).featuresetting) {
            if (getFeatureSetting(otype, featName).ignoreShow
                && getFeatureSetting(otype, featName).ignoreRequest
                && (sg === null || !sg.containsFeature(featName)))
                continue;
            if (typeinfo.obj2feat[otype][featName] === 'url')
                continue;
            if (hasSurfaceFeature && featName === configuration.surfaceFeature)
                continue;
            keylist.push(featName);
        }
        for (var ix = 0; ix < keylist.length; ++ix) {
            var featName = keylist[ix];
            var bal = new ButtonsAndLabel(getFeatureFriendlyName(otype, featName), featName, otype, useSavedFeatures ? ptqf.getSelector(featName) : ButtonSelection.DONT_CARE, ptqf.getHideFeatures(featName), !!getFeatureSetting(otype, featName).alternateshowrequestSql, !getFeatureSetting(otype, featName).ignoreShow, !getFeatureSetting(otype, featName).ignoreRequest, sg !== null && sg.containsFeature(featName), false);
            this.allBAL.push(bal);
            this.panel.append(bal.getRow());
        }
        this.panel.append('<tr><td colspan="5"></td><td class="leftalign">&nbsp;</tr>');
        this.panel.append('<tr>'
            + '<td colspan="2"></td>'
            + ("<th>" + localize('dont_care') + "</th>")
            + ("<th>" + localize('dont_show') + "</th>")
            + ("<th>" + (Qere.database_has_qere() && !Qere.otype_has_qere(otype) ? localize('show_qere') : '') + "</th>")
            + ("<th class=\"leftalign\">" + localize('other_sentence_unit_types') + "</th>")
            + '<th></th>'
            + '</tr>');
        for (var level in configuration.sentencegrammar) {
            var leveli = +level;
            if (isNaN(leveli))
                continue;
            var otherOtype = configuration.sentencegrammar[leveli].objType;
            if (otherOtype !== otype && configuration.objectSettings[otherOtype].mayselect) {
                var bal = new ButtonsAndLabel(getObjectFriendlyName(otherOtype), 'otherOtype_' + otherOtype, otype, useSavedFeatures ? ptqf.getObjectSelector(otherOtype) : ButtonSelection.DONT_CARE, null, false, false, false, true, Qere.database_has_qere() && Qere.otype_has_qere(otherOtype));
                this.allBAL.push(bal);
                this.panel.append(bal.getRow());
            }
        }
    }
    PanelForOneOtype.prototype.hide = function () {
        this.panel.hide();
    };
    PanelForOneOtype.prototype.show = function () {
        this.panel.show();
    };
    PanelForOneOtype.prototype.getPanel = function () {
        return this.panel;
    };
    return PanelForOneOtype;
}());
var PanelTemplQuizFeatures = (function () {
    function PanelTemplQuizFeatures(initialOtype, initialQf, where) {
        this.initialOtype = initialOtype;
        this.initialQf = initialQf;
        this.panels = {};
        this.fpan = $('<div id="fpan"></div>');
        where.append(this.fpan);
    }
    PanelTemplQuizFeatures.prototype.populate = function (otype) {
        if (otype === this.oldOtype)
            return;
        this.oldOtype = otype;
        if (this.visiblePanel)
            this.visiblePanel.hide();
        this.visiblePanel = this.panels[otype];
        if (!this.visiblePanel) {
            this.visiblePanel = new PanelForOneOtype(otype, this);
            this.panels[otype] = this.visiblePanel;
            this.fpan.append(this.visiblePanel.getPanel());
        }
        this.visiblePanel.show();
    };
    PanelTemplQuizFeatures.prototype.getSelector = function (feat) {
        if (!this.initialQf)
            return ButtonSelection.DONT_CARE;
        for (var i = 0; i < this.initialQf.showFeatures.length; ++i)
            if (this.initialQf.showFeatures[i] === feat)
                return ButtonSelection.SHOW;
        for (var i = 0; i < this.initialQf.requestFeatures.length; ++i)
            if (this.initialQf.requestFeatures[i].name === feat)
                return this.initialQf.requestFeatures[i].usedropdown ? ButtonSelection.REQUEST_DROPDOWN : ButtonSelection.REQUEST;
        if (this.initialQf.dontShowFeatures)
            for (var i = 0; i < this.initialQf.dontShowFeatures.length; ++i)
                if (this.initialQf.dontShowFeatures[i] === feat)
                    return ButtonSelection.DONT_SHOW;
        return ButtonSelection.DONT_CARE;
    };
    PanelTemplQuizFeatures.prototype.getHideFeatures = function (feat) {
        if (!this.initialQf)
            return null;
        for (var i = 0; i < this.initialQf.requestFeatures.length; ++i)
            if (this.initialQf.requestFeatures[i].name === feat)
                return this.initialQf.requestFeatures[i].hideFeatures;
    };
    PanelTemplQuizFeatures.prototype.getObjectSelector = function (otype) {
        if (this.initialQf && this.initialQf.dontShowObjects) {
            for (var i = 0; i < this.initialQf.dontShowObjects.length; ++i) {
                if (this.initialQf.dontShowObjects[i].content === otype) {
                    if (this.initialQf.dontShowObjects[i].show)
                        return ButtonSelection.SHOW_QERE;
                    else
                        return ButtonSelection.DONT_SHOW;
                }
            }
        }
        return ButtonSelection.DONT_CARE;
    };
    PanelTemplQuizFeatures.prototype.noRequestFeatures = function () {
        if (!this.visiblePanel)
            return true;
        if (this.visiblePanel.visualBAL.isSelected(ButtonSelection.REQUEST))
            return false;
        for (var i = 0; i < this.visiblePanel.allBAL.length; ++i)
            if (this.visiblePanel.allBAL[i].isSelected(ButtonSelection.REQUEST))
                return false;
        return true;
    };
    PanelTemplQuizFeatures.prototype.noShowFeatures = function () {
        if (!this.visiblePanel)
            return true;
        if (this.visiblePanel.visualBAL.isSelected(ButtonSelection.SHOW))
            return false;
        for (var i = 0; i < this.visiblePanel.allBAL.length; ++i)
            if (this.visiblePanel.allBAL[i].isSelected(ButtonSelection.SHOW))
                return false;
        return true;
    };
    PanelTemplQuizFeatures.prototype.getInfo = function () {
        var qf = {
            showFeatures: [],
            requestFeatures: [],
            dontShowFeatures: [],
            dontShowObjects: []
        };
        if (!this.visiblePanel)
            return null;
        if (this.visiblePanel.visualBAL.isSelected(ButtonSelection.SHOW))
            qf.showFeatures.push('visual');
        else if (this.visiblePanel.visualBAL.isSelected(ButtonSelection.REQUEST))
            qf.requestFeatures.push({ name: 'visual', usedropdown: this.visiblePanel.visualBAL.isSelected(ButtonSelection.REQUEST_DROPDOWN), hideFeatures: null });
        for (var i = 0; i < this.visiblePanel.allBAL.length; ++i) {
            var bal = this.visiblePanel.allBAL[i];
            if (bal.isSelected(ButtonSelection.SHOW))
                qf.showFeatures.push(bal.getFeatName());
            else if (bal.isSelected(ButtonSelection.REQUEST))
                qf.requestFeatures.push({ name: bal.getFeatName(), usedropdown: bal.isSelected(ButtonSelection.REQUEST_DROPDOWN), hideFeatures: bal.getHideFeatures() });
            else if (bal.isSelected(ButtonSelection.DONT_SHOW)) {
                var fn = bal.getFeatName();
                if (fn.substring(0, 11) === 'otherOtype_')
                    qf.dontShowObjects.push({ content: fn.substring(11) });
                else
                    qf.dontShowFeatures.push(fn);
            }
            else if (bal.isSelected(ButtonSelection.SHOW_QERE)) {
                qf.dontShowObjects.push({ content: Qere.otype(), show: Qere.feature() });
            }
        }
        return qf;
    };
    PanelTemplQuizFeatures.prototype.isDirty = function () {
        var qfnow = this.getInfo();
        if (qfnow.showFeatures.length !== this.initialQf.showFeatures.length ||
            qfnow.requestFeatures.length !== this.initialQf.requestFeatures.length ||
            qfnow.dontShowFeatures.length !== this.initialQf.dontShowFeatures.length ||
            qfnow.dontShowObjects.length !== this.initialQf.dontShowObjects.length) {
            return true;
        }
        for (var i = 0; i < qfnow.showFeatures.length; ++i)
            if (qfnow.showFeatures[i] !== this.initialQf.showFeatures[i]) {
                return true;
            }
        for (var i = 0; i < qfnow.requestFeatures.length; ++i) {
            if (qfnow.requestFeatures[i].name !== this.initialQf.requestFeatures[i].name ||
                qfnow.requestFeatures[i].usedropdown !== this.initialQf.requestFeatures[i].usedropdown) {
                return true;
            }
            if (qfnow.requestFeatures[i].hideFeatures !== this.initialQf.requestFeatures[i].hideFeatures) {
                if (qfnow.requestFeatures[i].hideFeatures === null || this.initialQf.requestFeatures[i].hideFeatures === null
                    || qfnow.requestFeatures[i].hideFeatures.length !== this.initialQf.requestFeatures[i].hideFeatures.length)
                    return true;
                for (var j = 0; j < qfnow.requestFeatures[i].hideFeatures.length; ++j)
                    if (qfnow.requestFeatures[i].hideFeatures[j] !== this.initialQf.requestFeatures[i].hideFeatures[j])
                        return true;
            }
        }
        for (var i = 0; i < qfnow.dontShowFeatures.length; ++i)
            if (qfnow.dontShowFeatures[i] !== this.initialQf.dontShowFeatures[i]) {
                return true;
            }
        for (var i = 0; i < qfnow.dontShowObjects.length; ++i)
            if (qfnow.dontShowObjects[i].content !== this.initialQf.dontShowObjects[i].content ||
                qfnow.dontShowObjects[i].show !== this.initialQf.dontShowObjects[i].show) {
                return true;
            }
        return false;
    };
    return PanelTemplQuizFeatures;
}());
var VerbClassSelection;
(function (VerbClassSelection) {
    VerbClassSelection[VerbClassSelection["YES"] = 0] = "YES";
    VerbClassSelection[VerbClassSelection["NO"] = 1] = "NO";
    VerbClassSelection[VerbClassSelection["DONT_CARE"] = 2] = "DONT_CARE";
})(VerbClassSelection || (VerbClassSelection = {}));
;
var VerbClassButtonsAndLabel = (function () {
    function VerbClassButtonsAndLabel(lab, name, dataName, select) {
        this.yes = $("<input type=\"radio\" name=\"" + name + "\" value=\"yes\"      data-name=\"" + dataName + "\">");
        this.no = $("<input type=\"radio\" name=\"" + name + "\" value=\"no\"       data-name=\"" + dataName + "\">");
        this.dontcare = $("<input type=\"radio\" name=\"" + name + "\" value=\"dontcare\" data-name=\"" + dataName + "\">");
        this.label = $("<span>" + lab + "</span>");
        switch (select) {
            case VerbClassSelection.YES:
                this.yes.prop('checked', true);
                break;
            case VerbClassSelection.NO:
                this.no.prop('checked', true);
                break;
            case VerbClassSelection.DONT_CARE:
                this.dontcare.prop('checked', true);
                break;
        }
    }
    VerbClassButtonsAndLabel.prototype.getRow = function () {
        var row = $('<tr></tr>');
        var cell;
        cell = $('<td></td>').append(this.yes);
        row.append(cell);
        cell = $('<td></td>').append(this.no);
        row.append(cell);
        cell = $('<td></td>').append(this.dontcare);
        row.append(cell);
        cell = $('<td class="leftalign"></td>').append(this.label);
        row.append(cell);
        return row;
    };
    return VerbClassButtonsAndLabel;
}());
var PanelForOneVcChoice = (function () {
    function PanelForOneVcChoice(enumValues, valueType, prefix, lv) {
        this.allBAL = [];
        this.panel = $('<table class="striped featuretable"></table>');
        this.panel.append('<tr>'
            + ("<th>" + localize('verb_class_yes') + "</th>")
            + ("<th>" + localize('verb_class_no') + "</th>")
            + ("<th>" + localize('verb_class_dont_care') + "</th>")
            + ("<th class=\"leftalign\">" + localize('verb_class') + "</th>")
            + '</tr>');
        var swsValues = [];
        for (var ix = 0; ix < enumValues.length; ++ix)
            swsValues.push(new StringWithSort(getFeatureValueFriendlyName(valueType, enumValues[ix], false, false), enumValues[ix]));
        swsValues.sort(function (a, b) { return StringWithSort.compare(a, b); });
        for (var ix = 0; ix < swsValues.length; ++ix) {
            var vc = swsValues[ix].getInternal();
            var vcsel = VerbClassSelection.DONT_CARE;
            if (lv.yes_values.indexOf(vc) != -1)
                vcsel = VerbClassSelection.YES;
            else if (lv.no_values.indexOf(vc) != -1)
                vcsel = VerbClassSelection.NO;
            var bal = new VerbClassButtonsAndLabel(swsValues[ix].getString(), prefix + "_" + vc, vc, vcsel);
            this.allBAL.push(bal);
            this.panel.append(bal.getRow());
        }
    }
    PanelForOneVcChoice.prototype.getPanel = function () {
        return this.panel;
    };
    return PanelForOneVcChoice;
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
var SortingCheckBox = (function () {
    function SortingCheckBox(name, value, text) {
        this.sws = new StringWithSort(text);
        this.checkbox = $("<input type=\"checkbox\" name=\"" + name + "\" value=\"" + value + "\">");
        this.jq = $('<span></span>');
        this.jq.append(this.checkbox, this.sws.getString());
    }
    SortingCheckBox.prototype.setSelected = function (selected) {
        this.checkbox.prop('checked', selected);
    };
    SortingCheckBox.prototype.getSws = function () {
        return this.sws;
    };
    SortingCheckBox.prototype.getJQuery = function () {
        return this.jq;
    };
    return SortingCheckBox;
}());
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
var VirtualKeyboard;
var origMayLocate;
var origSentBefore;
var origSentAfter;
var origFixedQuestions;
var panelSent;
var panelSentUnit;
var panelFeatures;
var isSubmitting = false;
var checked_passages;
var ckeditor;
var charset;
function isDirty() {
    if (isSubmitting)
        return false;
    if (ckeditor.ckeditorGet().checkDirty())
        return true;
    checked_passages = $('#passagetree').jstree('get_checked', null, false);
    if (checked_passages.length !== initial_universe.length)
        return true;
    if ($('#maylocate_cb').prop('checked') != origMayLocate)
        return true;
    if ($('#sentbefore').val() != origSentBefore)
        return true;
    if ($('#sentafter').val() != origSentAfter)
        return true;
    if ($('#fixedquestions').val() != origFixedQuestions)
        return true;
    for (var i = 0; i < checked_passages.length; ++i)
        if ($(checked_passages[i]).data('ref') !== initial_universe[i])
            return true;
    return panelSent.isDirty() || panelSentUnit.isDirty() || panelFeatures.isDirty();
}
function show_error(id, message) {
    $(id + '-text').text(message);
    $(id).show();
}
function hide_error(id) {
    $(id).hide();
}
function save_quiz() {
    checked_passages = $('#passagetree').jstree('get_checked', null, false);
    if (checked_passages.length == 0) {
        myalert(localize('passage_selection'), localize('no_passages'));
        return;
    }
    if (panelFeatures.noRequestFeatures()) {
        myalert(localize('feature_specification'), localize('no_request_feature'));
        return;
    }
    if (panelFeatures.noShowFeatures()) {
        myalert(localize('feature_specification'), localize('no_show_feature'));
        return;
    }
    hide_error('#filename-error');
    $('#filename-dialog-save').off('click');
    $('#filename-dialog-save').on('click', function () {
        if ($('#filename-name').val().trim() == '')
            show_error('#filename-error', localize('missing_filename'));
        else {
            quiz_name = $('#filename-name').val().trim();
            $.ajax(check_url + "?dir=" + encodeURIComponent(dir_name) + "&quiz=" + encodeURIComponent(quiz_name))
                .done(function (data, textStatus, jqXHR) {
                switch (data.trim()) {
                    case 'OK':
                        $('#filename-dialog').modal('hide');
                        save_quiz2();
                        break;
                    case 'EXISTS':
                        $('#filename-dialog').modal('hide');
                        check_overwrite();
                        break;
                    case 'BADNAME':
                        show_error('#filename-error', localize('badname'));
                        break;
                    default:
                        show_error('#filename-error', data);
                        break;
                }
            })
                .fail(function (jqXHR, textStatus, errorThrown) {
                show_error('#filename-error', localize('error_response') + " " + errorThrown);
            });
        }
    });
    $('#filename-dialog').modal('show');
}
function check_overwrite() {
    $('#overwrite-yesbutton').off('click');
    $('#overwrite-yesbutton').on('click', function () {
        save_quiz2();
        $('#overwrite-dialog-confirm').modal('hide');
    });
    $('#overwrite-dialog-confirm').modal('show');
}
function save_quiz2() {
    decoded_3et.desc = ckeditor.val();
    decoded_3et.selectedPaths = [];
    for (var i = 0; i < checked_passages.length; ++i) {
        var r = $(checked_passages[i]).data('ref');
        if (r != '')
            decoded_3et.selectedPaths.push(r);
    }
    decoded_3et.maylocate = $('#maylocate_cb').prop('checked');
    decoded_3et.sentbefore = $('#sentbefore').val();
    decoded_3et.sentafter = $('#sentafter').val();
    decoded_3et.fixedquestions = +$('#fixedquestions').val();
    if (!(decoded_3et.fixedquestions > 0))
        decoded_3et.fixedquestions = 0;
    decoded_3et.sentenceSelection = panelSent.getInfo();
    decoded_3et.quizObjectSelection = panelSentUnit.getInfo();
    decoded_3et.quizFeatures = panelFeatures.getInfo();
    var form = $("<form action=\"" + submit_to + "\" method=\"post\">\n                             <input type=\"hidden\" name=\"dir\"      value=\"" + encodeURIComponent(dir_name) + "\">\n                             <input type=\"hidden\" name=\"quiz\"     value=\"" + encodeURIComponent(quiz_name) + "\">\n                             <input type=\"hidden\" name=\"quizdata\" value=\"" + encodeURIComponent(JSON.stringify(decoded_3et)) + "\">\n                           </form>");
    $('body').append(form);
    isSubmitting = true;
    form.submit();
}
function shebanq_to_qo(qo, mql) {
    if (qo === null) {
        $('#qo-dialog-text').html("<p>" + localize('sentence_selection_imported') + "</p><p>" + localize('no_focus') + "</p>");
        $('#qo-yesbutton').hide();
        $('#qo-nobutton').hide();
        $('#qo-okbutton').show();
        $('#qo-dialog-confirm').modal('show');
    }
    else {
        var msg = mql.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
        msg = "<br><code>[" + qo + " " + msg + "]</code><br>";
        msg = localize('use_qo_selection').format(msg);
        msg = "<p>" + localize('sentence_selection_imported') + "</p><p>" + msg + "</p>";
        $('#qo-dialog-text').html(msg);
        $('#qo-yesbutton').show();
        $('#qo-nobutton').show();
        $('#qo-okbutton').hide();
        $('#qo-yesbutton').off('click');
        $('#qo-yesbutton').on('click', function () {
            $('#qo-dialog-confirm').modal('hide');
            panelSentUnit.setOtype(qo);
            panelSentUnit.setUsemql();
            panelSentUnit.setMql(mql);
        });
        $('#qo-dialog-confirm').modal('show');
    }
}
function import_from_shebanq() {
    hide_error('#import-shebanq-error');
    $('#import-shebanq-button').off('click');
    $('#import-shebanq-button').on('click', function () {
        $('.ui-dialog *').css('cursor', 'wait');
        var shebanq_id = encodeURIComponent($('#import-shebanq-qid').val()).trim();
        var shebanq_dbvers = encodeURIComponent($('#import-shebanq-dbvers').val()).trim();
        $.ajax(import_shebanq_url + "?id=" + shebanq_id + "&version=" + shebanq_dbvers)
            .done(function (data, textStatus, jqXHR) {
            $('.ui-dialog *').css('cursor', 'auto');
            var result = JSON.parse(data);
            if (result.error === null) {
                panelSent.setMql(result.sentence_mql);
                $('#import-shebanq-dialog').modal('hide');
                shebanq_to_qo(result.sentence_unit, result.sentence_unit_mql);
            }
            else {
                show_error('#import-shebanq-error', result.error);
            }
        })
            .fail(function (jqXHR, textStatus, errorThrown) {
            $('.ui-dialog *').css('cursor', 'auto');
            show_error('#import-shebanq-error', localize('error_response') + " " + errorThrown);
        });
    });
    $('#import-shebanq-dialog').modal('show');
}
function numberInputModifiedListener(e) {
    var s = $(e.currentTarget).val();
    $('#' + e.data.err_id).html('');
    if (s.length !== 0 && s.match(/\D/g) !== null)
        $('#' + e.data.err_id).html(localize('not_integer'));
}
setTimeout(function () {
    for (var i in configuration.sentencegrammar) {
        if (isNaN(+i))
            continue;
        addMethodsSgi(configuration.sentencegrammar[+i], configuration.sentencegrammar[+i].objType);
    }
    $(window).on('beforeunload', function () {
        if (isDirty())
            return 'You haven\'t saved your changes.';
    });
    charset = new Charset(configuration.charSet);
    if (VirtualKeyboard) {
        VirtualKeyboard.setVisibleLayoutCodes([charset.keyboardName]);
        VirtualKeyboard.toggle('firstinput', 'virtualkbid');
    }
    ckeditor = $('#txtdesc').ckeditor({
        uiColor: '#feeebd',
        toolbarGroups: [
            { name: 'clipboard', groups: ['clipboard', 'undo'] },
            { name: 'editing', groups: ['find', 'selection'] },
            { name: 'links' },
            { name: 'insert' },
            { name: 'document', groups: ['mode'] },
            '/',
            { name: 'basicstyles', groups: ['basicstyles', 'cleanup'] },
            { name: 'paragraph', groups: ['list', 'indent', 'blocks', 'align', 'bidi'] },
            { name: 'styles' },
            { name: 'colors' },
            { name: 'about' }
        ],
        removeButtons: 'Print,Preview,NewPage,Save,Flash,PageBreak,Iframe,CreateDiv,language',
        removeDialogTabs: 'image:advanced;link:advanced'
    });
    ckeditor.val(decoded_3et.desc);
    $('#quiz_tabs').tabs({ disabled: [3] });
    origMayLocate = decoded_3et.maylocate;
    $('#maylocate_cb').prop('checked', origMayLocate);
    origSentBefore = decoded_3et.sentbefore;
    $('#sentbefore').val(origSentBefore);
    origSentAfter = decoded_3et.sentafter;
    $('#sentafter').val(origSentAfter);
    origFixedQuestions = decoded_3et.fixedquestions;
    $('#fixedquestions').val(origFixedQuestions);
    $('#fixedquestions').on('keyup', null, { err_id: "fqerror" }, numberInputModifiedListener);
    panelFeatures = new PanelTemplQuizFeatures(decoded_3et.quizObjectSelection.object, decoded_3et.quizFeatures, $('#tab_features'));
    panelSentUnit = new PanelTemplQuizObjectSelector(decoded_3et.quizObjectSelection, $('#tab_sentence_units'), panelFeatures);
    panelSent = new PanelTemplSentenceSelector(decoded_3et.sentenceSelection, $('#quiz_tabs'), $('#tab_sentences'), panelSentUnit, panelFeatures);
    $('.quizeditor').show();
}, 1000);
