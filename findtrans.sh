#!/bin/bash

hfile=myapp/helpers/heb_trans_helper.php
afile=myapp/helpers/aram_trans_helper.php

cat > $hfile <<'EOF'
<?php
  // Translates Hebrew lexemes in internal format to transliterated format

$lex_translit = array();

EOF

cat > $afile <<'EOF'
<?php
  // Translates Aramaic lexemes in internal format to transliterated format

$lex_translit = array();

EOF


echo 'select all objects where [word language=Hebrew get lex,g_voc_lex_translit]' | mql -d db/ETCBC4 |grep word | x2c | sed -e 's/.*lex="\(.*\)",g_voc_lex_translit="\(.*\)".*/$lex_translit["\1"]="\2";/' | sort | uniq >> $hfile

echo 'select all objects where [word language=Aramaic get lex,g_voc_lex_translit]' | mql -d db/ETCBC4 |grep word | x2c | sed -e 's/.*lex="\(.*\)",g_voc_lex_translit="\(.*\)".*/$lex_translit["\1"]="\2";/' | sort | uniq >> $afile
