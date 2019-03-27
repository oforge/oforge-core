#!/usr/bin/env bash
rm -r ./../var/cache
rm -r ./../var/public
docker-compose -p oforgemysql down && docker-compose -p oforgemysql build && docker-compose -p oforgemysql up
