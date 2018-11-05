#!/bin/bash
php ./bin/console cache:clear
php ./bin/console cache:warmup
rm -r ./var/cache
php -S 127.0.0.1:8000 -t public
