// -*- js -*-
// Copyright © 2018 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk

// This code handles displaying a single question of an exercise.

/// <reference path="componentwithyesno.ts" />
/// <reference path="answer.ts" />


//****************************************************************************************************
// PanelQuestion class
//
// This class represents a single question (with multiple question items) of an exercise.
//
class PanelQuestion {
    private qd            : QuizData;      // The information required to generate the exercise
    private sentence      : MonadSet;      // The monads containing the question text in the Emdros database
    private location      : string;        // The current location (localized)
    private vAnswers      : Answer[] = []; // The correct answer for each question item
    private question_stat : QuestionStatistics = new QuestionStatistics; // Answer statistics
    private static kbid   : number = 1;    // Input field identification for virtual keyboard
    private gradingFlag	  : boolean;	   // May the statistics be used for grading the student?
    private subQuizIndex  : number = 0;    // Used to togge subquestions
    private subQuizMax    : number;        // Used to define max number of subquestions

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
    

    private prevNextSubQuestion(n: number): void {

        if (this.subQuizIndex + n >= 0 && this.subQuizIndex + n < this.subQuizMax) {
            this.subQuizIndex += n;
        }
        let i: number;
        let slides: JQuery = $('#quizcontainer').find('.quizcard');
        
        if (this.subQuizIndex < 1) {
            $('#prevsubquiz').css({ "visibility": "hidden" }); // hide button if the quizcard is the first
        };

        if (this.subQuizIndex > 0) {
            $('#prevsubquiz').css({ "visibility": "visible" });
        }

        if (this.subQuizIndex < slides.length - 1) {
            $('#nextsubquiz').css({"visibility": "visible"}) // hide button if the quizcard is the last
        }; 

        if (this.subQuizIndex === slides.length - 1) {
            $('#nextsubquiz').css({"visibility": "hidden"}) // hide button if the quizcard is the last
        }; 

        for (i = 0; i < slides.length; i++) { 
            if (i === this.subQuizIndex) {
                slides.slice(i).css({ "display": "block" });
            } else {
                slides.slice(i).css({ "display": "none" });
            }
        }
        // Scroll to myview
        $('html, body').animate({
            scrollTop: $('#myview').offset().top - 5
          }, 50);
    }

    // Functions used for inputfield manupilation //
    ////////////////////////////////////////////////        
    private delLastCharInput(fieldJQuery: JQuery): void {
        let $value: JQuery = fieldJQuery.find('input');
        $value.val('test');
    }


