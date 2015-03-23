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
        callback(2 /* groupstart */, objType, this.name, localization.grammargroup[objType][this.name], this);
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
        return localization.grammarsubfeature[objType][this.name][monob.mo.features[this.name]];
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
        callback(1 /* metafeature */, objType, this.name, localization.grammarmetafeature[objType][this.name], this);
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
        var locname = localization.grammarfeature && localization.grammarfeature[objType] && localization.grammarfeature[objType][this.name] ? localization.grammarfeature[objType][this.name] : localization.emdrosobject[objType][this.name];

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

function getObjectFriendlyName(otype) {
    if (otype === 'Patriarch')
        return otype;
    var fn = localization.emdrosobject[otype]._objname;
    return fn ? fn : otype;
}

function getObjectShortFriendlyName(otype) {
    if (localization.emdrosobject[otype + "_abbrev"] === undefined)
        return getObjectFriendlyName(otype);
    else
        return localization.emdrosobject[otype + "_abbrev"]._objname;
}

function getFeatureFriendlyName(otype, feature) {
    if (feature === 'visual')
        return 'Text';

    var fn = localization.emdrosobject[otype][feature];
    return fn ? fn : feature;
}

function getFeatureValueFriendlyName(featureType, value, abbrev) {
    if (abbrev && localization.emdrostype[featureType + "_abbrev"] !== undefined)
        // TODO: We assume there is no "list of " types here
        return localization.emdrostype[featureType + "_abbrev"][value];

    // TODO: For now we handle "list of ..." here. Is this OK with all the other locations where this is used?
    if (featureType.substr(0, 8) === "list of ") {
        featureType = featureType.substr(8); // Remove "list of "
        value = value.substr(1, value.length - 2); // Remove parenteses
        if (value.length == 0)
            return localization.emdrostype[featureType]["NA"];

        var verb_classes = value.split(',');
        var localized_verb_classes = [];

        for (var ix in verb_classes)
            localized_verb_classes.push(localization.emdrostype[featureType][verb_classes[+ix]]);

        localized_verb_classes.sort();
        return localized_verb_classes.join(', ');
    }

    return localization.emdrostype[featureType][value];
}

var FeatureHandler = (function () {
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
            case 'equals':
                return '=';
            case 'differs':
                return '<>';
            case 'matches':
                return '~';
        }
        return '';
    };

    FeatureHandler.prototype.getJoiner = function () {
        switch (this.comparator) {
            case 'equals':
                return ' OR ';
            case 'differs':
                return ' AND ';
            case 'matches':
                return ' OR ';
        }
        return '';
    };
    return FeatureHandler;
})();

var StringFeatureHandler = (function (_super) {
    __extends(StringFeatureHandler, _super);
    function StringFeatureHandler(key) {
        _super.call(this, 'stringfeature', key);
        this.values = [];
        this.normalize();
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
})(FeatureHandler);

var IntegerFeatureHandler = (function (_super) {
    __extends(IntegerFeatureHandler, _super);
    function IntegerFeatureHandler(key) {
        _super.call(this, 'integerfeature', key);
        this.values = [];
        this.normalize();
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
            return (this.comparator === 'differs' ? 'NOT ' : '') + this.name + ' IN (' + values.join(',') + ')';
    };
    return IntegerFeatureHandler;
})(FeatureHandler);

var RangeIntegerFeatureHandler = (function (_super) {
    __extends(RangeIntegerFeatureHandler, _super);
    function RangeIntegerFeatureHandler(key) {
        _super.call(this, 'rangeintegerfeature', key);
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
        } else {
            if (this.isSetHigh())
                return this.name + '<=' + this.value_high;
            else
                return '';
        }
    };
    return RangeIntegerFeatureHandler;
})(FeatureHandler);

var EnumFeatureHandler = (function (_super) {
    __extends(EnumFeatureHandler, _super);
    function EnumFeatureHandler(key) {
        _super.call(this, 'enumfeature', key);
        this.values = [];
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
        return (this.comparator === 'differs' ? 'NOT ' : '') + this.name + ' IN (' + this.values.join(',') + ')';
    };
    return EnumFeatureHandler;
})(FeatureHandler);

