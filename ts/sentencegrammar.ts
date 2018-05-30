// -*- js -*-

// Copyright © 2018 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk */

// The code here handles walking through the "sentencegrammar" part of the configuration object.
//
// Before using the classes in this file, the function addMethodsSgi() must called to enhace the
// configuration object with polymorhic functions, turning the contents of "sentencegrammar" into
// objects of various types.
//
// For example, addMethodsSgi() will turn this part of the configuration object:
//     {
//         "mytype": "GrammarFeature",
//         "name": "g_lex_utf8"
//     }
// into an object of class GrammarFeature with appropriate polymorphic functions added (such as
// walkFeatureNames, walkFeatureValues etc.)


//****************************************************************************************************
// addMethods function
//
// Takes method from a class and adds them to an object. Thereafter calls the method
// pseudoConstructor() if it exists.
//
// Parameters:
//     obj: The object to which methods should be added.
//     classname: The class from which methods should be taken.
//     param: The argument to pseudoConstructor().
//
function addMethods(obj : any, classname : any, param : string) : void {
    // Copy all methods except constructor
    for (let f in classname.prototype) {
        if (f==='constructor')
            continue;
        obj[f] = classname.prototype[f];
    }
    obj.pseudoConstructor && obj.pseudoConstructor(param); // Call pseudoConstructor if it exists
}


//****************************************************************************************************
// addMethodsSgi function
//
// Recursively enhances a part of the configuration object to be a polymorphic object of the class
// specified by the mytype field of the configuration object.
//
// Parameters:
//     sgi: The part of the configuration object that is to be enhanced.
//     param: The argument to pseudoConstructor() for the enhanced classes.
//
function addMethodsSgi(sgi : SentenceGrammarItem, param : string) : void {
    addMethods(sgi, eval(sgi.mytype), param); // sgi.mytype is the name of the subclass

    // Do the same with all members of the items array
    if (sgi.items) {
        for (let i in sgi.items) {
            if (isNaN(+i)) continue; // Not numeric
            addMethodsSgi(sgi.items[+i], param);
        }
    }
}


// The WHAT enum is used to identify various stages when walking through a configuration object.
enum WHAT {
    feature,
    metafeature,
    groupstart,
    groupend,
}


//****************************************************************************************************
// SentenceGrammarItem interface
//
// This interface describes the subobjects of the "sentencegrammar" part of the configuration object.
// It also defines methods that can optionally be added by calling addMethodsSgi().
//
interface SentenceGrammarItem {
    mytype : string;
    name? : string;
    objType? : string;
    items? : SentenceGrammarItem[];


    //------------------------------------------------------------------------------------------
    // walkFeatureNames method
    //
    // Walks through all the features in the SentenceGrammarItem object, calling a callback function
    // with the name of each feature.
    //
    // Parameters:
    //     objType: The type of the grammar object (word, phrase, etc.)
    //     callback: The function to call for each grammar object.
    //
    // Parameters for the callback function:
    //    whattype: Identification of the current point in the walkthrough.
    //    objType: The type of the current grammar object.
    //    origObjType: The original type of the current grammar object. (This can be different from
    //                 objType when, for example, a feature under "clause" has the name "clause_atom:tab".)
    //    featName: The name of the feature.
    //    featNameLoc: Localized feature name.
    //    sgiObj: The current SentenceGrammarItem object (always 'this').
    //
    walkFeatureNames?(objType  : string,
                      callback : (whattype    : WHAT,
                                  objType     : string,
                                  origObjType : string,
                                  featName    : string,
                                  featNameLoc : string,
                                  sgiObj      : SentenceGrammarItem
                                 ) => void
                     ) : void;
    
    //------------------------------------------------------------------------------------------
    // walkFeatureValues method
    //
    // Walks through all the features in the SentenceGrammarItem object, calling a callback function
    // with the localized value of each feature.
    //
    // Parameters:
    //     monob: The MonadObject containing the values we are retrieving.
    //     mix: Monad segment index (for split objects, such as a clause consisting of two parts).
    //     objType: The type of the grammar object (word, phrase, etc.).
    //     abbrev: Use abbreviated localized string, if it exists.
    //     callback: The function to call for each grammar object.
    //
    // Parameters for the callback function:
    //    whattype: Identification of the current point in the walkthrough.
    //    objType: The type of the current grammar object.
    //    origObjType: The original type of the current grammar object. (This can be different from
    //                 objType when, for example, a feature under "clause" has the name "clause_atom:tab".)
    //    featName: The name of the feature.
    //    featValLoc: Localized feature value.
    //    sgiObj: The current SentenceGrammarItem object (always 'this').
    //
    walkFeatureValues?(monob    : MonadObject,
                       mix      : number,
                       objType  : string,
                       abbrev   : boolean,
                       callback : (whattype    : WHAT,
                                   objType     : string,
                                   origObjType : string,
                                   featName    : string,
                                   featValLoc  : string,
                                   sgiObj      : SentenceGrammarItem
                                  ) => void
                      ) : void;

