// -*- js -*-
// Copyright © 2018 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk

// Code for displaying an exercise.


/// <reference path="statistics.ts" />

declare let VirtualKeyboard : any;

//****************************************************************************************************
// Quiz class
//
// Manages the execution of an exercise.
//
class Quiz {
    private currentDictIx        : number = -1;          // Current index in the array of dictionaries provided by the server
    private currentPanelQuestion : PanelQuestion = null; // The current question panel
    private quiz_statistics      : QuizStatistics;       // Statistics about the execution of the exercise
    private tHbOpen              : number;               // ID of timer for opening heartbeat dialog
    private tHbClose             : number;               // ID of timer for closing heartbeat dialog

    //------------------------------------------------------------------------------------------
    // Constructor method
    //
    // Initializes the object and sets up handlers for the buttons.
    //
    // Parameter:
    //     qid: The server's identification of statistics for this exercise execution.
    //
    constructor(qid : number) {
        this.quiz_statistics = new QuizStatistics(qid);

        $('#quiztab').append('<tr id="quiztabhead"></tr>');

        // Set up handlers for the buttons
        $('button#next_question').click(() => this.nextQuestion());
        $('button#finish')       .click(() => this.finishQuiz(true));
        $('button#finishNoStats').click(() => this.finishQuiz(false));
    }

    //------------------------------------------------------------------------------------------
    // nextQuestion method
    //
    // Called at the start of an exercise and whenever the user clicks 'Next',
    // Replaces the current quiz question with the next one, if any.
    //
    public nextQuestion() : void {
        const timeBeforeHbOpen  : number = 600000; // Time before heartbeat dialog is shown to user
        const timeBeforeHbClose : number = 28000;  // Time before heartbeat dialog is automatically closed

        //--------------------------------------------------------------------------------
        // monitorUser function
        //
        // Sets up timeout mechanism to monitor student activity.
        //
        let monitorUser = () => {
            // Reset timers from previous question
            window.clearTimeout(this.tHbOpen);
            window.clearTimeout(this.tHbClose);
            
            // After 10 minutes, show the heartbeatDialog
            this.tHbOpen = window.setTimeout(() => {
                heartbeatDialog.dialog('open');

                // After 28 seconds close the heartbeatDialog and remove the 'Next question' button
                this.tHbClose = window.setTimeout(() => {
                    heartbeatDialog.dialog('close');
                    $('#next_question').fadeOut();
                }, timeBeforeHbClose);
                
            }, timeBeforeHbOpen);

            let heartbeatDialog = $('<div></div>')
                .html(localize('done_practicing'))
                .dialog({
                    autoOpen: false,
                    title: localize('stop_practicing'),
                    resizable: true,
                    show: 'fade',
                    hide: 'explode',
                    height:140,
                    modal: true,
                    buttons:[{
                        text: localize('go_on'),
                        click: () => {
                            // The user clicked 'Go on' - restart the timeout mechanism
                            heartbeatDialog.dialog('close');
                            window.setTimeout(monitorUser, 0); // Rerun monitorUser asynchroneously
                        } 
                    }]
                });
        }
    
        monitorUser();

        if (this.currentPanelQuestion!==null)
            // Update statistics.
            // Set gradingFlag to true for now. It may be changed when the exercise is ended, and
            // the server only uses the final value.
            this.quiz_statistics.questions.push(this.currentPanelQuestion.updateQuestionStat(true));

        // Sanity check: are there more questions?
        if (++this.currentDictIx < dictionaries.sentenceSets.length) {
            // We have another question
            
            $('#virtualkbid').appendTo('#virtualkbcontainer'); // Move the keyboard back to its initial position
            $('#textarea').empty();
            $('#quiztab').empty();
            $('#quiztab').append('<tr id="quiztabhead"></tr>');
     
            // Get text for next question
            let currentDict : Dictionary = new Dictionary(dictionaries,this.currentDictIx,quizdata);
     
            $('#quizdesc').html(quizdata.desc);
            $('#quizdesc').find('a').attr('target','_blank'); // Force all hyperlinks in description to open a new browser tab
            if (supportsProgress)
                $('progress#progress').attr('value',this.currentDictIx+1).attr('max',dictionaries.sentenceSets.length);
            else
                $('div#progressbar').progressbar({value: this.currentDictIx+1, max: dictionaries.sentenceSets.length});
            $('#progresstext').html((this.currentDictIx+1)+'/'+dictionaries.sentenceSets.length);
            
            // Create a panel for the next question
            this.currentPanelQuestion = new PanelQuestion(quizdata, currentDict);
            
            if (this.currentDictIx+1 === dictionaries.sentenceSets.length)
                // This is the last question, disable the 'Next' button
                $('button#next_question').attr('disabled', 'disabled');

            if (quizdata.quizFeatures.useVirtualKeyboard &&
                (charset.keyboardName==='IL' || charset.keyboardName==='GR')) {
                VirtualKeyboard.setVisibleLayoutCodes([charset.keyboardName]);
                VirtualKeyboard.toggle('firstinput','virtualkbid');
            }
        }
        else
            alert('No more questions');

        util.FollowerBox.resetCheckboxCounters(); // Reset counters in preparation for the following trigger() call
        $('.grammarselector input:enabled:checked').trigger('change'); // Make sure grammar is displayed for relevant checkboxes
    }

    //------------------------------------------------------------------------------------------
    // finishQuiz method
    //
    // Called when the user clicks 'GRADE task' or 'SAVE outcome'.
    // Terminates the exercise and sends statistics to the server.
    //
    // Parameter:
    //     gradingFlag: May the statistics be used for grading the student?
    //
    private finishQuiz(gradingFlag : boolean) : void {
        if (quizdata.quizid == -1) // User not logged in
            window.location.replace(site_url + 'text/select_quiz'); // Go to quiz selection
        else {
            if (this.currentPanelQuestion===null)
                alert('System error: No current question panel');
            else
                this.quiz_statistics.questions.push(this.currentPanelQuestion.updateQuestionStat(gradingFlag));
            
            // Send statistics to server
            $('.grammarselector').empty();
            $('#textcontainer').html('<p>' + localize('sending_statistics') + '</p>');
            
            $.post(
                site_url + 'statistics/update_stat',
                this.quiz_statistics
            )
                .done(() => window.location.replace(site_url + 'text/select_quiz')) // Go to quiz selection
                .fail((jqXHR: JQueryXHR, textStatus: string, errorThrow: string) => {
                    $('#textcontainer')
                        .removeClass('textcontainer-background')
                        .addClass('alert alert-danger')
                        .html(`<h1>${localize('error_response')}</h1><p>${errorThrow}</p>`);
                });
        }
    }
}
