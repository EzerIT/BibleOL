// -*- js -*-
/* 2013 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk */

var urlTypeString = {
    'u' : 'click_for_web_site',
    'v' : 'click_for_video',
    'd' : 'click_for_document',
};

class DisplayMonadObject {
    /** The {@link MonadObject} being displayed (perhaps in part) by this {@code DisplayMonadObject}. */
    public displayedMo : MonadObject;

    /** The (single range) monad set being displayed by this object. */
    public range : MonadPair;

    /** The index in the monad set ranges that make up this object. */
    public mix : number;

    static uniqIdStatic : number = 0;

    public uniqId : number;

    /** The level of this object. (0 for a {@code DisplaySingleMonadObject}, &gt;0 for a {@code
     * DisplayMultipleMonadObject}.) */
    public level : number;

    /** The parent of this text component. */
    public parent : DisplayMultipleMonadObject;

    /** The children of this text component. */
    public children : DisplayMonadObject[];

    /** The name of the Emdros object type represented by this object. */
    public objType : string;

    /** Creates a {@code DisplayMonadObject}. This includes creating the display panel and popup.
     * @param mo The {@link MonadObject} displayed (perhaps in part) by this {@code DisplayMonadObject}.
     * @param objType The Emdros object type represented by this {@code DisplayMonadObject}.
     * @param level The level of the object.
     */
    constructor(mo : MonadObject, objType : string, level : number) {
        this.uniqId = ++DisplayMonadObject.uniqIdStatic;
        this.displayedMo = mo;

        if (mo.displayers==undefined)
            mo.displayers = [this];
        else 
            mo.displayers.push(this);

        this.objType = objType;
        this.level = level;
    }

    public generateHtml(qd : QuizData, sentenceTextArr : string[]) : JQuery {
        alert('Abstract function generateHtml called');
        return null;
    }

    /** Determines if this object is a subset of another {@code DisplayMonadObject}.
     * @param mo Another {@code DisplayMonadObject}.
     * @return True if the monad set represented by this {@code DisplayMonadObject} is a subset of the
     * monad set represented by the parameter {@code mo}.
     */
    public containedIn(mo : DisplayMonadObject) : boolean {
        return this.range.low>=mo.range.low && this.range.high<=mo.range.high;
    }

}



/** A {@code DisplaySingleMonadObject} is a {@code DisplayMonadObject} that can display a text
 * component at the lowest level, corresponding to a single monad in an Emdros database. This is
 * typically a single word.
 * <p>
 * Hebrew is a special case here. In most languages, words are separated by spaces. In Hebrew, that
 * is not necessarily the case. The Hebrew Bible starts with the word <i>bereshit</i> ("in the
 * beginning"), but this is actually two words: <i>be</i> ("in") and <i>reshit</i> ("beginning"). When this
 * program shows the text without annotation, the words are strung together (<i>bereshit</i>), but when
 * annotation is included, the words are split (<i>be-&nbsp;reshit</i>).
 */
class DisplaySingleMonadObject extends DisplayMonadObject {
    /** Is this {@code DisplaySingleObject} created as part of a quiz? */
    private inQuiz : boolean;

    static sof_pasuq : string = '&#x05c3;';

    static itemIndex : number;

    private monad : number;

    /** Creates a {@code DisplaySingleMonadObject}. This includes setting up a mouse listener that
     * highlights enclosing phrase and clause frames. Note that this constructor does not set the
     * text; that is done by a subsequent call to {@link #setText(String,String,Font,Color)}.
     * @param smo The {@link SingleMonadObject} displayed by this {@code DisplaySingleMonadObject}.
     * @param objType The Emdros object type represented by this {@code DisplaySingleMonadObject}.
     * @param inQuiz Is this part of a quiz (in which case we must not display chapter and verse).
     */
    constructor(smo : SingleMonadObject, objType : string, inQuiz : boolean) {
        super(smo,objType,0);
        this.inQuiz = inQuiz;
        this.monad = smo.mo.monadset.segments[0].low;
        this.range = {low: this.monad, high: this.monad};
        this.mix = 0;
    }


