// -*- js -*-
interface MqlData {
    object : string;
    mql : string;
    featHand : { vhand: FeatureHandler[]; }
    useForQo : boolean;
}

class FeatureHandler {
    public type : string;
    public name : string;
    public comparator : string; // Not used in all subclasses, but included here for the sake of a couple of methods

    constructor(typ : string, key : string) {
        this.type = typ;
        this.name = key;
        this.comparator = 'equals';
    }

    public normalize() : void {
        // Nothing
    }

    public hasValues() : boolean {
        alert('Abstract function hasValues() called');
        return false;
    }

    public toMql() : string {
        alert('Abstract function toMql() called');
        return '';
    }

    public getComparator() : string {
        switch (this.comparator) {
          case 'equals': return '=';
          case 'differs': return '<>';
          case 'matches': return '~';
        }
        return '';
    }

    public getJoiner() : string {
        switch (this.comparator) {
          case 'equals': return ' OR ';
          case 'differs': return ' AND ';
          case 'matches': return ' OR ';
        }
        return '';
    }
}

class StringFeatureHandler extends FeatureHandler {
    public values : string[];

    constructor(key : string) {
        super('stringfeature', key);
        this.values = [];
        this.normalize();
    }

    public normalize() : void {
        while (this.values.length<4)
            this.values.push(null);
    }

    public setValue(index : number, val : string) : void {
        this.values[index] = val;
    }

    public removeValue(index : number) : void {
        this.values[index] = null;
    }

    public hasValues() : boolean {
        for (var i=0; i<this.values.length; ++i)
            if (this.values[i]!==null)
                return true;

	return false;
    }

    public toMql() : string {
        var comparator : string = this.getComparator();
        var values : string[] = [];

	for (var i=0; i<this.values.length; ++i)
            if (this.values[i]!==null)
                values.push(this.name + comparator + '"' + this.values[i] + '"');


	if (values.length===1)
	    return values[0];

	return '(' + values.join(this.getJoiner()) + ')';
    }
}

class IntegerFeatureHandler extends FeatureHandler {
    public values : number[];

    constructor(key : string) {
        super('integerfeature', key);
        this.values = [];
        this.normalize();
    }

    public normalize() : void {
        while (this.values.length<4)
            this.values.push(null);
    }

    public setValue(index : number, val : number) : void {
        this.values[index] = val;
    }

    public removeValue(index : number) : void {
        this.values[index] = null;
    }

    public hasValues() : boolean {
        for (var i=0; i<this.values.length; ++i)
            if (this.values[i]!==null)
                return true;

	return false;
    }

    public toMql() : string {
        var values : number[] = [];

	for (var i=0; i<this.values.length; ++i)
            if (this.values[i]!==null)
                values.push(this.values[i]);

	if (values.length===1)
	    return this.name + this.getComparator() + values[0];
        else
	    return (this.comparator==='differs' ? 'NOT ' : '')
		+ this.name + ' IN (' + values.join(',') + ')';
    }
}

class RangeIntegerFeatureHandler extends FeatureHandler {
    public value_low : number;
    public value_high : number;

    constructor(key : string) {
        super('rangeintegerfeature', key);
    }

    public isSetLow() : boolean {
        return this.value_low!==null && this.value_low!==undefined;
    }

    public isSetHigh() : boolean {
        return this.value_high!==null && this.value_high!==undefined;
    }

    public hasValues() : boolean {
        return this.isSetLow() || this.isSetHigh();
    }

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

class EnumFeatureHandler extends FeatureHandler {
    public values : string[];

    constructor(key : string) {
        super('enumfeature', key);
        this.values = [];
    }

    public addValue(val : string) : void {
        this.values.push(val);
    }

    public removeValue(val : string) : void {
        var index = this.values.indexOf(val);

        if (index > -1)
            this.values.splice(index, 1);
    }

    public hasValues() : boolean {
        return this.values.length > 0;
    }

