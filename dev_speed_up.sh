php app/console cache:clear --no-warmup
php app/console assets:install --symlink --relative
php app/console assetic:dump --watch
