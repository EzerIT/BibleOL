/**
 *  $Id: dom.js 703 2010-11-23 17:28:05Z wingedfox $
 *  $HeadURL: https://svn.debugger.ru/repos/jslibs/BrowserExtensions/tags/BrowserExtensions.029/dom.js $
 *
 *  DOM-related stuff and CSS manipulation class
 *
 *  @author Ilya Lebedev
 *  @author $Author: wingedfox $
 *  @modified $Date: 2010-11-23 20:28:05 +0300 (Tue, 23 Nov 2010) $
 *  @version $Rev: 703 $
 *  @license LGPL
 *  @depends helpers.js
 *  @depends arrayextensions.js
 */

if (isUndefined(DOM)) var DOM = {};
/**
 *  Performs parent lookup by
 *   - node object: actually it's "is child of" check
 *   - tagname: getParent(el, 'li') == getParent(el, 'tagName', 'LI')
 *   - any node attribute
 *
 *  @param {HTMLElement} el source element
 *  @param {HTMLElement, String} cp DOMNode or string tagname or string attribute name
 *  @param {String} vl optional attribute value
 *  @return {HTMLElement, Null}
 *  @scope public
 */
DOM.getParent = function (el /* : HTMLElement */, cp /* :String, HTMLElement */, vl /* :String */) /* :HTMLElement */ {
  if (el == null) return null;
  else if (el.nodeType == 1 &&
      ((!isUndefined(vl) && el[cp] == vl) ||
       ('string' == typeof cp && DOM.hasTagName(el, cp)) ||
       el == cp)) return el;
  else return arguments.callee(el.parentNode, cp, vl); 
};
/**
 *  Calculates the offset for the DOM node from top left corner
 * 
 *  @author Matt Kruse
 *  @see http://javascripttoolbox.com/lib/objectposition/index.php
 *  @param {HTMLElement} el
 *  @return {Object} x: horizontal offset, y: vertical offset
 *  @scope public
 */
DOM.getOffset = function (el /* :HTMLElement */) /* :Object */ {
    var fixBrowserQuirks = true
       ,o = el
       ,left = 0
       ,top = 0
       ,width = 0
       ,height = 0
       ,parentNode = null
       ,offsetParent = null;

    if (o==null) return null;
    
    offsetParent = o.offsetParent;
    var originalObject = o
       ,el = o; // "el" will be nodes as we walk up, "o" will be saved for offsetParent references
    while (el.parentNode!=null) {
      el = el.parentNode;
      if (el.offsetParent!==null) {
        var considerScroll = true;
        /*
        In Opera, if parentNode of the first object is scrollable, then offsetLeft/offsetTop already 
        take its scroll position into account. If elements further up the chain are scrollable, their 
        scroll offsets still need to be added in. And for some reason, TR nodes have a scrolltop value
        which must be ignored.
        */
        if (fixBrowserQuirks && window.opera) {
          if (el==originalObject.parentNode || el.nodeName=="TR") {
            considerScroll = false;
          }
        }
        if (considerScroll) {
          if (el.scrollTop && el.scrollTop>0) {
            top -= el.scrollTop;
          }
          if (el.scrollLeft && el.scrollLeft>0) {
            left -= el.scrollLeft;
          }
        }
      }
      // If this node is also the offsetParent, add on the offsets and reset to the new offsetParent
      if (el == offsetParent) {
        left += o.offsetLeft;
        if (el.clientLeft && el.nodeName!="TABLE") { 
          left += el.clientLeft;
        }
        top += o.offsetTop;
        if (el.clientTop && el.nodeName!="TABLE") {
          top += el.clientTop;
        }
        o = el;
        if (o.offsetParent==null) {
          if (o.offsetLeft) {
            left += o.offsetLeft;
          }
          if (o.offsetTop) {
            top += o.offsetTop;
          }
        }
        offsetParent = o.offsetParent;
      }
    }
    
  
    if (originalObject.offsetWidth) {
      width = originalObject.offsetWidth;
    }
    if (originalObject.offsetHeight) {
      height = originalObject.offsetHeight;
    }
    
    return {'x':left, 'y':top, 'width':width, 'height':height};
};
  
//DOM.getOffset = function (el /* :HTMLElement */) /* :Object */ {
/*
    var xy = {'x' : el.offsetLeft , 'y' : el.offsetTop};
    if (el.offsetParent) {
        var xy1 = arguments.callee(el.offsetParent);
        xy.x += xy1.x;
        xy.y += xy1.y;
    }
    return xy;
}
*/
/**
 *  Returns the width of the window canvas
 * 
 *  @param {HTMLElement} el optional target node
 *  @return {Number}
 *  @scope public
 */
