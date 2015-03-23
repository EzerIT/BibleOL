// -*- js -*-
/* 2013 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk */

/// @file
/// @brief Contains the Answer class.

/// This class represents an answer to a single feature request in the current question.
class Answer {
    private comp : ComponentWithYesNo;  ///< The feature request component.
    private c : JQuery;                 ///< The answer part of {@link #comp}.
    private cType : COMPONENT_TYPE;     ///< The type of {@link #c}.
    private answerSws : StringWithSort; ///< The correct answer as a StringWithSort, when relevant.
    private answerString : string;      ///< The correct answer as as string.

    /// Regular expression to find a match. If null, a full match is required. The characters %s
    /// will be replaced by the user's input. If, for example, the user types "horse" and this regular expression is
    /// <code>"(.+[;,] +)?%s([,;].+)?"</code>, a match will occur if the correct answer is "horse, stallion".
    private matchRegexp : string;

    private hasAnswered : boolean = false; ///< Has the user answered this question?
    private firstAnswer : string;       ///< User's first answer.
    private firstAnswerCorrect : boolean;  ///< Is user's first answer correct?


    /** Constructs an Answer object.
     * @param comp The feature request component.
     * @param answerSws The correct answer.
     * @param answerString The correct answer as a String.
     * @param matchRegexp The regular expression used to find a match, null if none is used.
     */
    constructor(comp : ComponentWithYesNo, answerSws : StringWithSort, answerString : string, matchRegexp : string) {
        this.comp = comp;
        this.c = comp.getComp();
        this.cType = comp.getCompType();
        this.answerSws = answerSws;
        this.answerString = answerString;
        this.matchRegexp = matchRegexp;
    }

    /// Displays the correct answer.
    public showIt() : void {
        switch (this.cType) {
        case COMPONENT_TYPE.textField:
        case COMPONENT_TYPE.textFieldWithVirtKeyboard:
            $(this.c).val(this.answerString);
            break;
        case COMPONENT_TYPE.translatedField:
            /// @todo ((TranslatedField)this.c).setText(this.answerString);
            break;
        case COMPONENT_TYPE.comboBox1:
        case COMPONENT_TYPE.comboBox2:
            $(this.c).val(this.answerSws.getInternal()).prop('selected', true);
            break;
        }
    }

