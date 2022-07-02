// -*- js -*-
// Copyright © 2018 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk

// Many changes by Ernst Boogert on 3 Nov 2020 to create new fancy look of the grammar selection box.


//****************************************************************************************************
// About the IDs of HTML elements in the grammar selection box:
//
// The grammar selection box has the id "gramselect".
//
// A checkbox for Emdros object OOO feature FFF has the id "OOO_FFF_cb".
//
// The checkbox for "word spacing" (Hebrew only) has the id "ws_cb".
//
// A checkbox for "separate lines" for an Emdros object at level LLL (where, for example, 1 means
// phrase, 2 means clause, etc.) has the id "levLLL_seplin_cb".
//
// A checkbox for "show borders" for an Emdros object at level LLL (where, for example, 1 means
// phrase, 2 means clause, etc.) has the id "levLLL_sb_cb".
//
// The "Clear grammar" button has the id "cleargrammar".
//
//****************************************************************************************************

function getSessionValue() : any
{
    let sessionValue : any;
    
    try {
        sessionValue = JSON.parse(sessionStorage.getItem(configuration.propertiesName));
    }
    catch (e) {
        sessionValue = {};
    }

    if (!sessionValue)
        sessionValue = {};

    return sessionValue;
}

function setSessionValue(sessionValue : any) : void
{
    sessionStorage.setItem(configuration.propertiesName, JSON.stringify(sessionValue));
}

function setOneSessionValue(key : string, value : any) : void
{
    let sessionValue = getSessionValue();
    sessionValue[key] = value;
    setSessionValue(sessionValue);
}


//****************************************************************************************************
// GrammarSelectionBox class
//
// This singleton class creates HTML for the contents of the grammar selection box.
//
class GrammarSelectionBox {
    private hasSeenGrammarGroup : boolean;       // True if the generatorCallBack() method has seen a grammar group.
    private checkboxes          : string = '';   // Holds the generated HTML
    private subgroupgrammartabs : string = '';   // Holds temporary HTML for grammar subgroup tabs
    private subgroupgrammardivs : string = '';   // Holds temporary HTML for grammar subgroup divs
    private addBr = new util.AddBetween('<br>'); // AddBetween object to insert <br>

    private borderBoxes         : util.BorderFollowerBox[] = [];        // Handles checkbox for "show borders"
    private separateLinesBoxes  : util.SeparateLinesFollowerBox[] = []; // Handles checkbox for "separate lines"
    private wordSpaceBox        : util.WordSpaceFollowerBox;            // Handles checkbox for "word spacing"

    private seenFreqRank        : boolean = false;
    
    //****************************************************************************************************
    // adjustDivLevWidth static method
    //
    // Ensures that the width of a <span class="levX"> is at least as wide as the <span
    // class="gram"> holding its grammar information.
    //
    // Parameter:
    //    level Object level (word=0, phrase=1, etc.)
    //
    private static adjustDivLevWidth(level : number) : void {
        $(`.showborder.lev${level}`).each(function(index:number) {
            $(this).css('width','auto'); // Give div natural width

            let w = $(this).find('> .gram').width();
            if ($(this).width() < w + 10) // +10 is to creats some extra space
                $(this).width(w + 10); // Set width of div to width of information
        });
    }

