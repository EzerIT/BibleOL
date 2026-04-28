FROM ubuntu:22.04

ARG EMDROS_DIR=emdros_amd
ARG PLATFORM=linux

RUN echo 'debconf debconf/frontend select Noninteractive' | debconf-set-selections
RUN apt-get update && apt-get upgrade -y 
ADD https://raw.githubusercontent.com/emdros/emdros/master/SWIG/download_and_make_local_swig.sh /root/
ADD https://raw.githubusercontent.com/emdros/emdros/master/build-deb.sh ./root/

COPY ${EMDROS_DIR} /home

RUN apt-get update
RUN apt-get install -y sudo wget emacs-nox systemctl apache2 mysql-client mysql-server php php-curl php-dev php-sqlite3 php-intl php-xml php-json git php-mbstring curl php-mysql
RUN sudo apt-get install -y g++ make binutils zlib1g zlib1g-dev build-essential fakeroot debhelper pkg-config python3
RUN sudo apt install -y libmysqlclient-dev
RUN dpkg -i  /home/emdros_3.8.0*.deb
RUN sudo phpenmod -v 8.1 EmdrosPHP8
RUN sudo systemctl restart apache2

RUN sudo a2enmod ssl rewrite
RUN sudo a2dissite 000-default 

RUN cd /var/www/html && git clone --recursive https://github.com/EzerIT/BibleOL
RUN cd /var/www/html/BibleOL && bash git-hooks/setup.sh && sudo cp .htaccess-dist .htaccess

RUN sudo chown www-data:www-data /var/lib/php/sessions

ADD bibleol.conf /etc/apache2/sites-available/
# Fix CRLF line endings if building for Windows
RUN if [ "$PLATFORM" = "windows" ]; then sed -i 's/\r$//' /etc/apache2/sites-available/bibleol.conf; fi

RUN sudo a2ensite bibleol 
# RUN sudo service apache2 reload
EXPOSE 8000

ADD start.sh /
RUN chmod +x start.sh
# Fix CRLF line endings if building for Windows
RUN if [ "$PLATFORM" = "windows" ]; then sed -i 's/\r$//' /start.sh; fi

# update repository
RUN cd /var/www/html/BibleOL && git pull origin master

CMD '/bin/bash'
