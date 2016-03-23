// -*- js -*-
/* 2013 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk */

/// @file
/// @brief Main functions for handling text display a quizzes.

/// @if Ignore these in documentation
/// <reference path="bootstrap/bootstrap.d.ts" />
/// <reference path="jqueryui/jqueryui.d.ts" />
/// <reference path="util.ts" />
/// <reference path="configuration.ts" />
/// <reference path="sentencegrammar.ts" />
/// <reference path="charset.ts" />
/// <reference path="monadobject.ts" />
/// <reference path="displaymonadobject.ts" />
/// <reference path="localization.ts" />
/// <reference path="localization_general.ts" />
/// <reference path="quizdata.ts" />
/// <reference path="dictionary.ts" />
/// <reference path="panelquestion.ts" />
/// <reference path="stringwithsort.ts" />
/// <reference path="quiz.ts" />
/// <reference path="resizer.ts" />
/// @endif

// If you want to compile with --noImplicitAny, you will now (as of TypeScript v1) get this error:
//      error TS7017: Index signature of object type implicitly has an 'any' type.
// if you are indexing arrays with strings.
// According to https://typescript.codeplex.com/discussions/535628 you can fix this by adding:
//      interface Object {
//          [idx: string]: any;
//      }
// to the beginning of your code. Unfortunately, this breaks a definition of data() in the
// JQuery interface.


declare var useTooltip : boolean; ///< Does the user use tooltips rather than grammardisplay?

var supportsProgress : boolean; ///< Does the browser support &lt;progress&gt;?

var charset : Charset;

var inQuiz : boolean;
var quiz : Quiz;

var accordion_width : number;

var indentation_width : number;


/// Ensures that the width of a &lt;span class="levX"&gt; is at least as wide as the &lt;span
/// class="gram"&gt; holding its grammar information.
/// @param[in] level Object level (word=0, phrase=1, etc.)
function adjustDivLevWidth(level : number) {
    $('.showborder.lev' + level).each(function(index:number) {
        $(this).css('width','auto'); // Give div natural width

        var w = $(this).find('> .gram').width();
        if ($(this).width()<w)
            $(this).width(w); // Set width of div to width of information
        });
 }


// Creates HTML for checkboxes that select what grammar to display
class GenerateCheckboxes {
    private hasSeenGrammarGroup : boolean;
    private checkboxes : string = '';
    private addBr = new util.AddBetween('<br>'); ///< AddBetween object to insert &lt;br&gt;

    private borderBoxes : util.BorderFollowerBox[] = [];
    private separateLinesBoxes : util.SeparateLinesFollowerBox[] = [];
    private wordSpaceBox : util.WordSpaceFollowerBox;
    
    private generatorCallback(whattype:number, objType:string, featName:string,
                              featNameLoc:string, sgiObj:SentenceGrammarItem) : void {
        switch (whattype) {
        case WHAT.groupstart:
            if (!this.hasSeenGrammarGroup) {
                this.hasSeenGrammarGroup = true;
                this.checkboxes += '<div class="subgrammargroup">';
            }
            this.checkboxes += '<div class="grammargroup"><h2>{0}</h2><div>'.format(featNameLoc);
            this.addBr.reset();
            break;
    
        case WHAT.groupend:
            this.checkboxes += '</div></div>';
            break;
    
        case WHAT.feature:
        case WHAT.metafeature:
            if (mayShowFeature(objType,featName,sgiObj)) {
                this.checkboxes += '{0}<input id="{1}_{2}_cb" type="checkbox">{3}'
                    .format(this.addBr.getStr(),objType,featName,featNameLoc);
        
                var wordclass : string;
                if (whattype===WHAT.feature && getFeatureSetting(objType,featName).foreignText)
                    wordclass = charset.foreignClass;
                else if (whattype===WHAT.feature && getFeatureSetting(objType,featName).transliteratedText)
                    wordclass = charset.transliteratedClass;
                else
                    wordclass = 'latin';
            }
            else
                this.checkboxes += '{0}<input id="{1}_{2}_cb" type="checkbox" disabled>{3}'.format(this.addBr.getStr(),
                                                                                                   objType,
                                                                                                   featName,
                                                                                                   featNameLoc);
            break;
        }
    }

    /// Creates checkboxes related to objects (word, phrase, clause, etc.).
    /// @param level Object level (word=0, phrase=1, etc.)
    /// @return HTML for creating a checkbox
    private makeCheckBoxForObj(level : number) : string {
        if (level==0) {
            // Object is word
            if (charset.isHebrew)
                return '{0}<input id="ws_cb" type="checkbox">{1}</span>'.format(this.addBr.getStr(),localize('word_spacing'));
            else
                return '';
        }
        else  // Object is phrase, clause etc.
            return '{0}<input id="lev{1}_seplin_cb" type="checkbox">{2}</span><br><input id="lev{1}_sb_cb" type="checkbox">{3}</span>'.format(this.addBr.getStr(),level,localize('separate_lines'),localize('show_border'));
    }

