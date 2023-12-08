cd /home

apt-get update && apt-get install -y sudo emacs systemctl zip \
apache2 mysql-client mysql-server php php-curl php-dev php-sqlite3 \
php-intl php-xml php-json git php-mbstring curl php-mysql

apt-get install -y wget sudo

sudo apt-get install -y g++ make binutils zlib1g zlib1g-dev build-essential fakeroot debhelper pkg-config python3

sudo apt install -y libmysqlclient-dev
dpkg -i  /home/emdros_3.8.0*.deb
sudo phpenmod -v 8.1 EmdrosPHP8
sudo systemctl restart apache2