#!/bin/bash

seq=$1
f1=$2
f2=$3

echo "select all objects  $seq where [word as w part_of_speech=verb and $f1<>w.$f2 get $f1, $f2] GO" | ./mql.sh -d WIVU | ~/Documents/Shared/src/x2u/x2c | grep word
#echo "select all objects  $seq where [word as w part_of_speech=verb and $f1<>w.$f2 get $f1, $f2] GO" | ./mql.sh -d WIVU  | grep word
