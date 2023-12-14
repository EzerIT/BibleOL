# bibleol-amd-multistage

Run a docker container of the Bible Online Learner locally on your Linux, Windows, or Mac (Intel) machine.

## Directory Contents
**emdros_amd**: a directory containing Emdros installation files for the AMD architecture, the most important of these files is `emdros_amd/emdros_3.8.0_amd64.deb`, this is the file used to install Emdros in the container.

**bibleol.conf**: the apache configuration file for the Bible Online Learner site.

**build_emdros.sh**: a shell script that installs the dependencies and executes configuration steps for Emdros

**clean_windows.sh**: a shell script to delete the images and containers of this project for windows users who do not have access to the make commands.

**config.sh**: a shell script that executes the necessary steps for MySQL and Apache configuration

**Dockerfile**: the Dockerfile used to build the bibleol-amd image

**environment.txt**: a text file containing the necessary configuration values for the container such as BASE_URL, MYSQL_USER, PW_SALT, etc...

**make_windows.sh**: a shell script to build the project for windows users who do not have access to the make commands.

**Makefile**: the Makefile that is used for automatically building the project, without a need for fluency in docker commands.

    - make (make build): builds the docker image and runs the container with the required port mapping
    - make clean: deletes the image and container of the project
    - make pull: pulls the ARM64 image from Docker Hub

**start.sh**: a shell script that is executed after a container is ran, this script carries out a few steps such as starting the MYSQL service which are not possible to complete in the Dockerfile. 

