// -*- js -*-
// Copyright © 2018 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk


// Classes that represent display information about Emdros objects.



//****************************************************************************************************
// About the HTML elements displaying text and grammar
//****************************************************************************************************
//
// Words (lowest level Emdros objects):
//     The Emdros object is represented by a <span> element with the classes "textblock" and
//     "inline". Within this <span> element a number of other <span> elements are found:
//      
//         * One <span> element represents the word itself. It has the class "textdisplay" and a class
//           representing the character set of the text. Additionally it may have classes representing
//           Hebrew optional word spacing (see follow_class in the generateHtml method of the
//           DisplaySingleMonadObject class below). The element also has a 'data-idd' attribute, whose
//           value is the id_d of the object in the Emdros database.
//
//         * A number of <span> elements represent grammar features of the word. These elements have
//           the class "wordgrammar". Additionally, they may have these classes:
//               - "showit" / "dontshowit": Controls if the word feature is shown or not.
//               - The name of the feature.
//               - "hebrew" / "hebrew_translit" / "greek" / "latin" / "ltr": Identify the character set
//                 of the feature.
//
// Emdros objects above the word level (such as phrase or clause)...
//     ...have a level (for example, 1 for phrase, 2 for clause etc.).
//     ...may be split into non-contiguous segments, numbered from 0 and up.
//     ...may or may not be displayable. The Patriach element is not displayable, and not all
//        Greek words belong to a displayable clause1 or a clause2.
//
//     The Emdros object is represented by a <span> element with the class "lev#", where # is the
//     level number. Additionally it may have these classes:
//         - "nodummy": If the object is displayable.
//         - "showborder" / "dontshowborder": Controls if the border around the object is shown or not.
//         - "seplin" / "noseplin": Controls if the object is shown on a separate line or not.
//         - "hasp": If the object is split and has a predecessor.
//         - "hass": If the object is split and has a successor.
//
//     In the border, the Emdros object type may be shown in a <span> element. This element has a
//     'data-idd' attribute, whose value is the id_d of the object in the Emdros database, and a
//     'data-mix' attribute, whose value is the number of the current object segment. The <span>
//     element has class "gram" or "nogram", depending on whether the object is displayable or not.
//     Additionally, it has one of these classes:
//         - "showit" / "dontshowit": Controls if the feature is shown or not.
//
//     Following the name of the object type, the border may show features of the Emdros object in a
//     <span> element with class "xgrammar". Additionally, it has these classes:
//         - "showit" / "dontshowit": Controls if the feature is shown or not.
//         - Object type, underscore, feature name (for example "clause_kind").
//         - "indentation": If this feature should be displayed as a Hebrew clause indentation.
//****************************************************************************************************



// Maps URL type to non-localized hypterlink title
let urlTypeString : { [code : string] : string; } = {
    'u' : 'click_for_web_site',
    'v' : 'click_for_video',
    'd' : 'click_for_document',
};

//****************************************************************************************************
// DisplayMonadObject class
//
// This class represents the display information about a Emdros object. It is linked to a
// MonadObject which contains the features of the Emdros object.
//
// A DisplayMonadObject is either a DisplaySingleMonadObject (if the Emdros object is a word) or a
// DisplayMultipleMonadObject (if the Emdros objects is a phrase, clause, etc.).
//
abstract class DisplayMonadObject {
    protected displayedMo : MonadObject;                // The MonadObject being displayed (perhaps in part) by this DisplayMonadObject
    protected objType     : string;                     // The name of the Emdros object type represented by this object
    protected level       : number;                     // The level of this object. (0 for a DisplaySingleMonadObject, >0 for a DisplayMultipleMonadObject)
    public range          : MonadPair;                  // The (single range) monad set being displayed by this object
    protected mix         : number;                     // The index in the monad set ranges that make up this object
    public parent         : DisplayMultipleMonadObject; // The parent of this Emdros object
    public children       : DisplayMonadObject[];       // The children of this Emdros object

    //------------------------------------------------------------------------------------------
    // Constructor method
    //
    // Paramters:
    //     mo: The MonadObject displayed (perhaps in part) by this DisplayMonadObject.
    //     objType: The Emdros object type represented by this DisplayMonadObject.
    //     level: The level of the object. (0=word, 1=phrase, etc.)
    //    
    constructor(mo : MonadObject, objType : string, level : number) {
        // Link this DisplayMonadObject with the MonadObject it displays
        this.displayedMo = mo;

        if (mo.displayers==undefined)
            mo.displayers = [this];
        else 
            mo.displayers.push(this);

        
        this.objType = objType;
        this.level = level;
    }

