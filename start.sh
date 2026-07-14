#!/bin/sh

if [ -z "$PORT" ]; then
  echo "Error: PORT environment variable is not set"
  exit 1
fi

exec php -S 0.0.0.0:$PORT -t .
