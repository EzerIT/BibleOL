/**
 *  $Id: vk_iframe.js 774 2011-06-02 19:57:32Z wingedfox $
 *
 *  Keyboard Iframe mode loader
 *
 *  This software is protected by patent No.2009611147 issued on 20.02.2009 by Russian Federal Service for Intellectual Property Patents and Trademarks.
 *
 *  @author Ilya Lebedev
 *  @copyright 2006-2011 Ilya Lebedev <ilya@lebedev.net>
 *  @version $Rev: 774 $
 *  @lastchange $Author: wingedfox $ $Date: 2011-06-02 23:57:32 +0400 (Thu, 02 Jun 2011) $
 *  @class IFrameVirtualKeyboard
 *  @constructor
 */

IFrameVirtualKeyboard = new function() {
    var self = this;
    /**
     *  popup window handler
     * 
     *  @type {Window}
     *  @scope private
     */
    var hWnd = null;
    /**
     *  Frame with the keyboard
     *
     *  @type {HTMLIFrame}
     *  @scope private
     */
    var iFrame = null;
    /**
     *  path to this file
     * 
     *  @type {String}
     */
    var p = (function (sname) {var h =document.getElementsByTagName('html')[0].innerHTML,sr=new RegExp('<scr'+'ipt[^>]+?src\\s*=\\s*["\']?([^>]+?/|)'+sname+'([^"\'\\s]*)[^>]*>(.|[\r\n])*?</scr'+'ipt>','i'),m =h.match(sr);if (m) {if (m[1].match(/^((https?|file)\:\/{2,}|\w:[\\])/)) return [m[1],m[2]];if (m[1].indexOf("/")==0) return [m[1],m[2]];var b = document.getElementsByTagName('base');if (b[0] && b[0].href) return [b[0].href+m[1],m[2]];return [(document.location.href.match(/(.*[\/\\])/)[0]+m[1]).replace(/^\/+/,""),m[2]];}return [null,null];})('vk_iframe.js');
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
        if (hWnd&&hWnd.WirtualKeyboard)
            return hWnd.WirtualKeyboard.attachInput(el);
        return false;
    }
    /**
     *  Shows keyboard
     *
     *  @param {HTMLElement, String} input element or it to bind keyboard to
     *  @return {Boolean} operation state
     *  @scope public
     */
    self.open =
    self.show = function (target,holder) {
        var retval = false;
        if ('string' == typeof holder)
            holder = document.getElementById(holder);

        if ('string' == typeof target) 
            target = document.getElementById(target);
        if (target)
            tgt = target;
        if (!tgt)
            return false;

        if (!hWnd) {
            if (holder) {
                /*
                *  create frame only if holder passed
                */
                var div = document.createElement('div');
                div.innerHTML = "<iframe frameborder=\"0\" src=\""+p[0]+"vk_iframe.html"+p[1]+"\"></iframe>";
                iFrame = div.firstChild;
                retval = true;
            }
        } else if (!self.isOpen()) {
            iFrame.style.display = 'block';
            if (hWnd.WirtualKeyboard) {
                hWnd.WirtualKeyboard.show( tgt
                                          ,hWnd.document.body
                                         );
            }
            iFrame.style.height = hWnd.document.body.firstChild.offsetHeight+'px';
            iFrame.style.width = hWnd.document.body.firstChild.offsetWidth+'px';

            retval = true;
        }

        if (holder && iFrame && holder != iFrame.parentNode) {
            /*
            *  if another valid holder passed, attach keyboard there
            */
            holder.appendChild(iFrame);
        }

        return retval;
    }
    /**
     *  Hides keyboard
     *
     *  @scope public
     */
    self.close = 
    self.hide = function (target) {
        if (self.isOpen()) {
            iFrame.style.display='none';
            if (hWnd.WirtualKeyboard) {
                hWnd.WirtualKeyboard.close();
            }
        }
    }
    /**
     *  Returns open state
     *
     *
     */
    self.isOpen = function () {
        return iFrame&&'block'==iFrame.style.display;
    }
    /**
     *  Toggles keyboard state
     *
     *  @param {HTMLElement, String} input element or it to bind keyboard to
     *  @param {HTMLElement, String} holder to draw keyboard in
     *  @return {Boolean} operation state
     *  @access public
     */
    self.toggle = function (input,holder) {
        self.isOpen()?self.close():self.open(input,holder);
    }
    /**
     *  Onload callback event, invoked from the target window when onload event fires
     *
     *  @scope protected
     */
    self.onload = function () {
        hWnd = (iFrame.contentWindow||iFrame.contentDocument.window);
        this.close();

        /*
        *  set class names to add some styling to html, body
        */
        hWnd.document.body.className = hWnd.document.body.parentNode.className = 'VirtualKeyboardPopup';
        while (hWnd.document.body.firstChild) {
                hWnd.document.body.removeChild(hWnd.document.body.firstChild);
        }
        this.show();
    }
}
