# Start Apache and MySQL
sudo systemctl is-enabled mysql.service || systemctl enable mysql.service
sudo systemctl status mysql.service || sudo systemctl start mysql.service
sudo service apache2 start

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
sudo sed -i -e "s/array()/array('MyBH', 'RRG', 'Hinneh', 'AndrewsUniversity')/g" ol.php
sudo cp config.php-dist config.php
sudo sed -i -e "s@https://example.com@${BASE_URL}@g" config.php
cd /var/www/html/BibleOL
sudo mysql ${MYSQL_DATABASE} < bolsetup.sql
sudo ./setup_lang.sh

# Fix PHP Errors in uploading exercises
sed -i 's/upload_max_filesize = 2M/upload_max_filesize = 10M/g' /etc/php/8.1/apache2/php.ini
sed -i 's/post_max_size = 8M/post_max_size = 10M/g' /etc/php/8.1/apache2/php.ini

#prepare quizzes directory
mkdir /var/www/html/BibleOL/quizzes
sudo chown -R www-data:www-data /var/www/html/BibleOL/quizzes

sudo service apache2 restart

sudo php index.php users generate_administrator admin Default Admin bibleol_pwd
