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
    var io = feature.indexOf('_TYPE_'); // Separates feature from format
    if (io != -1)
        // This is a feature with a special format (which is not used here)
        return getObjectSetting(otype).featuresetting[feature.substr(0, io)];
    else
        return getObjectSetting(otype).featuresetting[feature];
}
// -*- js -*-
/* 2013 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk */
/// @file
/// @brief Characteristics of the current character set
var Charset = /** @class */ (function () {
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
/* 2013 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk */
var WHAT;
(function (WHAT) {
    WHAT[WHAT["feature"] = 0] = "feature";
    WHAT[WHAT["metafeature"] = 1] = "metafeature";
    WHAT[WHAT["groupstart"] = 2] = "groupstart";
    WHAT[WHAT["groupend"] = 3] = "groupend";
})(WHAT || (WHAT = {}));
var GrammarGroup = /** @class */ (function () {
    function GrammarGroup() {
    }
    GrammarGroup.prototype.getFeatName = function (objType, callback) {
        callback(WHAT.groupstart, objType, objType, this.name, l10n.grammargroup[objType][this.name], this);
        for (var i in this.items) {
            if (isNaN(+i))
                continue; // Not numeric
            this.items[+i].getFeatName(objType, callback);
        }
        callback(WHAT.groupend, objType, objType, this.name, null, this);
    };
    GrammarGroup.prototype.getFeatVal = function (monob, mix, objType, abbrev, callback) {
        callback(WHAT.groupstart, objType, objType, this.name, null, this);
        for (var i in this.items) {
            if (isNaN(+i))
                continue; // Not numeric
            this.items[+i].getFeatVal(monob, mix, objType, abbrev, callback);
        }
        callback(WHAT.groupend, objType, objType, this.name, null, this);
    };
    /** Do the children of this object identify the specified feature?
     * @param f The name of the feature to look for.
     * @return True if the specified feature matches this object.
     */
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
var GrammarSubFeature = /** @class */ (function () {
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
}());
var SentenceGrammar = /** @class */ (function (_super) {
    __extends(SentenceGrammar, _super);
    function SentenceGrammar() {
        return _super !== null && _super.apply(this, arguments) || this;
    }
    SentenceGrammar.prototype.getFeatName = function (objType, callback) {
        for (var i in this.items) {
            if (isNaN(+i))
                continue; // Not numeric
            this.items[+i].getFeatName(objType, callback);
        }
    };
    SentenceGrammar.prototype.getFeatVal = function (monob, mix, objType, abbrev, callback) {
        for (var i in this.items) {
            if (isNaN(+i))
                continue; // Not numeric
            this.items[+i].getFeatVal(monob, mix, objType, abbrev, callback);
        }
    };
    /** Do the children of this object identify the specified feature?
     * @param f The name of the feature to look for.
     * @return True if the specified feature matches this object.
     */
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
var GrammarMetaFeature = /** @class */ (function () {
    function GrammarMetaFeature() {
    }
    GrammarMetaFeature.prototype.getFeatName = function (objType, callback) {
        callback(WHAT.metafeature, objType, objType, this.name, l10n.grammarmetafeature[objType][this.name], this);
    };
    GrammarMetaFeature.prototype.getFeatVal = function (monob, mix, objType, abbrev, callback) {
        var res = '';
        for (var i in this.items) {
            if (isNaN(+i))
                continue; // Not numeric
            res += this.items[+i].getFeatValPart(monob, objType);
        }
        callback(WHAT.metafeature, objType, objType, this.name, res, this);
    };
    /** Do the children of this object identify the specified feature?
     * @param f The name of the feature to look for.
     * @return True if the specified feature matches this object.
     */
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
var GrammarFeature = /** @class */ (function () {
    function GrammarFeature() {
    }
    GrammarFeature.prototype.pseudoConstructor = function (objType) {
        var io = this.name.indexOf(':');
        if (io != -1) {
            // This is a feature of a subobject
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
    GrammarFeature.prototype.getFeatName = function (objType, callback) {
        var locname = l10n.grammarfeature && l10n.grammarfeature[this.realObjectType] && l10n.grammarfeature[this.realObjectType][this.realFeatureName]
            ? l10n.grammarfeature[this.realObjectType][this.realFeatureName]
            : l10n.emdrosobject[this.realObjectType][this.realFeatureName];
        callback(WHAT.feature, this.realObjectType, objType, this.realFeatureName, locname, this);
    };
    GrammarFeature.prototype.icon2class = function (icon) {
        if (icon.substr(0, 10) === 'glyphicon-')
            return 'glyphicon ' + icon;
        if (icon.substr(0, 8) === 'bolicon-')
            return 'bolicon ' + icon;
        return '';
    };
    GrammarFeature.prototype.getFeatVal = function (monob, mix, objType, abbrev, callback) {
        var featType = typeinfo.obj2feat[this.realObjectType][this.realFeatureName];
        var io = this.realFeatureName.indexOf('_TYPE_'); // Separates feature from format
        var res = io == -1 // Test if the feature name contains _TYPE_
            ? (this.isSubObj
                ? monob.subobjects[mix][0].features[this.realFeatureName]
                : (monob.mo.features ? monob.mo.features[this.realFeatureName] : '')) // Empty for dummy objects
            : (this.isSubObj
                ? monob.subobjects[mix][0].features[this.realFeatureName.substr(0, io)]
                : (monob.mo.features ? monob.mo.features[this.realFeatureName.substr(0, io)] : ''));
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
                        res2 += '<a style="padding-right:1px;padding-left:1px;" href="{0}" target="_blank"><span class="{1}" aria-hidden="true"></span></a>'
                            .format(res[i]['url'], this.icon2class(res[i]['icon']));
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
    /** Does this object identify the specified feature?
     * @param f The name of the feature to look for.
     * @return True if the specified feature matches this object.
     */
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
// This function copies all fields from the source to the destination
function copyFields(dst, src) {
    for (var f in src)
        dst[f] = src[f];
}
// This function adds relevant methods to a data object of the specified class
function addMethods(obj, classname, objType) {
    // Copy all methods except constructor
    for (var f in classname.prototype) {
        if (f === 'constructor')
            continue;
        obj[f] = classname.prototype[f];
    }
    obj.pseudoConstructor && obj.pseudoConstructor(objType); // Call pseudoConstructor if it exiss
}
// This function adds relevant methods to a data object of a specific SentenceGrammarItem subtype
function addMethodsSgi(sgi, objType) {
    addMethods(sgi, eval(sgi.mytype), objType); // sgi.mytype is the name of the subclass, generated by the server
    // Do the same with all members of the items array
    if (sgi.items) {
        for (var i in sgi.items) {
            if (isNaN(+i))
                continue; // Not numeric
            addMethodsSgi(sgi.items[+i], objType);
        }
    }
}
// -*- js -*-
/* 2013 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk */
function getObjectFriendlyName(otype) {
    if (otype === 'Patriarch') // Shouldn't happen
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
        // TODO: We assume there is no "list of " types here
        return doStripSort
            ? StringWithSort.stripSortIndex(l10n.emdrostype[featureType + '_abbrev'][value])
            : l10n.emdrostype[featureType + '_abbrev'][value];
    // TODO: For now we handle "list of ..." here. Is this OK with all the other locations where this is used?
    if (featureType.substr(0, 8) === 'list of ') {
        featureType = featureType.substr(8); // Remove "list of "
        value = value.substr(1, value.length - 2); // Remove parenteses
        if (value.length == 0)
            return doStripSort
                ? StringWithSort.stripSortIndex(l10n.emdrostype[featureType]['NA'])
                : l10n.emdrostype[featureType]['NA'];
        var verb_classes = value.split(',');
        var localized_verb_classes = [];
        for (var ix in verb_classes) {
            if (isNaN(+ix))
                continue; // Not numeric
            localized_verb_classes.push(doStripSort
                ? StringWithSort.stripSortIndex(l10n.emdrostype[featureType][verb_classes[ix]])
                : l10n.emdrostype[featureType][verb_classes[ix]]);
        }
        localized_verb_classes.sort();
        return localized_verb_classes.join(', ');
    }
    // TODO Distinguish between friendly name A and S (Westminster)
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
// -*- js -*-
/* 2013 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk */
function localize(s) {
    var str = l10n_js[s];
    return str === undefined ? '??' + s + '??' : str;
}
var FeatureHandler = /** @class */ (function () {
    function FeatureHandler(typ, key) {
        this.type = typ;
        this.name = key;
        this.comparator = 'equals';
    }
    FeatureHandler.prototype.normalize = function () {
        // Nothing
    };
    FeatureHandler.prototype.hasValues = function () {
        alert('Abstract function hasValues() called');
        return false;
    };
    FeatureHandler.prototype.toMql = function () {
        alert('Abstract function toMql() called');
        return '';
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
var StringFeatureHandler = /** @class */ (function (_super) {
    __extends(StringFeatureHandler, _super);
    function StringFeatureHandler(key) {
        var _this = _super.call(this, 'stringfeature', key) || this;
        _this.values = [];
        _this.normalize();
        return _this;
    }
    StringFeatureHandler.prototype.normalize = function () {
        while (this.values.length < 4)
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
var IntegerFeatureHandler = /** @class */ (function (_super) {
    __extends(IntegerFeatureHandler, _super);
    function IntegerFeatureHandler(key) {
        var _this = _super.call(this, 'integerfeature', key) || this;
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
var RangeIntegerFeatureHandler = /** @class */ (function (_super) {
    __extends(RangeIntegerFeatureHandler, _super);
    function RangeIntegerFeatureHandler(key) {
        return _super.call(this, 'rangeintegerfeature', key) || this;
    }
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
var EnumFeatureHandler = /** @class */ (function (_super) {
    __extends(EnumFeatureHandler, _super);
    function EnumFeatureHandler(key) {
        var _this = _super.call(this, 'enumfeature', key) || this;
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
var EnumListFeatureHandler = /** @class */ (function (_super) {
    __extends(EnumListFeatureHandler, _super);
    function EnumListFeatureHandler(key) {
        var _this = _super.call(this, 'enumlistfeature', key) || this;
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
var QereFeatureHandler = /** @class */ (function (_super) {
    __extends(QereFeatureHandler, _super);
    function QereFeatureHandler(key) {
        var _this = _super.call(this, 'qerefeature', key) || this;
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
var ListValuesHandler = /** @class */ (function () {
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
            stringValues.push('{0} HAS {1}'.format(featName, this.yes_values[+ix]));
        for (var ix = 0; ix < this.no_values.length; ++ix)
            stringValues.push('NOT {0} HAS {1}'.format(featName, this.no_values[+ix]));
        if (stringValues.length === 1)
            return stringValues[0];
        return '(' + stringValues.join(' AND ') + ')';
    };
    return ListValuesHandler;
}());
var PanelTemplMql = /** @class */ (function () {
    function PanelTemplMql(md, name_prefix) {
        var _this = this;
        this.featureCombo = $('<select></select>');
        this.objectTypeCombo = $('<select></select>');
        this.initialMd = md;
        this.name_prefix = name_prefix;
        this.fpan = $('<div id="{0}_fpan"></div>'.format(this.name_prefix));
        if (md.featHand) {
            for (var i = 0; i < md.featHand.vhand.length; ++i) {
                var vh = md.featHand.vhand[i];
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
                // Turn vhand into an associative array:
                md.featHand.vhand[vh.name] = vh;
            }
        }
        this.rbMql = $('<input type="radio" name="{0}_usemql" value="yes">'.format(this.name_prefix));
        this.rbMql.click(function () {
            if (_this.rbMql.is(':checked'))
                _this.switchToMql(true);
        });
        this.rbFriendly = $('<input type="radio" name="{0}_usemql" value="no">'.format(this.name_prefix));
        this.rbFriendly.click(function () {
            if (_this.rbFriendly.is(':checked')) {
                _this.switchToMql(false);
                _this.updateMql();
            }
        });
        this.rbMqlLabel = $('<span>YOU SHOULD NOT SEE THIS</span>');
        this.rbFriendlyLabel = $('<span>YOU SHOULD NOT SEE THIS</span>');
        this.mqlText = $('<textarea id="{0}_mqltext" cols="45" rows="2">'.format(this.name_prefix));
        this.featureCombo.on('change', function () {
            _this.currentBox.hide();
            _this.currentBox = _this.groups[_this.featureCombo.val()];
            _this.currentBox.show();
        });
        var setObject = (md != null && md.object != null) ? md.object : configuration.objHasSurface;
        for (var s in configuration.objectSettings) {
            if (configuration.objectSettings[s].mayselect) {
                var selectString = s === setObject ? 'selected="selected"' : '';
                var resetString = s === configuration.objHasSurface ? 'data-reset="yes"' : '';
                this.objectTypeCombo.append('<option value="{0}" {1} {2}>{3}</option>'
                    .format(s, selectString, resetString, getObjectFriendlyName(s)));
            }
        }
        this.objectTypeCombo.on('change', function () {
            $('#virtualkbid').appendTo('#virtualkbcontainer'); // Move the keyboard back to its initial position
            _this.fpan.html('<div id="{0}_fpan"></div>'.format(_this.name_prefix));
            _this.currentBox = null;
            _this.featureCombo.html('<select></select>');
            _this.objectSelectionUpdated(_this.objectTypeCombo.val(), null);
            _this.updateMql();
        });
        this.clear = $('<button id="clear_button" type="button">' + localize('clear_button') + '</button>');
        //this.clear.button(); Don't use this. The JQuery UI theme works for buttons but not dropdowns
        this.clear.click(function () {
            $('#virtualkbid').appendTo('#virtualkbcontainer'); // Move the keyboard back to its initial position
            _this.rbFriendly.prop('checked', true);
            _this.objectTypeCombo.find(':selected').prop('selected', false);
            _this.objectTypeCombo.find('[data-reset]').prop('selected', true);
            _this.fpan.html('<div id="{0}_fpan"></div>'.format(_this.name_prefix));
            _this.currentBox = null;
            _this.featureCombo.html('<select></select>');
            _this.objectSelectionUpdated(configuration.objHasSurface, null);
            _this.updateMql();
            _this.switchToMql(false);
        });
    }
    PanelTemplMql.prototype.stringTextModifiedListener = function (e) {
        var s = $(e.currentTarget).val();
        if (s.length === 0)
            e.data.sfh.removeValue(e.data.i);
        else
            e.data.sfh.setValue(e.data.i, s);
        this.updateMql();
    };
    PanelTemplMql.prototype.integerTextModifiedListener = function (e) {
        var s = $(e.currentTarget).val();
        $('#' + e.data.err_id).html('');
        if (s.length === 0)
            e.data.ifh.removeValue(e.data.i);
        else {
            if (s.match(/\D/g) !== null) // Note: Rejects minus sign
                $('#' + e.data.err_id).html(localize('not_integer'));
            else
                e.data.ifh.setValue(e.data.i, +s);
        }
        this.updateMql();
    };
    PanelTemplMql.prototype.rangeIntegerTextModifiedListener = function (e) {
        var s = $(e.currentTarget).val();
        $('#' + e.data.err_id).html('');
        if (s.length === 0)
            e.data.rfh[e.data.i] = null;
        else {
            if (s.match(/\D/g) !== null) // Note: Rejects minus sign
                $('#' + e.data.err_id).html(localize('not_integer'));
            else
                e.data.rfh[e.data.i] = +s;
        }
        this.updateMql();
    };
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
                this.objectSelectionUpdated(this.initialMd.object, this.initialMd.featHand.vhand);
            else
                this.objectSelectionUpdated(this.initialMd.object, null);
            this.switchToMql(true);
        }
        else {
            this.rbFriendly.prop('checked', true);
            this.objectSelectionUpdated(this.initialMd.object, this.initialMd.featHand.vhand);
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
        if (this.lastMonitored !== elem.attr('id')) { // A new element has focus
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
    /**
     * Creates the various feature selection panels associated with a newly selected quiz object type.
     * @param otype
     * @param fhs
     */
    PanelTemplMql.prototype.objectSelectionUpdated = function (otype, fhs) {
        var _this = this;
        this.handlers = [];
        this.groups = [];
        // Create selection boxes for all features of this object type
        for (var key in getObjectSetting(otype).featuresetting) {
            var valueType = typeinfo.obj2feat[otype][key];
            var featset = getFeatureSetting(otype, key);
            // Ignore specified features plus features of type id_d
            if (featset.ignoreSelect)
                continue;
            var group = $('<div></div>');
            this.groups[key] = group;
            var selectString;
            if (featset.isDefault) {
                group.show();
                this.currentBox = group;
                selectString = 'selected="selected"';
            }
            else {
                group.hide();
                selectString = '';
            }
            this.featureCombo.append('<option value="{0}" {1}>{2}</option>'
                .format(key, selectString, getFeatureFriendlyName(otype, key)));
            if (valueType === 'integer') {
                if (featset.isRange) {
                    var rfh = null;
                    if (fhs)
                        rfh = fhs[key];
                    if (!rfh)
                        rfh = new RangeIntegerFeatureHandler(key);
                    var group2 = $('<table></table>');
                    var rowLow = $('<tr></tr>');
                    var rowHigh = $('<tr></tr>');
                    var cellLab;
                    var cellInput;
                    var cellErr;
                    var jtf = $('<input type="text" size="8">');
                    if (rfh.isSetLow())
                        jtf.val(String(rfh.value_low));
                    var err_id = 'err_{0}_low'.format(key, i);
                    jtf.on('keyup', null, { rfh: rfh, i: 'value_low', err_id: err_id }, $.proxy(this.rangeIntegerTextModifiedListener, this));
                    cellLab = $('<td>' + localize('low_value_prompt') + '</td>');
                    cellInput = $('<td></td>');
                    cellInput.append(jtf);
                    cellErr = $('<td id="{0}"></td>'.format(err_id));
                    rowLow.append(cellLab, cellInput, cellErr);
                    jtf = $('<input type="text" size="8">');
                    if (rfh.isSetHigh())
                        jtf.val(String(rfh.value_high));
                    err_id = 'err_{0}_high'.format(key, i);
                    jtf.on('keyup', null, { rfh: rfh, i: 'value_high', err_id: err_id }, $.proxy(this.rangeIntegerTextModifiedListener, this));
                    cellLab = $('<td>' + localize('high_value_prompt') + '</td>');
                    cellInput = $('<td></td>');
                    cellInput.append(jtf);
                    cellErr = $('<td id="{0}"></td>'.format(err_id));
                    rowHigh.append(cellLab, cellInput, cellErr);
                    group2.append(rowLow, rowHigh);
                    group.append(group2);
                    this.handlers.push(rfh);
                }
                else {
                    var ifh = null;
                    if (fhs)
                        ifh = fhs[key];
                    if (!ifh)
                        ifh = new IntegerFeatureHandler(key);
                    var butEquals = $('<input type="radio" name="{0}_{1}_comp" value="equals">'
                        .format(this.name_prefix, key));
                    var butDiffers = $('<input type="radio" name="{0}_{1}_comp" value="differs">'
                        .format(this.name_prefix, key));
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
                    group.append(sel);
                    sel.click(ifh, function (e) {
                        // val() may return an empty value if the user clicks on, say, the = sign
                        var v = $(e.target).val();
                        switch (v) {
                            case 'equals':
                            case 'differs':
                                e.data.comparator = v; // e.data is ifh
                                _this.updateMql();
                                break;
                        }
                    });
                    var group2 = $('<table></table>');
                    for (var i = 0; i < ifh.values.length; ++i) {
                        var jtf = $('<input type="text" size="8">');
                        if (ifh.values[i])
                            jtf.val(String(ifh.values[i]));
                        var err_id = 'err_{0}_{1}'.format(key, i);
                        jtf.on('keyup', null, { ifh: ifh, i: i, err_id: err_id }, $.proxy(this.integerTextModifiedListener, this));
                        var row = $('<tr></tr>');
                        var cell = $('<td></td>');
                        cell.append(jtf);
                        row.append(cell);
                        row.append('<td id="{0}"></td>'.format(err_id));
                        group2.append(row);
                    }
                    group.append(group2);
                    this.handlers.push(ifh);
                }
            }
            else if (valueType === 'ascii' || valueType === 'string') {
                if (key === 'qere_utf8' || key === 'qere_translit') {
                    // Special handling of qere feature in ETCBC4
                    var qfh = null;
                    if (fhs)
                        qfh = fhs[key];
                    if (!qfh)
                        qfh = new QereFeatureHandler(key);
                    var butOmitqere = $('<input type="checkbox" name="{0}_{1}_sel" value="omit">'
                        .format(this.name_prefix, key));
                    if (qfh.omit)
                        butOmitqere.prop('checked', true);
                    var sel = $('<span></span>');
                    sel.append(butOmitqere, localize('omit_qere'));
                    group.append(sel);
                    sel.click(qfh, function (e) {
                        var target = $(e.target);
                        e.data.setValue(target.prop('checked')); // e.data is qfh
                        _this.updateMql();
                    });
                    this.handlers.push(qfh);
                }
                else {
                    var sfh = null;
                    if (fhs)
                        sfh = fhs[key];
                    if (!sfh)
                        sfh = new StringFeatureHandler(key);
                    var butEquals = $('<input type="radio" name="{0}_{1}_comp" value="equals">'
                        .format(this.name_prefix, key));
                    var butDiffers = $('<input type="radio" name="{0}_{1}_comp" value="differs">'
                        .format(this.name_prefix, key));
                    var butMatches = $('<input type="radio" name="{0}_{1}_comp" value="matches">'
                        .format(this.name_prefix, key));
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
                    group.append(sel);
                    sel.click(sfh, function (e) {
                        // val() may return an empty value if the user clicks on, say, the = sign
                        var v = $(e.target).val();
                        switch (v) {
                            case 'equals':
                            case 'differs':
                            case 'matches':
                                e.data.comparator = v; // e.data is sfh
                                _this.updateMql();
                                break;
                        }
                    });
                    var group2 = $('<table></table>');
                    if (featset.foreignText) {
                        for (var i = 0; i < sfh.values.length; ++i) {
                            var kbdRowId = '{0}_{1}_row{2}'.format(this.name_prefix, key, +i + 1);
                            var jtf = $('<input class="{0}" type="text" size="20" id="{1}_{2}_input{3}">'.format(charset.foreignClass, this.name_prefix, key, +i + 1));
                            if (sfh.values[i])
                                jtf.val(sfh.values[i]);
                            jtf.on('focus', null, { kbdRowId: kbdRowId, sfh: sfh, i: i }, function (e) {
                                $('#virtualkbid').appendTo('#' + e.data.kbdRowId);
                                VirtualKeyboard.attachInput(e.currentTarget);
                                _this.monitorChange($(e.currentTarget), e.data.sfh, e.data.i);
                            });
                            jtf.on('keyup', null, { sfh: sfh, i: i }, $.proxy(this.stringTextModifiedListener, this));
                            var row = $('<tr></tr>');
                            var cell = $('<td></td>');
                            cell.append(jtf);
                            row.append(cell);
                            group2.append(row);
                            group2.append('<tr><td id="{0}" style="text-align:right;"></td></tr>'.format(kbdRowId));
                        }
                    }
                    else {
                        for (var i = 0; i < sfh.values.length; ++i) {
                            var jtf = $('<input type="text" size="20">'); // VerifiedField
                            if (sfh.values[i])
                                jtf.val(sfh.values[i]);
                            jtf.on('keyup', null, { sfh: sfh, i: i }, $.proxy(this.stringTextModifiedListener, this));
                            var row = $('<tr></tr>');
                            var cell = $('<td></td>');
                            cell.append(jtf);
                            row.append(cell);
                            group2.append(row);
                        }
                    }
                    group.append(group2);
                    this.handlers.push(sfh);
                }
            }
            else if (valueType.substr(0, 8) === 'list of ') {
                var stripped_valueType = valueType.substr(8);
                var enumValues = typeinfo.enum2values[stripped_valueType];
                if (!enumValues) {
                    // We cannot handle lists of non-enums
                    console.log('Unknown valueType', valueType);
                }
                var elfh = null;
                if (fhs)
                    elfh = fhs[key];
                if (!elfh)
                    elfh = new EnumListFeatureHandler(key);
                var group_tabs = $('<div id="list_tabs_{0}"></div>'.format(key));
                var group_ul = $('<ul></ul>');
                group_tabs.append(group_ul);
                var tab_labels = [localize('1st_choice'),
                    localize('2nd_choice'),
                    localize('3rd_choice'),
                    localize('4th_choice')];
                for (var tabno = 0; tabno < 4; ++tabno) {
                    var lv = elfh.listvalues[tabno];
                    group_ul.append('<li><a href="#tab_{0}_{1}">{2}</li>'.format(key, tabno, tab_labels[tabno]));
                    var tab_contents = $('<div id="tab_{0}_{1}"></div>'.format(key, tabno));
                    var vc_choice = new PanelForOneVcChoice(enumValues, stripped_valueType, '{0}_{1}_{2}_{3}'.format(this.name_prefix, otype, key, tabno), lv);
                    tab_contents.append(vc_choice.getPanel());
                    group_tabs.append(tab_contents);
                    tab_contents.click(lv, function (e) {
                        var target = $(e.target);
                        if (target.attr('type') === 'radio') {
                            e.data.modifyValue(target.attr('data-name'), target.attr('value')); // e.data is lv
                            _this.updateMql();
                        }
                    });
                }
                group.append(group_tabs);
                group.tabs();
                this.handlers.push(elfh);
            }
            else { // valueType is an enum
                var enumValues = typeinfo.enum2values[valueType];
                if (!enumValues) {
                    console.log('Unknown valueType', valueType);
                }
                else {
                    var efh = null;
                    if (fhs)
                        efh = fhs[key];
                    if (!efh)
                        efh = new EnumFeatureHandler(key);
                    var butEquals = $('<input type="radio" name="{0}_{1}_comp" value="equals">'
                        .format(this.name_prefix, key));
                    var butDiffers = $('<input type="radio" name="{0}_{1}_comp" value="differs">'
                        .format(this.name_prefix, key));
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
                    group.append(sel);
                    sel.click(efh, function (e) {
                        // val() may return an empty value if the user clicks on, say, the = sign
                        var v = $(e.target).val();
                        switch (v) {
                            case 'equals':
                            case 'differs':
                                e.data.comparator = v; // e.data is efh
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
                        if (!efh.values)
                            alert('Assert efh.values failed for type ' + key);
                        scb.setSelected(efh.values && efh.values.indexOf(s) !== -1);
                        checkBoxes.push(scb);
                    }
                    checkBoxes.sort(function (a, b) { return StringWithSort.compare(a.getSws(), b.getSws()); });
                    // Decide how many columns and rows to use for feature values
                    var columns = checkBoxes.length > 12 ? 3 :
                        checkBoxes.length > 4 ? 2 : 1;
                    var rows = Math.ceil(checkBoxes.length / columns);
                    var group2 = $('<table></table>');
                    for (var r = 0; r < rows; ++r) {
                        var rw = $('<tr></tr>');
                        for (var c = 0; c < columns; ++c) {
                            var cell = $('<td></td>');
                            if (c * rows + r < checkBoxes.length)
                                cell.append(checkBoxes[c * rows + r].getJQuery());
                            rw.append(cell);
                        }
                        group2.append(rw);
                    }
                    group2.click(efh, function (e) {
                        var target = $(e.target);
                        if (target.attr('type') === 'checkbox') {
                            // The user clicked on a checkbox
                            if (target.prop('checked'))
                                e.data.addValue(target.attr('value')); // e.data is efh
                            else
                                e.data.removeValue(target.attr('value'));
                            _this.updateMql();
                        }
                    });
                    group.append(group2);
                    this.handlers.push(efh);
                }
            }
            this.fpan.append(group);
        }
        this.populateFeatureTab(otype);
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
    // Default value. Overidden in PanelTemplSentenceSelector
    PanelTemplMql.prototype.getUseForQo = function () {
        return false;
    };
    PanelTemplMql.prototype.isDirty = function () {
        return this.getMql() !== this.txtEntry;
    };
    PanelTemplMql.prototype.makeMql = function () {
        if (this.handlers) {
            var sb = '';
            var first = true;
            for (var i = 0; i < this.handlers.length; ++i) {
                var fh = this.handlers[i];
                if (fh.hasValues()) {
                    if (first)
                        first = false;
                    else
                        sb += ' AND ';
                    sb += fh.toMql();
                }
            }
            return sb;
        }
        else
            return '';
    };
    PanelTemplMql.prototype.switchToMql = function (useMql) {
        alert('Abstract function switchToMql() called');
    };
    PanelTemplMql.prototype.updateMql = function () {
        this.setMql(this.makeMql());
    };
    PanelTemplMql.prototype.populateFeatureTab = function (otype) {
        alert('Abstract function populateFeatureTab() called');
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
// -*- js -*-
/* Copyright 2013 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk */
var PanelTemplSentenceSelector = /** @class */ (function (_super) {
    __extends(PanelTemplSentenceSelector, _super);
    /**
     * Constructor.
     */
    function PanelTemplSentenceSelector(md, ttabs, where, qoselTab, featureTab) {
        var _this = _super.call(this, md, 'sensel') || this;
        _this.questObjTypeLab = $('<span>' + localize('sentence_unit_type_prompt') + '</span>');
        _this.featSelLab = $('<span>' + localize('feature_prompt') + '</span>');
        _this.importShebanq = $('<button type="button">' + localize('import_shebanq') + '</button>');
        _this.templTabs = ttabs;
        _this.dirty = false;
        _this.featureTab = featureTab;
        _this.qoselTab = qoselTab;
        _this.cbUseForQo = $('<input type="checkbox" name="useforqol">');
        _this.cbUseForQoLabel = $('<span>' + localize('use_for_qosel') + '</span>');
        _this.cbUseForQo.click(function () {
            if (_this.cbUseForQo.is(':checked'))
                _this.templTabs.tabs('disable', 3);
            else
                _this.templTabs.tabs('enable', 3);
            _this.populateFeatureTab(null);
            _this.dirty = true;
        });
        _this.rbMqlLabel = $('<span>' + localize('mql_qosel_prompt') + '</span>');
        _this.rbFriendlyLabel = $('<span>' + localize('friendly_featsel_prompt') + '</span>');
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
        return '[' + this.getOtype() + ' NORETRIEVE ' + _super.prototype.makeMql.call(this) + ']';
    };
    PanelTemplSentenceSelector.prototype.getMqlEmulQos = function () {
        return _super.prototype.makeMql.call(this);
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
// -*- js -*-
/* Copyright 2013 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk */
var PanelTemplQuizObjectSelector = /** @class */ (function (_super) {
    __extends(PanelTemplQuizObjectSelector, _super);
    /**
     * Constructor.
     */
    function PanelTemplQuizObjectSelector(md, where, featureTab) {
        var _this = _super.call(this, md, 'qosel') || this;
        _this.featSelLab = $('<span>' + localize('feature_prompt') + '</span>');
        _this.featureTab = featureTab;
        _this.rbMqlLabel = $('<span>' + localize('mql_featsel_prompt') + '</span>');
        _this.rbFriendlyLabel = $('<span>' + localize('friendly_featsel_prompt') + '</span>');
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
        cell = $('<td>' + localize('sentence_unit_type_prompt') + '</td>');
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
    PanelTemplQuizObjectSelector.prototype.populateFeatureTab = function (otype) {
        if (otype === null)
            otype = this.getOtype();
        this.featureTab.populate(otype);
    };
    return PanelTemplQuizObjectSelector;
}(PanelTemplMql));
// -*- js -*-
/* Copyright 2013 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk */
// Hand coded database dependency for qere detection
function database_has_qere() {
    return configuration.databaseName === "ETCBC4";
}
function otype_has_qere(otype) {
    return otype === "word";
}
function qere_otype() {
    return "word";
}
function qere_feature() {
    if (configuration.propertiesName === "ETCBC4")
        return "qere_utf8";
    if (configuration.propertiesName === "ETCBC4-translit")
        return "qere_translit";
    return null;
}
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
var ButtonsAndLabel = /** @class */ (function () {
    function ButtonsAndLabel(lab, featName, otype, select, useDropDown, canShow, canRequest, canDisplayGrammar, canShowQere) {
        var _this = this;
        this.featName = featName;
        this.useDropDown = useDropDown;
        this.canShow = canShow;
        this.canRequest = canRequest;
        this.canDisplayGrammar = canDisplayGrammar;
        this.canShowQere = canShowQere;
        this.showFeat = canShow ? $('<input type="radio" name="feat_{0}_{1}" value="show">'.format(otype, featName)) : $('<span></span>');
        this.reqFeat = canRequest ? $('<input type="radio" name="feat_{0}_{1}" value="request">'.format(otype, featName)) : $('<span></span>');
        this.dcFeat = $('<input type="radio" name="feat_{0}_{1}" value="dontcare">'.format(otype, featName));
        this.dontShowFeat = canDisplayGrammar ? $('<input type="radio" name="feat_{0}_{1}" value="dontshowfeat">'.format(otype, featName)) : $('<span></span>');
        this.showQere = canShowQere ? $('<input type="radio" name="feat_{0}_{1}" value="showqere">'.format(otype, featName)) : $('<span></span>');
        this.feat = $('<span>{0}</span>'.format(lab));
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
            this.ddCheck = $('<input type="checkbox" name="dd_{0}_{1}">'.format(otype, featName));
            this.ddCheck.prop('checked', select != ButtonSelection.REQUEST);
        }
        else if (canShowQere) {
            this.ddCheck = this.showQere; // Drop down and showQere share a position
        }
        else
            this.ddCheck = $('<span></span>'); // Empty space filler
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
    }
    ButtonsAndLabel.prototype.getRow = function () {
        var row = $('<tr></tr>');
        var cell;
        cell = $('<td></td>');
        cell.append(this.showFeat);
        row.append(cell);
        cell = $('<td></td>');
        cell.append(this.reqFeat);
        row.append(cell);
        cell = $('<td></td>');
        cell.append(this.dcFeat);
        row.append(cell);
        cell = $('<td></td>');
        cell.append(this.dontShowFeat);
        row.append(cell);
        cell = $('<td></td>');
        cell.append(this.ddCheck);
        row.append(cell);
        cell = $('<td class="leftalign"></td>');
        cell.append(this.feat);
        row.append(cell);
        return row;
    };
    ButtonsAndLabel.prototype.isSelected_showFeat = function () {
        if (this.canShow)
            return this.showFeat.prop('checked');
        else
            return false;
    };
    ButtonsAndLabel.prototype.isSelected_reqFeat = function () {
        if (this.canRequest)
            return this.reqFeat.prop('checked');
        else
            return false;
    };
    ButtonsAndLabel.prototype.isSelected_dontShowFeat = function () {
        if (this.canDisplayGrammar)
            return this.dontShowFeat.prop('checked');
        else
            return false;
    };
    ButtonsAndLabel.prototype.isSelected_showQere = function () {
        if (this.canShowQere)
            return this.showQere.prop('checked');
        else
            return false;
    };
    ButtonsAndLabel.prototype.isSelected_ddCheck = function () {
        if (this.useDropDown)
            return this.ddCheck.prop('checked');
        else
            return false;
    };
    ButtonsAndLabel.prototype.getFeatName = function () {
        return this.featName;
    };
    return ButtonsAndLabel;
}());
var PanelForOneOtype = /** @class */ (function () {
    function PanelForOneOtype(otype, ptqf) {
        this.allBAL = [];
        this.panel = $('<table class="striped featuretable"></table>');
        var useSavedFeatures = otype === ptqf.initialOtype;
        this.panel.append('<tr><th>{0}</th><th>{1}</th><th>{2}</th><th>{3}</th><th>{4}</th><th class="leftalign">{5}</th></tr>'
            .format(localize('show'), localize('request'), localize('dont_care'), localize('dont_show'), localize('multiple_choice'), localize('feature')));
        // First set up "visual" pseudo feature
        this.visualBAL = new ButtonsAndLabel(localize('visual'), 'visual', otype, useSavedFeatures ? ptqf.getSelector('visual') : ButtonSelection.DONT_CARE, configuration.objHasSurface === otype && !!getFeatureSetting(otype, configuration.surfaceFeature).alternateshowrequestSql, true, configuration.objHasSurface === otype, false, false);
        this.panel.append(this.visualBAL.getRow());
        // Now handle genuine features
        var hasSurfaceFeature = otype === configuration.objHasSurface;
        var sg = getSentenceGrammarFor(otype);
        var keylist = []; // Will hold sorted list of keys
        for (var key in getObjectSetting(otype).featuresetting) {
            // Ignore specified features
            if (getFeatureSetting(otype, key).ignoreShowRequest && (sg === null || !sg.containsFeature(key)))
                continue;
            // Ignore features of type 'url'
            if (typeinfo.obj2feat[otype][key] === 'url')
                continue;
            // Ignore the genuine feature already presented as "visual"
            if (hasSurfaceFeature && key === configuration.surfaceFeature)
                continue;
            keylist.push(key);
        }
        // Next, loop through the keys in the sorted order
        for (var ix = 0; ix < keylist.length; ++ix) {
            var key2 = keylist[ix];
            // This can be simplified when ignoreShowRequest is removed
            var ignoreShowRequest = getFeatureSetting(otype, key2).ignoreShowRequest;
            var ignoreShow = getFeatureSetting(otype, key2).ignoreShow;
            var ignoreRequest = getFeatureSetting(otype, key2).ignoreRequest;
            if (ignoreShowRequest) {
                ignoreShow = true;
                ignoreRequest = true;
            }
            var bal = new ButtonsAndLabel(getFeatureFriendlyName(otype, key2), key2, otype, useSavedFeatures ? ptqf.getSelector(key2) : ButtonSelection.DONT_CARE, !!getFeatureSetting(otype, key2).alternateshowrequestSql, !ignoreShow, !ignoreRequest, sg !== null && sg.containsFeature(key2), false);
            this.allBAL.push(bal);
            this.panel.append(bal.getRow());
        }
        this.panel.append('<tr><td colspan="5"></td><td class="leftalign">&nbsp;</tr>');
        this.panel.append('<tr><td colspan="2"></td><th>{0}</th><th>{1}</th><th>{2}</th><th class="leftalign">{3}</th></tr>'
            .format(localize('dont_care'), localize('dont_show'), database_has_qere() && !otype_has_qere(otype) ? localize('show_qere') : '', localize('other_sentence_unit_types')));
        // Generate buttons for other types:
        for (var level in configuration.sentencegrammar) {
            var leveli = +level;
            if (isNaN(leveli))
                continue; // Not numeric
            var otherOtype = configuration.sentencegrammar[leveli].objType;
            if (otherOtype !== otype && configuration.objectSettings[otherOtype].mayselect) {
                var bal = new ButtonsAndLabel(getObjectFriendlyName(otherOtype), 'otherOtype_' + otherOtype, otype, useSavedFeatures ? ptqf.getObjectSelector(otherOtype) : ButtonSelection.DONT_CARE, false, false, false, true, database_has_qere() && otype_has_qere(otherOtype));
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
var PanelTemplQuizFeatures = /** @class */ (function () {
    function PanelTemplQuizFeatures(otype, qf, where) {
        this.panels = []; // Maps quiz object -> associated panel
        this.fpan = $('<div id="fpan"></div>');
        this.initialOtype = otype;
        this.initialQf = qf;
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
        if (this.initialQf)
            for (var i = 0; i < this.initialQf.showFeatures.length; ++i)
                if (this.initialQf.showFeatures[i] === feat)
                    return ButtonSelection.SHOW;
        if (this.initialQf)
            for (var i = 0; i < this.initialQf.requestFeatures.length; ++i)
                if (this.initialQf.requestFeatures[i].name === feat)
                    return this.initialQf.requestFeatures[i].usedropdown ? ButtonSelection.REQUEST_DROPDOWN : ButtonSelection.REQUEST;
        if (this.initialQf && this.initialQf.dontShowFeatures)
            for (var i = 0; i < this.initialQf.dontShowFeatures.length; ++i)
                if (this.initialQf.dontShowFeatures[i] === feat)
                    return ButtonSelection.DONT_SHOW;
        return ButtonSelection.DONT_CARE;
    };
    PanelTemplQuizFeatures.prototype.getObjectSelector = function (otype) {
        if (this.initialQf && this.initialQf.dontShowObjects) {
            for (var i = 0; i < this.initialQf.dontShowObjects.length; ++i) {
                if (this.initialQf.dontShowObjects[i].content === otype) {
                    if (this.initialQf.dontShowObjects[i].show) // We assume the feature to show is qere
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
        if (this.visiblePanel.visualBAL.isSelected_reqFeat())
            return false;
        for (var i = 0; i < this.visiblePanel.allBAL.length; ++i)
            if (this.visiblePanel.allBAL[i].isSelected_reqFeat())
                return false;
        return true;
    };
    PanelTemplQuizFeatures.prototype.noShowFeatures = function () {
        if (!this.visiblePanel)
            return true;
        if (this.visiblePanel.visualBAL.isSelected_showFeat())
            return false;
        for (var i = 0; i < this.visiblePanel.allBAL.length; ++i)
            if (this.visiblePanel.allBAL[i].isSelected_showFeat())
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
        if (this.visiblePanel.visualBAL.isSelected_showFeat())
            qf.showFeatures.push('visual');
        else if (this.visiblePanel.visualBAL.isSelected_reqFeat())
            qf.requestFeatures.push({ name: 'visual', usedropdown: this.visiblePanel.visualBAL.isSelected_ddCheck() });
        for (var i = 0; i < this.visiblePanel.allBAL.length; ++i) {
            var bal = this.visiblePanel.allBAL[i];
            if (bal.isSelected_showFeat())
                qf.showFeatures.push(bal.getFeatName());
            else if (bal.isSelected_reqFeat())
                qf.requestFeatures.push({ name: bal.getFeatName(), usedropdown: bal.isSelected_ddCheck() });
            else if (bal.isSelected_dontShowFeat()) {
                var fn = bal.getFeatName();
                if (fn.substring(0, 11) === 'otherOtype_') // 11 is the length of 'otherOtype_'
                    qf.dontShowObjects.push({ content: fn.substring(11) });
                else
                    qf.dontShowFeatures.push(fn);
            }
            else if (bal.isSelected_showQere()) {
                qf.dontShowObjects.push({ content: qere_otype(), show: qere_feature() });
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
        for (var i = 0; i < qfnow.requestFeatures.length; ++i)
            if (qfnow.requestFeatures[i].name !== this.initialQf.requestFeatures[i].name ||
                qfnow.requestFeatures[i].usedropdown !== this.initialQf.requestFeatures[i].usedropdown) {
                return true;
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
// -*- js -*-
/* Copyright 2013 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk */
var VerbClassSelection;
(function (VerbClassSelection) {
    VerbClassSelection[VerbClassSelection["YES"] = 0] = "YES";
    VerbClassSelection[VerbClassSelection["NO"] = 1] = "NO";
    VerbClassSelection[VerbClassSelection["DONT_CARE"] = 2] = "DONT_CARE";
})(VerbClassSelection || (VerbClassSelection = {}));
;
var VerbClassButtonsAndLabel = /** @class */ (function () {
    function VerbClassButtonsAndLabel(lab, name, dataName, select) {
        this.yes = $('<input type="radio" name="{0}" value="yes" data-name="{1}">'.format(name, dataName));
        this.no = $('<input type="radio" name="{0}" value="no" data-name="{1}">'.format(name, dataName));
        this.dontcare = $('<input type="radio" name="{0}" value="dontcare" data-name="{1}">'.format(name, dataName));
        this.label = $('<span>{0}</span>'.format(lab));
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
        cell = $('<td></td>');
        cell.append(this.yes);
        row.append(cell);
        cell = $('<td></td>');
        cell.append(this.no);
        row.append(cell);
        cell = $('<td></td>');
        cell.append(this.dontcare);
        row.append(cell);
        cell = $('<td class="leftalign"></td>');
        cell.append(this.label);
        row.append(cell);
        return row;
    };
    return VerbClassButtonsAndLabel;
}());
var PanelForOneVcChoice = /** @class */ (function () {
    function PanelForOneVcChoice(enumValues, valueType, prefix, lv) {
        this.allBAL = [];
        this.panel = $('<table class="striped featuretable"></table>');
        this.panel.append('<tr><th>{0}</th><th>{1}</th><th>{2}</th><th class="leftalign">{3}</th></tr>'
            .format(localize('verb_class_yes'), localize('verb_class_no'), localize('verb_class_dont_care'), localize('verb_class')));
        var swsValues = [];
        for (var ix = 0; ix < enumValues.length; ++ix)
            swsValues.push(new StringWithSort(getFeatureValueFriendlyName(valueType, enumValues[ix], false, false), enumValues[ix]));
        swsValues.sort(function (a, b) { return StringWithSort.compare(a, b); });
        // Next, loop through the keys in the sorted order
        for (var ix = 0; ix < swsValues.length; ++ix) {
            var vc = swsValues[ix].getInternal();
            var vcsel = VerbClassSelection.DONT_CARE;
            if (lv.yes_values.indexOf(vc) != -1)
                vcsel = VerbClassSelection.YES;
            else if (lv.no_values.indexOf(vc) != -1)
                vcsel = VerbClassSelection.NO;
            var bal = new VerbClassButtonsAndLabel(swsValues[ix].getString(), '{0}_{1}'.format(prefix, vc), vc, vcsel);
            this.allBAL.push(bal);
            this.panel.append(bal.getRow());
        }
    }
    PanelForOneVcChoice.prototype.getPanel = function () {
        return this.panel;
    };
    return PanelForOneVcChoice;
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
var SortingCheckBox = /** @class */ (function () {
    /** Creates an initially unselected check box with text.
     * @param text The text to display, optionally starting with '#' followed by a sort index.
     * @see StringWithSort
     */
    function SortingCheckBox(name, value, text) {
        this.sws = new StringWithSort(text);
        this.checkbox = $('<input type="checkbox" name="{0}" value="{1}">'.format(name, value));
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
    var Pair = /** @class */ (function () {
        function Pair(first, second) {
            this.first = first;
            this.second = second;
        }
        return Pair;
    }());
    util.Pair = Pair;
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
/* 2013 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk */
/// <reference path="bootstrap/bootstrap.d.ts" />
/// <reference path="jquery/jquery.d.ts" />
/// <reference path="jqueryui/jqueryui.d.ts" />
/// <reference path="configuration.ts" />
/// <reference path="charset.ts" />
/// <reference path="sentencegrammar.ts" />
/// <reference path="localization.ts" />
/// <reference path="localization_general.ts" />
/// <reference path="paneltemplmql.ts" />
/// <reference path="paneltemplsentenceselector.ts" />
/// <reference path="paneltemplquizobjectselector.ts" />
/// <reference path="paneltemplquizfeatures.ts" />
/// <reference path="verbclasspanel.ts" />
/// <reference path="stringwithsort.ts" />
/// <reference path="sortingcheckbox.ts" />
/// <reference path="util.ts" />
var VirtualKeyboard;
var origMayLocate;
var panelSent; // Sentence selection panel
var panelSentUnit; // Sentence unit selection panel
var panelFeatures; // Features panel
var isSubmitting = false;
var last_quiz_name;
var mayOverwrite = false;
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
    for (var i = 0; i < checked_passages.length; ++i)
        if ($(checked_passages[i]).data('ref') !== initial_universe[i])
            return true;
    return panelSent.isDirty() || panelSentUnit.isDirty() || panelFeatures.isDirty();
}
// CT - 2016-11-07:
// The execution of this function is postponed one second to ensure that ckeditor and VirtualKeyboard
// have been loaded.
// This delay needed to be inserted after adding the Chinese interface; but later it seemed to be unnecessary.
// Maybe it can be removed again by replaceing setTimeout(....,1000) with $(....).
setTimeout(function () {
    for (var i in configuration.sentencegrammar) {
        if (isNaN(+i))
            continue; // Not numeric
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
            //{ name: 'forms' },
            //{ name: 'tools' },
            { name: 'document', groups: ['mode' /*, 'document', 'doctools'*/] },
            //{ name: 'others' },
            '/',
            { name: 'basicstyles', groups: ['basicstyles', 'cleanup'] },
            { name: 'paragraph', groups: ['list', 'indent', 'blocks', 'align', 'bidi'] },
            { name: 'styles' },
            { name: 'colors' },
            { name: 'about' }
        ],
        // Remove some buttons, provided by the standard plugins, which we don't need
        removeButtons: 'Print,Preview,NewPage,Save,Flash,PageBreak,Iframe,CreateDiv,language',
        // Make dialogs simpler.
        removeDialogTabs: 'image:advanced;link:advanced'
    });
    ckeditor.val(decoded_3et.desc);
    $('#quiz_tabs').tabs({ disabled: [3] });
    origMayLocate = decoded_3et.maylocate;
    $('#maylocate_cb').prop('checked', origMayLocate);
    panelFeatures = new PanelTemplQuizFeatures(decoded_3et.quizObjectSelection.object, decoded_3et.quizFeatures, $('#tab_features'));
    panelSentUnit = new PanelTemplQuizObjectSelector(decoded_3et.quizObjectSelection, $('#tab_sentence_units'), panelFeatures);
    panelSent = new PanelTemplSentenceSelector(decoded_3et.sentenceSelection, $('#quiz_tabs'), $('#tab_sentences'), panelSentUnit, panelFeatures);
    $('.quizeditor').show();
}, 1000);
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
    $('#filename-dialog-save').off('click'); // Remove any previous handler
    $('#filename-dialog-save').on('click', function () {
        if ($('#filename-name').val().trim() == '')
            show_error('#filename-error', localize('missing_filename'));
        else {
            quiz_name = $('#filename-name').val().trim();
            // Check if file may be written
            $.ajax('{0}?dir={1}&quiz={2}'.format(check_url, encodeURIComponent(dir_name), encodeURIComponent(quiz_name)))
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
                show_error('#filename-error', '{0} {1}'.format(localize('error_response'), errorThrown));
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
    decoded_3et.sentenceSelection = panelSent.getInfo();
    decoded_3et.quizObjectSelection = panelSentUnit.getInfo();
    decoded_3et.quizFeatures = panelFeatures.getInfo();
    var form = $('<form action="{0}" method="post">'.format(submit_to)
        + '<input type="hidden" name="dir" value="{0}">'.format(encodeURIComponent(dir_name))
        + '<input type="hidden" name="quiz" value="{0}">'.format(encodeURIComponent(quiz_name))
        + '<input type="hidden" name="quizdata" value="{0}">'.format(encodeURIComponent(JSON.stringify(decoded_3et)))
        + '</form>');
    $('body').append(form);
    isSubmitting = true;
    form.submit();
}
function shebanq_to_qo(qo, mql) {
    if (qo === null) {
        $('#qo-dialog-text').html('<p>{0}</p><p>{1}</p>'.format(localize('sentence_selection_imported'), localize('no_focus')));
        $('#qo-yesbutton').hide();
        $('#qo-nobutton').hide();
        $('#qo-okbutton').show();
        $('#qo-dialog-confirm').modal('show');
    }
    else {
        // This is a multi-level format substitution
        // Replace & < and > with HTML entities
        var msg = mql.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
        // Embded in HTML formatting
        msg = '<br><code>[{0} {1}]</code><br>'.format(qo, msg);
        // Embed in localized string
        msg = localize('use_qo_selection').format(msg);
        // Format for dialog
        msg = '<p>{0}</p><p>{1}</p>'.format(localize('sentence_selection_imported'), msg);
        // Set the dialog text
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
        $.ajax('{0}?id={1}&version={2}'.format(import_shebanq_url, encodeURIComponent($('#import-shebanq-qid').val().trim()), encodeURIComponent($('#import-shebanq-dbvers').val().trim())))
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
            show_error('#import-shebanq-error', '{0} {1}'.format(localize('error_response'), errorThrown));
        });
    });
    $('#import-shebanq-dialog').modal('show');
}
