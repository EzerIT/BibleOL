#!/bin/sh

if [ ! -d .git ]; then
    echo You are not running this script in the top directory of the repository
    exit 1
fi

ln -sf ../../git-hooks/post-checkout .git/hooks

#Run once after setup
.git/hooks/post-checkout

exit 0
