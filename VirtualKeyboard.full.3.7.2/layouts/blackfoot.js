VirtualKeyboard.addLayout({code:'BLA'
,name:'Blackfoot Phonetic'
,normal:'`1234567890-=\\wetyiop[]ashk;\'nm,᙮/'
,shift:{0:'~!@#$%^&*()_+|',24:'{}',35:':"',44:'<>?'}
,'cbk':/**
 * $Id: blackfoot.js 643 2009-07-09 15:19:14Z wingedfox $
 *
 * Blackfoot char processor
 *
 * This software is protected by patent No.2009611147 issued on 20.02.2009 by Russian Federal Service for Intellectual Property Patents and Trademarks.
 *
 * @author Konstantin Wiolowan
 * @copyright 2008-2009 Konstantin Wiolowan <wiolowan@mail.ru>
 * @version $Rev: 643 $
 * @lastchange $Author: wingedfox $ $Date: 2009-07-09 19:19:14 +0400 (Чт, 09 июл 2009) $
 */
new function () {
    var reNotBLA = /[^aehikmnopstwy]/
       ,remap={
           i:'ᖱ','ᐤi':'ᑯ','ᐨi':'ᒧ','ᘁi':'ᖽ','ᐢi':'ᒍ','ᐡi':'ᖹ','ᔈi':'ᓱ',yi:'ᔪ',wi:'ᖵ'
           ,'ᖳi':'ᖳᐟ','ᖰi':'ᖰᐟ','ᖲi':'ᖲᐟ','ᑫi':'ᑫᐟ','ᑭi':'ᑭᐟ','ᑲi':'ᑲᐟ','ᒣi':'ᒣᐟ','ᒥi':'ᒥᐟ','ᒪi':'ᒪᐟ','ᖿi':'ᖿᐟ','ᖼi':'ᖼᐟ','ᖾi':'ᖾᐟ','ᒉi':'ᒉᐟ','ᒋi':'ᒋᐟ','ᒐi':'ᒐᐟ','ᖻi':'ᖻᐟ','ᖸi':'ᖸᐟ','ᖺi':'ᖺᐟ','ᓭi':'ᓭᐟ','ᓯi':'ᓯᐟ','ᓴi':'ᓴᐟ','ᔦi':'ᔦᐟ','ᔨi':'ᔨᐟ','ᔭi':'ᔭᐟ','ᖷi':'ᖷᐟ','ᖴi':'ᖴᐟ','ᖶi':'ᖶᐟ'
           ,'ᖳo':'ᖳᐠ','ᖰo':'ᖰᐠ','ᑫo':'ᑫᐠ','ᑭo':'ᑭᐠ','ᒣo':'ᒣᐠ','ᒥo':'ᒥᐠ','ᖿo':'ᖿᐠ','ᖼo':'ᖼᐠ','ᒉo':'ᒉᐠ','ᒋo':'ᒋᐠ','ᖻo':'ᖻᐠ','ᖸo':'ᖸᐠ','ᓭo':'ᓭᐠ','ᓯo':'ᓯᐠ','ᔦo':'ᔦᐠ','ᔨo':'ᔨᐠ','ᖷo':'ᖷᐠ','ᖴo':'ᖴᐠ'
        }
       ,submap={
         //s:'Ꮝ'
         //nah:'Ꮐ'
           a:'ᖳ',e:'ᖰ',o:'ᖲ'
           ,'ᐤa':'ᑫ','ᐤe':'ᑭ','ᐤo':'ᑲ'
           ,'ᐨa':'ᒣ','ᐨe':'ᒥ','ᐨo':'ᒪ'
           ,'ᘁa':'ᖿ','ᘁe':'ᖼ','ᘁo':'ᖾ'
           ,'ᐢa':'ᒉ','ᐢe':'ᒋ','ᐢo':'ᒐ'
           ,'ᐡa':'ᖻ','ᐡe':'ᖸ','ᐡo':'ᖺ'
           ,'ᔈa':'ᓭ','ᔈe':'ᓯ','ᔈo':'ᓴ'
           ,ya:'ᔦ',ye:'ᔨ',yo:'ᔭ'
           ,wa:'ᖷ',we:'ᖴ',wo:'ᖶ'
           ,'ᐤy':'ᐤy','ᐨy':'ᐨy','ᘁy':'ᘁy','ᐢy':'ᐢy','ᐡy':'ᐡy','ᔈy':'ᔈy'
           ,'ᐤs':'ᐤs','ᐨs':'ᐨs','ᘁs':'ᘁs','ᐢs':'ᐢs','ᐡs':'ᐡs','ᔈs':'ᔈs'
           ,'ᐤw':'ᐤw','ᐨw':'ᐨw','ᘁw':'ᘁw','ᐢw':'ᐢw','ᐡw':'ᐡw','ᔈw':'ᔈw'
           ,p:'ᐤ',t:'ᐨ',k:'ᘁ',m:'ᐢ',n:'ᐡ',s:'ᔈ',h:'ᑊ'
           ,'ᑊk':'ᐦ'
          //tl:1, dl:1, ts:1, ds:1, qu:1, kw:1, gw:1, hn:1
        }
       ,premap={
            'ᖲo':'ᖲᖲ','ᑲo':'ᑲᖲ','ᒪo':'ᒪᖲ','ᖾo':'ᖾᖲ','ᒐo':'ᒐᖲ','ᖺo':'ᖺᖲ','ᓴo':'ᓴᖲ','ᔭo':'ᔭᖲ','ᖶo':'ᖶᖲ'
        }

    this.charProcessor = function(chr, buf){
        if (chr=='\u0008') { // backspace
            if (buf.length) {
                return [buf.slice(0,-1),buf.length-1]
            }
        } else if (reNotBLA.test(chr)) {
            return remap[buf+chr] || [buf+chr, 0]
        } else {
            var str,res,cres,h='';

            if (buf.charAt(0)=='ᐦ') {
                h='ᑊ';
                buf='ᘁ'
            }

            str=buf+chr
            if (res=remap[str]) {
                return [h+res,0]
            } else if (res=submap[str]) {
                return [h+res,res.length]
            } else if (res=premap[str]) {
                return [h+res, 1];
            } else if (res=submap[buf]) {
                if (/[ᐤᐨᘁᐢᐡᔈ][syw][aeio]/.test(str)) {
                    res=str.charAt(0)+str.charAt(2)
                    return ([h+(remap[res]||submap[res])+{s:'ᐧ',y:'ᑉ', w:'='}[str.charAt(1)] //chr=='i'?0:1
                            ,0])
                }
                if (cres=remap[chr])
                    return [res+cres,1];
                else
                    return [h+res+chr,1];
            } else {
                return [h+buf + (remap[chr]||submap[chr]||chr), 1]
            }
        }
    }
}});