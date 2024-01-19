#!/bin/bash

# SERVER_NAME='mercure.localhost:8080' \


MERCURE_PUBLISHER_JWT_KEY='!ChangeThisMercureHubJWTSecretKey!' \
MERCURE_SUBSCRIBER_JWT_KEY='!ChangeThisMercureHubJWTSecretKey!' \
caddy run --config Caddyfile
# ./mercure run --config Caddyfile