    //------------------------------------------------------------------------------------------
    // generatorCallback method
    //
    // This function is called repeatedly from the walkFeatureNames() method of the
    // SentenceGrammarItem interface. It generates HTML code that goes into the grammar selection
    // box. The HTML code is appended to this.checkboxes.
    //
    // Parameters:
    //    whattype: Identification of the current point in the walkthrough.
    //    objType: The type of the current grammar object.
    //    origObjType: The original type of the current grammar object. (This can be different from
    //                 objType when, for example, a feature under "clause" has the name "clause_atom:tab".)
    //    featName: The name of the feature.
    //    featNameLoc: Localized feature name.
    //    sgiObj: The current SentenceGrammarItem object (always 'this').
    //
    private generatorCallback(
        whattype    : WHAT,
        objType     : string,
        origObjType : string,
        featName    : string,
        featNameLoc : string,
        sgiObj      : SentenceGrammarItem) : void {
            switch (whattype) {
                case WHAT.groupstart:
                    if (!this.hasSeenGrammarGroup) {
                        this.hasSeenGrammarGroup = true;
                        this.subgroupgrammartabs += `<div id="grammargroup"><ul>`;
                    }
                    this.subgroupgrammartabs += `<li><a class="grammargroup" href="#${getHtmlAttribFriendlyName(featName)}"><h3>${featNameLoc}</h3></a></li>`;
                    this.subgroupgrammardivs += `<div id="${getHtmlAttribFriendlyName(featName)}">`
                    this.subgroupgrammardivs += `<div id="grammarbuttongroup">`
                    this.addBr.reset();
                    break;
                                          
                case WHAT.groupend:
                    this.subgroupgrammardivs += '</div>';
                    if (this.seenFreqRank && !inQuiz) {
                        this.subgroupgrammardivs += `<div class="color-limit"><span class="color-limit-prompt">${localize('word_frequency_color_limit')}</span><input id="color-limit" type="number" style="width:5em"></div>`;
                        this.seenFreqRank = false;
                    }
                    this.subgroupgrammardivs += '</div>';
                    break;
                                          
                case WHAT.feature:
                case WHAT.metafeature:
                    let disabled: string = mayShowFeature(objType, origObjType, featName, sgiObj) ? '' : 'disabled';
                                          
                if (this.hasSeenGrammarGroup) {
                    if (objType==="word" && featName==="frequency_rank")
                        this.seenFreqRank = true;
                    
                    this.subgroupgrammardivs += `<div class="selectbutton"><input id="${objType}_${featName}_cb" type="checkbox" ${disabled}><label class="${disabled}" for="${objType}_${featName}_cb">${featNameLoc}</label></div>`;
                    } else {
                        this.checkboxes += `<div class="selectbutton"><input id="${objType}_${featName}_cb" type="checkbox" ${disabled}><label class="${disabled}" for="${objType}_${featName}_cb">${featNameLoc}</label></div>`;
                    }
                    break;
            }
        }

    //------------------------------------------------------------------------------------------
    // makeInitCheckBoxForObj method
    //
    // Creates initial checkboxes for features related to objects (word, phrase, clause, etc.).
    // The initial checkboxes are
    //     for words: Word spacing (Hebrew only)
    //     for other objects: Separate lines, show border.
    //
    // Parameter:
    //     level: Object level (word=0, phrase=1, etc.)
    // Returns HTML for creating a checkbox.
    //
    private makeInitCheckBoxForObj(level : number) : string {
        if (level == 0) {
            // Object is word
            if (charset.isHebrew) {
                return `<div class="selectbutton"><input id="ws_cb" type="checkbox">` +
                    `<label for="ws_cb">${localize('word_spacing')}</label></div>`;
            }
            else
                return '';
        }
        else {
            // Object is phrase, clause etc.
            return `<div class="selectbutton"><input id="lev${level}_seplin_cb" type="checkbox">` +
                `<label for="lev${level}_seplin_cb">${localize('separate_lines')}</label></div>` +
                `<div class="selectbutton"><input id="lev${level}_sb_cb" type="checkbox">` +
                `<label for="lev${level}_sb_cb">${localize('show_border')}</label></div>`;
        }
    }