    public toMql() : string {
        return (this.comparator==='differs' ? 'NOT ' : '')
            + this.name + ' IN (' + this.values.join(',') + ')';
    }
}

class EnumListFeatureHandler extends FeatureHandler {
    public listvalues : ListValuesHandler[];

    constructor(key : string) {
        super('enumlistfeature', key);
        this.listvalues = [];
        this.normalize();
    }

    public normalize() : void {
        while (this.listvalues.length<4)
            this.listvalues.push(new ListValuesHandler());
    }

    public hasValues() : boolean {
        for (var i=0; i<this.listvalues.length; ++i)
            if (this.listvalues[i].hasValues())
                return true;

        return false;
    }

    public toMql() : string {
        if (this.listvalues.length>0) {
	    var sb : string = '(';
	    var first : boolean = true;

	    for (var i=0; i<this.listvalues.length; ++i) {
	        var lvh : ListValuesHandler = this.listvalues[i];

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

class ListValuesHandler {
    public type : string;
    public yes_values : string[];
    public no_values : string[];

    constructor() {
        this.type = 'listvalues';
        this.yes_values = [];
        this.no_values = [];
    }
    

    public modifyValue(name : string, val : string) {
        var yes_index = this.yes_values.indexOf(name);
        var no_index = this.no_values.indexOf(name);

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

    public hasValues() : boolean {
        return this.yes_values.length + this.no_values.length > 0;
    }

    public toMql(featName : string) : string {
        var stringValues : string[] = [];

        for (var ix=0; ix<this.yes_values.length; ++ix)
            stringValues.push('{0} HAS {1}'.format(featName, this.yes_values[+ix]));

        for (var ix=0; ix<this.no_values.length; ++ix)
            stringValues.push('NOT {0} HAS {1}'.format(featName, this.no_values[+ix]));

        if (stringValues.length===1)
            return stringValues[0];

	return '(' + stringValues.join(' AND ') + ')';
    }
}

class PanelTemplMql {
    public currentBox : JQuery;
    public featureCombo : JQuery = $('<select></select>');
    public fpan : JQuery; // Small panel to contain feature selectors
    public mqlText : JQuery;
    public objectTypeCombo : JQuery = $('<select></select>');
    public rbFriendly : JQuery;
    public rbFriendlyLabel : JQuery;
    public rbMql : JQuery;
    public rbMqlLabel : JQuery;
    public handlers : FeatureHandler[];
    public clear : JQuery;
    public initialMd : MqlData;
    public txtEntry : string;
    public groups : JQuery[];
    private name_prefix : string;  // Used as prefix for HTML element names

    public stringTextModifiedListener(e : JQueryEventObject) : void
    {
        var s : string = $(e.currentTarget).val();
        if (s.length===0)
            (<StringFeatureHandler>e.data.sfh).removeValue(e.data.i);
        else
            (<StringFeatureHandler>e.data.sfh).setValue(e.data.i, s);
        this.updateMql();
    }

    public integerTextModifiedListener(e : JQueryEventObject) : void
    {
        var s : string = $(e.currentTarget).val();
        $('#' + e.data.err_id).html('');
        if (s.length===0)
            (<IntegerFeatureHandler>e.data.ifh).removeValue(e.data.i);
        else {
            if (s.match(/\D/g)!==null) // Note: Rejects minus sign
                $('#' + e.data.err_id).html(localize('not_integer'));
            else
                (<IntegerFeatureHandler>e.data.ifh).setValue(e.data.i, +s);
        }
        this.updateMql();
    }

    public rangeIntegerTextModifiedListener(e : JQueryEventObject) : void
    {
        var s : string = $(e.currentTarget).val();
        $('#' + e.data.err_id).html('');
        if (s.length===0)
            (<RangeIntegerFeatureHandler>e.data.rfh)[e.data.i] = null;
        else {
            if (s.match(/\D/g)!==null) // Note: Rejects minus sign
                $('#' + e.data.err_id).html(localize('not_integer'));
            else
                (<RangeIntegerFeatureHandler>e.data.rfh)[e.data.i] = +s;
        }
        this.updateMql();
    }


    constructor(md : MqlData, name_prefix : string) {
        this.initialMd = md;
        this.name_prefix = name_prefix;

        this.fpan = $('<div id="{0}_fpan"></div>'.format(this.name_prefix));


        if (md.featHand) {
            for (var i=0; i<md.featHand.vhand.length; ++i) {
                var vh : FeatureHandler = md.featHand.vhand[i];
                switch (vh.type) {
                case 'enumfeature':
                    addMethods(vh, EnumFeatureHandler, null);
                    break;

                case 'enumlistfeature':
                    addMethods(vh, EnumListFeatureHandler, null);
                    var elfh : EnumListFeatureHandler = <EnumListFeatureHandler>vh;
                    for (var j=0; j < elfh.listvalues.length; ++j)
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
                }
                vh.normalize();

                // Turn vhand into an associative array:
                md.featHand.vhand[vh.name] = vh;
            }
        }

        this.rbMql = $('<input type="radio" name="{0}_usemql" value="yes">'.format(this.name_prefix));
        this.rbMql.click(() => {
            if (this.rbMql.is(':checked'))
 		this.switchToMql(true);
        });

        this.rbFriendly = $('<input type="radio" name="{0}_usemql" value="no">'.format(this.name_prefix));
	this.rbFriendly.click(() => {
            if (this.rbFriendly.is(':checked')) {
 		this.switchToMql(false);
                this.updateMql();
            }
        });

	this.rbMqlLabel = $('<span>YOU SHOULD NOT SEE THIS</span>');
	this.rbFriendlyLabel = $('<span>YOU SHOULD NOT SEE THIS</span>');

        this.mqlText = $('<textarea id="{0}_mqltext" cols="45" rows="2">'.format(this.name_prefix));


        this.featureCombo.on('change', () => {
            this.currentBox.hide();
            this.currentBox = this.groups[this.featureCombo.val()];
            this.currentBox.show();
        });


        var setObject : string = (md!=null && md.object!=null) ? md.object : configuration.objHasSurface;
        for (var s in configuration.objectSettings) {
            if (configuration.objectSettings[s].mayselect) {
                var selectString = s===setObject ? 'selected="selected"' : '';
                var resetString = s===configuration.objHasSurface ? 'data-reset="yes"' : '';
                this.objectTypeCombo.append('<option value="{0}" {1} {2}>{3}</option>'
                                            .format(s, selectString, resetString, getObjectFriendlyName(s)));
            }
        }

        this.objectTypeCombo.on('change', () => {
            $('#virtualkbid').appendTo('#virtualkbcontainer'); // Move the keyboard back to its initial position

            this.fpan.html('<div id="{0}_fpan"></div>'.format(this.name_prefix));
            this.currentBox = null
            this.featureCombo.html('<select></select>');
            this.objectSelectionUpdated(this.objectTypeCombo.val(), null)
            this.updateMql();
        });


        this.clear = $('<button id="clear_button" type="button">' + localize('clear_button') + '</button>');
        //this.clear.button(); Don't use this. The JQuery UI theme works for buttons but not dropdowns

        this.clear.click(() => {
            $('#virtualkbid').appendTo('#virtualkbcontainer'); // Move the keyboard back to its initial position

	    this.rbFriendly.prop('checked',true);
	    this.objectTypeCombo.find(':selected').prop('selected',false);
            this.objectTypeCombo.find('[data-reset]').prop('selected',true);
            this.fpan.html('<div id="{0}_fpan"></div>'.format(this.name_prefix));
	    this.currentBox = null;
            this.featureCombo.html('<select></select>');
	    this.objectSelectionUpdated(configuration.objHasSurface,null);
	    this.updateMql();
	    this.switchToMql(false);		
	});
    }

    public finish_construct() : void {
	if (this.initialMd==null) {
            this.rbFriendly.prop('checked',true);
	    this.objectSelectionUpdated(configuration.objHasSurface,null);
	    this.updateMql();
	    this.switchToMql(false);		
	}
	else if (this.initialMd.mql!=null) {
            this.rbMql.prop('checked',true);
	    this.mqlText.html(this.initialMd.mql);
            if (this.initialMd.featHand)
	        this.objectSelectionUpdated(this.initialMd.object,this.initialMd.featHand.vhand);
            else 
	        this.objectSelectionUpdated(this.initialMd.object,null);
	    this.switchToMql(true);
	}
	else {
            this.rbFriendly.prop('checked',true);
	    this.objectSelectionUpdated(this.initialMd.object,this.initialMd.featHand.vhand);
	    this.updateMql();
	    this.switchToMql(false);
	}
 	this.txtEntry = this.getMql();
    }


    public setMql(s : string) {
        this.mqlText.val(s);
    }

    public getMql() : string {
        return this.mqlText.val();
    }


    // Handles changes to input fields under virtual keyboard control
    private intervalHandler : number;
    private monitorOrigVal : string;
    private lastMonitored : string;

    private monitorChange(elem : JQuery, sfh : StringFeatureHandler, i : number) {
        clearInterval(this.intervalHandler);

        if (this.lastMonitored !== elem.attr('id')) { // A new element has focus
            this.monitorOrigVal = elem.val();
            this.lastMonitored = elem.attr('id');
        }

        this.intervalHandler = setInterval(() => {
            var s : string = elem.val();
            if (s!==this.monitorOrigVal) {
                this.monitorOrigVal = s;
                if (s.length===0)
                    sfh.removeValue(i);
                else
                    sfh.setValue(i, s);
                this.updateMql();
            }
        }, 500);
    }

    /**
     * Creates the various feature selection panels associated with a newly selected quiz object type.
     * @param otype
     * @param fhs
     */
    private objectSelectionUpdated(otype : string, fhs : FeatureHandler[]) : void {
        this.handlers = [];
        this.groups = [];

 	// Create selection boxes for all features of this object type
        for (var key in getObjectSetting(otype).featuresetting) {
            var valueType : string = typeinfo.obj2feat[otype][key];
            var featset : FeatureSetting = getFeatureSetting(otype,key);

            // Ignore specified features plus features of type id_d
            if (featset.ignoreSelect)
                continue;

            var group = $('<div></div>');

            this.groups[key] = group;

            var selectString : string;

            if (featset.isDefault) {
                group.show();
                this.currentBox = group;
                selectString = 'selected="selected"';
            }
            else {
                group.hide();
                selectString = '';
            }

            this.featureCombo.append('<option value="{0}" {1}>{2}</option>'
                                     .format(key, selectString, getFeatureFriendlyName(otype,key)));
            
            if (valueType === 'integer') {
                if (featset.isRange) {
                    var rfh : RangeIntegerFeatureHandler = null;
                    if (fhs)
                        rfh = <RangeIntegerFeatureHandler>fhs[key];
                    if (!rfh)
                        rfh = new RangeIntegerFeatureHandler(key);

                    var group2 : JQuery = $('<table></table>');
                    var rowLow : JQuery = $('<tr></tr>');
                    var rowHigh : JQuery = $('<tr></tr>');

                    var cellLab : JQuery;
                    var cellInput : JQuery;
                    var cellErr : JQuery;


                    var jtf : JQuery = $('<input type="text" size="8">');
                    if (rfh.isSetLow())
                        jtf.val(String(rfh.value_low));

                    var err_id : string = 'err_{0}_low'.format(key,i);
                    jtf.on('keyup', null, {rfh: rfh, i: 'value_low', err_id: err_id}, $.proxy(this.rangeIntegerTextModifiedListener,this));
                    cellLab = $('<td>' + localize('low_value_prompt') + '</td>');
                    cellInput = $('<td></td>');
                    cellInput.append(jtf);
                    cellErr = $('<td id="{0}"></td>'.format(err_id));
                    rowLow.append(cellLab,cellInput,cellErr);


                    jtf = $('<input type="text" size="8">');
                    if (rfh.isSetHigh())
                        jtf.val(String(rfh.value_high));

                    err_id = 'err_{0}_high'.format(key,i);
                    jtf.on('keyup', null, {rfh: rfh, i: 'value_high', err_id: err_id}, $.proxy(this.rangeIntegerTextModifiedListener,this));
                    
                    cellLab = $('<td>' + localize('high_value_prompt') + '</td>');
                    cellInput = $('<td></td>');
                    cellInput.append(jtf);
                    cellErr = $('<td id="{0}"></td>'.format(err_id));
                    rowHigh.append(cellLab,cellInput,cellErr);
                    
                    group2.append(rowLow, rowHigh);
                    group.append(group2);
                    this.handlers.push(rfh);
                }
                else {
                    var ifh : IntegerFeatureHandler = null;
                    if (fhs)
                        ifh = <IntegerFeatureHandler>fhs[key];
                    if (!ifh)
                        ifh = new IntegerFeatureHandler(key);

                    var butEquals  : JQuery = $('<input type="radio" name="{0}_{1}_comp" value="equals">'
                                                .format(this.name_prefix,key));
                    var butDiffers : JQuery = $('<input type="radio" name="{0}_{1}_comp" value="differs">'
                                                .format(this.name_prefix,key));

                    switch (ifh.comparator) {
                    case 'equals': butEquals.prop('checked',true); break;
                    case 'differs': butDiffers.prop('checked',true); break;
                    }

                    var sel : JQuery = $('<span></span>');
                    sel.append(butEquals, "=", butDiffers, "&#x2260;");
                    group.append(sel);

                    sel.click(ifh, (e : JQueryEventObject) => {
                        // val() may return an empty value if the user clicks on, say, the = sign
                        var v = $(e.target).val();
                        switch (v) {
                        case 'equals':
                        case 'differs':
                            e.data.comparator = v; // e.data is ifh
                            this.updateMql();
                            break;
                        }
                    });

                    var group2 : JQuery = $('<table></table>');
                 
                    for (var i=0; i<ifh.values.length; ++i) {
                        var jtf : JQuery = $('<input type="text" size="8">');
                        if (ifh.values[i])
                            jtf.val(String(ifh.values[i]));

                        var err_id : string = 'err_{0}_{1}'.format(key,i);
                        jtf.on('keyup', null, {ifh: ifh, i: i, err_id: err_id}, $.proxy(this.integerTextModifiedListener,this));
                        var row = $('<tr></tr>');
                        var cell = $('<td></td>');
                        cell.append(jtf);
                        row.append(cell);
                        row.append('<td id="{0}"></td>'.format(err_id));
                        group2.append(row);
                    }
                    group.append(group2);
                    this.handlers.push(ifh);
                }
 	    }
 	    else if (valueType==='ascii' || valueType==='string') {
                var sfh : StringFeatureHandler = null;
                if (fhs)
                    sfh = <StringFeatureHandler>fhs[key];
                if (!sfh)
                    sfh = new StringFeatureHandler(key);

                var butEquals  : JQuery = $('<input type="radio" name="{0}_{1}_comp" value="equals">'
                                            .format(this.name_prefix,key));
                var butDiffers : JQuery = $('<input type="radio" name="{0}_{1}_comp" value="differs">'
                                            .format(this.name_prefix,key));
                var butMatches : JQuery = $('<input type="radio" name="{0}_{1}_comp" value="matches">'
                                            .format(this.name_prefix,key));

                switch (sfh.comparator) {
                case 'equals': butEquals.prop('checked',true); break;
                case 'differs': butDiffers.prop('checked',true); break;
                case 'matches': butMatches.prop('checked',true); break;
                }

                var sel : JQuery = $('<span></span>');
                sel.append(butEquals, '=', butDiffers, '&#x2260;', butMatches, '~');
                group.append(sel);

                sel.click(sfh, (e : JQueryEventObject) => {
                    // val() may return an empty value if the user clicks on, say, the = sign
                    var v = $(e.target).val();
                    switch (v) {
                    case 'equals':
                    case 'differs':
                    case 'matches':
                        e.data.comparator = v; // e.data is sfh
                        this.updateMql();
                        break;
                    }
                });

                var group2 : JQuery = $('<table></table>');
                
                if (featset.foreignText) {
                    for (var i=0; i<sfh.values.length; ++i) {
                        var kbdRowId : string = '{0}_{1}_row{2}'.format(this.name_prefix, key, +i+1);

                        var jtf : JQuery  = $('<input class="{0}" type="text" size="20" id="{1}_{2}_input{3}">'.format(charset.foreignClass, this.name_prefix, key, +i+1));

                        if (sfh.values[i])
                            jtf.val(sfh.values[i]);

                        jtf.on('focus', null, {kbdRowId : kbdRowId, sfh: sfh, i: i}, (e : JQueryEventObject) => {
                            $('#virtualkbid').appendTo('#' + e.data.kbdRowId);
                            VirtualKeyboard.attachInput(e.currentTarget);
                            this.monitorChange($(e.currentTarget), e.data.sfh, e.data.i)
                        });


                        jtf.on('keyup', null, {sfh: sfh, i: i}, $.proxy(this.stringTextModifiedListener,this));

                        var row = $('<tr></tr>');
                        var cell = $('<td></td>');
                        cell.append(jtf);
                        row.append(cell);
                        group2.append(row);

                        group2.append('<tr><td id="{0}" style="text-align:right;"></td></tr>'.format(kbdRowId));
                    }
                }
                else {
                    for (var i=0; i<sfh.values.length; ++i) {
                        var jtf : JQuery = $('<input type="text" size="20">'); // VerifiedField
                        if (sfh.values[i])
                            jtf.val(sfh.values[i]);

                        jtf.on('keyup', null, {sfh: sfh, i: i}, $.proxy(this.stringTextModifiedListener,this));
                        var row = $('<tr></tr>');
                        var cell = $('<td></td>');
                        cell.append(jtf);
                        row.append(cell);
                        group2.append(row);
                    }
                }
                group.append(group2);
                this.handlers.push(sfh);
 	    }
            else if (valueType.substr(0,8)==='list of ') {
                var stripped_valueType : string = valueType.substr(8);
 	        var enumValues = typeinfo.enum2values[stripped_valueType];

                if (!enumValues) {
                    // We cannot handle lists of non-enums
                    console.log('Unknown valueType',valueType);
                }

                var elfh : EnumListFeatureHandler = null;
                if (fhs)
                    elfh = <EnumListFeatureHandler>fhs[key];
                if (!elfh)
                    elfh = new EnumListFeatureHandler(key);
                
                var group_tabs : JQuery = $('<div id="list_tabs_{0}"></div>'.format(key));
                var group_ul : JQuery = $('<ul></ul>');
                group_tabs.append(group_ul);
                
                var tab_labels : string[] = [localize('1st_choice'),
                                             localize('2nd_choice'),
                                             localize('3rd_choice'),
                                             localize('4th_choice')];
                for (var tabno=0; tabno<4; ++tabno) {
                    var lv : ListValuesHandler = elfh.listvalues[tabno];

                    group_ul.append('<li><a href="#tab_{0}_{1}">{2}</li>'.format(key,tabno,tab_labels[tabno]));
                    var tab_contents : JQuery = $('<div id="tab_{0}_{1}"></div>'.format(key,tabno));
                    
                    var vc_choice : PanelForOneVcChoice 
                        = new PanelForOneVcChoice(enumValues,
                                                  stripped_valueType, 
                                                  '{0}_{1}_{2}_{3}'.format(this.name_prefix, otype, key, tabno),
                                                  lv);
                    tab_contents.append(vc_choice.getPanel());
                    group_tabs.append(tab_contents);
		    tab_contents.click(lv, (e : JQueryEventObject) => {
                        var target : JQuery = $(e.target);
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
 	        var enumValues = typeinfo.enum2values[valueType];

                if (!enumValues) {
                    console.log('Unknown valueType',valueType);
                }
                else {
                    var efh : EnumFeatureHandler = null;
                    if (fhs)
                        efh = <EnumFeatureHandler>fhs[key];
                    if (!efh)
                        efh = new EnumFeatureHandler(key);

                    var butEquals  : JQuery = $('<input type="radio" name="{0}_{1}_comp" value="equals">'
                                                .format(this.name_prefix,key));
		    var butDiffers : JQuery = $('<input type="radio" name="{0}_{1}_comp" value="differs">'
                                                .format(this.name_prefix,key));

                    switch (efh.comparator) {
                    case 'equals': butEquals.prop('checked',true); break;
                    case 'differs': butDiffers.prop('checked',true); break;
                    }

                    var sel : JQuery = $('<span></span>');
                    sel.append(butEquals, '=', butDiffers, '&#x2260;');
                    group.append(sel);

                    sel.click(efh, (e : JQueryEventObject) => {
                        // val() may return an empty value if the user clicks on, say, the = sign
                        var v = $(e.target).val();
                        switch (v) {
                        case 'equals':
                        case 'differs':
                            e.data.comparator = v; // e.data is efh
                            this.updateMql();
                            break;
                        }
                    });


                    var checkBoxes : SortingCheckBox[] = [];

                    for (var i = 0; i<enumValues.length; ++i) {
                        var s : string = enumValues[i];

                        var hv : string[] = featset.hideValues;
		        var ov : string[] = featset.otherValues;
                        if ((hv && hv.indexOf(s)!==-1) || ((ov && ov.indexOf(s)!==-1)))
                            continue;

                        var scb = new SortingCheckBox(this.name_prefix + '_' + key, s, getFeatureValueFriendlyName(valueType, s, false));
                        if (!efh.values) alert('Assert efh.values failed for type ' + key);
                        scb.setSelected(efh.values && efh.values.indexOf(s)!==-1);
                        checkBoxes.push(scb);
                    }

                    checkBoxes.sort((a : SortingCheckBox, b : SortingCheckBox) => StringWithSort.compare(a.getSws(),b.getSws()));

		    // Decide how many columns and rows to use for feature values
		    var columns : number = 
		        checkBoxes.length>12 ? 3 :
		        checkBoxes.length>4 ? 2 : 1;

                    var rows : number = Math.ceil(checkBoxes.length / columns);

                    var group2 : JQuery = $('<table></table>');
                    for (var r=0; r<rows; ++r) {
                        var rw : JQuery = $('<tr></tr>');
                        for (var c=0; c<columns; ++c) {
                            var cell : JQuery = $('<td></td>');
                            if (c*rows + r < checkBoxes.length)
                                cell.append(checkBoxes[c*rows + r].getJQuery());
                            rw.append(cell);
                        }
                        group2.append(rw);
                    }


		    group2.click(efh, (e : JQueryEventObject) => {
                        var target : JQuery = $(e.target);
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

    public getOtype() : string {
        return this.objectTypeCombo.val();
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
    public getUseForQo() : boolean {
	return false;
    }
	
    public isDirty() : boolean {
	return this.getMql() !== this.txtEntry;
    }
	
    public makeMql() : string {
        if (this.handlers) {
	    var sb : string = '';
	    var first : boolean = true;

	    for (var i=0; i<this.handlers.length; ++i) {
	        var fh : FeatureHandler = this.handlers[i];

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

    public switchToMql(useMql : boolean) : void {
        alert('Abstract function switchToMql() called');
    }
	
    public updateMql() : void {
 	this.setMql(this.makeMql());
    }


    public populateFeatureTab(otype: string) : void {
        alert('Abstract function populateFeatureTab() called');
    }


    public getInfo() : MqlData {
        var res : MqlData = {
            object   : this.getOtype(),
            mql      : null,
            featHand : { vhand: null },
            useForQo : this.getUseForQo()
        };

        if (this.rbMql.prop('checked'))
            res.mql = this.getMql();
        else {
            res.featHand.vhand = [];

            for (var i=0; i<this.handlers.length; ++i) {
	        var fh : FeatureHandler = this.handlers[i];

		if (fh.hasValues())
                    res.featHand.vhand.push(fh);
            }
        }

        return res;
    }
}



