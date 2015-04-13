Bible Online Learner (Bible OL)
===============================

This repository contains the files necessary to run and modify the Bible Online Learner.

The code is best used on a computer running Linux, but it should not be difficult to make it run on
a Windows or Mac computer.


To clone this repository on a Linux machine, please use this command:

    git clone --recursive https://github.com/EzerIT/BibleOL

(If you have forked this GitHub repository, you should replace the URL in the "git clone" command
with with a URL that points to your repository on GitHub.)

Then go to the BibleOL directory you just downloaded and execute this command:

    git-hooks/setup.sh

This will install a Git hook that downloads the necessary databases from Dropbox when needed.

*If you want to set up a Bible OL website, or if you want to develop for Bible OL, you must read the
 file techdoc/techdoc.pdf.*

