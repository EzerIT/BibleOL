// -*- js -*-
// Copyright © 2018 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk

// Various utility types and functions, all placed in the namespace 'util'.

//****************************************************************************************************
// Extends the String interface
interface String {
    format(...text: any[]): string;
}

//****************************************************************************************************
// String.format method
//
// Allows text to be inserted into a string. For example, the function call:
//
//     'alpha {0} beta {1} gamma {0}'.format('AA','BB')
//
// will return the string
//
//     'alpha AA beta BB gamma AA'
//
String.prototype.format = function() : string {
    let args = arguments;
    return this.replace(/{(\d+)}/g, function(match : string, num : number) {
        return typeof args[num] != 'undefined'
            ? args[num]
            : match;
    });
};


//****************************************************************************************************
// String.trim method
//
// Modern browsers have string.trim(), but for older browsers we may need to define it ourselves.
// The function removes white space from the start and end of the string.
//
if (!String.prototype.trim) {
    // This browser doesn't have trim()
    String.prototype.trim = function() : string {
        return this.replace(/^\s+|\s+$/g, '');
    };
}



namespace util {

    //************************************************************************************************
    // str2str interface
    //
    // Represents an associative array that maps string => string
    //
    export interface str2str {
        [ key : string ] : string;
    }

    //************************************************************************************************
    // str2strArr interface
    //
    // Represents an associative array that maps string => string | string[]
    //
    export interface str2strArr {
        [ key : string ] : string | string[];
    }

    //************************************************************************************************
    // str2num interface
    //
    // Represents an associative array that maps string => number
    //
    export interface str2num {
        [ key : string ] : number;
    }


    //************************************************************************************************
    // FollowerBox class
    //
    // This class handles a checkbox that can either be set explicitly or implicitly. "Show border"
    // is an example of this. The user case request "Show border" explicitly by clicking its checkbox,
    // or implictly by choosing to display a feature.
    //
    // The class is associated with an actual checkbox.
    //     * If the FollowerBox is set explicitly, the checkbox is checked.
    //     * If the FollowerBox is not set explicitly, but at least one caller has set it implicitly,
    //       the checkbox is checked.
    //     * If the FollowerBox is not set explicitly or implicitly, the checkbox is unchecked.
    //
    export abstract class FollowerBox {
        private   is_explicit : boolean; // Has the checkbox been set explicitly?
        protected count       : number;  // The number of times the checkbox has been set implicitly
        protected level       : number;  // The grammar level (e.g. 0=word, 1=phrase, 2=clause etc.)
        private   idstring    : string;  // The ID attribute of the <input type="checkbox"> element

        private static resetChain : FollowerBox[] = []; // All FollowerBoxes. (They must be reset when a new question is shown.)

        //--------------------------------------------------------------------------------------
        // addToResetChain method
        //
        // Adds the current FollowerBox to resetChain.
        //
        private addToResetChain() : void {
            FollowerBox.resetChain.push(this);
        }

        //--------------------------------------------------------------------------------------
        // resetCheckboxCounters static method
        //
        // Resets the 'count' counters for all FollowerBoxes
        //
        public static resetCheckboxCounters() : void {
            for (let i in this.resetChain) {
                if (isNaN(+i)) continue; // Not numeric

                this.resetChain[i].count = 0;
            }
        }

        //--------------------------------------------------------------------------------------
        // Constructor method
        //
        // Creates an unset FollowerBox.
        //
        // Parameters:
        //     level: The grammar level (e.g. 0=word, 1=phrase, 2=clause etc.).
        //     idstring: The ID attribute of the actual checkbox <input type="checkbox"> element.
        //
        constructor(level : number, idstring : string) {
            this.level       = level;
            this.idstring    = idstring;
            this.is_explicit = false;
            this.count       = 0;
            this.addToResetChain();
        }

        //--------------------------------------------------------------------------------------
        // explicit method
        //
        // Sets/unsets the FollowerBox explicitly.
        //
        // Parameter:
        //     val: True for setting the FollowerBox, false for unsetting it.
        //
        public explicit(val : boolean) : void {
            this.is_explicit = val;
            this.setit(val);
        }
    
        //--------------------------------------------------------------------------------------
        // implict method
        //
        // Sets/unsets the FollowerBox implicitly.
        //
        // Parameter:
        //     val: True for setting the FollowerBox, false for unsetting it.
        //
        public implicit(val : boolean) : void {
            if (val)
                ++this.count;
            else
                --this.count;

            if (val && this.count==1) {
                // We moved from zero to one implicit setting, so
                // the checkbox should be marked as checked
                $(this.idstring).prop('disabled',true);
                $(this.idstring).prop('checked',true);
                this.setit(true);
            }
            else if (!val && this.count==0) {
                // We moved from to to zero implicit settings, so
                // the checkbox should be marked as unchecked unless it is explicitly set
                $(this.idstring).prop('disabled',false);
                $(this.idstring).prop('checked',this.is_explicit);
                this.setit(this.is_explicit);
            }
        }
    
        //--------------------------------------------------------------------------------------
        // setit method
        //
        // Check/uncheck the checkbox.
        //
        // Parameter:
        //     val: True for checking the checkbox, false for unchecking it
        protected abstract setit(val : boolean) : void;
    }

