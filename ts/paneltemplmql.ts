// -*- js -*-
// Copyright © 2018 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk

// Code to handle feature selection when creating an exercise



//****************************************************************************************************
// FeatureHandler class
//
// Superclass of all the handlers for feature searches in an exercise.
//
abstract class FeatureHandler {
    public type       : string; // The feature type ('stringfeature', 'integerfeature',
                                // 'rangeintegerfeature', 'enumfeature', 'enumlistfeature', or
                                // 'qerefeature')
    public name       : string; // The name of the feature
    public comparator : string; // Feature comparator. (Not used in all subclasses, but included
                                // here for the sake of a couple of methods.)

    //------------------------------------------------------------------------------------------
    // Constructor method
    //
    // Initializes the class fields.
    //
    // Parameters:
    //     type: The feature type ('stringfeature', 'integerfeature' etc.).
    //     key: The feature name.
    //
    constructor(typ : string, key : string) {
        this.type       = typ;
        this.name       = key;
        this.comparator = 'equals';
    }

    //------------------------------------------------------------------------------------------
    // normalize method
    //
    // Does nothing in this class. (In subclasses the function is intended to ensure that arrays in
    // an object has the expected number of elements.)
    //
    public normalize() : void {
        // Nothing
    }

    //------------------------------------------------------------------------------------------
    // hasValues method
    //
    // Returns true if the feature handler contains values.
    //
    public abstract hasValues() : boolean;

    
    //------------------------------------------------------------------------------------------
    // toMql method
    //
    // Returns an MQL string that selects Emdros objects based on the features encoded in the
    // FeatureHandler.
    //
    public abstract toMql() : string;

    //------------------------------------------------------------------------------------------
    // getComparator method
    //
    // Returns the MQL operator that represents the feature comparator.
    //
    protected getComparator() : string {
        switch (this.comparator) {
          case 'equals': return '=';
          case 'differs': return '<>';
          case 'matches': return '~';
        }
        return '';
    }

    //------------------------------------------------------------------------------------------
    // getJoing method
    //
    // When a feature selection contains multiple values, the resulting comparison must be combined
    // using either an AND or an OR operation. For example, when selecting a feature using 'a=1' and
    // 'a=2', the resulting comparison string should be 'a=1 OR a=2'; but when selecting a feature
    // using 'a<>1' and 'a<>2', the resulting comparison string should be 'a<>1 AND a<>2'.
    //
    // This function returns the MQL logic operator to use when combining several feature
    // comparisons.
    //
    public getJoiner() : string {
        switch (this.comparator) {
          case 'equals':  return ' OR ';
          case 'differs': return ' AND ';
          case 'matches': return ' OR ';
        }
        return '';
    }
}

//****************************************************************************************************
// StringFeatureHandler class
//
// This class handles searches for features that are strings.
//
class StringFeatureHandler extends FeatureHandler {
    public values : string[]; // The feature values to look for

    //------------------------------------------------------------------------------------------
    // Constructor method
    //
    // Parameter:
    //     key: The feature name.
    //
    constructor(key : string) {
        super('stringfeature', key);
        this.values = [];
        this.normalize();
    }

    //------------------------------------------------------------------------------------------
    // normalize method
    //
    // Ensures that the values array has 4 elements by adding null values when necessary.
    //
    public normalize() : void {
        while (this.values.length<4)
            this.values.push(null);
    }

    //------------------------------------------------------------------------------------------
    // setValue method
    //
    // Sets one of the feature values to a specific string.
    //
    // Parameters:
    //     index: The index into the array of string values handled by this StringFeatureHandler
    //            where this value should be stored.
    //     val: The value to set.
    //
    public setValue(index : number, val : string) : void {
        this.values[index] = val;
    }

    //------------------------------------------------------------------------------------------
    // removeValue method
    //
    // Removes one of the feature values.
    //
    // Parameters:
    //     index: The index of the string value to remove.
    //
    public removeValue(index : number) : void {
        this.values[index] = null;
    }

    //------------------------------------------------------------------------------------------
    // hasValues method
    //
    // Returns true if the feature handler contains values.
    //
    public hasValues() : boolean {
        for (let i=0; i<this.values.length; ++i)
            if (this.values[i]!==null)
                return true;

	return false;
    }

    //------------------------------------------------------------------------------------------
    // toMql method
    //
    // Returns an MQL string that selects Emdros objects based on the features encoded in the
    // StringFeatureHandler.
    //
    public toMql() : string {
        let comparator : string   = this.getComparator();
        let values     : string[] = [];

	for (let i=0; i<this.values.length; ++i)
            if (this.values[i]!==null)
                values.push(this.name + comparator + '"' + this.values[i] + '"');


	if (values.length===1)
	    return values[0];

	return '(' + values.join(this.getJoiner()) + ')';
    }
}

//****************************************************************************************************
// IntegerFeatureHandler class
//
// This class handles searches for features that have specific integer values.
//
class IntegerFeatureHandler extends FeatureHandler {
    public values : number[]; // The feature values to look for

    //------------------------------------------------------------------------------------------
    // Constructor method
    //
    // Parameter:
    //     key: The feature name.
    //
    constructor(key : string) {
        super('integerfeature', key);
        this.values = [];
        this.normalize();
    }

    //------------------------------------------------------------------------------------------
    // normalize method
    //
    // Ensures that the values array has 4 elements by adding null values when necessary.
    //
    public normalize() : void {
        while (this.values.length<4)
            this.values.push(null);
    }

    //------------------------------------------------------------------------------------------
    // setValue method
    //
    // Sets one of the feature values to a specific number.
    //
    // Parameters:
    //     index: The index into the array of integer values handled by this IntegerFeatureHandler
    //            where this value should be stored.
    //     val: The value to set.
    //
    public setValue(index : number, val : number) : void {
        this.values[index] = val;
    }

    //------------------------------------------------------------------------------------------
    // removeValue method
    //
    // Removes one of the feature values.
    //
    // Parameters:
    //     index: The index of the integer value to remove.
    //
    public removeValue(index : number) : void {
        this.values[index] = null;
    }

    //------------------------------------------------------------------------------------------
    // hasValues method
    //
    // Returns true if the feature handler contains values.
    //
    public hasValues() : boolean {
        for (let i=0; i<this.values.length; ++i)
            if (this.values[i]!==null)
                return true;

	return false;
    }

