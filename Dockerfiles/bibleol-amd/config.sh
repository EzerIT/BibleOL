# Start Apache and MySQL
sudo systemctl is-enabled mysql.service || systemctl enable mysql.service
sudo systemctl status mysql.service || sudo systemctl start mysql.service
sudo service apache2 start

# Apache Configuration
sudo a2enmod ssl rewrite
sudo a2dissite 000-default 

cd /var/www/html && sudo git clone --recursive https://github.com/EzerIT/BibleOL && \
    cd BibleOL && \
    bash git-hooks/setup.sh && \
    sudo cp .htaccess-dist .htaccess && \
    sudo apt install -y npm && \
    sudo npm install -g -y less && \
    sudo npm install -g -y typescript && \
    sudo npm install -y @types/bootstrap@4.5.0 @types/jquery @types/jqueryui

sudo chown www-data:www-data /var/lib/php/sessions
sudo mv /bibleol.conf /etc/apache2/sites-available/

sudo a2ensite bibleol 
sudo service apache2 reload

# MySQL Configuration
echo "CREATE USER '${MYSQL_USER}'@'localhost' IDENTIFIED BY '${MYSQL_PASSWORD}';" | mysql 
echo "CREATE DATABASE ${MYSQL_DATABASE}" | mysql
echo "GRANT ALL PRIVILEGES ON ${MYSQL_DATABASE}.* TO ${MYSQL_USER}@localhost;" | mysql
cd /var/www/html/BibleOL/myapp/config
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