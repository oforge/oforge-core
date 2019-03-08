#!/usr/bin/env bash
rm -rf ./../var/cache
rm -rf ./../var/public
docker-compose -p oforgemysql down && docker-compose -p oforgemysql build && docker-compose -p oforgemysql up
