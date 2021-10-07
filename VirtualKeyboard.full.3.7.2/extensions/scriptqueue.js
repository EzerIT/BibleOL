/**
 *  $Id: scriptqueue.js 761 2011-05-06 14:13:15Z wingedfox $
 *  $HeadURL: https://svn.debugger.ru/repos/jslibs/BrowserExtensions/tags/BrowserExtensions.029/scriptqueue.js $
 *
 *  Dynamically load scripts and script queues (when load order is important)
 *
 **********NOTE********
 *  If you need to load any scripts before ScriptQueue exists, use the following snippet
 *  <code>
 *      if (!(window.ScriptQueueIncludes instanceof Array)) window.ScriptQueueIncludes = []
 *      window.ScriptQueueIncludes = window.ScriptQueueIncludes.concat(scriptsarray);
 *  </code>
 *  ScriptQueue loads all the scripts, queued before its' load in the ScriptQueueIncludes
 **********
 *
 *  @author Ilya Lebedev <ilya@lebedev.net>
 *  @modified $Date: 2011-05-06 18:13:15 +0400 (Fri, 06 May 2011) $
 *  @version $Rev: 761 $
 *  @license LGPL 2.1 or later
 *
 *  @class ScriptQueue
 *  @param {Function} optional callback function, called on each successful script load
 *  @scope public
 */
ScriptQueue = function (cbk) {
    var self = this,
        st = arguments.callee;
    /*
    *  empty function is better than number of checks in the code...
    */
    if ('function' != typeof cbk) cbk = function(){}
    /**
     *  Queue for the current loader instance
     *
     *  @type {Array}
     *  @scope private
     */
    var queue = [];
    //-------------------------------------------------------------------------
    // PUBLIC
    //-------------------------------------------------------------------------
    /**
     *  Loads the single script, independent from any other
     *  
     *  @param {String} path path to the target script
     *  @return {Boolean} true when script is getting loaded, false otherwise
     *  @scope public
     */
    self.load = function (path) {
        load(path,cbk);
    }
    /**
     *  Builds a queue of scripts, when they should be loaded in the proper order
     *
     *  @param {String} path script name to add to the queue
     *  @scope public
     */
    self.queue = function (path) {
        var f = queue.length;
        queue[f] = path;
        if (!f) load(path,queuemonitor);
    }
    //-------------------------------------------------------------------------
    // PRIVATE
    //-------------------------------------------------------------------------
    /**
     *  Performs scripts existense check and loads it, if needed
     *
     *  @param {String} path path to the script
     *  @param {Function} cbk callback
     *  @scope private
     */
    var load = function (path, cbk) {
        var sid
           ,scr = st.scripts;
        if (sid = scr.hash[path]) { // in queue
            scr = st.scripts[sid]
            if (scr[2]) { // loaded
                setTimeout(function(){cbk(path,scr[2])}, 1);
            } else {
                scr[1].push(cbk);
            }
        } else {
            sid = scr.length;
            scr[sid] = [path,[cbk],false];
            scr.hash[path] = sid;
            ls(path);
        }
    }
    /**
     *  Attaches script to the document
     *
     *  @param {String} src path to script
     *  @scope private
     */
    var ls = function (src) {
        if (document.body) { // document is loaded, don't use document.write
            var s = document.createElement('script')
               ,h = document.getElementsByTagName("head")[0];
            s.type= "text/javascript";
            s.charset="UTF-8";
            s.src=src;
            /*
            *  some browsers does change the src, store original one here
            */
            s.rSrc=src; 
            s.onerror = s.onload = s.onreadystatechange = loadmonitor;
            s.timeout = setTimeout(function(){loadmonitor.call(s,{type: 'error', q:src})}, 10000);
            h.appendChild(s);
        } else {
            document.write("<scr"+"ipt onload=\"return 1;\" src=\""+src+"\" type=\"text/javascript\" charset=\"UTF-8\"></scr"+"ipt>");
            /*
            *  note, real onload handler is commented out, because IE calls it too late, 
            *  which is not acceptable, because it breaks onload there
            *  assume, that scripts are loaded successfully
            */
//            s.onload = s.onreadystatechange = loadmonitor;
            loadmonitor.call({'rSrc':src},{'type':'load'});
        }
    }
    //-------------------------------------------------------------------------
    // PROTECTED
    //-------------------------------------------------------------------------
    /**
     *  Monitors queue load and runs next iteration, untill empties the queue
     *
     *  @param {String} path loaded script
     *  @param {Boolean} s load completed status
     *  @scope protected
     */
    var queuemonitor = function (path,s) {
        /*
        *  report load result for the last run
        */
        cbk(path,s);

        /*
        *  find the first matching element, skipping any empty ones
        */
        var e;
        while ((!e || e == path) && queue.length) {
            e = queue.shift();
        }
        /*
        *  next run, when
        *  there's a script to load
        *  last load was successful
        */
        if (e && s) {
            load(e,arguments.callee);
        } else {
            /*
            *  report last run for either
            */
            setTimeout(function(){cbk(null,s)}, 1);
            queue.length = 0;
        }
    }
    /**
     *  Handles onload and onreadystatechange events
     *
     *  @param {Event} e handled event object
     *  @scope protected
     */
    var loadmonitor = function (e) {
        var scr = st.scripts
           ,sid = scr.hash[this.rSrc]
           ,e = e||window.event
           ,res;

        clearTimeout(this.timeout);
        scr = scr[sid];
        if (scr && !scr[2]) {
            if (e && 'readystatechange' == e.type && 'loading' == this.readyState) {
                // do nothing
            } else {
                if (e && ('load' == e.type || 'complete'==this.readyState || 'loaded'==this.readyState)) {
                    scr[2] = res = true;
                } else if (!e || 'error' == e.type || (e.toString && e.toString().match(/error/i))) {
                    res = false;
                }
                for (var i=0,cbk=scr[1],cL=cbk.length;i<cL;i++) {
                    cbk[i](scr[0],res);
                }
                if (!res) {
                    delete st.scripts.hash[this.rSrc];
                    delete st.scripts[sid];
                }
            }
        }
    }
}

/**
 *  Stores information about the loaded scripts
 *  Element structure:
 *  [0 : string script path
 *   1 : [ array of callback functions ]
 *   2 : boolean 'loaded' flag
 *  ]
 *  Array fields:
 *  .hash { maps script paths' to array ids }
 *
 *  @type {Array}
 *  @scope protected
 */
ScriptQueue.scripts = [false];
ScriptQueue.scripts.hash = {};
/**
 *  Static method to load bunch of scripts at once
 *
 *  @param {Array} arr list of scripts to execute
 *  @scope public
 */
ScriptQueue.queue = function(arr, cbk) {
    if (!arr.length) return;
    var q = new ScriptQueue(cbk);
    for (var i=0,aL=arr.length;i<aL;i++) {
        q.queue(arr[i]);
    }
}
/**
 *  Static method to load a script
 *
 *  @param {String} src script to load
 *  @param {Function} cbk callback function
 *  @scope public
 */
ScriptQueue.load = function(src, cbk) {
    if (src) {
        (new ScriptQueue(cbk)).load(src);
    }
}
/*
*  If any load requests does exists, serve them
*/
if (window.ScriptQueueIncludes instanceof Array) {
    ScriptQueue.queue(window.ScriptQueueIncludes);
}
