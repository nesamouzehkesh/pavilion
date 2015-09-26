sudo php app/console cache:clear --env=prod
sudo php app/console assetic:dump
sudo chmod -R 777 ./app/cache
sudo chmod -R 777 ./web/media
