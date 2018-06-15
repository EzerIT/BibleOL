// -*- js -*-
// Copyright © 2018 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk

// Code for generating HTML for checkboxes in a feature selector.


//****************************************************************************************************
// SortingCheckBox class
//
// This class generates HTML for a checkbox associated with a StringWithSort. The generated HTML
// looks like this:
// 
//    <span><input type="checkbox" name="NAME" value="VALUE">TEXT</span>
//
// where NAME, VALUE and TEXT are provided by the class user.
//
class SortingCheckBox {
    private sws      : StringWithSort; // Contains the descriptive text string for the checkbox
    private checkbox : JQuery;         // The checkbox
    private jq       : JQuery;         // HTML code for <span><input ...>TEXT</span>

    //------------------------------------------------------------------------------------------
    // Constructor method
    //
    // Parameters:
    //     name: The "name" attribute of the <input> element.
    //     value: The "value" attribute of the <input> element.
    //     text: The desciptive text of for the checkbox.
    constructor(name : string, value : string, text : string) {
        this.sws = new StringWithSort(text);

        this.checkbox = $(`<input type="checkbox" name="${name}" value="${value}">`);
        this.jq = $('<span></span>');
        this.jq.append(this.checkbox, this.sws.getString());
    }

    //------------------------------------------------------------------------------------------
    // setSelected method
    //
    // Checks or unchecks the checkbox.
    //
    // Parameter:
    //     selected: True for checking, false for unchecking the checkbox.
    //
    public setSelected(selected : boolean) : void {
	this.checkbox.prop('checked',selected);
    }

    //------------------------------------------------------------------------------------------
    // getSws method
    //
    // Returns:
    //     The StringWithSort used for the descriptive text.
    //
    public getSws() : StringWithSort {
        return this.sws;
    }

    //------------------------------------------------------------------------------------------
    // getJQuery method
    //
    // Returns:
    //     The JQuery object for the entire HTML code.
    //
    public getJQuery() : JQuery {
        return this.jq;
    }
}


