# Initial production setup — complete checklist

This guide walks through **first-time** deployment of **Change Hair & Beauty** from zero to a working **GitHub Pages** site + **PHP API on cPanel** + **MySQL**. Use it once per environment (e.g. production). Ongoing deploys are covered in [DEPLOYMENT.md](DEPLOYMENT.md).

**What you get:**

| Piece | Where it runs | Repo path |
|-------|----------------|-----------|
| Marketing + booking SPA (React/Vite) | GitHub Pages | Repo root (`src/`, `vite.config.ts`) |
| JSON API, OAuth, mail, admin PHP | Your host (e.g. cPanel) | `api/` |
| App database | cPanel MySQL | Created from `api/sql/schema.sql` |

**Typical URLs (adjust to your domain and paths):**

- **Site:** `https://<github-user>.github.io/<repo-name>/`
- **API base:** `https://tekmaxhosting.com/changehair-api` (no trailing slash in env vars)

---

## Prerequisites

- GitHub repository with this code (push access).
- cPanel (or equivalent) on **tekmaxhosting.com** (or your host): **SSH access**, **MySQL**, **PHP 8+** with extensions **`pdo_mysql`**, **`mbstring`**, **`openssl`** (and whatever your host recommends).
- **Composer** available on the server (SSH terminal or cPanel “Terminal”) for `composer install` in `api/`.
- A **Google Cloud** OAuth client if you use “Sign in with Google” (optional but common).

---

## Part A — MySQL database (one time)

Do this **before** or **in parallel** with uploading the API; the API needs `DB_*` in `.env`.

### A1. Create database and user (cPanel)

1. Log into **cPanel** → **MySQL® Databases** (or **MySQL Database Wizard**).
2. Create a **new database** (note the full name, often `username_dbname`).
3. Create a **new MySQL user** with a strong password.
4. **Add user to database** with **ALL PRIVILEGES**.
5. Note for `.env`:
   - **`DB_HOST`** — usually **`localhost`** on shared hosting (use what cPanel shows).
   - **`DB_NAME`**, **`DB_USER`**, **`DB_PASS`** — exactly as cPanel lists them.

### A2. Import schema (creates tables)

The application expects tables defined in **`api/sql/schema.sql`**.

**Option 1 — phpMyAdmin (easiest)**

1. cPanel → **phpMyAdmin**.
2. Select your **database** in the left sidebar.
3. **Import** → choose **`schema.sql`** from your repo (`api/sql/schema.sql`) → Go.
4. Confirm tables appear (e.g. `users`, `bookings`, …).

**Option 2 — SSH**

```bash
mysql -u YOUR_DB_USER -p -h localhost YOUR_DB_NAME < /path/to/api/sql/schema.sql
```

Enter password when prompted.

**After import:** You do **not** need to re-import `schema.sql` on every deploy. New columns on **existing** databases are applied by **`api/config/schema_auto_migrate.php`** when the PHP app connects (additive changes only). For a **brand-new** empty database, `schema.sql` is the source of truth.

### A3. Optional seed / demo data

There is **no** bundled production seed file. If you need rows beyond an empty schema (e.g. test users), add them manually in phpMyAdmin or via a **separate** SQL file you maintain—never commit real customer data.

First **merchant admin** can be created via **`ADMIN_NAME`**, **`ADMIN_EMAIL`**, **`ADMIN_INITIAL_PASSWORD`** in **`api/.env`** when no admin exists yet (see `api/config/schema_auto_migrate.php` and `api/README.md`).

---

## Part B — API files and Apache document root

The PHP app’s web root must be the **`public/`** folder inside wherever you install **`api/`**.

### B1. Directory layout on the server

After deployment, you should have something like:

```text
/home/USERNAME/.../changehair-api/    ← parent folder name is yours
  composer.json
  config/
  public/          ← Apache DocumentRoot must point HERE
    index.php (if any)
    api/
    google-oauth-callback.php
    ...
  vendor/          ← created by composer install
  .env             ← not in git; you create on server
```

