/*
*  $Id: documentselection.js 780 2011-06-22 13:20:20Z wingedfox $
*  $HeadURL: https://svn.debugger.ru/repos/jslibs/BrowserExtensions/tags/BrowserExtensions.029/documentselection.js $
*
*  Class implements cross-browser work with text selection
*
*  @author Ilya Lebedev
*  @author $Author: wingedfox $
*  @modified $Date: 2011-06-22 17:20:20 +0400 (Wed, 22 Jun 2011) $
*  @version $Rev: 780 $
*  @license LGPL
*/
/*
*  @class DocumentSelection
*/
DocumentSelection = new function () {
  var self = this;
  /*
  *  Stores hash of keys, applied to elements
  *
  *  @type Object
  *  @scope private
  */
  var keys = {
     'prevCalcNode' : '__prevCalcNode'
  }
  //---------------------------------------------------------------------------
  //  PRIVATES
  //---------------------------------------------------------------------------
  /**
   *  Calls specified method with the supplied params
   *  This is done to process only correct requests
   *
   *  @param {String} m method to call
   *  @param {Array} arg arguments of [target, param1, paramN]
   *  @return {Object} method call result or false, if any error happened
   *  @scope private
   */
  var callMethod = function (m, arg) {
      var el = arg[0]
         ,id
         ,module = ""
      if (!el || !el.tagName) return false;
      switch (arg[0].tagName.toLowerCase()) {
          case 'input':
              if (["button", "checkbox", "hidden", "image", "radio", "reset", "submit"].indexOf((el.type || "").toLowerCase()) > -1) return false;
          case 'textarea':
              module = "input";
              break;
          case 'iframe':
              module = "frame";
              arg[0] = el.contentWindow;
              break;
          default:
              return false;
      }
      /*
      *  instantiate the module
      */
      if ('function' == typeof self.module[module]) 
          self.module[module] = new self.module[module](keys);
      /*
      *  throw the exception, is method is not implemented
      */
      if (!self.module[module] || !self.module[module][m])
          throw new Error ('Method \''+m+'\' is not implemented for DocumentSelection \''+module+'\' module.');

      return self.module[module][m].apply(self, arg);
  }
  /**
   *  Keeps scrolling on the place for browsers, those don't support this natively
   *
   *  @param {HTMLElement} el target element
   *  @param {Number} ot old scrollTop property
   *  @param {Number} ol old scrollLeft property
   *  @scope private
   */
  var keepScroll = function (el,ot,ol) {
      if (window.getSelection && 'iframe'!=el.tagName.toLowerCase()) {
          var q = self.getSelectionOffset(el)
          if (el.contentWindow) el = el.contentWindow.document.body;

          var dy = q.y-ot;
          if (dy<0)                        el.scrollTop = q.y;
          else if (dy+q.h>el.clientHeight) el.scrollTop = q.y-el.clientHeight/2;
          else                             el.scrollTop = ot;

          if (ol>q.x)                      el.scrollLeft = q.x;
          else if (ol+el.clientWidth>q.x)  el.scrollLeft = ol;
          else                             el.scrollLeft = q.x-el.clientWidth/2; 
      }
  }
  //---------------------------------------------------------------------------
  //  SETTERS
  //---------------------------------------------------------------------------
  /**
   *  getSelectionRange wrapper/emulator
   *
   *  @param {HTMLElement}
   *  @param {Number} start position
   *  @param {Number} end position
   *  @param {Boolean} related indicates calculation of range relatively to current start point
   *  @return void
   *  @scope public
   */
  self.setRange = function(el, start, end, related) {
      var ot = el.scrollTop
         ,ol = el.scrollLeft
      /*
      *  set range on relative coordinates
      */
      if (related) {
          var st = self.getStart(el);
          end = st+end;
          start = st+start;
      }
      if (start < 0) start = 0;
      if (end < start) end = start;

      callMethod ('setRange',[el,start,end]);

      keepScroll(el,ot,ol);
  }
  //---------------------------------------------------------------------------
  //  GETTERS
  //---------------------------------------------------------------------------
  /**
   *  Return contents of the current selection
   *
   *  @param {HTMLElement} el element to look position on
   *  @return {String}
   *  @scope public
   */
  self.getSelection = function(el) {
      return callMethod('getSelection',[el]);
  }
  /**
   *  getSelectionStart wrapper/emulator
   *  adapted version
   *
   *  @param {HTMLElement} el element to calculate end position for
   *  @return {Number} start position
   *  @scope public
   */
  self.getStart = function (el) {
      return callMethod('getPos',[el])[0];
  }
  /*
  *  getSelectionEnd wrapper/emulator
  *  adapted version
  *
  *  @param {HTMLElement} el element to calculate end position for
  *  @return {Number} start position
  *  @scope public
  */
  self.getEnd = function (el) {
      return callMethod('getPos',[el])[0];
  }
  /*
  *  Return cursor position for supplied field
  *
  *  @param {HTMLElement} element to get cursor position from
  *  @return {Number} position
  *  @scope public
  */
  self.getCursorPosition = function (el) {
      return self.getStart(el);
  }
  //---------------------------------------------------------------------------
  //  MISC FUNCTIONS
  //---------------------------------------------------------------------------
  /*
  *  Insert text at cursor position
  *
  *  @param {HTMLElement} text field to insert text
  *  @param {String} text to insert
  *  @scope public
  */
  self.insertAtCursor = function (el, val, keep) {
      var ot = el.scrollTop
         ,ol = el.scrollLeft
      if (!keep) {
          callMethod('del',[el]);
      }
      var pos = callMethod('ins',[el,val]);
      keepScroll(el,ot,ol);
      return pos;
  }
  /*
  *  Wraps selection with start and end text
  *
  *  @param {HTMLElement} text field to insert text
  *  @param {String} start text at the beginnging of the selection
  *  @param {String} end text at the end of the selection
  *  @scope public
  */
  self.wrapSelection = function (el, start, end) {
    var s = self.getCursorPosition(el)
       ,e = self.getEnd(el)
    if (s==e) {
        self.insertAtCursor(el,start+end);
    } else {
        self.insertAtCursor(el,start,true);
        self.setRange(el,e+start.length,e+start.length);
        self.insertAtCursor(el,end,true);
    }
  }

  /*
  *  Deletes char at cursor position
  *
  *  @param {HTMLElement} text field to delete text
  *  @param {Boolean} delete text before (backspace) or after (del) cursor
  *  @scope public
  */
  self.deleteAtCursor = function (el, after) {
      if (!self.getSelection(el)) {
          if (after)
              self.setRange(el,0,1,true);
          else
              self.setRange(el,-1,0,true);
      }
      return self.deleteSelection(el);
  }
  /**
   *  Removes the selection, if available
   * 
   *  @param {HTMLElement} el field to delete text from
   *  @scope public
   */
  self.deleteSelection = function (el) {
      var ol = el.scrollLeft
         ,ot = el.scrollTop
         ,ret = callMethod('del',[el]);
      keepScroll(el,ot,ol);
      return ret;
  }
  /**
   *  Method is used to caclulate pixel offsets for the selection in TextArea (other inputs are not tested yet)
   *
   *  @param {HTMLTextareaElement} el target to calculate offsets
   *  @return {Object} {x: horizontal offset, y: vertical offset, h: height offset}
   *  @scope public
   */
  self.getSelectionOffset = function (el) {
      return callMethod('getSelectionOffset',[el],true);
  }
  /**
   *  Method is used to return cursor context within the current "word"
   *
   *  @param {HTMLTextareaElement} el target to calculate offsets
   *  @return {Array} 0 - part of word before the cursor, 1 - part after
   *  @scope public
   */
  self.getContext = function (el) {
      return callMethod('getContext',[el]);
  }
}
DocumentSelection.module = {
    /**
     *  Module processing selection in the 'input' and 'textarea' fields
     *
     *  @param {Object} keys properties, registered for use in DS
     *  @scope protected
     */
    'input' : function (keys) {
        var self=this;
        /**
         *  Special document node, used to calculate range offsets in Mozilla
         *
         *  @type HtmlDivElement
         *  @scope private
         */
        var offsetCalculator = null;
        /**
         *  Returns the cursor context
         *
         *  @param {HTMLInputElement, HTMLTextareaElement} el input or textarea to get context from
         *  @param {Boolean} start get start or end selection position
         *  @return {Number} offset from the beginning
         *  @scope private
         */
        self.getContext = function (el) {
            var pos = self.getPos(el)
               ,val = el.value
               ,r1 = val.match(new RegExp("(?:.|[\\r\\n]){0,"+(pos[0]-1)+"}(?:^|\\s)","m")) || ""
               ,r2 = val.match(new RegExp("(?:.|[\\r\\n]){"+pos[0]+"}","m"))[0]
               ,r3 = val.replace(r2,"")
               ,r4 = r3.substring(0,pos[1]-pos[0])
               ,r5 = (r3.replace(r4,"")).match(/(?:\S|$)*/)
            return [r2.replace(r1,""),r4,r5];
        }
        /**
         *  Returns selection start or end position in absolute chars from the field start
         *
         *  @param {HTMLInputElement, HTMLTextareaElement} el input or textarea to get position from
         *  @return {Number} offset from the beginning
         *  @scope private
         */
        self.getPos = function (el) {
            var val = el.value;
            var pos = [val.length,val.length];
            if ('function' == typeof window.getSelection) {
                try {
                    pos = [el.selectionStart,el.selectionEnd];
                } catch (e) {
                }
            } else if (window.document.selection) {
                el.setActive();
                var sel = el.document.selection.createRangeCollection()[0];
                if (el.tagName.toLowerCase() == "textarea") {
                    var c = sel.duplicate();
                    c.moveToElementText(el);

                    var l = (window.opera?val:val.replace(/\r/g,"")).length;

                    c.setEndPoint('StartToEnd', sel);
                    var st = 0+l-(window.opera?c.text:c.text.replace(/\r/g,"")).length;
                    c.setEndPoint('StartToStart', sel);
                    var en = 0+l-(window.opera?c.text:c.text.replace(/\r/g,"")).length;
                    pos[0] = Math.min(st,en);
                    pos[1] = Math.max(st,en);
                } else {
                    var clone = el.createTextRange();

                    clone.setEndPoint('EndToStart', sel);
                    pos[0] = (window.opera?clone.text:clone.text.replace(/\r/g,"")).length;

                    clone.setEndPoint('EndToEnd', sel);
                    pos[1] = (window.opera?clone.text:clone.text.replace(/\r/g,"")).length;
                }
            }
            return pos;
        }
        /**
         *  Removes the selection, if available
         * 
         *  @param {HTMLElement} el field to delete text from
         *  @return {String} deleted substring
         *  @scope public
         */
        self.del = function (el) {
            var ret = ""
               ,p = self.getPos(el)
               ,s = p[0]
               ,e = p[1];
            if (s!=e) {
                /*
                *  check for IE, because Opera uses \r\n sequence, but calculate positions correctly
                */
                var tmp = document.selection&&!window.opera?el.value.replace(/\r/g,""):el.value;
                ret = tmp.substring(s,e);
                el.value = tmp.substring(0, s)+tmp.substring(e,tmp.length);
                self.setRange(el,s,s);
            }
            return ret;
        }
        /**
         *  Inserts text to the textarea
         *
         *  @param {HTMLElement} text field to insert text
         *  @param {String} text to insert
         *  @return {Number} new cursor position
         *  @scope public
         */
        self.ins = function (el,val) {
            var ret = ""
               ,s = self.getPos(el)[0]
               ,oLen = el.value.length;
            /*
            *  check for IE, because Opera uses \r\n sequence, but calculate positions correctly
            */
            var tmp = document.selection&&!window.opera?el.value.replace(/\r/g,""):el.value;
            el.value = tmp.substring(0,s)+val+tmp.substring(s,tmp.length);
            s += el.value.length - oLen;
            self.setRange(el,s,s);
            return s;
        }
        /**
         *  Return contents of the current selection
         *
         *  @param {HTMLElement} el element to look position on
         *  @param {Number} s start position
         *  @param {Number} e end position
         *  @return {String}
         *  @scope public
         */
        self.getSelection = function (el) {
            var p = self.getPos(el)
               ,s = p[0]
               ,e = p[1];
            /*
            *  w/o this check content might be duplicated on delete
            */
            if (e<s) e = s;
            /*
            *  check for IE, because Opera does use \r\n sequence, but calculate positions correctly
            */
            var tmp = document.selection&&!window.opera?el.value.replace(/\r/g,""):el.value;
            return tmp.substring(s,e);
        }
        /**
         *  Sets the selection range
         *
         *  @param {HTMLElement}
         *  @param {Number} start position
         *  @param {Number} end position
         *  @return void
         *  @scope public
         */
        self.setRange = function (el,start,end) {
            if ('function' == typeof el.setSelectionRange) {
                /*
                *  for Mozilla
                */
                try {el.setSelectionRange(start, end)} catch (e) {}
                var p = self.getPos(el);
            } else {
                /*
                *  for IE
                */
                var range;
                /*
                *  just try to create a range....
                */
                range = el.createTextRange();
                el.setActive();
                range.collapse(true);
                range.moveStart("character", start);
                range.moveEnd("character", end - start);
                range.select();
            }
        }
        /**
         *  Method is used to caclulate pixel offsets for the selection in TextArea (other inputs are not tested yet)
         *
         *  @param {HTMLTextareaElement} el target to calculate offsets
         *  @return {Object} {x: horizontal offset, y: vertical offset, h: height offset}
         *  @scope public
         */
        self.getSelectionOffset = function (el) {
            var range
               ,doc = DOM.getWindow(el).document;
            if ('function' == typeof el.setSelectionRange) {
                /*
                *  For Mozilla
                */
                if (!offsetCalculator) {
                    /*
                    *  create hidden div, which will 'emulate' the textarea
                    *  it's put 'below the ground', because toggling block/none is too expensive
                    */
                    offsetCalculator = doc.createElement('td');
            
                    doc.body.appendChild(offsetCalculator);
                }
                /*
                *  store the reference to last-checked object, to prevent recalculation of styles
                */
                if (offsetCalculator[keys.prevCalcNode] != el) {
                    offsetCalculator[keys.prevCalcNode] = el;
                    var cs = doc.defaultView.getComputedStyle(el, null);
                    for (var i in cs) {
                        try {if (cs[i] && 'content' != i) offsetCalculator.style[i] = cs[i];}catch(e){}
                    }
                    offsetCalculator.style.overflow = 'auto';
                    offsetCalculator.style.position = 'absolute';
                    offsetCalculator.style.visibility = 'hidden';
                    offsetCalculator.style.zIndex = '-10';
                    offsetCalculator.style.left="-10000px";
                    offsetCalculator.style.top="-10000px";
                    offsetCalculator.style.clip = "";
                    offsetCalculator.style.maxWidth = "";
                    offsetCalculator.style.maxHeight = "";
                    offsetCalculator.style.backgroundColor = 'yellow';
                }
                /*
                *  caclulate offsets to target and move div right below it
                */
                var range = doc.createRange()
                   ,val = el.value || " ";
            
                if ('input'==el.tagName.toLowerCase()) {
                    offsetCalculator.style.width = 'auto'
                    offsetCalculator.style.whiteSpace =  'nowrap';
                } else {
                    offsetCalculator.style.whiteSpace = 'off'==el.getAttribute('wrap')?"pre":"";
                }
                
                val = val.replace(/\x20\x20/g,"\x20\xa0").replace(/</g,"&lt;").replace(/>/g,"&gt").replace(/\r/g,"");
                offsetCalculator.innerHTML = ( val.substring(0,el.selectionStart-1)+"<span>"+val.substring(el.selectionStart-1,el.selectionStart)+"\xa0</span>"
                                              +val.substring(el.selectionStart)).replace(/\n/g,"<br />")
                                                                                .replace(/\t/g,"<em style=\"white-space:pre\">\t</em>")
                /*
                *  span is used to find the offsets
                */
                var span = offsetCalculator.getElementsByTagName('span')[0];
                span.style.border = '1px solid red';
                range.offsetLeft = span.offsetLeft;
                range.offsetTop = span.offsetTop;
                range.offsetHeight = span.offsetHeight;
                span = null;
            } else if (doc.selection && doc.selection.createRange) {
                /*
                *  For IE
                */
                range = doc.selection.createRange();
                /*
                *  IE does not allow to calculate lineHeight, but this check is easy
                */
                range.offsetHeight = Math.round(range.boundingHeight/(range.text.replace(/[^\n]/g,"").length+1));
                if (el.tagName && 'textarea'==el.tagName.toLowerCase()) {
                    var xy = DOM.getOffset(el)
                    range = {
                        'offsetTop' : range.offsetTop+el.scrollTop-xy.y+DOM.getBodyScrollTop(el)
                       ,'offsetLeft' : range.offsetLeft+el.scrollLeft-xy.x+DOM.getBodyScrollLeft(el)
                       ,'offsetHeight' : range.offsetHeight
                    }
                }
            }
            if (range) {
                return {'x': range.offsetLeft, 'y': range.offsetTop, 'h': range.offsetHeight};
            }
            return {'x': 0, 'y': 0, 'h': 0};
        }
    }
   ,'frame' : function () {
        var self=this;
        /**
         *  Returns the cursor context
         *
         *  @param {HTMLInputElement, HTMLTextareaElement} el input or textarea to get context from
         *  @param {Boolean} start get start or end selection position
         *  @return {Number} offset from the beginning
         *  @scope private
         */
        self.getContext = function (el) {
            if ('function' == typeof el.getSelection) {
                var pos = self.getPos(el)
                   ,val = el.document.body.innerText || el.document.body.innerHTML.replace(/<\/?[a-z:]+[^>]*>/ig,"").replace("&nbsp;"," ")
                   ,r1 = val.match(new RegExp("(?:.|[\\r\\n]){0,"+(pos[0]-1)+"}(?:^|\\s)","m")) || ""
                   ,r2 = val.match(new RegExp("(?:.|[\\r\\n]){"+pos[0]+"}","m")) || ""
                   ,r3 = val.replace(r2,"")
                   ,r4 = r3.substring(0,pos[1]-pos[0])
                   ,r5 = (r3.replace(r4,"")).match(/(?:\S|$)*/)
                return [r2.toString().replace(r1,""),r4,r5];
            } else {
                var s1 = el.document.selection.createRange()
                   ,s2 = el.document.selection.createRange()
                   ,s3 = el.document.selection.createRange()
                s1.moveStart("word", -1)
                s3.moveEnd("word", 1)

                return [s1.text.replace(new RegExp(RegExp.escape(s2.text)+"$"),"")
                       ,s2.text
                       ,s3.text.replace(new RegExp("^"+RegExp.escape(s2.text)),"")];
            }
        }
        /**
         *  Returns selection start or end position in absolute chars from the field start
         *
         *  @param {HTMLInputElement, HTMLTextareaElement} el input or textarea to get position from
         *  @return {Number} offset from the beginning
         *  @scope private
         */
        self.getPos = function (el) {
            var pos = [0,0];
            if ('function' == typeof el.getSelection) {
                /*
                *  we need to calculate both start and end points, because range could be reversed
                *  but we can't move selection end point before start one
                */
                var sel = el.getSelection()
                   ,sn = sel.anchorNode
                   ,so = sel.anchorOffset
                   ,en = sel.focusNode
                   ,eo = sel.focusOffset
                   ,ss = false
                   ,es = false
                   ,sc = 0
                   ,ec = 0
                   ,cn
                   ,tw=el.document.createTreeWalker(el.document.body,NodeFilter.SHOW_TEXT,null,false)
                while (sn && sn.nodeType != 3) {
                    sn = sn.childNodes[so]
                    so = 0;
                }
                while (en && en.nodeType != 3) {
                    en = en.childNodes[eo]
                    eo = 0;
                }
                while (cn=tw.nextNode()) {
                    if (cn == en) {
                        ec += eo
                        es = true
                    }
                    if (cn == sn) {
                        sc += so
                        ss = true
                    }
                    if (!es) ec += cn.nodeValue.length
                    if (!ss) sc += cn.nodeValue.length
                    if (es && ss) break;
                }
                pos = [Math.min(ec,sc),Math.max(ec,sc)]
            } else {
                el.document.body.setActive();
                pos = [Math.abs(el.document.selection.createRange().moveStart("character", -100000000))
                      ,Math.abs(el.document.selection.createRange().moveEnd("character", -100000000))];
            }
            return pos;
        }
        /**
         *  Removes the selection, if available
         * 
         *  @param {HTMLElement} el field to delete text from
         *  @return {String} deleted substring
         *  @scope public
         */
        self.del = function (el) {
            if ('function' == typeof el.getSelection) {
                var s = el.getSelection()
                   ,i = s.rangeCount
                while (--i>-1) s.getRangeAt(i).deleteContents();

                /*
                *  insert empty text node for browsers that loose selection, when it's empty
                */
                var r = s.getRangeAt(s.rangeCount-1)
                r.insertNode(el.document.createTextNode(""))
                s.addRange(r);
            } else if (el.document && el.document.selection) {
                el.document.selection.createRange().text = "";
                el.document.selection.createRange().select();
            }
        }
        /**
         *  Inserts text to the textarea
         *
         *  @param {HTMLElement} text field to insert text
         *  @param {String} text to insert
         *  @scope public
         */
        self.ins = function (el,val) {
            if ('function' == typeof el.getSelection) {
                val = val.replace(/&/,"&amp;").replace(/</,"&lt;").replace(/>/,"&gt;").replace(/\x20/,"&nbsp;").replace(/[\r\n]/,"<br />");
                var n = el.document.createElement('span')
                   ,s = el.getSelection()
                   ,r = s.getRangeAt(0)
                   ,ln;
                n.innerHTML = val;
                r.insertNode(n);
                r.selectNodeContents(n);
                
                var pn = n.parentNode
                   ,ln = n.nextSibling

                /*
                *  replace holder node with the extracted document fragment
                */
                n.parentNode.replaceChild(r.extractContents(),n);

                /*
                *  if there's no last child, attempt to set range after the last child in the node
                */
                if (!ln)
                    ln = pn.lastChild;

                var r1 = el.document.createRange();
                /*
                *  if last node is text node
                */
                if (ln.nodeValue) {
                    /*
                    *  move selection to the very beginning of this node
                    */
                    r1.setStart(ln,0);
                } else {
                    /*
                    *  otherwise, move selection after the newly created node,
                    *  it's actual when creating line breaks
                    */
                    r1.setStartAfter(ln);
                }

                /*
                *  remove any existing selection
                *  and create the new one
                */
                s.removeAllRanges();
                s.addRange(r1);

            } else if (el.document && el.document.selection) {
                el.document.body.setActive();
                var r = el.document.selection.createRange();
                r.text = val;
                /*
                *  move selection only if there's a space to move it
                */
                if (r.moveStart("character", 1)) {
                    r.moveStart("character", -1);
                    r.moveEnd("character", -1);
                    r.select();
                }
            }
            return self.getPos(el)[0];
        }
        /**
         *  Return contents of the current selection
         *
         *  @param {HTMLElement} el element to look position on
         *  @param {Number} s start position
         *  @param {Number} e end position
         *  @return {String}
         *  @scope public
         */
        self.getSelection = function (el,s,e) {
            if ('function' == typeof el.getSelection) {
                var s = el.getSelection();
                return s?s.toString():"";
            } else if (el.document && el.document.selection) {
                return el.document.selection.createRange().text;
            }
        }
        /**
         *  Sets the selection range
         *
         *  @param {HTMLElement}
         *  @param {Number} start position
         *  @param {Number} end position
         *  @return void
         *  @scope public
         */
        self.setRange = function (el,start,end) {
            if ('function' == typeof el.getSelection) {
                var sel = el.getSelection();
                sel.removeAllRanges();
                var r = el.document.createRange()
                   ,cnt = 0
                   ,cl = 0
                   ,cn
                   ,pn
                   ,tw=el.document.createTreeWalker(el.document.body,NodeFilter.SHOW_TEXT,null,false);
            
                /*
                *  move start position to the very beginning of the first non-empty text node ( <= in the expression),
                *  it's actual when the actual selection is at the very beginning of the text node
                *  otherwise, selection will be started from the end of some previous node,
                *  this could lead to deletion of the intermediate non-text content
                */
                while ((cn=tw.nextNode())&&(!cn.nodeValue.length||(cnt+cn.nodeValue.length <= start))) {
                    pn = cn;
                    cnt += cn.nodeValue.length;
                }
                /*
                *  explicitly set range borders
                */
                if (cn||(cn=pn)) {
                    r.setStart(cn,start-cnt);
                    r.setEnd(cn,start-cnt);
                }
                if (cn) {
                    do {
                        if (cn.nodeType != 3) continue;
                        if (cnt+cn.nodeValue.length < end) {
                            cnt += cn.nodeValue.length;
                        } else {
                            r.setEnd(cn,end-cnt);
                            break;
                        }
                    } while (cn=tw.nextNode())
                }
                sel.addRange(r);
            } else if (el.document && el.document.selection) {
                el.document.body.setActive();
                var r = el.document.selection.createRange()
                r.moveToElementText(el.document.body);
                r.move("character",start);
                r.moveEnd("character",end-start);
                r.select();
            }
        }
        /**
         *  Method is used to calculate pixel offsets for the selection in TextArea (other inputs are not tested yet)
         *
         *  @param {HTMLTextareaElement} el target to calculate offsets
         *  @return {Object} {x: horizontal offset, y: vertical offset, h: height offset}
         *  @scope public
         */
        self.getSelectionOffset = function (el) {
            var off = {'x':0, 'y':0, 'h':0};
            if ('function' == typeof el.getSelection) {
                var r = el.getSelection().getRangeAt(0)
                   ,s = el.document.createElement('span')
                   ,contents = r.cloneContents()
                   ,e = r.endOffset
                   ,n = s;

                s.style.borderLeft='1px solid red';
                r.surroundContents(s);
                off.h = n.offsetHeight;
                while (n.offsetParent) {
                    off.x += n.offsetLeft;
                    off.y += n.offsetTop;
                    n = n.offsetParent
                }
                s.parentNode.removeChild(s);

                var r1 = el.document.createRange()
                if (contents.childNodes.length>0) {
                    for (var i=0;i<contents.childNodes.length;i++) {
                        var n = contents.childNodes[i];
                        r.insertNode(n);
                        r1.selectNode(n);
                    }
                    el.getSelection().addRange(r1);
                }
            } else if (el.document && el.document.selection) {
                var r = el.document.selection.createRange()
                off.h = r.boundingHeight
                off.x = r.offsetLeft;
                off.y = r.offsetTop;
            }
            return off;
        }
    }
}