    //------------------------------------------------------------------------------------------
    // Constructor method
    //
    // Parameter:
    //     qd: The information required to generate a exercise.
    //     dict: The collection of Emdros objects for this question.
    //
    constructor(qd : QuizData, dict : Dictionary) {
        this.qd = qd;
        this.sentence = dict.sentenceSetQuiz;
        ////////////////////////////////////////////////////////////////////
        // Calculate the Bible reference (the 'location') for this sentence.

        // We base the location on the first monad in the sentence.
        let smo : SingleMonadObject = dict.getSingleMonadObject(getFirst(this.sentence));
        let location_realname = ''; // Unlocalized
        this.location = smo.bcv_loc; // Localized
        for (let unix in configuration.universeHierarchy) {
            let unixi : number = +unix;
            if (isNaN(unixi)) continue; // Not numeric

            let uniname : string = configuration.universeHierarchy[unixi].type;

            switch (unixi) {
            case 0:
                location_realname += smo.bcv[unixi] + ', ';
                break;
            case 2:
                location_realname += ', ';
                // Fall through
            case 1:
                location_realname += smo.bcv[unixi];
                break;
            }
        }

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


        
        ///////////////////////////////////
        // Generate table of question items
        
        // Cache a few variables for easy access
        let dontShow        : boolean  = qd.quizFeatures.dontShow;
        let showFeatures    : string[] = qd.quizFeatures.showFeatures;
        let requestFeatures : {name : string; usedropdown : boolean; hideFeatures : string[];}[]
                                       = qd.quizFeatures.requestFeatures;
        let oType           : string   = qd.quizFeatures.objectType;
        
        // Variables for creating table content (<td> parts)
        let featuresHere    : FeatureMap        = typeinfo.obj2feat[oType];          // Maps feature name => feature type
        let qoFeatures      : util.str2strArr[] = this.buildQuizObjectFeatureList(); // Feature/value pairs for each question object
        let hasForeignInput : boolean           = false;                             // Do we need a virtual keyboard?
        let firstInput      : string            = 'id="firstinput"';                 // ID of <input> to receive virtual keyboard focus
        let quizItemID      : number            = 0;                                 // Counts quizitems to be used to group radio buttons together (see name attribute)

        
        // Save question text and location for statistics
        this.question_stat.text = dict.generateSentenceHtml(qd);
        this.question_stat.location = location_realname;

        /////////////////////////////////////////////////
        // CREATE TABLE ENTRIES FOR EACH QUESTION CARD //
        /////////////////////////////////////////////////
        let questionheaders: string[] = []; // Initialize empty array for questionheaders
                                            // For each question card, the headers are pulled out from this list
        let headInd    : number = 0;        // Header index is used to loop over the questionheaders by using
                                            // questionheaders[(headInd % headLen + headLen) % headLen];
                                            // The length of questionheaders depends and will be calculated later on
        
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
        let quizActive: boolean = true;
        let quizContainer: JQuery = $('div#quizcontainer');

        this.subQuizMax = quizCardNum;

        ///////////////////////////////////////
        // Loop through all the quiz objects //
        for (let qoid in qoFeatures) {
            if (isNaN(+qoid)) continue; // Not numeric
            // ++quizCardNum;
            // quizContainer.append(`<div class="quizcard"><table class="quiztab${qoid}"></table></div>`);
            if (quizActive === true) {
                quizContainer.append(`<div class="quizcard" style="display:block;"><table class="quiztab${qoid}"></table></div>`);
                quizActive = false;
            } else {
                quizContainer.append(`<div class="quizcard" style="display:none;"><table class="quiztab${qoid}"></table></div>`);
            }
            
            let quizTab: JQuery = $(`table.quiztab${qoid}`);
            let fvals: util.str2strArr = qoFeatures[+qoid]; // Feature/value pairs for current quiz object
            
            //////////////////////////////////////////////////////////////////////////////////////////////
            // Loop through all headers in question headers and append them one by one to each question //

            // Features that are marked by 'dontShow'
            if (dontShow) {
                quizTab.append('<tr>' + questionheaders[(headInd % headLen + headLen) % headLen]
                    + '<td>' + (+qoid + 1)
                    + '</td></tr>');
                ++headInd;
                this.question_stat.show_feat.values.push("" + (+qoid + 1));  // Save feature value for statistics
            }
            // Loop through display features
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
                    quizTab.append('<tr>'
                        + questionheaders[(headInd % headLen + headLen) % headLen]
                        + `<td class="${PanelQuestion.charclass(featset)}">${val === '' ? '-' : val}</td></tr>`);
                    ++headInd;
                }
                else {
                    quizTab.append('<tr>'
                        + questionheaders[(headInd % headLen + headLen) % headLen]
                        + `<td>${val}</td></tr>`);
                    ++headInd;
                }
            }
                
            ///////////////////////////////////
            // Loop through request features //
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

