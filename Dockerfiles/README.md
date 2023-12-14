# Bible Online Learner Docker Images
There are two primary docker images for the Bible Online Learner. The image ```bibleol-arm``` is used for Mac operating systems that have an M1 or M2 chip, this is most of the newer models. The other image ```bibleol-amd``` will work on all other architectures including Windows, Linux, and Mac (Intel).

# Dockerfiles Directory Contents

**bibleol-amd**: the directory containting the source code for running Bible Online Learner in an AMD container (Linux, Windows, Mac (Intel)).

**bibleol-arm**: the directory containting the source code for running Bible Online Learner in an ARM container (Mac M1 & M2).

**multistage**: a directory containing two sub-directories that build AMD and ARM containers respectively using primarily shell scripts instead of Dockerfiles, this approach is more space efficient, but the installation time will be constant across subsequent builds of the project because none of the required installation steps will be cached. This method is only reccomended for users who have less than 2GB of storage available.


# Installation
The initial installation will take approximately 15 minutes, if the image is not already downloaded to your machine. Subsequent installations will generally take between 1-2 minutes. There is no need to be fluent in Docker commands to build these projects, because each sub-directory contains shell scripts and Makefiles that automate the installation.

## Mac M1 & M2 
```
1. Open a terminal and change your directory to BibleOL/Dockerfiles/bibleol-arm

2. Build the project by typing 'make'. If you do not have make you can install it with apt-get install make

3. If this is your first installation of the containerized application, please allow for 15 minutes of installation time. If this is a subsequent installation, the installation time should take between 1-2 minutes.

3. Go to localhost:8000 and you should be viewing the home page.

4. Type make clean to delete the image and container
```

## Linux
```
1. Open a terminal and change your directory to BibleOL/Dockerfiles/bibleol-amd

2. Build the project by typing 'make'. If you do not have make you can install it with apt-get install make

3. If this is your first installation of the containerized application, please allow for 15 minutes of installation time. If this is a subsequent installation, the installation time should take between 1-2 minutes.

3. Go to localhost:8000 and you should be viewing the home page.

4. Type make clean to delete the image and container
```

## Windows
```
1. Open a terminal and change your directory to BibleOL/Dockerfiles/bibleol-amd

2. Build the project by typing 'make'. If you do not have make, you can install it with choclatey with choco install make, or you can use our shell scripts to automate the build with ./make_windows.sh.

3. If this is your first installation of the containerized application, please allow for 15 minutes of installation time. If this is a subsequent installation, the installation time should take between 1-2 minutes.

3. Go to localhost:8000 and you should be viewing the home page.

4. If you have make type make clean to delete the container and image, or use the shell script with ./clean_windows.sh
```

## Mac (Intel)
```
1. Open a terminal and change your directory to BibleOL/Dockerfiles/bibleol-amd

2. Build the project by typing 'make'. If you do not have make you can install it with apt-get install make

3. If this is your first installation of the containerized application, please allow for 15 minutes of installation time. If this is a subsequent installation, the installation time should take between 1-2 minutes.

3. Go to localhost:8000 and you should be viewing the home page.

4. Type make clean to delete the image and container
```


# Contributions
All of the running containers will have git installed, and the root directory ```/var/www/html/BibleOL``` will point to this repository. Therefore any edits made in a docker container can be commited and pushed back to this repository.