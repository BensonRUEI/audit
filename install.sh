sudo apt clean packages
sudo apt update
sudo apt install nginx default-mysql-server php-fpm php-mysql php-mbstring
sudo rm -rf /var/www/html/index.html
sudo rm -rf /var/www/html/index.nginx-debian.html
sudo mv /etc/nginx/sites-available/default /etc/nginx/sites-available/default.bak
sudo cp ./default /etc/nginx/sites-available/
sudo chmod 777 /etc/nginx/sites-enabled/default
sudo mv *.php /var/www/html
sudo mv *.csv /var/www/html
sudo sed -i 's|fastcgi_pass unix:/run/php/php8.2-fpm.sock;|fastcgi_pass unix:/run/php/php8.4-fpm.sock;|' /etc/nginx/sites-enabled/default