    //---------------------------------------------------------------------------------------------------
    // generateHtml method
    //
    // Generates the HTML that displays the Emdros object.
    //
    // Parameters:
    //     qd: Data for the current exercise. Null, if we are not generating an exercise.
    //     sentenceTextArr: The generated HTML is stored in element 0 of this array. Only element 0
    //                      is used; the parameter is an array to make it a call-by-reference parameter.
    // Returns:
    //     A JQuery object containing the generated HTML.
    //
    public abstract generateHtml(qd : QuizData, sentenceTextArr : string[], quizMonads: MonadSet) : JQuery;


    //------------------------------------------------------------------------------------------
    // containedIn method
    //
    // Determines if this object is a subset of another DisplayMonadObject.
    //
    // Parameters:
    //     mo: Another DisplayMonadObject.
    // Returns
    //     True if the monad set represented by this DisplayMonadObject is a subset of the
    //     monad set represented by the parameter 'mo'.
    //
    public containedIn(mo : DisplayMonadObject) : boolean {
        return this.range.low>=mo.range.low && this.range.high<=mo.range.high;
    }
}


//****************************************************************************************************
// DisplaySingleMonadObject class
//
// A DisplaySingleMonadObject is a DisplayMonadObject that can display a text component at the
// lowest level, corresponding to a single monad in an Emdros database. This is typically a single
// word.
//
// Hebrew is a special case here. In most languages, words are separated by spaces. In Hebrew, that
// is not necessarily the case. The Hebrew Bible starts with the word "bereshit" ("in the
// beginning"), but this is actually two words: "be" ("in") and "reshit" ("beginning"). When this
// program shows the text without annotation, the words are strung together ("bereshit"), but when
// annotation is included, the words are split ("be- reshit").
// 
class DisplaySingleMonadObject extends DisplayMonadObject {
    // Is this DisplaySingleObject created as part of a quiz?
    private inQuiz          : boolean; // True if we are displaying an exercise
    public static itemIndex : number;  // The number replacing a word in the displayed text of some exercises
    private monad           : number;  // The Emdros monad of this object

    //------------------------------------------------------------------------------------------
    // Constructor method
    //
    // Paramters:
    //     smo: The SingleMonadObject displayed by this DisplaySingleMonadObject.
    //     objType: The Emdros object type represented by this DisplaySingleMonadObject.
    //     inQuiz: True if we are displaying an exercise.
    //    
    constructor(smo : SingleMonadObject, objType : string, inQuiz : boolean) {
        super(smo,objType,0);
        this.inQuiz = inQuiz;
        this.monad = smo.mo.monadset.segments[0].low;
        this.range = {low: this.monad, high: this.monad};
        this.mix = 0;
    }