    //************************************************************************************************
    // BorderFollowerBox class
    //
    // This is a subclass of FollowerBox which is used to show/hide the border around a phrase,
    // clause etc.
    //
    export class BorderFollowerBox extends FollowerBox {
        
        //--------------------------------------------------------------------------------------
        // Constructor method
        //
        // Creates an unset BorderFollowerBox.
        //
        // Parameters:
        //     level: The grammar level (e.g. 1=phrase, 2=clause etc.).
        //
        constructor(level : number) {
            super(level, `#lev${level}_sb_cb`);
        }

        //--------------------------------------------------------------------------------------
        // setit method
        //
        // See description under FollowerBox
        //
        protected setit(val : boolean) : void {
            if (val) {
                $(`.lev${this.level} > .gram`).removeClass('dontshowit').addClass('showit');
                $(`.lev${this.level}`).removeClass('dontshowborder').addClass('showborder');
            }
            else {
                $(`.lev${this.level} > .gram`).removeClass('showit').addClass('dontshowit');
                $(`.lev${this.level}`).removeClass('showborder').addClass('dontshowborder');
            }
        }
    }

    //************************************************************************************************
    // SeparateLinesFollowerBox class
    //
    // This is a subclass of FollowerBox which is used to show/not show each phrase, clause etc. on
    // a separate line.
    //
    export class SeparateLinesFollowerBox extends FollowerBox {
        
        //--------------------------------------------------------------------------------------
        // Constructor method
        //
        // Creates an unset SeparateLinesFollowerBox.
        //
        // Parameters:
        //     level: The grammar level (e.g. 1=phrase, 2=clause etc.).
        //
        constructor(level : number) {
            super(level, `#lev${level}_seplin_cb`);
        }

        //--------------------------------------------------------------------------------------
        // setit method
        //
        // See description under FollowerBox
        //
        protected setit(val : boolean) : void {
            let oldSepLin : string = val ? 'noseplin' : 'seplin';
            let newSepLin : string = val ? 'seplin' : 'noseplin';
 
            $(`.notdummy.lev${this.level}`).removeClass(oldSepLin).addClass(newSepLin);
        }
    }

    //************************************************************************************************
    // WordSpaceFollowerBox class
    //
    // This is a subclass of FollowerBox which is used to show/not show spacing between all Hebrew
    // words.
    //
    export class WordSpaceFollowerBox extends FollowerBox {
        
        //--------------------------------------------------------------------------------------
        // Constructor method
        //
        // Creates an unset WordSpaceFollowerBox.
        //
        // Parameters:
        //     level: The grammar level (always 0).
        //
        constructor(level : number) {
            super(level, '#ws_cb');
        }

        //--------------------------------------------------------------------------------------
        // implict method
        //
        // Sets/unsets the WordSpaceFollowerBox implicitly.
        //
        // Parameter:
        //     val: True for setting the WordSpaceFollowerBox, false for unsetting it.
        //
        public implicit(val : boolean) : void {
            super.implicit(val);

            if (val && this.count==1) {
                $('.textblock').css('margin-left','30px').removeClass('inline').addClass('inlineblock');
            }
            else if (!val && this.count==0) {
                $('.textblock').css('margin-left','0').removeClass('inlineblock').addClass('inline');;
            }
        }

        //--------------------------------------------------------------------------------------
        // setit method
        //
        // See description under FollowerBox
        //
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


    //************************************************************************************************
    // AddBetween class
    //
    // Handles the insertion of a separator between several items.
    // The method getStr() returns an empty string the first time it is called; it returns the
    // specified separator every time it is called thereafter.
    //
    export class AddBetween {
        private separator : string;   // The separator to insert between items
        private first     : boolean;  // True if we have not see the first item yet

        //--------------------------------------------------------------------------------------
        // Constructor method
        //
        // Parameter:
        //     separator: The separator to insert between items.
        constructor(separator : string) {
            this.separator = separator;
            this.first = true;
        }

        //--------------------------------------------------------------------------------------
        // getStr method
        //
        // Returns an empty string the first time it is called.
        // Returns the separator on each subsequent call.
        //
        public getStr() : string {
            if (this.first) {
                this.first = false;
                return '';
            }
            else
                return this.separator;
        }

        //--------------------------------------------------------------------------------------
        // reset method
        //
        // Resets the AddBetween object to its initial state.
        //
        public reset() : void {
            this.first = true;
        }
    }

    //****************************************************************************************************
    // mydump function
    //
    // For debugging purposes: Formats a JavaScript object for printing.
    //
    // Parameters:
    //     arr: Object to print.
    //     level: The amount of indentation to generate.
    //     maxlevel: The maximum level of subjobjcts to include in the dump.
    //
    // Returns:
    //     A string containing the formatted object.
    //
    export function mydump(arr : any, level : number = 0, maxlevel : number = 5) : string {
        let dumped_text = '';
        let level_padding = '';
        for (let j : number = 0; j<level+1; j++)
            level_padding += '    ';

        if (typeof(arr) == 'object') {
            for (let item in arr) {
                let value : any = arr[item];

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
}
