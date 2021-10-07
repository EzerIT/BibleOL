VirtualKeyboard.addLayout({code:'EM-ET'
,name:'Ethiopic  Pan-Amharic'
,normal:'`1234567890-=\\ቀወeረተየuioፐ[]aሰደፈገሀጀከለ፤\'ዘሸቸቨበነመ፣./'
,shift:{0:'~!@#$%^&*()_+|ቐ',18:'ጠ',21:'ዕ',23:'ጰ{}',27:'ጸዻ',30:'ጘሐ',33:'ኸ',35:'፡"ዠ',39:'ጨ',42:'ኘ',44:'«»፧'}
,alt:{7:'፨፠'}
,'cbk':/**
 * $Id: ethiopic-pan-amharic.js 643 2009-07-09 15:19:14Z wingedfox $
 *
 * Ethiopian Pan-Amharic layout
 *
 * This software is protected by patent No.2009611147 issued on 20.02.2009 by Russian Federal Service for Intellectual Property Patents and Trademarks.
 *
 * @author Konstantin Wiolowan
 * @copyright 2008-2009 Konstantin Wiolowan <wiolowan@mail.ru>
 * @version $Rev: 643 $
 * @lastchange $Author: wingedfox $ $Date: 2009-07-09 19:19:14 +0400 (Чт, 09 июл 2009) $
 */
(function () {
    var vowelsEZ = {e:1,i:2,a:3,ie:4,u:5,o:6,ua:7,Y:8}
       ,EZfinal
       ,EZnonfinal;

    var getEthDD = function (s) {
        if (!s)
            return 1;

        var dd=/([፲-፺]?)([፩-፱]?)/
           ,m=s.match(dd)
        s=0;
        if (m[1]) {
            s = (m[1].charCodeAt(0)-0x1371)*10;
        }
        if (m[2]) {
            s+= m[2].charCodeAt(0)-0x1368;
        }
        return s;
    }

    var setEthDD = function (dd,lone1) {
        var s = ''
           ,n;

        if (dd!='00') {
            if ((n=dd.charAt(0)) != '0') {
                s = String.fromCharCode(parseInt(n)+0x1371);
            }
            if ((n=dd.charAt(1)) != '0') {
                s+= String.fromCharCode(parseInt(n)+0x1368);
            }
            if (s=='፩' && !lone1) {
                s = '';
            }
        }

        return s;
    }

    var convertEth2Arab = function (s) {
        var sum = ''
           ,m = s.match(/(([፲-፺]?[፩-፱]?)፼፼)?(([፲-፺]?[፩-፱]?)፻፼)?(([፲-፺]?[፩-፱]?)፼)?(([፲-፺]?[፩-፱]?)፻)?([፲-፺]?[፩-፱]?)?$/);

        if (m) {
            sum=0;
            if (m[1]) sum = getEthDD(m[2])*100000000;
            if (m[3]) sum+= getEthDD(m[4])*1000000;
            if (m[5]) sum+= getEthDD(m[6])*10000;
            if (m[7]) sum+= getEthDD(m[8])*100;
            if (m[9]) sum+= getEthDD(m[9]);
        }
        return sum.toString();
    }

    var convertArab2Eth = function (s) {
        if (s.length==1)
            return setEthDD('0'+s,1);

        var sum = ''
           ,m = s.split(/(?=(?:..)+$)/g)
        if (m.length!=1) {
            if (m[0].length==1)
                m[0] = '0'+m[0];
            m = m.reverse();
        }
        if (m[0] && m[0]!='00')
            sum = setEthDD(m[0],1);
        if (m[1]) {
            if (m[1]!='00')
                sum = setEthDD(m[1])+'፻'+sum;
            if (m[2]) {
                if (m[2]!='00')
                    sum = setEthDD(m[2])+'፼'+sum;
                if (m[3]) {
                    if (m[3]!='00')
                        sum = setEthDD(m[3])+'፻፼'+sum;
                    if( m[4] && m[4] != '00')
                        sum = setEthDD(m[4])+'፼፼'+sum;
                }
            }
        }
        return sum;
    }

    return {
        activate : function () {
            if (!EZfinal) {
                var nonfK = "'|'1|'2|'3|'4|'5|'6|'7|'8|'9|:፡|_|`|`1|`2|`3|`4|`5|`6|`7|`8|`9|`ህ|`ስ|`ሽ|`ዥ|`ጭ|`ጽ|a|e|i|o|u|ሁi|ህ|ህe|ህi|ህo|ህu|ህህ|ል|ልe|ልi|ልo|ልu|ሕ|ሕe|ሕi|ሕu|ሙe|ሙi|ም|ምe|ምi|ምo|ምu|ሥe|ሥi|ሥu|ር|ርe|ርi|ርo|ርu|ስ|ስe|ስi|ስo|ስu|ስስ|ሽ|ሽe|ሽi|ሽo|ሽu|ሽሽ|ቁi|ቅ|ቅe|ቅi|ቅo|ቅu|ቅይ|ቑe|ቑi|ቕ|ቕe|ቕi|ቕu|ቡe|ቡi|ብ|ብe|ብi|ብo|ብu|ቭ|ቭe|ቭi|ቭu|ት|ትe|ትi|ትo|ትu|ች|ችe|ችi|ችo|ችu|ችች|ኁe|ኁi|ኅe|ኅi|ኅo|ኅu|ን|ንe|ንi|ንo|ንu|ኝ|ኝe|ኝi|ኝo|ኝu|አa|አe|ኢi|ኩe|ኩi|ክ|ክe|ክi|ክo|ክu|ክይ|ኹe|ኹi|ኽ|ኽe|ኽi|ኽu|ኽይ|ው|ውe|ውi|ውo|ዓa|ዝ|ዝe|ዝi|ዝo|ዝu|ዥ|ዥe|ዥi|ዥu|ዥዝ|ዥዥ|ይ|ይe|ይi|ይo|ድ|ድe|ድi|ድo|ድu|ዽ|ዽe|ዽi|ዽo|ዽu|ጅ|ጅe|ጅi|ጅo|ጅu|ጉe|ጉi|ግ|ግe|ግi|ግo|ግu|ግይ|ጙe|ጙi|ጝ|ጝe|ጝi|ጝu|ጥ|ጥe|ጥi|ጥo|ጥu|ጭ|ጭe|ጭi|ጭo|ጭu|ጭች|ጭጭ|ጵ|ጵe|ጵi|ጵo|ጵu|ጽ|ጽe|ጽi|ጽu|ጽስ|ጽጽ|ፅe|ፅi|ፅo|ፉe|ፉi|ፍ|ፍe|ፍi|ፍu|ፑe|ፑi|ፕ|ፕe|ፕi|ፕo|ፕu|፡|፡፡|።፡|፣|፣፣|፤|፧|‹|‹‹|›|››|ⶥe|ⶥi|ⶭe|ⶭi|ⶵe|ⶵi|ⶽe|ⶽi|ⷅe|ⷅi|ⷍe|ⷍi|ⷕe|ⷕi|ⷝe|ⷝi"
                           .split('|')
                   ,nonfV = "'፩፪፫፬፭፮፯፰፱፠_`፩፪፫፬፭፮፯፰፱ኅሥⶥⶵⶽፅአእኢኦኡኊህሀሂሆሁኅልለሊሎሉሕሐሒሑᎀᎁምመሚሞሙሠሢሡርረሪሮሩስሰሲሶሱሥሽሸሺሾሹⶥቊቅቀቂቆቁⷅቘቚቕቐቒቑᎄᎅብበቢቦቡቭቨቪቩትተቲቶቱችቸቺቾቹⶭኈኊኀኂኆኁንነኒኖኑኝኘኚኞኙዓኧዒኰኲክከኪኮኩⷍዀዂኽኸኺኹⷕውወዊዎዐዝዘዚዞዙዥዠዢዡⶵⶵይየዪዮድደዲዶዱዽዸዺዾዹጅጀጂጆጁጐጒግገጊጎጉⷝⶓⶔጝጘጚጙጥጠጢጦጡጭጨጪጮጩⶽⶽጵጰጲጶጱጽጸጺጹፅፅፀፂፆᎈᎉፍፈፊፉᎌᎍፕፐፒፖፑ፡።:፣፥፤፧‹«›»ⶠⶢⶨⶪⶰⶲⶸⶺⷀⷂⷈⷊⷐⷒⷘⷚ"
                           .split('')
                   ,finK = "ጪeⶺeዺeጚeⶔeሒeኺeዂeⷒeኚeጲeቒeቚeጺeፂeጢeዢeⶲeቢeᎅeⶪeቺeዲeፊeᎉeጊeጒeⷚeሂeኂeኊeጂeኪeኲeⷊeሊeሚeᎁeኒeፒeᎍeቁeቂeቊeⷂeሪeሲeሢeቲeቪeዊeሺeⶢeዪeዚeጨeⶸeዸeጘeⶓeሐeኸeዀeⷐeኘeጰeቐeቘeጸeፀeጠeዠeⶰeበeᎄeⶨeቸeደeፈeᎈeገeጐeⷘeሀeኀeኈeጀeከeኰeⷈeለeመeᎀeነeፐeᎌeቀeⷀeረeሰeሠeተeቨeወeሸeⶠeየeዘeጭaጮaጩaⶽaⶽoⶽuዽaዾaዹaጝaጝoጙaጙuሕaሕoሑaኽaኽoⷕaኹuኹaⷕoⷕuኝaኞaኙaጵaጶaጱaቕaቕoቑaቑuጽaጽoጹaፅaፆaፅuጥaጦaጡaዥaዥoዡaⶵaⶵoⶵuብaቦaቡaቡuⶭaⶭoⶭuችaቹaቾaድaዶaዱaፍaፍoፉaፉuግaጎaጉaጉuⷝaⷝoⷝuህaሆaሁaሁeሁuኅaኆaኁaኁuጅaጆaጁaክaኮaኩaኩuⷍaⷍoⷍuልaሎaሉaምaሞaሙaሙuንaኖaኑaፕaፖaፑaፑuቅaቆaቁaቁuⷅaⷅoⷅuርaሮaሩaስaሶaሱaሥaሥoሡaትaቶaቱaቭaቭoቩaውaዎaውuሽaሾaሹaⶥaⶥoⶥuይaዮaይuዝaዞaዙaዐaእaኢeኧe`eኡu`u`i`aዒe`Eእe`ዕኦo`oኦaቆoኆoኮoጎo«‹»›'!፠፡፤፤፧፧፥፣፡፣፡-፡+፡#_1_2_3_4_5_6_7_8_0__"
                          .split(/(?=(?:..)+$)/g)
                   ,finV = "ጬⶼዼጜⶕሔኼዄⷔኜጴቔቜጼፄጤዤⶴቤᎆⶬቼዴፌᎊጌጔⷜሄኄኌጄኬኴⷌሌሜᎂኔፔᎎቈቄቌⷄሬሴሤቴቬዌሼⶤዬዜጬⶼዼጜⶕሔኼዄⷔኜጴቔቜጼፄጤዤⶴቤᎆⶬቼዴፌᎊጌጔⷜሄኄኌጄኬኴⷌሌሜᎂኔፔᎌቄⷄሬሴሤቴቬዌሼⶤዬዜጫⶐጯⶻⶾⶹዻⶍዿጛጞጟⶖሓሖሗኻኾⷓዅዃⷖⷑኛⶉኟጳⶑጷቓቖቛቝጻጾጿፃፇፁጣⶏጧዣዦዧⶳⶶⶱባⶅቧᎇⶫⶮⶩቻቿⶇዳⶌዷፋፎፏᎋጋጏጓጕⷛⷞⷙሃሇኋኈኍኃኇኋኍጃⶎጇካኯኳኵⷋⷎⷉላⶀሏማⶁሟᎃናⶈኗፓⶒፗᎏቃቇቋቍⷃⷆⷁራⶂሯሳⶃሷሣሦሧታⶆቷቫቮቯዋዏዉሻⶄሿⶣⶦⶡያዯዩዛⶋዟኣኣኤእዐዑዑዒዓዔዔዕዕዖዖⶊቈኈኰጐ<>¡:;?,፥፦፠፨᎐᎑᎒᎓᎔᎕᎖᎘᎙_"
                          .split('');
                EZfinal = {};
                EZnonfinal = {};
                for (var i=0,nonfVL=nonfV.length; i<nonfVL; i++) EZnonfinal[nonfK[i]]=nonfV[i];
                for (var i=0,finVL=finV.length; i<finVL; i++) EZfinal[finK[i]]=finV[i];
                nonfK=nonfV=finK=finV=null;
            }
        }
       ,charProcessor : function (chr, buf) {
            var str = buf+chr
               ,m ,sum
               ,len = 1;
            if (chr=='\u0008') { // backspace
                if (buf.length) {
                    if (m=convertEth2Arab(buf)) {
                        str = convertArab2Eth(m.slice(0,-1));
                        len = str.length;
                    } else {
                        str = '';
                        len = 0;
                        for (var key in EZnonfinal) {
                            if (EZnonfinal[key] == buf) {
                                if (key!=buf && !/^[A-z]/.test(key)) {
                                    str = key.charAt(0);
                                    len = 1;
                                }
                                break;
                            }
                        }
                    }
                }
            } else if (buf && chr=="'" && buf!="'") {
                if (!/[፻፩፪፫፬፭፮፯፰፱፼፲፳፴፵፶፷፸፹፺]/.test(buf)) {
                    str = buf;
                    len = 0;
                }
            } else if(buf && buf!="'" && buf!="_" && /\d/.test(chr)) {
                sum = convertEth2Arab(buf)+chr;
                len = 0;
                str = convertArab2Eth(sum);
                if (sum.length != 10) {
                    len = str.length;
                }
            } else {
                chr = VirtualKeyboard.Langs.ET.conv[chr]||chr;
                if (str = EZnonfinal[buf+chr]) {
                    len = str.length;
                } else if (str = EZfinal[buf+chr]) {
                    len = 0;
                } else if (str = EZnonfinal[chr]) {
                    str = buf + str;
                } else {
                    str = buf + chr;
                    len = 0;
                }
            }
            return [str, len];
        }
    }
})()});