    //---------------------------------------------------------------------------------------------------
    // generateHtml method
    //
    // See description under DisplayMonadObject.
    //
    public generateHtml(qd : QuizData, sentenceTextArr : string[], quizMonads: MonadSet) : JQuery {
        let smo     : SingleMonadObject = this.displayedMo as SingleMonadObject; // The SingleMonadObject being displayed by this DisplaySingleMonadObject

        let uhSize  : number = smo.bcv.length;  // The size of the hierarchy book/chapter/verse. This is currently always 3
        let chapter : string = null;            // Current chapter, set if the current word is the first word of a chapter
        let verse   : string = null;            // Current verse, set if the current word is the first word of a verse

        // For displaying link icons (only set on the first word in a verse):
        let refs : number[]   = null; // Any picture database references associated with the current verse
        let urls : string[][] = null; // Any URLs associated with the current verse

        if (uhSize!=0) {
            // If this is not an exercise, add book, chapter, and verse
            if (!this.inQuiz) {
                // Sanity check:
                if (uhSize!=smo.sameAsPrev.length) throw 'BAD2';
                if (uhSize!=smo.sameAsNext.length) throw 'BAD3';

                document.title = l10n.universe['book'][smo.bcv[0]] + ' ' + smo.bcv[1]; // Text in title bar
                $('#tabs-background h2').html(document.title); // Text in page heading: Change 2 Nov 2020 by Ernst Boogert

                for (let i : number = 0; i<uhSize; ++i) {
                    if (!smo.sameAsPrev[i]) {
                        if (i==1) {
                            // The current word is the first word in a chapter
                            chapter = smo.bcv[i];
                        }
                        else if (i==2) {
                            // The current word is the first word in a verse
                            verse = smo.bcv[i];
                            refs = smo.pics;
                            urls = smo.urls;
                        }
                    }
                }
	    }
        }

        let text : string; // The text to display for the current word
        let textDisplayClass : string = ''; // HTML element class for text
        if (qd && qd.monad2Id[this.monad] && containsMonad(quizMonads,this.monad)) {
            // This is a quiz object
            if (qd.quizFeatures.dontShow)
                text = `(${++DisplaySingleMonadObject.itemIndex})`;
            else
                text = this.displayedMo.mo.features[configuration.surfaceFeature] ;
            // <em>..</em> are added in order to mark the question object in statistics, but do not
            // affect how the word is displayed. The display is controlled by the textDisplayClass class.
            // TODO: The above comment is no longer true; but it should be!
            text = `<em>${text}</em>`;
            textDisplayClass = ' text-danger'; // (Red text) Indicates question object
        }
        else {
            text = this.displayedMo.mo.features[configuration.surfaceFeature];
            if (!containsMonad(quizMonads,this.monad))
                textDisplayClass = ' text-muted';
        }

        // Representation of chapter and verse number:
        let chapterstring : string = chapter==null ? '' : `<span class="chapter">${chapter}</span>&#x200a;`; // Currently not used
        let versestring   : string = verse==null   ? '' : `<span class="verse">${verse}</span>`;

        let refstring : string;  // String of icons representing pictures
        
        if (refs===null)
            refstring = '';
        else if (refs.length===4) // Only one reference
            refstring = `<a target="_blank" title="${localize('click_for_picture')}" href="http://resources.3bmoodle.dk/link.php?picno=${refs[3]}"><img src="${site_url}images/p.png"></a>`;
        else // More than one reference
            refstring = `<a target="_blank" title="${localize('click_for_pictures')}" href="http://resources.3bmoodle.dk/img.php?book=${refs[0]}&chapter=${refs[1]}&verse=${refs[2]}"><img src="${site_url}images/pblue.png"></a>`;
        
        let urlstring : string = ''; // String of icons representing URLs
        if (urls!==null)
            for (let uix : number = 0; uix<urls.length; ++uix)
                urlstring += `<a target="_blank" title="${localize(urlTypeString[urls[uix][1]])}" href="${urls[uix][0]}"><img src="${site_url}images/${urls[uix][1]}.png"></a>`;

        let grammar : string = ''; // Will hold the interlinear grammar information
        configuration.sentencegrammar[0]
            .walkFeatureValues(smo, 0, this.objType, false,
                                 (whattype    : WHAT,
                                  objType     : string,
                                  origObjType : string,
                                  featName    : string,
                                  featValLoc  : string) => {
                                      switch (whattype) {
                                      case WHAT.feature:
                                          let wordclass : string; // The class attribute of an HTML element
                                          let fs : FeatureSetting = getFeatureSetting(objType,featName);
                                          if (fs.foreignText)
                                              wordclass = charset.foreignClass;
                                          else if (fs.transliteratedText)
                                              wordclass = charset.transliteratedClass;
                                          else if (fs.isGloss && featName!='zh-Hans' && featName!='zh-Hant')
                                              wordclass = 'tenpoint ltr';
                                          else
                                              wordclass = 'ltr';

                                          // For ETCBC4, show only the first gloss
                                          // For nestle1904 with Swahili, show only the first gloss
                                          
                                          if ((configuration.databaseName=="ETCBC4" && fs.isGloss)
                                              || (configuration.databaseName=="nestle1904" && featName=="swahili")) {
                                              featValLoc = featValLoc.replace(/(&[gl]t);/,'$1Q')  // Remove ';' from "&gt;" and "&lt;" 
                                                                     .replace(/([^,;(]+).*/,'$1') // Remove everything after ',' or ';' or '('
                                                                     .replace(/(&[gl]t)Q/,'$1;'); // Reinsert ';' in "&gt;" and "&lt;" 
                                          }

                                          grammar += `<span class="wordgrammar dontshowit ${featName} ${wordclass}">${featValLoc}</span>`;
                                          break;

                                      case WHAT.metafeature:
                                          grammar += `<span class="wordgrammar dontshowit ${featName} ltr">${featValLoc}</span>`;
                                          break;
                                      }
                                  });

        let follow_space : string = '<span class="wordspace"> </span>'; // Enables line wrapping

        if (charset.isHebrew) {
            let suffix = smo.mo.features[configuration.suffixFeature];
            text += suffix;
            if (suffix==='' || suffix==='-' || suffix==='\u05be' /* maqaf */) {
                follow_space = ''; // Prevents line wrapping

                // Enable optional word spacing. This is handled by the util.WordSpaceFollowerBox class.
                // CSS class 'cont' identifies words concatenated to the next word.
                // CSS class 'contx' identifies words linked to the next word with a hypen/maqaf.
                // CSS class 'cont1' means "do not add word spacing".
                // CSS class 'cont2' means "add a hyphen and word spacing".
                // CSS class 'cont2x' means "add word spacing" (a hyphen/maqaf is already present).
                textDisplayClass += suffix==='' ? ' cont cont1' : ' contx cont1';

                sentenceTextArr[0] += text;
            }
            else
                sentenceTextArr[0] += text + ' ';
        }
        else
            sentenceTextArr[0] += text + ' ';
            
        return $(`<span class="textblock inline"><span class="textdisplay ${charset.foreignClass + textDisplayClass}" data-idd="${smo.mo.id_d}">${versestring}${refstring}${urlstring}${text}</span>${grammar}</span>${follow_space}`);
    }
}


//****************************************************************************************************
// DisplayMultipleMonadObject class
//
// A DisplayMultipleMonadObject is a DisplayMonadObject that can display a text component above the
// word level, such as a clause. However, a DisplayMultipleMonadObject always represents a
// contiguous set of monads, so if the clause is split, two or more DisplayMultipleMonadObjects will
// be required, and their fields 'hasPredecessor' and 'hasSuccessor' will be set to represent that
// fact.
//
// A DisplayMultipleMonadObject may be displayed with a border around it. If it has a predecessor or
// or a successor, a side border will be missing.
// 
class DisplayMultipleMonadObject extends DisplayMonadObject {
    private borderTitle    : string;  // The title in the border (that is, the name of the object type)
    private hasPredecessor : boolean; // Is this textual component split and has a preceding part?
    private hasSuccessor   : boolean; // Is this textual component split and has a succeeding part?
    private isPatriarch    : boolean; // Is this object the patriarch (that is, the single top-level object)?