    public generateHtml(qd : QuizData, sentenceTextArr : string[]) : JQuery {
        var smo : SingleMonadObject = <SingleMonadObject>this.displayedMo;
        var uhSize : number = smo.bcv.length;
        var chapter : string = null;
        var verse : string = null;
        var appendSofPasuq : boolean = false;
        var refs : number[] = null;
        var urls : string[][] = null;

        if (uhSize!=0) {
            if (uhSize!=smo.sameAsPrev.length) throw 'BAD2';
            if (uhSize!=smo.sameAsNext.length) throw 'BAD3';

            // If this is not a quiz, add book, chapter, and verse, plus sof pasuq, if needed
            if (!this.inQuiz) {
                document.title = l10n.universe['book'][smo.bcv[0]] + ' ' + smo.bcv[1];
                $('#textcontainer h1').html(document.title);

                for (var i : number = 0; i<uhSize; ++i) {
                    if (!smo.sameAsPrev[i]) {
                        if (i==1)
                            chapter=smo.bcv[i];
                        else if (i==2) {
                            verse=smo.bcv[i];
                            refs=smo.pics;
                            urls=smo.urls;
                        }
                    }
                    if (!smo.sameAsNext[i]) {
                        if (i==2)
                            appendSofPasuq = true;
                    }
                }
	    }
        }

        var text : string;
        var id_d : number = qd ? qd.monad2Id[this.monad] : null;
        if (id_d) {
            // This is a quiz object
            if (qd.quizFeatures.dontShow)
                text = '({0})'.format(++DisplaySingleMonadObject.itemIndex)
            else
                text = this.displayedMo.mo.features[configuration.surfaceFeature] ;
            text = '<em>' + text + '</em>';
        }
        else {
            text = this.displayedMo.mo.features[configuration.surfaceFeature];
            if (configuration.useSofPasuq && appendSofPasuq)
		text += charset.isRtl ? DisplaySingleMonadObject.sof_pasuq : ':';
	}

        var chapterstring : string = chapter==null ? '' : '<span class="chapter">{0}</span>&#x200a;'.format(chapter);
        var versestring : string = verse==null ? '' : '<span class="verse">{0}</span>'.format(verse);
        var refstring : string;
        
        if (refs===null)
            refstring = '';
        else if (refs.length===4) // Only one reference
            refstring = '<a target="_blank" title="{2}" href="http://resources.3bmoodle.dk/link.php?picno={0}"><img src="{1}images/p.png"></a>'
            .format(refs[3],site_url,localize('click_for_picture'));
        else // More than one reference
            refstring = '<a target="_blank" title="{4}" href="http://resources.3bmoodle.dk/img.php?book={0}&chapter={1}&verse={2}"><img src="{3}images/pblue.png"></a>'
            .format(refs[0],refs[1],refs[2],site_url,localize('click_for_pictures'));
        
        var urlstring : string = '';
        if (urls!==null) {
            var len = urls.length;
            for (var uix : number = 0; uix<urls.length; ++uix) {
                urlstring += '<a target="_blank" title="{0}" href="{1}"><img src="{2}images/{3}.png"></a>'
                .format(localize(urlTypeString[urls[uix][1]]),urls[uix][0],site_url,urls[uix][1]);
            }
        }
        var grammar = '';
        configuration.sentencegrammar[0]
            .getFeatVal(smo, 0, this.objType, false,
                        (whattype:number, objType:string, featName:string, featValLoc:string) => {
                            switch (whattype) {
                            case WHAT.feature:
                                var wordclass : string;
                                var fs : FeatureSetting = getFeatureSetting(objType,featName);
                                if (fs.foreignText)
                                    wordclass = charset.foreignClass;
                                else if (fs.transliteratedText)
                                    wordclass = charset.transliteratedClass;
                                else
                                    wordclass = 'ltr';

                                // For English and German in ETCBC4, display only the first gloss
                                
                                if (configuration.databaseName=="ETCBC4"
                                    && (featName=="english" || featName=="german")) {
                                    featValLoc = featValLoc.replace(/(&[gl]t);/,'$1Q')
                                                           .replace(/([^,;(]+).*/,'$1')
                                                           .replace(/(&[gl]t)Q/,'$1;');
                                }

                                grammar += '<span class="wordgrammar dontshowit {0} {2}">{1}</span>'.format(featName,featValLoc,wordclass);
                                break;

                            case WHAT.metafeature:
                                grammar += '<span class="wordgrammar dontshowit {0} ltr">{1}</span>'.format(featName,featValLoc);
                                break;
                            }
                        });

        var follow_space : string = '<span class="wordspace"> </span>'; // Enables line wrapping
        var follow_class : string = ''; 

        if (charset.isHebrew) {
            var suffix = smo.mo.features[configuration.suffixFeature];
            if (suffix==='' || suffix==='-' || suffix==='\u05be') {
                follow_space = ''; // Prevents line wrapping
                follow_class = suffix==='' ? ' cont cont1' : ' contx cont1';
                text += suffix;
                sentenceTextArr[0] += text;
            }
            else
                sentenceTextArr[0] += text + ' ';
        }
        else
            sentenceTextArr[0] += text + ' ';
            
        return $('<span class="textblock inline"><span class="textdisplay {0}" data-idd="{1}">{2}{3}{4}{5}{6}</span>{7}</span>{8}'
                 .format(charset.foreignClass + follow_class,
                         smo.mo.id_d,
                         '', //chapterstring,
                         versestring,
                         refstring,
                         urlstring,
                         text,
                         grammar,
                         follow_space));
    }
}


// TODO: Fix this
class Color {
    constructor(a:number,b:number,c:number) {}
}


class DisplayMultipleMonadObject extends DisplayMonadObject {
    /** The title in the border. */
    private borderTitle : string;

