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

        
        // Save question text and location for statistics
        this.question_stat.text = dict.generateSentenceHtml(qd);
        this.question_stat.location = location_realname;


        // Create heading for table of question items

        let colcount : number = 0; // Number of columns in <table> containing question items
        
        if (dontShow) {
            $('#quiztabhead').append('<th>' + localize('item_number') + '</th>');
            this.question_stat.show_feat.names.push('item_number');
            ++colcount;
        }

        for (let sfi in showFeatures) {
            if (isNaN(+sfi)) continue; // Not numeric

            $('#quiztabhead').append('<th>' + getFeatureFriendlyName(oType, showFeatures[sfi]) + '</th>');
            this.question_stat.show_feat.names.push(showFeatures[sfi]);  // Save feature name for statistics
            ++colcount;
        }

        for (let sfi in requestFeatures) {
            if (isNaN(+sfi)) continue; // Not numeric

            $('#quiztabhead').append('<th>' + getFeatureFriendlyName(oType, requestFeatures[sfi].name) + '</th>');
            this.question_stat.req_feat.names.push(requestFeatures[sfi].name);  // Save feature name for statistics
            ++colcount;
        }


        // Create table entries for each question item
        
        let featuresHere    : FeatureMap        = typeinfo.obj2feat[oType];          // Maps feature name => feature type
        let qoFeatures      : util.str2strArr[] = this.buildQuizObjectFeatureList(); // Feature/value pairs for each question object
        let hasForeignInput : boolean           = false;                             // Do we need a virtual keyboard?
        let firstInput      : string            = 'id="firstinput"';                 // ID of <input> to receive virtual keyboard focus
        
        // Loop through all the quiz objects
        for (let qoid in qoFeatures) {
            if (isNaN(+qoid)) continue; // Not numeric

            let currentRow : JQuery          = $('<tr></tr>');    // Current question item
            let fvals      : util.str2strArr = qoFeatures[+qoid]; // Feature/value pairs for current quiz object
            
            if (dontShow) {
                currentRow.append('<td>' + (+qoid+1) + '</td>');  // Item number
                this.question_stat.show_feat.values.push(""+(+qoid+1));  // Save feature value for statistics
            }

            ////////////////////////////////
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

                if (featType==='string' || featType=='ascii')
                    currentRow.append(`<td class="${PanelQuestion.charclass(featset)}">${val==='' ? '-' : val}</td>`);
                else
                    currentRow.append(`<td>${val}</td>`);
            }

            ////////////////////////////////
            // Loop through request features
            for (let rfi in requestFeatures) {
                if (isNaN(+rfi)) continue; // Not numeric

                let rf            : string   = requestFeatures[+rfi].name;         // Feature name
                let usedropdown   : boolean  = requestFeatures[+rfi].usedropdown;  // Use multiple choice?
                let hideFeatures  : string[] = requestFeatures[+rfi].hideFeatures;
                let correctAnswer : string   = fvals[rf] as string;                // Feature value (i.e., the correct answer)
                let featType      : string   = featuresHere[rf];                   // Feature type
                let featset       : FeatureSetting = getFeatureSetting(oType,rf); // Feature configuration
		let v             : JQuery   = null;                               // Component to hold the data entry field or a message

                if (correctAnswer==null)
                    alert('Unexpected correctAnswer==null in panelquestion.ts');
                if (correctAnswer==='')
                    correctAnswer = '-'; // Indicates empty answer

                if (featType==null && rf!=='visual')
                    alert('Unexpected (2) featType==null in panelquestion.ts');
                if (rf==='visual')
                    featType = 'string';


                // The layout of the feature request depends on the type of the feature:

                if (featset.alternateshowrequestDb!=null && usedropdown) {
                    // Multiple choice question item
                    let suggestions : string[] = fvals[rf + '!suggest!'] as string[]; // Values to choose between

                    if (suggestions==null) // No suggestions, just display the answer
                        v = $(`<td class="${PanelQuestion.charclass(featset)}">${correctAnswer}</td>`);
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
                        
                        let mc_div : JQuery = $('<div class="styled-select"></div>');

                        // direction:ltr forces left alignment of options (though not on Firefox)
                        let mc_select : JQuery = $(`<select class="${PanelQuestion.charclass(featset)}" style="direction:ltr">`);

                        mc_div.append(mc_select);

                        let optArray : JQuery[]           = []; // All the multiple choice options
                        let cwyn     : ComponentWithYesNo = new ComponentWithYesNo(mc_div,COMPONENT_TYPE.comboBox2); // Result indicator
                        cwyn.addChangeListener();

                        mc_select.append('<option value="NoValueGiven"></option>'); // Empty default choice
                        
                        for (let valix in suggestions) {
                            if (isNaN(+valix)) continue; // Not numeric

                            // We use a StringWithSort object to handle the option strings. This may
                            // seem unnecessary in this case, but it means that comboboxes can be
                            // handled in a uniform manner.
                            
                            let s      : string         = suggestions[+valix];     // Current suggestion
                            let item   : StringWithSort = new StringWithSort(s,s); // StringWithSort holding the current suggestion
                            let option : JQuery         = $(`<option value="${s}" class="${PanelQuestion.charclass(featset)}">${s}</option>`);

                            option.data('sws',item); // Associate the answer string with the <option> element
                            optArray.push(option);
                            if (s===correctAnswer)
				this.vAnswers.push(new Answer(cwyn,item,s,null));
                        }

                        // Sort the options alphabetically
                        optArray.sort((a : JQuery, b : JQuery) => StringWithSort.compare(a.data('sws'),b.data('sws')));

                        // Append optArray to mc_select
                        $.each(optArray, (ix : number, o : JQuery) => mc_select.append(o));

                        v = cwyn.getJQuery();
                    }
                }
                else if (featType==='string' || featType==='ascii') {
                    // Create this HTML structure in the variable v:      From these variables
                    // <span ...>                                          cwyn
                    //   <img ...>                                         cwyn
                    //   <img ...>                                         cwyn
                    //   <img ...>                                         cwyn
                    //   <input type="text" ...>                           vf
                    // </span>                                             cwyn
                        
                    let cwyn : ComponentWithYesNo;
                    if (featset.foreignText || featset.transliteratedText) {
                        let vf : JQuery =        // A text <input> element with an associated virtual keyboard
                            $(`<input ${firstInput} data-kbid="${PanelQuestion.kbid++}" type="text" size="20"`
                              + ` class="${PanelQuestion.charclass(featset)}"`
                              + ` onfocus="$('#virtualkbid').appendTo('#row${+qoid+1}');VirtualKeyboard.attachInput(this)">`);
                        firstInput = '';
                        hasForeignInput = true;
                        cwyn = new ComponentWithYesNo(vf,COMPONENT_TYPE.textFieldWithVirtKeyboard);
                    }
                    else {                   
                        let vf : JQuery = $('<input type="text" size="20">');
                        cwyn = new ComponentWithYesNo(vf,COMPONENT_TYPE.textField);
                    }
                    cwyn.addKeypressListener();
                    v = cwyn.getJQuery();
                    
                    let trimmedAnswer : string = correctAnswer.trim()
                        .replace(/&lt;/g,'<')
                        .replace(/&gt;/g,'>')
                        .replace(/&quot;/g,'"')
                        .replace(/&amp;/g,'&');   // Unescape HTML characters in correctAnswer
                    this.vAnswers.push(new Answer(cwyn, null, trimmedAnswer, featset.matchregexp));
                }
                else if (featType==='integer') {
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
                                row.append('<td style="text-align:left">'
                                           + `<input type="checkbox" value="${swsValues[ix].getInternal()}">`
                                           + swsValues[ix].getString()
                                           + '</td>');
                            else
                                row.append('<td></td>');
                        }
                        selections.append(row);
                    }
                    
                    let cwyn : ComponentWithYesNo = new ComponentWithYesNo(selections,COMPONENT_TYPE.checkBoxes);
                    cwyn.addChangeListener();
                    v = cwyn.getJQuery();
                    this.vAnswers.push(new Answer(cwyn,null,correctAnswer,null));
                }
                else {
                    // This is an enumeration feature type, get the collection of possible values
                    let values : string[] = typeinfo.enum2values[featType]; // Possible Emdros feature values

                    if (values==null)
                        v = $('<td>QuestionPanel.UnknType</td>');
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

                        let mc_select : JQuery   = $('<select></select>');
                        let optArray  : JQuery[] = []; // All the multiple choice options
                        let cwyn      : ComponentWithYesNo = new ComponentWithYesNo(mc_select,COMPONENT_TYPE.comboBox1); // Result indicator

                        cwyn.addChangeListener();

                        mc_select.append('<option value="NoValueGiven"></option>'); // Empty default choice


                        let correctAnswerFriendly : string =          // Localized correct answer:
                                                    getFeatureValueFriendlyName(featType, correctAnswer, false, false);
                        let hasAddedOther         : boolean =         // Have we added an 'Other value' to the list of values?
                                                    false;
                        let correctIsOther        : boolean =         // Is the correct answer one of the values that make up 'Other value'?
                                                    featset.otherValues && featset.otherValues.indexOf(correctAnswer)!==-1 ||
                                                    hideFeatures && hideFeatures.indexOf(correctAnswer)!==-1;
                        
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

                                    let item   : StringWithSort = new StringWithSort('#1000 ' + localize('other_value'),'othervalue');
                                    let option : JQuery         = $(`<option value="${item.getInternal()}">${item.getString()}</option>`);
                                    
                                    option.data('sws',item); // Associate the answer string with the <option> element
                                    optArray.push(option);
                                    if (correctIsOther) 
					this.vAnswers.push(new Answer(cwyn,item,localize('other_value'),null));
                                }
                            }
                            else {
                                let sFriendly : string         = getFeatureValueFriendlyName(featType, s, false, false); // Localized value of s
                                let item      : StringWithSort = new StringWithSort(sFriendly,s); // StringWithSort holding the value s
                                let option    : JQuery         = $(`<option value="${item.getInternal()}">${item.getString()}</option>`);

                                option.data('sws',item); // Associate the answer string with the <option> element
                                optArray.push(option);
                                if (sFriendly===correctAnswerFriendly) // s is the correct answer
				    this.vAnswers.push(new Answer(cwyn,item,s,null));
                            }
                        }

                        // Sort the options using the optional sorting index in the value strings
                        optArray.sort((a : JQuery, b : JQuery) => StringWithSort.compare(a.data('sws'),b.data('sws')));

                        // Append optArray to mc_select
                        $.each(optArray, (ix : number, o : JQuery) => mc_select.append(o));
                        
                        v = cwyn.getJQuery();
                    }
                }
                
                currentRow.append(v);
            }

            // currentrow now contains a question item. Set in in '#quiztab'.
            $('#quiztab').append(currentRow);

            if (hasForeignInput) // Add row for virtual keyboard
                $('#quiztab').append(`<tr><td colspan="${colcount}" id="row${+qoid+1}" style="text-align:right;"></td></tr>`);
        }

	// Add "Check answer" button
        $('button#check_answer').off('click'); // Remove old handler
        $('button#check_answer').on('click',
                                    () => {
                                        for (let ai in this.vAnswers) {
                                            if (isNaN(+ai)) continue; // Not numeric

                                            let a : Answer = this.vAnswers[+ai];
                                            a.checkIt(false);
                                        }
                                    });
            
        // Add "Show answer" button
        $('button#show_answer').off('click'); // Remove old handler
        $('button#show_answer').on('click',
                                   () => {
                                       for (let ai in this.vAnswers) {
                                           if (isNaN(+ai)) continue; // Not numeric

                                           let a : Answer = this.vAnswers[+ai];
                                           a.showIt();
                                           a.checkIt(true);
                                       }
                                   });

        this.question_stat.start_time = Math.round((new Date()).getTime() / 1000); // Start time for statistcs
    }
}
