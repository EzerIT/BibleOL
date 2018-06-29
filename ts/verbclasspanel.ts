// -*- js -*-
// Copyright © 2018 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk

// Code to handle feature selection for the verb class feature. This is the only feature of type
// "list of ..." that can be handleded by Bible OL at present.


//****************************************************************************************************
// VerbCalssSelection enumeration
//
// Names the radio buttons that speficy the inclusion or exclusion of a verb class.
//
enum VerbClassSelection { YES, NO, DONT_CARE };

//****************************************************************************************************
// VerbClassButtonsAndLabel class
//
// Handles the buttons and name of a single verb class.
//
class VerbClassButtonsAndLabel {
    private yes      : JQuery; // The "Yes" radio button
    private no       : JQuery; // The "No" radio button
    private dontcare : JQuery; // The "Don't care" radio button
    private label    : JQuery; // The <span> element containing the verb class name

    //------------------------------------------------------------------------------------------
    // Constructor method
    //
    // Generates HTML code for a single line in one of the "choice" tabs.
    //
    // The parameters are described below.
    //
    constructor(lab      : string,            // The localized name of the verb class
                name     : string,            // The value of the name="..." attribute of the <input> element for the radio button
                dataName : string,            // The Emdros name of the verb class (the enumeration constant)
                select   : VerbClassSelection // Initially selected radio button
               ) {

        this.yes      = $(`<input type="radio" name="${name}" value="yes"      data-name="${dataName}">`);
        this.no       = $(`<input type="radio" name="${name}" value="no"       data-name="${dataName}">`);
        this.dontcare = $(`<input type="radio" name="${name}" value="dontcare" data-name="${dataName}">`);
        this.label    = $(`<span>${lab}</span>`);

        switch (select) {
        case VerbClassSelection.YES:        this.yes.prop('checked',true);      break;
        case VerbClassSelection.NO:         this.no.prop('checked',true);       break;
        case VerbClassSelection.DONT_CARE:  this.dontcare.prop('checked',true); break;
        }
    }

    //------------------------------------------------------------------------------------------
    // getRow method
    //
    // Returns HTML for the table row managed by this class.
    //
    public getRow() : JQuery {
        var row  : JQuery = $('<tr></tr>');
        var cell : JQuery;

        cell = $('<td></td>')                  .append(this.yes);       row.append(cell);
        cell = $('<td></td>')                  .append(this.no);        row.append(cell);
        cell = $('<td></td>')                  .append(this.dontcare);  row.append(cell);
        cell = $('<td class="leftalign"></td>').append(this.label);     row.append(cell);

        return row;
    }
}

//****************************************************************************************************
// PanelForOneVcChoice class
//
// This class contains the verb class selection for one of the verb class coices.
//
class PanelForOneVcChoice  {
    public allBAL : VerbClassButtonsAndLabel[] = [];                            // The lines of the verb choice panel
    private panel : JQuery = $('<table class="striped featuretable"></table>'); // The verb choice panel

    //------------------------------------------------------------------------------------------
    // Constructor method
    //
    // Creates a verb class selection panel for a single verb class choice
    //
    // Parameters:
    //     enumValues
    constructor(enumValues : string[],         // Enumeration constants
                valueType  : string,           // Text folliwing 'list of ...' in feature type
                prefix     : string,           // Prefix to add to the name="..." attribute
                lv         : ListValuesHandler // Handler for the verb class collections
               ) {
        // Add headings to the table
        this.panel.append('<tr>'
                          + `<th>${localize('verb_class_yes')}</th>`
                          + `<th>${localize('verb_class_no')}</th>`
                          + `<th>${localize('verb_class_dont_care')}</th>`
                          + `<th class="leftalign">${localize('verb_class')}</th>`
                          + '</tr>');

        var swsValues : StringWithSort[] = []; // Sortable values of all the enumeration constants
        for (var ix=0; ix<enumValues.length; ++ix)
            swsValues.push(new StringWithSort(getFeatureValueFriendlyName(valueType, enumValues[ix], false, false), enumValues[ix]));

        // Sort the enumeration constants
        swsValues.sort((a : StringWithSort, b : StringWithSort) => StringWithSort.compare(a,b));


        // Loop through the keys in the sorted order
        for (var ix=0; ix<swsValues.length; ++ix) {
            var vc    : string             = swsValues[ix].getInternal();  // Verb class name
            var vcsel : VerbClassSelection = VerbClassSelection.DONT_CARE; // Radio button setting

            // Modfy vcsel to reflect the initial setting
            if (lv.yes_values.indexOf(vc)!=-1)
                vcsel = VerbClassSelection.YES;
            else if (lv.no_values.indexOf(vc)!=-1)
                vcsel = VerbClassSelection.NO;

            // Create a line of radio buttons
            var bal = new VerbClassButtonsAndLabel(swsValues[ix].getString(),  // Localized name of the verb class
                                                   `${prefix}_${vc}`,          // Value of the name="..." attribute of the <input> element for the radio button
                                                   vc,                         // Name of the verb class
                                                   vcsel);                     // Initially selected radio button
            this.allBAL.push(bal);
            this.panel.append(bal.getRow());
         }
    }

    //------------------------------------------------------------------------------------------
    // getPanel method
    //
    // Returns the HTML for the current verb class choice.
    //
    public getPanel() : JQuery {
        return this.panel;
    }
}