                    if (suggestions==null) // No suggestions, just display the answer
                        v = $('<tr>'
                            +  + questionheaders[(headInd % headLen + headLen) % headLen]
                            + `<td class="${PanelQuestion.charclass(featset)}">${correctAnswer}</td>`
                            + '</tr>');
                    else {
                        // Create this HTML structure in the variable v:            From these variables
                        // <span ...>                                                cwyn
                        //   <img ...>                                               cwyn
                        //   <img ...>                                               cwyn
                        //   <img ...>                                               cwyn
                        //   <div class="styled-select">                             mc_div
                        //     <select class="..." style="direction:ltr">            mc_select
                        //       <option value="NoValueGiven"></option>              
                        //       <option value="VAL1" class="...">VAL1</option>      optArray[0]
                        //       <option value="VAL2" class="...">VAL2</option>      optArray[1]
                        //       ...                                                 
                        //     </select>                                             mc_select
                        //   </div>                                                  mc_div
                        // </span>                                                   cwyn
                        
                        let quiz_div : JQuery = $('<div class="quizitem"></div>'); // Used to ancor the radio buttons and to add additional data

                        // // direction:ltr forces left alignment of options (though not on Firefox)
                        // let mc_select : JQuery = $(`<select class="${PanelQuestion.charclass(featset)}" style="direction:ltr">`);

                        // mc_div.append(mc_select);

                        let optArray : JQuery[]           = []; // All the multiple choice options
                        let cwyn     : ComponentWithYesNo = new ComponentWithYesNo(quiz_div ,COMPONENT_TYPE.comboBox2); // Result indicator
                        cwyn.addChangeListener();

                        // mc_select.append('<option value="NoValueGiven"></option>'); // Empty default choice
                        
                        for (let valix in suggestions) {
                            if (isNaN(+valix)) continue; // Not numeric

                            // We use a StringWithSort object to handle the option strings. This may
                            // seem unnecessary in this case, but it means that comboboxes can be
                            // handled in a uniform manner.
                            
                            let s      : string         = suggestions[+valix];     // Current suggestion
                            let item   : StringWithSort = new StringWithSort(s,s); // StringWithSort holding the current suggestion
                            let option: JQuery = $('<div class="selectbutton">'
                                + `<input type ="radio" id="${item.getInternal()}_${quizItemID}" name="quizitem_${quizItemID}" value="${item.getInternal()}">`
                                + `<label for="${item.getInternal()}_${quizItemID}">${item.getString()}</label>`
                                + '</div>');

                            option.data('sws',item); // Associate the answer string with the <option> element
                            optArray.push(option);
                            if (s===correctAnswer)
                                this.vAnswers.push(new Answer(cwyn,item,s,null));
                        }

                        // Sort the options alphabetically
                        optArray.sort((a : JQuery, b : JQuery) => StringWithSort.compare(a.data('sws'),b.data('sws')));

                        // Append optArray to quiz_div
                        $.each(optArray, (ix : number, o : JQuery) => quiz_div.append(o));

                        v = cwyn.getJQuery();
                    }
                }

                // In case a text input is requested
                else if (featType==='string' || featType==='ascii') {
                    // Create this HTML structure in the variable v:      From these variables
                    // <span ...>                                          cwyn
                    //   <img ...>                                         cwyn
                    //   <img ...>                                         cwyn
                    //   <img ...>                                         cwyn
                    //   <input type="text" ...>                           vf
                    // </span>                                             cwyn
                    
                    
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
                        $.each(answerArray, function(i, el){
                            if($.inArray(el, answerLetters) === -1) answerLetters.push(el);
                        });

                        ////////////////////////
                        // Check for shin-sin //
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
                            
                        // push combined letter if shinDot or sinDot === true
                        if (shinDot === true) {
                            answerLetters.push('ש' + '\u05C1');
                        }
                        if (sinDot === true) {
                            answerLetters.push('ש' + '\u05C2');
                        }

                        // Add additional letters to a maximum of 12 unique letters total
                        for (let index = 0; index < answerLetters.length; index++) {
                            let l: string = answerLetters[index];
                            switch (l) {
                                //////////////////////////////
                                // Hebrew Regular consonants
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
                        additionalCons = additionalCons.sort(function() {
                            return .5 - Math.random();
                          });
                        // Randomize additional Vowels
                        additionalVowels = additionalVowels.sort(function() {
                            return .5 - Math.random();
                          });

                        // Push max 2 Consonants and max 3 Vowels to answerLetters
                        // Make them all unique
                        answerLettersRandom = answerLetters.concat(additionalCons.slice(0, 3))
                            .concat(additionalVowels.slice(0, 3));
                        
                        // Make all letters unique and safe them in showLetters
                        $.each(answerLettersRandom, function(i, el){
                                if($.inArray(el, showLetters) === -1) showLetters.push(el);
                        });
                        
                        // Sort letters alphabetically
                        showLetters.sort();
                        
                        let vf : JQuery =        // A text <input> element with an associated virtual keyboard
                            $(`<div class="inputquizitem"><input ${firstInput} data-kbid="${PanelQuestion.kbid++}" type="text"`
                                + ` class="${PanelQuestion.charclass(featset)}"></div>`);
                        
                        // Set del button to remove letters from input field
                        vf.append(`<div class="letterinput"><div class="selectbutton delbutton" id="delinputchar"><label for="delinputchar">del</label></div></div>`);

                        // Set randomized letter buttons to be inputted in the input field
                        showLetters.forEach(letter => {
                            vf.find('.letterinput').append(`<div class="selectbutton inputbutton" id="inputchar"><label for="inputchar" class="${PanelQuestion.charclass(featset)}">${letter}</label></div>`);
                        });

        
                        firstInput = '';
                        hasForeignInput = true;
                        cwyn = new ComponentWithYesNo(vf, COMPONENT_TYPE.textField);
                        cwyn.addKeypressListener();
                        v = cwyn.getJQuery();

                    }
                    else {                   
                        let vf : JQuery = $('<div class="inputquizitem"><input type="text"></div>');
                        cwyn = new ComponentWithYesNo(vf, COMPONENT_TYPE.textField);
                        cwyn.addKeypressListener();
                        v = cwyn.getJQuery();
                    }
                    
                    this.vAnswers.push(new Answer(cwyn, null, trimmedAnswer, featset.matchregexp));
                }
                
                // In case a number is requested
                else if (featType === 'integer') {
                    // Create this HTML structure in the variable v:      From these variables
                    // <span ...>                                          cwyn
                    //   <img ...>                                         cwyn
                    //   <img ...>                                         cwyn
                    //   <img ...>                                         cwyn
                    //   <input type="number" ...>                         intf
                    // </span>                                             cwyn
                        
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
                    

                    // Create this HTML structure in the variable v:      From these variables
                    // <span ...>                                          cwyn
                    //   <img ...>                                         cwyn
                    //   <img ...>                                         cwyn
                    //   <img ...>                                         cwyn
                    //   <table class="list-of">                           selections
                    //     <tr>                                            row
                    //       <td style="text-align:left">
                    //         <input type="checkbox"...>VAL1
                    //       </td>
                    //       <td style="text-align:left">
                    //         <input type="checkbox"...>VAL2
                    //       </td>
                    //       <td style="text-align:left">
                    //         <input type="checkbox"...>VAL3
                    //       </td>
                    //     </tr>                                           row
                    //     ...
                    //   </table>                                          table
                    // </span>                                             cwyn


                    let selections : JQuery = $('<table class="list-of"></table>');

                    // Arrange in three columns
                    let numberOfItems : number = swsValues.length;                // Number of values
                    let numberOfRows  : number = Math.floor((numberOfItems+2)/3); // Number of rows with 3 values each

                    for (let r=0; r<numberOfRows; ++r) {
                        let row : JQuery = $('<tr></tr>');
                        for (let c=0; c<3; c++) {
                            let ix = r+c*numberOfRows;
                            if (ix<numberOfItems)
                                row.append(questionheaders[(headInd % headLen + headLen) % headLen]
                                        + '<td style="text-align:left">'
                                        + `<input type="checkbox" value="${swsValues[ix].getInternal()}">`
                                        + swsValues[ix].getString()
                                        + '</td>');
                            else
                                row.append(questionheaders[(headInd % headLen + headLen) % headLen]
                                           + '<td></td>');
                        }
                        selections.append(row);
                    }
                    
                    let cwyn : ComponentWithYesNo = new ComponentWithYesNo(selections,COMPONENT_TYPE.checkBoxes);
                    cwyn.addChangeListener();
                    v = cwyn.getJQuery();
                    this.vAnswers.push(new Answer(cwyn,null,correctAnswer,null));
                }
                
                // One option from a dropdown list is requested
                else {
                    // This is an enumeration feature type, get the collection of possible values
                    let values: string[] = typeinfo.enum2values[featType]; // Possible Emdros feature values

                    if (values == null) {
                        v.append('<tr>'
                            + questionheaders[(headInd % headLen + headLen) % headLen]
                            + '<td>QuestionPanel.UnknType</td></tr>');
                    }
                    else {
                        // This will be a multiple choice question

                        // Create this HTML structure in the variable v:          From these variables
                        // <span ...>                                              cwyn
                        //   <img ...>                                             cwyn
                        //   <img ...>                                             cwyn
                        //   <img ...>                                             cwyn
                        //   <select class="..." style="direction:ltr">            mc_select
                        //     <option value="NoValueGiven"></option>
                        //     <option value="VAL1" class="...">VAL1</option>      optArray[0]
                        //     <option value="VAL2" class="...">VAL2</option>      optArray[1]
                        //     ...
                        //   </select>                                             mc_select
                        // </span>                                                 cwyn

                        // <input type="radio" id="male" name="gender" value="male">
                        // <label for= "male" > Male < /label>
                            
                        let quiz_div : JQuery   = $('<div class="quizitem"></div>');
                        let optArray  : JQuery[] = []; // All the multiple choice options
                        let cwyn      : ComponentWithYesNo = new ComponentWithYesNo(quiz_div, COMPONENT_TYPE.comboBox1); // Result indicator

                        cwyn.addChangeListener();

                        // mc_select.append('<option value="NoValueGiven"></option>'); // Empty default choice


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
                                        + `<input type ="radio" id="${item.getInternal()}_${quizItemID}" name="quizitem_${quizItemID}" value="${item.getInternal()}">`
                                        + `<label for="${item.getInternal()}_${quizItemID}">${item.getString()}</label>`
                                        + '</div>');
                                    
                                    option.data('sws',item); // Associate the answer string with the <option> element
                                    optArray.push(option);
                                    if (correctIsOther) 
                                        this.vAnswers.push(new Answer(cwyn,item,localize('other_value'),null));
                                }
                            }
                            else {
                                let sFriendly : string         = getFeatureValueFriendlyName(featType, s, false, false); // Localized value of s
                                let item      : StringWithSort = new StringWithSort(sFriendly,s); // StringWithSort holding the value s
                                let option    : JQuery = $('<div class="selectbutton">'
                                                        + `<input type ="radio" id="${item.getInternal()}_${quizItemID}" name="quizitem_${quizItemID}" value="${item.getInternal()}">`
                                                        + `<label for="${item.getInternal()}_${quizItemID}">${item.getString()}</label>`
                                                        + '</div>');
                                
                                option.data('sws',item); // Associate the answer string with the <option> element
                                optArray.push(option);
                                if (sFriendly===correctAnswerFriendly) // s is the correct answer
                                    this.vAnswers.push(new Answer(cwyn,item,s,null));
                            }
                            
                        }
                        // ++quizItemID // Update quizItemID for the next grouping of radio buttons

                        // Sort the options using the optional sorting index in the value strings
                        optArray.sort((a : JQuery, b : JQuery) => StringWithSort.compare(a.data('sws'),b.data('sws')));

                        // Append optArray to mc_select
                        $.each(optArray, (ix : number, o : JQuery) => quiz_div.append(o));
                        
                        v = cwyn.getJQuery();
                    
                    }       
 
                }

                let quizRow: JQuery = $('<tr></tr>');
                quizRow.append(questionheaders[(headInd % headLen + headLen) % headLen]);
                quizRow.append(v);
                quizTab.append(quizRow);
                
                ++headInd;
            }      
           

        }

        this.subQuizMax = quizCardNum

        // Make buttons "Check answer" and "Show answer"
        let quizCard: JQuery = $('.quizcard');

        // Add check and show buttons
        quizCard.append('<div class="buttonlist1">'
            + `<button class="btn btn-quiz" id="check_answer" type="button">${localize('check_answer')}</button>`
            + `<button class="btn btn-quiz" id="show_answer" type="button">${localize('show_answer')}</button>`
            + '</div>');

        // Add prev and next buttons to slide through multiple subquizzes
        if (quizCardNum > 1) {
            quizContainer.prepend('<div class="prev-next-btn prev" id="prevsubquiz" style="visibility:hidden;">&#10094;</div>');
            quizContainer.append('<div class="prev-next-btn next" id="nextsubquiz">&#10095;</div>');    
            // quizContainer.prepend('<div class="prev-next-btn"><a class="prev" onclick="plusSubQuiz(-1)">&#10094;</a></div>');
            // quizContainer.append('<div class="prev-next-btn"><a class="next" onclick="plusSubQuiz(1)">&#10095;</a></div>'); 
        }
        
        '<div class="selectbutton inputbutton" id="inputchar"><label for="inputchar">${letter}</label></div>'

        $('div#inputchar').click(function () {
            let letter: string = String($(this).find('label').text());
            $(this)          // the delinputchar button
                  .parent().siblings('input')  // get the input field
                  .val($(this).parent().siblings('input').val() + letter);  // add letter
        
            return false;    // disable default link action (otherwise # adds to url)
        });


        $('div#delinputchar').click(function () {
            let value: string = String($(this).parent().siblings('input').val());
            $(this)          // the delinputchar button
                  .parent().siblings('input')    // get the input field
                  .val(value.slice(0, -1));  // clear its value
        
            return false;    // disable default link action (otherwise # adds to url)
        });
    

         // Add previous and next handlers for multiple subquestions
         $('#prevsubquiz').off('click'); // Remove old handler
         $('#prevsubquiz').on('click',
                                () => {
                                    this.prevNextSubQuestion(-1);
                                });
         $('#nextsubquiz').off('click'); // Remove old handler
         $('#nextsubquiz').on('click',
                                () => {
                                    this.prevNextSubQuestion(1);
                                });

        
	    // Add "Check answer" button handler
        $('button#check_answer').off('click'); // Remove old handler
        $('button#check_answer').on('click',
                                    () => {
                                        for (let ai in this.vAnswers) {
                                            if (isNaN(+ai)) continue; // Not numeric

                                            let a: Answer = this.vAnswers[+ai];
                                            a.checkIt(false);
                                        }

                                        $('html, body').animate({
                                            scrollTop: $('#myview').offset().top - 5
                                        }, 50);

                                    }
                                    );
            
        // Add "Show answer" button handler
        $('button#show_answer').off('click'); // Remove old handler
        $('button#show_answer').on('click',
                                () => {
                                    for (let ai in this.vAnswers) {
                                        if (isNaN(+ai)) continue; // Not numeric

                                        let a: Answer = this.vAnswers[+ai];
                                        a.showIt();
                                        a.checkIt(true);
                                    }

                                    $('html, body').animate({
                                        scrollTop: $('#myview').offset().top - 5
                                    }, 50);

                                }
            );
        
       

        this.question_stat.start_time = Math.round((new Date()).getTime() / 1000); // Start time for statistcs
    }
}
