#!/bin/bash

docker-compose down
docker rm packapps_database-server
docker volume rm packapps_packapps-db
docker-compose up --build
