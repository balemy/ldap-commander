#!/bin/bash
set -e

if [ "$ENABLE_CADDY" = "true" ]; then
    echo "Starting PHP in background..."
    php /app/cmda serve 0.0.0.0:8080 &
    echo "Starting Caddy with hostname: $HOSTNAME"
    exec caddy run --config /etc/caddy/Caddyfile --adapter caddyfile
else
    echo "Starting PHP only..."
    exec php /app/cmda serve 0.0.0.0:8080
fi

