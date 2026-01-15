// -*- js -*-
// Copyright © 2018 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk

// Code to handle feature selection for sentences when creating an exercise


//****************************************************************************************************
// PanelTemplSentenceSelector class
//
// Handles the sentence selection tab. It contains code for generating feature selectors for
// sentence selection.
//
// This class is a subclass of PanelTemplMql which handles much of the HTML generation.
//
class PanelTemplSentenceSelector extends PanelTemplMql {
    private cbUseForQo      : JQuery = $('<input type="checkbox" name="useforqol">');                     // 'Use for sentence unit selection' checkbox
    private cbUseForQoLabel : JQuery = $(`<span>${localize('use_for_qosel')}</span>`);                    // Label for cbUseForQo
    private questObjTypeLab : JQuery = $(`<span>${localize('sentence_unit_type_prompt')}</span>`);        // 'Sentence unit type' label
    private featSelLab	    : JQuery = $(`<span>${localize('feature_prompt')}</span>`);                   // 'Feature:' label
    private importShebanq   : JQuery = $(`<button type="button">${localize('import_shebanq')}</button>`); // Import from SHEBANQ label
    private dirty           : boolean;                                                                    // Has the user modifie this panel?
    
    //------------------------------------------------------------------------------------------
    // Constructor method
    //
    // Creates the panel for sentence selection.
    //
    // The parameters and additional class fields are described below.
    //
    constructor(initialMd          : MqlData,			   // The initial contents of the panel
		private templTabs  : JQuery,			   // The <div> containing the five tabs
		where		   : JQuery,			   // The <div> where this class should store the generated HTML
		private qoselTab   : PanelTemplQuizObjectSelector, // The tab for sentence unit selection
		private featureTab : PanelTemplQuizFeatures	   // The tab for feature selection
               ) {
	super(initialMd, 'sensel');
	this.dirty = false;

        // Set handler for changes to the 'use for sentence unit selection' checkbox
	this.cbUseForQo.click(() => {
            if (this.cbUseForQo.is(':checked'))
                this.templTabs.tabs('disable',3);
            else
                this.templTabs.tabs('enable',3);

            this.populateFeatureTab(null);
            this.dirty = true;
        });

        // Set localized text for MQL/friendly selector buttons
        this.rbMqlLabel = $(`<span>${localize('mql_qosel_prompt')}</span>`);
        this.rbFriendlyLabel = $(`<span>${localize('friendly_featsel_prompt')}</span>`);

        this.doLayout(where); // Do the actual layout of the panel

        // Handle initial setting of 'use for sentence unit selection'
	if (this.initialMd==null || this.initialMd.useForQo) {
            this.cbUseForQo.prop('checked',true);
            this.templTabs.tabs('disable',3);
        }
        else {
            this.cbUseForQo.prop('checked',false);
            this.templTabs.tabs('enable',3);
        }

        // Set handler for for 'Import from SHEBANQ' button
	this.importShebanq.click(import_from_shebanq);

        this.finish_construct();
    }

    //------------------------------------------------------------------------------------------
    // switchToMql method
    //
    // Switches between the friendly feature selector and MQL input.
    //
    // Parameter:
    //     useMql: True to switch to MQL, false to switch to friendly feature selector.
    //
    protected switchToMql(useMql : boolean) : void {
        // Enable/disable various HTML elements in the panel
        this.mqlText.prop('disabled', !useMql);
	this.objectTypeCombo.prop('disabled', useMql);
	this.featureCombo.prop('disabled', useMql);
        this.cbUseForQo.prop('disabled', useMql);

        // Show that certain HTML elements are enabled or disabled
        if (useMql) {
	    this.questObjTypeLab.addClass('disabled');
	    this.featSelLab.addClass('disabled');
            this.cbUseForQoLabel.addClass('disabled');
            this.cbUseForQo.prop('checked', false); // We must use sentence unit selection if MQL is enabled
            this.templTabs.tabs('enable',3);
            this.importShebanq.prop('disabled',false);
        }
        else {
	    this.questObjTypeLab.removeClass('disabled');
	    this.featSelLab.removeClass('disabled');
	    this.cbUseForQoLabel.removeClass('disabled');
            this.importShebanq.prop('disabled',true);
        }

        // Hide or show the current feature selector
	if (this.currentBox) {
            if (useMql)
		this.currentBox.hide();
            else
		this.currentBox.show();
        }

        this.populateFeatureTab(null);
    }

    //------------------------------------------------------------------------------------------
    // makeMql method
    //
    // Convert the settings of the friendly feature selectors to MQL. The feature selection is
    // embedded in an appropriate [ otype NORETRIEVE ... ] MQL string.
    //
    // Returns:
    //     The generated MQL string.
    //
    protected makeMql() : string {
        return `[${this.getOtype()} NORETRIEVE ${super.makeMql()}]`;
    }
    
    //------------------------------------------------------------------------------------------
    // getUseForQo method
    //
    // Returns the value of the "Use for sentence unit selection" checkbox.
    //
    protected getUseForQo() : boolean {
        return this.cbUseForQo.prop('checked');
    }
    
