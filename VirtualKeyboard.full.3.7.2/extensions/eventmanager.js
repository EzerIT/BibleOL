/**
 *  $Id: eventmanager.js 730 2011-04-14 19:47:54Z wingedfox $
 *  $HeadURL: https://svn.debugger.ru/repos/jslibs/BrowserExtensions/tags/BrowserExtensions.029/eventmanager.js $
 *
 *  EventManager (EM shorthand) is the class, written to manage event attach/detach/register and so on
 *
 *  @modified $Date: 2011-04-14 23:47:54 +0400 (Thu, 14 Apr 2011) $
 *  @version $Rev: 730 $
 *  @license LGPL 2.1 or later
 *  @author Ilya Lebedev <ilya@lebedev.net>
 *  @depends helpers.js
 *
 *  @class
 *  @constructor EventManager
 */
var EM = new function () {
    var self = this;
    /**
     *  Events pool
     *  Element format:
     *  { 'node' : {HTMLElement},
     *    ['rootEHCaller' : {Function}]
     *    'handler' : {
     *      <event_name> : [Function[, Function[, ...]]]
     *      [,<event_name> : [Function[, Function[, ...]]]]
     *    }
     *  }
     *
     *  @type Array
     *  @scope private
     */
    var pool = [];
    /**
     *  Unique ID counter, used to attach IDs to the objects
     *
     *  @type Number
     *  @scope private
     */
    var UID = 0;
    /**
     *  List of used keys, applied to different kinds of objects
     *
     *  @type Object
     *  @scope private
     */
    var keys = {
        'UEID' : '__eventManagerUniqueElementId'
//       ,'UHID' : '__eventManagerUniqueHandlerId'
    };
    /**************************************************************************
    *  PROTECTED METHODS
    ***************************************************************************/
    /**
     *  Method is being binded to any new event handler, then, when called,
     *  does some unification between browser platforms and calls all binded 
     *  event handlers
     *
     *  @param {Event} e event object
     *  @scope protected
     */
    var rootEventHandler = function (e) {
        unifyEvent(e);
        var id = null
           ,hid = null
           ,el = e.target
           ,fe = true
           ,res = true;

        if (!e.currentTarget || !(id = e.currentTarget[keys.UEID]) || !(hid = pool[id].handler[e.type])) return;

//        try {
            for (var i=0, hL=hid.length; i<hL; i++) /*if (isFunction(hid[i]))*/ res=res&&!(false===hid[i].call(e.currentTarget, e));
//        } catch (err) { setTimeout(function(){throw new Error("Event handler for ["+e.type+"] has failed with exception: \""+err.message+"\"");},10) }

        return res;
    };
    /**
     *  Performs events cleanup on page unload
     *  It aims to remove leaking closures
     *
     *  @param {Event} e window.unload event
     *  @scope protected
     */
    var unloadEventHandler = function (e) {
        for (var i=pool.length-1,pid=null,el=null; i>=0; i--) {
            if (pool[i] && (el=(pid = pool[i]).node)) {
                for (var z in pid.handler) {
                    if (!pid.handler.hasOwnProperty(z)) continue;
                    try {
                        if (el.removeEventListener) {
                            el.removeEventListener(z, pid.rootEHCaller?pid.rootEHCaller:rootEventHandler, false);
                        } else if (el.detachEvent) {
                            el.detachEvent('on'+z, pid.rootEHCaller?pid.rootEHCaller:rootEventHandler);
                        }
                    } catch (e) {}
                    pid.handler[z].length = 0;
                }
            }
            el = pid.node = null;
       }
       if (window.removeEventListener) {
           window.removeEventListener(z, arguments.callee, false);
       } else {
           window.detachEvent('on'+z, arguments.callee);
       }
    };
    /**************************************************************************
    *  PRIVATE METHODS
    ***************************************************************************/
    /**
     *  Makes an event clone, it does not dereference objects in the event properties
     *
     *  @param {Event} e event handler
     *  @return {Object} cloned event
     *  @scope private
     */
    var unifyEvent = function (e) {
        var i=self.EU.length
           ,cur,cur1,k,init
        while (i--) {
            cur = self.EU[i];
            if (cur[0].test(e.type)) {
               k=cur[1].length;
               init = null;
               while (k--) {
                   cur1 = cur[1][k];
                   if ('init' == cur1[0]) init = cur1[1]
                   else if (!e[cur1[0]]) e[cur1[0]] = cur1[1];
               }
               if (init) init.call(e);
            }
        }
        if (!e.target && e.type != 'unload') e.target = e.srcElement;
        return e;
    };
    /**
     *  Returns UEID property for the specified element, creates it, if asked
     *
     *  @param {Object} el element to find UEID on
     *  @param {Boolean} f optional flag to force UEID creation, if not exists
     *  @retutn {Number} UEID, if > 0
     *  @scope private
     */
    var getUEID = function (el, f) {
        return el[keys.UEID] || (f && (el[keys.UEID] = ++UID));
    };
    /**************************************************************************
    *  PUBLIC METHODS
    ***************************************************************************/
    /**
     *  Adds the event listener to the queue
     *
     *  @param {Object} el element to attach event handler to
     *  @param {String} et event name to attach event handler to (without 'on' prefix)
     *  @param {Function} h event handler
     *  @return {Boolean} success state
     *  @scope public
     */
    self.addEventListener = function (el, et, h) {
        if (!el || !isFunction(h)) return false;
//        if (!el.addEventListener && !el.attachEvent) return false;
        /*
        *  unique identifier is used to keep an eye on the element
        */
        var id = getUEID(el, true)
           ,pid = null
           ,hid = null;

        /*
        *  prepare pool object, if needed
        */
        if (!pool[id]) {
            pool[id] = {
                'node' : el
               ,'handler' : {}
            }
        };
        pid = pool[id];
        /*
        *  prepare handlers storage in the pool object, if needed
        */
        if (!pid.handler.hasOwnProperty(et)) {
            pid.handler[et] = hid = [];
            /*
            *  if we a here, this means that we have not connected to a node yet
            *  note, we've already made a check for the required methods existense
            */
            if (el.addEventListener) {
                el.addEventListener(et, rootEventHandler, false);
            } else if (el.attachEvent) {
                /*
                *  this workaround is used to avoid IE's lack of currentTarget property
                */
                hid.rootEHCaller = function(e) { 
                    e.currentTarget = pid.node;//pool[id].node;
                    var res = rootEventHandler(e);
                    e.currentTarget = null;
                    return res;
                };
                el.attachEvent('on'+et, hid.rootEHCaller);
            }
        } else {
            hid = pid.handler[et];
        }
        /*
        *  finally, attach handler, if it was not attached before
        */
        if (hid.indexOf(h)==-1) {
            hid[hid.length] = h;
            return true;
        }
        return false;
    };
    /**
     *  Removes the event listener from the queue
     *
     *  @param {Object} el element to attach event handler to
     *  @param {String} et event name to attach event handler to (without 'on' prefix)
     *  @param {Function} h event handler
     *  @return {Boolean} success state
     *  @scope public
     */
    self.removeEventListener = function (el,et,h) {
        if (!el || !isFunction(h)) return false;
        var id = getUEID(el)
           ,pid = pool[id]
           ,eid = null;
        if (pid && (eid = pid.handler[et])) {
            /*
            *  we've found an event handler
            */
            eid.splice(eid.indexOf(h),1);
            if (0 == eid.length) {
                delete pid.handler[et];
                /*
                *  remove the actual listener
                */
                if (el.removeEventListener) {
                    el.removeEventListener(et, eid.rootEHCaller?eid.rootEHCaller:rootEventHandler, false);
                } else if (el.detachEvent) {
                    el.detachEvent('on'+et, eid.rootEHCaller?eid.rootEHCaller:rootEventHandler);
                }
            }
            return true;
        }
        return false;
    };
    /**
     *  Dispatch custom events on the specified element
     *
     *  @param {Object} e event object itself
     *  @return {Boolean} cancelled status
     *  @scope public
     */
    self.dispatchEvent = function (e) {
        var res = rootEventHandler(e);
        return res;
    };
    /**
     *  Registers new event handler for any object
     *  It's a good idea to register events on the object instances, really
     *
     *  @param {Object} o object to register new event on
     *  @param {String} n bindable event name
     *  @param {Boolean} b optional bubbling allowed flag
     *  @param {Function} d optional default action function
     *  @return {EMEvent} object, allowing to invoke events
     *  @scope public
     *  @see EM.EventTarget
     */
    self.registerEvent = function (o, n, b, d) {
        var id = getUEID(o,true);
        if (!pool[id]) {
            pool[id] = {'node' : o,
                        'handler' : []};
        } else {
            pool[id].node = o;
        }
        return new EM.EventTarget(o, n, b, d);
    };
    /**
     *  Performs object initialization
     *
     */
    var __construct = function() {
        /*
        *  for IE, to dereference event handlers and remove memory leaks
        */
        if (window.attachEvent && !window.addEventListener) {
            window.attachEvent('onunload',unloadEventHandler);
        }
    };
    __construct();
};
/******************************************************************************
*  STATIC METHODS
******************************************************************************/
EM.preventDefaultAction = function(e) {
    e.preventDefault();
}
EM.stopPropagationAction = function(e) {
    e.stopPropagation();
}

