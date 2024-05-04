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
        /*
        if($('button#next_question').is(':hidden')){
            $('button#previous_question').hide()
        }
        */

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
    // savePreviousAnswerAdvanced method
    // 
    // This method is called to record the previous answer, but it works for advanced question with
    // multiple request features.
    public savePreviousAnswerAdvanced() : void {
        // create an object maping the request feature to an array of user input
        let tracking_data : {[key:string]:any} = {};
        let previous_data = this.quiz_statistics.questions[this.currentDictIx].req_feat;
        console.log('Previous Data: ', previous_data);
        let req_features = previous_data.names; // the request features (ex. lexeme, tense, etc.)
        let nreq_features = req_features.length;
        let user_answers = previous_data.users_answer; // the user answers (ex. 'Imperfect', 'Future', etc.)
        
        // for each user answer, add it to the corresponding req feature in tracking data ex. {tense: ['Imperfect', 'Future']}
        for(let i = 0; i < user_answers.length; i++){
            let answer_i = user_answers[i];
            let feature_idx = i % nreq_features;
            let feature_i = req_features[feature_idx];
            // if the feature is already in the tracking data, append the answer to the array, otherwise add it as a new key
            if(feature_i in tracking_data){
                tracking_data[feature_i].push(answer_i);
            }
            else {
                tracking_data[feature_i] = [answer_i];
            }
        }
        // add the tracking data to the verbose data for the current question
        // ex. {0: {tense: ['Imperfect', 'Future']}}
        
        this.qd_verbose[this.currentDictIx.toString()] = tracking_data;
        

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
        // set the last action to 'next'
        this.last_action = 'next';
        // if there is a current panel question, then add this question to the quiz statistics array
        if (this.currentPanelQuestion!==null){
            console.log('currentPanelQuestion is not null...')
            this.quiz_statistics.questions.push(this.currentPanelQuestion.updateQuestionStat()); // Update Statistics    
            
            // Save the previous answer in this.quiz_dictionary and this.qd_verbose
            //let previous_answers = this.quiz_statistics.questions[this.currentDictIx].req_feat.users_answer;
            //this.savePreviousAnswer(previous_answers);
            this.savePreviousAnswerAdvanced();

        }        
        else if (quizdata.fixedquestions>0) {
            $('button#finish').attr('disabled', 'disabled');
            $('button#finishNoStats').attr('disabled', 'disabled');
        }

        // Sanity check: are there more questions?
        if (++this.currentDictIx < dictionaries.sentenceSets.length) {
            // We have another question
            
            // $('#virtualkbid').appendTo('#virtualkbcontainer'); // Move the keyboard back to its initial position
            
            // empty out the previous question data
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
            /*
            if($('button#next_question').is(':hidden')){
                $('button#previous_question').hide()
            }
            */
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

        // log the tracking data to the console
        //this.logData();
        if(this.currentDictIx.toString() in this.qd_verbose) {
            this.loadPreviousAnswerAdvanced();
        }
        
        // load previous answer from next, if this question has already been visited.
        /*
        if(this.currentDictIx > 1){
            this.loadPreviousAnswer();
        }
        */
        /*
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
        */


    }
    //------------------------------------------------------------------------------------------
    // loadPreviousAnswer method
    //
    // This method loads the most recent previously entered answer into a question. It only
    // loads data into inputshow divs not radio buttons
    //
    
    public loadPreviousAnswer() :  void {

        // empty the inputshow div this is the text area for inserting lexemes
        $('.inputshow').empty();
        
        // the string index is the key mapping indexes to previous answers
        let str_idx = this.currentDictIx.toString();

        // if the string index is not in the quiz dictionary, then the previous answer is not available
        if(!(str_idx in this.quiz_dictionary)) {
            // get the data from the prior element of quiz_statistics array
            let previous_answers = this.quiz_statistics.questions[this.currentDictIx-1].req_feat.users_answer;
            console.log('Previous Answers: ', previous_answers);
            // iterate over the previous answers and append them to the inputshow div
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
        // if the string index is in the quiz dictionary, then the previous answer is available
        else {
            console.log('PREVIOUS ANSWER AVAILABLE!!!');
            let answer_data = this.qd_verbose[str_idx];
            let req_features = Object.keys(answer_data);
            console.log('REQ FEATURES: ', req_features);
            for(let i = 0; i < req_features.length; i++) {
                let feature_i = req_features[i];
                for(let j = 0; j < answer_data[feature_i].length; j++) {
                    if(answer_data[feature_i][j].includes('Unanswered')){
                        continue;
                    }
                    else {
                        if(feature_i === 'lemma'){
                            $('.inputshow')[j].append(answer_data[feature_i][j]);
                        }
                        else if(feature_i === 'tense') {
                            console.log('TENSE: ', answer_data[feature_i][j]);
                            $(`input[name="quizitem_2"]#${answer_data[feature_i][j]}_2`).prop('checked', true);                    
                        }
                    }
                    
                }
            }
            /*
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
            */
        }



        
    }

    public savePreviousAnswer(previous_answers:string []) : void {
        this.quiz_dictionary[this.currentDictIx.toString()] = previous_answers;
    }

    public getInputType(feature_name:string): string {
        let input_type = 'radio';
        if(feature_name === 'lemma'){
            input_type = 'text';
        }
        else if(feature_name === 'raw_normalized'){
            input_type = 'text';
        }
        return input_type;

    }
    public loadPreviousAnswerAdvanced() : void {
        //console.log('Welcome to loadPreviousAnswerAdvanced()');

        let previous_data = this.qd_verbose[this.currentDictIx.toString()];
        //console.log('Previous Data: ', previous_data);
        let iter = 1;
        let n_features = Object.keys(previous_data).length;
        for(let feat in previous_data){
            //console.log('Feature: ', feat);
            let user_answer = previous_data[feat];
            let input_type = this.getInputType(feat);
            if(input_type === 'text'){
                for(let i = 0; i < user_answer.length; i++){
                    let user_answer_i = user_answer[i];
                    //console.log('User Answer: ', user_answer_i);
                    if(user_answer_i.includes('Unanswered')){
                        continue;
                    }
                    else {
                        $('.inputshow')[i].append(user_answer_i);
                    }
                }
            }
            else {
                // get the number of sub parts in the question
                let n_parts = user_answer.length;

                // for each user anser, check the corresponding radio button
                for(let i = 0; i < n_parts; i++) {
                    let current_answer = user_answer[i];
                    //console.log('User Answer: ', current_answer);

                    if(current_answer.includes('Unanswered')){
                        continue;
                    }
                    let part_id = iter + (i*n_features);
                    let query = `input[name="quizitem_${part_id}"]#${current_answer}_${part_id}`;
                    $(query).prop('checked', true);
                }
            }

            iter = iter + 1;
        }

    }
    //------------------------------------------------------------------------------------------
    // saveCurrentAnswer method
    // 
    // save an answer after pressing 'Previous' button
    public saveCurrentAnswer() : void {
        let panel = this.currentPanelQuestion.updateQuestionStat();
        let previous_data = panel.req_feat;
        let feature_names = previous_data.names;
        let user_answers = previous_data.users_answer;
        let nreq_features = feature_names.length;
        let tracking_data : {[key:string]:any} = {};

        for(let i = 0; i < user_answers.length; i++){
            let answer_i = user_answers[i];
            let feature_idx = i % nreq_features;
            let feature_i = feature_names[feature_idx];
            // if the feature is already in the tracking data, append the answer to the array, otherwise add it as a new key
            if(feature_i in tracking_data){
                tracking_data[feature_i].push(answer_i);
            }
            else {
                tracking_data[feature_i] = [answer_i];
            }
        }
        console.log('Tracking Data: ', tracking_data);
        this.qd_verbose[this.currentDictIx.toString()] = tracking_data;

        this.logData();

    }


    public previousQuestion(first:boolean) : void {
        if(this.last_action === 'previous'){
            console.log('You should not have deleted!!!');
        }
        this.saveCurrentAnswer();
        




        //let user_answers = panel.req_feat.users_answer;
        //console.log(user_answers);

        console.log('==================================================')

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
            /*
            if($('button#next_question').is(':hidden')){
                $('button#previous_question').hide()
            }
            */
            this.currentDictIx = this.currentDictIx - 1;
            this.loadPreviousAnswerAdvanced();                    
        }

        // delete the last saved version of the question in case the user updates the answer
        this.quiz_statistics.questions.splice(this.currentDictIx, 1);
     
        //this.logData();
        this.last_action = 'previous';
        //this.saveCurrentAnswerAdvanced();
        console.log('FROM PREVIOUS QUESTION!!!');
        //this.logData();
        /*
        this.loadPreviousAnswer();
        console.log('Quiz Dictionary: ', this.quiz_dictionary);
        
        
        this.logData();
        */
        
        
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
