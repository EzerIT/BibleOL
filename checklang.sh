#!/bin/bash
# Lists differences between strings found in English and Danish

cd myapp/language/english
grep '$lang' *.php | sed -e 's/\s*=.*//' | sed -e "s/.*:\$lang\['//" -e "s/'\]//" | sort | uniq > /tmp/en.txt

cd ../da
allda=$(ls *php | sed -e 's/db_lang\.php//' -e 's/email_lang\.php//')

grep '$lang' $allda | sed -e 's/\s*=.*//' | sed -e "s/.*:\$lang\['//" -e "s/'\]//" | sort | uniq > /tmp/da.txt

diff /tmp/en.txt /tmp/da.txt
