FROM ubuntu:22.04

LABEL org.opencontainers.image.authors = "@tmccormack14, @ulrikp"

#
# Build a .deb package of Emdros for use with Bible Online Learner
#

# First, we make the installation Noninteractive, so as to not stop,
#  e.g., when configuring tzdata.
#
# See https://github.com/phusion/baseimage-docker/issues/58#issuecomment-47995343
#
RUN echo 'debconf debconf/frontend select Noninteractive' | debconf-set-selections

# Then, we ensure that Ubuntu is up-to-date
RUN apt-get update && apt-get upgrade -y 

# Then we install all the dependencies.
#
# We only install the dependencies actually needed for Emdros when it
# is used with Bible Online Learner.
#
# For example, mysql support is not provided in the Emdros package
#  that we build here, because Bible Online Learner only uses SQLite3
# Emdros databases.
#
#
RUN apt-get install -y \
    emacs-nox \
    git \
    wget \
    g++ make binutils build-essential \
    re2c \
    fakeroot debhelper \
    pkg-config \
    automake autoconf libtool autotools-dev \
    zip \
    zlib1g zlib1g-dev \
    libxml2-utils \
    groff imagemagick \
    texlive-latex-base latex2html \
    xinetd \
    && apt-get install -y \
    php-dev

#
# We must also use a locally compiled version of SWIG, because we need
# version >= 4.1.1.
#
ADD https://raw.githubusercontent.com/emdros/emdros/master/SWIG/download_and_make_local_swig.sh /root/
RUN bash /root/download_and_make_local_swig.sh 4.1.1 

# Now we add a script thaty will do the actual building of the .deb
#
# This script is part of Emdros.
#
# The script will take care of cloning the GitHub emdros/emdros repo,
# and running all of the requisite commands.
#
ADD https://raw.githubusercontent.com/emdros/emdros/master/build-deb.sh ./root/

# Now build Emdros as a .deb package
RUN export PATH=/root/opt/bin:${PATH} &&  \
    bash /root/build-deb.sh

#
# Copy the .deb to /tmp/emdros_deb/
# For copying out with podman's cp command
#
RUN mkdir -p /tmp/emdros_deb/ 
RUN chmod 755 /tmp/emdros_deb/
RUN cp /root/build/emdros-build-*/emdros/emdros*.deb /tmp/emdros_deb/ 
RUN chmod 644 /tmp/emdros_deb/*.deb 

#CMD /bin/bash