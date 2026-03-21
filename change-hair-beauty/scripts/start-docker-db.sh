#!/usr/bin/env sh
# Start MySQL container only (XAMPP + Docker DB)
cd "$(dirname "$0")/.." || exit 1
test -f docker-compose.yml || { echo "docker-compose.yml not found"; exit 1; }
docker compose up db -d
echo "MySQL (Docker) running. Use DB_HOST=127.0.0.1 DB_PORT=3307 in .env (see .env.xampp.example)."
