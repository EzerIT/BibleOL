// -*- js -*-
// Copyright © 2018 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk

// Code to handle the specification of display and request features when creating an exercise. This
// code is used by the 'Features' tab of the exercise editor.

//****************************************************************************************************
// QuizFeatures interface
//
// Specifies the way features should be presented to or requested from a user when running an exercise.
// This is a subset of interface ExtendedQuizFeatures in quizdata.ts
//
interface QuizFeatures {
    showFeatures     : string[]; // Features to show to the user
    requestFeatures  : {         // Features to request from the user
        name         : string;   // Name of feature
        usedropdown  : boolean;  // Is a drop down list used for this feature?
        hideFeatures : string[]; // List of feature values to hide from student
    } [];
    dontShowFeatures : string[]; // Features to hide from the user
    dontShowObjects  : {         // Object types to hide from the user
        content      : string;   // Name of object type
        show?        : string;   // Features to show even though object is hidden
    } [];
    glosslimit       : number;   // Frequency limit for showing glosses
}



//****************************************************************************************************
// ButtonSelection enumeration
//
// Names the radio buttons and checkboxes that speficy the handling of a feature
//
enum ButtonSelection { SHOW, REQUEST, REQUEST_DROPDOWN, DONT_CARE, DONT_SHOW };


//****************************************************************************************************
// ButtonsAndLabel class
//
// Handles the buttons and name of a single feature, corresponding to one line in the "Features" tab.
//
class ButtonsAndLabel {
    private showFeat	 : JQuery; // The "Show" radio button
    private reqFeat	 : JQuery; // The "Request" radio button
    public  dcFeat	 : JQuery; // The "Don't care" radio button
    public  dontShowFeat : JQuery; // The "Don't show" radio button
    private ddCheck	 : JQuery; // The "Multiple choice" checkbox
    private feat	 : JQuery; // The <span> element containing the feature name
    private limitter	 : JQuery; // The <span> element containing the hideFeatures selector

    private static buttonNumber : number = 0; // Used by button name generator
    
    //------------------------------------------------------------------------------------------
    // Constructor method
    //
    // Generates HTML code for a single line in the "Features" tab.
    //
    // The parameters and additional class fields are described below.
    //
    constructor(lab                       : string,          // The localized name of the feature
                private featName          : string,          // The Emdros name of the feature
                otype                     : string,          // The Emdros object type
                select                    : ButtonSelection, // Initially selected radio button
                private hideFeatures      : string[],        // List of feature values to hide from student
                private useDropDown       : boolean,         // Can multiple choice be used?
                private canShow           : boolean,         // Can this be a display feature?
                private canRequest        : boolean,         // Can this be a request feature?
                private canDisplayGrammar : boolean          // Can this be a "don't show" feature?
               ) {

        ++ButtonsAndLabel.buttonNumber;
        
        this.showFeat     = canShow           ? $(`<input type="radio" name="feat_${ButtonsAndLabel.buttonNumber}" value="show">`)	  : $('<span></span>');
	this.reqFeat	  = canRequest	      ? $(`<input type="radio" name="feat_${ButtonsAndLabel.buttonNumber}" value="request">`)	  : $('<span></span>');
	this.dcFeat	  =			$(`<input type="radio" name="feat_${ButtonsAndLabel.buttonNumber}" value="dontcare">`);
	this.dontShowFeat = canDisplayGrammar ? $(`<input type="radio" name="feat_${ButtonsAndLabel.buttonNumber}" value="dontshowfeat">`) : $('<span></span>');
	this.feat         =                     $(`<span>${lab}</span>`);
        this.limitter     =                     $('<span></span>');
        
	switch (select) {
        case ButtonSelection.SHOW:             this.showFeat.prop('checked',true);     break;
        case ButtonSelection.REQUEST:
        case ButtonSelection.REQUEST_DROPDOWN: this.reqFeat.prop('checked',true);      break;
        case ButtonSelection.DONT_CARE:        this.dcFeat.prop('checked',true);       break;
        case ButtonSelection.DONT_SHOW:        this.dontShowFeat.prop('checked',true); break;
	}

        if (useDropDown) {
            this.ddCheck = $(`<input type="checkbox" name="dd_${ButtonsAndLabel.buttonNumber}">`);
            this.ddCheck.prop('checked', select!=ButtonSelection.REQUEST);
        }
        else
            this.ddCheck = $('<span></span>'); // Empty space filler

        if (canRequest) {
            if (useDropDown) {
                // Enable or disable the "multiple choice" checkbox as required
                this.ddCheck.prop('disabled', !this.reqFeat.prop('checked'));
                if (canShow)
                    this.showFeat.click(() => this.ddCheck.prop('disabled', true));
                this.reqFeat.click(() => this.ddCheck.prop('disabled', false));
                this.dcFeat.click(() => this.ddCheck.prop('disabled', true));
                if (canDisplayGrammar)
                    this.dontShowFeat.click(() => this.ddCheck.prop('disabled', true));
            }
        }

        let valueType : string = typeinfo.obj2feat[otype][featName];

        if (typeinfo.enumTypes.indexOf(valueType)!=-1) {
            if (canRequest) {
                let limitButton : JQuery = $(`<a href="#" style="color:white" class="badge"></a>`);

                let updateBadge = () => {
                    if (!hideFeatures || hideFeatures.length==0) 
                        limitButton.removeClass('badge-danger').addClass('badge-success').html(localize('unlimited'));
                    else
                        limitButton.removeClass('badge-success').addClass('badge-danger').html(localize('limited'));
                }
                                                                                                 
                updateBadge();


                let limitButton_clickFunction = () => {
                    new LimitDialog(valueType,
                                    getFeatureSetting(otype,featName),
                                    hideFeatures,
                                    (newHideFeatures : string[]) => {
                                        this.hideFeatures = hideFeatures = newHideFeatures;
                                        updateBadge();
                                    });
                };

                let removeit = () => this.limitter.empty();
                
                this.reqFeat.change( () =>
                    {
                        limitButton.click(limitButton_clickFunction);
                        this.limitter.append(limitButton);
                    }
                );

                if (select===ButtonSelection.REQUEST)
                    this.reqFeat.change();  // Trigger the change event
                
                this.dcFeat.change(removeit);

                if (canShow)
                    this.showFeat.change(removeit);

                if (canDisplayGrammar)
                    this.dontShowFeat.change(removeit);
            }
        }
    }

