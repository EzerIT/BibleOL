// -*- js -*-
// Copyright © 2018 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk

// Representation of a correct answer


//****************************************************************************************************
// Answer class
//
// This class represents the correct answer to a single feature request in the current question.
//
class Answer {
    private comp         : ComponentWithYesNo; // The feature request component
    private c            : JQuery;             // The answer part of comp
    public cType        : COMPONENT_TYPE;     // The type of c
    private answerSws    : StringWithSort;     // The correct answer as a StringWithSort, when relevant
    private answerString : string;             // The correct answer as as string
    private answerArray  : string[];           // The correct answer as an array of values (only for COMPONENT_TYPE.checkBoxes)
    
    // Regular expression to find a match. If null, a full match is required. The characters %s
    // will be replaced by the user's input. If, for example, the user types "horse" and this regular expression is
    // "(.+[;,] +)?%s([,;].+)?", a match will occur if the correct answer is "horse, stallion".
    private matchRegexp : string;

    private hasAnswered        : boolean = false; // Has the user answered this question item?
    private firstAnswer        : string;          // User's first answer
    private firstAnswerCorrect : boolean;         // Is user's first answer correct?


    //------------------------------------------------------------------------------------------
    // Constructor method
    //
    // Parameters:
    //     comp: The feature request component.
    //     answerSws: The correct answer.
    //     answerString: The correct answer as a string.
    //     matchRegexp: The regular expression used to find a match, null if none is used.
    //
    constructor(comp : ComponentWithYesNo, answerSws : StringWithSort, answerString : string, matchRegexp : string) {
        this.comp         = comp;
        this.c            = comp.getComp();
        this.cType        = comp.getCompType();
        this.answerSws    = answerSws;
        this.answerString = answerString.normalize('NFC');
        this.matchRegexp  = matchRegexp;

        if (this.cType == COMPONENT_TYPE.checkBoxes) {
            if (this.answerString[0] == "(") {
                let aString : string = answerString.substr(1,answerString.length-2); // Remove surrounding '(' and ')'
                this.answerArray = aString.split(',');
            } else {
                this.answerArray = new Array(this.answerString);
            }
        }
    }

    //------------------------------------------------------------------------------------------
    // showIt method
    //
    // Displays the correct answer.
    //
    public showIt(): void {
        switch (this.cType) {
            case COMPONENT_TYPE.textField:
            case COMPONENT_TYPE.textFieldWithVirtKeyboard: {
                $(this.c).find('input').val(this.answerString);
                break;
            }

            case COMPONENT_TYPE.textFieldForeign: {
                $(this.c).find('.inputshow').text(this.answerString);
                break;
            }
            
            case COMPONENT_TYPE.comboBox: {
                // Define the correct answer
                let correctAnswer: string = this['answerSws']['internal'];
                let radios: JQuery = $(this.c).find('input');
                    
                radios.each(
                    function () {
                        let value: string = $(this).attr('value');
                        if (value === correctAnswer) {
                            $(this).prop('checked', true);
                        }
                    }
                )
                break;
            }

            case COMPONENT_TYPE.checkBoxes: {
                // Mark the correct items
                let inputs: JQuery = $(this.c).find('input');
                let xthis: Answer = this;
                inputs.each(
                    function () {
                        let value: string = $(this).attr('value');
                        $(this).prop('checked', xthis.answerArray.indexOf(value) != -1);
                    }
                );
                break;
            }
        }
    }

