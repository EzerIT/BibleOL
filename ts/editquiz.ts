// -*- js -*-
/* 2013 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk */

/// <reference path="jquery/jquery.d.ts" />
/// <reference path="jqueryui/jqueryui.d.ts" />
/// <reference path="configuration.ts" />
/// <reference path="charset.ts" />
/// <reference path="sentencegrammar.ts" />
/// <reference path="localization.ts" />
/// <reference path="paneltemplmql.ts" />
/// <reference path="paneltemplsentenceselector.ts" />
/// <reference path="paneltemplquizobjectselector.ts" />
/// <reference path="paneltemplquizfeatures.ts" />
/// <reference path="verbclasspanel.ts" />
/// <reference path="stringwithsort.ts" />
/// <reference path="sortingcheckbox.ts" />
/// <reference path="util.ts" />

// Dummy interface
interface MonadObject { mo : any}

interface myalertInterface{
    (dialogtitle : string, dialogtext : string) : void;
}

declare var myalert: myalertInterface;

interface JQuery{
    jstree : Function;
    ckeditor : Function;    
}

declare var decoded_3et      : any;
declare var initial_universe : string[];
declare var submit_to        : string;
declare var check_url        : string;
declare var quiz_name        : string;
declare var dir_name         : string;

var VirtualKeyboard  : any;


var panelSent     : PanelTemplSentenceSelector; // Sentence selection panel
var panelSentUnit : PanelTemplQuizObjectSelector; // Sentence unit selection panel
var panelFeatures : PanelTemplQuizFeatures; // Features panel
var isSubmitting  : boolean = false;

var last_quiz_name : string;
var mayOverwrite   : boolean = false;

var checked_passages : any[];
var ckeditor : any;

var charset : Charset;


function isDirty() : boolean {
    if (isSubmitting)
        return false;

    if (ckeditor.ckeditorGet().checkDirty())
        return true;

    checked_passages = $("#passagetree").jstree("get_checked",null,false);

    if (checked_passages.length !== initial_universe.length)
        return true;

    for (var i=0; i<checked_passages.length; ++i)
        if ($(checked_passages[i]).data('ref') !== initial_universe[i])
            return true;

    return panelSent.isDirty() || panelSentUnit.isDirty() || panelFeatures.isDirty();
}

$(function() {
    for (var i in configuration.sentencegrammar) {
        if (isNaN(+i)) continue; // Not numeric
        addMethodsSgi(configuration.sentencegrammar[+i]);
    }

    $(window).on('beforeunload', function() {
        if (isDirty())
            return 'You haven\'t saved your changes.';
    });

    charset = new Charset(configuration.charSet);

    if (VirtualKeyboard) {
        VirtualKeyboard.setVisibleLayoutCodes([charset.keyboardName]);
        VirtualKeyboard.toggle('firstinput','virtualkbid');
    }

    ckeditor = $('#txtdesc').ckeditor(
        {
            uiColor : '#feeebd',
	    toolbarGroups : [
		{ name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },
		{ name: 'editing',     groups: [ 'find', 'selection' ] },
		{ name: 'links' },
		{ name: 'insert' },
		//{ name: 'forms' },
		//{ name: 'tools' },
		{ name: 'document',    groups: [ 'mode'/*, 'document', 'doctools'*/ ] },
		//{ name: 'others' },
		'/',
		{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
		{ name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ] },
		{ name: 'styles' },
		{ name: 'colors' },
		{ name: 'about' }
	    ],

	    // Remove some buttons, provided by the standard plugins, which we don't need
	    removeButtons : 'Print,Preview,NewPage,Save,Flash,PageBreak,Iframe,CreateDiv,language',

	    // Make dialogs simpler.
	    removeDialogTabs : 'image:advanced;link:advanced'
        }
    );

    ckeditor.val(decoded_3et.desc);

    $("#quiz_tabs").tabs({ disabled: [3] });

    $('button').button();
    $('input[type="button"]').button();

    panelFeatures = new PanelTemplQuizFeatures(decoded_3et.quizObjectSelection.object, decoded_3et.quizFeatures, $('#tab_features'));

    panelSentUnit = new PanelTemplQuizObjectSelector(decoded_3et.quizObjectSelection, $('#tab_sentence_units'),
                                                     panelFeatures);
    panelSent = new PanelTemplSentenceSelector(decoded_3et.sentenceSelection, $('#quiz_tabs'), $('#tab_sentences'),
                                               panelSentUnit, panelFeatures);
});

function save_quiz() {
    checked_passages = $("#passagetree").jstree("get_checked",null,false);

    if (checked_passages.length==0) {
        myalert('Passage selection', 'No passages selected');
        return;
    }

    if (panelFeatures.noRequestFeatures()) {
        myalert('Feature specification','No request features specified');
        return;
    }

    if (panelFeatures.noShowFeatures()) {
        myalert('Feature specification','No show features specified');
        return;
    }
    

    $('#filename-error').text('');

    $("#filename-dialog").dialog({
            autoOpen: false,
            resizable: false,
            modal: true,
            width: 600,
            buttons: {
                "Save": function() {
                    if ($('#filename-name').val().trim()=='')
                        $('#filename-error').text("Missing filename");
                    else {
                        quiz_name = $('#filename-name').val().trim();

                        // Check if file may be written
                        $.ajax('{0}?dir={1}&quiz={2}'.format(check_url,
                                                             encodeURIComponent(dir_name),
                                                             encodeURIComponent(quiz_name)))
                            .done((data, textStatus, jqXHR) => {
                                switch (data.trim()) {
                                case 'OK':
                                    $(this).dialog('close');
                                    save_quiz2();
                                    break;
                                case 'EXISTS':
                                    $(this).dialog('close');
                                    check_overwrite();
                                    break;
                                default:
                                    $('#filename-error').text(data);
                                    break;
                                }
                            })
                            .fail((jqXHR, textStatus, errorThrown) => {
                                $('#filename-error').text('Error response from server: ' + errorThrown);
                            });
                    }
                },
                Cancel: function() {
                    $(this).dialog('close');
                }
            }
         }).dialog('open');
}

function check_overwrite() {
    $("#overwrite-dialog-confirm").dialog({
        autoOpen: true,
        resizable: false,
        modal: true,
        buttons: {
            "Yes": function() {
                $(this).dialog("close");
                save_quiz2();
            },
            "No": function() {
                $(this).dialog("close");
            }
        }
    });
}

function save_quiz2() {
    decoded_3et.desc = ckeditor.val();
    
    decoded_3et.selectedPaths = [];
    
    for (var i=0; i<checked_passages.length; ++i) {
        var r = $(checked_passages[i]).data('ref');
        if (r!='')
            decoded_3et.selectedPaths.push(r);
    }
    
    decoded_3et.sentenceSelection = panelSent.getInfo();
    decoded_3et.quizObjectSelection = panelSentUnit.getInfo();
    decoded_3et.quizFeatures = panelFeatures.getInfo();
    
    var form : JQuery = $('<form action="{0}" method="post">'.format(submit_to)
                          + '<input type="hidden" name="dir" value="{0}">'.format(encodeURIComponent(dir_name))
                          + '<input type="hidden" name="quiz" value="{0}">'.format(encodeURIComponent(quiz_name))
                          + '<input type="hidden" name="quizdata" value="{0}">'.format(encodeURIComponent(JSON.stringify(decoded_3et)))
                          + '</form>');
    
    $('body').append(form);

    isSubmitting = true;
    form.submit();
}
