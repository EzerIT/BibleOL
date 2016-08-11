// -*- js -*-


class ShowFeatStatistics {
    public names : string[] = [];
    public values : string[] = [];
}

class ReqFeatStatistics {
    public names : string[] = [];
    public correct_answer : string[] = [];
    public users_answer : string[] = [];
    public users_answer_was_correct : boolean[] = [];
}

class QuestionStatistics {
    public text : string;
    public location : string;
    public start_time : number; // UNIX time on client
    public end_time : number; // UNIX time on client
    public grading: number;
    public show_feat : ShowFeatStatistics = new ShowFeatStatistics();
    public req_feat : ReqFeatStatistics = new ReqFeatStatistics();
}

class QuizStatistics {
    private quizid : number;
    public questions : QuestionStatistics[] = [];
    constructor(qid : number) { this.quizid = qid; }
}

