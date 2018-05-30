// -*- js -*-
/* 2013 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk */

/// <reference path="statistics.ts" />

declare var VirtualKeyboard : any;
var tOutInner : any;
var tOutOuter : any;
var tDialog : any;

class Quiz {
    private currentDictIx : number = -1; ///< Current index in the array of dictionaries provided by the server
    private currentPanelQuestion : PanelQuestion = null;
    private quiz_statistics : QuizStatistics;
    private xx = 8;

    constructor(qid : number) {
        this.quiz_statistics = new QuizStatistics(qid);

        $('#quiztab').append('<tr id="quiztabhead"></tr>');
        $('button#next_question').click(() => this.nextQuestion());
        $('button#finish').click(() => this.finishQuiz());
        $('button#finishNoStats').click(() => this.finishQuizNoStats());
    }

    /// Replaces the current quiz question with the next one, if any.
    public nextQuestion() {
    
        var timeouter = 600000;
        var timeinner = 600000;
        var timeoutinner = 28000;

        var fun1 = function() {
            
            window.clearTimeout(tOutOuter);
            window.clearTimeout(tOutInner);
            window.clearTimeout(tDialog);
            
            tOutInner = window.setTimeout(function() {
                heartbeatDialog.dialog('open');
                tDialog = window.setTimeout(function(){
                    heartbeatDialog.dialog('close');
                    $('#next_question').fadeOut();
                    window.clearTimeout(tOutOuter);
                    window.clearTimeout(tOutInner);
                    return;
                },timeoutinner);
            }, timeinner);

            var heartbeatDialog = $('<div></div>')
                .html(localize('done_practicing'))
                .dialog({
                    autoOpen: false,
                    title: localize('stop_practicing'),
                    resizable: true,
                    show: 'fade',
                    hide: 'explode',
                    height:140,
                    modal: true,
                    buttons:[{
                        text: localize('go_on'),
                        click: function() {
                            $(this).dialog('close');
                            window.clearTimeout(tOutOuter);
                            window.clearTimeout(tOutInner);
                            window.clearTimeout(tDialog);
                            tOutOuter = window.setTimeout(fun1, timeouter);
                            return;
                        } 
                    }]
                });
        }
    
        fun1();

        if (this.currentPanelQuestion!==null)
            // Update statistics
            this.quiz_statistics.questions.push(this.currentPanelQuestion.updateQuestionStat(1));
     
        if (++this.currentDictIx < dictionaries.sentenceSets.length) {
            $('#virtualkbid').appendTo('#virtualkbcontainer'); // Move the keyboard back to its initial position
            $('#textarea').empty();
            $('#quiztab').empty();
            $('#quiztab').append('<tr id="quiztabhead"></tr>');
     
            var currentDict : Dictionary = new Dictionary(dictionaries,this.currentDictIx,true);
     
            $('#quizdesc').html(quizdata.desc);
            $('#quizdesc').find('a').attr('target','_blank'); // Force all hyperlinks to open new browser tab
            if (supportsProgress)
                $('progress#progress').attr('value',this.currentDictIx+1).attr('max',dictionaries.sentenceSets.length);
            else
                $('div#progressbar').progressbar({value: this.currentDictIx+1, max: dictionaries.sentenceSets.length});
            $('#progresstext').html((this.currentDictIx+1)+'/'+dictionaries.sentenceSets.length);
            
            this.currentPanelQuestion = new PanelQuestion(quizdata, currentDict, false);
            
            if (this.currentDictIx+1 === dictionaries.sentenceSets.length)
                $('button#next_question').attr('disabled', 'disabled');

            if (quizdata.quizFeatures.useVirtualKeyboard &&
                // TODO: This should not be needed:
                (charset.keyboardName==='IL' || charset.keyboardName==='GR')) {
                VirtualKeyboard.setVisibleLayoutCodes([charset.keyboardName]);
                VirtualKeyboard.toggle('firstinput','virtualkbid');
            }
        }
        else
            alert('No more questions');
     
        util.resetCheckboxCounters();
        $('.grammarselector input:enabled:checked').trigger('change'); // Make sure grammar is displayed for relevant checkboxes
    }

    public finishQuiz() {
        if (quizdata.quizid == -1) // User not logged in
            window.location.replace(site_url + 'text/select_quiz'); // Go to quiz selection
        else {
            if (this.currentPanelQuestion===null)
                alert('System error: No current question panel');
            else
                this.quiz_statistics.questions.push(this.currentPanelQuestion.updateQuestionStat(1));
            
            // Send statistics to server
            $('.grammarselector').empty();
            $('#textcontainer').html('<p>' + localize('sending_statistics') + '</p>');
            
            $.post(
                site_url + 'statistics/update_stat',
                this.quiz_statistics
            )
                .done(() => window.location.replace(site_url + 'text/select_quiz')) // Go to quiz selection
                .fail((jqXHR: JQueryXHR, textStatus: string, errorThrow: string) => {
                    $('#textcontainer')
                        .removeClass('textcontainer-background')
                        .addClass('alert alert-danger')
                        .html('<h1>' + localize('error_response') + '</h1><p>{0}</p>'.format(errorThrow));
                });
        }
    }
    
    finishQuizNoStats() {
    	if (quizdata.quizid == -1) // User not logged in
            window.location.replace(site_url + 'text/select_quiz'); // Go to quiz selection
        else {
            if (this.currentPanelQuestion===null)
                alert('System error: No current question panel');
            else
                this.quiz_statistics.questions.push(this.currentPanelQuestion.updateQuestionStat(0));
            
            // Send statistics to server
            $('.grammarselector').empty();
            $('#textcontainer').html('<p>' + localize('sending_statistics') + '</p>');
            
            $.post(
                site_url + 'statistics/update_stat',
                this.quiz_statistics
            )
                .done(() => window.location.replace(site_url + 'text/select_quiz')) // Go to quiz selection
                .fail((jqXHR: JQueryXHR, textStatus: string, errorThrow: string) => {
                    $('#textcontainer')
                        .removeClass('textcontainer-background')
                        .addClass('alert alert-danger')
                        .html('<h1>' + localize('error_response') + '</h1><p>{0}</p>'.format(errorThrow));
                });
        }
    }
}
