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
var FontSelector = (function () {
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
        if (val === 'XXmineXX') {
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
                var radio_button = $('<input name="{0}" type="radio" data-family="{1}" value="{2}_{3}">'.format(this.inputName, fontlist[i].name, this.alphabet, i));

                var td1 = $('<td>').append(fontlist[i].name);
                var td2 = $('<td class="sample" style="direction:{0}; font-family:{1}; font-size:16pt;">'.format(this.direction, fontlist[i].name)).append(this.sample);
                var td3 = $('<td class="centeralign">').append(radio_button);
                var tr = $('<tr>').append(td1).append(td2).append(td3);
                this.tableSelector.append(tr);
            }
        }

        // Add personal font
        this.myfont_text = $('<input type="text" name="{0}_myfont" value="{1}">'.format(this.alphabet, personal_font));
        this.myfont_radio_button = $('<input name="{0}" type="radio" data-family="XXmineXX" value="{1}_mine">'.format(this.inputName, this.alphabet));

        var td1 = $('<td>').append(localize('or_write_preferred') + '<br>').append(this.myfont_text);
        var td2 = $('<td class="sample" id="{0}_mysample" style="direction:{1}; font-family:{2}; font-size:16pt;">'.format(this.alphabet, this.direction, personal_font)).append(this.sample);
        var td3 = $('<td class="centeralign">').append(this.myfont_radio_button);
        var tr = $('<tr>').append(td1).append(td2).append(td3);
        this.tableSelector.append(tr);

        $('input:radio[value="{0}"]'.format(default_val)).prop('checked', true);

        // Handle changing of font selection
        $('input:radio[name="{0}"]'.format(this.inputName)).on('change', function () {
            return _this.familyChange();
        });
        this.familyChange();

        this.myfont_text.on('input', function (e) {
            return _this.personalChange();
        });
    };
    return FontSelector;
})();
