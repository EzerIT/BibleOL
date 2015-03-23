#!/bin/bash

php editprop.php $1  > x
php json_pretty_print.php $1 > x1
php json_pretty_print.php x > x2
diff x1 x2