    //------------------------------------------------------------------------------------------
    // getRow method
    //
    // Returns HTML for the table row managed by this class.
    //
    public getRow() : JQuery {
        let row  : JQuery = $('<tr></tr>');
        let cell : JQuery;

        cell = $('<td></td>')                  .append(this.showFeat);     row.append(cell);
        cell = $('<td></td>')                  .append(this.reqFeat);      row.append(cell);
        cell = $('<td></td>')                  .append(this.dcFeat);       row.append(cell);
        cell = $('<td></td>')                  .append(this.dontShowFeat); row.append(cell);
        cell = $('<td></td>')                  .append(this.ddCheck);      row.append(cell);
        cell = $('<td class="leftalign"></td>').append(this.feat);         row.append(cell);
        cell = $('<td></td>')                  .append(this.limitter);     row.append(cell);

        return row;
    }

    //------------------------------------------------------------------------------------------
    // isSelected method
    //
    // Checks if the specified radio button or checkbox is selected
    //
    // Parameter:
    //     button: The radio button or checkbox to check.
    // Returns:
    //     True if the radio button or checkbox is available and checked
    //
    public isSelected(button : ButtonSelection) {
        switch (button) {
        case ButtonSelection.SHOW:
            return this.canShow && this.showFeat.prop('checked');

        case ButtonSelection.REQUEST:
            return this.canRequest && this.reqFeat.prop('checked');

        case ButtonSelection.REQUEST_DROPDOWN:
            return this.useDropDown && this.ddCheck.prop('checked');

        case ButtonSelection.DONT_CARE:
            return this.dcFeat.prop('checked');

        case ButtonSelection.DONT_SHOW:
            return this.canDisplayGrammar && this.dontShowFeat.prop('checked');
        }
    }

    //------------------------------------------------------------------------------------------
    // getHideFeatures method
    //
    // Returns the current feature limitations
    //
    public getHideFeatures() : string[] {
        return this.hideFeatures;
    }
    
    //------------------------------------------------------------------------------------------
    // getFeatName method
    //
    // Returns the name of the Emdros feature.
    //
    public getFeatName() : string {
        return this.featName;
    }
}

//****************************************************************************************************
// LimitDialog class
//
// This class manages the feature limitation dialog
//
class LimitDialog {