    //------------------------------------------------------------------------------------------
    // toMql method
    //
    // Returns an MQL string that selects Emdros objects based on the features encoded in the
    // IntegerFeatureHandler.
    //
    public toMql() : string {
        let values : number[] = [];

	for (let i=0; i<this.values.length; ++i)
            if (this.values[i]!==null)
                values.push(this.values[i]);

	if (values.length===1)
	    return this.name + this.getComparator() + values[0];
        else
	    return (this.comparator==='differs' ? 'NOT ' : '')
		+ this.name + ' IN (' + values.join(',') + ')';
    }
}


//****************************************************************************************************
// RangeIntegerFeatureHandler class
//
// This class handles searches for features that have integer values in a specific range.
//
class RangeIntegerFeatureHandler extends FeatureHandler {
    // Either of these values may be null or undefined to indicate the absense of a limit.
    public value_low : number;  // The lower limit to the range of requested values 
    public value_high : number; // The upper limit to the range of requested values 

    //------------------------------------------------------------------------------------------
    // set_low_high method
    //
    // Sets either value_low or value_high based on the index parameter.
    //
    // Parameters:
    //     index: Either 'value_low' or 'value_high'.
    //     val: The value to store in the specified field.
    //
    public set_low_high(index : string, val : number) : void {
        switch (index) {
        case 'value_low':
            this.value_low = val;
            break;
            
        case 'value_high':
            this.value_high = val;
            break;
            
        default:
            throw 'Illegal index in access_low_high';
        }
    }

    //------------------------------------------------------------------------------------------
    // Constructor method
    //
    // Parameter:
    //     key: The feature name.
    //
    constructor(key : string) {
        super('rangeintegerfeature', key);
    }

    //------------------------------------------------------------------------------------------
    // isSetLow method
    //
    // Returns true if the lower range limit is set.
    //
    public isSetLow() : boolean {
        return this.value_low!==null && this.value_low!==undefined;
    }

    //------------------------------------------------------------------------------------------
    // isSetLow method
    //
    // Returns true if the upper range limit is set.
    //
    public isSetHigh() : boolean {
        return this.value_high!==null && this.value_high!==undefined;
    }

    //------------------------------------------------------------------------------------------
    // hasValues method
    //
    // Returns true if the feature handler contains values.
    //
    public hasValues() : boolean {
        return this.isSetLow() || this.isSetHigh();
    }

    //------------------------------------------------------------------------------------------
    // toMql method
    //
    // Returns an MQL string that selects Emdros objects based on the features encoded in the
    // RangeIntegerFeatureHandler.
    //
    public toMql() : string {
        if (this.isSetLow()) {
            if (this.isSetHigh())
                return '(' + this.name + '>=' + this.value_low + ' AND ' + this.name + '<=' + this.value_high + ')';
            else
                return this.name + '>=' + this.value_low;
        }
        else {
            if (this.isSetHigh())
                return this.name + '<=' + this.value_high;
            else
                return '';
        }
    }
}


//****************************************************************************************************
// EnumFeatureHandler class
//
// This class handles searches for features that are enumeration values.
//
class EnumFeatureHandler extends FeatureHandler {
    public values : string[]; // The feature values to look for

    //------------------------------------------------------------------------------------------
    // Constructor method
    //
    // Parameter:
    //     key: The feature name.
    //
    constructor(key : string) {
        super('enumfeature', key);
        this.values = [];
    }

    //------------------------------------------------------------------------------------------
    // addValue method
    //
    // Adds a feature value to the array of values to look for.
    //
    // Parameters:
    //     val: The value to add.
    //
    public addValue(val : string) : void {
        this.values.push(val);
    }

    //------------------------------------------------------------------------------------------
    // removeValue method
    //
    // Removess a feature value from the array of values to look for.
    //
    // Parameters:
    //     val: The value to remove.
    //
    public removeValue(val : string) : void {
        let index = this.values.indexOf(val);

        if (index > -1)
            this.values.splice(index, 1);
    }

    //------------------------------------------------------------------------------------------
    // hasValues method
    //
    // Returns true if the feature handler contains values.
    //
    public hasValues() : boolean {
        return this.values.length > 0;
    }

    //------------------------------------------------------------------------------------------
    // toMql method
    //
    // Returns an MQL string that selects Emdros objects based on the features encoded in the
    // EnumFeatureHandler.
    //
    public toMql() : string {
        return (this.comparator==='differs' ? 'NOT ' : '')
            + this.name + ' IN (' + this.values.join(',') + ')';
    }
}


//****************************************************************************************************
// EnumListFeatureHandler class
//
// This class handles searches for features that belong to one of several collections of enumeration
// values.
//
class EnumListFeatureHandler extends FeatureHandler {
    public listvalues : ListValuesHandler[]; // Handlers for each collection of enumeration values

    //------------------------------------------------------------------------------------------
    // Constructor method
    //
    // Parameter:
    //     key: The feature name.
    //
    constructor(key : string) {
        super('enumlistfeature', key);
        this.listvalues = [];
        this.normalize();
    }

    //------------------------------------------------------------------------------------------
    // normalize method
    //
    // Ensures that the listvalues array has 4 elements by adding null handlers when necessary.
    //
    public normalize() : void {
        while (this.listvalues.length<4)
            this.listvalues.push(new ListValuesHandler());
    }

    //------------------------------------------------------------------------------------------
    // hasValues method
    //
    // Returns true if the feature handler contains values.
    //
    public hasValues() : boolean {
        for (let i=0; i<this.listvalues.length; ++i)
            if (this.listvalues[i].hasValues())
                return true;

        return false;
    }

    //------------------------------------------------------------------------------------------
    // toMql method
    //
    // Returns an MQL string that selects Emdros objects based on the features encoded in the
    // EnumListFeatureHandler.
    //
    public toMql() : string {
        if (this.listvalues.length>0) {
	    let sb : string = '(';
	    let first : boolean = true;

	    for (let i=0; i<this.listvalues.length; ++i) {
	        let lvh : ListValuesHandler = this.listvalues[i];

		if (lvh.hasValues()) {
		    if (first)
			first = false;
		    else
			sb += ' OR ';
		    sb += lvh.toMql(this.name);
		}
	    }
            return sb + ')';
        }
        else
            return '';
    }
}