    public generateHtml() : string {
        for (var level in configuration.sentencegrammar) {
            var leveli : number = +level;
            if (isNaN(leveli)) continue; // Not numeric

            var objType : string = configuration.sentencegrammar[leveli].objType;

            this.addBr.reset();

            this.checkboxes += '<div class="objectlevel"><h1>' + getObjectFriendlyName(objType) + '</h1><div>';
            this.checkboxes += this.makeCheckBoxForObj(leveli);

            /// @todo This works if only one &lt;div class="objectlevel"&gt; has any &lt;div class="grammargroup"&gt; children
            /// and the grammargroups are not intermixed with grammarfeatures

            this.hasSeenGrammarGroup = false;

            configuration.sentencegrammar[leveli]
                .getFeatName(configuration.sentencegrammar[leveli].objType,
                             (whattype:number, objType:string, featName:string, featNameLoc:string, sgiObj:SentenceGrammarItem) =>
                             this.generatorCallback(whattype, objType, featName, featNameLoc, sgiObj));

            if (this.hasSeenGrammarGroup)
                this.checkboxes += '</div>';

            this.checkboxes += '</div></div>'
        }

        return this.checkboxes;
    }
    

    private setHandlerCallback(whattype:number, objType:string, featName:string, featNameLoc:string, leveli:number) : void {
        if (whattype!=WHAT.feature && whattype!=WHAT.metafeature)
            return;

        if (leveli===0) {
            // Handling of words

            $('#{0}_{1}_cb'.format(objType,featName)).change( (e : JQueryEventObject) => {
                if ($(e.currentTarget).prop('checked')) {
                    if (!inQuiz)
                        sessionStorage.setItem($(e.currentTarget).prop('id'),configuration.propertiesName);
                    $('.wordgrammar.{0}'.format(featName)).removeClass('dontshowit').addClass('showit');
                    this.wordSpaceBox.implicit(true);
                }
                else {
                    if (!inQuiz)
                        sessionStorage.removeItem($(e.currentTarget).prop('id'));
                    $('.wordgrammar.{0}'.format(featName)).removeClass('showit').addClass('dontshowit');
                    this.wordSpaceBox.implicit(false);
                }
                                           
                for (var lev=1; lev<configuration.maxLevels-1; ++lev)
                    adjustDivLevWidth(lev);
            });
        }
        else {
            // Handling of clause, phrase, etc.
                                   
            $('#{0}_{1}_cb'.format(objType,featName)).change( (e : JQueryEventObject) => {
                if ($(e.currentTarget).prop('checked')) {
                    if (!inQuiz)
                        sessionStorage.setItem($(e.currentTarget).prop('id'),configuration.propertiesName);
                    $('.xgrammar.{0}_{1}'.format(objType,featName)).removeClass('dontshowit').addClass('showit');
                    if (configuration.databaseName=='ETCBC4' && leveli==2 && objType=="clause_atom" && featName=="tab") {
                        this.separateLinesBoxes[leveli].implicit(true);
                        $('.lev2').css(charset.isRtl ? 'padding-right' : 'padding-left',indentation_width + 'px').css('text-indent',-indentation_width + 'px');
                    }
                    else
                        this.borderBoxes[leveli].implicit(true);
                }
                else {
                    if (!inQuiz)
                        sessionStorage.removeItem($(e.currentTarget).prop('id'));
                    $('.xgrammar.{0}_{1}'.format(objType,featName)).removeClass('showit').addClass('dontshowit');
                    if (configuration.databaseName=='ETCBC4' && leveli==2 && objType=="clause_atom" && featName=="tab") {
                        this.separateLinesBoxes[leveli].implicit(false);
                        $('.lev2').css(charset.isRtl ? 'padding-right' : 'padding-left','0').css('text-indent','0');
                    }
                    else
                        this.borderBoxes[leveli].implicit(false);
                }
                
                adjustDivLevWidth(leveli);
            });
        }
    }
        