    /// Compares the content of the feature request component with the correct answer and sets
    /// the Yes/No mark accordingly.
    /// @param fromShowIt True if this call comes from the user pressing "Show answer".
    public checkIt(fromShowIt : boolean) : void {
        if (fromShowIt) {
            // The question panel now shows the correct answers, but they were not
            // necessarily provided by the user. If the user has not committed an answer to
            // this question, mark the question as unanswered.
            if (!this.hasAnswered) {
                this.hasAnswered = true;
                this.firstAnswer = "*Unanswered*";
                this.firstAnswerCorrect = false;
            }
            this.comp.setYesNo(true);
        }
        else {
            // The question panel contains the user's answers.
            // Where answers are provided, their correctness is logged.
            var userAnswer : string;
            var isCorrect : boolean;

            switch (this.cType) {
            case COMPONENT_TYPE.textField:
            case COMPONENT_TYPE.textFieldWithVirtKeyboard:
                // TODO: Use Three_ET.dbInfo.charSet.converter.normalize (relevant only in Greek)

                userAnswer = $(this.c).val().trim()
                    //.replace(/\u003b/g, '\u037e')  // SEMICOLON -> GREEK QUESTION MARK
		    //.replace(/\u00b7/g, '\u0387')  // MIDDLE DOT -> GREEK ANO TELEIA
		    .replace(/\u03ac/g, '\u1f71')  // GREEK SMALL LETTER ALPHA WITH TONOS -> OXIA
		    .replace(/\u03ad/g, '\u1f73')  // GREEK SMALL LETTER EPSILON WITH TONOS -> OXIA
		    .replace(/\u03ae/g, '\u1f75')  // GREEK SMALL LETTER ETA WITH TONOS -> OXIA
		    .replace(/\u03af/g, '\u1f77')  // GREEK SMALL LETTER IOTA WITH TONOS -> OXIA
		    .replace(/\u03cc/g, '\u1f79')  // GREEK SMALL LETTER OMICRON WITH TONOS -> OXIA
		    .replace(/\u03cd/g, '\u1f7b')  // GREEK SMALL LETTER UPSILON WITH TONOS -> OXIA
		    .replace(/\u03ce/g, '\u1f7d')  // GREEK SMALL LETTER OMEGA WITH TONOS -> OXIA
		    .replace(/\u0390/g, '\u1fd3')  // GREEK SMALL LETTER IOTA WITH DIALYTIKA AND TONOS -> OXIA
		    .replace(/\u03b0/g, '\u1fe3'); // GREEK SMALL LETTER UPSILON WITH DIALYTIKA AND TONOS -> OXIA


                if (this.matchRegexp==null) {
                    isCorrect = userAnswer==this.answerString; // Not === for one may be a number
                    if (!isCorrect)
                        isCorrect = this.answerString==='-' && userAnswer==='\u05be'; // Accept Maqaf instead of hyphen
                }
                else {
                    // Escape all special characters in the user's answer
                    var re : RegExp = eval(this.matchRegexp.format(userAnswer.replace(/[-[\]{}()*+?.,\\^$|#\s]/g, "\\$&")));
                    isCorrect = this.answerString.match(re)!==null;
                }
                break;

            case COMPONENT_TYPE.translatedField:
                userAnswer = $(this.c).val().trim();
                if (this.matchRegexp==null)
                    isCorrect = userAnswer==this.answerString; // Not === for one may be a number
                else {
                    // Escape all special characters in the user's answer
                    var re : RegExp = eval(this.matchRegexp.format(userAnswer.replace(/[-[\]{}()*+?.,\\^$|#\s]/g, "\\$&")));
                    isCorrect = this.answerString.match(re)!==null;
                }
                break;

            case COMPONENT_TYPE.comboBox1:
            case COMPONENT_TYPE.comboBox2:
                // Note: At this point we use the intenal (language independent) name for the value.
                // This is necessary in order to produce language indenpendent statistics. However,
                // this will not work correctly if there are duplicate values in the friendly names.
                // At present, this is only the case with the Winchester database.

                var selectedOption : JQuery = $(this.c).find(":selected");
                if (selectedOption.attr('value')!=='NoValueGiven') {
                    var userAnswerSws : StringWithSort = $(this.c).find(":selected").data('sws');

                    isCorrect = userAnswerSws===this.answerSws;
                    userAnswer = userAnswerSws.getInternal();
                }
                break;
            }
            if (userAnswer && !this.hasAnswered) {
                this.hasAnswered = true;
                this.firstAnswer = userAnswer;
                this.firstAnswerCorrect = isCorrect;
            }
            if (this.hasAnswered)
                this.comp.setYesNo(isCorrect);
        }
    }

    /** This function is called for each question when the question panel is being closed.
     * If a question is unanswered, it will be marked as such.
     */
    public commitIt() : void {
        this.checkIt(false);
        if (!this.hasAnswered) {
            this.hasAnswered = true;
            this.firstAnswer = "*Unanswered*";
            this.firstAnswerCorrect = false;
        }
    }

    /** Gets the user's first answer to this question.
     * @return The user's answer.
     */
    public usersAnswer() : string{
        return this.firstAnswer;
    }

    /** Was the user's first answer to this question correct?
     * @return True if the user's first answer to this question was correct
     */
    public usersAnswerWasCorrect() : boolean {
        return this.firstAnswerCorrect;
    }

    /** Gets the correct answer as a string.
     * @return The correct answer as a string.
     */
    public correctAnswer() : string {
        return this.answerString;
    }
}
