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
    getFeatVal?(monob : MonadObject, mix : number, objType : string, abbrev : boolean,
                callback : (whattype:number, objType:string, featName:string, featValLoc:string, sgiObj : SentenceGrammarItem) => void) : void;
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

    public getFeatVal(monob : MonadObject, mix : number, objType : string, abbrev : boolean,
                      callback : (whattype:number, objType:string, featName:string, featValLoc:string, sgiObj : SentenceGrammarItem) => void) : void
    {
        callback(WHAT.groupstart, objType, this.name, null, this);
        for (var i in this.items) {
            if (isNaN(+i)) continue; // Not numeric
            this.items[+i].getFeatVal(monob, mix, objType, abbrev, callback);
        }
        callback(WHAT.groupend, objType, this.name, null, this);
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

    public getFeatVal(monob : MonadObject, mix : number, objType : string, abbrev : boolean,
                      callback : (whattype:number, objType:string, featName:string, featValLoc:string, sgiObj : SentenceGrammarItem) => void) : void
    {
        for (var i in this.items) {
            if (isNaN(+i)) continue; // Not numeric
            this.items[+i].getFeatVal(monob, mix, objType, abbrev, callback);
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

    public getFeatVal(monob : MonadObject, mix : number, objType : string, abbrev : boolean,
                   callback : (whattype:number, objType:string, featName:string, featValLoc:string, sgiObj : SentenceGrammarItem) => void) : void
    {
        var res : string = '';
        for (var i in this.items) {
            if (isNaN(+i)) continue; // Not numeric
            res += this.items[+i].getFeatValPart(monob, objType);
        }

        callback(WHAT.metafeature, objType, this.name, res, this);
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
    private realObjectType;
    private realFeatureName;
    //public coloring : { [index : string] : number[]; } = {};
    private isSubObj : boolean; 

    public pseudoConstructor(objType : string) {
        var io = this.name.indexOf(':');
        if (io!=-1) {
            // This is a feature of a subobject
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

    public getFeatName(objType : string,
                       callback : (whattype:number, objType:string, featName:string, featNameLoc:string, sgiObj : SentenceGrammarItem) => void) : void
    {
        var locname : string = 
            l10n.grammarfeature && l10n.grammarfeature[this.realObjectType] && l10n.grammarfeature[this.realObjectType][this.realFeatureName] 
            ? l10n.grammarfeature[this.realObjectType][this.realFeatureName]
            : l10n.emdrosobject[this.realObjectType][this.realFeatureName];

        callback(WHAT.feature, this.realObjectType, this.realFeatureName, locname, this);
    }

    public getFeatVal(monob : MonadObject, mix : number, objType : string, abbrev : boolean,
                   callback : (whattype:number, objType:string, featName:string, featValLoc:string, sgiObj : SentenceGrammarItem) => void) : void
    {
        var featType : string = typeinfo.obj2feat[this.realObjectType][this.realFeatureName];
        var io = this.realFeatureName.indexOf('_TYPE_'); // Separates feature from format
        var res : string =
            io==-1  // Test if the feature name contains _TYPE_
                ? (this.isSubObj
                   ? (<MultipleMonadObject>monob).subobjects[mix][0].features[this.realFeatureName]
                   : (monob.mo.features ? monob.mo.features[this.realFeatureName] : '')) // Empty for dummy objects
                : (this.isSubObj
                    ? (<MultipleMonadObject>monob).subobjects[mix][0].features[this.realFeatureName.substr(0,io)]
                    : (monob.mo.features ? monob.mo.features[this.realFeatureName.substr(0,io)] : ''));


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
                var res2 : string = '';
                for (var i=0; i<res.length; ++i) // TODO NOW: Fix bolicon
                    res2 += '<a style="padding-right:1px;padding-left:1px;" href="{0}" target="_blank"><span class="glyphicon {1}" aria-hidden="true"></span></a>'
                    .format(res[i]['url'],res[i]['icon']);

                res = res2;
            }
            break;

        case 'integer':
            break;

        default:
            if (io==-1) {
                if (res!=='')
                    res = StringWithSort.stripSortIndex(getFeatureValueFriendlyName(featType, res, abbrev));
            }
            else {
                res = getFeatureValueOtherFormat(this.realObjectType, this.realFeatureName, +res);
            }
            break;
        }

        callback(WHAT.feature, this.realObjectType, this.realFeatureName, res, this);
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
function addMethods(obj : any, classname : any, objType : string) {
    // Copy all methods except constructor
    for (var f in classname.prototype) {
        if (f==='constructor')
            continue;
        obj[f] = classname.prototype[f];
    }
    obj.pseudoConstructor && obj.pseudoConstructor(objType); // Call pseudoConstructor if it exiss
}



// This function adds relevant methods to a data object of a specific SentenceGrammarItem subtype
function addMethodsSgi(sgi : SentenceGrammarItem, objType : string) {
    addMethods(sgi, eval(sgi.mytype), objType); // sgi.mytype is the name of the subclass, generated by the server

    // Do the same with all members of the items array
    if (sgi.items) {
        for (var i in sgi.items) {
            if (isNaN(+i)) continue; // Not numeric
            addMethodsSgi(sgi.items[+i], objType);
        }
    }
}
