#!/usr/bin/env sh
# Start MySQL + phpMyAdmin containers
cd "$(dirname "$0")/.." || exit 1
test -f docker-compose.yml || { echo "docker-compose.yml not found"; exit 1; }
docker compose up db phpmyadmin -d
echo "MySQL + phpMyAdmin (Docker) running."
echo "  From host tools: DB_HOST=127.0.0.1 DB_PORT=3307"
echo "  phpMyAdmin: http://127.0.0.1:8081 (salon_user / salon_secret or root / DB_ROOT_PASSWORD)"