//****************************************************************************************************
// QereFeatureHandler class
//
// This class handles searches for Hebrew qere forms that need to be omitted from a selection.
//
class QereFeatureHandler extends FeatureHandler {
    public omit : boolean; // True if qere forms are to be omitted

    //------------------------------------------------------------------------------------------
    // Constructor method
    //
    // 
    // Parameter:
    //     key: The name of the feature holding a qere value.
    //
    constructor(key : string) {
        super('qerefeature', key);
        this.omit = false;
    }

    //------------------------------------------------------------------------------------------
    // setValue method
    //
    // Sets or the handling of qere forms.
    //
    // Parameter:
    //     val: True if qere forms are to be omitted, false otherwise.
    //
    public setValue(val : boolean) : void {
        this.omit = val;
    }

    //------------------------------------------------------------------------------------------
    // hasValues method
    //
    // Returns true if the feature handler is set to omit qere forms.
    //
    public hasValues() : boolean {
        return this.omit;
    }

    //------------------------------------------------------------------------------------------
    // toMql method
    //
    // Returns an MQL string that selects Emdros objects based on the specification in the
    // QereFeatureHandler.
    //
    public toMql() : string {
        if (this.omit)
            return "(" + this.name + "='' AND g_word_translit<>'HÎʔ')";
        else
            return '';
    }
}


//****************************************************************************************************
// ListValuesHandler class
//
// This class handling of searches for features that belong to a collection of enumeration values.
// The class maintains two lists of enumeration values. One contains enumeration values that must be
// present in the search result, the other contains enumeration values that must not be present in
// the search result.
//
class ListValuesHandler {
    public type       : string;    // Always 'listvalues'
    public yes_values : string[];  // Enumeration values that must be present in the search result
    public no_values  : string[];  // Enumeration values that must not be present in the search result

    //------------------------------------------------------------------------------------------
    // Constructor method
    //
    constructor() {
        this.type = 'listvalues';
        this.yes_values = [];
        this.no_values = [];
    }
    

    //------------------------------------------------------------------------------------------
    // modifyValue method
    //
    // Adds or removes an enumeration value from the list of values to include or exclude.
    //
    // Parameters:
    //     name: The name of the enumeration value.
    //     yes: The string 'yes' if the name must be present in the search result.
    //          The string 'no' if the name must not be present in the search result.
    //          The string 'dontcare' if the name is irrelevant for the search result.
    //
    public modifyValue(name : string, val : string) : void {
        let yes_index = this.yes_values.indexOf(name); // >-1 if name is found in yes_values
        let no_index  = this.no_values.indexOf(name);  // >-1 if name is found in no_values

        switch (val) {
        case 'yes':
            if (yes_index==-1)
                this.yes_values.push(name);
            if (no_index>-1)
                this.no_values.splice(no_index, 1);
            break;

        case 'no':
            if (no_index==-1)
                this.no_values.push(name);
            if (yes_index>-1)
                this.yes_values.splice(yes_index, 1);
            break;

        case 'dontcare':
            if (no_index>-1)
                this.no_values.splice(no_index, 1);
            if (yes_index>-1)
                this.yes_values.splice(yes_index, 1);
            break;
        }
    }

    //------------------------------------------------------------------------------------------
    // hasValues method
    //
    // Returns true if the feature handler contains values that must or must not be present in the
    // search result.
    //
    public hasValues() : boolean {
        return this.yes_values.length + this.no_values.length > 0;
    }

    //------------------------------------------------------------------------------------------
    // toMql method
    //
    // Returns an MQL string that selects Emdros objects based on the requirements in the
    // ListValuesHandler
    //
    public toMql(featName : string) : string {
        let stringValues : string[] = [];

        for (let ix=0; ix<this.yes_values.length; ++ix)
            stringValues.push(`${featName} HAS ${this.yes_values[+ix]}`);

        for (let ix=0; ix<this.no_values.length; ++ix)
            stringValues.push(`NOT ${featName} HAS ${this.no_values[+ix]}`);

        if (stringValues.length===1)
            return stringValues[0];

	return '(' + stringValues.join(' AND ') + ')';
    }
}


//****************************************************************************************************
// MqlData inteface
//
// Describes the structure of feature selection information as found in exercise definitions.
//
interface MqlData {
    object   : string;            // The type of the Emdros object to look for
    mql	     : string;		  // MQL string to use in search (may be null)
    featHand : {
        vhand: FeatureHandler[];  // A collection of FeatureHandlers
    }
    useForQo : boolean;           // Use for sentence unit selection? (Set only for sentence selection)
}


//****************************************************************************************************
// PanelTemplMql class
//
// This is a superclass for the sentence selection and sentence unit selection panels
// (PanelTemplSentenceSelector and PanelTemplQuizObjectSelector). It contains code for generating
// feature selectors.
//
// This class does not do the actual layout. That is done by the doLayout methods in the subclasses.
//
class PanelTemplMql {
    protected currentBox      : JQuery;  // The feature selector currently displayed
    protected featureCombo    : JQuery;  // Feature name combobox
    protected fpan            : JQuery;  // <div> to contain all feature selectors
    protected mqlText         : JQuery;  // The MQL <textarea>
    protected objectTypeCombo : JQuery;  // Object type combobox
    protected rbFriendly      : JQuery;  // Radio button for selecting the friendly feature selector
    protected rbFriendlyLabel : JQuery;  // Localized version of "Friendly feature selector"
    protected rbMql           : JQuery;  // Radio button for selecting MQL input
    protected rbMqlLabel      : JQuery;  // Localized label for MQL feature selector
    protected clear           : JQuery;  // The 'Clear' button
    protected initialMd       : MqlData; // The initial contents of the panel

