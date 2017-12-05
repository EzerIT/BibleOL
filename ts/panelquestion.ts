// -*- js -*-
/// <reference path="componentwithyesno.ts" />
/// <reference path="answer.ts" />


function charclass(featset : FeatureSetting, charset : Charset)
{
    return featset.foreignText ? charset.foreignClass
                               : featset.transliteratedText ? charset.transliteratedClass : '';
}

class PanelQuestion {
    /** The information required to generate the quiz. */
    private qd : QuizData;
    
    /** The monads containing the question text in the Emdros database. */
    private sentence : MonadSet;
    
    /** The current location. */
    private location : string; // Localized
    
    /** The correct answer for each question. */
    private vAnswers : Answer[] = [];
    
    private question_stat : QuestionStatistics = new QuestionStatistics;

    private static kbid = 1; // Input field identification for virtual keyboard
    
    private gradingFlag : number;

    public updateQuestionStat(gradingFlag) : QuestionStatistics {
        this.question_stat.end_time = Math.round((new Date()).getTime() / 1000);

        this.commitAll()
        for (var i:number=0, len=this.vAnswers.length; i<len; ++i) {
            var ans : Answer = this.vAnswers[i];
            this.question_stat.req_feat.correct_answer.push(ans.correctAnswer());
            this.question_stat.req_feat.users_answer.push(ans.usersAnswer());
            this.question_stat.req_feat.users_answer_was_correct.push(ans.usersAnswerWasCorrect());
        }

	this.question_stat.grading = gradingFlag;
        
        return this.question_stat;
    }

    /** Gets the question name.
     * @return The question name.
     */
    //public String getQName() {
    //    return m_qName.getText();
    //}
    
    /** Gets the question title.
     * @return The question title.
     */
    //public String getQTitle() {
    //    return m_qTitle.getText();
    //}
    

    /** Creates a list of feature=&gt;value maps holding the features for each question object.
     * @return A list of feature/value pairs for each question object.
     */
    private buildQuizObjectFeatureList() : string[][] {
        // qoFeatures holds the feature/value pairs for each question object
        var qoFeatures : string[][] = [];
        var hasSeen : Array<boolean> = []; // idd => true if seen
        
        var allmonads = getMonadArray(this.sentence);
        for (var i:number=0, len=allmonads.length; i<len; ++i) {
            var id_d : number = this.qd.monad2Id[allmonads[i]];
            if (id_d) {
                if (!hasSeen[id_d]) {
                    qoFeatures.push(this.qd.id2FeatVal[id_d]);
                    hasSeen[id_d] = true;
                }
            }
        }
        return qoFeatures;
    }
    
    
    
