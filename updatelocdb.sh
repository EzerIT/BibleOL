#!/bin/bash

# Updates localization information in the database with the information found in files.
# Must be run in Bible OL installation directory.

echo -n "You are about to change the contents of the database. Are you sure? (yes/no) "
read a
[ x$a != xyes ] && exit 1


# Interface localization

echo "# Updating interface localization"

iflangs=$(ls myapp/language/langsrc)

for lcode in $iflangs; do
    echo "#     Language code: $lcode"
    php index.php translate if_php2db $lcode myapp/language/langsrc/$lcode
done


# Grammar localization

echo
echo  "# Updating grammar localization"

php index.php translate gram_prop2db


# Lexicon localization

echo
echo "# Updating lexicon localization"

# Expects all csv files to be present
for file in lexicons/*.csv; do
    srclang=$(echo $file | sed -e 's|lexicons/\([^_]*\)_\(.*\)\.csv|\1|')
    dstlang=$(echo $file | sed -e 's|lexicons/\([^_]*\)_\(.*\)\.csv|\2|')

    echo "#     Updating $srclang -> $dstlang"
    php index.php translate import_lex $srclang $dstlang $file
done