    private   fname2fh        : { [key:string] : FeatureHandler; } = {}; // Maps feature name => feature handler for features
                                                                         // in initial contents of exercise file
    private   groups          : { [key:string] : JQuery; } = {};         // Maps feature name => feature selector
    private   handlers        : FeatureHandler[] = [];                   // FeatureHandlers for all features
    private   txtEntry        : string;                                  // The initial contents of the MQL text
    private   name_prefix     : string;                                  // Used as prefix for HTML element names
                                                                         // ('sensel' for sentence selector,
                                                                         // 'qosel' for sentence unit selector).

    
    //------------------------------------------------------------------------------------------
    // Constructor method
    //
    // Creates the common parts of the feature selection panel.
    //
    // Parameter:
    //     md: Selection information from exercise file.
    //     name_prefix: Identifies subclass ('sensel' for sentence selector, 'qosel' for sentence
    //                  unit selector).
    //
    constructor(md : MqlData, name_prefix : string) {
        this.initialMd = md;
        this.name_prefix = name_prefix;

        if (md.featHand) {
            // Exercise file has friendly feature selectors
            
            for (let i=0; i<md.featHand.vhand.length; ++i) {
                let vh : FeatureHandler = md.featHand.vhand[i]; // Current feature handler

                // Make feature handler polymorphic by adding relevant methods
                switch (vh.type) {
                case 'enumfeature':
                    addMethods(vh, EnumFeatureHandler, null);
                    break;

                case 'enumlistfeature':
                    addMethods(vh, EnumListFeatureHandler, null);
                    let elfh : EnumListFeatureHandler = vh as EnumListFeatureHandler;
                    for (let j=0; j < elfh.listvalues.length; ++j)
                        addMethods(elfh.listvalues[j], ListValuesHandler, null);
                    break;

                case 'stringfeature':
                    addMethods(vh, StringFeatureHandler, null);
                    break;

                case 'integerfeature':
                    addMethods(vh, IntegerFeatureHandler, null);
                    break;

                case 'rangeintegerfeature':
                    addMethods(vh, RangeIntegerFeatureHandler, null);
                    break;

                case 'qerefeature':
                    addMethods(vh, QereFeatureHandler, null);
                    break;
                }
                vh.normalize();

                // Make feature accessible by name
                this.fname2fh[vh.name] = vh;
            }
        }

        ////////////////////////////////////////////////////////////
        // The <div> that contains all the feature selectors (of which
        // at most one is shown at a time).
        this.fpan = $(`<div id="${this.name_prefix}_fpan"></div>`);


        ////////////////////////////////////////////////////////////
        // The radio button that switches to MQL input
        this.rbMql = $(`<input type="radio" name="${this.name_prefix}_usemql" value="yes">`);
        this.rbMql.click(() => {
            if (this.rbMql.is(':checked'))
 		this.switchToMql(true);
        });

        
        ////////////////////////////////////////////////////////////
        // The radio button that switches to friendly input
        this.rbFriendly = $(`<input type="radio" name="${this.name_prefix}_usemql" value="no">`);
	this.rbFriendly.click(() => {
            if (this.rbFriendly.is(':checked')) {
 		this.switchToMql(false);
                this.updateMql();
            }
        });


        ////////////////////////////////////////////////////////////
        // The MQL <textarea>
        this.mqlText = $(`<textarea id="${this.name_prefix}_mqltext" cols="45" rows="2">`);


        ////////////////////////////////////////////////////////////
        // The feature name combobox
        this.featureCombo = $('<select></select>');
        this.featureCombo.on('change', () => {
            // When a new feature is selected, show the appropriate selector
            this.currentBox.hide();
            this.currentBox = this.groups[this.featureCombo.val() as string];
            this.currentBox.show();
        });

        
        ////////////////////////////////////////////////////////////
        // The object type combobox
        this.objectTypeCombo = $('<select></select>');
        
        let selObject : string = (md!=null && md.object!=null) ? md.object : configuration.objHasSurface; // Initially selected object

        // Loop through all objects that may be selected
        for (let s in configuration.objectSettings) {
            if (configuration.objectSettings[s].mayselect) { 
                this.objectTypeCombo.append(`<option value="${s}"`
                                            + (s===selObject ? ' selected="selected"' : '')                // Select initial object
                                            + (s===configuration.objHasSurface ? ' data-reset="yes"' : '') // Select surface object on reset
                                            + `>${getObjectFriendlyName(s)}</option>`);
            }
        }

        this.objectTypeCombo.on('change', () => {
            // When a new object type is selected, reset the contents of all selectors
            
            $('#virtualkbid').appendTo('#virtualkbcontainer'); // Move the keyboard back to its initial position

            this.fpan.html(`<div id="${this.name_prefix}_fpan"></div>`);
            this.currentBox = null
            this.featureCombo.html('<select></select>');
            this.objectSelectionUpdated(this.objectTypeCombo.val() as string, null)
            this.updateMql();
        });


        ////////////////////////////////////////////////////////////
        // The 'Clear' button
        this.clear = $('<button id="clear_button" type="button">' + localize('clear_button') + '</button>');

        this.clear.click(() => {
            // When 'Clear' is pressed, reset everything and select the surface object ('[data-reset]')
            
            $('#virtualkbid').appendTo('#virtualkbcontainer'); // Move the keyboard back to its initial position

	    this.rbFriendly.prop('checked',true);
	    this.objectTypeCombo.find(':selected').prop('selected',false);
            this.objectTypeCombo.find('[data-reset]').prop('selected',true);
            this.fpan.html(`<div id="${this.name_prefix}_fpan"></div>`);
	    this.currentBox = null;
            this.featureCombo.html('<select></select>');
	    this.objectSelectionUpdated(configuration.objHasSurface,null);
	    this.updateMql();
	    this.switchToMql(false);		
	});
    }

    //------------------------------------------------------------------------------------------
    // finish_construct method
    //
    // This function contains code to execute at the end of the constructor. The constructor methods
    // of subclasses are required to call this method before they return.
    //
    protected finish_construct() : void {
	if (this.initialMd==null) {
            // We're creating a new exercise
            this.rbFriendly.prop('checked',true);
	    this.objectSelectionUpdated(configuration.objHasSurface,null);
	    this.updateMql();
	    this.switchToMql(false);		
	}
	else if (this.initialMd.mql!=null) {
            // We're handling an existing exercise using the MQL selector
            this.rbMql.prop('checked',true);
	    this.mqlText.html(this.initialMd.mql);
            if (this.initialMd.featHand)
	        this.objectSelectionUpdated(this.initialMd.object,this.fname2fh);
            else 
	        this.objectSelectionUpdated(this.initialMd.object,null);
	    this.switchToMql(true);
	}
	else {
            // We're handling an existing exercise using the friendly selector
            this.rbFriendly.prop('checked',true);
	    this.objectSelectionUpdated(this.initialMd.object,this.fname2fh);
	    this.updateMql();
	    this.switchToMql(false);
	}
 	this.txtEntry = this.getMql();
    }


