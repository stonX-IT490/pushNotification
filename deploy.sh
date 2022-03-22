#!/bin/bash

# Composer
cd ~/pushNotification/
sudo wget -O composer-setup.php https://getcomposer.org/installer
sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer
composer require phpmailer/phpmailer
composer update
cd ~/