**Important:** The URL `https://tekmaxhosting.com/changehair-api/` must map so that **`public/`** is the document root for that URL (or equivalent: subdomain whose docroot is `.../changehair-api/public`). Exact clicks vary by host (“Addon Domains”, “Subdomains”, or document root editor).

If document root points at **`changehair-api/`** instead of **`changehair-api/public/`**, PHP entrypoints and asset paths will break—fix this in cPanel before testing.

### B2. Install PHP dependencies (one time per server path)

SSH into the server, `cd` to the folder that contains **`api/`**’s contents (the directory that has `composer.json`), then:

```bash
composer install --no-dev --optimize-autoloader
```

Repeat after major Composer changes if you don’t automate it in CI.

---

## Part C — `api/.env` on the server (never commit)

1. Copy **`api/.env.example`** to **`api/.env`** on the server (create the file in the same directory as `composer.json`).
2. Fill at least:

| Variable | Notes |
|----------|--------|
| **`DB_HOST`**, **`DB_NAME`**, **`DB_USER`**, **`DB_PASS`** | From Part A. |
| **`ALLOWED_ORIGINS`** | Comma-separated. Include your **GitHub Pages** origin, e.g. `https://youruser.github.io` and if needed `https://youruser.github.io/your-repo-name`. |
| **`CHB_SESSION_CROSS_SITE`** | Use **`1`** when the SPA is on **github.io** and the API is on **tekmaxhosting.com** (cross-site cookies). |
| **`CHB_APP_PUBLIC_URL`** | Your **SPA** URL, **no** trailing slash, e.g. `https://youruser.github.io/changehair-beauty` — used for email links and OAuth return. |
| **`GOOGLE_CLIENT_ID`** / **`GOOGLE_CLIENT_SECRET`** | If using Google sign-in. |
| **`GOOGLE_REDIRECT_URI`** | Must match **exactly** what Google sees, e.g. `https://tekmaxhosting.com/changehair-api/google-oauth-callback.php` (adjust path to your real API base). |
| **SMTP** (`SMTP_*`, `CONTACT_MAIL_*`) | For contact form and mail; set per host instructions. |

See **`api/.env.example`** for CardConnect, webhooks, admin bootstrap, etc.

Restart PHP if your host caches opcode (or touch `.env` and wait)—some panels need “Restart PHP” or a few minutes.

---

## Part D — Google OAuth (if used)