DOM.getClientWidth = function (el) /* :Number */{
    var win = this.getWindow(el)
       ,doc = win.document
       ,w=0;
    if (win.innerWidth) w = win.innerWidth;
    else if (doc.documentElement && doc.documentElement.clientWidth) w = doc.documentElement.clientWidth;
    else if (doc.body) w = doc.body.clientWidth;
    return w;
};
/**
 *  Returns the width of the window itself
 * 
 *  @param {HTMLElement} el optional target node
 *  @return {Number}
 *  @scope public
 */
DOM.getOffsetWidth = function (el) /* :Number */{
    var win = this.getWindow(el)
       ,doc = win.document
       ,w=0;
    if (win.outerWidth) w = win.outerWidth;
    else if (doc.documentElement && doc.documentElement.clientWidth) w = doc.documentElement.clientWidth;
    else if (doc.body) w = doc.body.clientWidth;
    return w;
};
/**
 *  Returns the height of the window canvas
 * 
 *  @param {HTMLElement} el optional target node
 *  @return {Number}
 *  @scope public
 */
DOM.getClientHeight = function (el) /* :Number */{
    var win = this.getWindow(el)
       ,doc = win.document
       ,h=0;
    if (win.innerHeight) h = win.innerHeight;
    else if (doc.documentElement && doc.documentElement.clientHeight) h = doc.documentElement.clientHeight;
    else if (doc.body) h = doc.body.clientHeight;
    return h;
};
/**
 *  Returns the height of the window itself
 * 
 *  @param {HTMLElement} el optional target node
 *  @return {Number}
 *  @scope public
 */
DOM.getOffsetHeight = function (el) /* :Number */{
    var win = this.getWindow(el)
       ,doc = win.document
       ,h=0;
    if (win.outerHeight) h = win.outerHeight;
    else if (doc.documentElement && doc.documentElement.clientHeight) h = doc.documentElement.clientHeight;
    else if (doc.body) h = doc.body.clientHeight;
    return h;
};
/**
 *  Returns the height of the scrolled area for the body
 * 
 *  @param {HTMLElement} el optional target node
 *  @return {Number}
 *  @scope public
 */
DOM.getBodyScrollTop = function (el) /* :Number */{
    var win = this.getWindow(el)
       ,doc = win.document;
    return win.pageYOffset || (doc.documentElement && doc.documentElement.scrollTop) || (doc.body && doc.body.scrollTop);
};
/**
 *  Returns the height of the scrolled area for the body
 * 
 *  @param {HTMLElement} el optional target node
 *  @return {Number}
 *  @scope public
 */
DOM.getBodyScrollLeft = function (el) /* :Number */{
    var win = this.getWindow(el)
       ,doc = win.document;
    return win.pageXOffset || (doc.documentElement && doc.documentElement.scrollLeft) || (doc.body && doc.body.scrollLeft);
};
/**
 *  Tries to find the window for the target element
 *  Returns main window, if no target specified
 *
 *  @param {HTMLElement} el optional target node
 *  @return {Window} target window
 *  @scope public
 */
DOM.getWindow = function (el) {
    var win = window;
    if (el) {
        var doc = el.ownerDocument;
        win = doc.defaultView || doc.parentWindow || doc.window || window;
    }
    return win;
}

/**
 *  Calculates cursor position properly
 *
 *  @param {Event} e event object to get cursor positions from
 *  @return {Object} object with x and y cursor positions
 *  @scope protected
 *  @see http://hartshorne.ca/2006/01/23/javascript_cursor_position/
 *  @author Beau Hartshorne
 */
DOM.getCursorPosition = function (e) {
    if (e.pageX || e.pageY) return {'x': e.pageX, 'y': e.pageY};
    
    var de = document.documentElement || document.body;
    return {'x': e.clientX + de.scrollLeft - (de.clientLeft || 0)
           ,'y': e.clientY + de.scrollTop - (de.clientTop || 0)};
};
/**
 *  Checks, if property matches a tagname(s)
 * 
 *  @param {HTMLElement} prop
 *  @param {String, Array} tags
 *  @return {Boolean}
 *  @scope public
 */
DOM.hasTagName = function (prop /* :HTMLElement */, tags /* :String, Array */) {
    if ("string" == typeof tags) tags = [tags];
    if (!isArray(tags) || isEmpty(tags) || isUndefined(prop) || isEmpty(prop.tagName)) return false;
    var t = prop.tagName.toLowerCase();
    for (var i=0, tL=tags.length; i<tL; i++) {
        if (tags[i].toLowerCase() == t) return true;
    }
    return false;
};
/**
 *  Return the actual rgb color value from the following formats
 *  #rrggbb
 *  #rgb
 *  rgb (0..255, 0..255,0..255)
 *  rgb (0..100%, 0..100%,0..100%)
 *  <color_name>
 *
 *  @param {String} from attr name
 *  @return {Array} r,g,b values
 *  @scope public
 */