    //------------------------------------------------------------------------------------------
    // Constructor method
    //
    // Generates HTML code for a feature limitation dialog.
    // Note: In the dialog features to hide are unchecked, and features to show are checked.
    //
    // The parameters and class fields are described below.
    //
    constructor(valueType : string,                                     // The type of the feature
                featset : FeatureSetting,                               // Feature settings
                hideFeatures : string[],                                // Array of feature values to hide
                private callback : (newHideFeatures : string[]) => void // Function to call when the user clicks "Save"
               ) {

        let butSetAll   : JQuery = $(`<a class="badge badge-success" style="margin:0 5px 5px 0" href="#">${localize('set_all')}</a>`);
	let butClearAll : JQuery = $(`<a class="badge badge-success" style="margin:0 5px 5px 0" href="#">${localize('clear_all')}</a>`);

        butSetAll.click(() => $('input[type=checkbox][name=hideFeatures]').prop('checked',true));
        butClearAll.click(() => $('input[type=checkbox][name=hideFeatures]').prop('checked',false));

        
        let setclear : JQuery = $('<div></div>');
        setclear.append(butSetAll).append(butClearAll);

        // Hard coded functionality
        if (configuration.databaseName=='ETCBC4' && valueType=='verbal_stem_t') {
            let butHebrew  : JQuery = $(`<a class="badge badge-success" style="margin:0 5px 5px 0" href="#">${localize('set_hebrew')}</a>`);
	    let butAramaic : JQuery = $(`<a class="badge badge-success" style="margin:0 5px 5px 0" href="#">${localize('set_aramaic')}</a>`);

            let hebrewStems : string[] = ['NA','etpa','hif','hit','hof','hotp','hsht','htpo','nif','nit',
                                          'pasq','piel','poal','poel','pual','qal','tif'];
            let aramaicStems : string[] = ['NA','afel','etpa','etpe','haf ','hof ','hsht','htpa','htpe',
                                           'pael','peal','peil','shaf'];

            butHebrew.click(() => {
                $('input[type=checkbox][name=hideFeatures]').prop('checked',false);
                for (let i=0; i<hebrewStems.length; ++i)
                    $('input[type=checkbox][name=hideFeatures][value=' + hebrewStems[i] + ']').prop('checked',true);
            });

            butAramaic.click(() => {
                $('input[type=checkbox][name=hideFeatures]').prop('checked',false);
                for (let i=0; i<aramaicStems.length; ++i)
                    $('input[type=checkbox][name=hideFeatures][value=' + aramaicStems[i] + ']').prop('checked',true);
            });
            
            setclear = setclear.add($('<div></div>').append(butHebrew).append(butAramaic));
        }

        
 	let enumValues : string[] = typeinfo.enum2values[valueType];
	let checkBoxes : SortingCheckBox[] = [];

        
        for (let i = 0; i<enumValues.length; ++i) {
	    let s : string = enumValues[i];
 
	    let hv : string[] = featset.hideValues;
	    let ov : string[] = featset.otherValues;
	    if ((hv && hv.indexOf(s)!==-1) || ((ov && ov.indexOf(s)!==-1)))
		continue;
 
	    let scb = new SortingCheckBox('hideFeatures', s, getFeatureValueFriendlyName(valueType, s, false, false));
	    scb.setSelected(!hideFeatures || hideFeatures.indexOf(s)===-1);
	    checkBoxes.push(scb);
	}
 
	checkBoxes.sort((a : SortingCheckBox, b : SortingCheckBox) => StringWithSort.compare(a.getSws(),b.getSws()));
 
	// Decide how many columns and rows to use for feature values
	let columns : number =
	    checkBoxes.length>12 ? 3 :
	    checkBoxes.length>4 ? 2 : 1;
 
	let rows : number = Math.ceil(checkBoxes.length / columns);
 
	let table : JQuery = $('<table></table>');
 
	// Create a table of size columns×rows
	for (let r=0; r<rows; ++r) {
	    let row : JQuery = $('<tr></tr>');
	    for (let c=0; c<columns; ++c) {
		let cell : JQuery = $('<td></td>');
		if (c*rows + r < checkBoxes.length)
		    cell.append(checkBoxes[c*rows + r].getJQuery());
		row.append(cell);
	    }
	    table.append(row);
	}

        $('#feature-limit-body').empty().append(setclear).append(table);
        $('#feature-limit-dialog-save').off('click').on('click', () => this.saveButtonAction() );
        $('#feature-limit-dialog').modal('show');
    }


    
    //------------------------------------------------------------------------------------------
    // saveButtonAction method
    //
    // This function is called when the user clicks the "Save" button. It calls the callback
    // function with information about which feaatures are NOT checked.
    //
    // Note: This creates a new hideFeatures array so it will not affect the data stored in initialQf
    //
    private saveButtonAction() {
        let hideFeatures : string[] = [];

        $('input[type=checkbox][name=hideFeatures]:not(:checked)').each(
            function() {
                hideFeatures.push(<string>$(this).val());
            }
        );

        $('#feature-limit-dialog-save').off('click');

        this.callback(hideFeatures);
        
        $('#feature-limit-dialog').modal('hide');
    }
}



//****************************************************************************************************
// PanelForOneOtype class
//
// This class contains the exercise feature settings for a single Emdros object type.
//
class PanelForOneOtype  {
    public visualBAL : ButtonsAndLabel;                    // The setting for the "visual" pseudo feature
    public allBAL    : ButtonsAndLabel[] = [];             // The settings for all other features for the question object
    public allObjBAL : {[ key : string ] : ButtonsAndLabel[]} = {}; // The settings for all features for other objects

