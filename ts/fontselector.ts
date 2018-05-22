// -*- js -*-

/* 2013 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk */

/// <reference path="fontdetect.d.ts" />
/// <reference path="util.ts" />
/// <reference path="localization_general.ts" />

class FontSelector {
    private alphabet : string;
    private sample : string;
    private direction : string;
    private detector : Detector;
    private inputName : string;
    private tableSelector : JQuery;
    private myfont_text : JQuery; // The Input element for the personal font name
    private myfont_radio_button : JQuery; // The radio button for the personal font name

    constructor(alphabet : string, sample : string, direction : string) {
        this.alphabet = alphabet;
        this.sample = sample;
        this.direction = direction;
        this.detector = new Detector(alphabet);
        this.inputName = alphabet + 'choice';
        this.tableSelector = $('#' + alphabet + 'font');
    }

    private familyChange() {
        var val = $('input:radio[name="' + this.alphabet + 'choice"]:checked').attr('data-family');
        if (val==='XXmineXX') { // Personal font
            val = this.myfont_text.prop('value');
            $('#' + this.alphabet + '_mysample').css('font-family',val);
        }
        $('.' + this.alphabet + 'sample').css('font-family',val);
    }

    private personalChange() {
        $('input:radio[value="{0}_mine"]'.format(this.alphabet)).prop('checked',true);
        this.familyChange();
    }

    public detectFonts(fontlist : {name:string;webfont:boolean;}[], personal_font : string, default_val : string) : void {

        for (var i=0, len=fontlist.length; i<len; ++i) {
            if (fontlist[i].webfont || this.detector.detect(fontlist[i].name)) {
                var radio_button = $('<input name="{0}" type="radio" data-family="{1}" value="{2}_{3}">'
                                     .format(this.inputName,fontlist[i].name,this.alphabet,i));

                var td1 = $('<td>').append(fontlist[i].name);
                var td2 = $('<td class="sample" style="direction:{0}; font-family:{1}; font-size:16pt;">'
                            .format(this.direction,fontlist[i].name))
                    .append(this.sample);
                var td3 = $('<td class="centeralign">').append(radio_button);
                var tr = $('<tr>').append(td1).append(td2).append(td3);
                this.tableSelector.append(tr);
            }
        }

        // Add personal font
        this.myfont_text = $('<input type="text" name="{0}_myfont" value="{1}">'.format(this.alphabet,personal_font));
        this.myfont_radio_button = $('<input name="{0}" type="radio" data-family="XXmineXX" value="{1}_mine">'.format(this.inputName,this.alphabet));

        var td1 = $('<td>').append(localize('or_write_preferred')+'<br>').append(this.myfont_text);
        var td2 = $('<td class="sample" id="{0}_mysample" style="direction:{1}; font-family:{2}; font-size:16pt;">'
                    .format(this.alphabet,this.direction,personal_font))
            .append(this.sample);
        var td3 = $('<td class="centeralign">').append(this.myfont_radio_button);
        var tr = $('<tr>').append(td1).append(td2).append(td3);
        this.tableSelector.append(tr);

        $('input:radio[value="{0}"]'.format(default_val)).prop('checked',true);


        // Handle changing of font selection
        $('input:radio[name="{0}"]'.format(this.inputName)).on('change', () => this.familyChange());
        this.familyChange();

        this.myfont_text.on('input', (e : JQueryEventObject) => this.personalChange());
    }
}