DOM.color2rgb = function (prop) {
    var e;
    /*
    *  note, properties like borderColor might have the series of colors
    */
    if (/^([a-z]+)($|\s[a-z]+)/i.test(prop)) {
        var d = document.body, ov = d.vLink;
        d.vLink = prop.split(" ")[0];
        prop = d.vLink;
        d.vLink = ov;
    }
    try {
        if (e = prop.match(/^#([\da-f]{6})$/i)) {
            return e=parseInt(e[1],16),[(e&0xff0000)>>16,(e&0xff00)>>8,(e&0xff)]
        } else if (e = prop.match(/^#([\da-f]{3})$/i)) {
            return e=parseInt(e[1],16),[((e&0xf00)>>8)*0x11,((e&0xf0)>>4)*0x11,(e&0xf)*0x11];
        } else
            return (prop.match(/([\d%]+)/g).splice(0,3).map(function(a){ return /%/.test(a)?(parseInt(a)*2.55).toFixed(0):parseInt(a)}))
    } catch(err){
        return;
    }
}
DOM.setOpacity = function (el, opacity) {
    if (el.style.opacity != opacity) {
        el.style.opacity = 
        el.style.KhtmOpacity =
        el.style.MozOpacity = opacity;
        el.style.filter = "alpha(opacity="+(opacity*100)+")";
    }
}

/**
 *  Singleton for the stylesheets operations
 *
 *  @param {String} sname stylesheet to operate with, should contain a path part with or without ".css" suffix (will be added, whether not exists)
 *  @param {Window} win optional window object to look the stylesheet for, if null current one will be used
 *  @return {DOM.StyleSheet} interface to operate on the stylesheet
 */
DOM.StyleSheet = (function () {
    var StyleSheet = function (sname, win) {
        var self = this;

        /**
         *  Helper method, runs given operation on the stylesheet
         *
         *  @param {Function} callback function with the single argument, performing some operation on found stylesheet(s)
         *  @return {Number} number of the processed stylesheets
         *  @scope private
         */
        var operate = function (callback) {
            var n  = 0;
            if (sname && callback) {
                var ss = win.document.getElementsByTagName("link")
                   ,sr = new RegExp(sname+"$","i");
                for (var i=0, ssL = ss.length; i<ssL; i++) {
                    var sheet = ss[i];
                    if (sr.test(sheet.href)) {
                        callback(sheet);
                        n++;
                    }
                }
            }
            return n;
        }

        /**
         *  Helper method, retrieves the matching stylesheet urls
         *
         *  @return {Array} list of matching stylesheet urls
         *  @scope private
         */
        var get = function () {
            var sheets = [];
            if (sname) {
                var h  = win.document.getElementsByTagName('head')[0]
                   ,sr = new RegExp('<link[^>]+?href\\s*=\\s*["\']?(([^>]+?/|)'+sname+'[^"\'\\s]*)[^>]*>','ig')
                   ,m  = sr.exec(h.innerHTML);

                while (m && m[1]) {
                    sheets.push(m[1]);
                    m = sr.exec(h.innerHTML);
                }
            }
            return sheets;
        }

        /**
         *  Removes given stylesheet(s) from the document
         *
         *  @return {Number} number of processed stylesheets
         *  @scope public
         */
        self.remove = function () {
            return operate (function (el) { el.parentNode.removeChild(el)});
        }

        /**
         *  Disables given stylesheet(s) from the document
         *
         *  @return {Number} number of processed stylesheets
         *  @scope public
         */
        self.disable = function () {
            return operate (function (el) { el.disabled=true});
        }

        /**
         *  Enables given stylesheet(s) from the document
         *
         *  @param {String} sname stylesheet to look for, preferably the full url, might be looked up by @{link #DOM.getStylesheets}
         *  @param {Window} win optional window object to look the stylesheet in, if null default one will be used
         *  @return {Number} number of processed stylesheets
         *  @scope public
         */
        self.enable = function () {
            return operate (function (el) { el.disabled=false});
        }

        /**
         *  Checks whether the stylesheet has been attached to the document
         *
         *  @param {String} sname stylesheet to look for, should contain a path part with or without ".css" suffix (will be added, whether not exists)
         *  @param {Window} win optional window object to look the stylesheet, if null current one will be used
         *  @return {Boolean} true if any stylesheet for the given name found
         *  @scope public
         */
        self.add = function () {
            if (!self.exists()) {
                var head = win.document.getElementsByTagName('head')[0]
                   ,s = win.document.createElement('link');
                s.rel = 'stylesheet';
                s.type= 'text/css';
                s.href= sname;
                head.appendChild(s);
            }
        }

        /**
         *  Checks whether the stylesheet has been attached to the document
         *
         *  @return {Boolean} true if any stylesheet for the given name found
         *  @scope public
         */
        self.exists = function () {
            return Boolean(get().length);
        }

        /**
         *  Counts the number of matching stylesheets
         *
         *  @return {Number} count of found stylesheets
         */
        self.count = function () {
            return get().length;
        }

        /**
         *  Checks whether the stylesheet has been attached to the document
         *
         *  @return {String} full path t the first matching stylesheet or empty string if nothing found
         *  @scope public
         */
        self.get = function (idx) {
            return get()[(parseInt(idx) || 0)];
        }
    }

    return function (sname, win) {
        if (sname && !/\.css$/i.test(sname)) {
            sname += ".css";
        }
        if (!win || !win.document) {
            win = window;
        }
        return new StyleSheet(sname, win);
    }

})();
/**
 *  DOM.CSS is the CSS processing class, allowing to easy mangle class names
 *
 *  @param {HTMLElement} el element to provide interface for
 *  @scope public
 *  @constructor
 *  @class DOM.CSS
 *  @exception on invalid parameter
 *  @depends arrayextensions.js
 *  @depends helpers.js
 */
DOM.CSS = (function () {
    var self = arguments.callee;
    /**
     *  Adds the class name, unlimited number of arguments is supported
     *
     *  @param {String} class classname to apply to the element
     *  @return {Object} singleton object to chain operations
     *  @scope public
     */
    self.addClass = function() {
        var arg = isArray(arguments[0])?arguments[0]:Array.prototype.slice.call(arguments);
        var el = self.el;
        el.className = el.className+" "+Array.prototype.join.call(arg," ");
        return self;
    };
    /**
     *  Removes the class name, unlimited number of arguments is supported
     *
     *  @param {String} class classname to apply to the element
     *  @return {Object} singleton object to chain operations
     *  @scope public
     */
    self.removeClass = function() {
        var arg = isArray(arguments[0])?arguments[0]:arguments;
        var ac = arguments.callee;
        if (!ac.cache) ac.cache = {};
        var c = ac.cache;
        var el = self.el;
        for (var i=0, aL=arg.length; i<aL; i++) {
            var a = arg[i];
            if (!c.hasOwnProperty(a)) c[a] = new RegExp("((^|\\s+)"+a+"(?=\\s|$))+","g");
            el.className = el.className.replace(c[a]," ");
        }
        el.className=el.className.replace(/\s{2,}/g," ")
        return self;
    };
    /**
     *  Checks classname for the certain class
     *
     *  @param {String} c class name to check for
     *  @return {Boolean} class name existence
     *  @scope public
     */
    self.hasClass = function(c) {
        re=new RegExp("(^|\\s+)"+c+"(\\s+|$)");
        return self.el.className.match(re," "+c+" ");
    };
    /**
     *  Returns the actual CSS class for the element
     *
     *  @return {String} css class
     *  @scope public
     */
    self.getClass = function() {
        return self.el.className;
    }
    /**
     *  Retrieves class value from class name by pattern 
     *   class-var = "name:value"
     *   name = [a-z][-a-z0-9]
     *   value = value | val1:val2:...:valN
     *
     *  @param {String} c class name to check for
     *  @return {String, Array} value(s)
     *  @scope public
     */
    self.getClassValue = function(c) {
        var vals = self.el.className.match(new RegExp("(^|\\s)"+c+":([^\\s]+)"));
    
        return vals?((vals[2].indexOf(":")+1)?vals[2].split(":")
                                             :vals[2])
                   :null;
    };
    /**
     *  Returns actual style for the element, computed from CSS and inline styles
     *
     *  @param {String} prop optional style property to fetch
     *  @return {Object} computed style or property value
     *  @scope public
     */
    self.getComputedStyle = function(prop) {
        var y;
        var el = self.el;
        if (self.el.currentStyle)
            y = prop?el.currentStyle[prop]:el.currentStyle;
        else if (window.getComputedStyle) {
            y = document.defaultView.getComputedStyle(el,null);
            if (prop) y=y[prop];
        } else {
            y = null;
        }
        return y;
    }

    return function(el) {
        self.el = el;
        return self;
    }
})();
