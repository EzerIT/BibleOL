// -*- js -*-

class SortingCheckBox {
    /** The underlying {@link StringWithSort} object. */
    private sws : StringWithSort;
    private checkbox : JQuery;
    private jq : JQuery;


    /** Creates an initially unselected check box with text.
     * @param text The text to display, optionally starting with '#' followed by a sort index.
     * @see StringWithSort
     */
    constructor(name : string, value : string, text : string) {
        this.sws = new StringWithSort(text);

        this.checkbox = $('<input type="checkbox" name="{0}" value="{1}">'.format(name,value));
        this.jq = $('<span></span>');
        this.jq.append(this.checkbox, this.sws.getString());
    }

    public setSelected(selected : boolean) {
	this.checkbox.prop('checked',selected);
    }

    public getSws() : StringWithSort {
        return this.sws;
    }

    public getJQuery() : JQuery {
        return this.jq;
    }
}
