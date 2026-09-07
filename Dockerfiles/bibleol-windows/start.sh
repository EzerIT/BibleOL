# Start Apache and MySQL
# Fix mysql user home directory if it doesn't exist
if [ ! -d /var/lib/mysql ]; then
    mkdir -p /var/lib/mysql
    chown mysql:mysql /var/lib/mysql
fi
usermod -d /var/lib/mysql mysql

sudo systemctl is-enabled mysql.service || systemctl enable mysql.service
service mysql start
service apache2 start

# MySQL Configuration
# Ensure MySQL is running before executing commands
# Use a timeout to avoid infinite loop
MAX_TRIES=30
TRIES=0
until mysqladmin ping >/dev/null 2>&1 || [ $TRIES -eq $MAX_TRIES ]; do
  echo "Waiting for MySQL to start..."
  sleep 1
  TRIES=$((TRIES+1))
done

if [ $TRIES -eq $MAX_TRIES ]; then
  echo "Error: MySQL failed to start within $MAX_TRIES seconds."
  # Try to show logs if possible, though in Docker it might be hard
  exit 1
fi

echo "CREATE USER IF NOT EXISTS '${MYSQL_USER}'@'localhost' IDENTIFIED BY '${MYSQL_PASSWORD}';" | mysql 
echo "CREATE DATABASE IF NOT EXISTS ${MYSQL_DATABASE}" | mysql
echo "GRANT ALL PRIVILEGES ON ${MYSQL_DATABASE}.* TO ${MYSQL_USER}@localhost;" | mysql

# Optional steps to run in a container if the .htaccess is not created
cd /var/www/html/BibleOL
# Fix line endings for scripts in the mapped volume
find . -maxdepth 1 -name "*.sh" -exec sed -i 's/\r$//' {} +

if [ ! -e .htaccess ]; then  sudo cp .htaccess-dist .htaccess && echo "started hooks" ; fi

# Continue normal processing 
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
if [ -f bolsetup.sql ]; then
    sudo mysql ${MYSQL_DATABASE} < bolsetup.sql
else
    echo "Warning: bolsetup.sql not found."
fi

if [ -f setup_lang.sh ]; then
    sudo bash setup_lang.sh
else
    echo "Warning: setup_lang.sh not found."
fi

# Fix PHP Errors in uploading exercises
# Use generic path to php.ini or check version
PHP_INI=$(php -i | grep "Loaded Configuration File" | awk '{print $5}')
if [ -f "$PHP_INI" ]; then
    sed -i 's/upload_max_filesize = 2M/upload_max_filesize = 10M/g' "$PHP_INI"
    sed -i 's/post_max_size = 8M/post_max_size = 10M/g' "$PHP_INI"
fi

#prepare quizzes directory
if [ ! -e /var/www/html/BibleOL/quizzes ]; then mkdir /var/www/html/BibleOL/quizzes; fi
sudo chown -R www-data:www-data /var/www/html/BibleOL/quizzes

sudo service apache2 restart

sudo php index.php users generate_administrator admin Default Admin bibleol_pwd
