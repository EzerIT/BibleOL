// -*- js -*-
// Copyright © 2018 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk

// Code for displaying an exercise.


/// <reference path="statistics.ts" />

declare let WirtualKeyboard : any;

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
    private exam_mode            : boolean;              // Are we running an exam?

    //------------------------------------------------------------------------------------------
    // Constructor method
    //
    // Initializes the object and sets up handlers for the buttons.
    //
    // Parameter:
    //     qid: The server's identification of statistics for this exercise execution.
    //
    constructor(qid : number, inExam : boolean) {
        this.quiz_statistics = new QuizStatistics(qid);
        this.exam_mode = inExam;

        // Set up handlers for the buttons
        $('button#next_question').on('click', () => this.nextQuestion());
        $('button#finish').on('click', () => this.finishQuiz(true));
        $('button#finishNoStats').on('click', () => this.finishQuiz(false));
    }

    //------------------------------------------------------------------------------------------
    // nextQuestion method
    //
    // Called at the start of an exercise and whenever the user clicks 'Next',
    // Replaces the current quiz question with the next one, if any.
    //
    public nextQuestion() : void {
        const timeBeforeHbOpen  : number = 600000; // (600 seconds) Time before heartbeat dialog is shown to user
        const timeBeforeHbClose : number = 28000;  // (28 seconds) Time before heartbeat dialog is automatically closed

        // Move to top of the question
        $('html, body').animate({
            scrollTop: $('#myview').offset().top - 5 // -5 to add take 5 additional px above myview
        }, 50);


        //--------------------------------------------------------------------------------
        // monitorUser function
        //
        // Sets up timeout mechanism to monitor student activity.
        //

        let heartbeatDialog = $('#heartbeat-dialog');

        let monitorUser = () => {
            // Reset timers from previous question
            window.clearTimeout(this.tHbOpen);
            window.clearTimeout(this.tHbClose);
            
            // After 10 minutes, show the heartbeatDialog
            this.tHbOpen = window.setTimeout(() => {
                heartbeatDialog.modal('show');

                // After 28 seconds close the heartbeatDialog and remove the 'Next question' button
                this.tHbClose = window.setTimeout(() => {
                    heartbeatDialog.modal('hide');
                    $('#next_question').fadeOut();
                }, timeBeforeHbClose);
                
            }, timeBeforeHbOpen);
        }

        $('#heartbeat-dialog-go-on').on('click', (event : any) => {
            heartbeatDialog.modal('hide');
            window.setTimeout(monitorUser, 0); // Rerun monitorUser() asynchroneously
        });

        monitorUser();

        if (this.currentPanelQuestion!==null)
            // Update statistics.
            this.quiz_statistics.questions.push(this.currentPanelQuestion.updateQuestionStat());

        else if (quizdata.fixedquestions>0) {
            $('button#finish').attr('disabled', 'disabled');
            $('button#finishNoStats').attr('disabled', 'disabled');
        }

        // Sanity check: are there more questions?
        if (++this.currentDictIx < dictionaries.sentenceSets.length) {
            // We have another question
            
            // $('#virtualkbid').appendTo('#virtualkbcontainer'); // Move the keyboard back to its initial position
            $('#textarea').empty();
            $('#quizcontainer').empty();
            $('.quizcard').empty();
     
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
            this.currentPanelQuestion = new PanelQuestion(quizdata, currentDict, this.exam_mode);
            
            if (this.currentDictIx+1 === dictionaries.sentenceSets.length) {
                // This is the last question, disable the 'Next' button
                $('button#next_question').attr('disabled', 'disabled');
                $('button#finish').removeAttr('disabled');
                $('button#finishNoStats').removeAttr('disabled');
            }
        }
        else
            alert('No more questions');

        util.FollowerBox.resetCheckboxCounters(); // Reset counters in preparation for the following trigger() call
        $('#grammarbuttongroup input:enabled:checked').trigger('change'); // Make sure grammar is displayed for relevant checkboxes
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
        if (quizdata.quizid == -1) { // User not logged in
            if (this.exam_mode)
                window.location.replace(site_url + 'exam/active_exams');
            else
                window.location.replace(site_url + 'text/select_quiz'); // Go to quiz selection
        }
        else {
            if (this.currentPanelQuestion===null)
                alert('System error: No current question panel');
            else
                this.quiz_statistics.questions.push(this.currentPanelQuestion.updateQuestionStat());

            this.quiz_statistics.grading = gradingFlag;

            $('#textcontainer').html('<p>' + localize('sending_statistics') + '</p>');
            
            // Send statistics to server
            $.post(
                site_url + 'statistics/update_stat',
                this.quiz_statistics
            )
                .done(() => {
                    if (!this.exam_mode)
                        window.location.replace(site_url + 'text/select_quiz'); // Go to quiz selection
                })
                .fail((jqXHR: JQueryXHR, textStatus: string, errorThrow: string) => {
                    $('#textcontainer')
                        .removeClass('textcontainer-background')
                        .addClass('alert alert-danger')
                        .html(`<h1>${localize('error_response')}</h1><p>${errorThrow}</p>`);
                });
            if (this.exam_mode) {
                $.get(site_url + 'statistics/update_exam_quiz_stat?examid=' + $('#exam_id').html() + '&quizid=' + $('#quiz_id').html() + '&exercise_lst=' + $('#exercise_lst').html())
                    .done(() => {
                        if ($('#exercise_lst').html()) {
                            var exercise_lst = $('#exercise_lst').html().split("~");
                            var next_quiz = exercise_lst.shift();
                            return window.location.replace(site_url + 'text/show_quiz?quiz=' + next_quiz + '&count=10&examid=' + $('#exam_id').html() + '&exercise_lst=' + exercise_lst.join("~"));
                        }
                        else {
                            return window.location.replace(site_url + 'exams/exam_done');
                        }
                    })
                    .fail(function (jqXHR, textStatus, errorThrow) {
                        $('#textcontainer')
                            .removeClass('textcontainer-background')
                            .addClass('alert alert-danger')
                            .html(`<h1>${localize('error_response')}</h1><p>${errorThrow}</p>`);
                    });
            }
        }
    }
}
