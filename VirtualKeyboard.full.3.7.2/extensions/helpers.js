/**
 *  $Id: helpers.js 697 2010-11-11 18:54:51Z wingedfox $
 *  $HeadURL: https://svn.debugger.ru/repos/jslibs/BrowserExtensions/tags/BrowserExtensions.029/helpers.js $
 *
 *  File contains differrent helper functions
 * 
 * @author Ilya Lebedev <ilya@lebedev.net>
 * @license LGPL
 * @version $Rev: 697 $
 */
//-----------------------------------------------------------------------------
//  Variable/property checks
//-----------------------------------------------------------------------------
/**
 *  Checks if property is undefined
 *
 *  @param {Object} prop value to check
 *  @return {Boolean} true if matched
 *  @scope public
 */
function isUndefined (prop /* :Object */) /* :Boolean */ {
  return (typeof prop == 'undefined');
}
/**
 *  Checks if property is function
 *
 *  @param {Object} prop value to check
 *  @return {Boolean} true if matched
 *  @scope public
 */
function isFunction (prop /* :Object */) /* :Boolean */ {
  return (typeof prop == 'function');
}
/**
 *  Checks if property is string
 *
 *  @param {Object} prop value to check
 *  @return {Boolean} true if matched
 *  @scope public
 */
function isString (prop /* :Object */) /* :Boolean */ {
  return (typeof prop == 'string');
}
/**
 *  Checks if property is number
 *
 *  @param {Object} prop value to check
 *  @return {Boolean} true if matched
 *  @scope public
 */
function isNumber (prop /* :Object */) /* :Boolean */ {
  return (typeof prop == 'number');
}
/**
 *  Checks if property is the calculable number
 *
 *  @param {Object} prop value to check
 *  @return {Boolean} true if matched
 *  @scope public
 */
function isNumeric (prop /* :Object */) /* :Boolean */ {
  return (isNumber(prop)||isString(prop))&&!isNaN(parseInt(prop))&&isFinite(parseInt(prop));
}
/**
 *  Checks if property is array
 *
 *  @param {Object} prop value to check
 *  @return {Boolean} true if matched
 *  @scope public
 */
function isArray (prop /* :Object */) /* :Boolean */ {
  return (prop instanceof Array);
}
/**
 *  Checks if property is regexp
 *
 *  @param {Object} prop value to check
 *  @return {Boolean} true if matched
 *  @scope public
 */
function isRegExp (prop /* :Object */) /* :Boolean */ {
  return (prop instanceof RegExp);
}
/**
 *  Checks if property is a boolean value
 *
 *  @param {Object} prop value to check
 *  @return {Boolean} true if matched
 *  @scope public
 */
function isBoolean (prop /* :Object */) /* :Boolean */ {
  return ('boolean' == typeof prop);
}
/**
 *  Checks if property is a scalar value (value that could be used as the hash key)
 *
 *  @param {Object} prop value to check
 *  @return {Boolean} true if matched
 *  @scope public
 */
function isScalar (prop /* :Object */) /* :Boolean */ {
  return isNumeric(prop)||isString(prop)||isBoolean(prop);
}
/**
 *  Checks if property is empty
 *
 *  @param {Object} prop value to check
 *  @return {Boolean} true if matched
 *  @scope public
 */
function isEmpty (prop /* :Object */) /* :Boolean */ {
  if (isBoolean(prop)) return false;
  if (isRegExp(prop) && new RegExp("").toString() == prop.toString()) return true;
  if (isString(prop) || isNumber(prop) || isFunction(prop)) return !prop;
  if (Boolean(prop)&&false != prop) {
    for (var i in prop) if(prop.hasOwnProperty(i)) return false
  }
  return true;
}
//-----------------------------------------------------------------------------
//  File paths functions
//-----------------------------------------------------------------------------
/**
 *  used to glue path's
 *
 *  @param {String} number of strings
 *  @return {String} glued path
 *  @scope public
 */
function gluePath () /* :String */ {
  var aL=arguments.length, i=aL-2, s = arguments[aL-1]; 
  for(;i>=0;i--)
    s = ((!isString(arguments[i])&&!isNumber(arguments[i]))||isEmpty(arguments[i])
        ?s
        :arguments[i]+'\x00'+s); 
  return s?s.replace(/\/*\x00+\/*/g,"/"):"";
}

/**
 *  return full path to the script
 *
 *  @param {String} sname script name
 *  @return {String, Null} mixed string full path or null
 *  @scope public
 */
