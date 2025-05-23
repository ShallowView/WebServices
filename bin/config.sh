#!/bin/bash

#Configuration file for the Docker Caddy server

export DOCKER_FILE=Dockerfile

export DOCKER_IMAGE_REFERENCE=shallowview/www:2.10.0-alpine
export DOCKER_CONTAINER_NAME=sv-webservices

export DOCKER_CONFIG_VOLUME_NAME=$DOCKER_CONTAINER_NAME-config
export DOCKER_DATA_VOLUME_NAME=$DOCKER_CONTAINER_NAME-data
export EXPOSED_PORT=80