    //------------------------------------------------------------------------------------------
    // containsFeature method
    //
    // Returns true if the SentenceGrammarItem or one of its members contains the specified feature.
    //
    // Parameters:
    //     f: The name of the feature to search for.
    //
    containsFeature(f : string) : boolean;
}


//****************************************************************************************************
// GrammarGroup class
//
// A GrammarGroup groups GrammarFeatures and GrammarMetaFeatures into logical units, such as
// "Features that describe the lexeme" or "Features that describe the morphology".
//
class GrammarGroup implements SentenceGrammarItem {
    public mytype : string; // The string 'GrammarGroup'
    public name : string;   // The name of the feature
    public items : SentenceGrammarItem[]; // Members of the GrammarGroup (either GrammarFeatures or GrammarMetaFeatures)

    //------------------------------------------------------------------------------------------
    // walkFeatureNames method
    //
    // See description under SentenceGrammarItem
    //
    public walkFeatureNames(objType  : string,
                            callback : (whattype    : WHAT,
                                        objType     : string,
                                        origObjType : string,
                                        featName    : string,
                                        featNameLoc : string,
                                        sgiObj      : SentenceGrammarItem
                                       ) => void
                           ) : void
    {
        callback(WHAT.groupstart, objType, objType, this.name, l10n.grammargroup[objType][this.name], this);

        for (let i in this.items) {
            if (isNaN(+i)) continue; // Not numeric
            this.items[+i].walkFeatureNames(objType, callback);
        }

        callback(WHAT.groupend, objType, objType, this.name, null, this);
    }

    //------------------------------------------------------------------------------------------
    // walkFeatureValues method
    //
    // See description under SentenceGrammarItem
    //
    public walkFeatureValues(monob    : MonadObject,
                             mix      : number,
                             objType  : string,
                             abbrev   : boolean,
                             callback : (whattype    : WHAT,
                                         objType     : string,
                                         origObjType : string,
                                         featName    : string,
                                         featValLoc  : string,
                                         sgiObj      : SentenceGrammarItem
                                        ) => void
                            ) : void
    {
        callback(WHAT.groupstart, objType, objType, this.name, null, this);

        for (let i in this.items) {
            if (isNaN(+i)) continue; // Not numeric
            this.items[+i].walkFeatureValues(monob, mix, objType, abbrev, callback);
        }

        callback(WHAT.groupend, objType, objType, this.name, null, this);
    }

    //------------------------------------------------------------------------------------------
    // containsFeature method
    //
    // See description under SentenceGrammarItem
    //
    public containsFeature(f : string) : boolean {
        for (let i in this.items) {
            if (isNaN(+i)) continue; // Not numeric
            if (this.items[+i].containsFeature(f))
                return true;
        }
        return false;
    }
}

//****************************************************************************************************
// GrammarSubFeature class
//
// A GrammarSubFeature is a member of a GrammarMetaFeature.
//
class GrammarSubFeature implements SentenceGrammarItem {
    public mytype : string; // The string 'GrammarSubFeature'
    public name : string;   // The name of the feature

    //------------------------------------------------------------------------------------------
    // getFeatValPart method
    //
    // Retrieve the localized feature value.
    //
    // Parameters:
    //     monob: The MonadObject containing the value we are retrieving.
    //     objType: The type of the grammar object (word, phrase, etc.)
    //
    public getFeatValPart(monob : MonadObject, objType : string) : string {
        return l10n.grammarsubfeature[objType][this.name][monob.mo.features[this.name]];
    }

    //------------------------------------------------------------------------------------------
    // containsFeature method
    //
    // See description under SentenceGrammarItem
    //
    public containsFeature(f : string) : boolean {
        return this.name===f;
    }
}


//****************************************************************************************************
// SentenceGrammar class
//
// A SentenceGrammar groups GrammarGroups, GrammarFeatures and GrammarMetaFeatures for a single
// Emdros object type, such as word or phrase.
//
//
class SentenceGrammar extends GrammarGroup {
    public mytype : string; // The string 'SentenceGrammar'
    public objType : string; // The Emdros object type described by this SentenceGrammar
    public items : SentenceGrammarItem[]; // Members of the SentenceGrammar (either GrammarGroup, GrammarFeatures, or GrammarMetaFeatures)

