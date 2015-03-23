// -*- js -*-
/* Copyright 2013 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk */

enum VerbClassSelection { YES, NO, DONT_CARE };

class VerbClassButtonsAndLabel {
    private yes      : JQuery;
    private no       : JQuery;
    private dontcare : JQuery;
    private label    : JQuery;

    constructor(lab      : string,
                name     : string,
                dataName : string,
                select   : VerbClassSelection) {

        this.yes      = $('<input type="radio" name="{0}" value="yes" data-name="{1}">'.format(name,dataName));
        this.no       = $('<input type="radio" name="{0}" value="no" data-name="{1}">'.format(name,dataName));
        this.dontcare = $('<input type="radio" name="{0}" value="dontcare" data-name="{1}">'.format(name,dataName));
        this.label    = $('<span>{0}</span>'.format(lab));
        
        switch (select) {
        case VerbClassSelection.YES:        this.yes.prop('checked',true);      break;
        case VerbClassSelection.NO:         this.no.prop('checked',true);       break;
        case VerbClassSelection.DONT_CARE:  this.dontcare.prop('checked',true); break;
        }
    }

    public getRow() : JQuery {
        var row : JQuery = $('<tr></tr>');
        var cell : JQuery;

        cell = $('<td></td>');
        cell.append(this.yes);
        row.append(cell);

        cell = $('<td></td>');
        cell.append(this.no);
        row.append(cell);

        cell = $('<td></td>');
        cell.append(this.dontcare);
        row.append(cell);

        cell = $('<td class="leftalign"></td>');
        cell.append(this.label);
        row.append(cell);

        return row;
    }

//    public isSelected_showFeat() : boolean {
//        if (this.canShow)
//            return this.showFeat.prop('checked');
//        else
//            return false;
//    }
// 
//    public isSelected_reqFeat() : boolean {
//        if (this.canRequest)
//            return this.reqFeat.prop('checked');
//        else
//            return false;
//    }
// 
//    public isSelected_dontShowFeat() : boolean {
//        if (this.canDisplayGrammar)
//            return this.dontShowFeat.prop('checked');
//        else
//            return false;
//    }
// 
//    public isSelected_ddCheck() : boolean {
//        if (this.useDropDown)
//            return this.ddCheck.prop('checked');
//        else
//            return false;
//    }
// 
//    public getFeatName() : string {
//        return this.featName;
//    }
}

class PanelForOneVcChoice  {
    public allBAL    : VerbClassButtonsAndLabel[] = [];
    private panel    : JQuery = $('<table class="striped featuretable"></table>');
     
    constructor(enumValues : any, valueType: string, prefix: string, lv: ListValuesHandler) {
        this.panel.append("<tr><th>Yes</th><th>No</th><th>Don't care</th><th class=\"leftalign\">Verb class</th></tr>");

        // Next, loop through the keys in the sorted order
        for (var ix=0; ix<enumValues.length; ++ix) {
            var vc : string = enumValues[ix];
            var vcsel : VerbClassSelection = VerbClassSelection.DONT_CARE;

            if (lv.yes_values.indexOf(vc)!=-1)
                vcsel = VerbClassSelection.YES;
            else if (lv.no_values.indexOf(vc)!=-1)
                vcsel = VerbClassSelection.NO;

            var bal = new VerbClassButtonsAndLabel(getFeatureValueFriendlyName(valueType,vc,false),
                                                   '{0}_{1}'.format(prefix,vc),
                                                   vc,
                                                   vcsel);
            this.allBAL.push(bal);
            this.panel.append(bal.getRow());
         }
    }

    public getPanel() : JQuery {
        return this.panel;
    }
}



 