    /** Is this textual component split on the left (that is, is it part of, for example, a split
     * clause whose other half is displayed to the left)? */
    public hasPredecessor : boolean;

    /** Is this textual component split on the right (that is, is it part of, for example, a split
     * clause whose other half is displayed to the right)? */
    public hasSuccessor : boolean;

    /** Is this object the <i>patriarch</i> (that is, the single top-level object)? */
    private isPatriarch : boolean;


    /** A collection colors to use for the unhightlighted and highlighted frames at various levels. */
    static frameColors : util.Pair<Color,Color>[] = [new util.Pair(new Color(0.000, 0.27, 0.98), new Color(0.000, 0.98, 0.71)),
                                                     new util.Pair(new Color(0.667, 0.27, 0.98), new Color(0.667, 0.98, 0.71)),
                                                     new util.Pair(new Color(0.39, 0.27, 0.98),  new Color(0.39, 0.98, 0.71))];
    
    private myColors : util.Pair<Color,Color>;



    /** Creates a {@code DisplayMultipleMonadObject} for a non-partriarch textual component.
     * @param mmo The {@link MultipleMonadObject} displayed (perhaps in part) by this {@code DisplayMultipleMonadObject}.
     * @param monadPair The (single range) monad set being displayed by this object.
     * @param hasPredecessor Is this a later part of a split text component?
     * @param hasSuccessor Is this an earlier part of a split text component?
     * @param objType The Emdros object type represented by this {@code DisplayMonadObject}.
     * @param level The level of the object.
     */
    constructor(mmo : MultipleMonadObject, objType : string, level : number,  monadPair : MonadPair, monadix : number, hasPredecessor : boolean, hasSuccessor : boolean);

