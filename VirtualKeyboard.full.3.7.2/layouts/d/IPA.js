/**
 * $Id: IPA.js 643 2009-07-09 15:19:14Z wingedfox $
 *
 * IPA Phonetic
 *
 * This software is protected by patent No.2009611147 issued on 20.02.2009 by Russian Federal Service for Intellectual Property Patents and Trademarks.
 *
 * @author Konstantin Wiolowan
 * @copyright  Konstantin Wiolowan <wiolowan@mail.ru>
 * @version $Rev: 643 $
 * @lastchange $Author: wingedfox $ $Date: 2009-07-09 19:19:14 +0400 (Чт, 09 июл 2009) $
 */
VirtualKeyboard.Langs.IPA = new function () {
    var self = this;
    var INPArr = {
        '˧':'˥|˦|˧|˨|˩|a\u1dc4|a\u1dc5|a\u1dc7|a\u1dc6|a\u1dc8|a\u1dc9|˹|˺|˻|˼|˽|˾|˿'
       ,':':':ːˑ'
       ,'`':'ʼ`ʹʺʻʽʾʿˀˁ˂˃˄˅ˆˇˈˉˊˋˌˍˎˏːˑ˒˓˔˕˖˗˘˙˚˛˜˝˞˟ˬ˭ˮ˯˰˱˲˳˴˵˶˷˸'
       ,'Α':';|á|a\u031f|a\u0320|a\u0318|a\u0319|a\u031d|a\u031e|a\u0339|a\u031c|a\u0308|a\u033D|a\u0303|a\u032F|a\u0324|a\u0330|à|á|â|ã|ā|a̅|ă|ȧ|ä|ả|å|a̋|ǎ|a̍|a̎|ȁ|a̐|ȃ|a̒|a̓|a̔|a̕|a̖|a̗|a̘|a̙|a̚|a̛|a̜|a̡|a̢|ạ|a̤|ḁ|a̦|a̧|ą|a̩|a̪|a̫|a̬|a̭|a̮|a̯|a̰|a̱|a̲|a̳|i̴|i̵|i̶|i̷|a̸|a̹|a̺|a̻|a̼|a̽|a̾|a̿|à|á|a͂|a̓|ä́|aͅ|a͆|a͇|a͈|a͉|a͊|a͋|a͌|a͍|a͎|a͐|a͑|a͒|a͓|a͔|a͕|a͖|a͗|a͘|a͙|a͚|a͛|a͜|a͝|a͞|a͟|a͠|a͡|a͢|aͣ|aͤ|aͥ|aͦ|aͧ|aͨ|aͩ|aͪ|aͫ|aͬ|aͭ|aͮ|aͯͮ'
       ,'Β':'\'|b̓|b̥|b̬|b̻|b\u033c|b\u0329|b\u031a|b\u0324|b\u0330|b̪|b̺|b\u031f|b\u0320|b\u031d|b\u031e|b\u0339|b\u031c|b\u0303'
       ,m:'m|ᵐ|m\u0325|ɱ|ᶬ|ɯ|ᵯ|ᶆ|ḿ|ṁ|ṃ'
       ,n:'n|ɳ|ɲ|ŋ|ɴ|ñ|ṇ|n\u032a|ŋ̑|ŋ̇|ⁿ|ᶯ|ᶮ|ᵑ|ᶰ|n\u033a|n\u0325|ŋ̇̕|ƞ|ň|ń|ǹ|ņ|ŉ|ǌ|ȵ|ᵰ|ᶇ|ṅ|ṉ|ṋ'
       ,p:'p|ᵖ|pʰ|pʷ|p\u032A|p̓|pᶠ|ƥ|ᵱ|ᵽ|ᶈ|ṕ|ṗ'
       ,b:'b|ᵇ|b̪|b\u0325|b̓|bᵛ|β|β̞|ʙ|ɓ|ɓ̥|ᵬ|ᶀ|ḃ|ḅ|ḇ'
       ,t:'t|tʰ|t\u032a|t̕|tᶿ|ṭ|ʦ|ʧ|ʨ|ʈ|ᵗ|t̯|ƭ|t̪̕|t̕|ṭ̕|t̯̕|t͡s|t͡ʃ|t͡ɕ|ţ|ť|ŧ|ƫ|ᶵ|ƭ|ț|ȶ|ᵵ|ᵺ|ṫ|ṯ|ṱ|ẗ|ⱦ'
       ,d:'d|d̪|d\u0325|d̓|dᶞ|ḍ|ɖ|ʣ|ʤ|ʥ|ᵈ|d̯|ɗ|ɗ̥|d\u033a|ƃ|ď|đ|ƌ|ǆ|ǳ|ƍ|ȡ|ᵭ|ᶁ|ḋ|ḏ|ḑ|ḓ|ȸ|d͡z|d͡ʒ|d͡ʑ'
       ,k:'k|ᵏ|kʰ|kˣ|kʷ|k̓|k̯|ƙ|ķ|ĸ|ǩ|ʞ|ᶄ|ḱ|ḳ|ḵ|ⱪ'
       ,g:'g|ɣ|ˠ|ɰ|ɢ|ɢ̆|g̑|ġ|gˠ|ǧ|ᵍ|ᶭ|ɡ̊|ɠ|ʛ|ɠ̊|ʛ̥|ɡ|ᶢ|ᵷ|ɤ|ĝ|ğ|ģ|ǥ|ǵ|ᶃ|ḡ|ȝ'
       ,'ʘ':'ʘǀǃǂǁʗʭ'
       ,c:'c|ᶜ|cʰ|ç|ɕ|ᶝ|ɧ|ç|č|čʰ|ƈ|ć|ĉ|ċ|ȼ|ḉ'
       ,j:'jʲɟʝᶨɥĵǰȷɉɟⱼ'
       ,q:'q|qʰ|qʷ|ʠ|ȹ|ɋ'
       ,'?':'?|ʡ|ˀ|ʔ|ʕ|ˁ|ˤ|ʢ|ʡ̯|ʖ|ᵜ|Ɂ|ɂ'
       ,f:'fᶠɸᶲƒʩᵮᶂḟᶡⱷﬀﬁﬂﬃﬄﬅ'
       ,v:'v|ᵛ|v\u0325|ʋ|ᶹ|ⱱ|ⱱ̟|ᶌ|ṽ|ṿ|ⱴ|ᵥ'
       ,s:'s|ˢ|sʰ|ʃ|ᶴ|ʂ|ᶳ|θ|ᶿ|ɕ|ʄ|ʄ̊|ᴤ|ʅ|ʆ|ᶋ|ᶘ|ʇ|ſ|ẛ|ᵳ|þ|ś|ŝ|š|ş|ș|ȿ|ᶊ|ᵴ|ṡ|ṣ|ṩ|ṥ|ṧ|ﬆ'
       ,z:'zᶻʒᶾǯʐʑðᶞžᶼᶽᴣʓźżẓẑƶᵶẕȥⱬɀᶎƹƺᵹᶚ'
       ,x:'xˣχɧᶍẋẍ'
       ,r:'rʁɹɻʀяɾɿřɽʳɼɺᵣʴʵʶⱹŕŗȑȓɍᵲᶉṙṛṝṟ'
       ,h:'hʰħʜɥᶣɦʱɧȟĥħḣḥḧḩḫẖⱨⱶʮʯ'
       ,l:'l|ɬ|ɮ|ɭ|ʎ|ʎ̯|ʟ|ʟ̆|ł|ƛ|ˡ|l̪|ḷ|ɫ|ʪ|ʫ|ĺ|ļ|ľ|ŀ|ƚ|ǉ|ȴ|ᶅ|ᶩ|ᶪ|ḷ|ḹ|ḻ|ḽ|ⱡ|\uf266|\uf267|\uf268|\uf260'
       ,w:'wʷʍʬʭŵẁẃẅẇẉẘⱳ'
       ,a:'aʌæɐɑɒāáǎàᵃᵄᵅᶛᵆᶺâãäǟåǻḁăąȁȃȧǡᴂǣǽᴂᶏᶐᶑẚảạấầẩẫậắằẳẵặⱥ'
       ,e:'eəɚɛɜɝéēěèêëĕėęɘɞʚᵊᵉᵋᵌᶟᴈḕḗḙḛɇȅȇᶒȩḝᶓᶔᶕẹẻẽếềểễệⱸ'
       ,i:'iɨɪîïįīíǐìĩĭıĳᴉᵎᵻᶧᶤᶦᶥȉȋᶖḭḯᵢỉị'
       ,o:'oøɵœɔɶōóǒòᵒᶱᵓôõöŏȏőȍǿǫǭᶗᵔᵕɷȫȭȯȱṍṏṑṓọỏốồổỗộơớờởỡợⱺƣȣ'
       ,u:'uʉɯʊüųūúǔùûũŭůűᵘᶷᶸᵚᵙᶶᵤȕȗṻǖǘǚǜṳṵṷṹᴝᴞᴟᵾᵿᶙủụưứừửữựᵫ'
       ,y:'yʸʏɤýẏÿẙŷȳɏỳỵỷỹƴ'
       ,A:'AᴀᴬÆᴁᴭɅÀÁÂÃÄÅĀĂĄǍǞǠǢǺǼȀȂȦȺḀẠẢẤẦẨẪẬẮẰẲẴẶⱭⱯ'
       ,B:'BʙᴮƁƂɃᴃᴯḂḄḆ'
       ,C:'CᴄÇĆĈĊČƇȻḈ'
       ,D:'DᴅᴰÐÞĎĐƉᴆƊƋǄǅǱǲḊḌḎḐḒ'
       ,E:'EᴇᴱÈÉÊËĒĔĖĘĚƎⱻᴲƏƐȄȆȨɆḔḖḘḚḜẸẺẼẾỀỂỄỆ'
       ,F:'FƑḞ'
       ,G:'GᴳĜĞĠĢƓƔǤǦǴȜḠ'
       ,H:'HʜᴴĤĦƕǶȞḢḤḦḨḪⱧⱵ'
       ,I:'IᴵÌÍÎÏĨĪĬĮİĲƖƗǏȈȊḬḮỈỊ'
       ,J:'JᴊᴶĴɈ'
       ,K:'KᴋᴷĶƘǨḰḲḴⱩ'
       ,L:'LʟᴸᶫĹĻĽĿŁᴌȽḶḸḺḼⱠⱢ'
       ,M:'MᴍᴹⱮḾṀṂƜ'
       ,N:'NᴺÑṆŃŅŇŊƝǊǋǸȠṄṈṊᴎᴻ'
       ,O:'OᴏᴼÒÓÔÕÖØŌŎŐŒᴔƆᴐƟƠƢǑǪǬǾȌȎȢᴕᴽȪȬȮȰṌṎṐṒỌỎỐỒỔỖỘỚỜỞỠỢᴑᴓᴒ'
       ,P:'PᴘᴾṔṖƤⱣ'
       ,Q:'QɊ'
       ,R:'RᴿŔŖŘƦṘṚṜṞȐȒɌᴙᴚⱤ'
       ,S:'SŚŜŞŠƧƩȘṠṢṤṦṨ'
       ,T:'TᴛᵀŢŤŦƬƮȚȾṪṬṮṰ'
       ,U:'UᴜᵁÙÚÛÜŨŪŬŮŰŲƱƲǓǕǗǙǛȔȖɄỤỦƯỨỪỬỮỰ'
       ,V:'VᴠⱽṼṾɅ'
       ,W:'WᴡᵂẀẂŴẄẆẈⱲ'
       ,X:'XẊẌ'
       ,Y:'YʏŶŸƳɎẎỲỴỶỸ'
       ,Z:'ZᴢŹŻŽƵƷǮȤɀẐẒẔⱫ'
    }

    /**
     *  Callback to process keyboard input in the current IME style
     *
     *  @see VirtualKeyboard.processChar
     *  @param {String} chr current input char
     *  @param {String} buf actual processing buffer
     *  @return {Array} new buffer contents and length
     *  @scope protected
     */
    self.charProcessor = function (chr, buf) {
        var n, s, a
           ,prefixReg = /[ΑΒ˧]/;

        if (chr=='\u0008') { // backspace
             VirtualKeyboard.IME.hide();
             return ['',0];
        } else if (VirtualKeyboard.IME.getSuggestions().length) {
                if (isFinite(n=parseInt(chr))) {
                    s = VirtualKeyboard.IME.getChar(n);
                    if (!s) {
                        return[buf,buf.length];
                    } else {
                        VirtualKeyboard.IME.hide();
                        if (prefixReg.test(buf)) {
                            s=s.slice(-1);
                        }
                        return[s,0];
                    }
                } else if ((a = INPArr[chr] || []).length) {
                    s=VirtualKeyboard.IME.getSuggestions()[0];
                    VirtualKeyboard.IME.setSuggestions((typeof a=='string')?INPArr[chr]=a.split( a.indexOf('|')!=-1?'|'
                                                                                                                   :'')
                                                                           :a);
                    if (prefixReg.test(buf)) {
                        s=s.slice(-1);
                    }
                    return [s+chr,1]
                } else {
                    s=VirtualKeyboard.IME.getSuggestions()[0];
                    VirtualKeyboard.IME.hide();
                    if (prefixReg.test(buf)) {
                        s=s.slice(-1);
                    }
                    return [s+(chr.charCodeAt()==10? '': chr),0];
                }
        } else {
            a = INPArr[chr] || [];
            if (a.length) {
                VirtualKeyboard.IME.show((typeof a=='string')?INPArr[chr]=a.split( a.indexOf('|')!=-1?'|'
                                                                                                     :'')
                                                             :a);
                return [buf+chr,1];
            }
        }
        return [buf+chr,0];
    }
};
