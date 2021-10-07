VirtualKeyboard.addLayout({code:'KO-KR'
,name:'Ru-Kor'
,normal:'ё1234567890-=\\йцукенгшщзхъфывапролджэячсмитьбю.'
,shift:{1:'!"№;%:?*()_+/',46:','}
,'cbk':/**
 * $Id: korean-ru2kor.js 643 2009-07-09 15:19:14Z wingedfox $
 *
 * Korean phonetic IME
 *
 * This software is protected by patent No.2009611147 issued on 20.02.2009 by Russian Federal Service for Intellectual Property Patents and Trademarks.
 *
 * @author Konstantin Wiolowan
 * @copyright 2007-2009 Konstantin Wiolowan <wiolowan@mail.ru>
 * @version $Rev: 643 $
 * @lastchange $Author: wingedfox $ $Date: 2009-07-09 19:19:14 +0400 (Чт, 09 июл 2009) $
 */
function (chr, buf, keystate) {
    var Ru2Kor = {'-':'-','а':'ㅏ','А':'ㅏ','б':'ㅂ','Б':'ㅃ','в':'ㅗ','В':'ㅗ','г':'ㄱ','Г':'ㄲ','д':'ㄷ','Д':'ㄸ','е':'ㅔ','Е':'ㅔ','ё':'ㅛ','Ё':'ㅕ','ж':'ㅈ','Ж':'ㅈ','з':'ㅈ','З':'ㅈ','и':'ㅣ','И':'ㅣ','й':'ㅣ','Й':'ㅣ','к':'ㄱ','К':'ㄲ','л':'ㄹ','Л':'ㄹ','м':'ㅁ','М':'ㅁ','н':'ㄴ','Н':'ㅇ','о':'ㅗ','О':'ㅓ','п':'ㅂ','П':'ㅃ','р':'ㄹ','Р':'ㄹ','с':'ㅅ','С':'ㅆ','т':'ㄷ','Т':'ㄸ','у':'ㅜ','У':'ㅜ','ф':'ㅍ','Ф':'ㅍ','х':'ㅎ','Ч':'ㅎ','ц':'ㅉ','Ц':'ㅉ','ч':'ㅈ','Ч':'ㅉ','ш':'ㅅ','Ш':'ㅅ','щ':'ㅅ','Щ':'ㅅ','ъ':'ъ','ы':'ㅡ','Ы':'ㅡ','ь':'ㅓ','Ь':'ㅓ','э':'ㅐ','Э':'ㅐ','ю':'ㅠ','Ю':'ㅠ','я':'ㅑ','Я':'ㅑ'}
       ,RuVowels = "ьЬаАеЕёЁиИйЙОоуУыЫэЭюЮяЯ"
       ,Ru2KorJotVowels = "ㅕㅕㅑㅑㅖㅖㅕㅛㅣㅣㅣㅣㅕㅛㅠㅠㅡㅡㅒㅒㅠㅠㅑㅑ"
       ,Korean = VirtualKeyboard.Langs.KR
       ,CVC=Korean.parseHangul(buf)
    if(CVC==null){
        var kor, jamo
        if((kor= Ru2Kor[chr]) && (jamo = Korean.Jamo[kor])){
            var flagged='\u0448\u0428\u0439\u0419\u0432\u0412'.indexOf(chr)
            if(flagged >=0) Korean.flags |= parseInt('112244'.charAt(flagged), 16)
            if(jamo[0] & 1) {// V
                    return [String.fromCharCode(50500+jamo[1]),1]
            }
        }
    }else{
        switch (chr) {
        case '-': // -
            Korean.flags=0
            return  [buf, 0]
        case '\u044a': // tv.znak
                    if(CVC && CVC[2] && CVC[2]==4)// n->ng
                                    return [String.fromCharCode(CVC[0]+CVC[1]+21), 1]
                    else return [buf, buf && 1 || 0]
                    break
/*      case '\u044c': //m.znak
                    return [buf, buf && 1 || 0]
                    break
*/
        case '\u0445': // h
            var pos= '\u3142\u3137\u3148\u3131'.indexOf(buf) // p t c k
            if (pos!=-1 ) return ['\u314d\u314c\u314a\u314b'.charAt(pos), 1]
            else if(CVC[2]) switch (CVC[2]){
                    case 1: return [String.fromCharCode(CVC[0]+CVC[1]+24), 1] // k>kh
                    case 7: return [String.fromCharCode(CVC[0]+CVC[1]+25), 1] // t>th
                    case 17: return [String.fromCharCode(CVC[0]+CVC[1]+26), 1] // p>ph
                    case 22: return [String.fromCharCode(CVC[0]+CVC[1]+23), 1] // j>ch
                    case 11: return [String.fromCharCode(CVC[0]+CVC[1]+14), 1] // lp>lph
            }
            break
        case '\u0436': // zh
            if(buf=='\u3148' || buf=='\u3137') return ['\u3148', 1]
            else if(CVC[2]){
                    if(CVC[2]==22) return [buf, 1];
                    else if (CVC[2]==7) return [String.fromCharCode(CVC[0]+CVC[1]+22), 1]
            }
            break
        case '\u0448': case '\u0428': // sh
            Korean.flags =1
            return[buf+'\u3145',1]
            break
        case '\u0439': case '\u0419': // yot
        //debugger              
            if (CVC[1] == -1 || CVC[2]) Korean.flags =2 //s-y, sas-y
            break
        case '\u0432': case '\u0412': //w
            Korean.flags =4
            break
        default:
        if (CVC && (Korean.flags & 1 &&  CVC[1]==-1 || Korean.flags & 2 && CVC[2]==0)){//sha, rya
                var vow
                if ((vow=RuVowels.indexOf(chr))!=-1) {//vowel
                    Korean.flags &=~ 3
                    return Korean.charProcessor(Ru2KorJotVowels.charAt(vow), Korean.CV2C[(CVC[0]-44032)/588], [CVC[0],-1,0])
                }
            }
        }
    }
    return Korean.charProcessor(Ru2Kor[chr]||chr, buf, keystate, CVC, 1);
}});