    /** Creates a {@code DisplayMultipleMonadObject} for the partriarch (that is, top-level) textual component.
     * @param mmo The {@link MultipleMonadObject} displayed by this {@code DisplayMultipleMonadObject}.
     * @param monadSet The (not necessarily single range) monad set being displayed by this object.
     * @param objType The Emdros object type represented by this {@code DisplayMonadObject}.
     * @param level The level of the object.
     */
    constructor(mmo : MultipleMonadObject, objType : string, level : number, monadSet : MonadSet);

    // Implementation of the overloaded constructors
    constructor(mmo : MultipleMonadObject, objType : string, level : number, monadSet : any, monadix? : number, hasPredecessor? : boolean, hasSuccessor? : boolean) {
        super(mmo,objType,level);

        if (arguments.length == 7) {
            // Non-patriarch
            this.isPatriarch = false;
            this.range = monadSet;
            this.mix = monadix;
            this.children = [];

            this.hasPredecessor = hasPredecessor;
            this.hasSuccessor = hasSuccessor;
            this.borderTitle = getObjectFriendlyName(objType);

            this.myColors = DisplayMultipleMonadObject.frameColors[(level-1)%DisplayMultipleMonadObject.frameColors.length];
        }
        else {
            // Patriarch
            this.isPatriarch = true;

            this.range = {low: monadSet.segments[0].low, high: monadSet.segments[monadSet.segments.length-1].high};
            this.mix = 0;
            this.children = [];

            this.hasPredecessor = false;
            this.hasSuccessor = false;
        }
    }

    public generateHtml(qd : QuizData, sentenceTextArr : string[]) : JQuery {
        var spanclass : string = 'lev{0} dontshowborder noseplin'.format(this.level);
        if (this.hasPredecessor)
            spanclass += ' hasp';
        if (this.hasSuccessor)
            spanclass += ' hass';

        var grammar = '';

        var indent : number = 0;

        if (configuration.sentencegrammar[this.level]) {
            configuration.sentencegrammar[this.level]
            .getFeatVal(this.displayedMo, this.mix, this.objType, true,
                        (whattype:number, objType:string, featName:string, featValLoc:string) => {
                            if (whattype==WHAT.feature || whattype==WHAT.metafeature) {
                                if (configuration.databaseName=='ETCBC4' && objType=="clause_atom" && featName=="tab")
                                    indent=+featValLoc;
                                else
                                    grammar += '<span class="xgrammar dontshowit {0}_{1}">:{2}</span>'.format(objType,featName,featValLoc);
                            }
                        });
        }
        var jq : JQuery;
        if (this.isPatriarch)
            jq = $('<span class="{0}"></span>'.format(spanclass));
        else {
            if (this.displayedMo.mo.name=="dummy")
                jq = $('<span class="{0}"><span class="nogram dontshowit" data-idd="{1}" data-mix="0"></span></span>'.format(spanclass,
                                                                                                     this.displayedMo.mo.id_d));
            else if (configuration.databaseName=='ETCBC4' && this.level==2)
                jq = $('<span class="notdummy {0}"><span class="gram dontshowit" data-idd="{1}" data-mix="{2}">{3}{4}</span><span class="xgrammar clause_atom_tab dontshowit indentation" data-indent={5}></span></span>'
                       .format(spanclass,
                               this.displayedMo.mo.id_d,
                               this.mix,
                               getObjectShortFriendlyName(this.objType),
                               grammar,indent));
            else
                jq = $('<span class="notdummy {0}"><span class="gram dontshowit" data-idd="{1}" data-mix="{2}">{3}{4}</span></span>'
                       .format(spanclass,
                               this.displayedMo.mo.id_d,
                               this.mix,
                               getObjectShortFriendlyName(this.objType),
                               grammar));
        }

        for (var ch in this.children) {
            if (isNaN(+ch)) continue; // Not numeric
            jq.append(this.children[ch].generateHtml(qd, sentenceTextArr));
        }

        return jq;
    }
}
