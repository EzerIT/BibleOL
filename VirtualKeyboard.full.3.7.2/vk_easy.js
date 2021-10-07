/**
 *  Anonymous class for generic operations with the VK
 *
 *  This script has been designed to provide easy install of the VK to the user sites.
 *  Script usage:
 *  1. Install VirtualKeyboard to the target site (eg. to "/path/to/VK" folder)
 *  2. Include string <script src="/path/to/VK/vk_easy.js" type="text/javascript"></script> in the website head tag
 *  3. Define CSS class "keyboardInput" on each text field or iframe, which should take an input from VK (eg. <input type="text" class="keyboardInput" />
 *
 *  Note, you could use any URL parameters in form of ?vk_skin=param1&vk_layout=param2
 *  1. vk_skin, referring to the theme in the "css" folder, used as the current skin (eg. vk_skin=flat_gray)
 *  2. vk_layout, referencing to one of the existing layouts, http://debugger.ru/projects/virtualkeyboard/layouts (e.g. vk_layout=RU%20Russian)
 *
 *  This software is protected by patent No.2009611147 issued on 20.02.2009 by Russian Federal Service for Intellectual Property Patents and Trademarks.
 *
 *  @author Ilya Lebedev
 *  @copyright 2006-2011 Ilya Lebedev <ilya@lebedev.net>
 *  @version $Rev: 546 $
 *  @lastchange $Author: wingedfox $ $Date: 2009-02-27 11:53:11 +0300 (Пт, 27 фев 2009) $
 */
