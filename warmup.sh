sudo chmod -R 777 ./app/cache
sudo rm -R ./app/cache
sudo mkdir ./app/cache
sudo php app/console cache:clear
sudo php app/console cache:warmup
sudo php app/console assetic:dump
sudo chmod -R 777 ./app/cache
sudo chmod -R 777 ./web/media
