#!/bin/bash

#shellcheck source=dockerUtils.sh
source "bin/dockerUtils.sh"
#shellcheck source=dockerUtils.sh
source "bin/config.sh"
# ---
mode=${1-"prod"}
resourceDir="bin/docker"

# ---
stop $DOCKER_CONTAINER_NAME

removeImage $DOCKER_IMAGE_REFERENCE
buildImage $DOCKER_IMAGE_REFERENCE "$resourceDir/$DOCKER_FILE" "$resourceDir"

createVolumes $DOCKER_CONFIG_VOLUME_NAME $DOCKER_DATA_VOLUME_NAME

createContainer \
	$DOCKER_CONTAINER_NAME $DOCKER_IMAGE_REFERENCE "$mode" \
	-v $DOCKER_CONFIG_VOLUME_NAME:/config \
	-v $DOCKER_DATA_VOLUME_NAME:/data \
	-p $EXPOSED_PORT:80 \
