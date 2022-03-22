#!/bin/bash

# Composer
sudo wget -O composer-setup.php https://getcomposer.org/installer
sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer
composer require phpmailer/phpmailer
composer update

# RabbitMQ
git clone https://github.com/stonX-IT490/rabbitmq-common.git
cp config.php rabbitmq-common/
