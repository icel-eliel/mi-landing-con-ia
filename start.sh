#!/bin/sh
set -e

echo "Starting app with PORT=$PORT"

if [ -z "$PORT" ]; then
  echo "Error: PORT environment variable is not set" >&2
  exit 1
fi

exec php -S "0.0.0.0:$PORT" -t .