    //------------------------------------------------------------------------------------------
    // setMql method
    //
    // Sets the MQL <textarea> to a specific value.
    //
    // Parameter:
    //     s: The MQL string to set.
    //
    public setMql(s : string) {
        this.mqlText.val(s);
    }

    //------------------------------------------------------------------------------------------
    // setMql method
    //
    // Retrieves the text in the MQL <textarea>.
    //
    // Returns:
    //     The MQL string.
    //
    private getMql() : string {
        return this.mqlText.val() as string;
    }


    //------------------------------------------------------------------------------------------
    // Variables for managing changes to input fields under virtual keyboard control
    // 
    private intervalHandler : number; // The ID of the timer
    private monitorOrigVal  : string; // The previous value of the monitored <input> element
    private lastMonitored   : string; // The ID of the monitored <input> element

    //------------------------------------------------------------------------------------------
    // monitorChange method
    //
    // Handles changes to input fields under virtual keyboard control. Every 500 ms the current
    // value of a text field is compared to its last value; when the value changes it is updated in
    // the relevant StringFeatureHandler.
    //
    // Parameters:
    //     elem: The <input> element to monitor.
    //     sfh: The StringFeatureHandler containing the string in elem.
    //     i: The index of the string in sfh.
    //
    private monitorChange(elem : JQuery, sfh : StringFeatureHandler, i : number) {
        clearInterval(this.intervalHandler);

        if (this.lastMonitored !== elem.attr('id')) { // A new element has focus
            this.monitorOrigVal = elem.val() as string;
            this.lastMonitored = elem.attr('id');
        }

        this.intervalHandler = setInterval(() => {
            let s : string = elem.val() as string;
            if (s!==this.monitorOrigVal) { // The input value has changed
                this.monitorOrigVal = s;
                if (s.length===0)          // The input value is empty
                    sfh.removeValue(i);
                else                       // The input value is not empty
                    sfh.setValue(i, s);

                this.updateMql();
            }
        }, 500);
    }

    //------------------------------------------------------------------------------------------
    // stringTextModifiedListener method
    //
    // Called when the value in a text input field changes.
    //
    // Parameter:
    //     e: The event that represents the input change.
    //
    private stringTextModifiedListener(e : JQueryEventObject) : void {
        let s   : string               = $(e.currentTarget).val() as string;
        let sfh : StringFeatureHandler = e.data.sfh;

        if (s.length===0)              // The input value is empty
            sfh.removeValue(e.data.i);
        else                           // The input value is not empty
            sfh.setValue(e.data.i, s);

        this.updateMql();
    }

    //------------------------------------------------------------------------------------------
    // integerTextModifiedListener method
    //
    // Called when the value in an integer input field changes.
    //
    // Parameter:
    //     e: The event that represents the input change.
    //
    private integerTextModifiedListener(e : JQueryEventObject) : void {
        let s   : string                = $(e.currentTarget).val() as string;
        let ifh : IntegerFeatureHandler = e.data.ifh;

        $('#' + e.data.err_id).html(''); // Clear error indication
        
        if (s.length===0)              // The input value is empty
            ifh.removeValue(e.data.i);
        else {                         // The input value is not empty
            if (s.match(/\D/g)!==null) // Check that input is an integer (Note: Rejects minus sign)
                $('#' + e.data.err_id).html(localize('not_integer')); // Set error indication
            else
                ifh.setValue(e.data.i, +s);
        }
        
        this.updateMql();
    }

    //------------------------------------------------------------------------------------------
    // rangeIntegerTextModifiedListener method
    //
    // Called when the value in an integer range input field changes.
    //
    // Parameter:
    //     e: The event that represents the input change.
    //
    private rangeIntegerTextModifiedListener(e : JQueryEventObject) : void {
        let s   : string                     = $(e.currentTarget).val() as string;
        let rfh : RangeIntegerFeatureHandler = e.data.rfh;
        
        $('#' + e.data.err_id).html(''); // Clear error indication

        if (s.length===0)                // The input value is empty
            rfh.set_low_high(e.data.i, null);
        else {                           // The input value is not empty
            if (s.match(/\D/g)!==null)   // Check that input is an integer (Note: Rejects minus sign)
                $('#' + e.data.err_id).html(localize('not_integer')); // Set error indication
            else
                rfh.set_low_high(e.data.i, +s);
        }
        
        this.updateMql();
    }


