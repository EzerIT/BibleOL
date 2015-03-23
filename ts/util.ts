// -*- js -*-
/* 2013 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk */


interface String {
    format(...text: any[]): string;
}

String.prototype.format = function() {
    var args = arguments;
    return this.replace(/{(\d+)}/g, function(match : string, num : number) {
        return typeof args[num] != 'undefined'
            ? args[num]
            : match;
    });
};

// Modern browsers have trim()
if (!String.prototype.trim) {
    // This browser doesn't have trim()
    String.prototype.trim = function() {
        return this.replace(/^\s+|\s+$/g, '');
    };
}

module util {
    export function mydump(arr : any, level : number = 0, maxlevel : number = 5) : string {
        var dumped_text = '';
        var level_padding = '';
        for (var j : number = 0; j<level+1; j++)
            level_padding += '    ';

        if (typeof(arr) == 'object') {
            for (var item in arr) {
                var value : any = arr[item];

                if (typeof(value) == 'object') {
                    dumped_text += level_padding + "'" + item + "' ...\n";
                    if (level<maxlevel)
                        dumped_text += mydump(value,level+1,maxlevel);
                    else
                        dumped_text += level_padding + "MAX LEVEL\n";
                } else {
                    dumped_text += level_padding + "'" + item + "' => \"" + value + "\"\n";
                }
            }
        } else {
            dumped_text = "===>"+arr+"<===("+typeof(arr)+")";
        }
        return dumped_text;
    }



    // TODO: Make generic, when available in TypeScript
    export class Pair {
        constructor(public first : any, public second : any) {}
    }


    var setwordsp : boolean = false;
    var forceWsCount : number = 0;
    var forceWideCount : number = 0;

    var setborder : boolean[] = [];
    var forceBorderCount : number[] = [];

    export function resetCheckboxCounters() {
        forceWsCount = 0;
        forceWideCount = 0;
        forceBorderCount = [];
    }

    export function explicitWordSpace(val : boolean) : void {
        setwordsp = val;
        setWordSpace(val);
    }

    function setWordSpace(val : boolean) : void {
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
    }

    export function forceWordSpace(val : boolean) : void {
        if (val)
            ++forceWsCount;
        else
            --forceWsCount;

        if (val && forceWsCount==1) {
            $('#ws_cb').prop('disabled',true);
            $('#ws_cb').prop('checked',true);
            setWordSpace(true);
        }
        else if (!val && forceWsCount==0) {
            $('#ws_cb').prop('disabled',false);
            $('#ws_cb').prop('checked',setwordsp);
            setWordSpace(setwordsp);
        }
    }

    export function forceWide(val : boolean) : void {
        if (val)
            ++forceWideCount;
        else
            --forceWideCount;

        if (val && forceWideCount==1) {
            $('.textblock').css('margin-left','30px').removeClass('inline').addClass('inlineblock');
        }
        else if (!val && forceWideCount==0) {
            $('.textblock').css('margin-left','0').removeClass('inlineblock').addClass('inline');;
        }
    }

    export function explicitBorder(val : boolean, level : number) : void {
        setborder[level] = val;
        showBorder(val, level);
    }

    export function showBorder(val : boolean, level : number) : void {
        var classN : string = 'lev' + level;
        var noClassN : string = 'nolev' + level;

        if (val) {
            $('.' + noClassN + '> .gram').removeClass('dontshowit').addClass('showit'); //css('display','inline-block');
            $('.' + noClassN).addClass(classN);
            $('.' + noClassN).removeClass(noClassN);
        }
        else {
            $('.' + classN + '> .gram').removeClass('showit').addClass('dontshowit'); //css('display','none');
            $('.' + classN).addClass(noClassN);
            $('.' + classN).removeClass(classN);
        }
    }

    export function separateLines(val : boolean, level : number) : void {
        var oldSepLin : string = val ? 'noseplin' : 'seplin';
        var newSepLin : string = val ? 'seplin' : 'noseplin';

        $('.notdummy.nolev' + level).removeClass(oldSepLin).addClass(newSepLin);
        $('.notdummy.lev' + level).removeClass(oldSepLin).addClass(newSepLin);
    }

    export function forceBorder(val : boolean, level : number) : void {
        if (val)
            forceBorderCount[level] ? ++forceBorderCount[level] : forceBorderCount[level]=1;
        else
            --forceBorderCount[level];

        var cbid = '#lev{0}_sb_cb'.format(level);

        if (val && forceBorderCount[level]==1) {
            $(cbid).prop('disabled',true);
            $(cbid).prop('checked',true);
            showBorder(true, level);
        }
        else if (!val && forceBorderCount[level]==0) {
            $(cbid).prop('disabled',false);
            $(cbid).prop('checked',setborder[level]===true);
            showBorder(setborder[level]===true, level);
        }
    }

    export class AddBetween {
        private text : string;
        private first : boolean;
        constructor(text : string) {
            this.text = text;
            this.first = true;
        }

        public getStr() : string {
            if (this.first) {
                this.first = false;
                return '';
            }
            else
                return this.text;
        }

        public reset() : void {
            this.first = true;
        }
    }
}
