// -*- js -*-
/* 2015 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk */

// The idea for some of the following code is taken from
// http://stackoverflow.com/questions/18575582/how-to-detect-responsive-breakpoints-of-twitter-bootstrap-3-using-javascript

// This code provides information about Bootstrap's current concept of the window size


module resizer {
    // Returns the current window size, either 'xs', 'sm', 'md', or 'lg'.
    export function getWindowSize() : string {
        return $('.device-sizer:visible').attr('data-size');
    }

    // Checks if the current window size is siz (which is either 'xs', 'sm', 'md', or 'lg')
    export function sizeIs(siz : string) : boolean {
        return $('.device-is-' + siz).is(':visible');
    }

    var timers = {};

    // Specify a function that listens for window resizings.
    // Use a separate uniqueId for each instance that listens for a resize.
    export function addResizeListener(callback : (d:any)=>void, data : any, uniqueId : string) {
        // When the window resizes, wait for 500 ms and if no further resize occurs, call the callback function

        $(window).resize(() => {
            // If the window was recently resized, restart the timer, otherwize start the timer

            if (timers[uniqueId])
                clearTimeout(timers[uniqueId]);

            timers[uniqueId] = setTimeout(() => callback(data), 500); // Call callback in 500 ms
        });
    }

    $(function() {
        // Insert size detectors before </body>
        var sizes = ['xs', 'sm', 'md', 'lg'];
        for (var i=0; i<sizes.length; ++i)
            $('body').append('<div class="visible-{0}-block device-is-{0} device-sizer" data-size="{0}"></div>'
                             .format(sizes[i]));
    });
}


