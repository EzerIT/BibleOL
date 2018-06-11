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
/* 2017 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk */
/// <reference path="util.ts" />
/// <reference path="resizer.ts" />
function set_legend_height(data) {
    switch (resizer.getWindowSize()) {
        case 'xs':
            data.toChange.height('auto');
            break;
        default:
            data.toChange.height(data.source.height());
            break;
    }
}
function legend_adjust(toChange, source) {
    set_legend_height({ 'toChange': toChange, 'source': source });
}
function fix_legend_height(toChange, source) {
    resizer.addResizeListener(set_legend_height, { 'toChange': toChange, 'source': source }, 'abcdef');
    if (!resizer.sizeIs('xs')) {
        var cheight = source.height();
        if (toChange.height() < cheight)
            $(toChange).height(cheight);
    }
}
