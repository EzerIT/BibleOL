// -*- js -*-
// Copyright © 2018 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk

// Main functions and variables for handling creation and modification of exercises


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

// Dummy interfaces, used by sentencegrammar.ts instead of the full monadobject.ts.
interface MonadObject { mo : any }
interface MultipleMonadObject extends MonadObject { subobjects : any }

//****************************************************************************************************
// myalertInterface interface
//
// The function signature use by the myalert function, which displays an alert dialog in the browser.
//
// Parameters:
//     dialogtitle: The title of the dialog box.
//     dialogtext: The text of the dialog box.
//
interface myalertInterface {
    (dialogtitle : string, dialogtext : string) : void;
}

//****************************************************************************************************
// myalert function
//
// Displays a runtime alert in the browser.
// The function is defined in the server code.
//
declare let myalert: myalertInterface;

//****************************************************************************************************
// Extension of the JQuery interface
//
interface JQuery {
    jstree   : Function;
    ckeditor : Function;
}

//****************************************************************************************************
// Variables defined by server code
//
declare let decoded_3et        : any;      // JSON version of exercise template
declare let initial_universe   : string[]; // Bible passes from exercise template
declare let submit_to          : string;   // URL to which the edited exercise should be sent
declare let check_url          : string;   // URL for ajax queries about the validity of an exercise file name
declare let test_quiz_url      : string;   // URL for testing an exercise
declare let import_shebanq_url : string;   // URL for ajax queries for imports from SHEBANQ (Note: This is a URL on the Bible OL server, not the SHEBANQ server)
declare let quiz_name          : string;   // Name of exercise file
declare let dir_name           : string;   // Name of exercise file directory


//****************************************************************************************************
// Other globale variables
//
let WirtualKeyboard    : any;                          // Virtual keyboard
let origMayLocate      : boolean;                      // Does the exercise original allow users to locate passages?
let origSentBefore     : number;                       // Context sentences before question in original
let origSentAfter      : number;                       // Context sentences after question in original
let origFixedQuestions : number;                       // Fixed number of questions
let origRandomize      : boolean;                      // Randomize questions
let panelSent          : PanelTemplSentenceSelector;   // Sentence selection panel
let panelSentUnit      : PanelTemplQuizObjectSelector; // Sentence unit selection panel
let panelFeatures      : PanelTemplQuizFeatures;       // Features panel
let isSubmitting       : boolean = false;              // Are we in the process of sending an exercise to the server?
let checked_passages   : any[];                        // Selected Bible passages
let ckeditor           : any;                          // Text editor
let charset            : Charset;                      // Character set


//****************************************************************************************************
// isDirty function
//
// Checks if the user has changed anyting in the exercise
//
// Returns:
//     True if something in the exercise has been changed.
//
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

    if ($('#sentbefore').val()!=origSentBefore)
        return true;

    if ($('#sentafter').val()!=origSentAfter)
        return true;

    if ($('#fixedquestions').val()!=origFixedQuestions)
        return true;

    if ($('#randomorder').prop('checked')!=origRandomize)
        return true;

    for (let i=0; i<checked_passages.length; ++i)
        if ($(checked_passages[i]).data('ref') !== initial_universe[i])
            return true;

    return panelSent.isDirty() || panelSentUnit.isDirty() || panelFeatures.isDirty();
}


//****************************************************************************************************
// show_error function
//
// Displays an error message in a dialog.
//
// Parameters:
//     id: HTML ID of dialog.
//     message: The text to display.
//
function show_error(id : string, message : string) : void {
    $(id + '-text').text(message);
    $(id).show();
}

//****************************************************************************************************
// hide_error function
//
// Hides an error message in a dialog.
//
// Parameters:
//     id: HTML ID of dialog.
//
function hide_error(id : string) : void {
    $(id).hide();
}

