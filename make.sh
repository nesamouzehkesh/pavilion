sudo composer install
sudo chmod -R 777 ./app/config/parameters.yml
sudo chmod -R 777 ./app/cache/
sudo chmod -R 777 ./app/logs/
sudo chmod -R 777 ./app/media/
sudo php app/console doctrine:schema:update --force
sudo php app/console doctrine:fixtures:load