    //------------------------------------------------------------------------------------------
    // walkFeatureNames method
    //
    // See description under SentenceGrammarItem
    //
    public walkFeatureNames(objType  : string,
                            callback : (whattype    : WHAT,
                                        objType     : string,
                                        origObjType : string,
                                        featName    : string,
                                        featNameLoc : string,
                                        sgiObj      : SentenceGrammarItem
                                       ) => void
                           ) : void
    {
        for (let i in this.items) {
            if (isNaN(+i)) continue; // Not numeric
            this.items[+i].walkFeatureNames(objType, callback);
        }
    }

    //------------------------------------------------------------------------------------------
    // walkFeatureValues method
    //
    // See description under SentenceGrammarItem
    //
    public walkFeatureValues(monob    : MonadObject,
                             mix      : number,
                             objType  : string,
                             abbrev   : boolean,
                             callback : (whattype    : WHAT,
                                         objType     : string,
                                         origObjType : string,
                                         featName    : string,
                                         featValLoc  : string,
                                         sgiObj      : SentenceGrammarItem
                                        ) => void
                            ) : void
    {
        for (let i in this.items) {
            if (isNaN(+i)) continue; // Not numeric
            this.items[+i].walkFeatureValues(monob, mix, objType, abbrev, callback);
        }
    }

    //------------------------------------------------------------------------------------------
    // containsFeature method
    //
    // See description under SentenceGrammarItem
    //
    public containsFeature(f : string) : boolean {
        for (let i in this.items) {
            if (isNaN(+i)) continue; // Not numeric
            if (this.items[+i].containsFeature(f))
                return true;
        }
        return false;
    }
}

//****************************************************************************************************
// GrammarMetaFeature class
//
// A GrammarMetaFeature describes a combined value of a number of Emdros features (for example,
// person, gender, and number).
//
class GrammarMetaFeature implements SentenceGrammarItem {
    public mytype : string; // The string 'GrammarMetaFeature'
    public name : string;   // The name of the metafeature
    public items : GrammarSubFeature[]; // The features that make up the GrammarMetaFeature

    //------------------------------------------------------------------------------------------
    // walkFeatureNames method
    //
    // See description under SentenceGrammarItem
    //
    public walkFeatureNames(objType  : string,
                            callback : (whattype    : WHAT,
                                        objType     : string,
                                        origObjType : string,
                                        featName    : string,
                                        featNameLoc : string,
                                        sgiObj      : SentenceGrammarItem
                                       ) => void
                           ) : void
    {
        callback(WHAT.metafeature, objType, objType, this.name, l10n.grammarmetafeature[objType][this.name], this);
    }

    //------------------------------------------------------------------------------------------
    // walkFeatureValues method
    //
    // See description under SentenceGrammarItem
    //
    public walkFeatureValues(monob    : MonadObject,
                             mix      : number,
                             objType  : string,
                             abbrev   : boolean,
                             callback : (whattype    : WHAT,
                                         objType     : string,
                                         origObjType : string,
                                         featName    : string,
                                         featValLoc  : string,
                                         sgiObj      : SentenceGrammarItem
                                        ) => void
                            ) : void
    {
        let res : string = '';
        for (let i in this.items) {
            if (isNaN(+i)) continue; // Not numeric
            res += this.items[+i].getFeatValPart(monob, objType);
        }

        callback(WHAT.metafeature, objType, objType, this.name, res, this);
    }

    //------------------------------------------------------------------------------------------
    // containsFeature method
    //
    // See description under SentenceGrammarItem
    //
    public containsFeature(f : string) : boolean {
        for (let i in this.items) {
            if (isNaN(+i)) continue; // Not numeric
            if (this.items[+i].containsFeature(f))
                return true;
        }
        return false;
    }
}

//****************************************************************************************************
// GrammarFeature class
//
// A GrammarFeature describes a feature of an Emdros object.
//
class GrammarFeature implements SentenceGrammarItem {
    public mytype : string; // The string 'GrammarFeature'
    public name : string;   // The name of the Emdros feature

    // The feature can either belong to the Emdros object of the SentenceGrammar of which this
    // GrammarFeature is a member, or it can belong to a subobject of the Emdros object.
    // If it is a subobject, then isSubObj is set to true, realObjectType is set to the type of the
    // subobject, and realFeatureName is set to the name of the subobject feature.
    // Otherwise, isSubObj is set to false, realObjectType is set to the current object type, and
    // realFeatureName is set to this.name.
    private isSubObj : boolean; 
    private realObjectType;
    private realFeatureName;

    //------------------------------------------------------------------------------------------
    // pseudoConstructor method
    //
    // Sets information about whether this is a subobject feature (see above) or not.
    //
    public pseudoConstructor(objType : string) {
        let io : number = this.name.indexOf(':');
        if (io!=-1) {
            // This is a feature of a sub-object
            this.isSubObj = true;
            this.realObjectType = this.name.substr(0,io);
            this.realFeatureName = this.name.substr(io+1);
        }
        else {
            this.isSubObj = false;
            this.realObjectType = objType;
            this.realFeatureName = this.name;
        }
    }