/******************************************************************************
*  SUPPLEMENTARY CLASSES
******************************************************************************/
/**
 *  EventTarget base class
 *  Used to create self-containing event object
 *
 *  @class EM.EventTarget
 *  @constructor
 *  @param {String} name event name
 *  @param {Null, Object} obj event execution context (this), window if null passed
 *  @param {Boolean} bubble flag allowing event to bubble across element.parentNode
 *  @param {Function} def default action for the event
 */
EM.EventTarget = function (obj, name, bubble, def) {
    var self = this;

    /**
     *  Indicates possible bubbling, by default bubbling is not allowed
     *
     *  @type Boolean
     *  @default false
     *  @scope private
     */
    var canBubble = !!bubble;
    /**
     *  Stores function, performing default action for the event
     *
     *  @type Function
     *  @scope private
     */
    var defaultAction = isFunction(def)?def:null;
    /**************************************************************************
    *  PRIVATE METHODS
    ***************************************************************************/
    /**************************************************************************
    *  PROTECTED METHODS
    ***************************************************************************/
    /**
     *  Used to trigger created event on the supplied object or on the 'obj' constructor param
     *
     *
     *  @param {Object} el optional element to trigger event on (.target property in the event object)
     *  @param {Object} event data
     */
    self.trigger = function (el, data) {
        if (!(arguments.length-1) && el!=obj) {
            data = el;
            el = null;
        }
        if (!el) el = obj;
        var e = {}
           ,res = true
           ,undef = true
           ,tmp = null
        for (var i in data) {
            if (data.hasOwnProperty(i)) e[i] = data[i];
        }

        /*
        *  set defaults
        */
        canBubble = !!bubble;
        defaultAction = def;

        /*
        *  start the go
        */
        do {
            e.preventDefault = preventDefault;
            e.stopPropagation = stopPropagation;
            e.target = el;
            e.currentTarget = el;
            e.type = name;
            tmp = EM.dispatchEvent(e);
            undef &= (isUndefined(tmp))
            res &= !(false===tmp);
        } while ((el = el.parentNode) && canBubble);
        /*
        *  try to execute the default action
        */
        if (isFunction(defaultAction) && res && !undef) {
            defaultAction(e);
        }
        return (defaultAction && res && !undef);
    };
    /**
     *  Prevents default event action
     *
     *  @scope protected
     */
    var preventDefault = function () {
        defaultAction = null;
    };
    /**
     *  Stops bubbling
     *
     *  @scope protected
     */
    var stopPropagation = function () {
        canBubble = false;
    };
};
/**
 *  Namespace for event unification routines
 *
 *  @type Array
 *  @scope protected
 */