var EnumListFeatureHandler = (function (_super) {
    __extends(EnumListFeatureHandler, _super);
    function EnumListFeatureHandler(key) {
        _super.call(this, 'enumlistfeature', key);
        this.listvalues = [];
        this.normalize();
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
        } else
            return '';
    };
    return EnumListFeatureHandler;
})(FeatureHandler);

var ListValuesHandler = (function () {
    function ListValuesHandler() {
        this.type = "listvalues";
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
})();

var PanelTemplMql = (function () {
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
                        addMethods(vh, EnumFeatureHandler);
                        break;

                    case 'enumlistfeature':
                        addMethods(vh, EnumListFeatureHandler);
                        var elfh = vh;
                        for (var j = 0; j < elfh.listvalues.length; ++j)
                            addMethods(elfh.listvalues[j], ListValuesHandler);
                        break;

                    case 'stringfeature':
                        addMethods(vh, StringFeatureHandler);
                        break;

                    case 'integerfeature':
                        addMethods(vh, IntegerFeatureHandler);
                        break;

                    case 'rangeintegerfeature':
                        addMethods(vh, RangeIntegerFeatureHandler);
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

        this.mqlText = $('<textarea id="mqltext" cols="45" rows="2">');

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
                this.objectTypeCombo.append('<option value="{0}" {1} {2}>{3}</option>'.format(s, selectString, resetString, getObjectFriendlyName(s)));
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

        this.clear = $('<button id="clear_button" type="button">Clear</button>');

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
            if (s.match(/\D/g) !== null)
                $('#' + e.data.err_id).html('Not an integer');
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
            if (s.match(/\D/g) !== null)
                $('#' + e.data.err_id).html('Not an integer');
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
        } else if (this.initialMd.mql != null) {
            this.rbMql.prop('checked', true);
            this.mqlText.html(this.initialMd.mql);
            if (this.initialMd.featHand)
                this.objectSelectionUpdated(this.initialMd.object, this.initialMd.featHand.vhand);
            else
                this.objectSelectionUpdated(this.initialMd.object, null);
            this.switchToMql(true);
        } else {
            this.rbFriendly.prop('checked', true);
            this.objectSelectionUpdated(this.initialMd.object, this.initialMd.featHand.vhand);
            this.updateMql();
            this.switchToMql(false);
        }
        this.txtEntry = this.mqlText.val();
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

    /**
    * Creates the various feature selection panels associated with a newly selected quiz object type.
    * @param otype
    * @param fhs
    */
    PanelTemplMql.prototype.objectSelectionUpdated = function (otype, fhs) {
        var _this = this;
        this.handlers = [];
        this.groups = [];

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
            } else {
                group.hide();
                selectString = '';
            }

            this.featureCombo.append('<option value="{0}" {1}>{2}</option>'.format(key, selectString, getFeatureFriendlyName(otype, key)));

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
                    cellLab = $('<td>Low value</td>');
                    cellInput = $('<td></td>');
                    cellInput.append(jtf);
                    cellErr = $('<td id="{0}"></td>'.format(err_id));
                    rowLow.append(cellLab, cellInput, cellErr);

                    jtf = $('<input type="text" size="8">');
                    if (rfh.isSetHigh())
                        jtf.val(String(rfh.value_high));

                    err_id = 'err_{0}_high'.format(key, i);
                    jtf.on('keyup', null, { rfh: rfh, i: 'value_high', err_id: err_id }, $.proxy(this.rangeIntegerTextModifiedListener, this));

                    cellLab = $('<td>High value</td>');
                    cellInput = $('<td></td>');
                    cellInput.append(jtf);
                    cellErr = $('<td id="{0}"></td>'.format(err_id));
                    rowHigh.append(cellLab, cellInput, cellErr);

                    group2.append(rowLow, rowHigh);
                    group.append(group2);
                    this.handlers.push(rfh);
                } else {
                    var ifh = null;
                    if (fhs)
                        ifh = fhs[key];
                    if (!ifh)
                        ifh = new IntegerFeatureHandler(key);

                    var butEquals = $('<input type="radio" name="{0}_{1}_comp" value="equals">'.format(this.name_prefix, key));
                    var butDiffers = $('<input type="radio" name="{0}_{1}_comp" value="differs">'.format(this.name_prefix, key));

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
            } else if (valueType === "ascii" || valueType === "string") {
                var sfh = null;
                if (fhs)
                    sfh = fhs[key];
                if (!sfh)
                    sfh = new StringFeatureHandler(key);

                var butEquals = $('<input type="radio" name="{0}_{1}_comp" value="equals">'.format(this.name_prefix, key));
                var butDiffers = $('<input type="radio" name="{0}_{1}_comp" value="differs">'.format(this.name_prefix, key));
                var butMatches = $('<input type="radio" name="{0}_{1}_comp" value="matches">'.format(this.name_prefix, key));

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
                sel.append(butEquals, "=", butDiffers, "&#x2260;", butMatches, "~");
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
                } else {
                    for (var i = 0; i < sfh.values.length; ++i) {
                        var jtf = $('<input type="text" size="20">');
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
            } else if (valueType.substr(0, 8) === "list of ") {
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

                var tab_labels = ['1st choice', '2nd choice', '3rd choice', '4th choice'];
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
            } else {
                var enumValues = typeinfo.enum2values[valueType];

                if (!enumValues) {
                    console.log('Unknown valueType', valueType);
                } else {
                    var efh = null;
                    if (fhs)
                        efh = fhs[key];
                    if (!efh)
                        efh = new EnumFeatureHandler(key);

                    var butEquals = $('<input type="radio" name="{0}_{1}_comp" value="equals">'.format(this.name_prefix, key));
                    var butDiffers = $('<input type="radio" name="{0}_{1}_comp" value="differs">'.format(this.name_prefix, key));

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

                        var scb = new SortingCheckBox(this.name_prefix + '_' + key, s, getFeatureValueFriendlyName(valueType, s, false));
                        if (!efh.values)
                            alert('Assert efh.values failed for type ' + key);
                        scb.setSelected(efh.values && efh.values.indexOf(s) !== -1);
                        checkBoxes.push(scb);
                    }

                    checkBoxes.sort(function (a, b) {
                        return StringWithSort.compare(a.getSws(), b.getSws());
                    });

                    // Decide how many columns and rows to use for feature values
                    var columns = checkBoxes.length > 12 ? 3 : checkBoxes.length > 4 ? 2 : 1;

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

    // Default value. Overidden in PanelTemplSentenceSelector
    PanelTemplMql.prototype.getUseForQo = function () {
        return false;
    };

    PanelTemplMql.prototype.isDirty = function () {
        return this.mqlText.val() !== this.txtEntry;
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
        } else
            return '';
    };

    PanelTemplMql.prototype.switchToMql = function (useMql) {
        alert('Abstract function switchToMql() called');
    };

    PanelTemplMql.prototype.updateMql = function () {
        this.mqlText.val(this.makeMql());
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
            res.mql = this.mqlText.val();
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
})();
// -*- js -*-
/* Copyright 2013 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk */
var PanelTemplSentenceSelector = (function (_super) {
    __extends(PanelTemplSentenceSelector, _super);
    /**
    * Constructor.
    */
    function PanelTemplSentenceSelector(md, ttabs, where, qoselTab, featureTab) {
        var _this = this;
        _super.call(this, md, 'sensel');
        this.questObjTypeLab = $('<span>Sentence unit type:</span>');
        this.featSelLab = $('<span>Feature:</span>');
        this.templTabs = ttabs;
        this.dirty = false;
        this.featureTab = featureTab;
        this.qoselTab = qoselTab;

        this.cbUseForQo = $('<input type="checkbox" name="useforqol">');
        this.cbUseForQoLabel = $('<span>Use this for sentence unit selection</span>');

        this.cbUseForQo.click(function () {
            if (_this.cbUseForQo.is(':checked'))
                _this.templTabs.tabs('disable', 3);
            else
                _this.templTabs.tabs('enable', 3);

            _this.populateFeatureTab(null);
            _this.dirty = true;
        });

        this.rbMqlLabel = $('<span>MQL statement to select sentences:</span>');
        this.rbFriendlyLabel = $('<span>Friendly feature selector:</span>');

        this.doLayout(where);

        if (this.initialMd == null || this.initialMd.useForQo) {
            this.cbUseForQo.prop('checked', true);
            this.templTabs.tabs('disable', 3);
        } else {
            this.cbUseForQo.prop('checked', false);
            this.templTabs.tabs('enable', 3);
        }

        this.finish_construct();
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
        } else {
            this.questObjTypeLab.removeClass('disabled');
            this.featSelLab.removeClass('disabled');
            this.cbUseForQoLabel.removeClass('disabled');
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

    PanelTemplSentenceSelector.prototype.getMql = function () {
        return this.mqlText.val();
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
        } else
            this.qoselTab.populateFeatureTab(null);
    };
    return PanelTemplSentenceSelector;
})(PanelTemplMql);
// -*- js -*-
/* Copyright 2013 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk */
var PanelTemplQuizObjectSelector = (function (_super) {
    __extends(PanelTemplQuizObjectSelector, _super);
    /**
    * Constructor.
    */
    function PanelTemplQuizObjectSelector(md, where, featureTab) {
        _super.call(this, md, 'qosel');
        this.featSelLab = $('<span>Feature:</span>');
        this.featureTab = featureTab;

        this.rbMqlLabel = $('<span>MQL feature selector:</span>');
        this.rbFriendlyLabel = $('<span>Friendly feature selector:</span>');

        this.doLayout(where);

        this.finish_construct();
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

    PanelTemplQuizObjectSelector.prototype.getMql = function () {
        return this.mqlText.val();
    };

    PanelTemplQuizObjectSelector.prototype.doLayout = function (where) {
        var table = $('<table></table>');
        var row;
        var cell;

        row = $('<tr></tr>');
        cell = $('<td>Sentence unit type:</td>');
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
})(PanelTemplMql);
// -*- js -*-
/* Copyright 2013 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk */

var ButtonSelection;
(function (ButtonSelection) {
    ButtonSelection[ButtonSelection["SHOW"] = 0] = "SHOW";
    ButtonSelection[ButtonSelection["REQUEST"] = 1] = "REQUEST";
    ButtonSelection[ButtonSelection["REQUEST_DROPDOWN"] = 2] = "REQUEST_DROPDOWN";
    ButtonSelection[ButtonSelection["DONT_CARE"] = 3] = "DONT_CARE";
    ButtonSelection[ButtonSelection["DONT_SHOW"] = 4] = "DONT_SHOW";
})(ButtonSelection || (ButtonSelection = {}));
;

var ButtonsAndLabel = (function () {
    function ButtonsAndLabel(lab, featName, otype, select, useDropDown, canShow, canRequest, canDisplayGrammar) {
        var _this = this;
        this.featName = featName;
        this.useDropDown = useDropDown;
        this.canShow = canShow;
        this.canRequest = canRequest;
        this.canDisplayGrammar = canDisplayGrammar;
        this.showFeat = canShow ? $('<input type="radio" name="feat_{0}_{1}" value="show">'.format(otype, featName)) : $('<span></span>');
        this.reqFeat = canRequest ? $('<input type="radio" name="feat_{0}_{1}" value="request">'.format(otype, featName)) : $('<span></span>');
        this.dcFeat = $('<input type="radio" name="feat_{0}_{1}" value="dontcare">'.format(otype, featName));
        this.dontShowFeat = canDisplayGrammar ? $('<input type="radio" name="feat_{0}_{1}" value="dontshowfeat">'.format(otype, featName)) : $('<span></span>');
        this.feat = $('<span>{0}</span>'.format(lab));

        switch (select) {
            case 0 /* SHOW */:
                this.showFeat.prop('checked', true);
                break;
            case 1 /* REQUEST */:
            case 2 /* REQUEST_DROPDOWN */:
                this.reqFeat.prop('checked', true);
                break;
            case 3 /* DONT_CARE */:
                this.dcFeat.prop('checked', true);
                break;
            case 4 /* DONT_SHOW */:
                this.dontShowFeat.prop('checked', true);
                break;
        }

        if (useDropDown) {
            this.ddCheck = $('<input type="checkbox" name="dd_{0}_{1}"">'.format(otype, featName));
            this.ddCheck.prop('checked', select != 1 /* REQUEST */);
        } else
            this.ddCheck = $('<span></span>'); // Empty space filler

        if (canRequest) {
            if (useDropDown) {
                this.ddCheck.prop('disabled', !this.reqFeat.prop('checked'));
                if (canShow)
                    this.showFeat.click(function () {
                        return _this.ddCheck.prop('disabled', true);
                    });
                this.reqFeat.click(function () {
                    return _this.ddCheck.prop('disabled', false);
                });
                this.dcFeat.click(function () {
                    return _this.ddCheck.prop('disabled', true);
                });
                if (canDisplayGrammar)
                    this.dontShowFeat.click(function () {
                        return _this.ddCheck.prop('disabled', true);
                    });
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
})();

var PanelForOneOtype = (function () {
    function PanelForOneOtype(otype, ptqf) {
        this.allBAL = [];
        this.panel = $('<table class="striped featuretable"></table>');
        var useSavedFeatures = otype === ptqf.initialOtype;

        this.panel.append("<tr><th>Show</th><th>Request</th><th>Don't care</th><th>Don't show</th><th>Multiple choice</th><th class=\"leftalign\">Feature</th></tr>");

        // First set up "visual" pseudo feature
        this.visualBAL = new ButtonsAndLabel('Text', 'visual', otype, useSavedFeatures ? ptqf.getSelector('visual') : 3 /* DONT_CARE */, configuration.objHasSurface === otype && !!getFeatureSetting(otype, configuration.surfaceFeature).alternateshowrequestSql, true, configuration.objHasSurface === otype, false);

        this.panel.append(this.visualBAL.getRow());

        // Now handle genuine features
        var hasSurfaceFeature = otype === configuration.objHasSurface;

        var sg = getSentenceGrammarFor(otype);

        var keylist = [];
        for (var key in getObjectSetting(otype).featuresetting) {
            // Ignore specified features
            if (getFeatureSetting(otype, key).ignoreShowRequest && (sg === null || !sg.containsFeature(key)))
                continue;

            // Ignore the genuine feature already presented as "visual"
            if (hasSurfaceFeature && key === configuration.surfaceFeature)
                continue;
            keylist.push(key);
        }

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

            var bal = new ButtonsAndLabel(getFeatureFriendlyName(otype, key2), key2, otype, useSavedFeatures ? ptqf.getSelector(key2) : 3 /* DONT_CARE */, !!getFeatureSetting(otype, key2).alternateshowrequestSql, !ignoreShow, !ignoreRequest, sg !== null && sg.containsFeature(key2));

            this.allBAL.push(bal);
            this.panel.append(bal.getRow());
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
})();

var PanelTemplQuizFeatures = (function () {
    function PanelTemplQuizFeatures(otype, qf, where) {
        this.panels = [];
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
                    return 0 /* SHOW */;

        if (this.initialQf)
            for (var i = 0; i < this.initialQf.requestFeatures.length; ++i)
                if (this.initialQf.requestFeatures[i].name === feat)
                    return this.initialQf.requestFeatures[i].usedropdown ? 2 /* REQUEST_DROPDOWN */ : 1 /* REQUEST */;

        if (this.initialQf && this.initialQf.dontShowFeatures)
            for (var i = 0; i < this.initialQf.dontShowFeatures.length; ++i)
                if (this.initialQf.dontShowFeatures[i] === feat)
                    return 4 /* DONT_SHOW */;

        return 3 /* DONT_CARE */;
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
            dontShowFeatures: []
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
            else if (bal.isSelected_dontShowFeat())
                qf.dontShowFeatures.push(bal.getFeatName());
        }
        return qf;
    };

    PanelTemplQuizFeatures.prototype.isDirty = function () {
        var qfnow = this.getInfo();

        if (qfnow.showFeatures.length !== this.initialQf.showFeatures.length || qfnow.requestFeatures.length !== this.initialQf.requestFeatures.length || qfnow.dontShowFeatures.length !== this.initialQf.dontShowFeatures.length) {
            return true;
        }

        for (var i = 0; i < qfnow.showFeatures.length; ++i)
            if (qfnow.showFeatures[i] !== this.initialQf.showFeatures[i]) {
                return true;
            }
        for (var i = 0; i < qfnow.requestFeatures.length; ++i)
            if (qfnow.requestFeatures[i].name !== this.initialQf.requestFeatures[i].name || qfnow.requestFeatures[i].usedropdown !== this.initialQf.requestFeatures[i].usedropdown) {
                return true;
            }

        for (var i = 0; i < qfnow.dontShowFeatures.length; ++i)
            if (qfnow.dontShowFeatures[i] !== this.initialQf.dontShowFeatures[i]) {
                return true;
            }

        return false;
    };
    return PanelTemplQuizFeatures;
})();
// -*- js -*-
/* Copyright 2013 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk */
var VerbClassSelection;
(function (VerbClassSelection) {
    VerbClassSelection[VerbClassSelection["YES"] = 0] = "YES";
    VerbClassSelection[VerbClassSelection["NO"] = 1] = "NO";
    VerbClassSelection[VerbClassSelection["DONT_CARE"] = 2] = "DONT_CARE";
})(VerbClassSelection || (VerbClassSelection = {}));
;

var VerbClassButtonsAndLabel = (function () {
    function VerbClassButtonsAndLabel(lab, name, dataName, select) {
        this.yes = $('<input type="radio" name="{0}" value="yes" data-name="{1}">'.format(name, dataName));
        this.no = $('<input type="radio" name="{0}" value="no" data-name="{1}">'.format(name, dataName));
        this.dontcare = $('<input type="radio" name="{0}" value="dontcare" data-name="{1}">'.format(name, dataName));
        this.label = $('<span>{0}</span>'.format(lab));

        switch (select) {
            case 0 /* YES */:
                this.yes.prop('checked', true);
                break;
            case 1 /* NO */:
                this.no.prop('checked', true);
                break;
            case 2 /* DONT_CARE */:
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
})();

var PanelForOneVcChoice = (function () {
    function PanelForOneVcChoice(enumValues, valueType, prefix, lv) {
        this.allBAL = [];
        this.panel = $('<table class="striped featuretable"></table>');
        this.panel.append("<tr><th>Yes</th><th>No</th><th>Don't care</th><th class=\"leftalign\">Verb class</th></tr>");

        for (var ix = 0; ix < enumValues.length; ++ix) {
            var vc = enumValues[ix];
            var vcsel = 2 /* DONT_CARE */;

            if (lv.yes_values.indexOf(vc) != -1)
                vcsel = 0 /* YES */;
            else if (lv.no_values.indexOf(vc) != -1)
                vcsel = 1 /* NO */;

            var bal = new VerbClassButtonsAndLabel(getFeatureValueFriendlyName(valueType, vc, false), '{0}_{1}'.format(prefix, vc), vc, vcsel);
            this.allBAL.push(bal);
            this.panel.append(bal.getRow());
        }
    }
    PanelForOneVcChoice.prototype.getPanel = function () {
        return this.panel;
    };
    return PanelForOneVcChoice;
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
var SortingCheckBox = (function () {
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
})();
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
/* 2013 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk */
/// <reference path="jquery/jquery.d.ts" />
/// <reference path="jqueryui/jqueryui.d.ts" />
/// <reference path="configuration.ts" />
/// <reference path="charset.ts" />
/// <reference path="sentencegrammar.ts" />
/// <reference path="localization.ts" />
/// <reference path="paneltemplmql.ts" />
/// <reference path="paneltemplsentenceselector.ts" />
/// <reference path="paneltemplquizobjectselector.ts" />
/// <reference path="paneltemplquizfeatures.ts" />
/// <reference path="verbclasspanel.ts" />
/// <reference path="stringwithsort.ts" />
/// <reference path="sortingcheckbox.ts" />
/// <reference path="util.ts" />

var VirtualKeyboard;

var panelSent;
var panelSentUnit;
var panelFeatures;
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

    checked_passages = $("#passagetree").jstree("get_checked", null, false);

    if (checked_passages.length !== initial_universe.length)
        return true;

    for (var i = 0; i < checked_passages.length; ++i)
        if ($(checked_passages[i]).data('ref') !== initial_universe[i])
            return true;

    return panelSent.isDirty() || panelSentUnit.isDirty() || panelFeatures.isDirty();
}

$(function () {
    for (var i in configuration.sentencegrammar) {
        if (isNaN(+i))
            continue;
        addMethodsSgi(configuration.sentencegrammar[+i]);
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
            { name: 'document', groups: ['mode'] },
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

    $("#quiz_tabs").tabs({ disabled: [3] });

    $('button').button();
    $('input[type="button"]').button();

    panelFeatures = new PanelTemplQuizFeatures(decoded_3et.quizObjectSelection.object, decoded_3et.quizFeatures, $('#tab_features'));

    panelSentUnit = new PanelTemplQuizObjectSelector(decoded_3et.quizObjectSelection, $('#tab_sentence_units'), panelFeatures);
    panelSent = new PanelTemplSentenceSelector(decoded_3et.sentenceSelection, $('#quiz_tabs'), $('#tab_sentences'), panelSentUnit, panelFeatures);
});

function save_quiz() {
    checked_passages = $("#passagetree").jstree("get_checked", null, false);

    if (checked_passages.length == 0) {
        myalert('Passage selection', 'No passages selected');
        return;
    }

    if (panelFeatures.noRequestFeatures()) {
        myalert('Feature specification', 'No request features specified');
        return;
    }

    if (panelFeatures.noShowFeatures()) {
        myalert('Feature specification', 'No show features specified');
        return;
    }

    $('#filename-error').text('');

    $("#filename-dialog").dialog({
        autoOpen: false,
        resizable: false,
        modal: true,
        width: 600,
        buttons: {
            "Save": function () {
                var _this = this;
                if ($('#filename-name').val().trim() == '')
                    $('#filename-error').text("Missing filename");
                else {
                    quiz_name = $('#filename-name').val().trim();

                    // Check if file may be written
                    $.ajax('{0}?dir={1}&quiz={2}'.format(check_url, encodeURIComponent(dir_name), encodeURIComponent(quiz_name))).done(function (data, textStatus, jqXHR) {
                        switch (data.trim()) {
                            case 'OK':
                                $(_this).dialog('close');
                                save_quiz2();
                                break;
                            case 'EXISTS':
                                $(_this).dialog('close');
                                check_overwrite();
                                break;
                            default:
                                $('#filename-error').text(data);
                                break;
                        }
                    }).fail(function (jqXHR, textStatus, errorThrown) {
                        $('#filename-error').text('Error response from server: ' + errorThrown);
                    });
                }
            },
            Cancel: function () {
                $(this).dialog('close');
            }
        }
    }).dialog('open');
}

function check_overwrite() {
    $("#overwrite-dialog-confirm").dialog({
        autoOpen: true,
        resizable: false,
        modal: true,
        buttons: {
            "Yes": function () {
                $(this).dialog("close");
                save_quiz2();
            },
            "No": function () {
                $(this).dialog("close");
            }
        }
    });
}

function save_quiz2() {
    decoded_3et.desc = ckeditor.val();

    decoded_3et.selectedPaths = [];

    for (var i = 0; i < checked_passages.length; ++i) {
        var r = $(checked_passages[i]).data('ref');
        if (r != '')
            decoded_3et.selectedPaths.push(r);
    }

    decoded_3et.sentenceSelection = panelSent.getInfo();
    decoded_3et.quizObjectSelection = panelSentUnit.getInfo();
    decoded_3et.quizFeatures = panelFeatures.getInfo();

    var form = $('<form action="{0}" method="post">'.format(submit_to) + '<input type="hidden" name="dir" value="{0}">'.format(encodeURIComponent(dir_name)) + '<input type="hidden" name="quiz" value="{0}">'.format(encodeURIComponent(quiz_name)) + '<input type="hidden" name="quizdata" value="{0}">'.format(encodeURIComponent(JSON.stringify(decoded_3et))) + '</form>');

    $('body').append(form);

    isSubmitting = true;
    form.submit();
}