function findPath (sname /* :String */) /* :String */{
  var h =document.getElementsByTagName('html')[0].innerHTML 
     ,sr=new RegExp('<scr'+'ipt[^>]+?src\\s*=\\s*["\']?([^>]+?/|)('+sname+')([^"\'\\s]*)[^>]*>(.|[\r\n])*?</scr'+'ipt>','i')
     ,m =h.match(sr);
  if (m) {
      if (!m[1]) m[1] = "";
      /*
      *  we've matched the full path
      */
      if (m[1].match(/^((https?|file)\:\/{2,}|\w:[\\])/)) return m[1];
      /*
      *  we've matched absolute path from the site root
      */
      if (m[1].indexOf("/")==0) return m[1];
      b = document.getElementsByTagName('base');
      if (b[0] && b[0].href) return b[0].href+m[1];
      /*
      *  return matching part of the document location and path to js file
      */
      return (document.location.href.match(/(.*[\/\\])/)[0]+m[1]).replace(/^\/+/,"");
  }
  return null;
}
/**
 *  return parsed query string for the specified script name 
 *
 *  @param {String} sname script name
 *  @return {String, Null} mixed string full path or null
 *  @scope public
 */
function getScriptQuery (sname) {
    var h =document.getElementsByTagName('html')[0].innerHTML
       ,sr=new RegExp('<scr'+'ipt[^>]+?src\\s*=\\s*["\']?(?:[^>]+?/|)'+sname+'([^#"\']*).+?</scr'+'ipt>','i')
       ,m = h.match(sr);
    if (m) return parseQuery(m[1].replace(/^[^?]*\?([^#]+)/,"$1"));
    return {};
}
/**
 *  Function parses supplied query string and returns the hash with the values
 *  Multiple values are stored in the array  
 * 
 *  @param {String} q query string
 *  @return {Object}
 *  @scope public
 */
function parseQuery (q) {
    if ('string'!=typeof q || q.length<2) return {};
    q = q.split(/&amp;|&/g);
    for (var z=0,qL=q.length,rs={},kv,rkv;z<qL;z++) {
        kv=q[z].split("=");
        /*
        *  convert PHP and Perl-styled hashes to JS has keys 
        */
        kv[0]=kv[0].replace(/[{}\[\]]*$/,"");
        rkv = rs[kv[0]];
        /*
        *  replace all + with spaces, unescape skips this part
        */
        kv[1]=unescape(kv[1]?kv[1].replace("+"," "):"");
        if (rkv)
           if ('array'==typeof(rkv))rs[kv[0]][rs[kv[0]].length]=kv[1];
           else rs[kv[0]]=[rs[kv[0]],kv[1]];
        else rs[kv[0]]=kv[1];
    }
    return rs
}



//-----------------------------------------------------------------------------
//  Misc helpers
//-----------------------------------------------------------------------------
/**
 *  Method is used to convert table into the array
 *  
 * @param {String, HTMLTableElement, HTMLTBodyElement, HTMLTHeadElement, HTMLTFootElement} id
 * @param {Number} ci column indexes to put in the array
 * @param {String} section optional section type
 * @param {Object} subsection optional subsection index
 * @return {NULL, Array}
 * @scope public
 */
function table2array (id, ci, section, subsection) {
    if (isString(id)) id = document.getElementById(id);
    if (!id || !DOM.hasTagName(id, ['table','tbody,','thead','tfoot'])) return null;
    if (!isEmpty(section) && (!isString(section) || !(id = id.getElementsByTagName(section)))) return null;
    if (!isEmpty(subsection) && (!isNumber(subsection) || subsection<0 || !(id = id[subsection]))) return null;

    if (isUndefined(id.rows)) return null;
    var res = []
       ,span = document.createElement('span')
       ,ts = null
       ,ce = null
    for (var i=0, rL=id.rows.length; i<rL; i++) {
        var tr = [];
        if (isArray(ci)) {
            for (var z=0, cL=ci.length; z<cL; z++) {
                ce = id.rows[i].cells[ci[z]];
                if (ce) {
                    span.innerHTML = ce.innerText?ce.innerText:ce.innerHTML.replace(/<script\s+(.|\r?\n)*?<\/script>|<[^>]*>/g,"");
                    span.normalize();
                    tr[tr.length] = span.firstChild?span.firstChild.nodeValue.trim(" \xA0"):"";
                } else {
                    tr[tr.length] = "";
                }
            }
        } else {
            for (var z=0, tL=id.rows[i].cells.length; z<tL; z++) {
                cd = id.rows[i].cells[z];
                span.innerHTML = ce.innerText?ce.innerText:ce.innerHTML.replace(/<script\s+(.|\r?\n)*?<\/script>|<[^>]*>/g,"");
                span.normalize();
                tr[tr.length] = span.firstChild?span.firstChild.nodeValue.trim(" \xA0"):"";
            }
        }
        if (!isEmpty(tr)) res[res.length] = tr;
    }
    return res;
}

/**
 *  Creates element all-at-once
 *
 *  @param {String} tag name
 *  @param {Object} p element properties { 'class' : 'className',
 *                                         'style' : { 'property' : value, ... },
 *                                         'event' : { 'eventType' : handler, ... },
 *                                         'child' : [ child1, child2, ...],
 *                                         'param' : { 'property' : value, ... },
 *  @return {HTMLElement} created element or null
 *  @scope public
 */
document.createElementExt = function (tag /* :String */, p /* :Object */ ) /* :HTMLElement */{
  var L, i, k, el = document.createElement(tag);
  if (!el) return null;
  for (i in p) {
    if (!p.hasOwnProperty(i)) continue;
    switch (i) {
      case "class" : el.setAttribute('className',p[i]); el.setAttribute('class',p[i]); break;
      case "style" : for (k in p[i]) { if (!p[i].hasOwnProperty(k)) continue; el.style[k] = p[i][k]; } break;
      case "event" : for (k in p[i]) { if (!p[i].hasOwnProperty(k)) continue; el.attachEvent(k,p[i][k]); } break;
      case "child" : L = p[i].length; for (k = 0; k<L; k++) el.appendChild(p[i][k]); break;
      case "param" : for (k in p[i]) { if (!p[i].hasOwnProperty(k)) continue; try { el[k] = p[i][k] } catch(e) {} } break;
    }
  }
  return el;
}

/**
 * simple setInterval/setTimout wrappers
 *
 * @param {Function} f function to be launched
 * @param {Number} i interval
 * @param {Array} o optional function parameters to be applied
 * @return {Number} interval id
 * @scope public
 */
function playInterval (f /* :Function */, i /* :Number */, o /* :Array */) /* :Number */ { return setInterval(function(){(o instanceof Array)?f.apply(this,o):f.call(this,o)},i) }
function playTimeout (f /* :Function */, i /* :Number */, o /* :Array */) /* :Number */ { return setTimeout(function(){(o instanceof Array)?f.apply(this,o):f.call(this,o)},i) }

/**
 *  Clone object
 *
 *  @param optional object to clone
 *  @return cloned object
 *  @access public
 */
function cloneObject (obj) {
   if (isScalar(obj) || isFunction(obj) || null == obj) return obj;
   try { var newObject = new obj.constructor(); } catch(e) {return null;}
   if (isArray(newObject)) {
       for (var i=0,oL=obj.length;i<oL;i++) {
           newObject[i] = cloneObject(obj[i]);
       }
   } else {
       for (var i in obj) {
           if (!obj.hasOwnProperty(i)) continue;
           newObject[i] = cloneObject(obj[i]);
       }
   }
   return newObject;
}

/**
 *  Merges multiple objects to one
 *
 *  @param {Object} obj1 original object
 *  @param {Object} obj2 update object
 *  @param {Object} objN update object
 *  @return {Object} 
 *  @access public
 */
function mergeObject () {
    var res = {}
       ,oi
       ,obj
    for (var z=0,aL=arguments.length;z<aL;z++) {
        obj = arguments[z];
        for (var i in obj) {
            if (!obj.hasOwnProperty(i)) continue;
            oi = obj[i];
            if (null == oi) {
                if (!res.hasOwnProperty(i)) res[i] = oi;
            } else if (isArray(oi)) {
                if (isArray(res[i]))
                    res[i] = res[i].concat(oi).unique();
                else
                    res[i] = oi.slice(0);
            } else if (isScalar(oi) || isFunction(oi)) {
                res[i] = oi;
            } else {
                if (res.hasOwnProperty(i))
                    res[i] = mergeObject(res[i],oi)
                else
                    res[i] = cloneObject(oi);
            }
        }
    }
    return res;
}


//-----------------------------------------------------------------------------
//  Loaders
//-----------------------------------------------------------------------------
/**
 *  Method to load stylesheets
 *
 *  @param {String} sn path to stylesheet
 *  @return {HTMLLinkElement} reference to the corresponding link
 */
function loadStyleSheet (sn) { 
    if (!hasStyleSheet(sn)) {
        var head = document.getElementsByTagName('head')[0]
           ,link = document.createElement('link');
        link.rel = 'stylesheet';
        link.type = 'text/css';
        link.href = sn;
        head.appendChild(link);
    }
}

/**
 *  Checks if the stylesheet already added
 *
 *  @param {String} path stylesheet location
 */
function hasStyleSheet (path) {
    var h =document.getElementsByTagName('html')[0].innerHTML 
       ,sr=new RegExp('<link[^>]+?src\s*=\s*["\']?([^>]+?/)'+sn+'[^>]*>','i');
    return sr.test(h);
}