    //------------------------------------------------------------------------------------------
    // generateHtml method
    //
    // This is the main method of the class. It generates the HTML that displays the contents of the
    // grammar selection box.
    //
    // Returns:
    //     HTML code.
    //
    public generateHtml(): string {
        // Loop through 'word', 'phrase', 'clause', 'sentence' or the like
        this.checkboxes += `<ul>`;

        for (let level in configuration.sentencegrammar) {
            let leveli: number = +level;
            if (isNaN(leveli)) continue; // Not numeric

            let objType: string = configuration.sentencegrammar[leveli].objType; // objType is 'word', 'phrase' etc.

            this.checkboxes += `<li><a class="gramtabs" href="#${getHtmlAttribFriendlyName(objType)}"><h3>${getObjectFriendlyName(objType)}</h3></a></li>`;
        }
        this.checkboxes += `</ul>`;
             
        for (let level in configuration.sentencegrammar) {
            let leveli: number = +level;
            if (isNaN(leveli)) continue; // Not numeric

            let objType: string = configuration.sentencegrammar[leveli].objType; // objType is 'word', 'phrase' etc.
            
            this.checkboxes += `<div id="${getHtmlAttribFriendlyName(objType)}">`
            this.checkboxes += `<div class="objectlevel">`;
            this.checkboxes += `<div id="grammarbuttongroup">`;
            this.checkboxes += this.makeInitCheckBoxForObj(leveli);

            /// TO DO: This only works if the grammargroups are not intermixed with grammarfeatures.

            this.hasSeenGrammarGroup = false;
            
            configuration.sentencegrammar[leveli]
                .walkFeatureNames(objType, (whattype: WHAT,
                    objType: string,
                    origObjType: string,
                    featName: string,
                    featNameLoc: string,
                    sgiObj: SentenceGrammarItem) => this.generatorCallback(whattype,
                                                                           objType,
                                                                           origObjType,
                                                                           featName,
                                                                           featNameLoc,
                                                                           sgiObj));

            if (this.hasSeenGrammarGroup)
                // End the tablist and the divs and append them to this.checkboxes
                this.checkboxes += '</div>'
            this.checkboxes += this.subgroupgrammartabs + '</ul>' + this.subgroupgrammardivs + '</div>';
            this.subgroupgrammartabs = '';
            this.subgroupgrammardivs = '';

            this.checkboxes += '</div></div>';
        }
        this.checkboxes += `<button class="btn btn-clear" id="cleargrammar">${localize('clear_grammar')}</button>`;
        return this.checkboxes;
    }

    //------------------------------------------------------------------------------------------
    // setHandlerCallback method
    //
    // This function is called repeatedly from the walkFeatureNames() method of the
    // SentenceGrammarItem interface. It sets up event handlers for checking/unchecking of
    // checkboxes in the grammar selection box.
    //
    // Parameters:
    //    whattype: Identification of the current point in the walkthrough.
    //    objType: The type of the current grammar object.
    //    featName: The name of the feature.
    //    featNameLoc: Localized feature name.
    //    leveli: The sentence grammar index (0 for word, 1 for phrase, etc.)
    //
    private setHandlerCallback(whattype    : WHAT,
                               objType     : string,
                               featName    : string,
                               featNameLoc : string,
                               leveli      : number) : void {
                                   if (whattype!=WHAT.feature && whattype!=WHAT.metafeature)
                                       return;

                                   if (leveli===0) { // Handling of words

                                       $(`#${objType}_${featName}_cb`).change( (e : JQuery.TriggeredEvent, ...isManual : any) => {
                                           if ($(e.currentTarget).prop('checked')) {
                                               if (!inQuiz && isManual[0]!='manual') {
                                                   // Save setting in browser
                                                   setOneSessionValue($(e.currentTarget).prop('id'), true);
                                               }
                                               $(`.wordgrammar.${featName}`).removeClass('dontshowit').addClass('showit');
                                               this.wordSpaceBox.implicit(true);
                                           }
                                           else {
                                               if (!inQuiz && isManual[0]!='manual') {
                                                   // Remove setting from browser
                                                   setOneSessionValue($(e.currentTarget).prop('id'), false);
                                               }
                                               $(`.wordgrammar.${featName}`).removeClass('showit').addClass('dontshowit');
                                               this.wordSpaceBox.implicit(false);
                                           }
                                           
                                           for (let lev=1; lev<configuration.maxLevels-1; ++lev)
                                               GrammarSelectionBox.adjustDivLevWidth(lev);
                                       });
                                   }
                                   else { // Handling of clause, phrase, etc.
                                       
                                       $(`#${objType}_${featName}_cb`).change( (e : JQuery.TriggeredEvent, ...isManual : any) => {
                                           if ($(e.currentTarget).prop('checked')) {
                                               if (!inQuiz && isManual[0]!='manual') {
                                                   // Save setting in browser
                                                   setOneSessionValue($(e.currentTarget).prop('id'), true);
                                               }
                                               $(`.xgrammar.${objType}_${featName}`).removeClass('dontshowit').addClass('showit');
                                               if (configuration.databaseName=='ETCBC4' && leveli==2 && objType=="clause_atom" && featName=="tab") {
                                                   this.separateLinesBoxes[leveli].implicit(true);
                                                   $('.lev2').css(charset.isRtl ? 'padding-right' : 'padding-left',indentation_width + 'px').css('text-indent',-indentation_width + 'px');
                                               }
                                               else
                                                   this.borderBoxes[leveli].implicit(true);
                                           }
                                           else {
                                               if (!inQuiz && isManual[0]!='manual') {
                                                   // Remove setting from browser
                                                   setOneSessionValue($(e.currentTarget).prop('id'), false);
                                               }
                                               $(`.xgrammar.${objType}_${featName}`).removeClass('showit').addClass('dontshowit');
                                               if (configuration.databaseName=='ETCBC4' && leveli==2 && objType=="clause_atom" && featName=="tab") {
                                                   this.separateLinesBoxes[leveli].implicit(false);
                                                   $('.lev2').css(charset.isRtl ? 'padding-right' : 'padding-left','0').css('text-indent','0');
                                               }
                                               else
                                                   this.borderBoxes[leveli].implicit(false);
                                           }
                                           
                                           GrammarSelectionBox.adjustDivLevWidth(leveli);
                                       });
                                   }
                               }
    
