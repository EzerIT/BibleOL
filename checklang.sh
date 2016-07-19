#!/bin/bash
# Lists differences between strings found in English and $lan

lan=pt

cd myapp/language/english
grep '$lang' *.php | sed -e 's/\s*=.*//' | sed -e "s/.*:\$lang\['//" -e "s/'\]//" | sort | uniq > /tmp/en.txt

cd ../$lan
alllan=$(ls *php | sed -e 's/db_lang\.php//' -e 's/email_lang\.php//')

grep '$lang' $alllan | sed -e 's/\s*=.*//' | sed -e "s/.*:\$lang\['//" -e "s/'\]//" | sort | uniq > /tmp/$lan.txt

diff /tmp/en.txt /tmp/$lan.txt