//****************************************************************************************************
// save_quiz function
//
// Called when the user presses the "Save" button after editing an exercise.
//
// Phase 1:
// This function displays the filename dialog, then when the user has typed a file name, the
// function checks the validity of that name.
//
// Phase 2:
// Control is passed to save_quiz2 or check_overwrite (which may in turn call save_quiz2) for
// further processing.
//
function save_quiz() : void {
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

    // Set up handler for the 'Save' button in the filename dialog

    $('#filename-dialog-save').off('click'); // Remove any previous handler
    $('#filename-dialog-save').on('click',() => {
        // This code is executed when the user clicks 'Save' in the filename dialog

        if (($('#filename-name').val() as string).trim() == '')
            show_error('#filename-error', localize('missing_filename'));
        else {
            quiz_name = ($('#filename-name').val() as string).trim();

            // Ask the server if file may be written
            $.ajax(`${check_url}?dir=${encodeURIComponent(dir_name)}&quiz=${encodeURIComponent(quiz_name)}`)
                .done((data, textStatus, jqXHR) => {

                    // Check reply form check_url
                    switch (data.trim()) {
                    case 'OK':
                        // Everthing is find
                        $('#filename-dialog').modal('hide');
                        save_quiz2(); // Proceed to phase 2
                        break;

                    case 'EXISTS':
                        // The file already exists
                        $('#filename-dialog').modal('hide');
                        check_overwrite(); // Check if it is OK to overwrite it
                        break;

                    case 'BADNAME':
                        // The filename is illegal
                        show_error('#filename-error', localize('badname'));
                        break;

                    default:
                        // Error message - display it
                        show_error('#filename-error', data);
                        break;
                    }
                })
                .fail((jqXHR, textStatus, errorThrown) => {
                    // Ajax request failed - display error
                    show_error('#filename-error', `${localize('error_response')} ${errorThrown}`);
                });
        }
    });

    // Show the filename dialog
    $('#filename-dialog').modal('show');
}


function test_quiz(quiz_name:string) : void {
    //console.log('Quiz Name: ', qname);
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
    //quiz_name = 'tmp';

    // Build decoded_3et so that it contains the new exercise
    decoded_3et.desc = ckeditor.val();
    decoded_3et.selectedPaths = [];
    for (let i=0; i<checked_passages.length; ++i) {
        let r = $(checked_passages[i]).data('ref');
        if (r!='')
            decoded_3et.selectedPaths.push(r);
    }
    decoded_3et.maylocate = $('#maylocate_cb').prop('checked');
    decoded_3et.sentbefore = $('#sentbefore').val();
    decoded_3et.sentafter = $('#sentafter').val();
    decoded_3et.fixedquestions = +$('#fixedquestions').val(); // Convert to number
    decoded_3et.randomize = $('#randomorder').prop('checked');
    if (!(decoded_3et.fixedquestions>0))
        decoded_3et.fixedquestions = 0; // Non-positive or NaN

    decoded_3et.sentenceSelection   = panelSent.getInfo();
    decoded_3et.quizObjectSelection = panelSentUnit.getInfo();
    decoded_3et.quizFeatures        = panelFeatures.getInfo();


    // Build decoded_3et so that it contains the new exercise
    /*
    decoded_3et.desc = ckeditor.val();    

    decoded_3et.maylocate = $('#maylocate_cb').prop('checked');
    decoded_3et.sentbefore = $('#sentbefore').val();
    decoded_3et.sentafter = $('#sentafter').val();
    decoded_3et.fixedquestions = +$('#fixedquestions').val(); // Convert to number
    decoded_3et.randomize = $('#randomorder').prop('checked');

    if (!(decoded_3et.fixedquestions>0))
        decoded_3et.fixedquestions = 0; // Non-positive or NaN

    decoded_3et.sentenceSelection   = panelSent.getInfo();
    decoded_3et.quizObjectSelection = panelSentUnit.getInfo();
    decoded_3et.quizFeatures        = panelFeatures.getInfo();
    console.log('decoded_3et', decoded_3et);
    */
    // The HTML form contains the directory, the filename and the exercise as a JSON string
    let form : JQuery = $(`<form action="${test_quiz_url}" method="post">
                             <input type="hidden" name="dir"      value="${encodeURIComponent(dir_name)}">
                             <input type="hidden" name="quiz"     value="${encodeURIComponent(quiz_name)}">
                             <input type="hidden" name="quizdata" value="${encodeURIComponent(JSON.stringify(decoded_3et))}">
                           </form>`);

    $('body').append(form);
    isSubmitting = true;
    form.submit()

}

//****************************************************************************************************
// check_overwrite function
//
// Called if the file in which we try to save an exercise already exists. Asks the user to confirm a
// file overwrite.
//
function check_overwrite() : void {
    $('#overwrite-yesbutton').off('click');
    $('#overwrite-yesbutton').on('click',() => {
        // The user clicked the 'Yes' button
        save_quiz2(); // Proceed to phase 2 of the file saving
        $('#overwrite-dialog-confirm').modal('hide');
    });

    // Show the overwrite dialog
    $('#overwrite-dialog-confirm').modal('show');
}