    //------------------------------------------------------------------------------------------
    // Constructor method
    //
    // Creates a DisplayMultipleMonadObject for a non-partriarch textual component.
    //
    // Parameters
    //     mmo: The MultipleMonadObject displayed (perhaps in part) by this DisplayMultipleMonadObject.
    //     objType: The Emdros object type represented by this DisplayMonadObject.
    //     level: The level of the object (for example, 1=phrase, 2=clause, etc.).
    //     monadPair: The (single range) monad set being displayed by this object.
    //     monadix: The index in the monad set ranges that make up this object.
    //     hasPredecessor: Is this a later part of a split text component?
    //     hasSuccessor: Is this an earlier part of a split text component?
    // 
    constructor(mmo : MultipleMonadObject, objType : string, level : number,  monadPair : MonadPair,
                monadix : number, hasPredecessor : boolean, hasSuccessor : boolean);

    //------------------------------------------------------------------------------------------
    // Constructor method
    //
    // Creates a DisplayMultipleMonadObject for the partriarch (that is, top-level) textual component.
    //
    // Parameters
    //     mmo: The MultipleMonadObject displayed (perhaps in part) by this DisplayMultipleMonadObject.
    //     objType: The Emdros object type represented by this DisplayMonadObject.
    //     level: The level of the object (for example, 1=phrase, 2=clause, 3=etc.).
    //     monadSet: The (not necessarily single range) monad set being displayed by this object.
    //
    constructor(mmo : MultipleMonadObject, objType : string, level : number, monadSet : MonadSet);

