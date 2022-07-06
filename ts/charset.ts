// -*- js -*-
// Copyright © 2018 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk

// Character set management

//****************************************************************************************************
// Charset class
//
// This class handles characteristics of the current character set.
//
class Charset {
    public isHebrew            : boolean; // Is the main language of the text Hebrew?
    public isRtl               : boolean; // Is the main language of the text written right-to-left?
    public foreignClass        : string;  // CSS class for the main language of the text
    public transliteratedClass : string;  // CSS class for a transliteration of the main language of the text
    public keyboardName        : string;  // Keyboard layout selector


    //------------------------------------------------------------------------------------------
    // Constructor method
    //
    // Initializes the members of this object.
    //
    // Parameter:
    //     The character set from the configuration variable.
    //
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

        case 'latin':
            this.foreignClass = 'latin';
            this.isHebrew = false;
            this.isRtl = false;
            break;
        }
    }
}
