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
function localize(s) {
    var str = l10n_js[s];
    return str === undefined ? '??' + s + '??' : str;
}
// -*- js -*-
/* 2013 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk */
/// <reference path="jquery/jquery.d.ts" />
/// <reference path="fontdetect.d.ts" />
/// <reference path="util.ts" />
/// <reference path="localization_general.ts" />
var FontSelector = /** @class */ (function () {
    function FontSelector(alphabet, sample, direction) {
        this.alphabet = alphabet;
        this.sample = sample;
        this.direction = direction;
        this.detector = new Detector(alphabet);
        this.inputName = alphabet + 'choice';
        this.tableSelector = $('#' + alphabet + 'font');
    }
    FontSelector.prototype.familyChange = function () {
        var val = $('input:radio[name="' + this.alphabet + 'choice"]:checked').attr('data-family');
        if (val === 'XXmineXX') { // Personal font
            val = this.myfont_text.prop('value');
            $('#' + this.alphabet + '_mysample').css('font-family', val);
        }
        $('.' + this.alphabet + 'sample').css('font-family', val);
    };
    FontSelector.prototype.personalChange = function () {
        $('input:radio[value="{0}_mine"]'.format(this.alphabet)).prop('checked', true);
        this.familyChange();
    };
    FontSelector.prototype.detectFonts = function (fontlist, personal_font, default_val) {
        var _this = this;
        for (var i = 0, len = fontlist.length; i < len; ++i) {
            if (fontlist[i].webfont || this.detector.detect(fontlist[i].name)) {
                var radio_button = $('<input name="{0}" type="radio" data-family="{1}" value="{2}_{3}">'
                    .format(this.inputName, fontlist[i].name, this.alphabet, i));
                var td1 = $('<td>').append(fontlist[i].name);
                var td2 = $('<td class="sample" style="direction:{0}; font-family:{1}; font-size:16pt;">'
                    .format(this.direction, fontlist[i].name))
                    .append(this.sample);
                var td3 = $('<td class="centeralign">').append(radio_button);
                var tr = $('<tr>').append(td1).append(td2).append(td3);
                this.tableSelector.append(tr);
            }
        }
        // Add personal font
        this.myfont_text = $('<input type="text" name="{0}_myfont" value="{1}">'.format(this.alphabet, personal_font));
        this.myfont_radio_button = $('<input name="{0}" type="radio" data-family="XXmineXX" value="{1}_mine">'.format(this.inputName, this.alphabet));
        var td1 = $('<td>').append(localize('or_write_preferred') + '<br>').append(this.myfont_text);
        var td2 = $('<td class="sample" id="{0}_mysample" style="direction:{1}; font-family:{2}; font-size:16pt;">'
            .format(this.alphabet, this.direction, personal_font))
            .append(this.sample);
        var td3 = $('<td class="centeralign">').append(this.myfont_radio_button);
        var tr = $('<tr>').append(td1).append(td2).append(td3);
        this.tableSelector.append(tr);
        $('input:radio[value="{0}"]'.format(default_val)).prop('checked', true);
        // Handle changing of font selection
        $('input:radio[name="{0}"]'.format(this.inputName)).on('change', function () { return _this.familyChange(); });
        this.familyChange();
        this.myfont_text.on('input', function (e) { return _this.personalChange(); });
    };
    return FontSelector;
}());