    //------------------------------------------------------------------------------------------
    // Constructor method
    //
    // The implementation of the above overloaded constructors.
    //
    constructor(mmo : MultipleMonadObject, objType : string, level : number, monadSet : MonadPair | MonadSet,
                monadix? : number, hasPredecessor? : boolean, hasSuccessor? : boolean) {
        super(mmo, objType, level);

        if (arguments.length == 7) {
            // Non-patriarch
            this.isPatriarch = false;
            this.range = monadSet as MonadPair;
            this.mix = monadix;
            this.children = [];

            this.hasPredecessor = hasPredecessor;
            this.hasSuccessor = hasSuccessor;
            this.borderTitle = getObjectFriendlyName(objType);
        }
        else {
            // Patriarch
            this.isPatriarch = true;

            let mseg : MonadPair[] = (monadSet as MonadSet).segments;
            this.range = {low:  mseg[0].low,
                          high: mseg[mseg.length-1].high};
            this.mix = 0;
            this.children = [];

            this.hasPredecessor = false;
            this.hasSuccessor = false;
        }
    }

    //---------------------------------------------------------------------------------------------------
    // generateHtml method
    //
    // See description under DisplayMonadObject.
    //
    public generateHtml(qd : QuizData, sentenceTextArr : string[], quizMonads: MonadSet) : JQuery {
        let spanclass : string = `lev${this.level} dontshowborder noseplin`; // The class of the <span> element containing this object
        if (this.hasPredecessor)
            spanclass += ' hasp';
        if (this.hasSuccessor)
            spanclass += ' hass';

        let grammar : string = ''; // The class off the <span> element containing grammar information
        let indent  : number = 0;  // The current indentation level (for Hebrew clauses)

        if (configuration.sentencegrammar[this.level]) {
            // Generate the <span> elements for the features of this Emdros object
            configuration.sentencegrammar[this.level]
                .walkFeatureValues(this.displayedMo, this.mix, this.objType, true,
                                   (whattype:WHAT, objType:string, origObjType:string, featName:string, featValLoc:string) => {
                                       if (whattype==WHAT.feature || whattype==WHAT.metafeature) {
                                           if (configuration.databaseName=='ETCBC4' && objType=="clause_atom" && featName=="tab")
                                               indent = +featValLoc;
                                           else
                                               grammar += `<span class="xgrammar dontshowit ${objType}_${featName}">:${featValLoc}</span>`;
                                       }
                                   });
        }

        let jq : JQuery; // The resulting HTMl is built in this JQuery object

        if (this.isPatriarch) {
            // The patriarch object (topmost level) is not displayable
            jq = $(`<span class="${spanclass}"></span>`);
        }
        else if (this.displayedMo.mo.name=="dummy") {
            // We have an object that is not part of the hierarchy (frequent with Greek "δὲ").
            // Such an object is not displayable.
            jq = $(`<span class="${spanclass}"><span class="nogram dontshowit" data-idd="${this.displayedMo.mo.id_d}" data-mix="0"></span></span>`);
        }
        else if (configuration.databaseName=='ETCBC4' && this.level==2) {
            // Special case: Add indentation information to Hebrew clauses.
            // Note that the indentation <span class="xgrammar...> element is added at a less deep HTML level
            // than the other <span class="xgrammar...> elements.
            // (Don't use multi-line `strings` here - we don't want whitespace between the HTML elements.)
            jq = $(`<span class="notdummy ${spanclass}">`
                   + `<span class="gram dontshowit" data-idd="${this.displayedMo.mo.id_d}" data-mix="${this.mix}">`
                   + getObjectShortFriendlyName(this.objType)
                   + grammar
                   + '</span>'
                   + `<span class="xgrammar clause_atom_tab dontshowit indentation" data-indent="${indent}">`
                   + '</span>'
                   + '</span>');
        }
        else {
            // Normal case: We have a displayable object
            // (Don't use multi-line `strings` here - we don't want whitespace between the HTML elements.)
            jq = $(`<span class="notdummy ${spanclass}">`
                   + `<span class="gram dontshowit" data-idd="${this.displayedMo.mo.id_d}" data-mix="${this.mix}">`
                   + getObjectShortFriendlyName(this.objType)
                   + grammar
                   + '</span>'
                   + '</span>');
        }

        // Generate HTML for Emdros objects at lower levels
        for (let ch in this.children) {
            if (isNaN(+ch)) continue; // Not numeric
            jq.append(this.children[ch].generateHtml(qd, sentenceTextArr, quizMonads));
        }

        return jq;
    }
}
