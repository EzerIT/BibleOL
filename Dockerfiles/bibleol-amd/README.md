# bibleol-arm

Run a docker container of the Bible Online Learner locally on your M1 or M2 mac.


## Directory Contents
**emdros_arm**: a directory containing Emdros installation files for the ARM architecture, the most important of these files is `emdros_arm/emdros_3.8.0_arm64.deb`, this is the file used to install Emdros in the container.

**bibleol.conf**: the apache configuration file for the Bible Online Learner site.

**Dockerfile**: the Dockerfile used to build the bibleol-arm image

**environment.txt**: a text file containing the necessary configuration values for the container such as BASE_URL, MYSQL_USER, PW_SALT, etc...

**Makefile**: the Makefile that is used for automatically building the project, without a need for fluency in docker commands.

    - make (make build): builds the docker image and runs the container with the required port mapping
    - make clean: deletes the image and container of the project
    - make pull: pulls the ARM64 image from Docker Hub

**start.sh**: a shell script that is executed after a container is ran, this script carries out a few steps such as starting the MYSQL service which are not possible to complete in the Dockerfile. 

