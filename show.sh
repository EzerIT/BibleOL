#!/bin/bash

# Writes all information about the user with ID $3 to standard output. DB name is $3, DB prefix is $2

header() { printf "\n+++ %s:\n" "$*"; }

header "User account"
mysql -E $1 -e "select id, first_name, last_name, username, if(isadmin,'Yes','No') Administrator, email, oauth2_login, if(created_time=0,'Unknown',from_unixtime(created_time)) 'Created time', from_unixtime(last_login) 'Last login', warning_sent, if(isteacher,'Yes','No') Teacher, preflang, if(family_name_first,'Yes','No') 'Chinese name order', if(istranslator,'Yes','No') Translator, from_unixtime(accept_policy) 'Policy version', policy_lang, from_unixtime(acc_code_time) 'Accept policy' from $2user where id=$3"

header "Executed quizzes"
mysql -t $1 -e "select * from $2sta_quiz where userid=$3"

header "Quiz files"
mysql -t $1 -e "select id,userid,pathname,dbname,dbpropname,qoname from $2sta_quiztemplate where userid=$3"

header "Passages"
mysql -t $1 -e "select * from $2sta_universe where userid=$3" 

header "Questions"
mysql -E $1 -e "select * from $2sta_question where userid=$3"

header "Display features"
mysql -t $1 -e "select * from $2sta_displayfeature where userid=$3" 

header "Request features"
mysql -t $1 -e "select * from $2sta_requestfeature where userid=$3"

header "Grading"
mysql -t $1 -e "select * from $2sta_grading where userid=$3"

header "Grading system"
mysql -t $1 -e "select * from $2sta_grading_system where userid=$3" 

header "Grading feature"
mysql -t $1 -e "select * from $2sta_gradingfeature where userid=$3"

header "Grading path"
mysql -t $1 -e "select * from $2sta_gradingpath where userid=$3" 

header "Personal fonts"
mysql -t $1 -e "select * from $2personal_font where user_id=$3" 

header "Fonts"
mysql -E $1 -e "select * from $2font where user_id=$3" 
