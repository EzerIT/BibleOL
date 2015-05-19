// -*- js -*-
/* Copyright 2013 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk */

class PanelTemplQuizObjectSelector extends PanelTemplMql {
    private featSelLab : JQuery = $('<span>' + localize('feature_prompt') + '</span>');
    private featureTab : PanelTemplQuizFeatures;

    public switchToMql(useMql : boolean) : void {
        this.mqlText.prop('disabled', !useMql);
	this.featureCombo.prop('disabled', useMql);

        if (useMql)
	    this.featSelLab.addClass('disabled');
        else
	    this.featSelLab.removeClass('disabled');

	if (this.currentBox) {
            if (useMql)
		this.currentBox.hide();
            else
		this.currentBox.show();
        }
    }

    private doLayout(where : JQuery) : void {
        var table : JQuery = $('<table></table>');
        var row : JQuery;
        var cell : JQuery;

        row = $('<tr></tr>');
        cell = $('<td>' + localize('sentence_unit_type_prompt') + '</td>');
        row.append(cell);

        cell = $('<td></td>');
        cell.append(this.objectTypeCombo);
        row.append(cell);
        table.append(row);


        row = $('<tr></tr>');
        cell = $('<td></td>');
        cell.append(this.rbMql, this.rbMqlLabel);
        row.append(cell);
        
        cell = $('<td></td>');
        cell.append(this.mqlText);
        row.append(cell);
        table.append(row);
        
        row = $('<tr></tr>');
        cell = $('<td colspan="2"></td>');
        cell.append(this.rbFriendly, this.rbFriendlyLabel);
        row.append(cell);
        table.append(row);

        row = $('<tr></tr>');
        cell = $('<td></td>');
       
        cell.append(this.featSelLab);
        row.append(cell);
        
        cell = $('<td></td>');
        cell.append(this.featureCombo);
        row.append(cell);
        table.append(row);
        
        row = $('<tr></tr>');
        cell = $('<td id="clearbuttoncell"></td>');
       
        cell.append(this.clear);
        row.append(cell);
        
        cell = $('<td></td>');
        cell.append(this.fpan);
        row.append(cell);
        table.append(row);
        
        where.append(table);
    }

    /**
     * Constructor.
     */
    constructor(md : MqlData, where : JQuery, featureTab : PanelTemplQuizFeatures) {
        super(md,'qosel');
        this.featureTab = featureTab;

        this.rbMqlLabel = $('<span>' + localize('mql_featsel_prompt') + '</span>');
        this.rbFriendlyLabel = $('<span>' + localize('friendly_featsel_prompt') + '</span>');

        this.doLayout(where);

        this.finish_construct();
    }

    public populateFeatureTab(otype : string) : void {
        if (otype === null)
            otype = this.getOtype();
        this.featureTab.populate(otype);
    }
}
