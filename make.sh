sudo composer install
sudo mkdir ./app/media/
sudo chmod -R 777 ./app/config/parameters.yml
sudo chmod -R 777 ./app/cache/
sudo chmod -R 777 ./app/logs/
sudo chmod -R 777 ./app/media/
sudo php app/console doctrine:schema:update --force
sudo php app/console doctrine:fixtures:load

#sudo php app/console cache:clear --no-debug
#sudo php app/console assets:install --symlink --relative
#sudo php app/console assetic:dump --no-debug
#sudo php app/console assetic:watch
#sudo chmod -R 777 ./app/cache/
