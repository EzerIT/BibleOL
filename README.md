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

## Installation

### Dockerized Installation
Go to the Dockerfiles directory for detailed instructions on how to run Bible Online Learner on any computer.

### Standard Installation
Read Chapter 3 in techdoc/techdoc.pdf

## Contribute
Since Bible OL is fully developed in Open Source, you are welcome to contribute.
For any improvement you want to contribute, we kindly ask you to create a Github Issue with one of the following labels:
- enhancement
- new feature
- bug

For each contribution please give a clear description of the changes you want to make and how it will improve the learning system.
After creating the issue, one of the core developers will come back to you as soon as possible to discuss the improvement you want to make.


## Report bugs, other issues, or feature requests
Any user is invited to report bugs, other issues, or feature requests. To do so, please create a GitHub issue with one of the following labels:
- bug
- new feature
- question
- help wanted

One of our core developers will come back to you as soon as possible to discuss the issue.

