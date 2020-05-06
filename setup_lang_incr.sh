#!/bin/bash

echo Updating interface languages

php index.php translate if_php2db -i comment myapp/language/langsrc/comment

for i in $(ls myapp/language/langsrc); do
    if [ $i != comment ]; then
        echo Importing interface language $i
        php index.php translate if_php2db -i $i myapp/language/langsrc/$i
    fi
done