    // Set up handling of checkboxes
    public setHandlers() : void {
        for (var level in configuration.sentencegrammar) {
            var leveli : number = +level;
            if (isNaN(leveli)) continue; // Not numeric

            var sg : SentenceGrammar = configuration.sentencegrammar[leveli];
        
            if (leveli===0) {
                // Although only Hebrew uses a word spacing checkbox, the mechanism is also used by Greek,
                // because we use it to set up the inline-blocks for word grammar information.
                this.wordSpaceBox = new util.WordSpaceFollowerBox(leveli);

                // Only Hebrew has a #ws_cb
                $('#ws_cb').change((e : JQueryEventObject) => {
                    if ($(e.currentTarget).prop('checked')) {
                        if (!inQuiz)
                            sessionStorage.setItem($(e.currentTarget).prop('id'),configuration.propertiesName);
                        this.wordSpaceBox.explicit(true);
                    }
                    else {
                        if (!inQuiz)
                            sessionStorage.removeItem($(e.currentTarget).prop('id'));
                        this.wordSpaceBox.explicit(false);
                    }
                        
                    for (var lev=1; lev<configuration.maxLevels-1; ++lev)
                        adjustDivLevWidth(lev);
                });
            }
            else {
                this.separateLinesBoxes[leveli] = new util.SeparateLinesFollowerBox(leveli);

                $('#lev{0}_seplin_cb'.format(leveli)).change(leveli, (e : JQueryEventObject) => {
                    if ($(e.currentTarget).prop('checked')) {
                        if (!inQuiz)
                            sessionStorage.setItem($(e.currentTarget).prop('id'),configuration.propertiesName);
                        this.separateLinesBoxes[e.data].explicit(true);
                    }
                    else {
                        if (!inQuiz)
                            sessionStorage.removeItem($(e.currentTarget).prop('id'));
                        this.separateLinesBoxes[e.data].explicit(false);
                    }
                });

                this.borderBoxes[leveli] = new util.BorderFollowerBox(leveli);
                
                $('#lev{0}_sb_cb'.format(leveli)).change(leveli, (e : JQueryEventObject) => {
                    if ($(e.currentTarget).prop('checked')) {
                        if (!inQuiz)
                            sessionStorage.setItem($(e.currentTarget).prop('id'),configuration.propertiesName);
                        this.borderBoxes[e.data].explicit(true);
                    }
                    else {
                        if (!inQuiz)
                            sessionStorage.removeItem($(e.currentTarget).prop('id'));
                        this.borderBoxes[e.data].explicit(false);
                    }

                    adjustDivLevWidth(e.data);
                });
            }

            sg.getFeatName(sg.objType,
                           (whattype:number, objType:string, featName:string, featNameLoc:string) =>
                           this.setHandlerCallback(whattype, objType, featName, featNameLoc, leveli));
        }
    }

    public static clearBoxes(force : boolean) {
        $('input[type="checkbox"]').prop('checked',false);

        if (!inQuiz) {
            if (force) {
                // Remove all information about selected grammar items
                for (var i in sessionStorage) {
                    if (sessionStorage[i]==configuration.propertiesName) {
                        sessionStorage.removeItem(i);
                        $('#' + i).prop('checked',false);
                        $('#' + i).trigger('change');
                    }
                }
            }
            else {
                // Enforce selected grammar items
                for (var i in sessionStorage) {
                    if (sessionStorage[i]==configuration.propertiesName)
                        $('#' + i).prop('checked',true);
                }
            }
        }
    }
}


// Build accordion for grammar selector.
// Returns its width
function buildGrammarAccordion() : number {
    var acc1 : JQuery = $('#gramselect').accordion({heightStyle: 'content', collapsible: true, header: 'h1'});
    var acc2 : JQuery = $('.subgrammargroup').accordion({heightStyle: 'content', collapsible: true, header: 'h2'});

    /// @todo Does this work if there are multiple '.subgrammargroup' divs?
    var max_width  = 0;
    for (var j=0; j<acc2.find('h2').length; ++j) {
        acc2.accordion('option','active',j);
        if (acc2.width() > max_width)
            max_width = acc2.width();
    }
    acc2.accordion('option','active',false); // No active item 
    acc2.width(max_width*1.05);  // I don't know why I have to add 5% here

    max_width = 0;
    for (var j=0; j<acc1.find('h1').length; ++j) {
        acc1.accordion('option','active',j);
        if (acc1.width() > max_width)
            max_width = acc1.width();
    }
    acc1.accordion('option','active',false);
    acc1.width(max_width);
   
    return max_width;
}


/// Main code executed when the page has been loaded.
$(function() {
    inQuiz = $('#quiztab').length>0;

    // Does the browser support <progress>?
    // (Use two statements because jquery.d.ts does not recognize .max)
    var x : any = document.createElement('progress');
    supportsProgress = x.max != undefined; // Thanks to http://lab.festiz.com/progressbartest/index.htm

    configuration.maxLevels = configuration.sentencegrammar.length+1; // Include patriarch level

    
    // Set up CSS classes for text.
    charset = new Charset(configuration.charSet);
    $('#textarea').addClass(charset.isRtl ? 'rtl' : 'ltr');


    for (var i in configuration.sentencegrammar) {
        if (isNaN(+i)) continue; // Not numeric
        addMethodsSgi(configuration.sentencegrammar[+i], configuration.sentencegrammar[+i].objType);
    }


    // Create HTML for checkboxes that select what grammar to display
    var generateCheckboxes = new GenerateCheckboxes();
    $('#gramselect').append(generateCheckboxes.generateHtml());
    generateCheckboxes.setHandlers();
    GenerateCheckboxes.clearBoxes(false);


    accordion_width = buildGrammarAccordion();

    if (inQuiz) {
        if (supportsProgress)
            $('div#progressbar').hide();
        else
            $('progress#progress').hide();

        quiz = new Quiz(quizdata.quizid);
        quiz.nextQuestion();
    }
    else {
        // Display text
        $('#cleargrammar').on('click',() => { GenerateCheckboxes.clearBoxes(true); });

        var currentDict : Dictionary = new Dictionary(dictionaries,0,false);
        currentDict.generateSentenceHtml(null);
        $('.grammarselector input:enabled:checked').trigger('change'); // Make sure grammar is displayed for relevant checkboxe
    }
});


