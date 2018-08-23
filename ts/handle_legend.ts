// -*- js -*-
// Copyright © 2018 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk

// This code changes the height of a graph legend as the browser window size changes.

/// <reference path="util.ts" />
/// <reference path="resizer.ts" />


//****************************************************************************************************
// set_legend_height function
//
// Sets the height of an HTML element based on the browser window size.
// With small window sizes, the HTML element height is set to 'auto'.
// With largere window sizes, the HTML element height is set the height of another HTML element.
//
// Parameters:
//     data.toChange: The HTML element whose height to change.
//     data.source: The HTML element whose height may be used as the source value.
//
function set_legend_height(data : any) {
    switch (resizer.getWindowSize()) {
    case 'xs':
    case 'sm':
        data.toChange.height('auto');
        break;

    default:
        data.toChange.height(data.source.height());
        break;
    }
}

//****************************************************************************************************
// legend_adjust function
//
// A wrapper function for set_legend_height (see above).
//
// Parameters:
//     toChange: The HTML element whose height to change.
//     source: The HTML element whose height may be used as the source value.
//
function legend_adjust(toChange : JQuery, source : JQuery) {
    set_legend_height({'toChange': toChange, 'source': source});
}

//****************************************************************************************************
// fix_legend_height function
//
// Listens for browser window size changes and calls set_legend_heigh when the window size changes.
//
// Parameters:
//     toChange: The HTML element whose height to change.
//     source: The HTML element whose height may be used as the source value.
//
function fix_legend_height(toChange : JQuery, source : JQuery) {
    resizer.addResizeListener(set_legend_height, {'toChange': toChange, 'source': source}, 'abcdef');

    if (!resizer.sizeIs('xs') && !resizer.sizeIs('sm')) {
        let cheight = source.height();
        if (toChange.height()<cheight)
            $(toChange).height(cheight);
    }
}
