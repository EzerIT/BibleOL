// -*- js -*-

/* 2013 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk */

enum WHAT {
    feature,
    metafeature,
    groupstart,
    groupend,
}

interface SentenceGrammarItem {
    mytype : string;
    name? : string;
    objType? : string;
    items? : SentenceGrammarItem[];

    getFeatName?(objType : string,
                callback : (whattype:number, objType:string, featName:string, featNameLoc:string, sgiObj : SentenceGrammarItem) => void) : void;
    getFeatVal?(monob : MonadObject, objType : string, abbrev : boolean,
                callback : (whattype:number, objType:string, featName:string, featVal:string, featValLoc:string, sgiObj : SentenceGrammarItem) => void) : void;
    getFeatValPart?(monob : MonadObject, objType : string) : string;
    containsFeature?(f : string) : boolean;
}


class GrammarGroup implements SentenceGrammarItem {
    public mytype : string;
    public name : string;
    public items : SentenceGrammarItem[];

    public getFeatName(objType : string,
                       callback : (whattype:number, objType:string, featName:string, featNameLoc:string, sgiObj : SentenceGrammarItem) => void) : void
    {
        callback(WHAT.groupstart, objType, this.name, l10n.grammargroup[objType][this.name], this);
        for (var i in this.items) {
            if (isNaN(+i)) continue; // Not numeric
            this.items[+i].getFeatName(objType, callback);
        }
        callback(WHAT.groupend, objType, this.name, null, this);
    }

    public getFeatVal(monob : MonadObject, objType : string, abbrev : boolean,
                      callback : (whattype:number, objType:string, featName:string, featVal:string, featValLoc:string, sgiObj : SentenceGrammarItem) => void) : void
    {
        callback(WHAT.groupstart, objType, this.name, null, null, this);
        for (var i in this.items) {
            if (isNaN(+i)) continue; // Not numeric
            this.items[+i].getFeatVal(monob, objType, abbrev, callback);
        }
        callback(WHAT.groupend, objType, this.name, null, null, this);
    }

    /** Do the children of this object identify the specified feature?
     * @param f The name of the feature to look for.
     * @return True if the specified feature matches this object.
     */
    public containsFeature(f : string) : boolean {
        for (var i in this.items) {
            if (isNaN(+i)) continue; // Not numeric
            if (this.items[+i].containsFeature(f))
                return true;
        }
        return false;
    }
}

class GrammarSubFeature implements SentenceGrammarItem {
    public mytype : string;
    public name : string;

    public getFeatValPart(monob : MonadObject, objType : string) : string {
        return l10n.grammarsubfeature[objType][this.name][monob.mo.features[this.name]];
    }

    /** Does this object identify the specified feature?
     * @param f The name of the feature to look for.
     * @return True if the specified feature matches this object.
     */
    public containsFeature(f : string) : boolean {
        return this.name===f;
    }
}

class SentenceGrammar extends GrammarGroup {
    items : SentenceGrammarItem[];
    objType : string;

    public getFeatName(objType : string,
                       callback : (whattype:number, objType:string, featName:string, featNameLoc:string, sgiObj : SentenceGrammarItem) => void) : void
    {
        for (var i in this.items) {
            if (isNaN(+i)) continue; // Not numeric
            this.items[+i].getFeatName(objType, callback);
        }
    }

    public getFeatVal(monob : MonadObject, objType : string, abbrev : boolean,
                      callback : (whattype:number, objType:string, featName:string, featVal:string, featValLoc:string, sgiObj : SentenceGrammarItem) => void) : void
    {
        for (var i in this.items) {
            if (isNaN(+i)) continue; // Not numeric
            this.items[+i].getFeatVal(monob, objType, abbrev, callback);
        }
    }

    /** Do the children of this object identify the specified feature?
     * @param f The name of the feature to look for.
     * @return True if the specified feature matches this object.
     */
    public containsFeature(f : string) : boolean {
        for (var i in this.items) {
            if (isNaN(+i)) continue; // Not numeric
            if (this.items[+i].containsFeature(f))
                return true;
        }
        return false;
    }
}

