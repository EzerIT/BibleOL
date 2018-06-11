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

    // A followerBox handles a checkbox that can either be set explicitly or implicitly. "Show border"
    // is an example of this. The user case request "Show border" explicitly by clicking its checkbox,
    // or implictly by choosing to display a feature.
    export abstract class FollowerBox {
        private is_explicit : boolean;
        protected count : number;
    
        constructor(protected level : number, private idstring : string) {
            this.is_explicit = false;
            this.resetCount();
            addToResetChain(this);
        }
    
        public resetCount() {
            this.count = 0;
        }

        public explicit(val : boolean) : void {
            this.is_explicit = val;
            this.setit(val);
        }
    
        public implicit(val : boolean) : void {
            if (val)
                ++this.count;
            else
                --this.count;

            if (val && this.count==1) {
                $(this.idstring).prop('disabled',true);
                $(this.idstring).prop('checked',true);
                this.setit(true);
            }
            else if (!val && this.count==0) {
                $(this.idstring).prop('disabled',false);
                $(this.idstring).prop('checked',this.is_explicit);
                this.setit(this.is_explicit);
            }
        }
    
        protected abstract setit(val : boolean) : void;
    }

    export class BorderFollowerBox extends FollowerBox {
        constructor(level : number) {
            super(level, '#lev{0}_sb_cb'.format(level));
        }

        protected setit(val : boolean) : void {
            if (val) {
                $('.lev' + this.level + '> .gram').removeClass('dontshowit').addClass('showit');
                $('.lev' + this.level).removeClass('dontshowborder').addClass('showborder');
            }
            else {
                $('.lev' + this.level + '> .gram').removeClass('showit').addClass('dontshowit');
                $('.lev' + this.level).removeClass('showborder').addClass('dontshowborder');
            }
        }
    }

    export class SeparateLinesFollowerBox extends FollowerBox {
        constructor(level : number) {
            super(level, '#lev{0}_seplin_cb'.format(level));
        }

        protected setit(val : boolean) : void {
            var oldSepLin : string = val ? 'noseplin' : 'seplin';
            var newSepLin : string = val ? 'seplin' : 'noseplin';
 
            $('.notdummy.lev' + this.level).removeClass(oldSepLin).addClass(newSepLin);
        }
    }

    export class WordSpaceFollowerBox extends FollowerBox {
        constructor(level : number) {
            super(level, '#ws_cb');
        }

        public implicit(val : boolean) : void {
            super.implicit(val);

            if (val && this.count==1) {
                $('.textblock').css('margin-left','30px').removeClass('inline').addClass('inlineblock');
            }
            else if (!val && this.count==0) {
                $('.textblock').css('margin-left','0').removeClass('inlineblock').addClass('inline');;
            }
        }


        protected setit(val : boolean) : void {
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
    }


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


    var resetChain : FollowerBox[] = [];

    function addToResetChain(fb : FollowerBox) {
        resetChain.push(fb);
    }

    export function resetCheckboxCounters() {
        for (var i in resetChain) {
            if (isNaN(+i)) continue; // Not numeric
            resetChain[i].resetCount();
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