    private static accordionNumber : number = 0; // Used by accordion ID generator
    private static collapseNumber  : number = 0; // Used by accordion body name generator
    private panel                  : JQuery;     // The entire feature panel for one object type

    //------------------------------------------------------------------------------------------
    // Constructor method
    //
    // Creates a feature setting panel for a single Emdros object type.
    //
    // Parameters:
    //     otype: The Emdros object type.
    //     ptqf: The PanelTemplQuizFeatures object of which panel is a member.
    //
    constructor(otype : string, ptqf : PanelTemplQuizFeatures) {
        //////////////////////////////////////////////////
        // Create buttons for the specified object type //
        //////////////////////////////////////////////////

        let useSavedFeatures : boolean = otype === ptqf.initialOtype; // Use features read from exercise file?


        ++PanelForOneOtype.accordionNumber;
        this.panel = $(`<div class="accordion" id="accordion${PanelForOneOtype.accordionNumber}"></div>`);
        
        let table : JQuery = $('<table class="striped featuretable"></table>'); // Feature selections for one object type
        
        // Add headings to the table
        table.append('<tr>'
                     + `<th>${localize('show')}</th>`
                     + `<th>${localize('request')}</th>`
                     + `<th>${localize('dont_care')}</th>`
                     + `<th>${localize('dont_show')}</th>`
                     + `<th>${localize('multiple_choice')}</th>`
                     + `<th class="leftalign">${localize('feature')}</th>`
                     + '<th></th>'
                     + '</tr>');

        console.log(otype)
        console.log(configuration.surfaceFeature);
        console.log(getFeatureSetting(otype,configuration.surfaceFeature));
        // Set up "visual" pseudo feature
        this.visualBAL = new ButtonsAndLabel(localize('visual'),                  // The localized name of the feature
                                             'visual',                            // The Emdros name of the feature
                                             otype,                               // The Emdros object type
                                             useSavedFeatures ? ptqf.getSelector('visual') : ButtonSelection.DONT_CARE, // Initially selected radio button
                                             null,                                // hideFeatures
                                             configuration.objHasSurface===otype && Boolean(getFeatureSetting(otype,configuration.surfaceFeature).alternateshowrequestSql), // Can multiple choice be used?
                                             true,                                // Can this be a display feature?
                                             configuration.objHasSurface===otype, // Can this be a request feature?
                                             configuration.objHasSurface===otype);// Can this be a "don't show" feature?

        table.append(this.visualBAL.getRow());

        // Set up genuine features
        let hasSurfaceFeature : boolean         = otype===configuration.objHasSurface;
        let sg                : SentenceGrammar = getSentenceGrammarFor(otype);
        let keylist           : string[]        = []; // Will hold list of relevant feature names
        // Note:
        // getFeatureSetting(otype, featName).ignoreShow means featName cannot be a display feature.
        // getFeatureSetting(otype, featName).ignoreRequest means featName cannot be a request feature.
        // sg===null || !sg.containsFeature(featName) means featName cannot be a "don't show" feature (because it is never shown).
        
        // Go through all features and identify the ones to include in the panel
        for (let featName in getObjectSetting(otype).featuresetting) {
            // Ignore features marked to be ignored, unless they belong to a SentenceGrammar
            if (getFeatureSetting(otype, featName).ignoreShow
                && getFeatureSetting(otype, featName).ignoreRequest
                && (sg===null || !sg.containsFeature(featName)))
                continue;

            // Ignore features of type 'url'
            if (typeinfo.obj2feat[otype][featName]==='url')
                continue;

            // Ignore the genuine feature already presented as "visual"
            if (hasSurfaceFeature && featName===configuration.surfaceFeature)
                continue;
            keylist.push(featName);
        }

        // Loop through the relevant features and create radio buttons for each
        for (let ix=0; ix<keylist.length; ++ix) {
            let featName : string = keylist[ix]; // Feture name
            let bal = new ButtonsAndLabel(getFeatureFriendlyName(otype, featName),                     // The localized name of the feature  
                                          featName,                                                    // The Emdros name of the feature     
                                          otype,                                                       // The Emdros object type             
                                          useSavedFeatures ? ptqf.getSelector(featName) : ButtonSelection.DONT_CARE, // Initially selected radio button
                                          ptqf.getHideFeatures(featName),                              // List of feature values to hide from student
                                          Boolean(getFeatureSetting(otype,featName).alternateshowrequestSql), // Can multiple choice be used?       
                                          !getFeatureSetting(otype, featName).ignoreShow,              // Can this be a display feature?     
                                          !getFeatureSetting(otype, featName).ignoreRequest,           // Can this be a request feature?     
                                          sg!==null && sg.containsFeature(featName));                  // Can this be a "don't show" feature?
            this.allBAL.push(bal);
            table.append(bal.getRow());
        }

        

        // Manually add Linkage Button and Label
        let real_name = 'code_TYPE_text';
        let friendly_name = 'Linkage';
        let bal_row = this.addManualButton(real_name, friendly_name, otype, useSavedFeatures, ptqf.getSelector(real_name), ptqf.getHideFeatures(real_name));
        table.append(bal_row);

        // Manually add Syntactic Code Button and Label
        real_name = 'code';
        friendly_name = 'Syntactic Code';
        bal_row = this.addManualButton(real_name, friendly_name, otype, useSavedFeatures, ptqf.getSelector(real_name), ptqf.getHideFeatures(real_name));
        table.append(bal_row);
        

        

        this.panel.append(this.wrapInCard(getObjectFriendlyName(otype), table, true, `accordion${PanelForOneOtype.accordionNumber}`));

        
        ////////////////////////////////////////////////////////////////////////////////////////
        // Create buttons for the additional object types to be included in the current panel //
        ////////////////////////////////////////////////////////////////////////////////////////


        // Generate buttons for other types:
        for (let level in configuration.sentencegrammar) {
            let leveli : number = +level;
            if (isNaN(leveli)) continue; // Not numeric

            let otherOtype : string = configuration.sentencegrammar[leveli].objType;
            
            if (otherOtype!==otype && configuration.objectSettings[otherOtype].mayselect) {
                table = $('<table class="striped featuretable"></table>'); // Feature selections for one object type

                // Add headings to the table
                table.append('<tr>'
                             + '<td colspan="2"></td>'
                             + `<th>${localize('dont_care')}</th>`
                             + `<th>${localize('dont_show')}</th>`
                             + '<td colspan="3"></td>'
                             + '</tr>');

            
                this.allObjBAL[otherOtype] = []; // The settings for all features for otherOtype

                // Build 'Set all' button line
                let buttonrow : JQuery = $('<tr><td colspan="2"></td></tr>');
                let td_dcb : JQuery = $('<td></td>');
                let td_dsb : JQuery = $('<td></td>');

                let setAllDontCareButton : JQuery = $(`<a href="#" style="color:white" class="badge badge-primary">${localize('set_all')}</a>`);
                let setAllDontShowButton : JQuery = $(`<a href="#" style="color:white" class="badge badge-primary">${localize('set_all')}</a>`);
                td_dcb.append(setAllDontCareButton);
                td_dsb.append(setAllDontShowButton);

                buttonrow.append(td_dcb).append(td_dsb).append('<td colspan="3"></td>');
                table.append(buttonrow);
                
                let other_sg : SentenceGrammar = getSentenceGrammarFor(otherOtype);

                if (other_sg===null) // Object has not features to display
                    continue;

                let hasSeenGlosses : boolean = false;
                other_sg.walkFeatureNames(otherOtype,
                                          (whattype    : WHAT,
                                           objType     : string,
                                           origObjType : string,
                                           featName    : string,
                                           featNameLoc : string,
                                           sgiObj      : SentenceGrammarItem
                                          ) : void  => {
                                              if (whattype!==WHAT.feature && whattype!==WHAT.metafeature)
                                                  return;
                                              
                                              if (whattype===WHAT.feature && getFeatureSetting(objType,featName).isGloss!==undefined) {
                                                  if (hasSeenGlosses)
                                                      return;
                                                  hasSeenGlosses = true;
                                                  featName = 'glosses';
                                                  featNameLoc = localize(featName);
                                              }
                                              
                                              // Go through all features and identify the ones to include in the panel
                                              // Ignore features of type 'url'
                                              if (typeinfo.obj2feat[objType][featName]==='url')
                                                  return;

                                              let bal = new ButtonsAndLabel(featNameLoc,                              // The localized name of the feature  
                                                                            featName,                                                                  // The Emdros name of the feature     
                                                                            objType,                                                                // The Emdros object type             
                                                                            useSavedFeatures ? ptqf.getObjectSelector(origObjType,featName) : ButtonSelection.DONT_CARE, // Initially selected radio button
                                                                            null,                                                                      // List of feature values to hide from student
                                                                            false,                                                                     // Can multiple choice be used?       
                                                                            false,                                                                     // Can this be a display feature?     
                                                                            false,                                                                     // Can this be a request feature?     
                                                                            true);                                                                     // Can this be a "don't show" feature?
                                              this.allObjBAL[otherOtype].push(bal);
                                              table.append(bal.getRow());
                                          }
                                         );
                setAllDontCareButton.click( () =>
                    {
                        for (let bal of this.allObjBAL[otherOtype])
                            bal.dcFeat.click();
                        return false;
                    });
                setAllDontShowButton.click( () =>
                    {
                        for (let bal of this.allObjBAL[otherOtype])
                            bal.dontShowFeat.click();
                        return false;
                    });

                this.panel.append(this.wrapInCard(getObjectFriendlyName(otherOtype), table, false, `accordion${PanelForOneOtype.accordionNumber}`));
            }
        }

        /////////////////////////////////
        // Create gloss limiter dialog //
        /////////////////////////////////

        this.panel.append(this.wrapInCard(localize('gloss_limit'),
                                          $(`<div><span class="gloss-limit-prompt">${localize('gloss_limit_prompt')} </span><input id="gloss-limit-${otype}" type="number" value="${ptqf.getGlossLimit()}" style="width:5em"></span>`),
                                          false,
                                          `accordion${PanelForOneOtype.accordionNumber}`));
    }
    
