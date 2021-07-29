// -*- js -*-
// Copyright © 2018 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk

// 2 Nov 2020
// Changes by Ernst Boogert (Booge IT) in getJQuery() method

// Contains code for handling components with an icon indicating answer correctness.


//****************************************************************************************************
// COMPONENT_TYPE enum
//
// Identifies the types of input field that can be associated with a correct/wrong indication.
//
enum COMPONENT_TYPE {
    textField,                 // Text field
    textFieldWithVirtKeyboard, // Text field with virtual keyboard
    comboBox1,                 // An ordinary combobox (for Latin text)
    comboBox2,                 // A styled combobox (for Hebrew text)
    checkBoxes,                // A collection of several checkboxes
}

//****************************************************************************************************
// site_url variable
//
// The main URL of the website. Used for generating links to images.
//
declare let site_url : string;


//****************************************************************************************************
// ComponentWithYesNo class
//
// This class generates HTML for an input field associated with an icon for indicating a correct or
// wrong answer. The generated HTML looks like this:
//
//   <td>
//     <span style="white-space:nowrap;">
//       <img src=".../images/ok.png" alt="Yes">            <!-- 'Correct' icon -->
//       <img src=".../images/notok.png" alt="No">          <!-- 'Wrong' icon -->
//       <img src=".../images/none.png" alt="None">         <!-- No icon -->
//       INPUT ELEMENT
//     </span>
//   </td>
//
// where the INPUT ELEMENT depends on the specified COMPONENT_TYPE. The INPUT ELEMENT is provided by
// the class user.
//
// The class also handles listening for input and displaying of appropriate icons.
//
class ComponentWithYesNo {
    private elem     : JQuery;         // The INPUT ELEMENT displayed with this object.
    private elemType : COMPONENT_TYPE; // The type of the element handled by this object

    private yesIcon  : JQuery;         // The "correct answer" icon.
    private noIcon   : JQuery;         // The "wrong answer" icon.
    private noneIcon : JQuery;         // An empty icon.

    //------------------------------------------------------------------------------------------
    // Constructor method
    //
    // Parameters:
    //     elem: The component to display. (The INPUT ELEMENT.)
    //     elemType: The type of the component to display.
    //
    constructor(elem : JQuery, elemType : COMPONENT_TYPE) {
        this.elem     = elem;
        this.elemType = elemType;
        this.yesIcon  = $(`<img src="${site_url}/images/ok.png" alt="Yes">`);
        this.noIcon   = $(`<img src="${site_url}/images/notok.png" alt="No">`);
        this.noneIcon = $(`<img src="${site_url}/images/none.png" alt="None">`);
    }

    //------------------------------------------------------------------------------------------
    // getJQuery method
    //
    // Constructs HTML for the input element and the icons and wraps it in <td> and <span> elements.
    //
    // Returns:
    //     The complete HTML for the component.
    //
    public getJQuery() : JQuery {
        let spn = $('<td class="combobox"></td>').append([this.yesIcon,this.noIcon,this.noneIcon,this.elem]);
        this.setNone();

        return spn;
    }


    // The following code and fields handle changes to input fields under virtual keyboard control.
    // These fields are static, because there is only ever one monitored field, namely the field
    // with focus.

    private static intervalHandler : number; // Polling interval handler
    private static monitorOrigVal  : string; // Value of monitored element before change
    private static lastMonitored   : string; // Last monitored element

    //------------------------------------------------------------------------------------------
    // monitorChange method
    //
    // Called when a text field controlled by a virtual keyboad get focus. The function polls the
    // value of the text field and clears the icon when the value is changed.
    //
    // Parameters:
    //     elem: Text input element to monitor
    //
    private monitorChange(elem : JQuery) {
        clearInterval(ComponentWithYesNo.intervalHandler);
        
        if (ComponentWithYesNo.lastMonitored !== elem.data('kbid')) { // A new element has focus
            ComponentWithYesNo.monitorOrigVal = elem.val() as string;
            ComponentWithYesNo.lastMonitored = elem.data('kbid');
        }

        // Closure around polling function
        function timedfun(elem2 : JQuery, me : ComponentWithYesNo) {
            return () => {
                let s : string = elem2.val() as string;
                if (s!==ComponentWithYesNo.monitorOrigVal) { // Component value changed
                    ComponentWithYesNo.monitorOrigVal = s;   // Update copy of value
                    me.setNone();                            // Show no icon
                }
            };
        }

        ComponentWithYesNo.intervalHandler = setInterval(timedfun(elem,this), 500);
    }


    public addChangeListener() : void {
        this.elem.on('change', () => this.setNone());
    }

    //------------------------------------------------------------------------------------------
    // addKeypressListener method
    //
    // Listen for input events. When an input field is changed, clear the icon and the background color.
    //
    public addKeypressListener() : void {
        this.elem.on('input', (e1 : JQueryEventObject) => this.setNone());

        // Listen for focus events on fields with virtual keyboard
        if (this.elemType===COMPONENT_TYPE.textFieldWithVirtKeyboard) {
            // We must do continuous polling of changes
            this.elem.on('focus',
                         (e : JQueryEventObject) => this.monitorChange(<JQuery<HTMLElement>>$(e.currentTarget)));
        }
    }

    //------------------------------------------------------------------------------------------
    // getComp method
    //
    // Returns the component displayed with this object.
    //
    public getComp() : JQuery {
//        if (this.elemType === COMPONENT_TYPE.comboBox2)
//            return $(this.elem.children()[0]); // A comboBox2 is a <div> containing a <select>. We return the <select>.
//        else
            return this.elem;
    }

    //------------------------------------------------------------------------------------------
    // getCompType method
    //
    // Returns the type of the component.
    //
    public getCompType() : COMPONENT_TYPE {
        return this.elemType;
    }

    //------------------------------------------------------------------------------------------
    // setYesNot method
    //
    // Sets the icon to indicate the correctness of an answer.
    //
    // Parameter:
    //     yes: Is the answer correct?
    //
    public setYesNo(yes : boolean) : void {
        if (ComponentWithYesNo.lastMonitored === this.elem.data('kbid'))
            ComponentWithYesNo.monitorOrigVal = this.elem.val() as string; // Lest the polling detects the change and removes the yes/no mark

        if (yes) {
            $(this.elem).css({
                "background-color": "rgba(67, 176, 42, 0.1)",
                "outline": "solid 2px rgba(67, 176, 42, 1.0)",
            }) 
            this.yesIcon.show();
            this.noIcon.hide();
        }
        else {
            $(this.elem).css({
                "background-color": "rgba(195, 92, 244, 0.1)",
                "outline": "solid 2px rgba(195, 92, 244, 1.0)",
            })
            this.yesIcon.hide();
            this.noIcon.show();
        }
        this.noneIcon.hide();
    }

    //------------------------------------------------------------------------------------------
    // setNone method
    //
    // Displays an empty icon.
    //
    public setNone() : void {
        this.yesIcon.hide();
        this.noIcon.hide();
        this.noneIcon.show();
        this.elem.css({
            "background-color": "",
            "outline": "",
        });
    }
}