    //------------------------------------------------------------------------------------------
    // checkIt method
    //
    // Compares the content of the feature request component with the correct answer and sets
    // the Yes/No mark accordingly.
    //
    // Parameter:
    //     fromShowIt: True if this call comes from the user clicking "Show answer".
    //     displayIt: True if the result should be displayed on the web page
    //
    public checkIt(fromShowIt : boolean, displayIt : boolean) : void {
        if (fromShowIt) {
            // The question panel now shows the correct answers, but they were not
            // necessarily provided by the user. If the user has not committed an answer to
            // this question item, mark the question item as unanswered.
            if (!this.hasAnswered) {
                this.hasAnswered = true;
                this.firstAnswer = "*Unanswered*";
                this.firstAnswerCorrect = false;
            }
            if (displayIt)
                this.comp.setYesNo(true);
        }
        else {
            // The question panel contains the user's answers.
            // Where answers are provided, their correctness is logged.
            let userAnswer : string;  // The user's answer (perhaps slightly edited)
            let isCorrect  : boolean; // Was the user's answer correct?

            switch (this.cType) {
            case COMPONENT_TYPE.textField:
            case COMPONENT_TYPE.textFieldForeign:
            case COMPONENT_TYPE.textFieldWithVirtKeyboard:
                // Check if the string provided by the user is correct
                
                // TODO: Normalize Unicode characters (relevant only in Greek)

                if (this.cType==COMPONENT_TYPE.textFieldForeign)
                    userAnswer = ($(this.c).find('.inputshow').text() as string);
                else
                    userAnswer = ($(this.c).find('input').val() as string);
                
                userAnswer = userAnswer.normalize('NFC')
                    .trim()
                    .replace(/  +/g, ' ');          // Remove extra spaces

                if (this.matchRegexp==null) {
                    isCorrect = userAnswer==this.answerString; // Not === for one may be a number
                    if (!isCorrect)
                        isCorrect = this.answerString==='-' && userAnswer==='\u05be'; // Accept Maqaf instead of hyphen for empty answer
                }
                else {
                    // Escape all special characters in the user's answer
                    let re : RegExp = eval(this.matchRegexp.format(userAnswer.replace(/[-[\]{}()*+?.,\\^$|#\s]/g, "\\$&")));
                    isCorrect = this.answerString.match(re)!==null;
                }
                break;

            case COMPONENT_TYPE.comboBox:
                // Check if the user selected the correct option
                
                // Note: At this point we use the intenal (language independent) name for the value.
                // This is necessary in order to produce language indenpendent statistics. However,
                // this will not work correctly if there are duplicate values in the localized names.

                let selectedOption: JQuery = $(this.c).find("input:checked");
                if (selectedOption.attr('value') != null) {
                    let userAnswerSws: StringWithSort = $(this.c).find("input:checked").parent().data('sws');

                    isCorrect = userAnswerSws===this.answerSws;
                    userAnswer = userAnswerSws.getInternal();
                }
                break;

            case COMPONENT_TYPE.checkBoxes:
               // Check if the user has marked the correct checkboxes
                
                let inputs : JQuery = $(this.c).find('input');
                let xthis : Answer = this;
                isCorrect  = true;
                userAnswer = '';
                inputs.each(
                    function() {
                        let value : string = $(this).attr('value');
                        if ($(this).prop('checked')) {
                            userAnswer += value + ',';
                            isCorrect = isCorrect && xthis.answerArray.indexOf(value)!=-1;
                        }
                        else
                            isCorrect = isCorrect && xthis.answerArray.indexOf(value)==-1;
                    }
                );

                if (userAnswer!=='') {
                    // Strip final comma from userAnswer and enclose in parentheses
                    userAnswer = '(' + userAnswer.substr(0,userAnswer.length-1) + ')';
                }
                break;
            }
            
            if (userAnswer && !this.hasAnswered) {
                this.hasAnswered = true;
                this.firstAnswer = userAnswer;
                this.firstAnswerCorrect = isCorrect;
            }
            if (this.hasAnswered && displayIt)
                this.comp.setYesNo(isCorrect);
        }
    }

    //------------------------------------------------------------------------------------------
    // commitIt method
    //
    // This function is called for each question item when the question panel is being closed.
    // It checks the correctness of each question item and marks unaswered question items as such.
    //
    public commitIt() : void {
        this.checkIt(false,false);
        if (!this.hasAnswered) {
            this.hasAnswered = true;
            this.firstAnswer = "*Unanswered*";
            this.firstAnswerCorrect = false;
        }
    }

    //------------------------------------------------------------------------------------------
    // usersAnswer method
    //
    // Returns:
    //     The user's first answer to this question itme.
    //
    public usersAnswer() : string {
        return this.firstAnswer;
    }

    //------------------------------------------------------------------------------------------
    // usersAnswerWasCorrect method
    //
    // Returns:
    //     True if the user's first answer to this question item was correct.
    //
    public usersAnswerWasCorrect() : boolean {
        return this.firstAnswerCorrect;
    }

    //------------------------------------------------------------------------------------------
    // correctAnswer method
    //
    // Returns:
    //     The correct answer as a string.
    //
    public correctAnswer() : string {
        return this.answerString;
    }
}