    //------------------------------------------------------------------------------------------
    // objectSelectionUpdated method
    //
    // Creates the various feature selection panels associated with a newly selected quiz object
    // type.
    //
    // Parameters:
    //     otype: Quiz object type.
    //     fhs: Maps feature name => feature handler. This parameter is only set when we're editing
    //          an existing exercise with feature handlers, otherwise this parameter is null.
    //
    private objectSelectionUpdated(otype : string, fhs : { [key:string] : FeatureHandler; }) : void {

 	// Create selection boxes for all features of this object type
        for (let key in getObjectSetting(otype).featuresetting) {
            let valueType : string         = typeinfo.obj2feat[otype][key];
            let featset   : FeatureSetting = getFeatureSetting(otype,key);

            // Ignore features that cannot be used for selection
            if (featset.ignoreSelect)
                continue;

            let group : JQuery = $('<div></div>');

            this.groups[key] = group;

            let selectString : string;

            if (featset.isDefault) {
                group.show();
                this.currentBox = group;
                selectString = 'selected="selected"';
            }
            else {
                group.hide();
                selectString = '';
            }

            // Add feature name to feature combobox
            this.featureCombo.append(`<option value="${key}" ${selectString}>${getFeatureFriendlyName(otype,key)}</option>`);

            //////////////////////////////////////////////////////////////////
            // Set up feature handlers based on the type of the feature values
            //
            if (valueType === 'integer') {
                if (featset.isRange) {
                    ///////////////////////////
                    // Integer range feature //
                    ///////////////////////////
                    
                    let rfh : RangeIntegerFeatureHandler = null;
                    if (fhs)
                        rfh = fhs[key] as RangeIntegerFeatureHandler; // Use existing feature handler...
                    if (!rfh)
                        rfh = new RangeIntegerFeatureHandler(key);    // ...or create a new one

                    // Append this HTML structure to the variable group:    From these variables
                    // <table>                                               group2
                    //   <tr>                                                rowLow
                    //     <td>Label</td>                                    cellLab
                    //     <td><input type="text"...></td>                   cellInput
                    //     <td id="err_id"></td>                             cellErr
                    //   </tr>                                               rowLow
                    //   <tr>                                                rowHigh
                    //     <td>Label</td>                                    cellLab
                    //     <td><input type="text"...></td>                   cellInput
                    //     <td id="err_id"></td>                             cellErr
                    //   </tr>                                               rowHigh
                    // </table>                                              group2

                    let group2    : JQuery = $('<table></table>');
                    let rowLow    : JQuery = $('<tr></tr>');
                    let rowHigh   : JQuery = $('<tr></tr>');

                    let cellLab   : JQuery; // Table cell containing label
                    let cellInput : JQuery; // Table cell containing input field
                    let cellErr   : JQuery; // Table cell containing error indication

                    let jtf       : JQuery; // Integer text field


                    ////////////////////////////////////////////////////////////
                    // First row
                    //
                    jtf = $('<input type="text" size="8">'); // Integer text field

                    if (rfh.isSetLow())
                        jtf.val(String(rfh.value_low));

                    let err_id : string = `err_${key}_low`;

                    // Set handler for field changes
                    jtf.on('keyup', null,
                           {rfh: rfh, i: 'value_low', err_id: err_id}, // Event data
                           $.proxy(this.rangeIntegerTextModifiedListener,this));
                    
                    cellLab = $('<td>' + localize('low_value_prompt') + '</td>');
                    cellInput = $('<td></td>');
                    cellInput.append(jtf);
                    cellErr = $(`<td id="${err_id}"></td>`);
                    rowLow.append(cellLab,cellInput,cellErr);


                    ////////////////////////////////////////////////////////////
                    // Second row
                    //
                    jtf = $('<input type="text" size="8">');
                    if (rfh.isSetHigh())
                        jtf.val(String(rfh.value_high));

                    err_id = `err_${key}_high`;

                    // Set handler for field changes
                    jtf.on('keyup', null,
                           {rfh: rfh, i: 'value_high', err_id: err_id}, // Event data
                           $.proxy(this.rangeIntegerTextModifiedListener,this));
                    
                    cellLab = $('<td>' + localize('high_value_prompt') + '</td>');
                    cellInput = $('<td></td>');
                    cellInput.append(jtf);
                    cellErr = $('<td id="{0}"></td>'.format(err_id));
                    rowHigh.append(cellLab,cellInput,cellErr);
                    

                    ////////////////////////////////////////////////////////////
                    // Add to group and handlers
                    //
                    group2.append(rowLow, rowHigh);
                    group.append(group2);
                    this.handlers.push(rfh);
                }
                else {
                    /////////////////////
                    // Integer feature //
                    /////////////////////
                    
                    let ifh : IntegerFeatureHandler = null;
                    if (fhs)
                        ifh = fhs[key] as IntegerFeatureHandler; // Use existing feature handler...
                    if (!ifh)
                        ifh = new IntegerFeatureHandler(key);    // ...or create a new one

                    // Append this HTML structure to the variable group:    From these variables
                    // <span>                                                sel
                    //   <input type="radio" ... value="equals">             butEquals
                    //   =
                    //   <input type="radio" ... value="differs">            butDiffers
                    //   ≠
                    // </span>                                               sel
                    // <table>                                               group2
                    //   <tr>                                                row
                    //     <td><input type="text"...></td>                   cell
                    //     <td id="err_id"></td>
                    //   </tr>                                               row
                    //   ...
                    // </table>                                              group2

                    let butEquals  : JQuery = $(`<input type="radio" name="${this.name_prefix}_${key}_comp" value="equals">`);
                    let butDiffers : JQuery = $(`<input type="radio" name="${this.name_prefix}_${key}_comp" value="differs">`);

                    switch (ifh.comparator) {
                    case 'equals':  butEquals.prop('checked',true);  break;
                    case 'differs': butDiffers.prop('checked',true); break;
                    }

                    let sel : JQuery = $('<span></span>');
                    sel.append(butEquals, "=", butDiffers, "&#x2260;");
                    group.append(sel);

                    // Set handler radio button changes
                    sel.on('click', ifh, (e : JQueryEventObject) => {
                        // val() normally returns 'equals' or 'differs', but it may return an empty
                        // value if the user clicks on, say, the = sign.
                        let v = $(e.target).val();
                        switch (v) {
                        case 'equals':
                        case 'differs':
                            e.data.comparator = v; // e.data is ifh
                            this.updateMql();
                            break;
                        }
                    });

                    let group2 : JQuery = $('<table></table>');
                 
                    for (let i=0; i<ifh.values.length; ++i) {
                        let jtf : JQuery = $('<input type="text" size="8">'); // Integer text field
                        if (ifh.values[i])
                            jtf.val(String(ifh.values[i]));

                        let err_id : string = `err_${key}_${i}`;
                        
                        // Set handler for field changes
                        jtf.on('keyup', null,
                               {ifh: ifh, i: i, err_id: err_id}, // Event data
                               $.proxy(this.integerTextModifiedListener,this));

                        let row  : JQuery = $('<tr></tr>');
                        let cell : JQuery = $('<td></td>');
                        cell.append(jtf);
                        row.append(cell);
                        row.append(`<td id="${err_id}"></td>`);
                        group2.append(row);
                    }
                    group.append(group2);
                    this.handlers.push(ifh);
                }
 	    }
 	    else if (valueType==='ascii' || valueType==='string') {
                if (key==='qere_utf8' || key==='qere_translit') { // Hard coded qere feature names for ETCBC4
                    //////////////////
		    // Qere feature //
		    //////////////////
                    
                    let qfh : QereFeatureHandler = null;
                    if (fhs)
                        qfh = <QereFeatureHandler>fhs[key]; // Use existing feature handler...
                    if (!qfh)
                        qfh = new QereFeatureHandler(key);  // ...or create a new one

                    // Append this HTML structure to the variable group:    From these variables
                    // <span>                                                sel
                    //   <input type="checkbox" ... value="omit">            butOmitqere
                    // </span>                                               sel

                    let butOmitqere : JQuery = $(`<input type="checkbox" name="${this.name_prefix}_${key}_sel" value="omit">`);
                    if (qfh.omit)
                        butOmitqere.prop('checked',true);

                    let sel : JQuery = $('<span></span>');
                    sel.append(butOmitqere, localize('omit_qere'));
                    group.append(sel);

                    // Set handler checkbox changes
                    sel.on('click', qfh, (e : JQueryEventObject) => {
                        let target : JQuery = $(e.target);
                        e.data.setValue(target.prop('checked'));  // e.data is qfh
                        this.updateMql();
                    });

                    this.handlers.push(qfh);
                }
                else {
                    ////////////////////
                    // String feature //
                    ////////////////////
                    
                    let sfh : StringFeatureHandler = null;
                    if (fhs)
                        sfh = <StringFeatureHandler>fhs[key]; // Use existing feature handler...
                    if (!sfh)
                        sfh = new StringFeatureHandler(key);  // ...or create a new one

                    // Append this HTML structure to the variable group:    From these variables
                    // <span>                                                sel
                    //   <input type="radio" ... value="equals">             butEquals
                    //   =
                    //   <input type="radio" ... value="differs">            butDiffers
                    //   ≠
                    //   <input type="radio" ... value="differs">            butMatches
                    //   ~
                    // </span>                                               sel
                    // <table>                                               group2
                    //   <tr>                                                row
                    //     <td><input type="text"...></td>                   cell
                    //     <td id="err_id"></td>
                    //   </tr>                                               row
                    //   ...
                    //   <tr><td id="${kbdRowId}" ...></td></tr>             (Only if virtual keyboard is used)
                    // </table>                                              group2

                    let butEquals  : JQuery = $(`<input type="radio" name="${this.name_prefix}_${key}_comp" value="equals">`);
                    let butDiffers : JQuery = $(`<input type="radio" name="${this.name_prefix}_${key}_comp" value="differs">`);
                    let butMatches : JQuery = $(`<input type="radio" name="${this.name_prefix}_${key}_comp" value="matches">`);

                    switch (sfh.comparator) {
                    case 'equals':  butEquals.prop('checked',true);  break;
                    case 'differs': butDiffers.prop('checked',true); break;
                    case 'matches': butMatches.prop('checked',true); break;
                    }

                    let sel : JQuery = $('<span></span>');
                    sel.append(butEquals, '=', butDiffers, '&#x2260;', butMatches, '~');
                    group.append(sel);

                    // Set handler radio button changes
                    sel.on('click', sfh, (e : JQueryEventObject) => {
                        // val() normally returns 'equals', 'differs', or 'matches', but it may
                        // return an empty value if the user clicks on, say, the = sign.
                        let v = $(e.target).val();
                        switch (v) {
                        case 'equals':
                        case 'differs':
                        case 'matches':
                            e.data.comparator = v; // e.data is sfh
                            this.updateMql();
                            break;
                        }
                    });

                    let group2 : JQuery = $('<table></table>');
                    
                    for (let i=0; i<sfh.values.length; ++i) {
                        let jtf : JQuery =
                            featset.foreignText
                            ? $(`<input class="${charset.foreignClass}" type="text" size="20" id="${this.name_prefix}_${key}_input${+i+1}">`)
                            : $('<input type="text" size="20">');
                                
                        if (sfh.values[i])
                                jtf.val(sfh.values[i]);

                        let kbdRowId : string; // ID of row containing virtual keyboard
                        if (featset.foreignText) {
                            kbdRowId = `${this.name_prefix}_${key}_row${+i+1}`;

                            // Move virtual keyboard when an input field gets focus
                            jtf.on('focus', null,
                                   {kbdRowId : kbdRowId, sfh: sfh, i: i}, // Event data
                                   (e : JQueryEventObject) => {
                                       $('#virtualkbid').appendTo('#' + e.data.kbdRowId);
                                       VirtualKeyboard.attachInput(e.currentTarget);
                                       
                                       // Monitor changes to the input field
                                       this.monitorChange($(e.currentTarget), e.data.sfh, e.data.i);
                                   });
                        }

                        // Set handler for direct field changes
                        jtf.on('keyup', null,
                               {sfh: sfh, i: i}, // Event data
                               $.proxy(this.stringTextModifiedListener,this));

                        let row  : JQuery = $('<tr></tr>');
                        let cell : JQuery = $('<td></td>');
                        cell.append(jtf);
                        row.append(cell);
                        group2.append(row);

                        if (featset.foreignText)
                            group2.append(`<tr><td id="${kbdRowId}" style="text-align:right;"></td></tr>`);
                    }
                    group.append(group2);
                    this.handlers.push(sfh);
                }
 	    }
            else if (valueType.substr(0,8)==='list of ') {
                let stripped_valueType : string = valueType.substr(8);
 	        let enumValues = typeinfo.enum2values[stripped_valueType];

                if (!enumValues) {
                    // We cannot handle lists of non-enums
                    console.log('Unknown valueType',valueType);
                }

                let elfh : EnumListFeatureHandler = null;
                if (fhs)
                    elfh = <EnumListFeatureHandler>fhs[key];
                if (!elfh)
                    elfh = new EnumListFeatureHandler(key);
                
                let group_tabs : JQuery = $('<div id="list_tabs_{0}"></div>'.format(key));
                let group_ul : JQuery = $('<ul></ul>');
                group_tabs.append(group_ul);
                
                let tab_labels : string[] = [localize('1st_choice'),
                                             localize('2nd_choice'),
                                             localize('3rd_choice'),
                                             localize('4th_choice')];
                for (let tabno=0; tabno<4; ++tabno) {
                    let lv : ListValuesHandler = elfh.listvalues[tabno];

                    group_ul.append('<li><a href="#tab_{0}_{1}">{2}</li>'.format(key,tabno,tab_labels[tabno]));
                    let tab_contents : JQuery = $('<div id="tab_{0}_{1}"></div>'.format(key,tabno));
                    
                    let vc_choice : PanelForOneVcChoice 
                        = new PanelForOneVcChoice(enumValues,
                                                  stripped_valueType, 
                                                  '{0}_{1}_{2}_{3}'.format(this.name_prefix, otype, key, tabno),
                                                  lv);
                    tab_contents.append(vc_choice.getPanel());
                    group_tabs.append(tab_contents);
		    tab_contents.on('click', lv, (e : JQueryEventObject) => {
                        let target : JQuery = $(e.target);
                        if (target.attr('type')==='radio') {
                            e.data.modifyValue(target.attr('data-name'), target.attr('value')); // e.data is lv
                            this.updateMql();
                        }
                    });
                }
                group.append(group_tabs);
                group.tabs();
                this.handlers.push(elfh);
            }
            else {  // valueType is an enum
 	        let enumValues = typeinfo.enum2values[valueType];

                if (!enumValues) {
                    console.log('Unknown valueType',valueType);
                }
                else {
                    let efh : EnumFeatureHandler = null;
                    if (fhs)
                        efh = <EnumFeatureHandler>fhs[key];
                    if (!efh)
                        efh = new EnumFeatureHandler(key);

                    let butEquals  : JQuery = $('<input type="radio" name="{0}_{1}_comp" value="equals">'
                                                .format(this.name_prefix,key));
		    let butDiffers : JQuery = $('<input type="radio" name="{0}_{1}_comp" value="differs">'
                                                .format(this.name_prefix,key));

                    switch (efh.comparator) {
                    case 'equals': butEquals.prop('checked',true); break;
                    case 'differs': butDiffers.prop('checked',true); break;
                    }

                    let sel : JQuery = $('<span></span>');
                    sel.append(butEquals, '=', butDiffers, '&#x2260;');
                    group.append(sel);

                    sel.on('click', efh, (e : JQueryEventObject) => {
                        // val() may return an empty value if the user clicks on, say, the = sign
                        let v = $(e.target).val();
                        switch (v) {
                        case 'equals':
                        case 'differs':
                            e.data.comparator = v; // e.data is efh
                            this.updateMql();
                            break;
                        }
                    });


                    let checkBoxes : SortingCheckBox[] = [];

                    for (let i = 0; i<enumValues.length; ++i) {
                        let s : string = enumValues[i];

                        let hv : string[] = featset.hideValues;
		        let ov : string[] = featset.otherValues;
                        if ((hv && hv.indexOf(s)!==-1) || ((ov && ov.indexOf(s)!==-1)))
                            continue;

                        let scb = new SortingCheckBox(this.name_prefix + '_' + key, s, getFeatureValueFriendlyName(valueType, s, false, false));
                        if (!efh.values) alert('Assert efh.values failed for type ' + key);
                        scb.setSelected(efh.values && efh.values.indexOf(s)!==-1);
                        checkBoxes.push(scb);
                    }

                    checkBoxes.sort((a : SortingCheckBox, b : SortingCheckBox) => StringWithSort.compare(a.getSws(),b.getSws()));

		    // Decide how many columns and rows to use for feature values
		    let columns : number = 
		        checkBoxes.length>12 ? 3 :
		        checkBoxes.length>4 ? 2 : 1;

                    let rows : number = Math.ceil(checkBoxes.length / columns);

                    let group2 : JQuery = $('<table></table>');
                    for (let r=0; r<rows; ++r) {
                        let rw : JQuery = $('<tr></tr>');
                        for (let c=0; c<columns; ++c) {
                            let cell : JQuery = $('<td></td>');
                            if (c*rows + r < checkBoxes.length)
                                cell.append(checkBoxes[c*rows + r].getJQuery());
                            rw.append(cell);
                        }
                        group2.append(rw);
                    }


		    group2.on('click', efh, (e : JQueryEventObject) => {
                        let target : JQuery = $(e.target);
                        if (target.attr('type')==='checkbox') {
                            // The user clicked on a checkbox
                            if (target.prop('checked'))
                                e.data.addValue(target.attr('value'));  // e.data is efh
                            else
                                e.data.removeValue(target.attr('value'));
                            this.updateMql();
                        }
                    });

                    group.append(group2);
                    this.handlers.push(efh);
 	        }
            }
	    this.fpan.append(group);
 	}

        this.populateFeatureTab(otype);
    }

