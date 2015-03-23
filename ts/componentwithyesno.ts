// -*- js -*-
/// @file
/// @brief Contains the ComponentWithYesNo class and the COMPONENT_TYPE enum.


/// The types of input field that can be associated with a correct/wrong indication.
enum COMPONENT_TYPE {
    textField,
    textFieldWithVirtKeyboard,
    translatedField,
    comboBox1, // An ordinary combobox (for Latin text)
    comboBox2, // A styled combobox (for Hebrew text)
}

declare var site_url : string; ///< The main URL of the website. Used for generating links to images.

class ComponentWithYesNo {
    private elem : JQuery;             ///< The component displayed with this object.
    private elemType : COMPONENT_TYPE; ///< The type of the element handled by this object

    private yesIcon : JQuery;          ///< The "correct answer" icon.
    private noIcon : JQuery;           ///< The "wrong answer" icon.
    private noneIcon : JQuery;         ///< An empty icon.

    /// Creates a ComponentWithYesNo containing a specified component.
    /// @param elem The component to display.
    /// @param elemType The type of the component to display.
    constructor(elem : JQuery, elemType : COMPONENT_TYPE) {
        this.elem = elem;
        this.elemType = elemType;
        this.yesIcon = $('<img src="' + site_url + '/images/ok.png" alt="Yes">');
        this.noIcon = $('<img src="' + site_url + '/images/notok.png" alt="No">');
        this.noneIcon = $('<img src="' + site_url + '/images/none.png" alt="None">');

    }

    public appendMeTo(dest : JQuery) : JQuery {
        var spn = $('<span style="white-space:nowrap;"></span>').append(this.yesIcon).append(this.noIcon).append(this.noneIcon).append(this.elem);
        dest.append(spn);
        this.setNone();
        return dest;
    }


    // Handles changes to input fields under virtual keyboard control
    private static intervalHandler : number;
    private static monitorOrigVal : string;
    private static lastMonitored : string;
    private static monitorChange(elem : JQuery, me : ComponentWithYesNo) {
        clearInterval(ComponentWithYesNo.intervalHandler);

        if (ComponentWithYesNo.lastMonitored !== elem.data('kbid')) { // A new element has focus
            ComponentWithYesNo.monitorOrigVal = elem.val();
            ComponentWithYesNo.lastMonitored = elem.data('kbid');
        }

        // Closure around polling function
        function timedfun(elem2 : JQuery, me2 : ComponentWithYesNo) {
            return () => {
                var s : string = elem2.val();
                if (s!==ComponentWithYesNo.monitorOrigVal) {
                    ComponentWithYesNo.monitorOrigVal = s;
                    me2.setNone();
                }
            };
        }

        ComponentWithYesNo.intervalHandler = setInterval(timedfun(elem,me), 500);
    }


    public addChangeListener() : void {
        this.elem.on('change', () => this.setNone());
    }

    // Check for keypress and paste event
    public addKeypressListener() : void {
        // TODO: Can all of this be changed to on('input', ...)?
        this.elem.on('paste cut', (e1 : JQueryEventObject) => this.setNone());

        // Note: Firefox sends keypress event on arrows and CTRL-C, Chrome and IE do not
        this.elem.on('keypress', (e1 : JQueryEventObject) => this.setNone())
            .on('keydown', (e2 : JQueryEventObject) => {
                if (e2.keyCode==8 /* Backspace */ || e2.keyCode==46 /* Del */)
                    this.elem.trigger('keypress');
            }); /* Ensure that backspace and del trigger keypress - they don't normally on Chrome */

        if (this.elemType===COMPONENT_TYPE.textFieldWithVirtKeyboard) {
            // We must do continuous polling of changes
            this.elem.on('focus',
                         (e : JQueryEventObject) => ComponentWithYesNo.monitorChange($(e.currentTarget), this));
        }
    }

    /// Gets the contained component.
    /// @return The component displayed with this object.
    public getComp() : JQuery {
        if (this.elemType===COMPONENT_TYPE.comboBox2)
            return $(this.elem.children()[0]); // A comboBox2 is a <div> containing a <select>. We return the <select>.
        else
            return this.elem;
    }

    /// Gets the type of the component.
    /// @return The type of the component.
    public getCompType() : COMPONENT_TYPE {
        return this.elemType;
    }

    /// Sets the icon to indicate the correctness of an answer.
    /// @param yes Is the answer correct?
    public setYesNo(yes : boolean) : void {
        if (ComponentWithYesNo.lastMonitored === this.elem.data('kbid'))
            ComponentWithYesNo.monitorOrigVal = this.elem.val(); // Lest the polling detects the change and removes the yes/no mark

        if (yes) {
            this.yesIcon.show();
            this.noIcon.hide();
        }
        else {
            this.yesIcon.hide();
            this.noIcon.show();
        }
        this.noneIcon.hide();
    }

    /// Displays an empty icon.
    public setNone() : void {
        this.yesIcon.hide();
        this.noIcon.hide();
        this.noneIcon.show();
    }
}