    private addManualButton(real_name:string, friendly_name:string, otype:string, useSavedFeatures:boolean, get_selector:number, get_hide_features:string[]): JQuery {
            
        let useDropDown = false;        // Can multiple choice be used?
        let canShow = false;            // Can this be a display feature?
        let canRequest = false;         // Can this be a request feature?
        let canDisplayGrammar = true;   // Can this be a "don't show" feature?

        let new_bal = new ButtonsAndLabel(friendly_name,
            real_name,
            otype,
            useSavedFeatures ? get_selector : ButtonSelection.DONT_CARE,
            get_hide_features,
            useDropDown, 
            canShow, 
            canRequest, 
            canDisplayGrammar);

        // Add the new button and label to allBAL
        this.allBAL.push(new_bal);
        return new_bal.getRow();
    }
    
    
    


    //------------------------------------------------------------------------------------------
    // wrapInCard method
    //
    // Embeds a heading and a feature table in a Bootstrap <div class="card"> element for use
    // by the accordion in PanelForOneOtype.constructor().
    //
    // Parameters:
    //    heading: The string to use as heading for the card.
    //    contents: The contents of the card.
    //    open: True if this card of the accordion should be shown initially
    //    accordionId: The ID of the accordion containing this card.
    //
    private wrapInCard(heading : string, contents : JQuery, open : boolean, accordionId : string) : JQuery {
        ++PanelForOneOtype.collapseNumber;

        let cardBody : JQuery = $('<div class="card-body"></div>')
        let cardBodyWrapper : JQuery = $(`<div id="accbody${PanelForOneOtype.collapseNumber}" class="collapse ${open ? "show" : ""}" data-parent="#${accordionId}"></div>`);

        cardBody.append(contents);
        cardBodyWrapper.append(cardBody);

            
        let cardHeader : JQuery = $('<div class="card-header">' +
                                    `<button class="btn text-left" type="button" aria-expanded="${open}" data-toggle="collapse" data-target="#accbody${PanelForOneOtype.collapseNumber}">` +
                                    heading +
                                    '</button>' +
                                    '</div>');

        let card : JQuery = $('<div class="card"></div>');

        card.append(cardHeader).append(cardBodyWrapper);

        return card;
    }

