// -*- js -*-
// Copyright © 2018 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk

// This code as been extensively modified by Ernst Boogert in November 2020

// This code handles displaying a single question of an exercise.

/// <reference path="componentwithyesno.ts" />
/// <reference path="answer.ts" />


//****************************************************************************************************
// Foreign2Shortcut class
//
// This class manages the releationship between a non-Latic character set and the associated Latin
// shortcut key.

class Foreign2Shortcut {
    private static map : { [foreign:string] : string } = {};

    // Initializes the table
    public static init() {
        switch (configuration.charSet) {
        case 'hebrew':
            Foreign2Shortcut.map['א'] = '>';
            Foreign2Shortcut.map['ב'] = 'b';
            Foreign2Shortcut.map['ג'] = 'g';
            Foreign2Shortcut.map['ד'] = 'd';
            Foreign2Shortcut.map['ה'] = 'h';
            Foreign2Shortcut.map['ו'] = 'w';
            Foreign2Shortcut.map['ז'] = 'z';
            Foreign2Shortcut.map['ח'] = 'x';
            Foreign2Shortcut.map['ט'] = 'v';
            Foreign2Shortcut.map['י'] = 'j';
            Foreign2Shortcut.map['ך'] = 'K';
            Foreign2Shortcut.map['כ'] = 'k';
            Foreign2Shortcut.map['ל'] = 'l';
            Foreign2Shortcut.map['ם'] = 'M';
            Foreign2Shortcut.map['מ'] = 'm';
            Foreign2Shortcut.map['ן'] = 'N';
            Foreign2Shortcut.map['נ'] = 'n';
            Foreign2Shortcut.map['ס'] = 's';
            Foreign2Shortcut.map['ע'] = '<';
            Foreign2Shortcut.map['ף'] = 'P';
            Foreign2Shortcut.map['פ'] = 'p';
            Foreign2Shortcut.map['ץ'] = 'Y';
            Foreign2Shortcut.map['צ'] = 'y';
            Foreign2Shortcut.map['ק'] = 'q';
            Foreign2Shortcut.map['ר'] = 'r';
            Foreign2Shortcut.map['שׁ'] = 'c';
            Foreign2Shortcut.map['שׂ'] = 'f';
            Foreign2Shortcut.map['ש'] = '#';
            Foreign2Shortcut.map['ת'] = 't';
            Foreign2Shortcut.map['־'] = '&';  // Maqaf
            Foreign2Shortcut.map['ֿ'] = '2';  // Rafe
            Foreign2Shortcut.map['ּ'] = '.';  // Dagesh
            Foreign2Shortcut.map['ֽ'] = '$';  // Meteg
            Foreign2Shortcut.map['ְ'] = ':';  // Sheva
            Foreign2Shortcut.map['ֳ'] = '+';  // Hataf qamats
            Foreign2Shortcut.map['ֲ'] = 'A';  // Hataf patah
            Foreign2Shortcut.map['ֱ'] = 'E';  // Hataf segol
            Foreign2Shortcut.map['ֵ'] = '1';  // Tsere
            Foreign2Shortcut.map['ָ'] = '@';  // Qamats
            Foreign2Shortcut.map['ַ'] = 'a';  // Patah
            Foreign2Shortcut.map['ֶ'] = 'e';  // Segol
            Foreign2Shortcut.map['ִ'] = 'I';  // Hiriq
            Foreign2Shortcut.map['ֹ'] = 'o';  // Holam
            Foreign2Shortcut.map['ֻ'] = 'u';  // Qubuts

            Foreign2Shortcut.map['-'] = '-';  // Empty answer indicator
            break;

        case "greek":
            Foreign2Shortcut.map['α'] = 'a';
            Foreign2Shortcut.map['β'] = 'b';
            Foreign2Shortcut.map['γ'] = 'g';
            Foreign2Shortcut.map['δ'] = 'd';
            Foreign2Shortcut.map['ε'] = 'e';
            Foreign2Shortcut.map['ζ'] = 'z';
            Foreign2Shortcut.map['η'] = 'h';
            Foreign2Shortcut.map['θ'] = 'q';
            Foreign2Shortcut.map['ι'] = 'i';
            Foreign2Shortcut.map['κ'] = 'k';
            Foreign2Shortcut.map['λ'] = 'l';
            Foreign2Shortcut.map['μ'] = 'm';
            Foreign2Shortcut.map['ν'] = 'n';
            Foreign2Shortcut.map['ξ'] = 'x';
            Foreign2Shortcut.map['ο'] = 'o';
            Foreign2Shortcut.map['π'] = 'p';
            Foreign2Shortcut.map['ρ'] = 'r';
            Foreign2Shortcut.map['ς'] = 'c';
            Foreign2Shortcut.map['σ'] = 's';
            Foreign2Shortcut.map['τ'] = 't';
            Foreign2Shortcut.map['υ'] = 'u';
            Foreign2Shortcut.map['φ'] = 'f';
            Foreign2Shortcut.map['χ'] = 'j';
            Foreign2Shortcut.map['ψ'] = 'q';
            Foreign2Shortcut.map['ω'] = 'w';

            Foreign2Shortcut.map['Α'] = 'A';
            Foreign2Shortcut.map['Β'] = 'B';
            Foreign2Shortcut.map['Γ'] = 'G';
            Foreign2Shortcut.map['Δ'] = 'D';
            Foreign2Shortcut.map['Ε'] = 'E';
            Foreign2Shortcut.map['Ζ'] = 'Z';
            Foreign2Shortcut.map['Η'] = 'H';
            Foreign2Shortcut.map['Θ'] = 'Q';
            Foreign2Shortcut.map['Ι'] = 'I';
            Foreign2Shortcut.map['Κ'] = 'K';
            Foreign2Shortcut.map['Λ'] = 'L';
            Foreign2Shortcut.map['Μ'] = 'M';
            Foreign2Shortcut.map['Ν'] = 'N';
            Foreign2Shortcut.map['Ξ'] = 'X';
            Foreign2Shortcut.map['Ο'] = 'O';
            Foreign2Shortcut.map['Π'] = 'P';
            Foreign2Shortcut.map['Ρ'] = 'R';
            Foreign2Shortcut.map['Σ'] = 'S';
            Foreign2Shortcut.map['Τ'] = 'T';
            Foreign2Shortcut.map['Υ'] = 'U';
            Foreign2Shortcut.map['Φ'] = 'F';
            Foreign2Shortcut.map['Χ'] = 'J';
            Foreign2Shortcut.map['Ψ'] = 'Q';
            Foreign2Shortcut.map['Ω'] = 'W';
            break;

        case "transliterated_hebrew":
            for (let a = 97; a<123; ++a)
                Foreign2Shortcut.map[String.fromCharCode(a)] = String.fromCharCode(a);
            Foreign2Shortcut.map['ʔ'] = '>';
            Foreign2Shortcut.map['ʕ'] = '<';
            break;
        }
    }

    // Performs a lookup in the table
    // Parameter:
    //    letter: The non-Latin character
    // Returns:
    //    The Latin shortcut key, or '?' if no shortcut exists
    public static get(letter : string) : string {
        if (Foreign2Shortcut.map[letter])
            return Foreign2Shortcut.map[letter];
        else
            return '?';
    }
}


//****************************************************************************************************
// KeyTable class
//
// This class provides the link between shortcut keystrokes and the actions they perform.
//
// The class manages three items of information:
// elements: The IDs of HTML elements to be manipulated by a given keystroke
// actions: The action performed on the target element (1=check, 2=click, 3=toggle)
// focus: The ID of the HTML element, if any, to receive input focus when a row is selected
class KeyTable {
    private elements : any = [];  // Indexed by qoid, rowid, key, item. Value is element ID
    private actions : any = [];  // Indexed by qoid, rowid. Value is action
    private focus : any = [];  // Indexed by qoid, rowid. Value is element ID