    //------------------------------------------------------------------------------------------
    // isDirty method
    //
    // Returns true if the user has changed the data in the exercise template.
    //
    public isDirty() : boolean {
	return super.isDirty() || this.dirty;
    }

    //------------------------------------------------------------------------------------------
    // doLayout method
    //
    // Performs the actual layout of the HTML code generated by this class and its superclass.
    //
    // Parameter:
    //     where: The <div> where this class should store the generated HTML.
    //
    private doLayout(where : JQuery) : void {
        // The layout is a table containing a number of rows
        let table : JQuery = $('<table></table>');
        let row   : JQuery;
        let cell  : JQuery;
        let fpan2 : JQuery = $('<div style="display:none; padding-top:10px;" id="fpan2"></div>');
        //let accordion2 : JQuery = $('<div id="accordion2" class="accordion"></div>');
        //let card : JQuery = $('<div class="card"></div>');
        //let card_header : JQuery = $('<div id="cardhead_0" class="card-header"></div>');
        //let card_body : JQuery = $(`<div id="card-body_0" class="card-body"></div>`);


        // 1st row: Contains the 'Use for sentence unit selection' checkbox
        row = $('<tr></tr>');
        cell = $('<td colspan="2"></td>');
        cell.append(this.cbUseForQo, '&nbsp;', this.cbUseForQoLabel);
        row.append(cell);
        table.append(row);

        // 2nd row: Contains the MQL selector radio button and the MQL <textarea>
        row = $('<tr></tr>');
        cell = $('<td></td>');
       
        cell.append(this.rbMql, '&nbsp;', this.rbMqlLabel);
        row.append(cell);
        
        cell = $('<td></td>');
        cell.append(this.mqlText);
        row.append(cell);
        table.append(row);

        // 3rd row: Contains the 'Import from SHEBANQ' button
        row = $('<tr></tr>');
        cell = $('<td></td>');
        row.append(cell);
        cell = $('<td></td>');
        cell.append(this.importShebanq);
        row.append(cell);
        table.append(row);

        // 4th row: Contains the friendly feature selector radio button
        row = $('<tr></tr>');
        cell = $('<td colspan="2"></td>');
        cell.append(this.rbFriendly, '&nbsp;', this.rbFriendlyLabel);
        row.append(cell);
        table.append(row);

        // 5th row: Contains the question object type combobox
        row = $('<tr></tr>');
        cell = $('<td></td>');
       
        cell.append(this.questObjTypeLab);
        row.append(cell);
        
        cell = $('<td></td>');
        cell.append(this.objectTypeCombo);
        row.append(cell);
        table.append(row);

        // 6th row: Contains the feature selector combobox
        row = $('<tr></tr>');
        cell = $('<td></td>');
       
        cell.append(this.featSelLab);
        row.append(cell);
        
        cell = $('<td></td>');
        cell.append(this.featureCombo);
        row.append(cell);
        table.append(row);

        // 7th row: Contains the 'Clear' button and the feature selector panels
        row = $('<tr></tr>');
        cell = $('<td id="clearbuttoncell"></td>');

        cell.append(this.clear);
        cell.append(this.test_query);
        row.append(cell);
        
        cell = $('<td></td>');
        cell.append(this.fpan);
        row.append(cell);
        table.append(row);
        
        where.append(table);
        where.append(fpan2);
        /*
        let all_books = ["Matthew", "Mark", "Luke", "John"];
        for(let i = 0; i < all_books.length; i++) {
            let book_name = all_books[i];
            let book_cell = $(`<tr class="bookrow_${i}"></tr>`);
            let book_data = $(`<td id=row_book_${i}></td>`);
            let book_button = $(`<button data-toggle="collapse" data-target="" id=book_${i} class="btn text-left"><b>${book_name}</b><span></span></button>`);

            book_data.append(book_button);
            book_cell.append(book_data);

            if(i == 0) {
                // add the first book section to the accordion
                card.append(card_header);
                card.append(card_body);
                accordion2.append(card);
                fpan2.append(accordion2);
                where.append(fpan2);
                $('#cardhead_0').append(book_cell); 
            }
            else {
                // create a new card body and header
                let new_card : JQuery = $('<div class="card"></div>');
                let new_card_header : JQuery = $(`<div id="cardhead_${i}" class="card-header"></div>`);
                let new_card_body : JQuery  = $(`<div id="card-body_${i}" class="card-body"></div>`);
                new_card_body.hide(); // for non initial cards hide the card body
                new_card.append(new_card_header);
                new_card.append(new_card_body);
                accordion2.append(new_card);
                fpan2.append(accordion2);
                
                new_card_header.append(book_cell);

            }
        }
        */


    }

    //------------------------------------------------------------------------------------------
    // populateFeatureTab method
    //
    // Generate the content of the 'Features' tab based on the newly selected question object type.
    //
    // Parameter:
    //     otype: The question object to set.
    //
    public populateFeatureTab(otype: string) : void {
        if (this.cbUseForQo.prop('checked')) {
            // The current class has the object type
            if (otype === null)
                otype = this.getOtype();
            this.featureTab.populate(otype);
        }
        else
            // The sentence unit selector tab has the object type
            this.qoselTab.populateFeatureTab(null);
    }
}
