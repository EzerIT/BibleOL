// -*- js -*-
/// <reference path="jquery/jquery.d.ts" />

/* 2013 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk */


interface FeatureSetting {
    hideWord? : boolean;
    foreignText? : boolean;
    transliteratedText? : boolean;
    ignoreSelect? : boolean;
    ignoreShowRequest? : boolean;  // Old value. Means: ignoreShow==true && ignoreRequest==true
    ignoreShow? : boolean;
    ignoreRequest? : boolean;
    isDefault? : boolean;
    matchregexp? : string;
    isRange? : boolean;
    hideValues? : string[];
    otherValues? : string[];
    alternateshowrequestDb? : string; 
    alternateshowrequestSql? : string; 
}

interface ObjectSetting {
    mayselect? : boolean;
    additionalfeatures? : string;
    featuresetting? : { [featurename : string] : FeatureSetting; };
}

interface UHTriple {
    type : string;
    feat : string;
}

interface Configuration {
    version : number;
    databaseName : string;
    propertiesName : string;
    charSet : string;
    databaseVersion : string;
    granuarity : string;
    surfaceFeature : string;
    objHasSurface : string;
    suffixFeature : string;
    useSofPasuq : boolean;
    objectSettings : { [objectname : string ] : ObjectSetting; };
    universeHierarchy : UHTriple[];
    picDb : string;
    sentencegrammar : SentenceGrammar[];
    maxLevels? : number; // Added programmatically
}

interface FeatureMap {
    [featName : string] : string;
}


function getObjectSetting(otype : string) : ObjectSetting {
    return configuration.objectSettings[otype];
}

function getFeatureSetting(otype : string, feature : string) : FeatureSetting {
    // Handle the pseudo-feature
    if (feature==='visual') {
        otype = configuration.objHasSurface;
        feature = configuration.surfaceFeature;
    }

    var io = feature.indexOf('_TYPE_'); // Separates feature from format
    if (io!=-1)
        // This is a feature with a special format (which is not used here)
        return getObjectSetting(otype).featuresetting[feature.substr(0,io)];
    else
        return getObjectSetting(otype).featuresetting[feature];
}


interface TypeInfo {
    objTypes : string[];
    obj2feat : {[objType : string] : FeatureMap;};
    enumTypes : string[];
    enum2values : { [enumname : string] : string[]; };
}



declare var configuration : Configuration;
declare var typeinfo : TypeInfo;
