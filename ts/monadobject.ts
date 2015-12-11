// -*- js -*-
/* 2013 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk */

interface MonadPair {
    low : number;
    high : number;
}

interface MonadSet {
    segments : MonadPair[];
}

function getFirst(ms : MonadSet) : number {
    var first : number = 1000000000;
    for (var pci in ms.segments) {
        if (isNaN(+pci)) continue; // Not numeric

        var pc : MonadPair = ms.segments[+pci];
    	if (pc.low<first)
    	    first = pc.low;
    }
    return first;
}

function getSingleInteger(ms : MonadSet) : number{
    if (ms.segments.length===1) {
        var p : MonadPair = ms.segments[0];
        if (p.low === p.high)
            return p.low;
    }

    throw 'MonadSet.ObjNotSingleMonad';
}

function getMonadArray(ms : MonadSet) : number[] {
    var res : number[] = [];

    for (var i in ms.segments) {
        if (isNaN(+i)) continue; // Not numeric

        var mp : MonadPair = ms.segments[+i];
        for (var j=mp.low; j<=mp.high; ++j)
            res.push(j);
    }
    return res;
}


interface MatchedObject {
    id_d : number;
    name : string;
    monadset : MonadSet;
    features : {[key : string] : string;};
    sheaf : any;  // Not used, but set by server
}

interface MonadObject {
    mo : MatchedObject;
    children_idds : number[];
    parent? : MonadObject;
    displayers? : DisplayMonadObject[];
}

interface SingleMonadObject extends MonadObject {
    text : string;  // Not used, but set by server
    suffix : string;  // Not used, but set by server
    bcv : string[];
    sameAsNext : boolean[];
    sameAsPrev : boolean[];
    pics : number[];
    urls : string[][];
}

interface MultipleMonadObject extends MonadObject {
    subobjects : MatchedObject[][];
}
