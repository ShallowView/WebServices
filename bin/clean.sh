#!/bin/bash

#shellcheck source=dockerUtils.sh
source "bin/dockerUtils.sh"
#shellcheck source=dockerUtils.sh
source "bin/config.sh"

# ---
clean $DOCKER_CONTAINER_NAME \
	$DOCKER_CONFIG_VOLUME_NAME \
	$DOCKER_DATA_VOLUME_NAME