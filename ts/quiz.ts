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
    private exam_mode            : boolean;              // Are we running an exam?
    private last_action          : string = 'next';      // Last action taken by the user
    private quiz_dictionary      : { [key: string]: any } = {}; // a dictionary tracking the previous answer data
    private qd_verbose           : {[key:string]:any} = {}; // a dictionary tracking the verbose data for each question
    //------------------------------------------------------------------------------------------
    // Constructor method
    //
    // Initializes the object and sets up handlers for the buttons.
    //
    // Parameter:
    //     qid: The server's identification of statistics for this exercise execution.
    //
    constructor(qid : number, inExam : boolean) {
        console.log('CONSTRUCTION!!!');
        this.quiz_statistics = new QuizStatistics(qid);
        this.exam_mode = inExam;

        // Set up handlers for the buttons
        $('button#next_question').on('click', () => this.nextQuestion(false));
        $('button#previous_question').on('click', () => this.previousQuestion(false));
        if($('button#next_question').is(':hidden')){
            $('button#previous_question').hide()
        }

        $('button#finish').on('click', () => this.finishQuiz(true));
        $('button#finishNoStats').on('click', () => this.finishQuiz(false));
    }
    public logData() : void {
        console.log('Quiz Statistics: ', this.quiz_statistics.questions);
        console.log('this.currentDictIx: ', this.currentDictIx);
        console.log('Last Action: ', this.last_action);
        console.log('Quiz Dictionary: ', this.quiz_dictionary);
        console.log('QD Verbose: ', this.qd_verbose);
        console.log('--------------------------------------------------------------------------')
    }

    
    //------------------------------------------------------------------------------------------
    // nextQuestion method
    //
    // Called at the start of an exercise and whenever the user clicks 'Next',
    // Replaces the current quiz question with the next one, if any.
    //
    // Parameter:
    //    first: True for the first question in a quiz
    //
    public nextQuestion(first : boolean) : void {
        this.last_action = 'next';
        if (this.currentPanelQuestion!==null){
            this.quiz_statistics.questions.push(this.currentPanelQuestion.updateQuestionStat()); // Update Statistics    
            
            // Save previous answer
            let previous_answers = this.quiz_statistics.questions[this.currentDictIx].req_feat.users_answer;
            this.savePreviousAnswer(previous_answers);

            let tracking_data : {[key:string]:any} = {};

            let previous_data = this.quiz_statistics.questions[this.currentDictIx].req_feat;
            let req_features = previous_data.names;
            let nreq_features = req_features.length;
            let user_answers = previous_data.users_answer;
            for(let i = 0; i < user_answers.length; i++){
                let answer_i = user_answers[i];
                let feature_idx = i % nreq_features;
                let feature_i = req_features[feature_idx];
                if(feature_i in tracking_data){
                    tracking_data[feature_i].push(answer_i);
                }
                else {
                    tracking_data[feature_i] = [answer_i];
                }
            }
            this.qd_verbose[this.currentDictIx.toString()] = tracking_data;
            
        }        
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
            if($('button#next_question').is(':hidden')){
                $('button#previous_question').hide()
            }
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

        // Move to top of the question
        $('html, body').animate({
            scrollTop: first ? 0 : $('#myview').offset().top - 5 // -5 to add take 5 additional px above myview
        }, 50);
        this.logData();
        
        // load previous answer from next
        let str_idx = this.currentDictIx.toString();
        if(str_idx in this.quiz_dictionary) {
            console.log('STRIDX IN QUIZ DICTIONARY!!!');   
            let previous_answers = this.quiz_dictionary[str_idx];
            console.log('PREVIOUS ANSWERS: ', previous_answers); 
            for(let i = 0; i < previous_answers.length; i++) {
                let answer_i = previous_answers[i];
                if(answer_i.includes('Unanswered')){
                    continue;
                }
                else {
                    $('.inputshow')[i].append(answer_i);
                }
            }
        }


    }
    // this method loads the previously entered answer when a user clicks the previous button
    public loadPreviousAnswer() :  void {
        // empty the inputshow div
        $('.inputshow').empty();
        
        let str_idx = this.currentDictIx.toString();

        if(!(str_idx in this.quiz_dictionary)) {
            let previous_answers = this.quiz_statistics.questions[this.currentDictIx].req_feat.users_answer;
            for(let i = 0; i < previous_answers.length; i++) {
                let answer_i = previous_answers[i];
                if(answer_i.includes('Unanswered')){
                    continue;
                }
                else {
                    $('.inputshow')[i].append(answer_i);
                }
            }
            this.savePreviousAnswer(previous_answers);
        }
        else {
            console.log('IN HERE!!!');
            let previous_answers = this.quiz_dictionary[str_idx];
            for(let i = 0; i < previous_answers.length; i++) {
                let answer_i = previous_answers[i];
                if(answer_i.includes('Unanswered')){
                    continue;
                }
                else {
                    $('.inputshow')[i].append(answer_i);
                }
            }
            this.savePreviousAnswer(previous_answers);
        }



        
    }

    public savePreviousAnswer(previous_answers:string []) : void {
        this.quiz_dictionary[this.currentDictIx.toString()] = previous_answers;
    }
    
    public previousQuestion(first:boolean) : void {
        if(this.last_action === 'previous'){
            console.log('You should not have deleted!!!');
        }
        //console.log('Welcome to previousQuestion()!!!');
        if(this.currentDictIx > 0){
             
            // enable the next question button if it is disabled
            $('button#next_question').removeAttr('disabled');

            // get the text for the previous question
            let previousDict : Dictionary = new Dictionary(dictionaries, this.currentDictIx - 1, quizdata);

            // empty current question data
            $('#textarea').empty();
            $('#quizcontainer').empty();
            $('.quizcard').empty();

            // update the progress bar
            $('progress#progress').attr('value',this.currentDictIx).attr('max',dictionaries.sentenceSets.length);
            
            // Create a panel for the next question
            this.currentPanelQuestion = new PanelQuestion(quizdata, previousDict, this.exam_mode);
            if($('button#next_question').is(':hidden')){
                $('button#previous_question').hide()
            }
            this.currentDictIx = this.currentDictIx - 1;                    

        }
        this.loadPreviousAnswer();
        console.log('Quiz Dictionary: ', this.quiz_dictionary);
        this.quiz_statistics.questions.splice(this.currentDictIx, 1);
        
        this.last_action = 'previous';
        this.logData();
        



        
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
                    else {
                        $.get(site_url + 'statistics/update_exam_quiz_stat?examid=' + $('#exam_id').html() + '&quizid=' + $('#quiz_id').html() + '&exercise_lst=' + $('#exercise_lst').html())
                            .done(() => {
                                if ($('#exercise_lst').html()) {
                                    var exercise_lst = $('#exercise_lst').html().split("~");
                                    var next_quiz = exercise_lst.shift();
                                    window.location.replace(site_url + 'text/show_quiz?quiz=' + next_quiz + '&count=10&examid=' + $('#exam_id').html() + '&exercise_lst=' + exercise_lst.join("~"));
                                }
                                else
                                    window.location.replace(site_url + 'exams/exam_done');
                            })
                            .fail(function (jqXHR, textStatus, errorThrow) {
                                $('#textcontainer')
                                    .removeClass('textcontainer-background')
                                    .addClass('alert alert-danger')
                                    .html(`<h1>${localize('error_response')}</h1><p>${errorThrow}</p>`);
                            });
                    }
                })
                .fail((jqXHR: JQueryXHR, textStatus: string, errorThrow: string) => {
                    $('#textcontainer')
                        .removeClass('textcontainer-background')
                        .addClass('alert alert-danger')
                        .html(`<h1>${localize('error_response')}</h1><p>${errorThrow}</p>`);
                });
        }
    }
}
