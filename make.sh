sudo composer install
mkdir ./app/cache/
chmod -R 777 ./app/config/parameters.yml
chmod -R 777 ./app/cache/
chmod -R 777 ./app/logs/
chmod -R 777 ./app/media/
php app/console doctrine:schema:update --force
php app/console doctrine:fixtures:load
$ php app/console cache:clear --env=prod --no-debug
php app/console assets:install --symlink --relative
php app/console assetic:dump --watch