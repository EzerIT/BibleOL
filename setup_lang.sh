#!/bin/bash

echo Setting up interface languages

php index.php translate if_php2db comment myapp/language/langsrc/comment

for i in $(ls myapp/language/langsrc); do
    if [ $i != comment ]; then
        echo Importing interface language $i
        php index.php translate if_php2db $i myapp/language/langsrc/$i
    fi
done

echo Importing localized grammar terms
php index.php translate gram_prop2db db/property_files

echo Importing localized lexicons
for i in lexicons/*.csv; do
    j=${i/lexicons\//}
    s=${j/_*.csv/}
    j2=${j/*_/}
    d=${j2/.csv/}
    echo Source language: $s. Destination language: $d.
    php index.php translate import_lex $s $d $i
done