//****************************************************************************************************
// save_quiz2 function
//
// Phase 2 of saving an exercise to a file. This function creates an HTML form and posts it to the
// server.
//
function save_quiz2() : void {
    // Build decoded_3et so that it contains the new exercise

    decoded_3et.desc = ckeditor.val();

    decoded_3et.selectedPaths = [];

    for (let i=0; i<checked_passages.length; ++i) {
        let r = $(checked_passages[i]).data('ref');
        if (r!='')
            decoded_3et.selectedPaths.push(r);
    }

    decoded_3et.maylocate = $('#maylocate_cb').prop('checked');
    decoded_3et.sentbefore = $('#sentbefore').val();
    decoded_3et.sentafter = $('#sentafter').val();
    decoded_3et.fixedquestions = +$('#fixedquestions').val(); // Convert to number
    decoded_3et.randomize = $('#randomorder').prop('checked');
    if (!(decoded_3et.fixedquestions>0))
        decoded_3et.fixedquestions = 0; // Non-positive or NaN

    decoded_3et.sentenceSelection   = panelSent.getInfo();
    decoded_3et.quizObjectSelection = panelSentUnit.getInfo();
    decoded_3et.quizFeatures        = panelFeatures.getInfo();

    // The HTML form contains the directory, the filename and the exercise as a JSON string
    let form : JQuery = $(`<form action="${submit_to}" method="post">
                             <input type="hidden" name="dir"      value="${encodeURIComponent(dir_name)}">
                             <input type="hidden" name="quiz"     value="${encodeURIComponent(quiz_name)}">
                             <input type="hidden" name="quizdata" value="${encodeURIComponent(JSON.stringify(decoded_3et))}">
                           </form>`);

    $('body').append(form);

    isSubmitting = true;
    form.submit();
}

//****************************************************************************************************
// shebanq_to_qo function
//
// Called when a query has been retrieved from SHEBANQ. This function asks the user if data from
// SHEBANQ should also be used to set the sentence unit selection.
//
// Parameters:
//     qo: Question object (that is, the sentence unit for the question).
//     mql: The MQL for sentence unit selection.
//
function shebanq_to_qo(qo : string, mql : string) : void {
    if (qo===null) {
        // The query specified no object with FOCUS
        $('#qo-dialog-text').html(`<p>${localize('sentence_selection_imported')}</p><p>${localize('no_focus')}</p>`);
        $('#qo-yesbutton').hide();
        $('#qo-nobutton').hide();
        $('#qo-okbutton').show();

        $('#qo-dialog-confirm').modal('show');
    }
    else {
        // Decode the MQL string

        // This is a multi-level format substitution
        // Replace & < and > with HTML entities
        let msg : string = mql.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');

        // Embded in HTML formatting
        msg = `<br><code>[${qo} ${msg}]</code><br>`;

        // Embed in localized string
        msg = localize('use_qo_selection').format(msg);

        // Format for dialog
        msg = `<p>${localize('sentence_selection_imported')}</p><p>${msg}</p>`;

        // Set the dialog text
        $('#qo-dialog-text').html(msg);

        $('#qo-yesbutton').show();
        $('#qo-nobutton').show();
        $('#qo-okbutton').hide();

        $('#qo-yesbutton').off('click');
        $('#qo-yesbutton').on('click',() => {
            // The user selected to use the data for sentence unit selection
            $('#qo-dialog-confirm').modal('hide');
            panelSentUnit.setOtype(qo);
            panelSentUnit.setUsemql();
            panelSentUnit.setMql(mql);
        });

        // Ask if user wants to use the data for sentence unit selection
        $('#qo-dialog-confirm').modal('show');
    }
}

//****************************************************************************************************
// import_from_shebanq function
//
// Shows the "Import from SHEBANQ" dialog to the user and retrieves the requested MQL statement from
// the Bible OL server, which in turn retrieves it from the SHEBANQ server.
//
function import_from_shebanq() : void {
    hide_error('#import-shebanq-error');

    $('#import-shebanq-button').off('click');
    $('#import-shebanq-button').on('click',() => {
        // The user clicked the "Import" button in the dialog
        $('.ui-dialog *').css('cursor', 'wait');

        // Encode data from the dialog
        let shebanq_id     : string = encodeURIComponent($('#import-shebanq-qid').val()    as string).trim();
        let shebanq_dbvers : string = encodeURIComponent($('#import-shebanq-dbvers').val() as string).trim();

        // Ask the Bible OL server to ask SHEBANQ for the data
        $.ajax(`${import_shebanq_url}?id=${shebanq_id}&version=${shebanq_dbvers}`)
            .done((data, textStatus, jqXHR) => {
                // Request was answered

                $('.ui-dialog *').css('cursor', 'auto');

                let result :
                    {
                        error             : string; // Error information
                        sentence_mql      : string; // MQL for sentence selection
                        sentence_unit     : string; // Sentence unit (object with FOCUS in the MQL request)
                        sentence_unit_mql : string; // MQL for sentence unit selection
                    }
                    = JSON.parse(data);

                if (result.error===null) {
                    panelSent.setMql(result.sentence_mql); // Use sentence_mql for sentence selection
                    $('#import-shebanq-dialog').modal('hide');
                    shebanq_to_qo(result.sentence_unit, result.sentence_unit_mql); // Optionally set sentence unit selection
                }
                else {
                    show_error('#import-shebanq-error', result.error);
                }
            })
            .fail((jqXHR, textStatus, errorThrown) => {
                // Communication error
                $('.ui-dialog *').css('cursor', 'auto');
                show_error('#import-shebanq-error', `${localize('error_response')} ${errorThrown}`);
            });
    });


    $('#import-shebanq-dialog').modal('show');
}