EM.EU = [
    [/./ , [
        /**
         *  Prevents event from calling default event handler
         *
         *  @scope protected
         */
        ['preventDefault', function() {
                 this.returnValue = false;
             }
        ]
        /**
         *  Prevents event from futher bubbling
         *
         *  @scope protected
         */
       ,['stopPropagation', function() {
                this.cancelBubble = true;
             }
        ]
    ]]
   ,[/^mouse(over|out|down|up)/ , [
        /**
         *  Used to detect left or right button pressed.
         *  Due to some browser inconsistense, middle button is ignored
         *
         *  @return {Number} 1 - left button, 2 - right button
         *  @scope protected
         */
        ['getButton', function () {
                 return this.button==2?2:1
             }
        ]
       ,['EM_MB_LEFT', '1']
       ,['EM_MB_RIGHT', '2']
    ]]
   ,[/^key(down|up|press)/ , [
        /**
         *  Used to return browser-independend keycode
         *
         *  @return {Number} fixed key code
         *  @scope protected
         */
        ['getKeyCode', function () {
                 switch (this.keyCode) {
                     case 189: return 109;
                     case 187: return 61;  // InternetExplorer
                     case 107: return 61;  // Firefox3
                     case 186: return 59;
                     default:  return this.keyCode;
                 }
             }
        ]
       /**
        *  Calculates 'repeat' property for the key events
        *
        *  @return {Number} 0 means no repeating keystroke detected
        *  @scope protected
        */
       ,['getRepeat', function () {
                          return arguments.callee.repeat
                      }
        ]
       ,['init', function () {
                  var ac = this.getRepeat
                  if ('keyup' == this.type) {
                      ac.repeat = 0;
                      ac.keyCode = 0;
                  } else if ('keydown' == this.type) {
                      ac.repeat = ac.keyCode==this.keyCode;
                      ac.keyCode=this.keyCode;
                  }
              }
        ]
    ]]

];

/*
*  register core event handler, domload
*  it's called right on the document initialization, before images complete load
*/
(function (){
    
    var evt = EM.registerEvent(window,'domload')
       ,executed = false
       ,clearEvents = function() {
           //For IE
           EM.removeEventListener(document, 'propertychange', handlers.ie);
           //For Mozilla
           EM.removeEventListener(document, 'DOMContentLoaded', handlers.mz);
           //For someone else
           EM.removeEventListener(window, 'load', handlers.mz);
       }
       ,handlers = { 'ie' : function(e) {
                               if (window.event.propertyName == 'activeElement' && !executed) {
                                   evt.trigger(window);
                                   clearEvents();
                                   executed = true;
                               }
                           }
                    ,'mz' : function (e) {if(!executed)evt.trigger(window); executed=true;}
                   };

    //For IE
    EM.addEventListener(document, 'propertychange', handlers.ie);
    //For Mozilla
    EM.addEventListener(document,'DOMContentLoaded', handlers.mz);
    //For Safari and Opera
    if (/WebKit|Khtml/i.test(navigator.userAgent)||(window.opera&&parseInt(window.opera.version())<9))
        (function(){if (!executed) /loaded|complete/.test(document.readyState)?(evt.trigger(window),executed=true):setTimeout(arguments.callee,100)})();
    //For someone else
    EM.addEventListener(window, 'load', handlers.mz);
})();
