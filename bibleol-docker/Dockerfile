FROM ubuntu:22.04
FROM tmccormack14/emdros-image:2023_11_20

# skip any interactive post-install configuration steps
RUN echo 'debconf debconf/frontend select Noninteractive' | debconf-set-selections

# Bible Online Learner Dependencies
RUN apt-get update && apt-get install -y sudo emacs systemctl \
    apache2 mysql-client mysql-server php php-curl php-dev php-sqlite3 \
    php-intl php-xml php-json git php-mbstring curl php-mysql

# Start Apache Service
RUN sudo service apache2 start

# Add configuration file for MySQL and Apache
ADD config.sh /
RUN chmod +x /config.sh

# Emdros (Uncomment these lines to install emdros without using the emdros docker image)
# RUN sudo apt-get install wget
# RUN cd home && wget https://emdros.org/downloads/emdros/emdros-3.8.0.tar.gz && \
#     tar -xvf emdros-3.8.0.tar.gz && \
#     sudo apt-get install -y g++ make binutils zlib1g zlib1g-dev build-essential fakeroot debhelper pkg-config python3 && \
#     sudo apt install -y libmysqlclient-dev && \
#     cd emdros-3.8.0 && dpkg-buildpackage -rfakeroot -d -us -uc && \
#     cd .. && \
#     sudo dpkg -i emdros_3.8.0*.deb &&  \
#     sudo phpenmod -v 8.1 EmdrosPHP8 && \
#     sudo systemctl restart apache2

# install emdros from deb file, enable emdros extension for php
RUN sudo dpkg -i /tmp/emdros_deb/*.deb
RUN sudo phpenmod -v 8.1 EmdrosPHP8
RUN sudo systemctl restart apache2


# Bibleol and Client Set up
RUN cd /var/www/html && sudo git clone --recursive https://github.com/EzerIT/BibleOL && \
    cd BibleOL && \
    bash git-hooks/setup.sh && \
    sudo cp .htaccess-dist .htaccess && \
    sudo apt install -y npm && \
    sudo npm install -g -y less && \
    sudo npm install -g -y typescript && \
    sudo npm install -y @types/bootstrap@4.5.0 @types/jquery @types/jqueryui

# Apache Configuration
ADD bibleol.conf /etc/apache2/sites-available/
RUN sudo chown www-data:www-data /var/lib/php/sessions

# set working directory
WORKDIR /var/www/html/BibleOL

# allow interactive terminal for users
CMD "/bin/bash"
