/**
 * $Id: KR.js 643 2009-07-09 15:19:14Z wingedfox $
 *
 * Korean IME implementation
 *
 * This software is protected by patent No.2009611147 issued on 20.02.2009 by Russian Federal Service for Intellectual Property Patents and Trademarks.
 *
 * @author Konstantin Wiolowan
 * @copyright 2007-2009 Konstantin Wiolowan <wiolowan@mail.ru>
 * @version $Rev: 643 $
 * @lastchange $Author: wingedfox $ $Date: 2009-07-09 19:19:14 +0400 (Чт, 09 июл 2009) $
 */
VirtualKeyboard.Langs.KR = new function () {
    var self = this;
    self.Jamo = {'ㄱ':[14,44032,1],'ㄲ':[6,44620,2],'ㄳ':[4,-1,3],'ㄴ':[14,45208,4],'ㄵ':[4,-1,5],'ㄶ':[4,-1,6],'ㄷ':[6,45796,7],'ㄸ':[2,46384,0],'ㄹ':[14,46972,8],'ㄺ':[4,-1,9],'ㄻ':[4,-1,10],'ㄼ':[4,-1,11],'ㄽ':[4,-1,12],'ㄾ':[4,-1,13],'ㄿ':[4,-1,14],'ㅀ':[4,-1,15],'ㅁ':[6,47560,16],'ㅂ':[14,48148,17],'ㅃ':[2,48736,0],'ㅄ':[4,-1,18],'ㅅ':[14,49324,19],'ㅆ':[6,49912,20],'ㅇ':[6,50500,21],'ㅈ':[6,51088,22],'ㅉ':[2,51676,0],'ㅊ':[6,52264,23],'ㅋ':[6,52852,24],'ㅌ':[6,53440,25],'ㅍ':[6,54028,26],'ㅎ':[6,54616,27],'ㅏ':[1,0,0],'ㅐ':[1,28,0],'ㅑ':[1,56,0],'ㅒ':[1,84,0],'ㅓ':[1,112,0],'ㅔ':[1,140,0],'ㅕ':[1,168,0],'ㅖ':[1,196,0],'ㅗ':[1,224,0],'ㅛ':[1,336,0],'ㅜ':[1,364,0],'ㅠ':[1,476,0],'ㅡ':[1,504,0],'ㅣ':[1,560,0]}
    self.VV2V = [0,0,0,0,0,0,0,0,0,224,224,224,0,0,364,364,364,0,0,504,0]
    self.V2VV = [0,0,0,0,0,0,0,0,{'ㅏ':252,'ㅐ':280,'ㅣ':308},0,0,0,0,{'ㅓ':392,'ㅔ':420,'ㅣ':448},0,0,0,0, {'ㅣ':532},0,0]
    self.CV2C = 'ㄱㄲㄴㄷㄸㄹㅁㅂㅃㅅㅆㅇㅈㅉㅊㅋㅌㅍㅎ'.split('')
    self.C2CC = {'ㄱ':'ㄲ','ㄷ':'ㄸ','ㅂ':'ㅃ','ㅅ':'ㅆ','ㅈ':'ㅉ'}
    self.CC2C = {'ㄲ':'ㄱ','ㄸ':'ㄷ','ㅃ':'ㅂ','ㅆ':'ㅅ','ㅉ':'ㅈ'}
    self.PP2P = [0,0,1,1,0,4,4,0,0,8,8,8,8,8,8,8,0,0,17,0,19,0,0,0,0,0,0,0]
    self.PP2PC= [0,[0,44032],[0,44620],[1,49324],[0,45208],[4,51088],[4,54616],[0,45796],[0,46972],[8,44032],[8,47560],[8,48148],[8,49324],[8,53440],[8,54028],[8,54616],[0,47560],[0,48148],[17,49324],[0,49324],[0,49912],[0,50500],[0,51088],[0,52264],[0,52852],[0,53440],[0,54028],[0,54616]]
    self.P2PP = [0,{'ㄱ':2, 'ㅅ':3},0,0,{'ㅈ':5, 'ㅎ':6},0,0,0,{'ㄱ':9,'ㅁ':10,'ㅂ':11,'ㅅ':12,'ㅌ':13,'ㅍ':14,'ㅎ':15},0,0,0,0,0,0,0,0,{'ㅅ':18},0,{'ㅅ':20},0,0,0,0,0,0,0,0]

   /**
    * 1 -sh
    * 2 -jot
    * 4 -w
    * 8 -
    * 16 -
    */
    self.flags = 0 //for some crosstalk

    self.parseHangul = function (bufchar) {
        if(bufchar=='' || bufchar.length>1) return null
        var code=bufchar.charCodeAt()
        if(code<0x3131 || code >0xD7A3) return null // non Korean buffer
        else if(code<0x314F && code>0x3130) return [self.Jamo[bufchar][1],-1,0] // consonant in buffer
        code -= 44032
        var arr=[]
        arr[0]=44032+588*(code / 588 >>0)
        code %= 588
        arr[1]= 28*(code / 28 >>0)
        arr[2]= code % 28
        return arr
    }
    self.charProcessor  = function (chr, buf, keystate, CVC, rukbd) {
        var jamo=self.Jamo[chr]
        if (!CVC) CVC=self.parseHangul(buf)
        if (CVC==null) {
            if (!jamo) {
                return [chr,0]
            } else {
                if(jamo[0] & 2) return [chr,1] //can start a syllable
                else return [chr,0]
            }
        } else { // full buf
            if (chr=='\u0008'){
                if (CVC[2]) {
                    return [ String.fromCharCode( CVC[0]+CVC[1]+self.PP2P[CVC[2]]), 1] 
                } else if(CVC[1]>-1){
                    var VV2V=self.VV2V[CVC[1]/28]
                    if(VV2V) 
                        return [String.fromCharCode(CVC[0]+VV2V), 1]
                    else 
                        return [self.CV2C[(CVC[0]-44032)/588], 1]
                } else if(self.CC2C[buf]) {
                    return [self.CC2C[buf],1]
                } else {
                    self.flags=0
                    return['',0] 
                }
            } else if(!jamo){
                self.flags=0
                return [buf+chr,0]
            } else if(CVC[2]) { // [CVC]
                if (jamo[0] & 2) { //[CVC] +C
                    var P2PP = self.P2PP[CVC[2]][chr]    
                    if(P2PP) return [ String.fromCharCode( CVC[0]+CVC[1]+P2PP), 1] // [CVCC]
                    else return [buf+chr, 1] // CVC, [C]
                } else if (jamo[0] & 1) {// [CVC] +V
                    if(rukbd && CVC[2]==21) 
                        return [buf+String.fromCharCode(50500+jamo[1]),1]

                    return [ String.fromCharCode( CVC[0]+CVC[1]+self.PP2PC[CVC[2]][0])
                            +String.fromCharCode( self.PP2PC[CVC[2]][1]+self.Jamo[chr][1])
                            ,1] // CV(P) [PV]
                } else { // [CVC] + PP
                        return [buf+chr, 0]
                }
            } else if(CVC[1]>-1) { // [CV]
                self.flags &=~ 3
                if (jamo[0] & 4) { // [CV] +P
                    return [String.fromCharCode(CVC[0]+CVC[1]+jamo[2]), 1] // [CVC]
                } else if(jamo[0] & 1) { // [CV]+V
                    if (rukbd) {
                        var vow
                        if (self.flags & 4 && (vow='\u3153\u3154\u3163'.indexOf(chr))!=-1) {//weo, we, wi
                            self.flags &=~4
                            return [String.fromCharCode(CVC[0]+[392,308,448][vow]),1]
                        }
                    }
                    var V2VV = self.V2VV[CVC[1]/28][chr]
                    if (V2VV) {// [CVV]
                        //self.flags &=~7
                        return [String.fromCharCode(CVC[0]+V2VV), 1] 
                    } else {// CV,[V]
                        if (rukbd) {
                            //self.flags &=~7
                            return [buf+String.fromCharCode(50500+jamo[1]),1]
                        }
                        else return [buf+chr, 0] 
                    }
                }
                else return [buf+chr, 1] //CV [C]
            } else if(jamo[0] & 1) {// [C] +V 
                return [String.fromCharCode(self.Jamo[buf][1]+jamo[1]), 1]
            } else { //[C]+C
                if(buf==chr && self.C2CC[buf]) return [self.C2CC[buf],1]
                else return [buf+chr, 1]
            }
        }
    }
};
