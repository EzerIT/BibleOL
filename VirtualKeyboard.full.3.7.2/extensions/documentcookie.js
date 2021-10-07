/**
 * $Id$
 *  $HeadURL: https://svn.debugger.ru/repos/jslibs/BrowserExtensions/trunk/documentselection.js $
 *
 * Script is used to manipulate cookies.
 *
 * @version $Rev$
 * @title ObjectCookie
 * @author Ilya Lebedev ilya@lebedev.net, (c) 2004-2009
 * @class DocumentCookie
 **/
DocumentCookie = new function () {
  var self = this;
  /**
   *  Cookies storage
   *
   *  @param Object
   *  @scope private
   */
  var cookie = {};

  /**
   *  Cookie getter
   *
   *  @param {String} name
   *  @return {String} value
   *  @scope public
   */
  self.get = function (name) {
      return cookie[name];
  }

  /**
   *  Cookie setter
   *
   *  @param {String} name
   *  @param {String} value
   *  @parma {Date, Number} exprire expiration date, either Date object with the desired date or Number of the seconds from now till expiration
   *  @param {Stinrg} path path within the domain
   *  @param {String} domain domain
   *  @param {Boolean} secure for https connections only
   *  @return {Boolean} was cookie set or not
   *  @scope public
   */
  self.set = function (name, value, expire, path, domain, secure) {
      if (name) {
          value = escape(value);
          document.cookie = name + "=" + value
                         + (path ? ";path=" + path
                                 : "")
                         + (expire ? ";NoExp=" + ((expire instanceof Date)?expire.toGMTString()
                                                                          :new Date(new Date().getTime()+expire*1000).toGMTString())
                                   : "")
                         + (domain ? ";domain=" + domain || document.location.domain
                                   : "")
                         + (secure ? ";secure"
                                   : "");
          cookie[name] = value;
          return true;
      }
      return false;
  }

  /**
   *  Does cookie exists?
   *
   *  @param {String} name
   *  @return {Boolean} exists or not
   */
  self.isSet = function (name) {
      return !!cookie[name];
  }

  /**
   *  Removes particular cookie
   *
   *  @param {String} name
   *  @param {Stinrg} path path within the domain
   *  @param {String} domain domain
   *  @param {Boolean} secure for https connections only
   *  @return {Boolean} removal success
   *  @scope public
   */
  self.del = function (name,path,domain) {
      if (Cookie.isSet(name)) {
          document.cookie = name + "="
                         + (path ? "; path=" + path : "")
                         + (domain ? "; domain=" + domain : "")
                         + (secure ? "; secure" : "")
                         + "; NoExp=Thu, 01-Jan-70 00:00:01 GMT";
          delete cookie[name];
          return true;
      }
      return false;
  }

  /**
   *  Removes all cookies
   *
   *  @scope public
   */
  self.delAll = function () {
      for (var i in cookie) {
          if (cookie.hasOwnProperty(i))
              self.del(i);
      }
  }

  ;(function () {
      var p = document.cookie.split(/\s*;\s*/);
      for (var i=0,pL=p.length; i<pL; i++) {
          var parts = p[i].split(/\s*=\s*/);
          cookie[parts[0]] = unescape(parts[1]);
      }
  })()
}