    // Adds an action to be performed on a given element.
    // Parameters:
    //     card: The question object number
    //     row: The row on the card for the question object
    //     letter: The shortcut key
    //     id: The ID of the HTML element to be manipulated by the keystroke
    //     action: The actio to be performed on the element (1=check, 2=click, 3=toggle)
    public add(card : number, row : number, letter : string, id : string, action : number) {
        if (!this.elements[card]) this.elements[card] = [];
        if (!this.elements[card][row]) this.elements[card][row] = new Object;
        if (!this.elements[card][row][letter]) this.elements[card][row][letter] = [];

        this.elements[card][row][letter].push(id);

        if (!this.actions[card]) this.actions[card] = [];
        this.actions[card][row] = action;
    }

    // Adds an element to receive input focus when a row is selected.
    // Parameters:
    //     card: The question object number
    //     row: The row on the card for the question object
    //     id: The ID of the HTML element to receive input focus
    public addfocus(card : number, row : number, id : string) {
        if (!this.focus[card]) this.focus[card] = [];
        this.focus[card][row] = id;
    }

    // Returns an array of the IDs of elements to be affected by a given keystroke.
    // Parameters:
    //     card: The question object number
    //     row: The row on the card for the question object
    //     letter: The shortcut key
    public get_element(card : number, row : number, letter : string) : string[] {
        if (!this.elements[card] || !this.elements[card][row])
            return null;
        return this.elements[card][row][letter];
    }

    // Returns the action to be performed on a given element.
    // Parameters:
    //     card: The question object number
    //     row: The row on the card for the question object
    public get_action(card : number, row : number) : number {
        if (!this.actions[card])
            return null;
        return this.actions[card][row];
    }

    // Returns the ID of the element to receive input focus when a given row is selected.
    // Parameters:
    //     card: The question object number
    //     row: The row on the card for the question object
    public get_focus(card : number, row : number) : number {
        if (!this.focus[card])
            return null;
        return this.focus[card][row];
    }
}

//****************************************************************************************************
// Cursor class
//
// This class keeps track of the currently selected question object and row and the associated
// cursor (that is, the > pointing to the selected row).
//
class Cursor {
    public static card : number; // The current question object
    public static row : number;  // The selected row
    private static minrow: number; // The row containing the first request feature
    private static maxrow: number; // The maximum number of rows + 1

    // Initializes the cursor
    public static init(minrow : number, maxrow : number)
    {
        Cursor.minrow = minrow;
        Cursor.maxrow = maxrow;
        Cursor.card = 0;
        Cursor.row = minrow;
    }

    // Hides the cursor for the current row.
    private static hide() {
        $(`#ptr_${Cursor.card}_${Cursor.row}`).hide();
    }

    // Displays the cursor for the current row.
    // This function also scrolls the window to an appropriate position and, if necessary, assigns
    // input focus.
    // Parameter:
    //     force: True if this fuction was called because of a manually set focus. In that case,
    //            only the cursor mark should be set.

    private static show(force : boolean = false) {
        $(`#ptr_${Cursor.card}_${Cursor.row}`).show();

        if (force)
            return;

        let questiontop = $('#quizdesc').offset().top; // Preferred top of screen
        let scrollToPos : number; // The position to which to scroll
        let top = $(`#row_${Cursor.card}_${Cursor.row}`).offset().top; // Top of current request attribute
        let bottom = top + $(`#row_${Cursor.card}_${Cursor.row}`).height() + 10; // Bottom of current request attribue
                                                                                 // (the 10 is a  margin)

        if (bottom - window.scrollY >= window.innerHeight || top - window.scrollY < 0) {
            // We must scroll - preferably to questiontop if this keeps the question inside the window

            if (questiontop + window.innerHeight >= bottom)
                scrollToPos = questiontop;
            else
                scrollToPos = bottom - window.innerHeight;
        }

        if ($(`#keyinp_${Cursor.card}_${Cursor.row}`).length) {
            // Assign focus and make sure that keystrokes are not sent to other elements
            $(`#keyinp_${Cursor.card}_${Cursor.row}`).focus();
            $('body').unbind('keydown');
        }

        // Scroll
        $('html, body').animate({
            scrollTop: scrollToPos
        }, 50);
    }

    // Goes to a specified question object and row
    // Parameters:
    //     c: The question object number
    //     r: The row number.
    //     force: True if this fuction was called because of a manually set focus. In that case,
    //            only the cursor mark should be set.
    public static set(c : number = 0, r : number = Cursor.minrow, force : boolean = false) {
        Cursor.hide();

        Cursor.card = c;
        Cursor.row = r;

        Cursor.show(force);
    }

    // Moves to the next or previous row
    // Parameter:
    //    n: +1 or -1. Indicates direction of movement.
    // Returns:
    //    True if the cursor was moved, false otherwise
    public static prevNextItem(n : number /* 1 or -1 */) : boolean {
        if (n>0) {
            if (Cursor.row+n<Cursor.maxrow) {
                Cursor.set(Cursor.card, Cursor.row+n);
                return true;
            }
            else
                return false;
        }
        else {
            if (Cursor.row+n>=Cursor.minrow) {
                Cursor.set(Cursor.card, Cursor.row+n);
                return true;
            }
            else
                return false;
        }
    }
}


//****************************************************************************************************
// PanelQuestion class
//
// This class represents a single question (with multiple question items) of an exercise.
//
class PanelQuestion {
    private qd             : QuizData;      // The information required to generate the exercise
    private sentence       : MonadSet;      // The monads containing the question text in the Emdros database
    private location       : string;        // The current location (localized)
    private vAnswers       : Answer[] = []; // The correct answer for each question item
    private answersPerCard : number[] = []; // answersPerCard[n] is the first available index in vAnswers after question object number n
    private question_stat  : QuestionStatistics = new QuestionStatistics; // Answer statistics
    private static kbid    : number = 1;    // Input field identification for keyboard
    private gradingFlag	   : boolean;	    // May the statistics be used for grading the student?
    private subQuizIndex   : number = 0;    // Used to toggle subquestions
    private subQuizMax     : number;        // Used to define max number of subquestions

    private keytable : KeyTable = new KeyTable;
    private keyinps : string[] = [];


    //------------------------------------------------------------------------------------------
    // charclass static method
    //
    // Determines the appropriate CSS class for a given feature.
    //
    // Parameter:
    //     featset: FeatureSetting from the configuration variable.
    // Returns:
    //     The appropriate CSS class for the feature.
    //
    private static charclass(featset : FeatureSetting) : string {
        return featset.foreignText ? charset.foreignClass
             : featset.transliteratedText ? charset.transliteratedClass : '';
    }


    //------------------------------------------------------------------------------------------
    // updateQuestionStat method
    //
    // Updates the private question statistics with information about the student's answers and
    // returns the statistics.
    //
    // Returns:
    //     The question statistics.
    //
    public updateQuestionStat() : QuestionStatistics {
        this.question_stat.end_time = Math.round((new Date()).getTime() / 1000);

        for (let i=0, len=this.vAnswers.length; i<len; ++i) {
            let ans : Answer = this.vAnswers[i];
            ans.commitIt(); // Check answer correctness and identify unanswered questions

            this.question_stat.req_feat.correct_answer.push(ans.correctAnswer());
            this.question_stat.req_feat.users_answer.push(ans.usersAnswer());
            this.question_stat.req_feat.users_answer_was_correct.push(ans.usersAnswerWasCorrect());
        }

        return this.question_stat;
    }


    //------------------------------------------------------------------------------------------
    // buildQuizObjectFeatureList method
    //
    // Creates a list of feature=>value maps holding the features for each question object.
    //
    // Returns:
    //     A list of feature/value pairs for each question object.
    //
    private buildQuizObjectFeatureList() : util.str2strArr[] {
        let qoFeatures : util.str2strArr[] = [];  // The feature/value pairs for each question object

        let hasSeen    : boolean[]         = [];  // Maps id_d => true if the id_d has been seen. (An id_d
                                                  // can occur several times; for example, the id_d of a
                                                  // clause may occur for each monad within the clause.)

        let allmonads : number[] = getMonadArray(this.sentence); // All monads in the sentence
        for (let i=0, len=allmonads.length; i<len; ++i) {
            let id_d : number = this.qd.monad2Id[allmonads[i]];
            if (id_d) {
                if (!hasSeen[id_d]) {
                    qoFeatures.push(this.qd.id2FeatVal[id_d]);
                    hasSeen[id_d] = true;
                }
            }
        }
        return qoFeatures;
    }


