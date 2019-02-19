#!/usr/bin/env bash
docker-compose -p oforgemysql down && docker-compose -p oforgemysql build && docker-compose -p oforgemysql up