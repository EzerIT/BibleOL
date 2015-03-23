// -*- js -*-
/* 2013 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk */

/// @file
/// @brief Characteristics of the current character set

class Charset {
    public isHebrew : boolean; ///< Is the main language of the text Hebrew?
    public isRtl : boolean; ///< Is the main language of the text written right-to-left?
    public foreignClass : string; ///< CSS class for the main language of the text
    public transliteratedClass : string; ///< CSS class for a transliteration of the main language of the text
    public keyboardName : string; ///< Keyboard layout selector


    constructor(cs : string) {
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
}
