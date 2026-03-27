# Docker (local development)

## Layout

| Path | Purpose |
|------|--------|
| `../docker-compose.yml` | **Entry point** — run from **repository root** (`docker compose up`). Starts Vite `frontend`, PHP `web`, MySQL `db`, optional `phpmyadmin`. |
| `web/Dockerfile` | PHP 8.2 + Apache image for the API (`pdo_mysql`, `mbstring`, rewrite). |
| `web/apache/000-default.conf` | Apache `DocumentRoot` → `/var/www/html/public`. |

Application source for the `web` service is mounted from **`api/`** in the root compose file — this folder only defines the **image** build.

## Commands

From the **repository root**:

```bash
cp api/.env.example api/.env   # optional
docker compose up --build -d
```

- Frontend: `http://localhost:3000` (default `FRONTEND_PORT`)
- API: `http://localhost:8080` (default `WEB_PORT`)
- phpMyAdmin: `http://localhost:8081` (default `PMA_PORT`)

**cPanel production** does not use these files; only `api/` is deployed (see `.github/workflows/deploy-api.yml`).
