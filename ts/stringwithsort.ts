// -*- js -*-
/* 2013 by Ezer IT Consulting. All rights reserved. E-mail: claus@ezer.dk */

/// @file
/// @brief Contains the StringWithSort class.


/// Represents a string with a sort index. Strings with sort indexes are specified in the properties
/// files in as, for example, "#8 Foobar", where 8 is the sort index and "Foobar" is the string. The
/// sort index will be used when ordering objects. Objects of this class can also be constructed from
/// strings without a sort index, in which case comparison reverts to lexical case-free comparison.
/// @see #StringWithSort::compare
class StringWithSort {
    private sort : number;      ///< The sort index. -1 if no sort index is specified.
    private str : string;       ///< The string with the sort index removed.
    private internal : string;  ///< The internal (that is, language independent) value.

    /// Constructs a StringWithSort object with an internal name.
    /// @param s String with an optional sort index. This string may take the form "#X SSS" where X
    /// is a non-negative integer and SSS is the string proper; in this case X will be used as the
    /// sort index. Alternatively this parameter may be specified as a string without a sort index,
    /// in which case the sort index will be set to -1 and comparison will be lexical.
    /// @param internal The internal (that is, languague independent) name for this value.
    constructor(s : string, internal : string=null) {
        if (s.length>0 && s.charAt(0)==='#') {
            var sp : number = s.indexOf(' ');
            this.sort = +s.substring(1,sp);
            this.str = s.substring(sp+1);
        }
        else {
            this.sort = -1;
            this.str = s;
        }
        this.internal = internal;
    }

    /// Extracts the proper string part of a string with an optional sort index.
    /// @param s String with an optional sort index.
    /// @return The proper string part.
    static stripSortIndex(s : string) : string {
        return (s.length>0 && s.charAt(0)==='#')
            ? s.substring(s.indexOf(' ')+1)
            : s;
    }

    /// Gets the internal value.
    /// @return The internal (that is, language independent) value. This may be null.
    public getInternal() : string {
        return this.internal;
    }

    /// Gets the string proper.
    /// @return The string with the sort index removed.
    public getString() : string {
        return this.str;
    }

    /// Compares two StringWithSort objects. If the two objects have different sort indexes, the
    /// sort index will be used for ordering. If the two objects have the same sort index or if one
    /// of the sort indexes is -1, lexcial case insensitive ordering will be used.
    /// @param sws1 The first object to be compared.
    /// @param sws2 The second object to be compared.
    /// @return -1, 0, or 1, depending on whether sws1 is less than, equal to, or greater than
    /// sws2.
    static compare(sws1 : StringWithSort, sws2 : StringWithSort) : number {
        if (sws1.sort==-1 || sws2.sort==-1 || sws1.sort==sws2.sort) {
            // Use lexical comparison
            var s1 : string = sws1.str.toLowerCase();
            var s2 : string = sws2.str.toLowerCase();
            return s1<s2 ? -1 : s1>s2 ? 1 : 0;
        }
        else
            return sws1.sort<sws2.sort ? -1 : 1; // Note: No zero here because equality is handled above
    }
}
