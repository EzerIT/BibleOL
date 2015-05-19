// -*- js -*-
/* 2013 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk */

// Localized strings for the user interface in general
interface Localization_general {
    [key : string] : string;
}

declare var l10n_js : Localization_general;


function localize(s : string) {
    var str = l10n_js[s];

    return str===undefined ? '??' + s + '??' : str;
}
