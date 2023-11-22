echo "Set root password (if password is empty)? (y/n)"
read ans
if test $ans = 'y'
then
   echo "root new password:"
   read pass
   sudo mysql -uroot -e "GRANT ALL PRIVILEGES ON *.* TO root@localhost IDENTIFIED BY '$pass'";
else
   echo "please input root password:"
   read pass
   sudo mysql -uroot -p$pass -e "GRANT ALL PRIVILEGES ON *.* TO root@localhost IDENTIFIED BY '$pass'";
fi

echo "Create DB 'audit'"
sudo mysql -uroot -p$pass -e "CREATE DATABASE IF NOT EXISTS audit";
echo "Create Table 'Computer, Detail'"
sudo mysql -uroot -p$pass audit < audit.sql
echo "<?php

\$DB_HOST = 'localhost';
\$DB_USER = 'root';
\$DB_PASS = '$pass';" > config.php
sudo mv config.php /var/www/html
