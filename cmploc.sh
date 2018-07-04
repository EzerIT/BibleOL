#!/bin/bash

# Compare localization information in the database with the information found in files.
# Must be run in Bible OL installation directory.

TMP=/tmp/loc$$

if [ -e $TMP -a ! -d $TMP ]; then
    echo $TMP exists but is not a directory
    exit 1
fi

[ ! -e $TMP ] && mkdir $TMP


# Interface localization

echo "# Comparing interface localization"

mkdir $TMP/interface

iflangs=$(ls myapp/language/langsrc)

for lcode in $iflangs; do
    php index.php translate if_db2php $lcode $TMP/interface
done

for lcode in $iflangs; do
    echo -n "#     Comparing $lcode"
    if diff -r myapp/language/langsrc/$lcode $TMP/interface/$lcode > $TMP/output; then
        echo " --  No difference"
    else
        echo
        cat $TMP/output
    fi
    rm $TMP/output
done


# Grammar localization

echo
echo -n "# Comparing grammar localization"

mkdir $TMP/grammar

php index.php translate gram_db2prop $TMP/grammar

if diff -rb db/property_files $TMP/grammar > $TMP/output; then
    echo " --  No difference"
else
    echo
    cat $TMP/output
fi
rm $TMP/output


# Lexicon localization

echo
echo "# Comparing lexicon localization"

mkdir $TMP/lexicons

# Expects all csv files to be present
for file in lexicons/*.csv; do
    srclang=$(echo $file | sed -e 's|lexicons/\([^_]*\)_\(.*\)\.csv|\1|')
    dstlang=$(echo $file | sed -e 's|lexicons/\([^_]*\)_\(.*\)\.csv|\2|')

    php index.php translate download_lex $srclang $dstlang > $TMP/lexicons/${srclang}_${dstlang}.csv

    echo -n "#     Comparing $srclang -> $dstlang"

    if diff $file $TMP/lexicons/${srclang}_${dstlang}.csv > $TMP/output; then
        echo " --  No difference"
    else
        echo
        cat $TMP/output
    fi
    rm $TMP/output
done

echo
echo "# Files were stored in directory $TMP"

