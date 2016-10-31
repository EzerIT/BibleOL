#include <emdros/emdfdb.h>
#include <emdros/emdros_environment.h>
#include <emdros/mql_sheaf.h>
#include <emdros/emdf_value.h>
#include <iostream>
#include <fstream>
#include <string>
#include <tuple>
//#include <set>
#include <map>
//#include <vector>

#include "emdros_iterators.hpp"

// two entries are the same if strongs,strongs_unreliable, and lemma are the same

using namespace std;

int main(int argc, char **argv)
{
    map<tuple<long,bool,string>,tuple<string,string>> m; // (strongs,strongs_unreliable,lemma) => (raw_lemma,ref)
    map<tuple<long,bool,string>,int> tally; // (strongs,strongs_unreliable,lemma) => tally
    bool bResult{false};
    EmdrosEnv EE{kOKConsole,
                 kCSUTF8,
                 "localhost",
                 "",
                 "",
                 "../nestle1904",
                 kSQLite3};


    if (!EE.executeString("SELECT ALL OBJECTS WHERE [word get ref, strongs, strongs_unreliable, lemma, raw_lemma]",
                          bResult, false, true))
        return 1;


    if (!EE.isSheaf()) {
        cerr << "ERROR: Result is not sheaf\n";
        return 1;
    }

    for (StrawOk straw : SheafOk{EE.getSheaf()}) {
        for (const MatchedObject mo : straw) {

            string ref                {mo.getFeatureAsString(0)};
            long   strongs            {mo.getFeatureAsLong(1)};
            long   strongs_unreliable {mo.getFeatureAsLong(2)};
            string lemma              {mo.getFeatureAsString(3)};
            string raw_lemma          {mo.getFeatureAsString(4)};
            
            auto tup1 = make_tuple(strongs,strongs_unreliable==1,lemma);
            auto tup2 = make_tuple(raw_lemma,ref);
            if (m.count(tup1)==0) {
                m[tup1] = tup2;
                tally[tup1] = 1;
            }
            else
                ++tally[tup1];
                
        }
    }

    for (auto entry : m) {
        cout << get<0>(entry.first) << "\t"
             << get<1>(entry.first) << "\t"
             << get<2>(entry.first) << "\t"
             << get<0>(entry.second) << "\t"
             << get<1>(entry.second) << "\t"
             << tally[entry.first] << "\n";
    }
}
//id,strongs,strongs_unreliable,lemma,raw_lemma (sortorder),tally,ref

// Only word with same strongs, strongs_unreliable, and raw_lemma, but different lemma is
// strongs=707
// strongs_unreliable=false
// raw_lemma=αριμαθαια
// lemma=Ἁριμαθαία or Ἀριμαθαία
