#!/usr/bin/env sh
# Start MySQL container only (XAMPP + Docker DB)
cd "$(dirname "$0")/.." || exit 1
test -f docker-compose.yml || { echo "docker-compose.yml not found"; exit 1; }
docker compose up db phpmyadmin -d
echo "MySQL + phpMyAdmin (Docker) running."
echo "  From host/XAMPP PHP: DB_HOST=127.0.0.1 DB_PORT=3307 (.env.xampp.example)"
echo "  phpMyAdmin: http://127.0.0.1:8081 (salon_user / salon_secret or root / DB_ROOT_PASSWORD)"
