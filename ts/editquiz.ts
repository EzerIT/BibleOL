// -*- js -*-
/* 2013 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk */

/// <reference path="bootstrap/bootstrap.d.ts" />
/// <reference path="jquery/jquery.d.ts" />
/// <reference path="jqueryui/jqueryui.d.ts" />
/// <reference path="configuration.ts" />
/// <reference path="charset.ts" />
/// <reference path="sentencegrammar.ts" />
/// <reference path="localization.ts" />
/// <reference path="localization_general.ts" />
/// <reference path="paneltemplmql.ts" />
/// <reference path="paneltemplsentenceselector.ts" />
/// <reference path="paneltemplquizobjectselector.ts" />
/// <reference path="paneltemplquizfeatures.ts" />
/// <reference path="verbclasspanel.ts" />
/// <reference path="stringwithsort.ts" />
/// <reference path="sortingcheckbox.ts" />
/// <reference path="util.ts" />

// Dummy interfaces
interface MonadObject { mo : any}
interface MultipleMonadObject extends MonadObject { subobjects : any }


interface myalertInterface{
    (dialogtitle : string, dialogtext : string) : void;
}

declare var myalert: myalertInterface;

interface JQuery{
    jstree : Function;
    ckeditor : Function;    
}

declare var decoded_3et        : any;
declare var initial_universe   : string[];
declare var submit_to          : string;
declare var check_url          : string;
declare var import_shebanq_url : string;
declare var quiz_name          : string;
declare var dir_name           : string;

var VirtualKeyboard  : any;


var origMayLocate : boolean;
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

    checked_passages = $('#passagetree').jstree('get_checked',null,false);

    if (checked_passages.length !== initial_universe.length)
        return true;

    if ($('#maylocate_cb').prop('checked')!=origMayLocate)
        return true;
    
    for (var i=0; i<checked_passages.length; ++i)
        if ($(checked_passages[i]).data('ref') !== initial_universe[i])
            return true;

    return panelSent.isDirty() || panelSentUnit.isDirty() || panelFeatures.isDirty();
}

