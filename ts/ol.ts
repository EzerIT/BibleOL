// -*- js -*-
// Copyright © 2018 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk

// Main functions for handling displaying of text and exercises

/// <reference path="util.ts" />
/// <reference path="configuration.ts" />
/// <reference path="sentencegrammar.ts" />
/// <reference path="grammarselectionbox.ts" />
/// <reference path="charset.ts" />
/// <reference path="monadobject.ts" />
/// <reference path="displaymonadobject.ts" />
/// <reference path="localization.ts" />
/// <reference path="localization_general.ts" />
/// <reference path="quizdata.ts" />
/// <reference path="dictionary.ts" />
/// <reference path="panelquestion.ts" />
/// <reference path="stringwithsort.ts" />
/// <reference path="quiz.ts" />
/// <reference path="resizer.ts" />


//****************************************************************************************************
// Global variables

declare let useTooltip : boolean; // Does the user use tooltips rather than grammardisplay?
let supportsProgress   : boolean; // Does the browser support <progress>?
let charset            : Charset; // Current character set
let inQuiz             : boolean; // Are we displaying a quiz?
let quiz               : Quiz;    // Current quiz
let accordion_width    : string;  // Width of the grammar selector accordions
let indentation_width  : number;  // Width of indentation of ETCBC4 clause atoms


//****************************************************************************************************
// Main code executed when the page has been loaded.

$(function() {
    inQuiz = $('#quizcontainer').length>0;

    // Does the browser support <progress>?
    // (Use two statements because jquery.d.ts does not recognize .max)
    let x : any = document.createElement('progress');
    supportsProgress = x.max != undefined; // Thanks to http://lab.festiz.com/progressbartest/index.htm

    configuration.maxLevels = configuration.sentencegrammar.length+1; // Include patriarch level

    
    // Set up CSS classes for text.
    charset = new Charset(configuration.charSet);
    $('#textarea').addClass(charset.isRtl ? 'rtl' : 'ltr');


    // Add polymorphic function to the contents of configuration.sentencegrammar
    for (let i in configuration.sentencegrammar) {
        if (isNaN(+i)) continue; // Not numeric
        addMethodsSgi(configuration.sentencegrammar[+i], configuration.sentencegrammar[+i].objType);
    }


    // Create HTML the grammar selection box
    let generateCheckboxes = new GrammarSelectionBox();
    $('#gramtabs').append(generateCheckboxes.generateHtml());
    generateCheckboxes.setHandlers();
    GrammarSelectionBox.clearBoxes(false);

    accordion_width = GrammarSelectionBox.buildGrammarAccordion();

    if (inQuiz) {
        $('#cleargrammar').on('click', () => { GrammarSelectionBox.clearBoxes(true); });

        if (supportsProgress)
            $('div#progressbar').hide();
        else
            $('progress#progress').hide();

        // Run the exercise
        quiz = new Quiz(quizdata.quizid, $('#exam_id').length>0);
        quiz.nextQuestion(true);
        
        $('#gramtabs .selectbutton input:enabled:checked').trigger('change'); // Make sure the relevant features are displayed
    }
    else {
        // Display text
        $('#cleargrammar').on('click',() => { GrammarSelectionBox.clearBoxes(true); });

        // Generate the text to display
        let currentDict : Dictionary = new Dictionary(dictionaries,0,null);
        currentDict.generateSentenceHtml(null);
        $('#gramtabs .selectbutton input:enabled:checked').trigger('change'); // Make sure the relevant features are displayed

        // Colorize rare words
        $('.textdisplay').each(function() {
            if (+$(this).siblings('.lexeme_occurrences').text()<+$('#color-limit').val())
                $(this).css('color','blue');
        });

        $('#color-limit').change(() => {
            $('.textdisplay').each(function() {
                if (+$(this).siblings('.lexeme_occurrences').text()<+$('#color-limit').val())
                    $(this).css('color','blue');
                else
                    $(this).css('color','black');

            });
        });

    }


});


