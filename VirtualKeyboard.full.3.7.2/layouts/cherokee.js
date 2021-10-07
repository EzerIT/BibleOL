VirtualKeyboard.addLayout({code:'CHR'
,name:'Cherokee Phonetic'
,normal:'`1234567890-=\\qwetyuio[]asdghjkl;\'cvnm,./'
,shift:{0:'~!@#$%^&*()_+|',24:'{}',35:':"',44:'<>?'}
,'cbk':/**
 * $Id: cherokee.js 643 2009-07-09 15:19:14Z wingedfox $
 *
 * Cherokee char processor
 *
 * This software is protected by patent No.2009611147 issued on 20.02.2009 by Russian Federal Service for Intellectual Property Patents and Trademarks.
 *
 * @author Konstantin Wiolowan
 * @copyright 2008-2009 Konstantin Wiolowan <wiolowan@mail.ru>
 * @version $Rev: 643 $
 * @lastchange $Author: wingedfox $ $Date: 2009-07-09 19:19:14 +0400 (Чт, 09 июл 2009) $
 */
new function () {
    var reNotCHR = /[^adeghik-oqs-wyz]/
       ,remap = {
            a:'Ꭰ',e:'Ꭱ',i:'Ꭲ',o:'Ꭳ',u:'Ꭴ',v:'Ꭵ'
           ,ga:'Ꭶ',ka:'Ꭷ',ge:'Ꭸ',gi:'Ꭹ',go:'Ꭺ',ge:'Ꭻ',gv:'Ꭼ',ke:'Ꭸ',ki:'Ꭹ',ko:'Ꭺ',ke:'Ꭻ',kv:'Ꭼ'
           ,ha:'Ꭽ',he:'Ꭾ',hi:'Ꭿ',ho:'Ꮀ',hu:'Ꮁ',hv:'Ꮂ'
           ,la:'Ꮃ',le:'Ꮄ',li:'Ꮅ',lo:'Ꮆ',lu:'Ꮇ',lv:'Ꮈ'
           ,ma:'Ꮉ',me:'Ꮊ',mi:'Ꮋ',mo:'Ꮌ',mu:'Ꮍ'
           ,hna:'Ꮏ'
           ,na:'Ꮎ', ne:'Ꮑ',ni:'Ꮒ',no:'Ꮓ',nu:'Ꮔ',nv:'Ꮕ'
           ,qua:'Ꮖ',que:'Ꮗ',qui:'Ꮘ',quo:'Ꮙ',quu:'Ꮚ',quv:'Ꮛ'
           ,kwa:'Ꮖ',kwe:'Ꮗ',kwi:'Ꮘ',kwo:'Ꮙ',kwu:'Ꮚ',kwv:'Ꮛ'
           ,gwa:'Ꮖ',gwe:'Ꮗ',gwi:'Ꮘ',gwo:'Ꮙ',gwu:'Ꮚ',gwv:'Ꮛ'
         //,sa:'Ꮝ',se:'Ꮞ',si:'Ꮟ',so:'Ꮠ',su:'Ꮡ',sv:'Ꮢ'
           ,'Ꮝa':'Ꮜ','Ꮝe':'Ꮞ','Ꮝi':'Ꮟ','Ꮝo':'Ꮠ','Ꮝu':'Ꮡ','Ꮝv':'Ꮢ'
           ,da:'Ꮣ',ta:'Ꮤ',de:'Ꮥ',te:'Ꮦ',di:'Ꮧ',ti:'Ꮨ','do':'Ꮩ',du:'Ꮪ',dv:'Ꮫ',to:'Ꮩ',tu:'Ꮪ',tv:'Ꮫ'
           ,dla:'Ꮬ',tla:'Ꮭ',tle:'Ꮮ',tli:'Ꮯ',tlo:'Ꮰ',tlu:'Ꮱ',tlv:'Ꮲ',dle:'Ꮮ',dli:'Ꮯ',dlo:'Ꮰ',dlu:'Ꮱ',dlv:'Ꮲ'
           ,tsa:'Ꮳ',tse:'Ꮴ',tsi:'Ꮵ',tso:'Ꮶ',tsu:'Ꮷ',tsv:'Ꮸ'
           ,dsa:'Ꮳ',dse:'Ꮴ',dsi:'Ꮵ',dso:'Ꮶ',dsu:'Ꮷ',dsv:'Ꮸ'
           ,wa:'Ꮹ',we:'Ꮺ',wi:'Ꮻ',wo:'Ꮼ',wu:'Ꮽ',wv:'Ꮾ'
           ,ya:'Ꮿ',ye:'Ᏸ',yi:'Ᏹ',yo:'Ᏺ',yu:'Ᏻ',yv:'Ᏼ'
        }
       ,submap = {
            s:'Ꮝ'
         //,nah:'Ꮐ'
           ,tl:1, dl:1, ts:1, ds:1, qu:1, kw:1, gw:1, hn:1
        }

   this.charProcessor = function(chr, buf) {
        if (chr=='\u0008') { // backspace
            if (buf.length) {
                return [buf.slice(0,-1),buf.length-1]
            } 
        } else if (reNotCHR.test(chr)){
            return remap[buf+chr] || [buf+chr, 0]
        } else {
            var str=buf+chr
               ,res,cres;
            if (res=remap[str]) {
                return [res,0];
            } else if (res=submap[str]) {
                switch (res) {
                    case 1:
                        return [str, 2];
                    default:
                        return [res,1];
                }
            } else if (res=remap[buf]){
                if (cres=remap[chr])
                    return [res+cres,1]
                else 
                    return [res+chr,1]
            } else {
                return [buf + (remap[chr]||submap[chr]||chr), 1]
            }
        }
    }
}});