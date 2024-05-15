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
    private answers_exposed      : {[key:string]:any} = {}; // a dictionary tracking the answers exposed by the user using 'Show Answer' or 'Check Answer'

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
    public savePreviousAnswerAdvanced(n_parts:number) : void {
        // there are some multiple choice features that require special treatment
        let mc_features = ['g_prs_utf8', 'g_word_nocant_utf8'];


        // create an object maping the request feature to an array of user input
        let tracking_data : {[key:string]:any} = {};
        let previous_data = this.quiz_statistics.questions[this.currentDictIx];
        let request_features = previous_data.req_feat;
        //console.log('++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++');
        //console.log('Previous Data: ', previous_data);
        //console.log('++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++');
        let names = request_features.names; // the request features (ex. lexeme, tense, etc.)
        let nreq_features = names.length;
        let user_answers = request_features.users_answer; // the user answers (ex. 'Imperfect', 'Future', etc.)
        let correct_answers = request_features.correct_answer;

        /*
        console.log('++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++');
        console.log(this.qd_verbose);
        console.log('Names: ', names);
        console.log('n: ', nreq_features);
        console.log('User Answers: ', user_answers);
        console.log('Correct Answers: ', correct_answers);
        if(n_parts * names.length != user_answers.length) {
            console.log('Null feature');
        }
        console.log('++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++');
        */

        // for each user answer, add it to the corresponding req feature in tracking data ex. {tense: ['Imperfect', 'Future']}
        for(let i = 0; i < user_answers.length; i++){
            let answer_i = user_answers[i];
            let feature_idx = i % nreq_features;
            let feature_i = names[feature_idx];

            if(this.checkForTarget(feature_i, mc_features) === true) {
                console.log('Multiple Choice Feature');
            }
            
            // if the feature is already in the tracking data, append the answer to the array, otherwise add it as a new key
            if(feature_i in tracking_data) {
                tracking_data[feature_i].push(answer_i);
            }
            else {
                tracking_data[feature_i] = [answer_i];
            }
            

            
        }

        // add the tracking data to the verbose data for the current question
        // ex. {0: {tense: ['Imperfect', 'Future']}}
        this.qd_verbose[this.currentDictIx.toString()] = tracking_data;

        // check if there is a null feature
        /*
        for(let key in this.qd_verbose) {
            let child_object = this.qd_verbose[key];
            for(let child_key in child_object) {
                let child_answers = child_object[child_key];
            }
        }
        */

        /*
        console.log('=========================================================================================');
        let n_children_keys = 0; // if this variable does not remain constant, then there is a null feature
        let n_changes = 0; // track the number of times n_children_keys changes, if this is more than one, then there is a null feature
        let is_null = false;
        for(let key_index in this.qd_verbose) {
            if(this.qd_verbose.hasOwnProperty(key_index)) {
                let child_object = this.qd_verbose[key_index];
                let n = Object.keys(child_object).length; 
                console.log('+++++++++--' + key_index + '--+++++++++');
                console.log('n: ', n);
                console.log(child_object);
                if(n_children_keys !== n) {

                    n_changes = n_changes + 1;
                }
                

            }
        }
        console.log('=========================================================================================');
        */
        
    }

    //------------------------------------------------------------------------------------------
    // saveAnswersExposed method
    //
    // record whether or not a given answer has been exposed with 'Show Answer' or 'Check Answer',
    // exposed answers are automatically incorrect
    //
    public saveAnswersExposed() : void {
        // Check if Show Answer has been pressed

        // get the show_anyway_button from the alert modal
        let show_anyway_button = document.getElementById('show_anyway_button');
        if(show_anyway_button != null){
            // when 'Show Anyway' is clicked, record the answer as exposed
            show_anyway_button.addEventListener('click', () => {
                let sub_index = this.currentPanelQuestion.getSubQuizIndex(); // the current sub index
                let sub_quiz_max = this.currentPanelQuestion.getSubQuizMax(); // the number of subparts in the question
                let currentDictIx_str = this.currentDictIx.toString(); // the current index

                // if the current index is not in the answers_exposed dictionary, add it
                if(!(currentDictIx_str in this.answers_exposed)){
                    let is_exposed = new Array(sub_quiz_max).fill(false);
                    is_exposed[sub_index] = true;
                    this.answers_exposed[currentDictIx_str] = is_exposed;
                } // otherwise, update the sub index to be exposed
                else {
                    this.answers_exposed[currentDictIx_str][sub_index] = true;
                }

            });
        }

        // Check if Check Answer has been pressed
        let check_anyway_button = document.getElementById('check_anyway_button');
        if(check_anyway_button != null){
            // when 'Check Anyway' is clicked, record the answer as exposed
            check_anyway_button.addEventListener('click', () => {
                let sub_index = this.currentPanelQuestion.getSubQuizIndex(); // the current sub index
                let sub_quiz_max = this.currentPanelQuestion.getSubQuizMax(); // the number of subparts in the question
                let currentDictIx_str = this.currentDictIx.toString(); // the current index

                // if the current index is not in the answers_exposed dictionary, add it
                if(!(currentDictIx_str in this.answers_exposed)){
                    let is_exposed = new Array(sub_quiz_max).fill(false);
                    is_exposed[sub_index] = true;
                    this.answers_exposed[currentDictIx_str] = is_exposed;
                } // otherwise, update the sub index to be exposed
                else {
                    this.answers_exposed[currentDictIx_str][sub_index] = true;
                }

            });
        }
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
            this.quiz_statistics.questions.push(this.currentPanelQuestion.updateQuestionStat()); // Update Statistics    
            
            // Save the previous answer in this.quiz_dictionary and this.qd_verbose
            //let previous_answers = this.quiz_statistics.questions[this.currentDictIx].req_feat.users_answer;
            //this.savePreviousAnswer(previous_answers);
            let number_of_parts = this.currentPanelQuestion.getSubQuizMax();
            this.savePreviousAnswerAdvanced(number_of_parts);
            this.logData();

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
        
        this.saveAnswersExposed();
        //this.logData();


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
            let answer_data = this.qd_verbose[str_idx];
            let req_features = Object.keys(answer_data);
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

    public checkForTarget(target:string, series:string[]):boolean {
        let found = false;
        for(let i = 0; i < series.length; i++) {
            if(target === series[i]){
                found = true;
                break;
            }
        }
        return found;
    }

    public getInputType(feature_name:string): string {
        let vocab_features = ['amharic', 'danish', 'dutch', 'english', 'portuguese', 'spanish', 'swahili'];
        let text_features = ['lemma', 'raw_lemma', 'normalized', 'raw_normalized'];

        let input_type = 'radio';
        if(this.checkForTarget(feature_name, text_features) === true) {
            input_type = 'text';
        }
        else if(this.checkForTarget(feature_name, vocab_features) === true) {
            input_type = 'vocab';
        }

        return input_type;

    }

    public populateVocabAnswers(n_parts:number, vocab_features:string []): void {
        let previous_data = this.qd_verbose[this.currentDictIx.toString()];
        let counter = 0
        for(let i = 0; i < n_parts; i++) {
            for(let j = 0; j < vocab_features.length; j++) { 
                let vocab_elem = $('input[type="text"]')[counter] as HTMLInputElement;
                let user_answers = previous_data[vocab_features[j]];
                counter = counter + 1;
                if(user_answers[i].includes('Unanswered')) {
                    // do not display the answer
                    continue;
                }
                else {
                    // do display the answer
                    vocab_elem.value = user_answers[i];
                }
                
            }
        }
    
    }
    
    public populateTextAnswers(n_parts:number, text_features:string []): void {
        let previous_data = this.qd_verbose[this.currentDictIx.toString()];
        let counter = 0;
        for(let i = 0; i < n_parts; i++) {
            for(let j = 0; j < text_features.length; j++) {
                let user_answers = previous_data[text_features[j]];
                let user_answer_i = user_answers[i];
                if(!(user_answer_i.includes('Unanswered'))) {
                    $('.inputshow')[counter].append(user_answer_i);    
                }
                counter = counter + 1;
                
            }
        }
    }
    


    public populateRadioAnswers(n_parts:number, radio_features:string []): void {
        let iter = 1;
        let previous_data = this.qd_verbose[this.currentDictIx.toString()];
        let n_features = Object.keys(previous_data).length;
        for(let feature in previous_data) {
            let user_answers = previous_data[feature];
            if(this.checkForTarget(feature, radio_features) === true) {
                for(let i = 0; i < n_parts; i++) {
                    let current_answer = user_answers[i];
                    if(!(current_answer).includes('Unanswered')) {
                        let part_id = iter + (i * n_features);
                        let radio_elem = `input[name="quizitem_${part_id}"]#${current_answer}_${part_id}`;
                        $(radio_elem).prop('checked', true);
                    }                
                }
            }
            iter = iter + 1;
        }
    }

    public populateCheckboxAnswers(checkbox_features:string[]) {
        // get the data the user has answered from the previous question
        let previous_data = this.qd_verbose[this.currentDictIx.toString()];
        let iter = 1;
        // for each feature in the question, check if its a checkbox feature, if it is, then populate the checkbox with the answer
        for(let feature in previous_data) {
            if(this.checkForTarget(feature, checkbox_features)) {
                let user_answers = previous_data[feature];
                // for each subquestion, check all the boxes the user previously entered
                for(let i = 0; i < user_answers.length; i++) {
                    // delete the parentheses from the string
                    let boxes_array_str = user_answers[i].replace("(", "").replace(")", "");
                    // make an array containing the values of all the checked boxes
                    let boxes_array = boxes_array_str.split(',');
                    for(let j = 0; j < boxes_array.length; j++) {
                        let box_j = boxes_array[j];
                        if(box_j.includes('Unanswered')) {
                            continue;
                        }
                        let box_j_id = `#${box_j}_${iter}`;
                        $(box_j_id).prop('checked', true);
                    }
                    iter = iter + 1;
                }
                

                /*
                turn this off for a second.
                for(let i = 0; i < user_answers.length; i++) {
                    let boxid = user_answers[i].replace("(", "");
                    boxid = boxid.replace(")", "");
                    boxid = '#' + boxid + `_${iter}`;
                    console.log('BOXID: ', boxid);
                    $(boxid).prop('checked', true);
                }
                iter = iter + 1;
                */
            }
        }
        console.log('-----------------------------------------');
    }

    public getInputTypeAdvanced(vanswers:any, iter:number) : string {
        let ctype = vanswers[iter].cType;
        //console.log('ctype: ', ctype);
        let input_type = 'radio';
        if(ctype === COMPONENT_TYPE.textFieldForeign) {
            input_type = 'text';
        }
        else if(ctype === COMPONENT_TYPE.textField) {
            input_type = 'vocab';
        }
        else if(ctype === COMPONENT_TYPE.textFieldWithVirtKeyboard) {
            input_type = 'vocab';
        }
        else if(ctype === COMPONENT_TYPE.checkBoxes) {
            input_type = 'checkbox';
        }
        return input_type;
    }

    public loadPreviousAnswerAdvanced() : void {
        let previous_data = this.qd_verbose[this.currentDictIx.toString()];
        let text_features : string[] = [];
        let vocab_features : string[] = [];
        let checkbox_features : string[] = [];
        let radio_features : string[] = [];
        let n_parts  =  0;
        let vanswers = this.currentPanelQuestion.getVanswers();
        let iter = 0;
        for(let feat in previous_data){
            //let input_type = this.getInputType(feat);
            //console.log('Feature: ', feat);
            let input_type = this.getInputTypeAdvanced(vanswers, iter);
            //console.log('Input Type: ', input_type);
            //console.log('========================================');
            //console.log('VANSWERS: ', vanswers[iter]);
            //let ctype = vanswers[iter].cType;
            
            
            let user_answer = previous_data[feat];
            n_parts = user_answer.length;
    
            if(input_type === 'text'){
                text_features.push(feat);
            }
            else if(input_type === 'vocab'){
                vocab_features.push(feat);
            }
            else if(input_type === 'checkbox'){
                checkbox_features.push(feat);
            }
            else {
                radio_features.push(feat);
            }
            iter += 1;
        }
    
        // Handle vocab features
        this.populateVocabAnswers(n_parts, vocab_features);
    
        // Handle text features
        this.populateTextAnswers(n_parts, text_features);
    
        // Handle radio features
        this.populateRadioAnswers(n_parts, radio_features);

        // Handle checkbox features
        this.populateCheckboxAnswers(checkbox_features);
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
        this.qd_verbose[this.currentDictIx.toString()] = tracking_data;

        //this.logData();

    }

    public enableNext() {
        // Show "Next", "GRADE task" and "SAVE outcome" buttons
        $('button#next_question').show();
        $('button#finish').show();
        $('button#finishNoStats').show();
        $('button#next_question').removeAttr('disabled');
    }

    public previousQuestion(first:boolean) : void {
        

        this.saveCurrentAnswer();

        if(this.currentDictIx > 0){
             
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
            let parts = this.currentPanelQuestion.getSubQuizMax();
            this.currentDictIx = this.currentDictIx - 1;
            this.loadPreviousAnswerAdvanced();   
            if(parts === 1) {
                this.enableNext();
            }
            else if (this.currentDictIx+1 < dictionaries.sentenceSets.length){
                this.enableNext();
            }

            
                             
        }

        // delete the last saved version of the question in case the user updates the answer
        this.quiz_statistics.questions.splice(this.currentDictIx, 1);     
        this.last_action = 'previous';

        // save the exposed status of the answers
        this.saveAnswersExposed();
        
        
        
    }

    //------------------------------------------------------------------------------------------
    // markRevealedIncorrect method
    // 
    // Update the quiz statistics given the principle that revealed answers are incorrect
    //
    
    public markRevealedIncorrect():void {
        for(let i = 0; i < this.quiz_statistics.questions.length; i++) {
            let question = this.quiz_statistics.questions[i];
            let req_feat = question.req_feat;
            let n = req_feat.names.length; // how many request features are there
            let answer_status = req_feat.users_answer_was_correct;
            let index_str = i.toString();
            if(!(index_str in Object.keys(this.answers_exposed))) {
                continue;
            }
                
            let exposed_data = this.answers_exposed[index_str];
            let offset = 0;
            for(let j = 0; j < exposed_data.length; j++) {
                let exposed_status = exposed_data[j];
                for(let k = offset; k < offset + n; k++){
                    if(answer_status[k] === true && exposed_status === true){
                        answer_status[k] = false;
                    }
                }
                offset = offset + n;
            }
            
            this.quiz_statistics.questions[i].req_feat.users_answer_was_correct = answer_status;
        }


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
        //this.logData();
        if (quizdata.quizid == -1) { // User not logged in
            if (this.exam_mode)
                window.location.replace(site_url + 'exam/active_exams');
            else
                window.location.replace(site_url + 'text/select_quiz'); // Go to quiz selection
        }
        else {
            if (this.currentPanelQuestion===null) {
                alert('System error: No current question panel');
            }
            else {
                this.quiz_statistics.questions.push(this.currentPanelQuestion.updateQuestionStat());
                this.saveAnswersExposed();
            }

            this.quiz_statistics.grading = gradingFlag;
            //this.markRevealedIncorrect();
            
            try {
                this.markRevealedIncorrect();
            } catch(error) {
                console.log(error.message);
            }
            


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
