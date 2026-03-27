# Deployment guide — Change Hair & Beauty

**First-time from scratch?** Use the full checklist: **[INITIAL-SETUP.md](INITIAL-SETUP.md)** (MySQL import, cPanel paths, GitHub secrets, OAuth, verification).

One place for the **three production targets**: GitHub Pages (frontend), cPanel (PHP API), cPanel MySQL.

**Layout:** Vite/React SPA at **repo root** (`src/`, `index.html`, `vite.config.ts`) · **`api/`** = PHP API · **`api/sql/`** = schema/migrations.

---

## Current shape (repo layout)

| Area | Location | Role |
|------|----------|------|
| **Frontend** | Repo root (`src/`, `index.html`, `vite.config.ts`, `package.json`) | Vite/React SPA → `npm run build` → `dist/` for GitHub Pages |
| **API** | `api/` | PHP; JSON under `public/api/*`, OAuth, mail, static assets under `public/` |
| **DB** | `api/sql/` | Schema + migrations; local Docker MySQL mirrors production conceptually |
| **Dev** | Root `docker-compose.yml` | Frontend + API + MySQL locally (`docker/README.md`) |

---

## 1. GitHub Pages (frontend)

**Build:** from **repository root**:

```bash
npm ci && npm run build
```

Output: **`dist/`** — published by CI.

**Build-time env (Vite):** configure in **`.env`** locally; in GitHub Actions use repo **Secrets** (`VITE_API_URL`). Workflow sets `VITE_BASE` to `/<repo-name>/`.

| Variable | Purpose |
|----------|---------|
| `VITE_API_URL` | Public base URL of the PHP API, **no trailing slash**. |
| `VITE_BASE` | Router/asset base path for project Pages (often `/<repository-name>/`). |

**Not baked into the SPA:** `CHB_APP_PUBLIC_URL` is **API-only** (`api/.env` on the server).

**CI:** `.github/workflows/deploy.yml` — `npm ci`, `npm run build`, copies `dist/index.html` → `404.html`, uploads **`dist`**.

**API alignment:** `ALLOWED_ORIGINS` must include your Pages origin; `CHB_SESSION_CROSS_SITE=1` on HTTPS when SPA and API differ by host.

---

## 2. cPanel (API — PHP)

Deploy so the vhost **document root** maps to **`api/public/`**.

- Upload **`api/`** (rsync, FTP, or `.github/workflows/deploy-api.yml`).
- **`api/.env`** on the server only (never commit secrets).

**Checklist:** PHP 8.x + `pdo_mysql`, `composer install --no-dev`, OAuth/SMTP/CORS/`CHB_APP_PUBLIC_URL` as in `api/.env.example`.

**Docker** is local dev only; production cPanel does not use `docker/`.

---

## 3. cPanel MySQL

1. Create database + user in cPanel (host often **`localhost`**).
2. Import **`api/sql/schema.sql`**.
3. Set **`DB_*`** in **`api/.env`**.

---

## Environment file map

| File | Role |
|------|------|
| **Repo root** `.env` | **Vite** — `VITE_*` for the SPA. |
| **`api/.env`** | **PHP** — MySQL, SMTP, OAuth, CORS, CardConnect, `CHB_APP_PUBLIC_URL`. |
| **Repo root `.env`** (optional) | Merged **after** `api/.env` by PHP (`config/env.php`) for overrides like `CHB_PAYMENT_SKIP`. |

---

## CI alignment

| Workflow | Purpose |
|----------|---------|
| **`deploy.yml`** | Frontend only → GitHub Pages. |
| **`deploy-api.yml`** | API only → rsync `api/` to cPanel (`api/**` path filter). |

---

## Assets

- **`src/assets/`** — bundled with the SPA (primary UI images).
- **`api/public/assets/`** — PHP-served / email / legacy paths; keep `config/salon_data.php` URLs consistent with deployment.

---

## Summary

Use **`.env.example`** (root) and **`api/.env.example`** as templates; from repo root run **`npm run dev`** / **`npm run build`**.
