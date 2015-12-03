// -*- js -*-
/* 2013 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk */

interface DictionaryIf {
    sentenceSets : MonadSet[];
    monadObjects: MonadObject[][][]; // First index is sentence set number, second index is level (word, phrase, clause etc.), third index gives the actual object
    bookTitle : string;
}


declare var dictionaries : DictionaryIf;


class Dictionary {
    public sentenceSet : MonadSet;
    public monadObjects1: MonadObject[][]; // First index is level (word, phrase, clause etc.), second index gives the actual object
    public bookTitle : string;

    public monads : MonadObject[] = []; // Maps id_d => monad object
    public level : number[] = []; // Maps id_d => object level
    private singleMonads : SingleMonadObject[] = [];
    public dispMonadObjects : DisplayMonadObject[][] = [];

    private toolTipFunc : (x_this : Element, set_head : boolean) => util.Pair<string,string>; // first is content, second is heading


    constructor(dictif : DictionaryIf, index : number, inQuiz : boolean) {
        this.sentenceSet = dictif.sentenceSets[index];
        this.monadObjects1 = dictif.monadObjects[index];
        this.bookTitle = dictif.bookTitle;

        // Index monads
        for (var level in this.monadObjects1) {
            var leveli : number = +level;
            if (isNaN(leveli)) continue; // Not numeric

            for (var i in this.monadObjects1[leveli]) {
                if (isNaN(+i)) continue; // Not numeric

                var item = this.monadObjects1[leveli][+i];
                if (leveli===0)
                    this.singleMonads[getSingleInteger(item.mo.monadset)] = <SingleMonadObject>item;
                this.monads[item.mo.id_d] = item;
                this.level[item.mo.id_d] = leveli;
            }
        }

        // Bind parents and children
	for (var i in this.monads) {
            if (isNaN(+i)) continue; // Not numeric

	    var parent : MonadObject = this.monads[+i];
	    for (var i2 in parent.children_idds) {
                if (isNaN(+i2)) continue; // Not numeric

		var child_idd : number = parent.children_idds[+i2];
                this.monads[child_idd].parent = parent;
            }
        }


        // Create display hierarchy
        // Single monads
        var objType = configuration.sentencegrammar[0].objType;
        this.dispMonadObjects.push([]);

        // singleMonads is sparsely populated
        for (var se in this.singleMonads) {
            if (isNaN(+se)) continue; // Not numeric

	    var dmo = new DisplaySingleMonadObject(this.singleMonads[+se], objType, inQuiz);
	    this.dispMonadObjects[0].push(dmo);
	    // Do we need this?: dispSingleMonads[se] = dmo;
        }


        // Multiple monads
        for (var lev : number = 1; lev<configuration.maxLevels; ++lev) {
            var ldmo : DisplayMonadObject[] = [];

            this.dispMonadObjects.push(ldmo);

            if (/*inQuiz || */ lev<configuration.maxLevels-1)
                objType = configuration.sentencegrammar[lev].objType;
            else
                objType = 'Patriarch'; //$NON-NLS-1$
            
            if (lev<configuration.maxLevels-1) {
                for (var i in this.monadObjects1[lev]) {
                    if (isNaN(+i)) continue; // Not numeric

                    var monadObject : MonadObject = this.monadObjects1[lev][parseInt(i)];
                    // Split object into contiguous segments
                    var segCount : number = monadObject.mo.monadset.segments.length;
                    
                    for (var mix : number = 0; mix<segCount; ++mix) {
                        var mp : MonadPair = monadObject.mo.monadset.segments[mix];
                        ldmo.push(new DisplayMultipleMonadObject(monadObject,
                                                                 objType,
                                                                 lev,
                                                                 mp,
                                                                 mix>0, 
                                                                 mix<segCount-1));
                    }
                }
                
                ldmo.sort(
                    function(a : DisplayMonadObject, b : DisplayMonadObject) {
                        // Sort in monad order
                        return a.range.low - b.range.low;
                    });
            }
            else {
                // At the top level there is always only one DisplayMultipleMonadObject
                var monadObject : MonadObject = this.monadObjects1[lev][0];
                ldmo.push(new DisplayMultipleMonadObject(monadObject,
                                                         objType,
                                                         lev,
                                                         monadObject.mo.monadset));
            }
        }


        /////////////////////////////////////////////////////////
        // Construct child-parent linkage for DisplayMonadObjects
        /////////////////////////////////////////////////////////

        for (var lev : number = 1; lev<configuration.maxLevels; ++lev) {
            // Find constituent MonadObjects
            
            // Loop through monads at level lev
            for (var parentDmoIx in this.dispMonadObjects[lev]) {
                if (isNaN(+parentDmoIx)) continue; // Not numeric

                var parentDmo : DisplayMonadObject = this.dispMonadObjects[lev][+parentDmoIx];

                // Loop through mondads at child level
                for (var childDmoIx in this.dispMonadObjects[lev-1]) {
                    if (isNaN(+childDmoIx)) continue; // Not numeric

                    var childDmo : DisplayMonadObject = this.dispMonadObjects[lev-1][+childDmoIx];
                    if (childDmo.containedIn(parentDmo)) {
                        // We found a child
                        if (childDmo.parent!=undefined) throw 'BAD1'; // Ensures that the tree is properly constructed
                        childDmo.parent = <DisplayMultipleMonadObject>parentDmo;
                        parentDmo.children.push(childDmo);
                    }
                }
            }
        }
    }