//------------------------------------------------------------------------------------------
// numberInputModifiedListener method
//
// Called when the value in an integer input field changes.
//
// Parameter:
//     e: The event that represents the input change.
//
function numberInputModifiedListener(e : JQueryEventObject) : void {
    let s   : string = $(e.currentTarget).val() as string;

    $('#' + e.data.err_id).html(''); // Clear error indication

    if (s.length!==0 && s.match(/\D/g)!==null) // Check that input is an integer (Note: Rejects minus sign)
        $('#' + e.data.err_id).html(localize('not_integer')); // Set error indication
}

interface FeatureOrder {
    [key:string]: any;
}

//****************************************************************************************************
// The main program
//
// The execution of this function is postponed one second to ensure that ckeditor and WirtualKeyboard
// have been loaded.
// This delay needed to be inserted after adding the Chinese interface; but later it seemed to be unnecessary.
// Maybe it can be removed again by replaceing setTimeout(....,1000) with $(....).
//
setTimeout(function() {
    // Add polymorphic function to the contents of configuration.sentencegrammar
    for (let i in configuration.sentencegrammar) {
        if (isNaN(+i)) continue; // Not numeric
        addMethodsSgi(configuration.sentencegrammar[+i], configuration.sentencegrammar[+i].objType);
    }

    // Warn the user if they leave the webpage with unsaved changes
    $(window).on('beforeunload', function() {
        if (isDirty())
            return 'You haven\'t saved your changes.';
    });

    charset = new Charset(configuration.charSet);


    if (WirtualKeyboard) {
        WirtualKeyboard.setVisibleLayoutCodes([charset.keyboardName]);
        WirtualKeyboard.toggle('firstinput','virtualkbid');
    }

    // Configure the text editor
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

    // Show the exercise description in the text editor
    ckeditor.val(decoded_3et.desc);

    $('#quiz_tabs').tabs({ disabled: [3] }); // Set up the tabs with the "sentence unit selection" tab disabled

    origMayLocate = decoded_3et.maylocate;
    $('#maylocate_cb').prop('checked', origMayLocate);

    origSentBefore = decoded_3et.sentbefore;
    $('#sentbefore').val(origSentBefore);

    origSentAfter = decoded_3et.sentafter;
    $('#sentafter').val(origSentAfter);

    origFixedQuestions = decoded_3et.fixedquestions;
    $('#fixedquestions').val(origFixedQuestions);

    origRandomize = decoded_3et.randomize;
    $("#randomorder").prop("checked", origRandomize);
    $("#fixedorder").prop("checked", !origRandomize);

    // Monitor that #fixedquestions contains an integer
    $('#fixedquestions').on('keyup', null,
                            {err_id: "fqerror"}, // Event data
                            numberInputModifiedListener);

    //console.log('decoded_3et', decoded_3et.quizFeatures.requestFeatures);
    let order_idx:number = 1;
    let order_features: Array<string> = new Array();

    for(let i = 0; i < decoded_3et.quizFeatures.requestFeatures.length; i++){
        let rf = decoded_3et.quizFeatures.requestFeatures[i];
        order_features.push(rf.name);
        order_idx++;
    }

    panelFeatures = new PanelTemplQuizFeatures(decoded_3et.quizObjectSelection.object, decoded_3et.quizFeatures, $('#tab_features'), order_features);
    panelSentUnit = new PanelTemplQuizObjectSelector(decoded_3et.quizObjectSelection, $('#tab_sentence_units'), panelFeatures);
    panelSent     = new PanelTemplSentenceSelector(decoded_3et.sentenceSelection, $('#quiz_tabs'), $('#tab_sentences'), panelSentUnit, panelFeatures);

    $('.quizeditor').show();
},1000);
