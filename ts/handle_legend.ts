// -*- js -*-

/* 2017 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk */

/// <reference path="util.ts" />
/// <reference path="resizer.ts" />


function set_legend_height(data : any) {
    switch (resizer.getWindowSize()) {
    case 'xs':
        data.toChange.height('auto');
        break;

    default:
        data.toChange.height(data.source.height());
        break;
    }
}

function legend_adjust(toChange : JQuery, source : JQuery) {
    set_legend_height({'toChange': toChange, 'source': source});
}

function fix_legend_height(toChange : JQuery, source : JQuery) {
    resizer.addResizeListener(set_legend_height, {'toChange': toChange, 'source': source}, 'abcdef');

    if (!resizer.sizeIs('xs')) {
        var cheight = source.height();
        if (toChange.height()<cheight)
            $(toChange).height(cheight);
    }
}
