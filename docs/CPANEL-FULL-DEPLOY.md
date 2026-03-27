# Full stack on cPanel (SPA + PHP API, same site)

**This deployment — public UI:** `https://tekmaxhosting.com/bookings/changehair/` (API under `…/bookings/changehair/public/`).

Host the **Vite build** (`dist/`) and the **PHP API** (`api/`) under the **same hostname** so login and refresh cookies work reliably (first-party cookies, no GitHub Pages ↔ API cross-site issues).

Elsewhere in this doc, replace `https://YOURDOMAIN.com` with your real host if it differs.

---

## Target layout (example)

| Piece | Server path (example) | Public URL (example) |
|-------|------------------------|----------------------|
| React SPA (contents of `dist/`) | `public_html/bookings/changehair/` | `https://YOURDOMAIN.com/bookings/changehair/` |
| PHP API (`api/` project: `config/`, `public/`, `vendor/`, …) | `public_html/bookings/changehair/` **same folder** if you deploy both here, **or** a sibling folder | API base **no trailing slash**: `https://YOURDOMAIN.com/bookings/changehair` |

**Critical:** The web server must execute PHP from **`public/`** inside the API tree. If your document root is `public_html/bookings/changehair`, that folder should contain the API’s `public/` contents **or** the document root must be set to `.../changehair/public` (depends on host UI). If you rsync the whole `api/` repo into `changehair/`, URLs look like:

- `https://YOURDOMAIN.com/bookings/changehair/public/api/auth/me.php`

So your **`VITE_API_URL`** (and `CHB_APP_PUBLIC_URL`) must use the **same path prefix** the browser uses to reach `public/`.

**Simplest mental model:** pick one public URL for the API (where `public/api/` lives) and set `VITE_API_URL` to that base **without** a trailing slash.

---

## 1. Database (once)

1. cPanel → **MySQL** → create database + user, grant **ALL** on that database.
2. **phpMyAdmin** → Import **`api/sql/schema.sql`**.
3. Note `DB_HOST` (often `localhost`), `DB_NAME`, `DB_USER`, `DB_PASS`.

---

## 2. Deploy API files

- Upload/sync the **`api/`** tree (see `scripts/deploy-cpanel.sh` or rsync), excluding server-only rules (`sql/` can be excluded from sync; keep `.env` only on server).
- On the server: **`composer install --no-dev --optimize-autoloader`** inside the folder that contains `composer.json`.
- Create **`api/.env`** on the server from **`api/.env.example`** (see section **4** below).

---

## 3. Build and deploy the SPA

On your computer (repo root):

1. Copy **`.env.example`** → **`.env`** and set **same-origin** values (see **4**).
2. Run:

```bash
npm ci
npm run build
```

3. Upload everything inside **`dist/`** to the folder that serves the site (e.g. `public_html/bookings/changehair/`).  
   - If the API and SPA share one directory, only put **SPA static files** (`index.html`, `assets/`, etc.) where they won’t overwrite needed PHP `public/` files—often people use **separate** subfolders or subdomains. If unsure, use **two paths**: e.g. SPA at `.../app/` and API at `.../api-root/` with docroot for PHP on `api-root/public`.

---

## 4. Environment variables

### A) Repo root `.env` (used at **`npm run build` only**)

These are **compiled into** the JS bundle. Rebuild and re-upload `dist/` after any change.

| Variable | Same-origin cPanel example |
|----------|----------------------------|
| `VITE_API_URL` | `https://YOURDOMAIN.com/bookings/changehair` — must match the URL prefix where **`public/api/`** is reachable (no trailing slash). |
| `VITE_BASE` | If the app is not at domain root, set the path, e.g. `/bookings/changehair/`. |

### B) Server **`api/.env`** (PHP; not in git)

| Variable | Same-origin cPanel |
|----------|---------------------|
| `DB_*` | From cPanel MySQL. |
| `ALLOWED_ORIGINS` | Include your SPA origin, e.g. `https://YOURDOMAIN.com` (comma-separated if multiple). |
| `CHB_SESSION_CROSS_SITE` | **`0`** when SPA and API are the **same site** (not GitHub Pages + API). |
| `CHB_TOKEN_SECRET` | Long random string (see **5**). |
| `CHB_APP_PUBLIC_URL` | Public URL of the **React app**, no trailing slash, e.g. `https://YOURDOMAIN.com/bookings/changehair` |
| `GOOGLE_*`, `SMTP_*`, CardConnect | As in `api/.env.example`. |

After editing `api/.env`, some hosts need a PHP restart or a few minutes for opcode cache.

---

## 5. Token auth (not “JWT library”, but signed tokens)

The app uses **short-lived access tokens** (HMAC-signed, Bearer header) and a **refresh token** stored in an HttpOnly cookie (`CHBRT`) plus DB.

- Set **`CHB_TOKEN_SECRET`** in **`api/.env`** to a long random value (e.g. 32+ bytes hex).  
- Do **not** commit it. If you rotate it, all existing access tokens become invalid until users log in again (refresh tokens may still work depending on implementation).

This is **not** a third-party JWT package; signing is implemented in `api/config/token_auth.php`.

---

## 6. Verify

1. Open `https://YOURDOMAIN.com/.../public/status.php` (adjust path) — DB connectivity.
2. Load the SPA, **log in**, **refresh** — you should stay logged in.
3. Google OAuth: **`GOOGLE_REDIRECT_URI`** and Google Console must match the live **`google-oauth-callback.php`** URL exactly.

---

## 7. Scripts reference

- **`scripts/deploy-cpanel.sh`** — rsync `dist/` and/or `api/` (see script header for env vars).
- **`scripts/push-and-deploy-api.sh`** — `git push` then API deploy (optional).

---

## Related docs

- [INITIAL-SETUP.md](INITIAL-SETUP.md) — broader first-time checklist.  
- [DEPLOYMENT.md](DEPLOYMENT.md) — GitHub Pages + split API overview.