// CT - 2016-11-07:
// The execution of this function is postponed one second to ensure that ckeditor and VirtualKeyboard
// have been loaded.
// This delay needed to be inserted after adding the Chinese interface; but later it seemed to be unnecessary.
// Maybe it can be removed again by replaceing setTimeout(....,1000) with $(....).
setTimeout(function() {
    for (var i in configuration.sentencegrammar) {
        if (isNaN(+i)) continue; // Not numeric
        addMethodsSgi(configuration.sentencegrammar[+i], configuration.sentencegrammar[+i].objType);
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

    $('#quiz_tabs').tabs({ disabled: [3] });

    origMayLocate = decoded_3et.maylocate;
    $('#maylocate_cb').prop('checked', origMayLocate);
    
    panelFeatures = new PanelTemplQuizFeatures(decoded_3et.quizObjectSelection.object, decoded_3et.quizFeatures, $('#tab_features'));

    panelSentUnit = new PanelTemplQuizObjectSelector(decoded_3et.quizObjectSelection, $('#tab_sentence_units'),
                                                     panelFeatures);
    panelSent = new PanelTemplSentenceSelector(decoded_3et.sentenceSelection, $('#quiz_tabs'), $('#tab_sentences'),
                                               panelSentUnit, panelFeatures);

    $('.quizeditor').show();
},1000);

function show_error(id : string, message : string) {
    $(id + '-text').text(message);
    $(id).show();
}

function hide_error(id : string) {
    $(id).hide();
}

function save_quiz() {
    checked_passages = $('#passagetree').jstree('get_checked',null,false);

    if (checked_passages.length==0) {
        myalert(localize('passage_selection'), localize('no_passages'));
        return;
    }

    if (panelFeatures.noRequestFeatures()) {
        myalert(localize('feature_specification'), localize('no_request_feature'));
        return;
    }

    if (panelFeatures.noShowFeatures()) {
        myalert(localize('feature_specification'), localize('no_show_feature'));
        return;
    }
    

    hide_error('#filename-error');

    $('#filename-dialog-save').off('click'); // Remove any previous handler
    $('#filename-dialog-save').on('click',() => {
        if ($('#filename-name').val().trim()=='')
            show_error('#filename-error', localize('missing_filename'));
        else {
            quiz_name = $('#filename-name').val().trim();

            // Check if file may be written
            $.ajax('{0}?dir={1}&quiz={2}'.format(check_url,
                                                 encodeURIComponent(dir_name),
                                                 encodeURIComponent(quiz_name)))
                .done((data, textStatus, jqXHR) => {
                    switch (data.trim()) {
                    case 'OK':
                        $('#filename-dialog').modal('hide');
                        save_quiz2();
                        break;
                    case 'EXISTS':
                        $('#filename-dialog').modal('hide');
                        check_overwrite();
                        break;
                    case 'BADNAME':
                        show_error('#filename-error', localize('badname'));
                        break;
                    default:
                        show_error('#filename-error', data);
                        break;
                    }
                })
                .fail((jqXHR, textStatus, errorThrown) => {
                    show_error('#filename-error',
                               '{0} {1}'.format(localize('error_response'), errorThrown));
                });
        }
    });
    $('#filename-dialog').modal('show');        
}

function check_overwrite() {
    $('#overwrite-yesbutton').off('click');
    $('#overwrite-yesbutton').on('click',() => {
        save_quiz2();
        $('#overwrite-dialog-confirm').modal('hide');
    });
    $('#overwrite-dialog-confirm').modal('show');
}

function save_quiz2() {
    decoded_3et.desc = ckeditor.val();
    
    decoded_3et.selectedPaths = [];
    
    for (var i=0; i<checked_passages.length; ++i) {
        var r = $(checked_passages[i]).data('ref');
        if (r!='')
            decoded_3et.selectedPaths.push(r);
    }

    decoded_3et.maylocate = $('#maylocate_cb').prop('checked');

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

function shebanq_to_qo(qo : string, mql : string) {
    if (qo===null) {
        $('#qo-dialog-text').html('<p>{0}</p><p>{1}</p>'.format(localize('sentence_selection_imported'),
                                                                localize('no_focus')));
        $('#qo-yesbutton').hide();
        $('#qo-nobutton').hide();
        $('#qo-okbutton').show();

        $('#qo-dialog-confirm').modal('show');
    }
    else {
        // This is a multi-level format substitution
        // Replace & < and > with HTML entities
        var msg = mql.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
        
        // Embded in HTML formatting
        msg = '<br><code>[{0} {1}]</code><br>'.format(qo, msg);

        // Embed in localized string
        msg = localize('use_qo_selection').format(msg);

        // Format for dialog
        msg = '<p>{0}</p><p>{1}</p>'.format(localize('sentence_selection_imported'), msg);

        // Set the dialog text
        $('#qo-dialog-text').html(msg);

        $('#qo-yesbutton').show();
        $('#qo-nobutton').show();
        $('#qo-okbutton').hide();

        $('#qo-yesbutton').off('click');
        $('#qo-yesbutton').on('click',() => {
            $('#qo-dialog-confirm').modal('hide');
            panelSentUnit.setOtype(qo);
            panelSentUnit.setUsemql();
            panelSentUnit.setMql(mql);
        });
        $('#qo-dialog-confirm').modal('show');
    }
}

function import_from_shebanq() {
    hide_error('#import-shebanq-error');

    $('#import-shebanq-button').off('click');
    $('#import-shebanq-button').on('click',() => {
        $('.ui-dialog *').css('cursor', 'wait');

        $.ajax('{0}?id={1}&version={2}'.format(import_shebanq_url,
                                               encodeURIComponent($('#import-shebanq-qid').val().trim()),
                                               encodeURIComponent($('#import-shebanq-dbvers').val().trim())))
            .done((data, textStatus, jqXHR) => {
                $('.ui-dialog *').css('cursor', 'auto');

                var result = JSON.parse(data);
                if (result.error===null) {
                    panelSent.setMql(result.sentence_mql);
                    $('#import-shebanq-dialog').modal('hide');
                    shebanq_to_qo(result.sentence_unit, result.sentence_unit_mql);
                }
                else {
                    show_error('#import-shebanq-error', result.error);
                }
            })
            .fail((jqXHR, textStatus, errorThrown) => {
                $('.ui-dialog *').css('cursor', 'auto');
                show_error('#import-shebanq-error',
                           '{0} {1}'.format(localize('error_response'), errorThrown));
            });
    });


    $('#import-shebanq-dialog').modal('show');
}