    protected getOtype() : string {
        return this.objectTypeCombo.val() as string;
    }

    public setOtype(otype : string) {
        this.objectTypeCombo.val(otype);
        this.objectTypeCombo.change();
    }

    public setUsemql() {
        this.rbMql.prop('checked', true);
        this.rbMql.click();
    }
    
    
    // Default value. Overidden in PanelTemplSentenceSelector
    protected getUseForQo() : boolean {
	return false;
    }
	
    public isDirty() : boolean {
	return this.getMql() !== this.txtEntry;
    }
	
    protected makeMql() : string {
        if (this.handlers) {
	    let sb : string = '';
	    let first : boolean = true;

	    for (let i=0; i<this.handlers.length; ++i) {
	        let fh : FeatureHandler = this.handlers[i];

		if (fh.hasValues()) {
		    if (first)
			first = false;
		    else
			sb += ' AND ';
		    sb += fh.toMql();
		}
	    }
            return sb;
        }
        else
            return '';
    }

    protected switchToMql(useMql : boolean) : void {
        alert('Abstract function switchToMql() called');
    }
	
    protected updateMql() : void {
 	this.setMql(this.makeMql());
    }


    protected populateFeatureTab(otype: string) : void {
        alert('Abstract function populateFeatureTab() called');
    }


    public getInfo() : MqlData {
        let res : MqlData = {
            object   : this.getOtype(),
            mql      : null,
            featHand : { vhand: null },
            useForQo : this.getUseForQo()
        };

        if (this.rbMql.prop('checked'))
            res.mql = this.getMql();
        else {
            res.featHand.vhand = [];

            for (let i=0; i<this.handlers.length; ++i) {
	        let fh : FeatureHandler = this.handlers[i];

		if (fh.hasValues())
                    res.featHand.vhand.push(fh);
            }
        }

        return res;
    }
}
