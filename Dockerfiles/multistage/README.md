# Bible Online Learner Multistage Docker Images

A multistage approach entails installing most of the project using shell scripts instead of Dockerfiles. The purpose of multistage image builds is to optimize the space efficiency that images take up. When an image is built via a Dockerfile the installation steps are cached to save time on subsequent installations. The downside of this is that the cached memory takes up space, that could be freed in the case of a multistage approach. The memory requirements for the multistage approach are 800 MB, and the requirements for the regular approach are 2GB, so the multistage approach is only reccomended if the user cannot allocate 2GB of storage to this project.

## Directory Contents

**bibleol-amd-multistage**: a directory containing the source code responsible for building the AMD image and container using a multistage approach.

**bibleol-arm-multistage**: a directory containing the source code responsible for building the ARM image and container using a multistage approach.
