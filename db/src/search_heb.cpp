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
    map<tuple<string,string,string>,string> m; // (lex,vs,language) => ref

    bool bResult{false};
    EmdrosEnv EE{kOKConsole,
                 kCSUTF8,
                 "localhost",
                 "",
                 "",
                 "../ETCBC4",
                 kSQLite3};


    if (!EE.executeString("SELECT ALL OBJECTS WHERE [verse get label [word get lex,vs,language]]",
                          bResult, false, true))
        return 1;


    if (!EE.isSheaf()) {
        cerr << "ERROR: Result is not sheaf\n";
        return 1;
    }

    for (StrawOk s_outer : SheafOk{EE.getSheaf()}) {
        for (const MatchedObject mo_outer : s_outer) {

            auto verse_label = mo_outer.getFeatureAsString(0);

            for (StrawOk s_inner : SheafOk{mo_outer.getSheaf()}) {
                for (const MatchedObject mo_inner : s_inner) {

                    string lex      {mo_inner.getFeatureAsString(0)};
                    string vs       {mo_inner.getFeatureAsString(1)};
                    string language {mo_inner.getFeatureAsString(2)};
            
                    auto tup = make_tuple(lex,vs,language);
                    if (m.count(tup)==0)
                        m[tup] = verse_label;
                }
            }
        }
    }

    for (auto entry : m) {
        cout << get<0>(entry.first) << "\t"
             << get<1>(entry.first) << "\t"
             << get<2>(entry.first) << "\t"
             << entry.second << "\n";
    }
}
