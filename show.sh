#!/bin/bash

# Writes all information about the user with ID $1 to standard output

header() { printf "\n+++ %s:\n" "$*"; }

header "User account"
mysql -E pl -e "select id, first_name, last_name, username, if(isadmin,'Yes','No') Administrator, email, oauth2_login, if(created_time=0,'Unknown',from_unixtime(created_time)) 'Created time', from_unixtime(last_login) 'Last login', warning_sent, if(isteacher,'Yes','No') Teacher, preflang, if(family_name_first,'Yes','No') 'Chinese name order', if(istranslator,'Yes','No') Translator, from_unixtime(accept_policy) 'Policy version', policy_lang, from_unixtime(acc_code_time) 'Accept policy' from pl_user where id=$1"

header "Executed quizzes"
mysql -t pl -e "select * from pl_sta_quiz where userid=$1"

header "Quiz files"
mysql -t pl -e "select id,userid,pathname,dbname,dbpropname,qoname from pl_sta_quiztemplate where userid=$1"

header "Passages"
mysql -t pl -e "select * from pl_sta_universe where userid=$1" 

header "Questions"
mysql -E pl -e "select * from pl_sta_question where userid=$1"

header "Display features"
mysql -t pl -e "select * from pl_sta_displayfeature where userid=$1" 

header "Request features"
mysql -t pl -e "select * from pl_sta_requestfeature where userid=$1"

header "Grading"
mysql -t pl -e "select * from pl_sta_grading where userid=$1"

header "Grading system"
mysql -t pl -e "select * from pl_sta_grading_system where userid=$1" 

header "Grading feature"
mysql -t pl -e "select * from pl_sta_gradingfeature where userid=$1"

header "Grading path"
mysql -t pl -e "select * from pl_sta_gradingpath where userid=$1" 

header "Personal fonts"
mysql -t pl -e "select * from pl_personal_font where user_id=$1" 

header "Fonts"
mysql -E pl -e "select * from pl_font where user_id=$1" 
