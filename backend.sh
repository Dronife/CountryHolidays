#!/usr/bin/env bash

# Make us independent from working directory
pushd $(dirname $0) >/dev/null
popd >/dev/null

docker-compose exec php bash
