# Bible Online Learner Windows Image
This directory containting the source code for running Bible Online Learner in a Windows container (using WSL2).

## Installation
The initial installation will take approximately 15 minutes, if the image is not already downloaded to your machine. Subsequent installations will generally take between 1-2 minutes. 

1. Open a terminal (PowerShell or Command Prompt) and change your directory to BibleOL/Dockerfiles/bibleol-windows

2. Build the project by typing 'make'. If you do not have make, you can install it with chocolatey with `choco install make`, or you can use our shell scripts to automate the build with `./make_windows.sh`.

3. If this is your first installation of the containerized application, please allow for 15 minutes of installation time. If this is a subsequent installation, the installation time should take between 1-2 minutes.

4. Go to localhost:8000 and you should be viewing the home page.

5. Type `make stop` to stop or pause the docker container, if you do not have make installed then copy and paste the commands from the Makefile stop target into your terminal.

6. Type `make start` to start a stopped container, if you do not have make installed then copy and paste the commands from the Makefile start target into your terminal.

7. If you have make type `make clean` to delete the container and image, or use the shell script with `./clean_windows.sh`

8. To log on as the default administrator go to Login and enter the login information

username: admin
password: bibleol_pwd
