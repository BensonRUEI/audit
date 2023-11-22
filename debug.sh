echo "Please input password:"
read pass
sudo mysql -uroot -p$pass -e "SHOW GRANTS FOR root@localhost;";

sudo cat /var/www/html/config.php