1. [Google Cloud Console](https://console.cloud.google.com/apis/credentials) → **APIs & Services** → **Credentials** → **Create credentials** → **OAuth client ID** → **Web application**.
2. **Authorized redirect URIs** — add **exactly** (no wrong slash):
   - `https://tekmaxhosting.com/changehair-api/google-oauth-callback.php`  
   (or your real API callback URL.)
3. **Authorized JavaScript origins** can include your GitHub Pages URL if required by your flow.
4. Copy **Client ID** and **Client Secret** into **`api/.env`**.
5. Publish the **OAuth consent screen** (External + test users, or In production).

---

## Part E — GitHub repository configuration

### E1. Branch and workflows

Workflows are configured for **`main`** by default:

- **`.github/workflows/deploy.yml`** — builds the SPA and deploys to **GitHub Pages**.
- **`.github/workflows/deploy-api.yml`** — rsyncs **`api/`** to your server over **SSH**.

Merge or push to **`main`** so these run (or duplicate the branch list in the YAML if you deploy from another branch).

### E2. Enable GitHub Pages

1. Repo → **Settings** → **Pages**.
2. **Build and deployment** → **Source: GitHub Actions**.

### E3. Repository secrets (Settings → Secrets and variables → Actions)

**Frontend build (`deploy.yml`):**

| Secret | Purpose |
|--------|---------|
| **`VITE_API_URL`** | Public API base URL, **no trailing slash**, e.g. `https://tekmaxhosting.com/changehair-api` |

**API deploy (`deploy-api.yml`):**

| Secret | Purpose |
|--------|---------|
| **`CPANEL_SSH_HOST`** | SSH hostname (often your domain or a server hostname from the provider). |
| **`CPANEL_SSH_USER`** | SSH username. |
| **`CPANEL_SSH_KEY`** | **Private** SSH key (full PEM). Use a **deploy key** or dedicated user with minimal access. Paste carefully—newlines matter. |
| **`CPANEL_REMOTE_PATH`** | Absolute path on the server where the **`api/`** folder contents should sync (the directory that will contain `public/`, `config/`, `composer.json`). Example: `/home/USERNAME/public_html/changehair-api` — **must match** your real layout. |

The workflow **excludes** `sql/`, `.env`, and `.dockerignore` from rsync—so **secrets and SQL dumps are not overwritten** from the repo.

### E4. SSH key setup (quick)

1. On your machine: `ssh-keygen -t ed25519 -f ./deploy_key -N ""` (or use an existing deploy key).
2. Add **public** key to cPanel → **SSH Access** → **Manage SSH Keys** → **Authorize**.
3. Put **private** key in **`CPANEL_SSH_KEY`**.

Test: `ssh -i deploy_key USER@HOST` then list `CPANEL_REMOTE_PATH`.

---

## Part F — First deploy sequence

Recommended order:

1. **MySQL:** Part A done (database + `schema.sql` import).
2. **Server:** Part B (correct document root, `composer install`, **`api/.env`** with `DB_*` and URLs).
3. **Smoke test API in browser:**  
   `https://tekmaxhosting.com/changehair-api/status.php` (or your equivalent) should return a simple “up” message if present.
4. **GitHub secrets:** Part E3 filled in.
5. **Push to `main`** (or run **Actions → workflow dispatch** for `deploy-api` and Pages workflows).
6. **After API deploy:** if `vendor/` was missing, SSH once and run **`composer install --no-dev`** again in the deploy path.
7. **Frontend:** ensure **`VITE_API_URL`** matches your live API URL; Pages workflow should build and publish **`dist/`**.

---

## Part G — Verify end-to-end

- [ ] SPA loads from GitHub Pages URL.
- [ ] Browser devtools: API calls go to `VITE_API_URL` (no mixed-content errors on HTTPS).
- [ ] Login / session works (`ALLOWED_ORIGINS` + `CHB_SESSION_CROSS_SITE`).
- [ ] Google OAuth (if enabled) completes without `redirect_uri_mismatch`.
- [ ] Contact form / booking flow (SMTP configured).

---

## Part H — Local development (optional)

For a full stack on your laptop: **`docker compose`** from the repo root and **`docker/README.md`**. Copy **`api/.env.example`** → **`api/.env`** and root **`.env.example`** → **`.env`** for Vite.

---

## Troubleshooting

| Symptom | Things to check |
|---------|------------------|
| **502 / blank PHP** | Document root = **`api/public`**, PHP version ≥ 8, `vendor/` exists (`composer install`). |
| **DB connection error** | `DB_*` in **`api/.env`**, MySQL user granted on DB, host `localhost`. |
| **CORS / cookies** | `ALLOWED_ORIGINS`, `CHB_SESSION_CROSS_SITE=1`, HTTPS on API. |
| **OAuth redirect mismatch** | `GOOGLE_REDIRECT_URI` matches Google Console **exactly**; callback URL reachable. |
| **Pages SPA 404 on refresh** | GitHub Pages + SPA: workflow copies `index.html` → `404.html` (already in `deploy.yml`). |
| **Workflow never runs** | Pushes on **`main`**? For API workflow, last commit must touch **`api/**`** unless you use **workflow_dispatch**. |

---

## Related docs

- [DEPLOYMENT.md](DEPLOYMENT.md) — ongoing deploy targets and env map.
- [docker/README.md](../docker/README.md) — local Docker stack.
- [api/README.md](../api/README.md) — PHP app behavior, admin bootstrap, Docker notes.