    //------------------------------------------------------------------------------------------
    // setHandlers method
    //
    // Sets up event handlers for checking/unchecking of checkboxes in the grammar selection box.
    // This function itself handles the "word spacing", "separate lines", and "show border"
    // checkboxes; it then calls walkFeatureNames() to handle the other checkboxes.
    //
    public setHandlers() : void {
        for (let level in configuration.sentencegrammar) {
            let leveli : number = +level;
            if (isNaN(leveli)) continue; // Not numeric

            let sg : SentenceGrammar = configuration.sentencegrammar[leveli];
            
            if (leveli===0) { // Handling of words

                // Set change handler for the checkbox for "word spacing".
                // Although only Hebrew uses a word spacing checkbox, the mechanism is also used by Greek,
                // because we use it to set up the inline-blocks for word grammar information.
                this.wordSpaceBox = new util.WordSpaceFollowerBox(leveli);

                // Only Hebrew has a #ws_cb
                $('#ws_cb').change( (e : JQuery.TriggeredEvent, ...isManual : any) => {
                    if ($(e.currentTarget).prop('checked')) {
                        if (!inQuiz && isManual[0]!='manual') {
                            // Save setting in browser
                            setOneSessionValue($(e.currentTarget).prop('id'), true);
                        }
                        this.wordSpaceBox.explicit(true);
                    }
                    else {
                        if (!inQuiz && isManual[0]!='manual') {
                            // Remove setting from browser
                            setOneSessionValue($(e.currentTarget).prop('id'), false);
                        }
                        this.wordSpaceBox.explicit(false);
                    }
                    
                    for (let lev=1; lev<configuration.maxLevels-1; ++lev)
                        GrammarSelectionBox.adjustDivLevWidth(lev);
                });
            }
            else { // Handling of clause, phrase, etc.
                
                // Set change handlers for the checkboxes for "separate lines" and "show border".
                this.separateLinesBoxes[leveli] = new util.SeparateLinesFollowerBox(leveli);

                $(`#lev${leveli}_seplin_cb`).change(leveli, (e : JQuery.TriggeredEvent, ...isManual : any) => {
                    if ($(e.currentTarget).prop('checked')) {
                        if (!inQuiz && isManual[0]!='manual') {
                            // Save setting in browser
                            setOneSessionValue($(e.currentTarget).prop('id'), true);
                        }
                        this.separateLinesBoxes[e.data].explicit(true);
                    }
                    else {
                        if (!inQuiz && isManual[0]!='manual') {
                            // Remove setting from browser
                            setOneSessionValue($(e.currentTarget).prop('id'), false);
                        }
                        this.separateLinesBoxes[e.data].explicit(false);
                    }
                });

                this.borderBoxes[leveli] = new util.BorderFollowerBox(leveli);
                
                $(`#lev${leveli}_sb_cb`).change(leveli, (e : JQuery.TriggeredEvent, ...isManual : any) => {
                    if ($(e.currentTarget).prop('checked')) {
                        if (!inQuiz && isManual[0]!='manual') {
                            // Save setting in browser
                            setOneSessionValue($(e.currentTarget).prop('id'), true);
                        }
                        this.borderBoxes[e.data].explicit(true);
                    }
                    else {
                        if (!inQuiz && isManual[0]!='manual') {
                            // Remove setting from browser
                            setOneSessionValue($(e.currentTarget).prop('id'), false);
                        }
                        this.borderBoxes[e.data].explicit(false);
                    }

                    GrammarSelectionBox.adjustDivLevWidth(e.data);
                });
            }

            // Handle the remaining checkboxes:
            sg.walkFeatureNames(sg.objType, (whattype    : WHAT,
                                             objType     : string,
                                             origObjType : string,
                                             featName    : string,
                                             featNameLoc : string,
                                             sgiObj      : SentenceGrammarItem) => this.setHandlerCallback(whattype,
                                                                                                           objType,
                                                                                                           featName,
                                                                                                           featNameLoc,
                                                                                                           leveli));
        }
    }

