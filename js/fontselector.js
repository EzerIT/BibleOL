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
function localize(s) {
    var str = l10n_js[s];
    return str === undefined ? '??' + s + '??' : str;
}
var FontSelector = (function () {
    function FontSelector(alphabet, sample, direction) {
        this.alphabet = alphabet;
        this.sample = sample;
        this.direction = direction;
        this.text_align = direction == 'rtl' ? 'right' : 'left';
        this.detector = new Detector(alphabet);
        this.inputName = alphabet + 'choice';
        this.tableSelector = $("#" + alphabet + "font");
    }
    FontSelector.prototype.familyChange = function () {
        var val = $('input:radio[name="' + this.alphabet + 'choice"]:checked').attr('data-family');
        if (val === 'XXmineXX') {
            val = this.myfont_text.prop('value');
            $("#" + this.alphabet + "_mysample").css('font-family', val);
        }
        $("." + this.alphabet + "sample").css('font-family', val);
    };
    FontSelector.prototype.personalChange = function () {
        $("input:radio[value=\"" + this.alphabet + "_mine\"]").prop('checked', true);
        this.familyChange();
    };
    FontSelector.prototype.detectFonts = function (fontlist, personal_font, default_val) {
        var _this = this;
        for (var i = 0, len = fontlist.length; i < len; ++i) {
            if (fontlist[i].webfont || this.detector.detect(fontlist[i].name)) {
                var radio_button = $("<input name=\"" + this.inputName + "\" type=\"radio\" data-family=\"" + fontlist[i].name + "\" value=\"" + this.alphabet + "_" + i + "\">");
                var td1_1 = $('<td>').append(fontlist[i].name);
                var td2_1 = $("<td class=\"sample\" style=\"direction:" + this.direction + "; text-align:" + this.text_align + "; font-family:" + fontlist[i].name + "; font-size:16pt;\">")
                    .append(this.sample);
                var td3_1 = $('<td class="centeralign">').append(radio_button);
                var tr_1 = $('<tr>').append(td1_1).append(td2_1).append(td3_1);
                this.tableSelector.append(tr_1);
            }
        }
        this.myfont_text = $("<input type=\"text\" name=\"" + this.alphabet + "_myfont\" value=\"" + personal_font + "\">");
        this.myfont_radio_button = $("<input name=\"" + this.inputName + "\" type=\"radio\" data-family=\"XXmineXX\" value=\"" + this.alphabet + "_mine\">");
        var td1 = $('<td>').append(localize('or_write_preferred') + '<br>').append(this.myfont_text);
        var td2 = $("<td class=\"sample\" id=\"" + this.alphabet + "_mysample\" style=\"direction:" + this.direction + "; text-align:" + this.text_align + "; font-family:" + personal_font + "; font-size:16pt;\">")
            .append(this.sample);
        var td3 = $('<td class="centeralign">').append(this.myfont_radio_button);
        var tr = $('<tr>').append(td1).append(td2).append(td3);
        this.tableSelector.append(tr);
        $("input:radio[value=\"" + default_val + "\"]").prop('checked', true);
        $("input:radio[name=\"" + this.inputName + "\"]").on('change', function () { return _this.familyChange(); });
        this.familyChange();
        this.myfont_text.on('input', function (e) { return _this.personalChange(); });
    };
    return FontSelector;
}());
