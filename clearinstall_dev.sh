#!/bin/bash
php app/console cache:clear --env=dev --no-debug
php app/console assets:install web --env=dev
php app/console assetic:dump --env=dev 
chown -R www-data:manuroot ../Symfony_epost
chown -R manuroot:manuroot ../Symfony_epost/src/*
chown -R manuroot:manuroot ../Symfony_epost/web/*
chown -R www-data:manuroot ../Symfony_epost/web/uploads

