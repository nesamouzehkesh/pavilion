Best practices basic web application development using Symfony2

----------------------------------
Welcome to the Symfony Standard Edition - a fully-functional Symfony2
application that you can use as the skeleton for your new applications.

This document contains information on how to download, install, and start
using Symfony. For a more detailed explanation, see the [Installation][1]
chapter of the Symfony Documentation.

1) Installing the Standard Edition
----------------------------------
First create a Db for this project (for example symfony_basic)
Go to WWW and then type:

    git clone https://github.com/SamanShafigh/symfony-basic.git

Then, use the `./make.sh` command to generate the application. To do this first give
permission to this file and then run it. It will install all third party vendors, and
creating the Database Tables. 

    sudo chmod 777 make.sh
    ./make.sh

Or do followings:
1: Install third party vendors

    sudo composer install
    sudo mkdir ./app/cache/
    sudo chmod -R 777 ./app/config/parameters.yml
    sudo chmod -R 777 ./app/cache/
    sudo chmod -R 777 ./app/logs/
    sudo chmod -R 777 ./app/media/
    
2: Creating the Database Tables/Schema

    sudo php app/console doctrine:schema:update --force

3: Populate System Database using Data Fixtures

    php app/console doctrine:fixtures:load