    //------------------------------------------------------------------------------------------
    // clearBoxes static method
    //
    // If we are not running a quiz and 'force' is false, the method checks or unchecks the
    // checkboxes in the grammar selection box according to the values stored in the browser.
    //
    // If we are not running a quiz and 'force' is true, the method unchecks all checkboxes and
    // updates the browser information accordingly.
    //
    // If we are running a quiz, all checkboxes are unchecked, but the browser information is not
    // updated.
    //
    // Parameter:
    //     force: False means set checkboxes according to information in browser.
    //            True means uncheck all checkboxes.
    //
    public static clearBoxes(force : boolean) {
        if (!inQuiz) {
            let sessionValue = getSessionValue();

            if (force) {
                // Remove all information about selected grammar items
                for (let i in sessionValue) {
                    if (i==='color-limit')
                        $('#color-limit').val(9999).trigger('change','manual');
                    else if (sessionValue[i]) { // sessionValue[i] is true if the box is checked
                                                // sessionvalue[i] is false or absent if the box is not checked
                        $('#' + i).prop('checked',false).trigger('change','manual');
                    }
                }
                sessionStorage.removeItem(configuration.propertiesName);
            }
            else {
                // Enforce selected grammar items
                $('#color-limit').val(9999);  // Default value
                for (let i in sessionValue) {
                    if (i==='color-limit')
                        $('#color-limit').val(sessionValue[i]);
                    else
                        $('#' + i).prop('checked',sessionValue[i]);
                }
            }
        }
        else {
            // Force removal of grammar items in Quiz, without saving to session
            if (force) {
                let IDs: any[] = []
                $('#grammarbuttongroup .selectbutton input:checked').each(function () { IDs.push($(this).attr('id')); });    
                for (let i in IDs) {
                    $('#' + IDs[i]).prop('checked',false);
                    $('#' + IDs[i]).trigger('change','manual');
                }
            }
        }
    }

    //****************************************************************************************************
    // setColorizeHandler static method
    //
    // Sets up eventhandlers for changes to the word frequency color limit
    //
    public static setColorizeHandler() {
        
        //****************************************************************************************************
        // colorizeFunction colors all words with a frequency_rank greater than the specified limit
        let colorizeFunction = function(event : JQuery.TriggeredEvent, ...isManual : any) {
            let collim : number = +$('#color-limit').val();
            
            $('.textdisplay').each(function() {
                $(this).toggleClass('colorized', +$(this).siblings('.frequency_rank').text() > collim);
            });

            if (isManual[0]!='manual')
                setOneSessionValue('color-limit', collim);
        };

        // Add handler to the change even (when the up/down button is used) and a delayed hadler for
        // the keyup even (when the user types a value)
        
        $('#color-limit').change(colorizeFunction);

        let timeoutId = 0;
        $('#color-limit').keyup(function() {
            clearTimeout(timeoutId); // doesn't matter if it's 0
            timeoutId = setTimeout(colorizeFunction,20); // Wait 20 ms for input field to be updated
        });
    }

    
    //****************************************************************************************************
    // buildGrammarAccordion static method
    //
    // Builds accordion for grammar selector.
    //
    // Returns:
    //     The width of the accordion
    //
    public static buildGrammarAccordion(): string {

        let tabs1: JQuery = $('#myview').tabs({
            heightStyle: 'content',
            collapsible: true,
        });

        let tabs2: JQuery = $('#gramtabs').tabs({
            heightStyle: 'content',
            collapsible: true,
        });

        let tabs3: JQuery = $('#grammargroup').tabs({
            heightStyle: 'content',
            collapsible: true,
        });
        
        let max_width = 'auto';

        tabs1.tabs('option', 'active', false);
        tabs2.tabs('option', 'active', false);
        tabs3.tabs('option', 'active', false);

        return max_width;
    }
}