    private hoverForGrammar() {
        var thisDict : Dictionary = this;

        if (useTooltip) {
            $(document).tooltip(
                {
                    items: "[data-idd]",
                    disabled: false, 
                    content: function() { return thisDict.toolTipFunc(this,true).first; }
                });
        }
        else {
            $("[data-idd]")
                .hover(
                    function() {
                        // Calculate vertical position of '.grammardisplay'.
                        // It should be placed at least 20px from top of window but not higher
                        // than '#textcontainer'
                        var scrTop   : number = $(window).scrollTop();
                        var qcTop    : number = $('#textcontainer').offset().top;
                        $('.grammardisplay')
                            .html(thisDict.toolTipFunc(this,true).first)
                            .css('top',Math.max(0,scrTop-qcTop+5))
                            .outerWidth($('#grammardisplaycontainer').outerWidth()-25) // 25px is a littel mora than margin-right
                            .show();
                    },
                    function () {
                        $('.grammardisplay').hide();
                    }
                );
            $("[data-idd]").off('click');
        }
    }
 
    private clickForGrammar() {
        if (useTooltip)
            $(document).tooltip({items: "[data-idd]", disabled: true});
        else
            $("[data-idd]").off("mouseenter mouseleave");

        $("[data-idd]").on('click', (event : any) => {
            var info = this.toolTipFunc(event.currentTarget,false);
            $('#grammar-info-label').html(info.second);
            $('#grammar-info-body').html(info.first);
            $('#grammar-info-dialog').modal('show');
        });
    }

    private static handleDisplaySize(thisDict : Dictionary) {
        switch (resizer.getWindowSize()) {
        case 'xs':
            thisDict.clickForGrammar();
            break;

        default:
            thisDict.hoverForGrammar();
            break;
        }
    }

    public generateSentenceHtml(qd : QuizData) : string {
        DisplaySingleMonadObject.itemIndex = 0;
        var sentenceTextArr : string[] = [''];
        $('#textarea').append(this.dispMonadObjects[this.dispMonadObjects.length-1][0].generateHtml(qd,sentenceTextArr));


        var thisDict : Dictionary = this;

        this.toolTipFunc  =
            function(x_this : Element, set_head : boolean) : util.Pair<string,string> {
                var monob : MonadObject = thisDict.monads[+($(x_this).attr("data-idd"))];
                var level : number = thisDict.level[+($(x_this).attr("data-idd"))];
                var sengram : SentenceGrammar = configuration.sentencegrammar[level];

                var res : string = '<table>';

                if (set_head)
                    res += '<tr><td colspan="2" class="tooltiphead">{0}</td></tr>'.format(getObjectFriendlyName(sengram.objType));

                if (level===0 && (!qd || !qd.quizFeatures.dontShow))
                    res += '<tr><td>{2}</td><td class="bol-tooltip leftalign {0}">{1}</td></tr>'.format(charset.foreignClass, monob.mo.features[configuration.surfaceFeature],localize('visual'));
                
                var map : Array<string> = [];
    
                sengram.getFeatName(sengram.objType,
                                    (whattype:number, objType:string, featName:string, featNameLoc:string, sgiObj:SentenceGrammarItem) => {
                                        if (whattype==WHAT.feature || whattype==WHAT.metafeature)
                                            if (!mayShowFeature(objType, featName, sgiObj))
                                                return;
                                        
                                        if (whattype==WHAT.feature || whattype==WHAT.metafeature || whattype==WHAT.groupstart)
                                            map[featName] = featNameLoc;
                                    });

                sengram.getFeatVal(monob,sengram.objType,false,
                                   (whattype:number, objType:string, featName:string, featVal:string, featValLoc:string, sgiObj:SentenceGrammarItem) => {
                                       switch (whattype) {
                                       case WHAT.feature:
                                           if (mayShowFeature(objType, featName, sgiObj)) {
                                               var wordclass : string;
                                               var fs : FeatureSetting = getFeatureSetting(objType,featName);
                                               if (fs.foreignText)
                                                   wordclass = charset.foreignClass;
                                               else if (fs.transliteratedText)
                                                   wordclass = charset.transliteratedClass;
                                               else
                                                   wordclass = '';
                                               res += '<tr><td>{0}</td><td class="bol-tooltip leftalign {2}">{1}</td></tr>'.format(map[featName], featValLoc, featValLoc==='-' ? '' : wordclass);
                                           }
                                           break;

                                       case WHAT.metafeature:
                                           if (mayShowFeature(objType, featName, sgiObj))
                                               res += '<tr><td>{0}</td><td class="bol-tooltip leftalign">{1}</td></tr>'.format(map[featName], featValLoc);
                                           break;


                                       case WHAT.groupstart:
                                           res += '<tr><td><b>{0}:</b></td><td class="leftalign"></td></tr>'.format(map[featName]);
                                           break;
                                       }
                                   });
                
                return new util.Pair(res + '</table>', getObjectFriendlyName(sengram.objType));
            };

        resizer.addResizeListener(Dictionary.handleDisplaySize, this, 'xyzzy');
        Dictionary.handleDisplaySize(this);

        return sentenceTextArr[0];
    }

    private showattrs(idd : number) : void {
        var monob : MonadObject = this.monads[idd];

        for (var level=0; level<configuration.maxLevels-1; ++level) {
            configuration.sentencegrammar[level]
                .getFeatVal(monob,configuration.sentencegrammar[level].objType,false,
                            (whattype:number, objType:string, featName:string, featVal:string, featValLoc:string, sgiObj:SentenceGrammarItem) => {
                                if (whattype==WHAT.feature || whattype==WHAT.metafeature)
                                    $('#{0}_{1}_show'.format(objType,featName)).html(featValLoc);
                            });
            monob = monob.parent;
        }
    }



    public getSingleMonadObject(monad : number) : SingleMonadObject {
        return this.singleMonads[monad];
    }

}
