#!/bin/sh

current=$(date +%Y%m%d-%H%M%S)

docker compose exec nginx mv /var/log/nginx/access.log "/var/log/nginx/access.log.${current}"
docker compose exec nginx /usr/sbin/nginx -s reopen

docker compose exec mysql mv /var/log/mysql/mysql-slow.log "/var/log/mysql/mysql-slow.log.${current}"
docker compose exec mysql mysqladmin -u root --password=root flush-logs

if [ -f .idea/record ]; then
  mv .idea/record ".idea/record.${current}"
fi