    //------------------------------------------------------------------------------------------
    // hide method
    //
    // Hides the panel for the current object type.
    //
    public hide() : void {
        this.panel.hide();
    }

    //------------------------------------------------------------------------------------------
    // show method
    //
    // Shows the panel for the current object type.
    //
    public show() : void {
        this.panel.show();
    }

    //------------------------------------------------------------------------------------------
    // getPanel method
    //
    // Returns the HTML for the current object type.
    //
    public getPanel() : JQuery {
        return this.panel;
    }
}


//****************************************************************************************************
// PanelTemplQuizFeatures class
//
// This class holds the contents of the 'Features' tab. Note that this can include feature selection
// panels for several object types, if the user switches between object types while creating an
// exercise.
//
class PanelTemplQuizFeatures {
    private oldOtype     : string;                                        // The previously displayed object type
    private panels       : { [ keyk : string ] : PanelForOneOtype } = {}; // Maps object type => associated panel
    private visiblePanel : PanelForOneOtype;                              // The currently visible panel
    private fpan         : JQuery = $('<div id="fpan"></div>');           // The HTML is built here

    //------------------------------------------------------------------------------------------
    // Constructor method
    //
    // Creates the panel for specifying display and request features.
    //
    // The parameters and additional class fields are described below.
    //
    constructor(public initialOtype : string,       // Emdros object from exercise file
                private initialQf   : QuizFeatures, // Feature specification from exercise file
                where               : JQuery        // The <div> where this class should store the generated HTML
               ) {
        where.append(this.fpan);
    }

    //------------------------------------------------------------------------------------------
    // populate method
    //
    // Generate a panel for a specific Emdros object type.
    //
    // Parameter:
    //     otype: The Emdros object type.
    //
    public populate(otype : string) : void {
        if (otype === this.oldOtype) // This is the object type from the exercise file
            return;

        this.oldOtype = otype;
        if (this.visiblePanel)
            this.visiblePanel.hide();

	this.visiblePanel = this.panels[otype];

	if (!this.visiblePanel) {
	    this.visiblePanel = new PanelForOneOtype(otype, this);
	    this.panels[otype] = this.visiblePanel;
            this.fpan.append(this.visiblePanel.getPanel());
	}

	this.visiblePanel.show();
    }