    //------------------------------------------------------------------------------------------
    // prevNextSubQuestion method
    //
    // Method used to toggle subquestions in a quiz.
    //
    public prevNextSubQuestion(n : number): void {
        if (this.subQuizIndex + n >= 0 && this.subQuizIndex + n < this.subQuizMax) {
            // If the proposed move (n; always 1 or -1) is within the boundaries, proceed...
            this.subQuizIndex += n;
        }
        let i: number;
        let slides: JQuery = $('#quizcontainer').find('.quizcard');

        if (this.subQuizIndex < 1)
            $('#prevsubquiz').hide(); // hide button if the quizcard is the first
        else
            $('#prevsubquiz').show(); // show button if the index is 1 or higher

        if (this.subQuizIndex < slides.length - 1) {
            $('#nextsubquiz').show(); // show button if the quizcard is not the last
            // Hide "Next", "GRADE task" and "SAVE outcome" buttons
            $('button#next_question').hide();
            $('button#finish').hide();
            $('button#finishNoStats').hide();
        };

        if (this.subQuizIndex === slides.length - 1) {
            $('#nextsubquiz').hide(); // hide button if the quizcard is the last
            // Show "Next", "GRADE task" and "SAVE outcome" buttons
            $('button#next_question').show();
            $('button#finish').show();
            $('button#finishNoStats').show();
        };

        // Show the quizcard change
        for (i = 0; i < slides.length; i++) {
            if (i === this.subQuizIndex) {
                slides.slice(i).css({ "display": "block" });
            } else {
                slides.slice(i).css({ "display": "none" });
            }
        }

        Cursor.set(this.subQuizIndex);
    }

    //------------------------------------------------------------------------------------------
    // location_info method
    //
    // Handles the "Locate" information in a question
    //
    // Parameter:
    //     dict: The collection of Emdros objects for this question.
    //
    private location_info(dict : Dictionary) {
        // Calculate the Bible reference (the 'location') for this sentence.

        // We base the location on the first monad in the sentence.
        let smo : SingleMonadObject = dict.getSingleMonadObject(getFirst(this.sentence));
        this.location = smo.bcv_loc; // Localized


        // Save the location for statistics
        this.question_stat.location = '';

        for (let unix in configuration.universeHierarchy) {
            let unixi : number = +unix;
            if (isNaN(unixi)) continue; // Not numeric

            this.question_stat.location += smo.bcv[unixi] + (unixi!=2 ? ', ' : '');
        }

        // Location functionality: used by locate button in quiz
        if (this.qd.maylocate) {
            $('input#locate_cb').on('click',null,this.location,function(e) {
                if ($(this).prop('checked'))
                    $('.location').html(e.data);
                else
                    $('.location').html('');
            });
        }
        else
            $('#locate_choice').hide();

        if ($('#locate_cb').prop('checked'))
            $('.location').html(this.location);
    }

    //------------------------------------------------------------------------------------------
    // body_keydown handler
    //
    // Handles shortcut characters outside of text input fields.
    // NOTE: This is an event handler, so it is declared as a field, not a method.
    //
    private body_keydown = (event : any) => {
        let pq : PanelQuestion = event.data;
        let ctrl : boolean = event.ctrlKey || event.metaKey;

        if (event.key==="PageDown")
            $('#nextsubquiz:visible').click();
        else if (event.key==="PageUp")
            $('#prevsubquiz:visible').click();
        else if (event.key==="ArrowDown" && !ctrl)
            Cursor.prevNextItem(1);
        else if (event.key==="ArrowUp")
            Cursor.prevNextItem(-1);
        else if (event.key==="ArrowDown" && ctrl)
            $('#next_question:visible:enabled').click();
        else if (event.key==="g" && ctrl)
            $('#check_answer').click();
        else if (event.key==="j" && ctrl)
            $('#show_answer').click();
        else if (event.key==="s" && ctrl) {
            $('.shortcut').toggle();
        }
        else if (!ctrl) {
            let ids = pq.keytable.get_element(Cursor.card, Cursor.row, event.key);

            if (ids) {
                switch (pq.keytable.get_action(Cursor.card, Cursor.row)) {
                case 1: // Check
                    if (ids.length>1) {
                        // More than one option starts with this character
                        for (let i in ids) {
                            if (isNaN(+i)) continue; // Not numeric

                            if ($(`#${ids[i]}`).prop('checked')) {
                                let i1 : number = +i+1;
                                if (i1==ids.length)
                                    i1 = 0;
                                $(`#${ids[i1]}`).prop('checked',true);
                                $(`#${ids[i1]}`).change();
                                return false;
                            }
                        }
                        // If we reach this point, no item starting the character has been checked
                    }
                    $(`#${ids[0]}`).prop('checked',true);
                    $(`#${ids[0]}`).change();
                    break;

                case 2: // Click
                    $(`#${ids[0]}`).click();
                    $(`#${ids[0]}`).change();
                    break;

                case 3: // Toggle
                    $(`#${ids[0]}`).prop('checked', !$(`#${ids[0]}`).prop('checked'));
                    $(`#${ids[0]}`).change();
                }
            }
            else
                return true;
        }
        else
            return true;

        return false;
    }

    //------------------------------------------------------------------------------------------
    // textfield_keydown handler
    //
    // Handles shortcut characters inside text input fields.
    // NOTE: This is an event handler, so it is declared as a field, not a method.
    //
    private textfield_keydown = (event : any) => {
        let pq : PanelQuestion = event.data;
        let ctrl : boolean = event.ctrlKey || event.metaKey;

        if (event.key==="ArrowDown" && !ctrl) {
            if (Cursor.prevNextItem(1))
                $(event.target).blur();
            return false;
        }
        else if (event.key==="ArrowUp") {
            if (Cursor.prevNextItem(-1))
                $(event.target).blur();
            return false;
        }
        else if (event.key==="PageDown") {
            if ($('#nextsubquiz').is(':visible')) {
                $(event.target).blur();
                $('#nextsubquiz:visible').click();
            }
            return false;
        }
        else if (event.key==="PageUp") {
            if ($('#prevsubquiz').is(':visible')) {
                $(event.target).blur();
                $('#prevsubquiz:visible').click();
            }
            return false;
        }
        else if (event.key==="ArrowDown" && ctrl) {
            $('#next_question:visible:enabled').click();
            return false;
        }
        else if (event.key==="g" && ctrl) {
            $('#check_answer').click();
            return false;
        }
        else if (event.key==="j" && ctrl) {
            $('#show_answer').click();
            return false;
        }
        else if (event.key==="s" && ctrl) {
            $('.shortcut').toggle();
            return false;
        }

        return true;
    }



