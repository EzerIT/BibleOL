VirtualKeyboard.addLayout({code:'AM-ET'
,name:'Ethiopic WashRa'
,normal:'`1234567890-=\\ቀወeረተኀሸየዐፐ«»አሰደፈገሀጀከለ፤፣ዘጠቸጸበነመ,።/'
,shift:{0:'~!@#$%^&*()_+|ቐ',19:'YUIOጰ{}Aሠዻ',30:'ጘሐ',33:'ኸ',35:'፦፥ዠ',39:'ጨፀቨኘ',44:'፠፨፧'}
,'cbk':/**
 * $Id: ethiopic-washra.js 643 2009-07-09 15:19:14Z wingedfox $
 *
 * Ethiopian WashRa layout
 *
 * This software is protected by patent No.2009611147 issued on 20.02.2009 by Russian Federal Service for Intellectual Property Patents and Trademarks.
 *
 * @author Konstantin Wiolowan
 * @copyright 2008-2009 Konstantin Wiolowan <wiolowan@mail.ru>
 * @version $Rev: 643 $
 * @lastchange $Author: wingedfox $ $Date: 2009-07-09 19:19:14 +0400 (Чт, 09 июл 2009) $
 */
function (chr, buf) {
    var vow
       ,str = ''
       ,len = 0
       ,convWashRa = {A:'እ',U:'ሽ',O:'ዕ',I:'ይ',Y:'ኅ'}
       ,vowelsWashRa = {e:0, ',':1,'ይ':2,'እ':3,'ኅ':4,'ሽ':5,'ዕ':6,'/':7,'1':9,'3':10,'4':11,'5':12,'6':13};

    if (chr=='\u0008') { // backspace
        if (buf.length) {
            str = buf.slice(0,-1);
            len = buf.length-1;
        }
    } else {
        chr = VirtualKeyboard.Langs.ET.conv[chr] || convWashRa[chr] || chr;
        if (chr==' ') {
            str = '፡';
        } else {
            if (buf=='`') {
                if (/[0-9]/.test(chr))
                    str = '፻፩፪፫፬፭፮፯፰፱'.charAt(chr);
                else if('`'==chr)
                    str = chr;
                else
                    str = buf+chr;
            } else if (buf=='~') {
                if (/[0-9]/.test(chr))
                    str = '፼፲፳፴፵፶፷፸፹፺'.charAt(chr);
                else if('~'==chr)
                    str = chr;
                else 
                    sre = buf+chr;
            } else {
                vow = vowelsWashRa[chr] || vowelsWashRa[chr.toLowerCase()];
                if (isNumber(vow) && buf) {
                    str = VirtualKeyboard.Langs.ET.cons[buf].charAt(vow);
                    if (!str || str==' ') {
                        str = buf;
                        len = buf.length;
                    }
                } else {
                    str = buf+chr;
                    if ('`' == str || '~' == str)
                        len = 1;
                    else
                        len = VirtualKeyboard.Langs.ET.cons[chr]?1:0;
                }
            }
        }
    }
    return [str, len];
}});