// -*- js -*-
// Copyright © 2018 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk

// Classes for keeping track of statistics.

//****************************************************************************************************
// ShowFeatStatistics class
//
// Data about display features.
//
class ShowFeatStatistics {
    public names  : string[] = []; // The names of the display features
    public values : string[] = []; // The values of the display features
}

//****************************************************************************************************
// ReqFeatStatistics class
//
// Data about request features.
//
class ReqFeatStatistics {
    public names                    : string[]  = []; // The names of the request features
    public correct_answer           : string[]  = []; // The correct value of the request features
    public users_answer             : string[]  = []; // The values provided by the user
    public users_answer_was_correct : boolean[] = []; // Was the user's value correct?
}

//****************************************************************************************************
// QuestionStatistics class
//
// Data about a question (consisting of several question items)
//
class QuestionStatistics {
    public text       : string; // The question text
    public location   : string; // The Bible reference for the text
    public start_time : number; // UNIX time on client at the start of this question
    public end_time   : number; // UNIX time on client at the end of this question
    public show_feat  : ShowFeatStatistics = new ShowFeatStatistics(); // Display features
    public req_feat   : ReqFeatStatistics  = new ReqFeatStatistics();  // Request features
}

//****************************************************************************************************
// QuestionStatistics class
//
// Data about an exercise
//
class QuizStatistics {
    public questions : QuestionStatistics[] = []; // All the questions of the exericse
    public grading   : boolean;                   // Should the exercise be used for grading?
    public question_count : number;               // Total number of questions of the exercise/quiz (MRCN)

    //------------------------------------------------------------------------------------------
    // Constructor method
    //
    // Initializes the ID of this exercise.
    //
    // The parameter and additional class field is described below.
    //
    constructor(
        private quizid : number // The server's identification of statistics for this exercise execution
    ) {
      // Capture the ttal number of questions (MRCN)
      this.question_count = Object.keys(quizdata.monad2Id).length; // Get number of questions
    }
}
