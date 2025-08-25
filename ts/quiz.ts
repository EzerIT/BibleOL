// -*- js -*-
// Copyright © 2018 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk

// Code for displaying an exercise.


/// <reference path="statistics.ts" />

declare let WirtualKeyboard : any;

// Define the type for the dictionary
interface StringArrayDictionary {
    [key: string]: string[];
}

interface StatisticsDictionary {
    [key: string]: QuestionStatistics;
}

// Declare an empty dictionary with the defined type
let myDictionary: StringArrayDictionary = {};
let featDictionary: StringArrayDictionary = {};
let statDictionary: StatisticsDictionary = {};

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

        if(this.exam_mode == true){
            $(`#timer`).hide();
        }
        else {
            $(`#prev_question`).hide();
        }
        
        
        // Set up handlers for the buttons
        $('button#next_question').on('click', () => this.nextQuestion(false));
        $('button#prev_question').on('click', () => this.prevQuestion());
        $('button#finish').on('click', () => this.finishQuiz(true));
        $('button#finishNoStats').on('click', () => this.finishQuiz(false));
    }

    public logInput():void {
        // populate the panel with the old answer from the previous question
        let previous_data = this.quiz_statistics.questions[this.currentDictIx].req_feat;
        let user_answers = previous_data.users_answer; // (ex. 'Imperfect', 'Future', etc.)
        console.log("Input Log: ");
        console.log(user_answers);

    }

    public logMyDictionary():void {
        console.log("IX: ", this.currentDictIx);
        console.log("{");
        for (let key in myDictionary) {
            console.log("\t" + key + " : " +  "[" + myDictionary[key] + "]");
        }
        console.log("}");
    }

    public logFeatDictionary():void {
        console.log("IX: ", this.currentDictIx);
        console.log("{");
        for (let key in featDictionary) {
            console.log("\t" + key + " : " +  "[" + featDictionary[key] + "]");
        }
        console.log("}");
    }

    public logStatDictionary():void {
        console.log("IX: ", this.currentDictIx);
        console.log(statDictionary);
    }



    //------------------------------------------------------------------------------------------
    // prevQuestion method
    //
    // Called whenever the user clicks 'Previous',
    // Replaces the current quiz question with the previous one, if any.
    //
    //
    public prevQuestion():void {

        
        let qstat = this.currentPanelQuestion.updateQuestionStat();
        let previous_data = qstat.req_feat;
        let user_answers = previous_data.users_answer; // (ex. 'Imperfect', 'Future', etc.)
        myDictionary[this.currentDictIx.toString()] = user_answers;
        let feat_names = previous_data.names;
        featDictionary[this.currentDictIx.toString()] = feat_names;
        statDictionary[this.currentDictIx.toString()] = qstat;

        // clear current question
        $('#textarea').empty();
        $('#quizcontainer').empty();
        $('.quizcard').empty();


        // decrease current index
        --this.currentDictIx;

        // Get text for previous question
        let currentDict : Dictionary = new Dictionary(dictionaries,this.currentDictIx,quizdata);

        // Create a panel for the next question
        this.currentPanelQuestion = new PanelQuestion(quizdata, currentDict, this.exam_mode);
        
        let number_subquestions = this.currentPanelQuestion.getSubQuizMax();
        
        if(number_subquestions == 1) {
            $('button#next_question').show();
            //$('button#finish').show();
            //$('button#finishNoStats').show();
        }
            

        if (this.currentDictIx+1 <= dictionaries.sentenceSets.length) {
            // enable the 'Next' button
            $('button#next_question').removeAttr('disabled');
            //$('button#finish').attr('disabled', 'disabled');
            $('button#finish').addClass('not-clickable');
            $('button#finishNoStats').attr('disabled', 'disabled');
        }

        // if the current index is zero, hide the previous button
        let first : boolean = (this.currentDictIx == 0) ? true : false;
        if(first == true)
            $('#prev_question').hide();
        else
            $('#prev_question').show();

        //console.log(this.currentDictIx);
        //this.logInput();
        
        // update the description 
        $('#quizdesc').html(quizdata.desc);
        $('#quizdesc').find('a').attr('target','_blank'); // Force all hyperlinks in description to open a new browser tab
        
        // update the progress bar
        if (supportsProgress)
            $('progress#progress').attr('value',this.currentDictIx+1).attr('max',dictionaries.sentenceSets.length);
        else
            $('div#progressbar').progressbar({value: this.currentDictIx+1, max: dictionaries.sentenceSets.length});
        
        $('#progresstext').html((this.currentDictIx+1)+'/'+dictionaries.sentenceSets.length);
        this.loadAnswer();
        this.logStatDictionary();
        //this.logMyDictionary();
        //this.logFeatDictionary();

    }

    public populateRadio(answer_idx:number, current_answer:string):void {
        if(!(current_answer).includes('Unanswered')) {
            let radio_elem = `input[name="quizitem_${answer_idx+1}"]#${current_answer}_${answer_idx+1}`;
            $(radio_elem).prop('checked', true);
        }
    }
    public populateVocab(nvocab:number, current_answer:string):void {
        if(!(current_answer).includes('Unanswered')) {
            let vocab_elem = $('input[type="text"]')[nvocab] as HTMLInputElement;
            vocab_elem.value = current_answer;
        }
    }
    public populateText(ntext:number, current_answer:string): void {
        if(!(current_answer).includes('Unanswered')) {
            let text_elem = $('.inputshow')[ntext] as HTMLInputElement;
            text_elem.append(current_answer);
        }
    }

    public checkQuestionVisited():boolean{
        let hasVisited = false;
        let visited_indices = Object.keys(myDictionary);
        for(let i = 0; i < visited_indices.length; i++) {
            if(visited_indices[i] == this.currentDictIx.toString()) {
                hasVisited = true;
                break;
            }
        }
        return hasVisited;
    }

    public loadAnswer(): void {
        // check if this question has been visited before, if it has been visited, then we need to load the previous user input
        let hasVisited = this.checkQuestionVisited();
        
        let inputTypes = this.currentPanelQuestion.getInputTypes();
        console.log("Input Types: ", inputTypes);
        if(hasVisited == true) {
            let answer_to_load = myDictionary[this.currentDictIx.toString()]
            let request_features = featDictionary[this.currentDictIx.toString()];
            let nreq_features = request_features.length;
            let nvocab = 0;
            let ntext = 0;
            for(let i = 0; i < answer_to_load.length; i++) {
                let current_answer = answer_to_load[i];
                let feature_idx = i % nreq_features;
                let current_feature = request_features[feature_idx];
                let feature_type = inputTypes[feature_idx];
                if(feature_type == "vocab") {
                    this.populateVocab(nvocab, current_answer);
                    nvocab++;
                }
                else if(feature_type == "text") {
                    this.populateText(ntext, current_answer);
                    ntext++;
                }
                else {
                    this.populateRadio(i, current_answer);
                }
            }
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
        // if this question is first, hide the previous question button
        if(first == true) {
            $('#prev_question').hide();
        }
        else {
            if(this.exam_mode == true)
                $('#prev_question').show();
        }

        
        if (this.currentPanelQuestion!==null) {
            let qstat = this.currentPanelQuestion.updateQuestionStat();
            //console.log(qstat);
            // Update statistics.
            //this.quiz_statistics.questions.push(qstat);

            if(first == false) {
                console.log(this.currentDictIx);
                //this.logInput();
                let previous_data = qstat.req_feat;
                let user_answers = previous_data.users_answer; // (ex. 'Imperfect', 'Future', etc.)
                console.log("-----------------------------------------------");
                console.log("UPDATING:  ");
                console.log(user_answers);
                console.log(previous_data);
                console.log(this.quiz_statistics.questions);
                console.log("-----------------------------------------------");
                myDictionary[this.currentDictIx.toString()] = user_answers;  
                let feat_names = previous_data.names;
                featDictionary[this.currentDictIx.toString()] = feat_names; 
                statDictionary[this.currentDictIx.toString()] = qstat; 
            }
        }
        else if (quizdata.fixedquestions>0) {
            //$('button#finish').attr('disabled', 'disabled');
            $('button#finish').addClass('not-clickable');
            $('button#finishNoStats').attr('disabled', 'disabled');
        }


        
        
        // Sanity check: are there more questions?
        if (++this.currentDictIx < dictionaries.sentenceSets.length) {
            // We have another question

            // $('#virtualkbid').appendTo('#virtualkbcontainer'); // Move the keyboard back to its initial position
            $('#textarea').empty();
            $('#quizcontainer').empty();
            $('.quizcard').empty();
            //throw new Error("This is a forced error.");
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
                $('button#finish').removeClass('not-clickable');
                $('button#finishNoStats').removeAttr('disabled');
            }


            this.loadAnswer();
            //this.logMyDictionary();
            //this.logFeatDictionary();
            this.logStatDictionary();



        }
        else
            alert('No more questions');

        if(this.exam_mode == true) {
            if ($('#progress').val() < $('#progress').attr('max')) {
                $('#finish').addClass('not-clickable')
            } else {
              $('#finish').removeClass('not-clickable');
            }
        }
        util.FollowerBox.resetCheckboxCounters(); // Reset counters in preparation for the following trigger() call
        $('#grammarbuttongroup input:enabled:checked').trigger('change'); // Make sure grammar is displayed for relevant checkboxes

        // Move to top of the question
        $('html, body').animate({
            scrollTop: first ? 0 : $('#myview').offset().top - 5 // -5 to add take 5 additional px above myview
        }, 50);

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
            if (this.currentPanelQuestion===null) {
                alert('System error: No current question panel');
            }
            else {
                //this.quiz_statistics.questions.push(this.currentPanelQuestion.updateQuestionStat());
                // update the final question to statistics dictionary
                let qstat = this.currentPanelQuestion.updateQuestionStat()
                statDictionary[this.currentDictIx.toString()] = qstat;

                // package the statDictionary into the questions array
                for(const index in statDictionary) {
                    if(statDictionary.hasOwnProperty(index)) {
                        this.quiz_statistics.questions.push(statDictionary[index]);
                    }
                }
            }
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
