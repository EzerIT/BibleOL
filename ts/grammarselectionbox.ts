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
                    this.subgroupgrammardivs += '</div></div>';
                    break;
                                          
                case WHAT.feature:
                case WHAT.metafeature:
                    let disabled: string = mayShowFeature(objType, origObjType, featName, sgiObj) ? '' : 'disabled';
                                          
                    if (this.hasSeenGrammarGroup) {
                        this.subgroupgrammardivs += `<div class="selectbutton"><input id="${objType}_${featName}_cb" type="checkbox" ${disabled}><label for="${objType}_${featName}_cb">${featNameLoc}</label></div>`;
                    } else {
                        this.checkboxes += `<div class="selectbutton"><input id="${objType}_${featName}_cb" type="checkbox" ${disabled}><label for="${objType}_${featName}_cb">${featNameLoc}</label></div>`;
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
        // this.checkboxes += `<p><button class="btn btn-clear" id="cleargrammar">${localize('clear_grammar')}</button></p>`;
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

                                       $(`#${objType}_${featName}_cb`).on('change', (e : JQueryEventObject) => {
                                           if ($(e.currentTarget).prop('checked')) {
                                               if (!inQuiz) {
                                                   // Save setting in browser
                                                   sessionStorage.setItem($(e.currentTarget).prop('id'), configuration.propertiesName);
                                               }
                                               $(`.wordgrammar.${featName}`).removeClass('dontshowit').addClass('showit');
                                               this.wordSpaceBox.implicit(true);
                                           }
                                           else {
                                               if (!inQuiz) {
                                                   // Remove setting from browser
                                                   sessionStorage.removeItem($(e.currentTarget).prop('id'));
                                               }
                                               $(`.wordgrammar.${featName}`).removeClass('showit').addClass('dontshowit');
                                               this.wordSpaceBox.implicit(false);
                                           }
                                           
                                           for (let lev=1; lev<configuration.maxLevels-1; ++lev)
                                               GrammarSelectionBox.adjustDivLevWidth(lev);
                                       });
                                   }
                                   else { // Handling of clause, phrase, etc.
                                       
                                       $(`#${objType}_${featName}_cb`).on('change', (e : JQueryEventObject) => {
                                           if ($(e.currentTarget).prop('checked')) {
                                               if (!inQuiz) {
                                                   // Save setting in browser
                                                   sessionStorage.setItem($(e.currentTarget).prop('id'), configuration.propertiesName);
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
                                               if (!inQuiz) {
                                                   // Remove setting from browser
                                                   sessionStorage.removeItem($(e.currentTarget).prop('id'));
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
                $('#ws_cb').on('change',(e : JQueryEventObject) => {
                    if ($(e.currentTarget).prop('checked')) {
                        if (!inQuiz) {
                            // Save setting in browser
                            sessionStorage.setItem($(e.currentTarget).prop('id'), configuration.propertiesName);
                        }
                        this.wordSpaceBox.explicit(true);
                    }
                    else {
                        if (!inQuiz) {
                            // Remove setting from browser
                            sessionStorage.removeItem($(e.currentTarget).prop('id'));
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

                $(`#lev${leveli}_seplin_cb`).on('change', leveli, (e : JQueryEventObject) => {
                    if ($(e.currentTarget).prop('checked')) {
                        if (!inQuiz) {
                            // Save setting in browser
                            sessionStorage.setItem($(e.currentTarget).prop('id'), configuration.propertiesName);
                        }
                        this.separateLinesBoxes[e.data].explicit(true);
                    }
                    else {
                        if (!inQuiz) {
                            // Remove setting from browser
                            sessionStorage.removeItem($(e.currentTarget).prop('id'));
                        }
                        this.separateLinesBoxes[e.data].explicit(false);
                    }
                });

                this.borderBoxes[leveli] = new util.BorderFollowerBox(leveli);
                
                $(`#lev${leveli}_sb_cb`).on('change', leveli, (e : JQueryEventObject) => {
                    if ($(e.currentTarget).prop('checked')) {
                        if (!inQuiz) {
                            // Save setting in browser
                            sessionStorage.setItem($(e.currentTarget).prop('id'), configuration.propertiesName);
                        }
                        this.borderBoxes[e.data].explicit(true);
                    }
                    else {
                        if (!inQuiz) {
                            // Remove setting from browser
                            sessionStorage.removeItem($(e.currentTarget).prop('id'));
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
            if (force) {
                // Remove all information about selected grammar items
                for (let i in sessionStorage) {
                    if (sessionStorage[i]==configuration.propertiesName) {
                        sessionStorage.removeItem(i);
                        $('#' + i).prop('checked',false);
                        $('#' + i).trigger('change');
                    }
                }
            }
            else {
                // Enforce selected grammar items
                for (let i in sessionStorage) {
                    if (sessionStorage[i]==configuration.propertiesName)
                        $('#' + i).prop('checked',true);
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
                        $('#' + IDs[i]).trigger('change');
                }
            }
        }
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