    //------------------------------------------------------------------------------------------
    // getSelector method
    //
    // Returns the initial radio button setting for a specific feature.
    //
    // Parameter:
    //     feat: The name of the feature.
    // Returns:
    //     The radiobutton setting for the specifed feature.
    //
    public getSelector(feat : string) : ButtonSelection {
	if (!this.initialQf)
	    return ButtonSelection.DONT_CARE;

        for (let i=0; i<this.initialQf.showFeatures.length; ++i)
	    if (this.initialQf.showFeatures[i]===feat)
		return ButtonSelection.SHOW;

        for (let i=0; i<this.initialQf.requestFeatures.length; ++i)
	    if (this.initialQf.requestFeatures[i].name===feat)
		return this.initialQf.requestFeatures[i].usedropdown ? ButtonSelection.REQUEST_DROPDOWN : ButtonSelection.REQUEST;

	if (this.initialQf.dontShowFeatures)
            for (let i=0; i<this.initialQf.dontShowFeatures.length; ++i)
		if (this.initialQf.dontShowFeatures[i]===feat)
		    return ButtonSelection.DONT_SHOW;

	return ButtonSelection.DONT_CARE;
    }

    //------------------------------------------------------------------------------------------
    // getHideFeatures method
    //
    // Returns the initial value of feature limitations for a given feature.
    //
    // Parameter:
    //     feat: The feature whose limitations we seek.
    // Return:
    //     An array of feature values to hide (or null for none)
    //
    public getHideFeatures(feat : string) : string[] {
	if (!this.initialQf)
	    return null;

        for (let i=0; i<this.initialQf.requestFeatures.length; ++i)
	    if (this.initialQf.requestFeatures[i].name===feat)
                return this.initialQf.requestFeatures[i].hideFeatures;
    }        
    

    //------------------------------------------------------------------------------------------
    // getObjectSelector method
    //
    // Returns the initial radio button setting for a specific additional object.
    //
    // Parameter:
    //     otype: The name of the additional object.
    // Returns:
    //     The radiobutton setting for the specifed feature.
    //
    public getObjectSelector(otype : string, featName : string) : ButtonSelection {
        let regex_feat = new RegExp(`\\b${featName}\\b`);

	if (this.initialQf && this.initialQf.dontShowObjects) {
            for (let i=0; i<this.initialQf.dontShowObjects.length; ++i) {
		if (this.initialQf.dontShowObjects[i].content===otype) {
                    if (this.initialQf.dontShowObjects[i].show!==undefined && this.initialQf.dontShowObjects[i].show.match(regex_feat))
		        return ButtonSelection.DONT_CARE;
                    else
		        return ButtonSelection.DONT_SHOW;
                }
            }
        }

	return ButtonSelection.DONT_CARE;
    }

    //------------------------------------------------------------------------------------------
    // noRequestFeatures method
    //
    // Returns true if no request features have been specified.
    //
    public noRequestFeatures() : boolean {
	if (!this.visiblePanel)
	    return true;

	if (this.visiblePanel.visualBAL.isSelected(ButtonSelection.REQUEST))
            return false;

        for (let i=0; i<this.visiblePanel.allBAL.length; ++i)
	    if (this.visiblePanel.allBAL[i].isSelected(ButtonSelection.REQUEST))
                return false;

	return true;
    }

    //------------------------------------------------------------------------------------------
    // noRequestFeatures method
    //
    // Returns true if no display features have been specified.
    //
    public noShowFeatures() : boolean {
	if (!this.visiblePanel)
	    return true;

	if (this.visiblePanel.visualBAL.isSelected(ButtonSelection.SHOW))
            return false;

        for (let i=0; i<this.visiblePanel.allBAL.length; ++i)
	    if (this.visiblePanel.allBAL[i].isSelected(ButtonSelection.SHOW))
                return false;

	return true;
    }


    //------------------------------------------------------------------------------------------
    // getGlossLimit method
    //
    // Returns the initial glosslimit
    //
    public getGlossLimit() : number {
        return this.initialQf.glosslimit;
    }