    //------------------------------------------------------------------------------------------
    // walkFeatureNames method
    //
    // See description under SentenceGrammarItem
    //
    public walkFeatureNames(objType  : string,
                            callback : (whattype    : WHAT,
                                        objType     : string,
                                        origObjType : string,
                                        featName    : string,
                                        featNameLoc : string,
                                        sgiObj      : SentenceGrammarItem
                                       ) => void
                           ) : void
    {
        // Normally localized feature names are found in l10n.emdrosobject, but occasionally special
        // translations exists that are to be used in the grammal selection box. These special
        // translations are found in l10n.grammarfeature.
        let locname : string = 
            l10n.grammarfeature && l10n.grammarfeature[this.realObjectType] && l10n.grammarfeature[this.realObjectType][this.realFeatureName] 
            ? l10n.grammarfeature[this.realObjectType][this.realFeatureName]
            : l10n.emdrosobject[this.realObjectType][this.realFeatureName];

        callback(WHAT.feature, this.realObjectType, objType, this.realFeatureName, locname, this);
    }

    //------------------------------------------------------------------------------------------
    // icon2class method
    //
    // Converts the name of an icon to the required HTML element classes.
    //
    // Parameter:
    //     icon: The name of the icon.
    // Returns:
    //     The HTML element class string.
    //
    private icon2class(icon : string) : string {
        if (icon.substr(0,10)==='glyphicon-')
            return 'glyphicon ' + icon;
        if (icon.substr(0,8)==='bolicon-')
            return 'bolicon ' + icon;
        return '';
    }

    //------------------------------------------------------------------------------------------
    // walkFeatureValues method
    //
    // See description under SentenceGrammarItem
    //
    public walkFeatureValues(monob    : MonadObject,
                             mix      : number,
                             objType  : string,
                             abbrev   : boolean,
                             callback : (whattype    : WHAT,
                                         objType     : string,
                                         origObjType : string,
                                         featName    : string,
                                         featValLoc  : string,
                                         sgiObj      : SentenceGrammarItem
                                        ) => void
                            ) : void
    {
        let featType : string = typeinfo.obj2feat[this.realObjectType][this.realFeatureName];

        // Normally featType will contain the type of the feature. However, the feature name can
        // contain the string _TYPE_, in which case the an alternative type is used.
        // The variable realRealFeature name is set to the part of the feature name that comes before _TYPE_.

        let io : number = this.realFeatureName.indexOf('_TYPE_'); // Separates feature from format
        let realRealFeatureName : string = io==-1 ? this.realFeatureName : this.realFeatureName.substr(0,io);
        
        let res : string = this.isSubObj
            ? (monob as MultipleMonadObject).subobjects[mix][0].features[realRealFeatureName]
            : (monob.mo.features ? monob.mo.features[realRealFeatureName] : ''); // Empty for dummy objects

        switch (featType) {
        case 'string':
        case 'ascii':
            if (res==='')
                res = '-';
            break;

        case 'url':
            if (res.length==0)
                res = '-';
            else {
                // Assume res is an array, where each element is an array of two elements
                let res2 : string = '';
                for (let i=0; i<res.length; ++i)
                    res2 += `<a style="padding-right:1px;padding-left:1px;" href="${res[i]['url']}" target="_blank">`
                          + `<span class="${this.icon2class(res[i]['icon'])}" aria-hidden="true"></span></a>`;

                res = res2;
            }
            break;

        case 'integer':
            break;

        default:
            if (io==-1) {
                if (res!=='')
                    res = getFeatureValueFriendlyName(featType, res, abbrev, true);
            }
            else {
                res = getFeatureValueOtherFormat(this.realObjectType, this.realFeatureName, +res);
            }
            break;
        }

        callback(WHAT.feature, this.realObjectType, objType, this.realFeatureName, res, this);
    }

    //------------------------------------------------------------------------------------------
    // containsFeature method
    //
    // See description under SentenceGrammarItem
    //
    public containsFeature(f : string) : boolean {
        return this.name===f;
    }
}


//****************************************************************************************************
// getSentenceGrammarFor function
//
// Retrieves from the configuraion structure the SentenceGrammar object that describes a particular
// Emdros object type.
//
// Parameter:
//     oType: The name of the Emdros object.
// Returns:
//     The corresponding SentenceGrammar object.
//
function getSentenceGrammarFor(oType : string) : SentenceGrammar {
    for (let i=0; i<configuration.sentencegrammar.length; ++i)
        if (configuration.sentencegrammar[i].objType===oType)
            return configuration.sentencegrammar[i];

    return null;
}


