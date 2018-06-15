// -*- js -*-
// Copyright © 2018 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk

// Declarations for the JavaScript file fontdetect.js.

//****************************************************************************************************
// Detector interface
//
interface Detector {
    //------------------------------------------------------------------------------------------
    // detect method
    //
    // Checks if a particular font is available in the current browser.
    //
    // Parameter:
    //     font: Name of font.
    // Returns:
    //     True if the font is available, false otherwise.
    //
    detect(font : string): boolean;
}

//----------------------------------------------------------------------------------------------------
// Constructor method for JavaScript Detector class
//
// Parameter:
//     lang: Name of alphabet (e.g. 'hebrew', 'greek')
//
declare let Detector : new(lang : string) => Detector;
