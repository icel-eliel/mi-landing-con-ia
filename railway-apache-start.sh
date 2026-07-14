#!/bin/sh
set -e

PORT="${PORT:-8080}"

case "$PORT" in
  ''|*[!0-9]*)
    echo "PORT must be numeric, received '$PORT'. Falling back to 8080."
    PORT=8080
    ;;
esac

sed -i "s/^Listen .*/Listen ${PORT}/" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:[0-9]\+>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf

exec apache2-foreground
