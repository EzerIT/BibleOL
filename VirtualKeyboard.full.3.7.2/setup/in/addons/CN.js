/**
 * $Id: CN.js 643 2009-07-09 15:19:14Z wingedfox $
 *
 * Chinese char processor implementation
 *
 * This software is protected by patent No.2009611147 issued on 20.02.2009 by Russian Federal Service for Intellectual Property Patents and Trademarks.
 *
 * @author Konstantin Wiolowan
 * @copyright 2007-2009 Konstantin Wiolowan <wiolowan@mail.ru>
 * @version $Rev: 643 $
 * @lastchange $Author: wingedfox $ $Date: 2009-07-09 19:19:14 +0400 (Thu, 09 Jul 2009) $
 */
WirtualKeyboard.Langs.CN = new function () {
    var self = this;
    self.INPArr = [];
    /**
     *  Callback to process keyboard input in the current IME style
     *
     *  @see WirtualKeyboard.processChar
     *  @param {String} chr current input char
     *  @param {String} buf actual processing buffer
     *  @return {Array} new buffer contents and length
     *  @scope protected
     */
    self.processChar = function (chr, buf) {
        var num, str, arr
        if (chr=='\u0008') { // backspace
            if (buf && (str=buf.slice(0,-1))) {
                WirtualKeyboard.IME.show(self.INPArr[str.toLowerCase()] || []);
                return [str,str.length]
            } else {
                WirtualKeyboard.IME.hide()
                return ['',0] //total delete; some other cases
            }
        } else { //non backspace
            str=buf+chr
            arr = self.INPArr[str.toLowerCase()] || []
            if (arr.length) { // miao
                WirtualKeyboard.IME.show((typeof arr =='string')? self.INPArr[str.toLowerCase()]=arr.split('') : arr)
                return [str, str.length]
            } else if(WirtualKeyboard.IME.getSuggestions().length) { // not a part of a syllable
                if (isFinite(num=parseInt(chr))) { // miao3
                    str = WirtualKeyboard.IME.getChar(num);
                    if (!str) { //miao9 - no such variant
                        return[buf,buf.length]
                    } else {
                        WirtualKeyboard.IME.hide();
                        return[str,0]
                    }
                } else if ((arr = self.INPArr[chr.toLowerCase()] || []).length) { //nih
                    str=WirtualKeyboard.IME.getSuggestions()[0]
                    WirtualKeyboard.IME.setSuggestions((typeof arr =='string')? self.INPArr[str.toLowerCase()]=arr.split('') : arr)
                    return [str+chr,1]
                } else { // ni,
                    str=WirtualKeyboard.IME.getSuggestions()[0]
                    WirtualKeyboard.IME.hide()
                    return [str+(chr.charCodeAt()==10? '': chr),0]
                }
            }
        }
        return [buf+chr,0] //non-chinese talk
    }
};
