// -*- js -*-
// Copyright © 2018 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk

// Intefaces and functions for Emdros objects

//****************************************************************************************************
// About the relationship between the MonadObject and DisplayMonadObject classes
//****************************************************************************************************
//
// Please see the description in the file dictionary.ts.
//
//****************************************************************************************************


//****************************************************************************************************
// MonadPair interface
//
// Represents a single range of monads
//
interface MonadPair {
    low  : number;  // Lowest monad
    high : number;  // Highest monad
}


//****************************************************************************************************
// MonadPair interface
//
// Represents a set of monads
//
interface MonadSet {
    segments : MonadPair[]; // Array of monad ranges
}


//****************************************************************************************************
// MatchedObject interface
//
// Represents a single monad object harvested from an MQL request.
//
interface MatchedObject {
    id_d     : number;                  // Emdros id_d of the object
    name     : string;                  // Name of object type
    monadset : MonadSet;                // Monads that make up the object
    features : {[key : string] : any;}; // Maps feature name => feature value
    sheaf    : any;                     // Not used, but set by server
}


//****************************************************************************************************
// MonadObject interface
//
// Augments a MatchedObject with information about its place in the Emdros object hierarchy
//
interface MonadObject {
    mo            : MatchedObject;        // The Emdros object
    children_idds : number[];             // The id_d values of the children of this object
    parent?       : MonadObject;          // The parent of this object
    displayers?   : DisplayMonadObject[]; // The DisplayMonadObjects that handle the displaying of this object
}

//****************************************************************************************************
// SingleMonadObject interface
//
// A subclass intended for Emdros objects at the lowest level (that is, words)
//
interface SingleMonadObject extends MonadObject {
    text       : string;     // Not used, but set by server
    suffix     : string;     // Not used, but set by server
    bcv        : string[];   // Book, chapter and verse of the Bible
    bcv_loc    : string;     // Localized version of bcv
    sameAsNext : boolean[];  // Is the book, chapter or verse of this word the same as that of the next word?
    sameAsPrev : boolean[];  // Is the book, chapter or verse of this word the same as that of the previous word?
    pics       : number[];   // Index of pictures in the resource database linked to the current verse (set only for first word of verse)
    urls       : string[][]; // URLs linked to the current verse (set only for first word of verse). Each entry consists of a URL and a type.
}

//****************************************************************************************************
// SingleMonadObject interface
//
// A subclass intended for Emdros objects above the lowest level (phrase, clause etc.)
//
interface MultipleMonadObject extends MonadObject {
    subobjects : MatchedObject[][]; // Subobjects (e.g., clause_atom as a subobject of clause) in cases where a sentencegrammar refers to such objects
}


//****************************************************************************************************
// getFirst function
//
// Retrieves the lowest monad in a MonadSet.
//
// Parameter:
//     ms: MonadSet to search.
// Returns:
//     The lowest monad in the monad set
//
function getFirst(ms : MonadSet) : number {
    let first : number = 1000000000;
    for (let pci in ms.segments) {
        if (isNaN(+pci)) continue; // Not numeric

        let pc : MonadPair = ms.segments[+pci];
    	if (pc.low<first)
    	    first = pc.low;
    }
    return first;
}



//****************************************************************************************************
// getSingleInteger function
//
// Retrieves the single monad from a MonadSet containing only one monad.
//
// Parameter:
//     ms: MonadSet to search.
// Returns:
//     The only monad in the monad set
//
function getSingleInteger(ms : MonadSet) : number {
    if (ms.segments.length===1) {
        let p : MonadPair = ms.segments[0];
        if (p.low === p.high)
            return p.low;
    }

    throw 'MonadSet.ObjNotSingleMonad';
}

//****************************************************************************************************
// containsMonad function
//
// Checks if a monad set contains a specific monad
//
// Parameter:
//     ms: MonadSet to search.
//     monad: Monad to look for.
// Returns:
//     True if the MonadSet contains the monad
//
function containsMonad(ms : MonadSet, monad : number) : boolean {
    for (let i in ms.segments) {
        if (isNaN(+i)) continue; // Not numeric

        let mp : MonadPair = ms.segments[+i];
        if (monad>=mp.low && monad<=mp.high)
            return true;
    }
    return false;
}



//****************************************************************************************************
// getMonadArray function
//
// Returns an array containing all the monads in a MonadSet.
//
// Parameter:
//     ms: MonadSet from which monads are taken.
// Returns:
//     An array containing all the monads of the MonadSet.
//
function getMonadArray(ms : MonadSet) : number[] {
    let res : number[] = [];

    for (let i in ms.segments) {
        if (isNaN(+i)) continue; // Not numeric

        let mp : MonadPair = ms.segments[+i];
        for (let j=mp.low; j<=mp.high; ++j)
            res.push(j);
    }
    return res;
}

