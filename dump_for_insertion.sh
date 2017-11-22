#!/bin/bash

# This script dumps the database $1 in a format suitable for generating a new, virgin installation
# of Bible OL.
# The database prefix is $2.
# The output is sent to the file $3.
# Learning Journey output is sent to the file $4.

if [ $# -ne 4 ]; then
   echo 'Usage: dump_for_insertion.sh <database> <prefix> <output> <LJ output>'
   exit 1;
fi

(
    # Tables for which no data should be dumped (bol_user must be first because of foreign keys):
    mysqldump --lock-all-tables --quote-names --events --no-data "$1" \
              $2_user                  \
              $2_bible_refs            \
              $2_bible_urls            \
              $2_class                 \
              $2_classexercise         \
              $2_exercisedir           \
              $2_exerciseowner         \
              $2_heb_urls              \
              $2_language_en           \
              $2_personal_font         \
              $2_sta_displayfeature    \
              $2_sta_question          \
              $2_sta_quiz              \
              $2_sta_quiztemplate      \
              $2_sta_requestfeature    \
              $2_sta_universe          \
              $2_userclass             \
              $2_userconfig

    # Tables for which data should be dumped:
    mysqldump --lock-all-tables --quote-names --extended-insert --create-options --events "$1" \
              $2_alphabet              \
              $2_migrations            \
              $2_db_localize           \
              $2_lexicon_Aramaic       \
              $2_lexicon_Hebrew        \
              $2_lexicon_greek

    # Tables for which some data should be dumped:
    mysqldump --lock-all-tables --quote-names --extended-insert --create-options --where='user_id=0' --events "$1" \
              $2_font

    # Tables to ignore
    # $2_language_comment
    # $2_language_da
    # $2_language_es
    # $2_language_pt
    # $2_language_zh-simp
    # $2_language_zh-trad
    # $2_lexicon_Aramaic_da
    # $2_lexicon_Aramaic_de
    # $2_lexicon_Aramaic_en
    # $2_lexicon_Hebrew_da
    # $2_lexicon_Hebrew_de
    # $2_lexicon_Hebrew_en
    # $2_lexicon_greek_en

) | sed -e 's/ AUTO_INCREMENT=[0-9]*\b//g' > "$3"

(
    # Tables for Learning Journey (no data)
    mysqldump --lock-all-tables --quote-names --extended-insert --create-options --events --no-data "$1" \
              $2_sta_grading           \
              $2_sta_grading_system    \
              $2_sta_gradingfeature    \
              $2_sta_gradingpath
) | sed -e 's/ AUTO_INCREMENT=[0-9]*\b//g' > "$4"