    //------------------------------------------------------------------------------------------
    // getInfo method
    //
    // Returns the feature specification as a QuizFeatures object.
    //
    public getInfo() : QuizFeatures {
        let qf : QuizFeatures = {
            showFeatures     : [],
            requestFeatures  : [],
            dontShowFeatures : [],
            dontShowObjects  : [],
            glosslimit       : 0
        };


	if (!this.visiblePanel)
	    return null;

        // Store information about the "visual" pseudo feature
	if (this.visiblePanel.visualBAL.isSelected(ButtonSelection.SHOW))
	    qf.showFeatures.push('visual');
	else if (this.visiblePanel.visualBAL.isSelected(ButtonSelection.REQUEST))
	    qf.requestFeatures.push({name : 'visual', usedropdown : this.visiblePanel.visualBAL.isSelected(ButtonSelection.REQUEST_DROPDOWN), hideFeatures : null});
        else if (this.visiblePanel.visualBAL.isSelected(ButtonSelection.DONT_SHOW))
	    qf.dontShowFeatures.push('visual');

        // Store informaiton about other features for the question object
        for (let i=0; i<this.visiblePanel.allBAL.length; ++i) {
            let bal : ButtonsAndLabel = this.visiblePanel.allBAL[i];

	    if (bal.isSelected(ButtonSelection.SHOW))
		qf.showFeatures.push(bal.getFeatName());
	    else if (bal.isSelected(ButtonSelection.REQUEST))
	        qf.requestFeatures.push({name : bal.getFeatName(), usedropdown : bal.isSelected(ButtonSelection.REQUEST_DROPDOWN), hideFeatures : bal.getHideFeatures()});
	    else if (bal.isSelected(ButtonSelection.DONT_SHOW))
		qf.dontShowFeatures.push(bal.getFeatName());
        }

        // Store informaiton about other features for other objects
        // Method:
        //     If all features for an object are DONT_CARE, skip the object.
        //     If at least one feature is DONT_SHOW, push the object along with information about the DONT_CARE features
        for (let otherOtype in this.visiblePanel.allObjBAL) {
            let allDONT_CARE : boolean = true;
            for (let bal of this.visiblePanel.allObjBAL[otherOtype]) {
                if (bal.isSelected(ButtonSelection.DONT_SHOW)) {
                    allDONT_CARE = false;
                    break;
                }
            }
            if (!allDONT_CARE) {
                let showstring : string = '';
                for (let bal of this.visiblePanel.allObjBAL[otherOtype]) {
                    if (bal.isSelected(ButtonSelection.DONT_CARE))
                        showstring += bal.getFeatName() + ' ';
                }
                if (showstring==='') 
                    qf.dontShowObjects.push({content: otherOtype});
                else
                    qf.dontShowObjects.push({content: otherOtype, show : showstring.trim()});
            }
        }

        qf.glosslimit = +$(`#gloss-limit-${this.oldOtype}`).val();
	return qf;
    }

    //------------------------------------------------------------------------------------------
    // isDirty method
    //
    // Returns true if the user has changed the data in the exercise template.
    //
    public isDirty() : boolean {
        let qfnow : QuizFeatures = this.getInfo();
        console.log(qfnow.showFeatures);
        console.log(this.initialQf.showFeatures);
        if (qfnow.showFeatures.length !== this.initialQf.showFeatures.length ||
            qfnow.requestFeatures.length !== this.initialQf.requestFeatures.length ||
            qfnow.dontShowFeatures.length !== this.initialQf.dontShowFeatures.length ||
            qfnow.dontShowObjects.length !== this.initialQf.dontShowObjects.length ||
            qfnow.glosslimit != this.initialQf.glosslimit) {
            return true;
        }

        for (let i=0; i<qfnow.showFeatures.length; ++i)
            if (qfnow.showFeatures[i] !== this.initialQf.showFeatures[i]) {
                return true;
            }
        
        for (let i=0; i<qfnow.requestFeatures.length; ++i) {
            if (qfnow.requestFeatures[i].name !== this.initialQf.requestFeatures[i].name ||
                qfnow.requestFeatures[i].usedropdown !== this.initialQf.requestFeatures[i].usedropdown) {
                return true;
            }

            if (qfnow.requestFeatures[i].hideFeatures !== this.initialQf.requestFeatures[i].hideFeatures) {
                if (qfnow.requestFeatures[i].hideFeatures === null || this.initialQf.requestFeatures[i].hideFeatures === null
                    || qfnow.requestFeatures[i].hideFeatures.length !== this.initialQf.requestFeatures[i].hideFeatures.length)
                    return true;

                for (let j=0; j<qfnow.requestFeatures[i].hideFeatures.length; ++j)
                    if (qfnow.requestFeatures[i].hideFeatures[j] !== this.initialQf.requestFeatures[i].hideFeatures[j])
                        return true;
            }
        }

        for (let i=0; i<qfnow.dontShowFeatures.length; ++i)
            if (qfnow.dontShowFeatures[i] !== this.initialQf.dontShowFeatures[i]) {
                return true;
            }

        for (let i=0; i<qfnow.dontShowObjects.length; ++i)
            if (qfnow.dontShowObjects[i].content !== this.initialQf.dontShowObjects[i].content ||
                qfnow.dontShowObjects[i].show !== this.initialQf.dontShowObjects[i].show) {
                return true;
            }

        return false;
    }
}
