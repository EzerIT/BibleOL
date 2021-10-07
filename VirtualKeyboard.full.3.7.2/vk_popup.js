/**
 *  $Id: vk_popup.js 774 2011-06-02 19:57:32Z wingedfox $
 *
 *  Keyboard Iframe mode loader
 *
 *  This software is protected by patent No.2009611147 issued on 20.02.2009 by Russian Federal Service for Intellectual Property Patents and Trademarks.
 *
 *  @author Ilya Lebedev
 *  @copyright 2006-2011 Ilya Lebedev <ilya@lebedev.net>
 *  @version $Rev: 774 $
 *  @lastchange $Author: wingedfox $ $Date: 2011-06-02 23:57:32 +0400 (Thu, 02 Jun 2011) $
 *  @class PopupVirtualKeyboard
 *  @constructor
 */
PopupVirtualKeyboard = new function() {
    var self = this;
    /**
     *  popup window handler
     * 
     *  @type {Window}
     *  @scope private
     */
    var hWnd = null;
    /**
     *  Unload handler
     *
     *  @type {Function}
     *  @scope private
     */
    var unloadHandler;
    /**
     *  path to this file
     * 
     *  @type {String}
     */
    var p = (function (sname) {var h =document.getElementsByTagName('html')[0].innerHTML,sr=new RegExp('<scr'+'ipt[^>]+?src\\s*=\\s*["\']?([^>]+?/|)'+sname+'([^"\'\\s]*)[^>]*>(.|[\r\n])*?</scr'+'ipt>','i'),m =h.match(sr);if (m) {if (m[1].match(/^((https?|file)\:\/{2,}|\w:[\\])/)) return [m[1],m[2]];if (m[1].indexOf("/")==0) return [m[1],m[2]];var b = document.getElementsByTagName('base');if (b[0] && b[0].href) return [b[0].href+m[1],m[2]];return [(document.location.href.match(/(.*[\/\\])/)[0]+m[1]).replace(/^\/+/,""),m[2]];}return [null,null];})('vk_popup.js');
    /**
     *  Tells, if the keyboard is open
     * 
     *  @return {Boolean}
     *  @scope public
     */
    self.isOpen = function () {
        return null!=hWnd && !hWnd.closed;
    }
    /**
     *  Target input
     *
     *  @type {String}
     *  @scope private
     */
    var tgt = null;
    /**
     *  Attaches keyboard to the specified input field
     *
     *  @param {Null, HTMLInputElement,String} element to attach keyboard to
     *  @return {HTMLInputElement, Null}
     *  @access public
     */
    self.attachInput = function(el) {
        if (hWnd && !hWnd.closed && hWnd.WirtualKeyboard) {
            return hWnd.WirtualKeyboard.attachInput(el);
        }
        return false
    }
    /**
     *  Shows keyboard
     *
     *  @param {HTMLElement, String} input element or it to bind keyboard to
     *  @param {Function} unload monitor
     *  @return {Boolean} operation state
     *  @scope public
     */
    self.open =
    self.show = function (target, unload) {
        if (!hWnd || hWnd.closed) {
          var features = ["status=0","title=0","dependent=yes","dialog=yes","resizable=no","scroll=no","scrollbars=no","width=500","height=500"];
          hWnd = (window.showModelessDialog||window.open)(p[0]+"vk_popup.html"+p[1],window,features.join(window.showModelessDialog?";":","));
          unloadHandler = unload;
          tgt = target;
          return true;
        }
        return false;
    }
    /**
     *  Hides keyboard
     *
     *  @scope public
     */
    self.close = 
    self.hide = function (target) {
        if (!hWnd || hWnd.closed) return false;
        if (hWnd.WirtualKeyboard.isOpen()) hWnd.WirtualKeyboard.hide();
        hWnd.close();
        hWnd = null;
        if ('function' == typeof unloadHandler) {
            unloadHandler();
        }
    }
    /**
     *  Toggles keyboard state
     *
     *  @param {HTMLElement, String} input element or it to bind keyboard to
     *  @return {Boolean} operation state
     *  @access public
     */
    self.toggle = function (input) {
        self.isOpen()?self.close():self.open(input);
    }
    /**
     *  Onload callback event, invoked from the target window when onload event fires
     *
     *  @scope protected
     */
    self.onload = function () {
        if ('string' == typeof tgt)
            tgt = document.getElementById(tgt);
        hWnd.WirtualKeyboard.show( tgt
                                  ,hWnd.document.body
                                 );
        /*
        *  set class names to add some styling to html, body
        */
        hWnd.document.body.className = hWnd.document.body.parentNode.className = 'VirtualKeyboardPopup';
        if (hWnd.sizeToContent) {
            hWnd.sizeToContent();
        } else {
            var kbd = hWnd.document.body.firstChild;
            while ("virtualKeyboard" != kbd.id) {
                hWnd.document.body.removeChild(kbd);
                kbd = hWnd.document.body.firstChild;
            }
            hWnd.dialogHeight = kbd.offsetHeight+'px';
            hWnd.dialogWidth = kbd.offsetWidth+'px';
            hWnd.resizeTo(kbd.offsetWidth+hWnd.DOM.getOffsetWidth()-hWnd.DOM.getClientWidth()
                         ,kbd.offsetHeight+hWnd.DOM.getOffsetHeight()-hWnd.DOM.getClientHeight());
        }
        hWnd.onunload = self.close;
    }
    if (window.attachEvent) window.attachEvent('onunload', self.close);
    else if (window.addEventListener) window.addEventListener('unload', self.close, false);
}
