#!/bin/sh

docker compose exec nginx mv /var/log/nginx/access.log /var/log/nginx/access.log.`date +%Y%m%d-%H%M%S`
docker compose exec nginx /usr/sbin/nginx -s reopen