(function (){
    /**
     *  Script info, .0 - path to the script, .1 - script query
     *
     *  @scope private
     */
    var scriptInfo = (function (sname){var h =document.getElementsByTagName('html')[0].innerHTML,sr=new RegExp('<scr'+'ipt[^>]+?src\\s*=\\s*["\']?([^>]+?/|)('+sname+')([^"\'\\s]*)[^>]*>(.|[\r\n])*?</scr'+'ipt>','i'),m =h.match(sr);if (m) {if (m[1].match(/^((https?|file)\:\/{2,}|\w:[\\])/)) return [m[1],m[3]];if (m[1].indexOf("/")==0) return [m[1],m[3]];var b = document.getElementsByTagName('base');if (b[0] && b[0].href) return [b[0].href+m[1],m[3]];return [(document.location.href.match(/(.*[\/\\])/)[0]+m[1]).replace(/^\/+/,""),m[3]];}return null;})
                      ('vk_easy.js')

    /** 
     *  Currently focused node
     *
     *  @scope private
     */
       ,currentTarget

    /**
     *  CSS class name used to turn VK on
     *
     *  @scope private
     */
       ,cssClass = 'keyboardInput'

    /**
     *  Timeout id for hiding an keyboard icon
     *
     *  @scope private
     */
       ,iconHideTimeout
    /**
     *  Final fields - icon and VK container
     */
       ,icon,vk;
        
    /**
     *  Places icon to the right-bottom corner of the input field
     *
     *  @param {HTMLElement} el target input
     *  @scope private
     */
    var placeIcon = function(el) {
        var img = icon.firstChild;
        img.style.visibility = 'hidden';
        img.style.top=0;
        img.style.left=0;
        if (el.parentNode != icon.parentNode) {
            el.parentNode.insertBefore(icon,el);
        }
        updateIconState();

        var offEl = DOM.getOffset(el)
           ,offImg = DOM.getOffset(img);

        img.style.top = offEl.y-offImg.y+offEl.height-offImg.height+"px";
        img.style.left = offEl.x-offImg.x+offEl.width+"px";
        img.style.visibility = 'visible';
    }

    /**
     *  Simply switches icon image on/off state
     *
     *  @scope private
     */
    var updateIconState = function() {
        var img = icon.firstChild;
        if (currentTarget != VirtualKeyboard.getAttachedInput()) {
            img.src = img.src.replace(/jsvk[^.]*/,"jsvk");
        } else {
            img.src = img.src.replace(/jsvk[^.]*/,"jsvk_off");
        }
    }

    /**
     *  Handler for looking for the hover elements and attach keyboard icon whether needed
     *
     *  @param {Event} e mouseover/mouseout events
     *  @scope protected
     */
    var inputHover = function (e) {
        var el = e.srcElement||e.target
           ,tagName = (el.tagName || "").toLowerCase()
           ,type = (el.type || "").toLowerCase()

        if (('textarea' == tagName
           ||'input' == tagName && ('text' == type || 'password' == type)
           ||'iframe' == tagName && ('on' == (el.contentWindow.document.designMode || "").toLowerCase()
                                            ||el.contentWindow.document.body.contentEditable)
            ) && DOM.CSS(el).hasClass('keyboardInput')
           ) {
            currentTarget = el;

            placeIcon(el);

            clearTimeout(iconHideTimeout);
            iconHideTimeout = null;
        } else if (!iconHideTimeout && el != icon.firstChild) {
            iconHideTimeout = setTimeout(iconHide, 200);
        }
    }

    /**
     *  Hides or moves icon to the original field
     *
     *  @scope private
     */
    var iconHide = function () {
        if (VirtualKeyboard.isOpen()) {
            var ct = VirtualKeyboard.getAttachedInput();
            if (currentTarget != ct) {
                currentTarget = ct;
                placeIcon(ct);
            }
        } else if (currentTarget) {
            icon.parentNode.removeChild(icon);
            currentTarget = null;
        }
    }

    /**
     *  Handler for open/close of the VK
     *
     *  @scope protected
     */
    var iconClick = function () {
        if (currentTarget != VirtualKeyboard.getAttachedInput()) {
            var container = vk.firstChild;
            container.style.top=0;
            container.style.left=0;
            icon.parentNode.insertBefore(vk,currentTarget);
            var offCt = DOM.getOffset(currentTarget)
               ,offVk = DOM.getOffset(container)

            container.style.top = offCt.y-offVk.y+offCt.height+"px";
            container.style.left = offCt.x-offVk.x+"px";
            VirtualKeyboard.toggle(currentTarget, container);
            clearTimeout(iconHideTimeout);
            iconHideTimeout = null;
        } else {
            VirtualKeyboard.close();
            vk.parentNode.removeChild(vk);
            iconHideTimeout = setTimeout(iconHide, 1000);
        }
        updateIconState();
        return false;
    }

    ;(function () {
        var scriptPath = scriptInfo[0]
           ,scriptQuery = scriptInfo[1]
        // keyboard icon
        icon = document.createElement('span');
        icon.id = "VirtualKeyboardIcon";
        icon.style.cssText = "width: 0px; height: 0px; position: relative; overflow: visible";
        icon.innerHTML = "<img src='"+scriptPath+"img/jsvk.gif' alt='VirtualKeyboard' style='position:absolute; border:0; margin:0; padding:0; display: block; z-index: 1;' />";
        icon.firstChild.onmouseup = iconClick;

        // keyboard itself
        vk = document.createElement('span');
        vk.innerHTML = "<div style='position: absolute; display: block; margin:0; padding: 0; overflow:visible'></div>";
        
        // load VK
        if (document.body) {
            var scr = document.createElement('script');
            scr.type="text/javascript";
            scr.src = scriptPath+"vk_loader.js"+(scriptQuery||"");
            document.body.appendChild(scr);
        } else {
            document.write("<scr"+"ipt src='"+scriptPath+"vk_loader.js'"+(scriptQuery|"")+" type='text/javascript'></scr"+"ipt>");
        }
        
        if (window.addEventListener) 
            window.addEventListener('load', function(){EM.addEventListener(document.body, 'mouseover', inputHover);}, false);
        else if (window.attachEvent)
            window.attachEvent('onload', function(){EM.addEventListener(document.body, 'mouseover', inputHover);}, false);
    })();
})();