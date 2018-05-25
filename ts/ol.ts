// -*- js -*-
// Copyright © 2018 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk

// Main functions for handling text display and quizzes.

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

// If you want to compile with --noImplicitAny, you will now (as of TypeScript v1) get this error:
//      error TS7017: Index signature of object type implicitly has an 'any' type.
// if you are indexing arrays with strings.
// According to https://typescript.codeplex.com/discussions/535628 you can fix this by adding:
//      interface Object {
//          [idx: string]: any;
//      }
// to the beginning of your code. Unfortunately, this breaks a definition of data() in the
// JQuery interface.


//****************************************************************************************************
// Global variables

declare var useTooltip : boolean; // Does the user use tooltips rather than grammardisplay?
let supportsProgress   : boolean; // Does the browser support <progress>?
let charset            : Charset; // Current character set
let inQuiz             : boolean; // Are we displaying a quiz?
let quiz               : Quiz;    // Current quiz
let accordion_width    : number;  // Width of the grammar selector accordions
let indentation_width  : number;  // Width of indentation of ETCBC4 clause atoms


/// Main code executed when the page has been loaded.
$(function() {
    inQuiz = $('#quiztab').length>0;

    // Does the browser support <progress>?
    // (Use two statements because jquery.d.ts does not recognize .max)
    let x : any = document.createElement('progress');
    supportsProgress = x.max != undefined; // Thanks to http://lab.festiz.com/progressbartest/index.htm

    configuration.maxLevels = configuration.sentencegrammar.length+1; // Include patriarch level

    
    // Set up CSS classes for text.
    charset = new Charset(configuration.charSet);
    $('#textarea').addClass(charset.isRtl ? 'rtl' : 'ltr');


    for (let i in configuration.sentencegrammar) {
        if (isNaN(+i)) continue; // Not numeric
        addMethodsSgi(configuration.sentencegrammar[+i], configuration.sentencegrammar[+i].objType);
    }


    // Create HTML for checkboxes that select what grammar to display
    let generateCheckboxes = new GrammarSelectionBox();
    $('#gramselect').append(generateCheckboxes.generateHtml());
    generateCheckboxes.setHandlers();
    GrammarSelectionBox.clearBoxes(false);

    accordion_width = GrammarSelectionBox.buildGrammarAccordion();

    if (inQuiz) {
        if (supportsProgress)
            $('div#progressbar').hide();
        else
            $('progress#progress').hide();

        quiz = new Quiz(quizdata.quizid);
        quiz.nextQuestion();
    }
    else {
        // Display text
        $('#cleargrammar').on('click',() => { GrammarSelectionBox.clearBoxes(true); });

        let currentDict : Dictionary = new Dictionary(dictionaries,0,false);
        currentDict.generateSentenceHtml(null);
        $('.grammarselector input:enabled:checked').trigger('change'); // Make sure grammar is displayed for relevant checkboxe
    }
});