    //------------------------------------------------------------------------------------------
    // Constructor method
    //
    // Parameters:
    //     qd: The information required to generate a exercise.
    //     dict: The collection of Emdros objects for this question.
    //     exam_mode: We're running an exam.
    //
    constructor(qd : QuizData, dict : Dictionary, exam_mode: boolean) {
        this.qd = qd;
        this.sentence = dict.sentenceSetQuiz;

        Foreign2Shortcut.init();
        this.location_info(dict);

        // Save question text for statistics
        this.question_stat.text = dict.generateSentenceHtml(qd);

        ///////////////////////////////////
        // Generate table of question items

        // Cache a few variables for easy access
        let dontShow        : boolean  = qd.quizFeatures.dontShow;   // Display (number) instead of text of the quizobject
        let showFeatures    : string[] = qd.quizFeatures.showFeatures;
        let requestFeatures : {name : string; usedropdown : boolean; hideFeatures : string[];}[]
                                       = qd.quizFeatures.requestFeatures;
        let oType           : string   = qd.quizFeatures.objectType;

        // Variables for creating table content (<td> parts)
        let featuresHere    : FeatureMap        = typeinfo.obj2feat[oType];          // Maps feature name => feature type
        let qoFeatures      : util.str2strArr[] = this.buildQuizObjectFeatureList(); // Feature/value pairs for each question object
        let hasForeignInput : boolean           = false;                             // Do we need a virtual keyboard?
        let quizItemID      : number            = 0;                                 // Counts quizitems to be used to group radio buttons together (see name attribute)


        /////////////////////////////////////////////////
        // CREATE TABLE ENTRIES FOR EACH QUESTION CARD //
        /////////////////////////////////////////////////
        let questionheaders: string[] = []; // Initialize empty array for questionheaders
                                            // For each question card, the headers are pulled out from this list
        let headInd : number = 0;           // Header index is the index into questionheaders

        /////////////////////////////
        // Define question headers //

        // Define headers for dontShow items
        if (dontShow) {
            questionheaders.push('<th>' + localize('item_number') + '</th>');
            this.question_stat.show_feat.names.push('item_number');
        }

        // Define headers for showFeatures items
        for (let sfi in showFeatures) {
            if (isNaN(+sfi)) continue; // Not numeric

            questionheaders.push('<th>' + getFeatureFriendlyName(oType, showFeatures[sfi]) + '</th>');
            this.question_stat.show_feat.names.push(showFeatures[sfi]);  // Save feature name for statistics
        }

        // Define headers for requestFeatures items
        for (let sfi in requestFeatures) {
            if (isNaN(+sfi)) continue; // Not numeric

            questionheaders.push('<th>' + getFeatureFriendlyName(oType, requestFeatures[sfi].name) + '</th>');
            this.question_stat.req_feat.names.push(requestFeatures[sfi].name);  // Save feature name for statistics
        }

        // Now, the array questionheaders contains a list of <th>...</th> elements to be put before
        // each question on the question cards
        let headLen: number = questionheaders.length;
        let quizCardNum: number = qoFeatures.length; // Count number of quizcards to define the appearance of toggle buttons
        let quizContainer: JQuery = $('div#quizcontainer');

        this.subQuizMax = quizCardNum;

        // The <div class="quizcontainer">...</div> will be created with this contents:
        //
        // <div class="quizcontainer">
        //     <div class="prev-next-btn prev" id="prevsubquiz">❮</div>      -- Button to previous quiz object
        //
        //     <div class="quizcard" style="display:block">                  -- One for each quiz object, but only the first one will be visible
        //         <table class="quiztab">...</table>
        //     <div>
        //
        //     <div class="quizcard" style="display:none">
        //         <table class="quiztab">...</table>
        //     <div>
        //
        //     <div class="quizcard" style="display:none">
        //         <table class="quiztab">...</table>
        //     <div>
        //
        //     ...
        //
        //     <div class="prev-next-btn next" id="nextsubquiz">❯</div>       -- Button to next quiz object
        // </div>

        ///////////////////////////////////////
        // Loop through all the quiz objects //
        for (let qoid in qoFeatures) {
            if (isNaN(+qoid)) continue; // Not numeric

            if (headInd>=headLen)
                headInd -= headLen; // Normalize headInd to lie in the range 0..headLen.

            let quizCard : JQuery = +qoid===0
                                        ? $('<div class="quizcard" style="display:block"></div>')
                                        : $('<div class="quizcard" style="display:none"></div>');
            let quizTab : JQuery = $('<table class="quiztab"></table>');

            quizCard.append(quizTab);
            quizContainer.append(quizCard);

            let fvals: util.str2strArr = qoFeatures[+qoid]; // Feature/value pairs for current quiz object


            ///////////////////////////////////
            // Loop through display features
            //
            // For each display feature, the following will be added to <table class="quiztab">...</table>:
            // <tr>
            //     <td>&nbsp;</td>
            //     <th>Feature name</th>    -- Taken from questionheaders[headInd]
            //     <td>Feature value</td>
            // </tr>

            // Extra "display feature" for quiz objects that are marked by 'dontShow'
            if (dontShow) {
                quizTab.append(`<tr><td>&nbsp;</td>${questionheaders[headInd]}<td>${+qoid + 1}</td></tr>`);
                ++headInd;
                this.question_stat.show_feat.values.push("" + (+qoid + 1));  // Save feature value for statistics
            }
            for (let sfi in showFeatures) {
                if (isNaN(+sfi)) continue; // Not numeric

                let sf       : string         = showFeatures[+sfi];          // Feature name
                let val      : string         = fvals[sf] as string;         // Feature value
                let featType : string         = featuresHere[sf];            // Feature type
                let featset  : FeatureSetting = getFeatureSetting(oType,sf); // Feature configuration

                this.question_stat.show_feat.values.push(val);  // Save feature value for statistics

                if (featType==null && sf!=='visual')
                    alert(`Unexpected (1) featType==null in panelquestion.ts; sf="${sf}"`);

                if (sf==='visual')
                    featType = 'string';

                if (featType=='hint') {
                    // The feature value looks like this:
                    // "featurename=value" or "featurename=value,featurename=value"
                    let sp : string[] = val.split(/[,=]/);
                    if (sp.length==2) {
                        val = getFeatureFriendlyName(oType, sp[0]) + "=" +
                            getFeatureValueFriendlyName(featuresHere[sp[0]],sp[1],false,true);
                    }
                    else if (sp.length==4) {
                        val = getFeatureFriendlyName(oType, sp[0])
                            + "="
                            + getFeatureValueFriendlyName(featuresHere[sp[0]],sp[1],false,true)
                            + ", "
                            + getFeatureFriendlyName(oType, sp[2])
                            + "="
                            + getFeatureValueFriendlyName(featuresHere[sp[2]],sp[3],false,true);
                    }
                    else if (val==='*')
                        val = '-';
                }
                else if (featType!=='string' && featType!=='ascii' && featType!=='integer') {
                    // This is an enumeration feature type
                    // Replace val with the appropriate friendly name or "Other value"
                    if (featset.otherValues && featset.otherValues.indexOf(val)!==-1)
                        val = localize('other_value');
                    else
                        val = getFeatureValueFriendlyName(featType, val, false, true);
                }

                if (val==null)
                    alert('Unexpected val==null in panelquestion.ts');

                if (featType === 'string' || featType == 'ascii') {
                    quizTab.append(`<tr><td>&nbsp;</td>${questionheaders[headInd]}`
                        + `<td class="${PanelQuestion.charclass(featset)}">${val === '' ? '-' : val}</td></tr>`);
                    ++headInd;
                }
                else {
                    quizTab.append(`<tr><td>&nbsp;</td>${questionheaders[headInd]}<td>${val}</td></tr>`);
                    ++headInd;
                }
            }

            ///////////////////////////////////
            // Loop through request features
            //
            // For each request feature, the following will be added to <table class="quiztab">...</table>:
            // <tr>
            //     <td>Pointer</td>
            //     <th>Feature name</th>       -- Taken from questionheaders[headInd]
            //     <td class="qbox">...</td>   -- The user's answer is chosen here.
            // </tr>
            //
            // Note: The variable v, introduced below, will eventually hold the string
            // <td class="qbox">...</td> with the appropriate contents.

            Cursor.init(headInd,headLen);

            for (let rfi in requestFeatures) {
                if (isNaN(+rfi)) continue; // Not numeric

                let rf            : string   = requestFeatures[+rfi].name;         // Feature name
                let usedropdown   : boolean  = requestFeatures[+rfi].usedropdown;  // Use multiple choice?
                let hideFeatures  : string[] = requestFeatures[+rfi].hideFeatures;
                let correctAnswer : string   = fvals[rf] as string;                // Feature value (i.e., the correct answer)
                let featType      : string   = featuresHere[rf];                   // Feature type
                let featset       : FeatureSetting = getFeatureSetting(oType,rf);  // Feature configuration
                let v             : JQuery   = null;                               // Component to hold the data entry field or a message

                ++quizItemID; // Update quizItemID for each grouping of radio buttons

                if (correctAnswer==null)
                    alert('Unexpected correctAnswer==null in panelquestion.ts');
                if (correctAnswer==='')
                    correctAnswer = '-'; // Indicates empty answer

                if (featType==null && rf!=='visual')
                    alert('Unexpected (2) featType==null in panelquestion.ts');
                if (rf==='visual')
                    featType = 'string';


                //////////////////////////////////////////////////////////////////////////
                // The layout of the feature request depends on the type of the feature:

                if (featset.alternateshowrequestDb!=null && usedropdown) {
                    // Multiple choice question item
                    let suggestions : string[] = fvals[rf + '!suggest!'] as string[]; // Values to choose between

                    if (suggestions==null) // No suggestions, just display the answer as if it were a display feature
                        v = $(`<td class="${PanelQuestion.charclass(featset)}">${correctAnswer}</td></tr>`);
                    else {
                        // Create this HTML structure in the variable v:                    From these variables
                        // <td class="qbox">                                                       cwyn
                        //   <img ...>                                                             cwyn
                        //   <img ...>                                                             cwyn
                        //   <img ...>                                                             cwyn
                        //   <div class="quizitem">                                                quiz_div
                        //
                        //     <div class="selectbutton multiple_choice">                          optArray[0]
                        //       <input type="radio" id="VAL1_N" name="quizitem_N" value="VAL1">   optArray[0]
                        //       <label class="CHARSET" for="VAL1_N">VAL1</label>                  optArray[0]
                        //     </div>                                                              optArray[0]
                        //
                        //     <div class="selectbutton multiple_choice">                          optArray[1]
                        //       <input type="radio" id="VAL2_N" name="quizitem_N" value="VAL2">   optArray[1]
                        //       <label class="CHARSET" for="VAL2_N">VAL2</label>                  optArray[1]
                        //     </div>                                                              optArray[1]
                        //
                        //     ...
                        //
                        //   </div>                                                                quiz_div
                        // </td>                                                                   cwyn
                        //
                        // In the items above, the letter N is the value of the quizItemID variable

                        let quiz_div : JQuery             = $('<div class="quizitem"></div>'); // Used to ancor the checkbox buttons and to add additional data
                        let optArray : JQuery[]           = [];                                // All the multiple choice options
                        let cwyn     : ComponentWithYesNo = new ComponentWithYesNo(quiz_div, COMPONENT_TYPE.comboBox2); // Result indicator
                        let charSetClass : string         = configuration.charSet=='transliterated_hebrew' ? 'hebrew_translit' : configuration.charSet;

                        cwyn.addChangeListener();

                        for (let valix in suggestions) {
                            if (isNaN(+valix)) continue; // Not numeric

                            // We use a StringWithSort object to handle the option strings. This may
                            // seem unnecessary in this case, but it means that comboboxes can be
                            // handled in a uniform manner.

                            let s      : string         = suggestions[+valix];     // Current suggestion
                            let item   : StringWithSort = new StringWithSort(s,s); // StringWithSort holding the current suggestion
                            let option : JQuery         = $('<div class="selectbutton multiple_choice">'
                                + `<input type="radio" id="${item.getInternal()}_${quizItemID}" name="quizitem_${quizItemID}" value="${item.getInternal()}">`
                                + `<label class="${charSetClass}" for="${item.getInternal()}_${quizItemID}">${item.getString()}<span class="shortcut multichoice"></span></label>`
                                + '</div>');

                            option
                                .data('sws',item) // Associate the answer string with the <option> element
                                .data('id',`${item.getInternal()}_${quizItemID}`); // The id of the <input> element


                            optArray.push(option);
                            if (s===correctAnswer)
                                this.vAnswers.push(new Answer(cwyn,item,s,null));
                        }

                        // Sort the options alphabetically
                        optArray.sort((a: JQuery, b: JQuery) => StringWithSort.compare(a.data('sws'), b.data('sws')));


                        // Append optArray to quiz_div and associate keystroke
                        $.each(optArray,
                               (ix : number, o : JQuery) => {
                                   quiz_div.append(o);
                                   let sc : string = String.fromCharCode(ix+97); // a, b, c, etc.
                                   o.find('.shortcut').text(sc);
                                   this.keytable.add(+qoid, headInd, sc, o.data('id'), 1);
                               }
                              );

                        v = cwyn.getJQuery();
                    }
                }

                // In case a text input is requested
                else if (featType==='string' || featType==='ascii') {
                    // Create this HTML structure in the variable v:      From these variables
                    // <td class="qbox">                                   cwyn
                    //   <img ...>                                         cwyn
                    //   <img ...>                                         cwyn
                    //   <img ...>                                         cwyn
                    //   <div class="inputquizitem">                       vf
                    //     <input data-kbid="N" type="text">               vf
                    //     <div class="letterinput">                       vf
                    //       <div class="delbutton">←</div>                vf
                    //       <div class="inputbutton">A</div>              vf
                    //       <div class="inputbutton">B</div>              vf
                    //       ...                                           vf
                    //     </div>                                          vf
                    //   </div>                                            vf
                    // </td>                                               cwyn


                    let cwyn: ComponentWithYesNo;
                    let trimmedAnswer : string = correctAnswer.trim()
                        .replace(/&lt;/g,'<')
                        .replace(/&gt;/g,'>')
                        .replace(/&quot;/g,'"')
                        .replace(/&amp;/g, '&');   // Unescape HTML characters in correctAnswer


                    if (featset.foreignText || featset.transliteratedText) {
                        let answerArray           : string[] = trimmedAnswer.split(""); // Array of chars of the correct answer
                        let answerLetters         : string[] = [];                      // Array with only unique letters of correct answer
                        let additionalCons        : string[] = [];                      // Array with consonants to be added to answerLetters
                        let additionalVowels      : string[] = [];                      // Array with vowels to be added to answerLetters
                        let answerLettersRandom   : string[];                           // Random array of answerLetters + additionalCons
                        let showLetters           : string[] = [];                      // Unique letters that are finally shown

                        // Push unique letters to answerLetters
                        $.each(answerArray, (i: number, el: string) => {
                            if($.inArray(el, answerLetters) === -1) answerLetters.push(el);
                        });

                        /////////////////////////////////
                        // Find shin-sin and remove it //
                        let shinDot: boolean = false;
                        let sinDot: boolean = false;
                        for (let i = 0; i < answerLetters.length; i++) {
                            if (answerLetters[i] === 'ש') { // Check for sin-shin letter
                                answerLetters.splice(i, 1);
                                break;
                            }
                        }
                        for (let i = 0; i < answerLetters.length; i++) {
                            if (answerLetters[i] === '\u05C1') { // Checks for shin-dot
                                answerLetters.splice(i, 1);
                                shinDot = true;
                                break;
                            }
                        }
                        for (let i = 0; i < answerLetters.length; i++) {
                            if (answerLetters[i] === '\u05C2') { // Check for sin-dot
                                answerLetters.splice(i, 1);
                                sinDot = true;
                                break;
                            }
                        }

                        // push combined letter if shinDot or sinDot are set
                        if (shinDot) {
                            answerLetters.push('ש' + '\u05C1');
                        }
                        if (sinDot) {
                            answerLetters.push('ש' + '\u05C2');
                        }

                        // Add additional letters to a maximum of 12 unique letters total
                        for (let index = 0; index < answerLetters.length; index++) {
                            let l: string = answerLetters[index];
                            switch (l) {
                                //////////////////////////////
                                // Hebrew Regular consonants
                                case '-':
                                    // The correct answer is empty, so we propose a few random characters
                                    if (configuration.charSet==='hebrew') {
                                        additionalCons.push('י'); // yod
                                        additionalCons.push('ם'); // mem
                                        additionalCons.push('מ'); // final mem
                                        additionalCons.push('ך'); // final kaf
                                        additionalCons.push('ת'); // tav

                                        additionalVowels.push('ְ');  // Sheva
                                        additionalVowels.push('ֵ');  // Tsere
                                        additionalVowels.push('ָ');  // Qamats
                                        additionalVowels.push('ַ');  // Patah
                                        additionalVowels.push('ֶ');  // Segol
                                        additionalVowels.push('ִ');  // Hiriq
                                        additionalVowels.push('ֹ');  // Holam
                                        additionalVowels.push('ֻ');  // Qubuts
                                    }
                                    break;
                                case 'א':                       // if alef
                                    additionalCons.push('ע');   // push ayin
                                    break;
                                case 'ב':                       // if bet
                                    additionalCons.push('כ');   // push kaf
                                    break;
                                case 'ד':                       // if dalet
                                    additionalCons.push('ר');   // push resh
                                    additionalCons.push('ה');   // push he
                                    break;
                                case 'ח':                       // if het
                                    additionalCons.push('ה');   // push he
                                    additionalCons.push('ת');   // push tav
                                    break;
                                case 'ט':                       // if tet
                                    additionalCons.push('ת');   // push tav
                                    break;
                                case 'ו':                       // if waw
                                    additionalCons.push('י');   // push yod
                                    additionalCons.push('ז');   // push zayin
                                    break;
                                case 'י':                       // if yod
                                    additionalCons.push('ו');   // push waw
                                    break;
                                case 'ק':                       // if qof
                                    additionalCons.push('כ');   // push kaf
                                    break;
                                case 'כ':                       // if kaf
                                    additionalCons.push('ק');   // push qof
                                    additionalCons.push('ב');   // push bet
                                    break;
                                case 'ר':                       // if resh
                                    additionalCons.push('ד');   // push dalet
                                    additionalCons.push('ה');   // push he
                                    break;
                                case 'ש' + '\u05C1':                     // if shin
                                    additionalCons.push('ש' + '\u05C2'); // push sin
                                    break;
                                case 'ש' + '\u05C2':                     // if sin
                                    additionalCons.push('ש' + '\u05C1'); // push shin
                                    break;
                                case 'ת':                       // if tav
                                    additionalCons.push('ט');   // push tet
                                    additionalCons.push('ע');   // push ayin
                                    break;
                                ///////////////////////////////////
                                // Hebrew Sofit - closing letters
                                case 'ך':                             // if kaf sofit
                                    additionalCons.push('כ');         // push kaf
                                    additionalCons.push('ו');          // push waw --> because of suffixes
                                    additionalVowels.push('\u05B9');  // push holem --> because of suffixes
                                    break;
                                case 'ף':                             // if pe sofit
                                    additionalCons.push('פ');         // push pe
                                    additionalCons.push('ך');         // push kaf sofit
                                    break;
                                case 'ץ':                             // if tsade sofit
                                    additionalCons.push('צ');         // push tsade
                                    break;
                                case 'ם':                             // if mem sofit
                                    additionalCons.push('מ');         // push mem
                                    additionalCons.push('ן');          // push nun sofit
                                    break;
                                case 'ן':                              // if nun sofit
                                    additionalCons.push('נ');          // push nun
                                    additionalCons.push('ם');          // push mem sofit
                                    break;
                                ///////////////////////////////////
                                // Hebrew Vowels
                                case '\u05B8':                         // if qamats
                                    additionalVowels.push('\u05B8');   // push hatef
                                    additionalVowels.push('\u05B3');   // push hatef qamats
                                    break;
                                case '\u05B3':                         // if hatef qamats
                                    additionalVowels.push('\u05B0');   // push sheva
                                    additionalVowels.push('\u05B8');   // push qamats
                                    break;
                                case '\u05B7':                         // if patah
                                    additionalVowels.push('\u05B8');   // push qamats
                                    additionalVowels.push('\u05B2');   // push hatef patah
                                    break;
                                case '\u05B2':                         // if hatef patah
                                    additionalVowels.push('\u05B0');   // push sheva
                                    additionalVowels.push('\u05B7');   // push patah
                                    break;
                                case '\u05B0':                         // if sheva
                                    additionalVowels.push('\u05B2');   // push hatef patah
                                    additionalVowels.push('\u05B1');   // push hatef segol
                                    additionalVowels.push('\u05B3');   // push hatef qamats
                                    break;
                                case '\u05B5':                         // if tsere
                                    additionalVowels.push('\u05B6');   // push segol
                                    break;
                                case '\u05B6':                         // if segol
                                    additionalVowels.push('\u05B5');   // push tsere
                                    additionalVowels.push('\u05B1');   // push hatef segol
                                    break;
                                case '\u05B6':                         // if hatef segol
                                    additionalVowels.push('\u05B0');   // push sheva
                                    additionalVowels.push('\u05B6');   // push segol
                                    break;
                                case '\u05B9':                         // if holem
                                    additionalVowels.push('\u05BB');   // push qubuts
                                    break;
                                case '\u05BB':                         // if qubuts
                                    additionalVowels.push('\u05B9');   // push holem
                                    break;
                                ////////////////////////////////
                                // Greek consonants
                                case 'β':                              // if beta
                                    additionalCons.push('δ');          // push delta
                                    break;
                                case 'γ':                              // if gamma
                                    additionalCons.push('κ');          // push kappa
                                    break;
                                case 'δ':                              // if delta
                                    additionalCons.push('β');          // push beta
                                    break;
                                case 'ζ':                              // if dzeta
                                    additionalCons.push('ξ');          // push xi
                                    break;
                                case 'θ':                              // if theta
                                    additionalCons.push('τ');          // push tau
                                    break;
                                case 'κ':                              // if kappa
                                    additionalCons.push('γ');          // push gamma
                                    break;
                                case 'λ':                              // if lambda
                                    additionalCons.push('μ');          // push mu
                                    break;
                                case 'μ':                              // if mu
                                    additionalCons.push('ν');          // push nu
                                    break;
                                case 'ν':                              // if nu
                                    additionalCons.push('μ');          // push mu
                                    break;
                                case 'ξ':                              // if xi
                                    additionalCons.push('ζ');          // push dzeta
                                    break;
                                case 'π':                              // if pi
                                    additionalCons.push('ψ');          // push psi
                                    break;
                                case 'ρ':                              // if rho
                                    additionalCons.push('λ');          // push lambda
                                    break;
                                case 'σ':                              // if sigma regular
                                    additionalCons.push('ς');          // push sigma final
                                    break;
                                case 'ς':                              // if sigma final
                                    additionalCons.push('σ');          // push sigma regular
                                    break;
                                case 'τ':                              // if tau
                                    additionalCons.push('θ');          // push theta
                                    break;
                                case 'φ':                              // if phi
                                    additionalCons.push('θ');          // push theta
                                    break;
                                case 'χ':                              // if chi
                                    break;
                                case 'ψ':                              // if psi
                                    additionalCons.push('π');          // push pi
                                    break;

                                ///////////////////////////////////
                                // Greek vowels
                                case 'α':                              // if alpha
                                    additionalVowels.push('η');          // push eta
                                    additionalVowels.push('ε');          // push epsilon
                                    break;
                                case 'ε':                              // if epsilon
                                    additionalVowels.push('ι');          // push iota
                                    break;
                                case 'η':                              // if eta
                                    additionalVowels.push('ε');          // push epsilon
                                    break;
                                case 'ι':                              // if iota
                                    additionalVowels.push('ε');          // push epsilon
                                    break;
                                case 'υ':                              // if upsilon
                                    additionalVowels.push('η');          // push eta
                                    break;
                                case 'ο':                              // if omikron
                                    additionalVowels.push('η');          // push eta
                                    additionalVowels.push('ω');          // push omega
                                    break;
                                case 'ω':                              // if omega
                                    additionalVowels.push('ο');          // push omikron
                                    break;
                            }

                        }

                        // Randomize additional Consonants
                        additionalCons = additionalCons.sort( () => {
                            return .5 - Math.random();
                          });
                        // Randomize additional Vowels
                        additionalVowels = additionalVowels.sort( () => {
                            return .5 - Math.random();
                          });

                        // Push max 2 Consonants and max 3 Vowels to answerLetters
                        answerLettersRandom = answerLetters.concat(additionalCons.slice(0, 3))
                            .concat(additionalVowels.slice(0, 3));

                        // Make all letters unique and save them in showLetters
                        $.each(answerLettersRandom, (i: number, el: string) => {
                            if($.inArray(el, showLetters) === -1)
                                showLetters.push(el);
                        });

                        if (configuration.charSet==='hebrew' && $.inArray('-', showLetters) === -1)
                            showLetters.push('-');  // An empty answer should always be an option in hebrew

                        // Sort letters alphabetically
                        showLetters.sort();

//                        let vf : JQuery = $(`<div class="inputquizitem"><input data-kbid="${PanelQuestion.kbid++}" type="text"`
//                                + ` class="${PanelQuestion.charclass(featset)}"></div>`);
                        let vf : JQuery = $(`<div class="inputquizitem"><span class="inputshow ${PanelQuestion.charclass(featset)}"></div>`);

                        // Create letterinput and set del button to remove letters from input field
                        let letterinput : JQuery = $('<div class="letterinput"></div>');

                        vf.append(letterinput);

                        if (charset.isRtl)
                            letterinput.append(`<div class="delbutton" id="bs_${quizItemID}">&rarr;</div>`);
                        else
                            letterinput.append(`<div class="delbutton" id="bs_${quizItemID}">&larr;</div>`);

                        this.keytable.add(+qoid, headInd, 'Backspace', `bs_${quizItemID}`, 2);


                        // Set randomized letter buttons to be inputted in the input field
                        showLetters.forEach((letter: string, i: number) => {
                            let sc : string = Foreign2Shortcut.get(letter);
                            if (sc!='?') {
                                let sc_id : string = 'sc' + sc.charCodeAt(0);
                                letterinput.append(`<div class="inputbutton ${PanelQuestion.charclass(featset)}" id="${sc_id}_${quizItemID}" data-letter="${letter}">${letter}<span class="shortcut keybutton">${sc}</span></div>`);
                                this.keytable.add(+qoid, headInd, sc, `${sc_id}_${quizItemID}`, 2);
                            }
                            else
                                letterinput.append(`<div class="inputbutton ${PanelQuestion.charclass(featset)}" data-letter="${letter}">${letter}</div>`);

                        });


                        hasForeignInput = true;
                        cwyn = new ComponentWithYesNo(vf, COMPONENT_TYPE.textFieldForeign);
                        cwyn.addKeypressListener();
                        cwyn.addChangeListener();
                        v = cwyn.getJQuery();
                    }
                    else {
                        let vf : JQuery = $(`<div class="inputquizitem"><input id="keyinp_${+qoid}_${headInd}" data-qoid="${+qoid}" data-row="${headInd}" type="text"></div>`);
                        this.keyinps.push(`keyinp_${+qoid}_${headInd}`);

                        cwyn = new ComponentWithYesNo(vf, COMPONENT_TYPE.textField);
                        cwyn.addKeypressListener();
                        cwyn.addChangeListener();
                        v = cwyn.getJQuery();
                    }

                    this.vAnswers.push(new Answer(cwyn, null, trimmedAnswer, featset.matchregexp));
                }

                // In case a number is requested
                else if (featType === 'integer') {
                    // Create this HTML structure in the variable v:      From these variables
                    // <td class="qbox">                                   cwyn
                    //   <img ...>                                         cwyn
                    //   <img ...>                                         cwyn
                    //   <img ...>                                         cwyn
                    //   <input type="number" ...>                         intf
                    // </td>                                               cwyn

                    let intf : JQuery = $('<input type="number">');
                    let cwyn : ComponentWithYesNo = new ComponentWithYesNo(intf,COMPONENT_TYPE.textField);
                    cwyn.addKeypressListener();
                    v = cwyn.getJQuery();
                    this.vAnswers.push(new Answer(cwyn,null,correctAnswer,null));
                }

                // Checkboxes
                else if (featType.substr(0,8)==='list of ') {
                    let subFeatType : string   = featType.substr(8);                // Remove "list of "
                    let values      : string[] = typeinfo.enum2values[subFeatType]; // Possible Emdros feature values
                    let swsValues   : StringWithSort[] = [];                        // StringWithSort equivalents of feature values

                    // Create StringWithSort objects for every feature value
                    for (let i=0, len=values.length; i<len; ++i)
                        swsValues.push(new StringWithSort(getFeatureValueFriendlyName(subFeatType, values[i], false, false), values[i]));

                    // Sort the values using the optional sorting index in the value strings
                    swsValues.sort((a : StringWithSort, b : StringWithSort) => StringWithSort.compare(a,b));

                    // Add "None of these" as a final option
                    swsValues.push(new StringWithSort(`<i>${localize('none_of_these')}</i>`, 'none_of_these'));


                    // Create this HTML structure in the variable v:                  From these variables
                    // <td class="qbox">                                               cwyn
                    //   <img ...>                                                     cwyn
                    //   <img ...>                                                     cwyn
                    //   <img ...>                                                     cwyn
                    //   <table class="list-of">                                       selections
                    //     <tr><td colspan="3">Select one or more:</td></tr>
                    //     <tr>                                                        row
                    //       <td style="text-align:left">
                    //         <div class="selectbutton">
                    //           <input type="checkbox"...><label for=...>VAL1</label>
                    //         </div>
                    //       </td>
                    //       <td style="text-align:left">
                    //         <div class="selectbutton">
                    //           <input type="checkbox"...><label for=...>VAL2</label>
                    //         </div>
                    //       </td>
                    //       <td style="text-align:left">
                    //         <div class="selectbutton">
                    //           <input type="checkbox"...><label for=...>VAL3</label>
                    //         </div>
                    //       </td>
                    //     </tr>                                                       row
                    //     ...
                    //   </table>                                                      table
                    // </td>                                                           cwyn


                    let selections : JQuery = $('<table class="list-of"></table>');
                    selections.append(`<tr><td colspan="3">${localize('select_1_or_more')}</td></tr>`);

                    // Arrange in three columns
                    let numberOfItems : number = swsValues.length;                // Number of values
                    let numberOfRows  : number = Math.floor((numberOfItems+2)/3); // Number of rows with 3 values each

                    for (let r=0; r<numberOfRows; ++r) {
                        let row : JQuery = $('<tr></tr>');
                        for (let c=0; c<3; c++) {
                            let ix = r+c*numberOfRows;
                            if (ix<numberOfItems) {
                                let sc : string = String.fromCharCode(ix+97); // a, b, c, etc.

                                row.append('<td style="text-align:left"><div class="selectbutton">'
                                           + `<input type="checkbox" id="${swsValues[ix].getInternal()}_${quizItemID}" value="${swsValues[ix].getInternal()}">`
                                           + `<label for="${swsValues[ix].getInternal()}_${quizItemID}">${swsValues[ix].getString()}<span class="shortcut multioption">${sc}</span></label>`
                                           + '</div></td>');

                                this.keytable.add(+qoid, headInd, sc, `${swsValues[ix].getInternal()}_${quizItemID}`, 3);
                            }
                            else
                                row.append('<td></td>');
                        }
                        selections.append(row);
                    }

                    let cwyn : ComponentWithYesNo = new ComponentWithYesNo(selections,COMPONENT_TYPE.checkBoxes);
                    cwyn.addChangeListener();
                    v = cwyn.getJQuery();
                    if (correctAnswer==='()')
                        correctAnswer = '(none_of_these)';
                    this.vAnswers.push(new Answer(cwyn,null,correctAnswer,null));
                }

                // One option from a multiple choice list is requested
                else {
                    // This is an enumeration feature type, get the collection of possible values
                    let values: string[] = typeinfo.enum2values[featType]; // Possible Emdros feature values

                    if (values == null) {
                        v.append('<tr><td>&nbsp;</td>'
                            + questionheaders[headInd]
                            + '<td>QuestionPanel.UnknType</td></tr>');
                    }
                    else {
                        // This will be a multiple choice question

                        // Create this HTML structure in the variable v:                    From these variables
                        // <td class="qbox">                                                       cwyn
                        //   <img ...>                                                             cwyn
                        //   <img ...>                                                             cwyn
                        //   <img ...>                                                             cwyn
                        //   <div class="quizitem">                                                quiz_div
                        //
                        //     <div class="selectbutton multiple_choice">                          optArray[0]
                        //       <input type="radio" id="VAL1_N" name="quizitem_N" value="VAL1">   optArray[0]
                        //       <label for="VAL1_N">VAL1</label>                                  optArray[0]
                        //     </div>                                                              optArray[0]
                        //
                        //     <div class="selectbutton multiple_choice">                          optArray[1]
                        //       <input type="radio" id="VAL2_N" name="quizitem_N" value="VAL2">   optArray[1]
                        //       <label for="VAL2_N">VAL2</label>                                  optArray[1]
                        //     </div>                                                              optArray[1]
                        //
                        //     ...
                        //
                        //   </div>                                                                quiz_div
                        // </td>                                                                   cwyn
                        //
                        // In the items above, the letter N is the value of the quizItemID variable

                        let quiz_div : JQuery   = $('<div class="quizitem"></div>');
                        let optArray  : JQuery[] = []; // All the multiple choice options
                        let cwyn      : ComponentWithYesNo = new ComponentWithYesNo(quiz_div, COMPONENT_TYPE.comboBox1); // Result indicator

                        cwyn.addChangeListener();

                        let correctAnswerFriendly : string =          // Localized correct answer:
                                                    getFeatureValueFriendlyName(featType, correctAnswer, false, false);
                        let hasAddedOther         : boolean =         // Have we added an 'Other value' to the list of values?
                                                    false;
                        let correctIsOther        : boolean =         // Is the correct answer one of the values that make up 'Other value'?
                                                    featset.otherValues && featset.otherValues.indexOf(correctAnswer)!==-1 ||
                                                    hideFeatures && hideFeatures.indexOf(correctAnswer) !== -1;



                        // Loop though all possible values and add the appropriate localized name
                        // or "Other value" to the combo box

                        for (let valix in values) {
                            if (isNaN(+valix)) continue; // Not numeric


                            let s : string = values[+valix]; // Feature value under consideration
                            if (featset.hideValues && featset.hideValues.indexOf(s)!==-1)
                                continue;  // Don't show the value s

                            if (featset.otherValues && featset.otherValues.indexOf(s)!==-1 ||
                                hideFeatures && hideFeatures.indexOf(s)!==-1) {
                                // The value s is one of the values that make up 'Other value'
                                if (!hasAddedOther) {
                                    hasAddedOther = true;

                                    let item: StringWithSort = new StringWithSort('#1000 ' + localize('other_value'), 'othervalue');


                                    let option: JQuery = $('<div class="selectbutton">'
                                        + `<input type="radio" id="${item.getInternal()}_${quizItemID}" name="quizitem_${quizItemID}" value="${item.getInternal()}">`
                                        + `<label for="${item.getInternal()}_${quizItemID}">${item.getString()}</label>`
                                        + '</div>');

                                    option
                                        .data('sws',item) // Associate the answer string with the <option> element
                                        .data('char',item.getString()[0].toLowerCase()) // The first character of the option
                                        .data('id',`${item.getInternal()}_${quizItemID}`); // The id of the <input> element

                                    optArray.push(option);
                                    if (correctIsOther)
                                        this.vAnswers.push(new Answer(cwyn,item,localize('other_value'),null));
                                }
                            }
                            else {
                                let sFriendly : string         = getFeatureValueFriendlyName(featType, s, false, false); // Localized value of s
                                let item      : StringWithSort = new StringWithSort(sFriendly,s); // StringWithSort holding the value s
                                let option    : JQuery = $('<div class="selectbutton">'
                                                        + `<input type="radio" id="${item.getInternal()}_${quizItemID}" name="quizitem_${quizItemID}" value="${item.getInternal()}">`
                                                        + `<label for="${item.getInternal()}_${quizItemID}">${item.getString()}</label>`
                                                        + '</div>');

                                option
                                    .data('sws',item) // Associate the answer string with the <option> element
                                    .data('char',item.getString()[0].toLowerCase()) // The first character of the option
                                    .data('id',`${item.getInternal()}_${quizItemID}`); // The id of the <input> element
                                optArray.push(option);
                                if (sFriendly===correctAnswerFriendly) // s is the correct answer
                                    this.vAnswers.push(new Answer(cwyn,item,s,null));
                            }

                        }

                        // Sort the options using the optional sorting index in the value strings
                        optArray.sort((a : JQuery, b : JQuery) => StringWithSort.compare(a.data('sws'),b.data('sws')));


                        // Append optArray to quiz_div and associate keystroke
                        $.each(optArray,
                               (ix : number, o : JQuery) => {
                                   quiz_div.append(o);
                                   this.keytable.add(+qoid, headInd, o.data('char'), o.data('id'), 1);
                               }
                              );

                        v = cwyn.getJQuery();

                    }

                }

                let quizRow: JQuery = $(`<tr id="row_${+qoid}_${headInd}"></tr>`);
                quizRow.append(`<td><span style="display:none" id="ptr_${+qoid}_${headInd}">&gt;</span></td>`);
                quizRow.append(questionheaders[headInd]);
                quizRow.append(v);
                quizTab.append(quizRow);

                ++headInd;
            }

            this.answersPerCard.push(this.vAnswers.length);
            Cursor.set();
        }

        for (let keyi of this.keyinps)
            $(`#${keyi}`)
            .keydown(this, this.textfield_keydown)
            .focus(event => Cursor.set($(event.target).data('qoid'),$(event.target).data('row'),true))
            .blur(() => $('body').unbind('keydown').keydown(this, this.body_keydown));

        $('body')
            .unbind('keydown')
            .keydown(this, // Will be stored in event.data when a key is pressed
                     this.body_keydown);


        this.subQuizMax = quizCardNum

        // Make buttons "Check answer" and "Show answer"
        let quizCard: JQuery = $('.quizcard');

        if (!exam_mode) {
            // Add check and show buttons
            quizCard.append('<div class="buttonlist1">'
                            + `<button class="btn btn-quiz" id="check_answer" type="button">${localize('check_answer')}</button>`
                            + `<button class="btn btn-quiz" id="show_answer" type="button">${localize('show_answer')}</button>`
                            + '</div>');
        }

        // Add prev and next buttons to slide through multiple subquizzes
        if (quizCardNum > 1) {
            let prevsubquiz : JQuery = $('<div class="prev-next-btn prev" id="prevsubquiz">&#10094;</div>');
            quizContainer.prepend(prevsubquiz);
            prevsubquiz.hide();

            quizContainer.append('<div class="prev-next-btn next" id="nextsubquiz">&#10095;</div>');

            // Hide "Next", "GRADE task" and "SAVE outcome" buttons
            $('button#next_question').hide();
            $('button#finish').hide();
            $('button#finishNoStats').hide();
        }


        $('div.inputbutton').click(function () {
            let letter: string = $(this).data('letter'); //String($(this).text());
            $(this)
                  .parent().siblings('span')  // get the input field
                  .text($(this).parent().siblings('span').text() + letter);  // add letter
            $(this).change();
            return false;    // disable default link action (otherwise # added to url)
        });


        $('div.delbutton').click(function () {
            let value: string = String($(this).parent().siblings('span').text());
            $(this)          // the delete button
                  .parent().siblings('span')    // get the input field
                  .text(value.slice(0, -1));  // clear its value

            return false;    // disable default link action (otherwise # added to url)
        });


         // Add previous and next handlers for multiple subquestions
         $('#prevsubquiz').off('click'); // Remove old handler
         $('#prevsubquiz').on('click', () => this.prevNextSubQuestion(-1));
         $('#nextsubquiz').off('click'); // Remove old handler
         $('#nextsubquiz').on('click', () => this.prevNextSubQuestion(1));


	// Add "Check answer" button handler
        $('button#check_answer').off('click'); // Remove old handler
        $('button#check_answer').on('click',
                                    () => {
                                        let firstAns : number = this.subQuizIndex==0 ? 0 : this.answersPerCard[this.subQuizIndex-1];
                                        let lastAns  : number = this.answersPerCard[this.subQuizIndex];
                                        for (let aix=firstAns; aix<lastAns; ++aix) {
                                            let a: Answer = this.vAnswers[aix];
                                            a.checkIt(false,true);
                                        }
                                    }
                                   );

        // Add "Show answer" button handler
        $('button#show_answer').off('click'); // Remove old handler
        $('button#show_answer').on('click',
                                () => {
                                    let firstAns : number = this.subQuizIndex==0 ? 0 : this.answersPerCard[this.subQuizIndex-1];
                                    let lastAns  : number = this.answersPerCard[this.subQuizIndex];
                                    for (let aix=firstAns; aix<lastAns; ++aix) {
                                        let a: Answer = this.vAnswers[+aix];
                                        a.showIt();
                                        a.checkIt(true,true);
                                    }
                                }
            );

        switch (resizer.getWindowSize()) {
        case 'lg':
        case 'xl':
            $('.shortcut').show();
            break;

        default:
            $('.shortcut').hide();
            break;
        }


        this.question_stat.start_time = Math.round((new Date()).getTime() / 1000); // Start time for statistcs
    }
}
