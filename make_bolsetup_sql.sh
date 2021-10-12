#!/bin/bash

#echo "INSERT INTO \`bol_font\` VALUES (1,0,1,'Ezra SIL Webfont, Times New Roman, Serif',19,0,0,14,0,0,14,0,0,14,0,0),(2,0,2,'Doulos SIL Webfont, Times New Roman, serif',16,0,0,14,0,0,14,0,0,14,0,0),(3,0,3,'Gentium Plus Webfont, Times New Roman, serif',16,0,0,14,0,0,14,0,0,14,0,0),(4,0,4,'Segoe UI, Arial, sans-serif',16,0,0,14,0,0,14,0,0,14,0,0),(30,1,1,'Ezra SIL Webfont',21,0,0,17,0,0,17,0,0,17,0,0),(31,1,2,'Doulos SIL Webfont',16,0,0,14,0,0,14,0,0,14,0,0),(32,1,3,'Galatia SIL Webfont',16,0,0,14,0,0,14,0,0,14,0,0),(42,25,1,'Ezra SIL Webfont',21,0,0,17,0,0,17,0,0,17,0,0),(43,25,2,'Times New Roman',15,0,0,14,0,0,14,0,0,14,0,0),(44,25,3,'Gentium Plus Webfont',16,0,0,14,0,0,14,0,0,14,0,0),(45,43,1,'Ezra SIL Webfont',19,0,0,14,0,0,14,0,0,14,0,0),(46,43,2,'Helvetica',16,0,0,14,0,0,14,0,0,14,0,0),(47,43,3,'Times New Roman',16,0,0,14,0,0,14,0,0,14,0,0);" | sed -e 's/^\(INSERT INTO `bol_font` VALUES (1,.*),(2,.*),(3,.*),(4,.*)\).*;/\1;/'  | sed -e 's/^\(INSERT INTO `bol_font` VALUES (1[^)]*)\).*/\1;/'
#exit 1


database=pl
prefix=pl_

prune() {
    sed -e "s/$prefix/bol_/g" | sed -e 's/AUTO_INCREMENT=[0-9]* //' | grep -v '^--' | grep -v '^/\*'
}
    
dump_struct() {
    echo Dumping $prefix$1 > /dev/tty
    mysqldump -d -x -C -Q -e --create-options -E $database $prefix$1 | prune
}

dump_struct_and_data() {
    echo Dumping $prefix$1 > /dev/tty
    mysqldump -x -C -Q -e --create-options -E $database $prefix$1 | prune
}

dump_font() {
    echo Dumping $prefix$1 > /dev/tty
    mysqldump -x -C -Q -e --create-options -E $database $prefix$1 | prune | sed -e 's/^\(INSERT INTO `bol_font` VALUES (1,[^)]*),(2,[^)]*),(3,[^)]*),(4,[^)]*)\).*;/\1;/'
}


dump_struct user
dump_struct bible_refs
dump_struct bible_urls
dump_struct class
dump_struct classexercise
dump_struct exercisedir
dump_struct exerciseowner
dump_struct heb_urls
dump_struct language_en
dump_struct personal_font
dump_struct sta_displayfeature
dump_struct sta_question
dump_struct sta_quiz
dump_struct sta_quiztemplate
dump_struct sta_requestfeature
dump_struct sta_universe
dump_struct userclass
dump_struct userconfig
dump_struct_and_data alphabet
dump_struct_and_data migrations
dump_struct db_localize
dump_struct_and_data lexicon_Aramaic
dump_struct_and_data lexicon_Hebrew
dump_struct_and_data lexicon_greek
dump_font font
dump_struct_and_data translation_languages
dump_struct exam
dump_struct exam_active
dump_struct exam_finished
dump_struct exam_results
dump_struct exam_status

echo '*****************************************************************' > /dev/tty
echo 'NOTE: Check that `bol_font` only contains 4 records in the output' > /dev/tty
echo '*****************************************************************' > /dev/tty
