FROM ubuntu:latest

RUN echo 'debconf debconf/frontend select Noninteractive' | debconf-set-selections
RUN apt-get update && apt-get upgrade -y 
ADD https://raw.githubusercontent.com/emdros/emdros/master/SWIG/download_and_make_local_swig.sh /root/
ADD https://raw.githubusercontent.com/emdros/emdros/master/build-deb.sh ./root/

COPY emdros_arm /home

ADD build_emdros.sh /
RUN chmod +x /build_emdros.sh

ADD config.sh /
RUN chmod +x /config.sh

ADD bibleol.conf /

EXPOSE 8000

CMD '/bin/bash'