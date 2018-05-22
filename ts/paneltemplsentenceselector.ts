// -*- js -*-
/* Copyright 2013 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk */

// This fixes an error in @types/jqueryui:

interface JQuery {
    tabs(methodName: 'enable', index: number): void;
    tabs(methodName: 'disable', index: number): void;
}



class PanelTemplSentenceSelector extends PanelTemplMql {
    private cbUseForQo : JQuery;
    private cbUseForQoLabel : JQuery;
    private templTabs : JQuery;
    private questObjTypeLab : JQuery = $('<span>' + localize('sentence_unit_type_prompt') + '</span>');
    private featSelLab : JQuery = $('<span>' + localize('feature_prompt') + '</span>');
    private importShebanq : JQuery = $('<button type="button">' + localize('import_shebanq') + '</button>');
    private dirty : boolean;
    private featureTab : PanelTemplQuizFeatures;
    private qoselTab : PanelTemplQuizObjectSelector;
    
    public switchToMql(useMql : boolean) : void {
        this.mqlText.prop('disabled', !useMql);
	this.objectTypeCombo.prop('disabled', useMql);
	this.featureCombo.prop('disabled', useMql);
        this.cbUseForQo.prop('disabled', useMql);

        if (useMql) {
	    this.questObjTypeLab.addClass('disabled');
	    this.featSelLab.addClass('disabled');
            this.cbUseForQoLabel.addClass('disabled');
            this.cbUseForQo.prop('checked', false);
            this.templTabs.tabs('enable',3);
            this.importShebanq.prop('disabled',false);
        }
        else {
	    this.questObjTypeLab.removeClass('disabled');
	    this.featSelLab.removeClass('disabled');
	    this.cbUseForQoLabel.removeClass('disabled');
            this.importShebanq.prop('disabled',true);
        }

	if (this.currentBox) {
            if (useMql)
		this.currentBox.hide();
            else
		this.currentBox.show();
        }

        this.populateFeatureTab(null);
    }

    public makeMql() : string {
        return '[' + this.getOtype() + ' NORETRIEVE ' + super.makeMql() + ']';
    }
    
    public getMqlEmulQos() : string {
    	return super.makeMql();
    }

    public getUseForQo() : boolean {
        return this.cbUseForQo.prop('checked');
    }
    
    public isDirty() : boolean {
	return super.isDirty() || this.dirty;
    }
 
    private doLayout(where : JQuery) : void {
        var table : JQuery = $('<table></table>');
        var row : JQuery;
        var cell : JQuery;

        row = $('<tr></tr>');
        cell = $('<td colspan="2"></td>');
        cell.append(this.cbUseForQo, this.cbUseForQoLabel);
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
        cell = $('<td></td>');
        row.append(cell);
        cell = $('<td></td>');
        cell.append(this.importShebanq);
        row.append(cell);
        table.append(row);

        row = $('<tr></tr>');
        cell = $('<td colspan="2"></td>');
        cell.append(this.rbFriendly, this.rbFriendlyLabel);
        row.append(cell);
        table.append(row);

        row = $('<tr></tr>');
        cell = $('<td></td>');
       
        cell.append(this.questObjTypeLab);
        row.append(cell);
        
        cell = $('<td></td>');
        cell.append(this.objectTypeCombo);
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
    constructor(md : MqlData, ttabs : JQuery, where : JQuery,
                qoselTab : PanelTemplQuizObjectSelector, featureTab : PanelTemplQuizFeatures) {
	super(md, 'sensel');
	this.templTabs = ttabs;
	this.dirty = false;
        this.featureTab = featureTab;
        this.qoselTab = qoselTab;

        this.cbUseForQo = $('<input type="checkbox" name="useforqol">');
        this.cbUseForQoLabel = $('<span>' + localize('use_for_qosel') + '</span>');

	this.cbUseForQo.click(() => {
            if (this.cbUseForQo.is(':checked'))
                this.templTabs.tabs('disable',3);
            else
                this.templTabs.tabs('enable',3);

            this.populateFeatureTab(null);
            this.dirty = true;
        });

        this.rbMqlLabel = $('<span>' + localize('mql_qosel_prompt') + '</span>');
        this.rbFriendlyLabel = $('<span>' + localize('friendly_featsel_prompt') + '</span>');

        this.doLayout(where);

	if (this.initialMd==null || this.initialMd.useForQo) {
            this.cbUseForQo.prop('checked',true);
            this.templTabs.tabs('disable',3);
        }
        else {
            this.cbUseForQo.prop('checked',false);
            this.templTabs.tabs('enable',3);
        }

	this.importShebanq.click(import_from_shebanq);
        this.finish_construct();
    }

    public populateFeatureTab(otype: string) : void {
        if (this.cbUseForQo.prop('checked')) {
            if (otype === null)
                otype = this.getOtype();
            this.featureTab.populate(otype);
        }
        else
            this.qoselTab.populateFeatureTab(null);
    }
}