    //public generateSentenceText() : string {
        // FOR MOODLE
    //}
    
    
    /**
     * Constructs a {@code PanelQuestion} that is to be part of a {@link PanelGeneratedQuestionSet}
     * or {@link PanelContinuousQuestions} panel.
     * @param qd The information required to generate a quiz.
     * @param generator True if this is called as part of a question set generation for Moodle
     */
    constructor(qd : QuizData, dict : Dictionary, generator : boolean) {
        this.qd = qd;
        this.sentence = dict.sentenceSet;
        
        // We base the location on the first monad in the sentence
        var smo : SingleMonadObject = dict.getSingleMonadObject(getFirst(this.sentence));
        var location_realname = ''; // Unlocalized
        this.location = smo.bcv_loc; // Localized
        for (var unix in configuration.universeHierarchy) {
            var unixi : number = +unix;
            if (isNaN(unixi)) continue; // Not numeric

            var uniname : string = configuration.universeHierarchy[unixi].type;

            // TODO: This only works for Bible references
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
        
        // Optimisations:
        var dontShow : boolean                                           = qd.quizFeatures.dontShow;
        var showFeatures : string[]                                   = qd.quizFeatures.showFeatures;
        var requestFeatures : {name : string; usedropdown : boolean; }[] = qd.quizFeatures.requestFeatures;
        var oType : string                                            = qd.quizFeatures.objectType;
        
	if (generator) {
            // TODO: Quiz generator for Moodle not yet implementd
        }
        else {
            this.question_stat.text = dict.generateSentenceHtml(qd);
            this.question_stat.location = location_realname;
        }

        var colcount = 0;

        if (dontShow) {
            $('#quiztabhead').append('<th>Item number</th>');
            this.question_stat.show_feat.names.push('item_number');
            ++colcount;
        }

        for (var sfi in showFeatures) {
            if (isNaN(+sfi)) continue; // Not numeric

            $('#quiztabhead').append('<th>' + getFeatureFriendlyName(oType, showFeatures[sfi]) + '</th>');
            this.question_stat.show_feat.names.push(showFeatures[sfi]);
            ++colcount;
        }

        for (var sfi in requestFeatures) {
            if (isNaN(+sfi)) continue; // Not numeric

            $('#quiztabhead').append('<th>' + getFeatureFriendlyName(oType, requestFeatures[sfi].name) + '</th>');
            this.question_stat.req_feat.names.push(requestFeatures[sfi].name);
            ++colcount;
        }

        // The requested object type can have these features. This maps feature name to feature type.
        var featuresHere : FeatureMap = typeinfo.obj2feat[oType];
        
        // qoFeatures holds the feature/value pairs for each question object
        var qoFeatures : string[][] = this.buildQuizObjectFeatureList();

        var hasForeignInput : boolean = false;
        var firstInput : string = 'id="firstinput"'; // ID of <input> to receive virtual keyboard focus
        
        // Loop through all the quiz objects
        for (var qoid in qoFeatures) {
            if (isNaN(+qoid)) continue; // Not numeric

            var currentRow : JQuery = $('<tr></tr>');
            var mm : string[] = qoFeatures[+qoid]; // Feature/value pairs for current quiz object
            
            if (dontShow) {
                currentRow.append('<td>' + (+qoid+1) + '</td>');
                this.question_stat.show_feat.values.push(""+(+qoid+1));
            }

            // Loop through show features
            for (var sfi in showFeatures) {
                if (isNaN(+sfi)) continue; // Not numeric

                var sf : string = showFeatures[+sfi];        // Feature name
                var val : string = mm[sf];                   // Feature value
                var featType : string = featuresHere[sf];    // Feature type
                var featset : FeatureSetting = getFeatureSetting(oType,sf);

                this.question_stat.show_feat.values.push(val);

                if (featType==null && sf!=='visual')
                    alert('Unexpected (1) featType==null in panelquestion.ts; sf="' + sf + '"');

                if (/*featType==null && */   //TODO: Why this?
                    sf==='visual')
                    featType = 'string';

                
                if (featType!=='string' && featType!=='ascii' && featType!=='integer') {
                    // This is an enumeration feature type
                    // Replace val with the appropriate friendly name or "Other value"
                    if (featset.otherValues && featset.otherValues.indexOf(val)!==-1)
                        val = localize('other_value');
                    else
                        val = getFeatureValueFriendlyName(featType, val, false, true);
                }

                if (val==null)
                    alert('Unexpected val==null in panelquestion.ts');

                if (/*val!=null && */ // TODO: Why this?
                    (featType==='string' || featType=='ascii'))
                    currentRow.append('<td class="{0}">{1}</td>'.format(charclass(featset,charset), val==='' ? '-' : val));
                else
                    currentRow.append('<td>' + val + '</td>');
            }

            // Loop through request features
            for (var rfi in requestFeatures) {
                if (isNaN(+rfi)) continue; // Not numeric

                var rf : string = requestFeatures[+rfi].name; // Feature name
                var usedropdown : boolean = requestFeatures[+rfi].usedropdown;

                var correctAnswer : string = mm[rf]; // Feature value (i.e., the correct answer)
		var v : JQuery = null; // Component to hold the data entry field or an error message
                if (correctAnswer==null)
                    alert('Unexpected correctAnswer==null in panelquestion.ts');
                if (correctAnswer==='')
                    correctAnswer = '-'; // Indicates empty answer

                if (correctAnswer!=null /* TODO: Why this? */) {
                    var featType : string = featuresHere[rf];    // Feature type
                    var featset : FeatureSetting = getFeatureSetting(oType,rf);

                    if (featType==null && rf!=='visual')
                        alert('Unexpected (2) featType==null in panelquestion.ts');
                    if (/*featType==null && */  // TODO: Why this?
                        rf==='visual')
                        featType = 'string';

                    if (featset.alternateshowrequestDb!=null && usedropdown) {
                        var suggestions : string[] = mm[rf + '!suggest!'];
                        if (suggestions==null)
                            v = $('<td class="{0}">{1}</td>'
                                  .format(charclass(featset,charset), correctAnswer));
                        else {
                            // This will be a multiple choice question
                            var selectdiv : JQuery = $('<div class="styled-select"></div>');

                            // direction:ltr forces left alignment of options (though not on Firefox)
                            var jcb : JQuery = $('<select class="{0}" style="direction:ltr">'
                                                 .format(charclass(featset,charset)));

                            selectdiv.append(jcb);
                            var optArray : JQuery[] = [];
                            var cwyn : ComponentWithYesNo = new ComponentWithYesNo(selectdiv,COMPONENT_TYPE.comboBox2);
                            cwyn.addChangeListener();

                            jcb.append('<option value="NoValueGiven"></option>'); // Empty default choice
                            
                            for (var valix in suggestions) {
                                if (isNaN(+valix)) continue; // Not numeric

                                var s : string = suggestions[+valix];
                                var item = new StringWithSort(s,s);
                                var option : JQuery = $('<option value="{0}" class="{1}">{2}</option>'
                                                        .format(item.getInternal(),charclass(featset,charset),item.getString()));
                                option.data('sws',item);
                                optArray.push(option);
                                if (s===correctAnswer)
				    this.vAnswers.push(new Answer(cwyn,item,s,null));
                            }
                            optArray.sort((a : JQuery, b : JQuery) => StringWithSort.compare(a.data('sws'),b.data('sws')));
                            $.each(optArray, (ix : number, o : JQuery) => jcb.append(o));

                            v = cwyn.appendMeTo($('<td></td>'));
                        }
                    }
                    else if (featType==='string' || featType==='ascii') {
                        var cwyn : ComponentWithYesNo;
                        if (featset.foreignText || featset.transliteratedText) {
                            var vf : JQuery =
                                $('<input {0} data-kbid="{1}" type="text" size="20" class="{2}" onfocus="$(\'#virtualkbid\').appendTo(\'#row{3}\');VirtualKeyboard.attachInput(this)">'
                                  .format(firstInput, PanelQuestion.kbid++, charclass(featset,charset), +qoid+1));
                            firstInput = '';
                            hasForeignInput = true;
                            cwyn = new ComponentWithYesNo(vf,COMPONENT_TYPE.textFieldWithVirtKeyboard);
                        }
                        else {                   
                            var vf : JQuery = $('<input type="text" size="20">'); // VerifiedField
                            cwyn = new ComponentWithYesNo(vf,COMPONENT_TYPE.textField);
                        }
                        cwyn.addKeypressListener();
                        v = cwyn.appendMeTo($('<td></td>'));
                        
                        var trimmedAnswer : string = correctAnswer.trim()
                            .replace(/&lt;/g,'<')
                            .replace(/&gt;/g,'>')
                            .replace(/&quot;/g,'"')
                            .replace(/&amp;/g,'&');
                        this.vAnswers.push(new Answer(cwyn, null, trimmedAnswer, featset.matchregexp));
                    }
                    else if (featType==='integer') {
                        var intf : JQuery = $('<input type="number">');
                        var cwyn : ComponentWithYesNo = new ComponentWithYesNo(intf,COMPONENT_TYPE.textField);
                        cwyn.addKeypressListener();
                        v = cwyn.appendMeTo($('<td></td>'));
                        this.vAnswers.push(new Answer(cwyn,null,correctAnswer,null));
                    }
                    else if (featType.substr(0,8)==='list of ') {
                        var subFeatType = featType.substr(8); // Remove "list of "
                        var values : string[] = typeinfo.enum2values[subFeatType];
                        var swsValues : StringWithSort[] = [];

                        for (var i:number=0, len=values.length; i<len; ++i)
                            swsValues.push(new StringWithSort(getFeatureValueFriendlyName(subFeatType, values[i], false, false), values[i]));
                        swsValues.sort((a : StringWithSort, b : StringWithSort) => StringWithSort.compare(a,b));
                        
                        var selections : JQuery = $('<table class="list-of"></table>');

                        // Arrange in three columns
                        var numberOfItems = swsValues.length;
                        var numberOfRows = Math.floor((numberOfItems+2)/3);

                        for (var r=0; r<numberOfRows; ++r) {
                            var row : JQuery = $('<tr></tr>');
                            for (var c=0; c<3; c++) {
                                var ix = r+c*numberOfRows;
                                if (ix<numberOfItems)
                                    row.append('<td style="text-align:left"><input type="checkbox" value="{0}">{1}</td>'
                                               .format(swsValues[ix].getInternal(), swsValues[ix].getString()));
                                else
                                    row.append('<td></td>');
                            }
                            selections.append(row);
                        }
                        
                        var cwyn : ComponentWithYesNo = new ComponentWithYesNo(selections,COMPONENT_TYPE.checkBoxes);
                        cwyn.addChangeListener();
                        v = cwyn.appendMeTo($('<td></td>'));
                        this.vAnswers.push(new Answer(cwyn,null,correctAnswer,null));
                    }
                    else {
                        // This is an enumeration feature type, get the collection of possible values
                        var values : string[] = typeinfo.enum2values[featType];
                        if (values==null)
                            v = $('<td>QuestionPanel.UnknType</td>');
                        else {
                            // This will be a multiple choice question
                            var jcb : JQuery = $('<select></select>');
                            var optArray : JQuery[] = [];
                            var cwyn : ComponentWithYesNo = new ComponentWithYesNo(jcb,COMPONENT_TYPE.comboBox1);
                            cwyn.addChangeListener();

                            jcb.append('<option value="NoValueGiven"></option>'); // Empty default choice
                            
                            var correctAnswerFriendly : string = getFeatureValueFriendlyName(featType, correctAnswer, false, false);
                            
                            var hasAddedOther : boolean = false;
                            var correctIsOther : boolean = featset.otherValues && featset.otherValues.indexOf(correctAnswer)!==-1;
                            
                            // Loop though all possible values and add the appropriate friendly name
                            // or "Other value" to the combo box
                            for (var valix in values) {
                                if (isNaN(+valix)) continue; // Not numeric

                                var s : string = values[+valix];
                                if (featset.hideValues && featset.hideValues.indexOf(s)!==-1)
                                    continue;
                                // TODO: if (Three_ET.dbInfo.isDupFeatureValueFriendlyNameA(featType, s))  - Westminster
                                // TODO:     continue;
                                if (featset.otherValues && featset.otherValues.indexOf(s)!==-1) {
                                    if (!hasAddedOther) {
                                        hasAddedOther = true;
                                        var item = new StringWithSort('#1000 ' + localize('other_value'),'othervalue');
                                        var option : JQuery = $('<option value="{0}">{1}</option>'
                                                                .format(item.getInternal(),item.getString()));
                                        option.data('sws',item);
                                        optArray.push(option);
                                        if (correctIsOther)
					    this.vAnswers.push(new Answer(cwyn,item,localize('other_value'),null));
                                    }
                                }
                                else {
                                    var sFriendly : string = getFeatureValueFriendlyName(featType, s, false, false);
                                    var item = new StringWithSort(sFriendly,s);
                                    var option : JQuery = $('<option value="{0}">{1}</option>'
                                                            .format(item.getInternal(),item.getString()));
                                    option.data('sws',item);
                                    optArray.push(option);
                                    if (sFriendly===correctAnswerFriendly) // Correct answer
					this.vAnswers.push(new Answer(cwyn,item,s,null));
                                }
                            }
                            optArray.sort((a : JQuery, b : JQuery) => StringWithSort.compare(a.data('sws'),b.data('sws')));
                            $.each(optArray, (ix : number, o : JQuery) => jcb.append(o));
                            v = cwyn.appendMeTo($('<td></td>'));
                        }
                    }
                }
                else {
                    alert('Unexpected correctAnswer==null');
                    v = $('<td>WHAT?</td>'); // TODO: When can this happen?
                }
 
                currentRow.append(v);
            }
            $('#quiztab').append(currentRow);
            if (hasForeignInput) // Add row for storing keyboard
                $('#quiztab').append('<tr><td colspan="{0}" id="row{1}" style="text-align:right;"></td></tr>'.format(colcount, +qoid+1));
        }

	// Add "Check answer" button
        $('button#check_answer').off('click'); // Remove old handler
        $('button#check_answer').on('click',
                                    () => {
                                        for (var ai in this.vAnswers) {
                                            if (isNaN(+ai)) continue; // Not numeric

                                            var a : Answer = this.vAnswers[+ai];
                                            a.checkIt(false);
                                        }
                                    });
            
        // Add "Show answer" button
        $('button#show_answer').off('click'); // Remove old handler
        $('button#show_answer').on('click',
                                   () => {
                                       for (var ai in this.vAnswers) {
                                           if (isNaN(+ai)) continue; // Not numeric

                                           var a : Answer = this.vAnswers[+ai];
                                           a.showIt();
                                           a.checkIt(true);
                                       }
                                   });

        this.question_stat.start_time = Math.round((new Date()).getTime() / 1000);
    }

    /** This function is called when the question panel is being closed. 
     * Unanswered questions will be marked as such.
     */
    private commitAll() : void {
        for (var i:number=0, len=this.vAnswers.length; i<len; ++i)
            this.vAnswers[i].commitIt();
    }
}
