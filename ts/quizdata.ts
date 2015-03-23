// -*- js -*-

interface ExtendedQuizFeatures {
    showFeatures : string[];
    requestFeatures : {name : string; usedropdown : boolean; }[];
    dontShowFeatures : string[];
    objectType : string;
    dontShow : boolean;
    useVirtualKeyboard : boolean;
}


/**
 * Contains all data and methods required to uniquely identify and manipulate 
 * the candidate sentences in a quiz.
 */
interface QuizData {
    quizid : number;
    quizFeatures : ExtendedQuizFeatures;
    desc : string;
    monad2Id : number[];
    id2FeatVal : string[][];
}

declare var quizdata : QuizData;

function mayShowFeature(oType : string, feat : string, sgiObj : SentenceGrammarItem) : boolean {
    var inQuiz : boolean = $('#quiztab').length>0;

    if (!inQuiz)
        return true;

    if (sgiObj.mytype==='GrammarMetaFeature') {
        for (var i in sgiObj.items) {
            if (isNaN(+i)) continue; // Not numeric
            if (!mayShowFeature(oType, sgiObj.items[+i].name, sgiObj.items[+i]))
                return false;
        }
        return true;
    }

    var qf : ExtendedQuizFeatures = quizdata.quizFeatures;

    if (oType!==qf.objectType)
        return true;

    for (var ix=0, len=qf.requestFeatures.length; ix<len; ++ix)
        if (qf.requestFeatures[ix].name===feat)
            return false;

    return qf.dontShowFeatures.indexOf(feat)===-1;
}