class GrammarMetaFeature implements SentenceGrammarItem {
    public mytype : string;
    public name : string;
    public items : SentenceGrammarItem[];

    public getFeatName(objType : string,
                       callback : (whattype:number, objType:string, featName:string, featNameLoc:string, sgiObj : SentenceGrammarItem) => void) : void
    {
        callback(WHAT.metafeature, objType, this.name, l10n.grammarmetafeature[objType][this.name], this);
    }

    public getFeatVal(monob : MonadObject, objType : string, abbrev : boolean,
                   callback : (whattype:number, objType:string, featName:string, featVal:string, featValLoc:string, sgiObj : SentenceGrammarItem) => void) : void
    {
        var res : string = '';
        for (var i in this.items) {
            if (isNaN(+i)) continue; // Not numeric
            res += this.items[+i].getFeatValPart(monob, objType);
        }

        callback(WHAT.metafeature, objType, this.name, null, res, this);
    }

    /** Do the children of this object identify the specified feature?
     * @param f The name of the feature to look for.
     * @return True if the specified feature matches this object.
     */
    public containsFeature(f : string) : boolean {
        for (var i in this.items) {
            if (isNaN(+i)) continue; // Not numeric
            if (this.items[+i].containsFeature(f))
                return true;
        }
        return false;
    }
}


class GrammarFeature implements SentenceGrammarItem {
    public mytype : string;
    public name : string;
    public coloring : { [index : string] : number[]; } = {};

    public getFeatName(objType : string,
                       callback : (whattype:number, objType:string, featName:string, featNameLoc:string, sgiObj : SentenceGrammarItem) => void) : void
    {
        var locname : string = 
            l10n.grammarfeature && l10n.grammarfeature[objType] && l10n.grammarfeature[objType][this.name] 
            ? l10n.grammarfeature[objType][this.name]
            : l10n.emdrosobject[objType][this.name];

        callback(WHAT.feature, objType, this.name, locname, this);
    }

    public getFeatVal(monob : MonadObject, objType : string, abbrev : boolean,
                   callback : (whattype:number, objType:string, featName:string, featVal:string, featValLoc:string, sgiObj : SentenceGrammarItem) => void) : void
    {
        var featType : string = typeinfo.obj2feat[objType][this.name];
        var res1 : string = monob.mo.features ? monob.mo.features[this.name] : ''; // Empty for dummy objects
        var res : string = res1;

        switch (featType) {
        case 'string':
        case 'ascii':
            if (res==='')
                res = '-';
            break;

        case 'integer':
            break;

        default:
            if (res!=='')
                res = StringWithSort.stripSortIndex(getFeatureValueFriendlyName(featType, res, abbrev));
            break;
        }
        callback(WHAT.feature,objType,this.name,res1,res,this);
    }

    /** Does this object identify the specified feature?
     * @param f The name of the feature to look for.
     * @return True if the specified feature matches this object.
     */
    public containsFeature(f : string) : boolean {
        return this.name===f;
    }
}


function getSentenceGrammarFor(oType : string) : SentenceGrammar {
    for (var i=0; i<configuration.sentencegrammar.length; ++i)
        if (configuration.sentencegrammar[i].objType===oType)
            return configuration.sentencegrammar[i];

    return null;
}


// This function copies all fields from the source to the destination
function copyFields(dst : any, src : any) {
    for (var f in src)
        dst[f] = src[f];
}

// This function adds relevant methods to a data object of the specified class
function addMethods(obj : any, classname : any) {
    // Copy all methods except constructor
    for (var f in classname.prototype) {
        if (f==='constructor')
            continue;
        obj[f] = classname.prototype[f];
    }
}



// This function adds relevant methods to a data object of a specific SentenceGrammarItem subtype
function addMethodsSgi(sgi : SentenceGrammarItem) {
    addMethods(sgi, eval(sgi.mytype)); // sgi.mytype is the name of the subclass, generated by the server

    // Do the same with all members of the items array
    if (sgi.items) {
        for (var i in sgi.items) {
            if (isNaN(+i)) continue; // Not numeric
            addMethodsSgi(sgi.items[+i]);
        }
    }
}
