apt-get update
apt-get install sudo
sudo apt-get install -y emacs
sudo apt-get update
sudo apt-get install -y systemctl
sudo apt install -y apache2 mysql-client mysql-server php php-curl php-dev php-sqlite3 php-intl php-xml php-json git php-mbstring curl php-mysql
sudo systemctl start mysql.service
sudo service apache2 start

#sudo dpkg -i /tmp/emdros_deb/*.deb
sudo phpenmod -v 8.1 EmdrosPHP8
sudo systemctl restart apache2

cd /var/www/html && sudo git clone --recursive https://github.com/EzerIT/BibleOL
cd BibleOL
echo "CREATE USER '${MYSQL_USER}'@'localhost' IDENTIFIED BY '${MYSQL_PASSWORD}';" | mysql 
echo "CREATE DATABASE ${MYSQL_DATABASE}" | mysql
echo "GRANT ALL PRIVILEGES ON ${MYSQL_DATABASE}.* TO ${MYSQL_USER}@localhost;" | mysql
cd myapp/config
sudo cp database.php-dist database.php
sudo sed -i -e "s/USERNAME/${MYSQL_USER}/g" database.php 
sudo sed -i -e "s/PASSWORD/${MYSQL_PASSWORD}/g" database.php 
sudo sed -i -e "s/DATABASE/${MYSQL_DATABASE}/g" database.php 
sudo sed -i -e "s/localhost/localhost:3306/g" database.php
sudo cp ol.php-dist ol.php
sudo cp config.php-dist config.php
sudo sed -i -e "s@https://example.com@${BASE_URL}@g" config.php
cd /var/www/html/BibleOL
sudo mysql ${MYSQL_DATABASE} < bolsetup.sql
sudo ./setup_lang.sh
sudo cp .htaccess-dist .htaccess
sudo systemctl restart apache2
cd /etc/apache2/sites-available

cd /var/www/html/BibleOL && /
    bash git-hooks/setup.sh && /
    sudo apt install -y npm && /
    sudo npm install -g -y less && /
    sudo npm install -g -y typescript && /
    sudo npm install -y @types/bootstrap@4.5.0 @types/jquery @types/jqueryui

sudo chown www-data:www-data /var/lib/php/sessions
a2enmod ssl rewrite
systemctl restart apache2

a2dissite 000-default
sudo systemctl reload apache2
a2ensite bible
sudo systemctl reload apache2
