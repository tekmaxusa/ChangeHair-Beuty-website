# Change Hair Beauty — local PHP + MySQL (Docker)

Salon demo app: signup/login with hashed passwords, sessions, and appointments with **one booking per date+time** (DB unique key + server checks). No heavy frameworks.

The **marketing layout** is aligned with the public site **[changehair-beauty on GitHub Pages](https://tekmaxusa.github.io/changehair-beauty/)** and repo **[tekmaxusa/changehair-beauty](https://github.com/tekmaxusa/changehair-beauty)** (colors `#fdfbf7` / `#f5f2ed` / `#c5a059`, Playfair + Inter, hero slides, story, signature services, menu, gallery, testimonials, contact/footer). Copy and images are driven from `config/salon_data.php` and `public/assets/`. **Online booking** on this stack uses the **client dashboard** (MySQL) instead of the Vite app’s Google Script modal.

**Contact Us** matches the reference layout (name, email, phone, message, **Send Message**) and posts to **`/contact-send.php`**. Notifications use the same dual path as **[tekmaxusa/changehair-beauty](https://github.com/tekmaxusa/changehair-beauty)**:

1. **Google Apps Script** — if **`CHB_GOOGLE_SCRIPT_URL`** (or **`VITE_GOOGLE_SCRIPT_URL`** / **`CONTACT_GOOGLE_SCRIPT_URL`**) is set, the server POSTs **`application/x-www-form-urlencoded`** with `name`, `email`, `phone`, `service`, `date`, `time` (contact uses `service=Contact Request` and puts the message in `time`, like the Vite app).
2. **PHP `mail()`** — if **`CONTACT_MAIL_TO`** / **`contact_email`** is set, the salon also gets a plain-text email (`config/contact_mail.php`).

At least one channel must be configured or the contact form shows an error. **Dashboard bookings** call the same notifier after a successful DB insert so the salon gets an email and/or Script POST for each confirmed appointment.

CSRF, honeypot, and a short rate limit protect the contact form.

## Prerequisites

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) (or Docker Engine + Compose)

## Quick start

1. **Copy environment file** (optional — defaults work for local dev):

   ```bash
   cd change-hair-beauty
   copy .env.example .env
   ```

   On macOS/Linux: `cp .env.example .env`

2. **Build and start**:

   ```bash
   docker compose up --build -d
   ```

3. **Open the app**: [http://localhost:8080](http://localhost:8080) (not port 80 — other tools like Postgres may show a different page there).  
   - Plain check: [http://localhost:8080/status.php](http://localhost:8080/status.php) → `server is up and running.`  
   - Sign up → log in → **Dashboard** lists your bookings and lets you book 30-minute slots (9:00–17:00).

**Port 3306 in use?** MySQL from Compose maps to host **`3307`** by default (`DB_PORT` in `.env`). Change it if that port is taken too.

### XAMPP (PHP/Apache sa PC) + MySQL lang sa Docker

Oo — puwede: tumatakbo ang **database sa Docker**, ang **site naman sa XAMPP** sa `localhost`.

**Handang files sa repo:**

| File | Gamit |
|------|--------|
| **`.env.xampp.example`** | Kopyahin bilang **`.env`** (`copy .env.xampp.example .env`) — may `DB_HOST=127.0.0.1` at `DB_PORT=3307` na |
| **`scripts/start-docker-db.ps1`** | PowerShell: `.\scripts\start-docker-db.ps1` → `docker compose up db -d` |
| **`scripts/start-docker-db.sh`** | macOS/Linux: `sh scripts/start-docker-db.sh` |
| **`docker/xampp-vhost.conf.example`** | Halimbawa ng Apache `VirtualHost` → `DocumentRoot` sa `public/` |

1. Sa folder na `change-hair-beauty`:

   ```bash
   copy .env.xampp.example .env
   docker compose up db -d
   ```

   (o gamitin ang script sa `scripts/`.)

2. Sa XAMPP: **DocumentRoot** = `change-hair-beauty/public` (tingnan `docker/xampp-vhost.conf.example`). Naka-enable ang **`pdo_mysql`** sa `php.ini`.

3. Buksan ang site sa URL ng XAMPP.  
   **OAuth redirect:** idagdag ang eksaktong URI sa Google Console (hal. `http://changehair.local/google-oauth-callback.php`).

Huwag paganahin ang XAMPP **MySQL** kung gusto mo **isang** MySQL lang (Docker) — o iwanan ang XAMPP MySQL sa `3306` at Docker sa `3307` para walang bangga.

**Already ran Docker before (old DB)?** On first DB connection the app runs **`config/schema_auto_migrate.php`** and adds missing columns (`service_category`, `service_name`, `google_sub`, nullable `password`) if needed. Requires `ALTER` privilege (default Docker user has it).

You can still apply SQL manually if you prefer:

```bash
docker compose exec -T db mysql -u root -p"$env:DB_ROOT_PASSWORD" change_hair_beauty < sql/migrate_v2_booking_services_google.sql
```

(On PowerShell, set the password or run `mysql` inside the container interactively.) Or recreate the volume: `docker compose down -v` (deletes data).

**Google sign-in (“Continue with Google”):**  
The [Vite reference site](https://github.com/tekmaxusa/changehair-beauty) only opens `https://accounts.google.com/` in a new tab when OAuth isn’t wired. This PHP app does the **same** when `GOOGLE_CLIENT_ID` / `GOOGLE_CLIENT_SECRET` are missing; when they **are** set, the button uses **server OAuth** (`/google-oauth-start.php`) so users return logged in.

1. [Google Cloud Console](https://console.cloud.google.com/apis/credentials) → **Create credentials** → **OAuth client ID** → type **Web application**.  
2. Under **Authorized redirect URIs**, add **exactly** (no trailing slash on the path):  
   - Local: `http://localhost:8080/google-oauth-callback.php`  
   - Live: `https://YOUR-DOMAIN.com/google-oauth-callback.php`  
3. Put `GOOGLE_CLIENT_ID` and `GOOGLE_CLIENT_SECRET` in **`.env`** (loaded by `config/env.php`) or in Docker `environment:`.  
4. **Published site behind HTTPS** (Cloudflare, nginx TLS, etc.): the app uses `X-Forwarded-Proto` / `X-Forwarded-Host` when present. If Google still says **redirect_uri_mismatch**, set **`GOOGLE_REDIRECT_URI=https://YOUR-DOMAIN.com/google-oauth-callback.php`** explicitly in `.env`.  
5. OAuth consent screen must be configured (External + test users, or In production).

4. **Stop**:

   ```bash
   docker compose down
   ```

   To wipe the database volume as well: `docker compose down -v`

## Project layout

| Path | Role |
|------|------|
| `docker-compose.yml` | PHP-Apache + MySQL, env-based DB settings |
| `Dockerfile` | `php:8.2-apache` + `pdo_mysql` |
| `docker/apache/000-default.conf` | `DocumentRoot` → `/var/www/html/public` |
| `sql/schema.sql` | Tables + unique slot constraint (loaded on first DB init) |
| `config/database.php` | PDO factory (`ATTR_EMULATE_PREPARES` off) |
| `config/session.php` | Session bootstrap, `require_login()` |
| `auth/signup.php` | `register_user()` — `password_hash`, prepared INSERT |
| `auth/login.php` | `login_user()` / `logout_user()` — `password_verify`, `session_regenerate_id` |
| `booking/booking.php` | `create_booking`, `fetch_bookings_for_user`, `is_slot_taken` |
| `public/` | Web root: `index.php`, `login.php`, `signup.php`, `dashboard/`, `contact-send.php` |
| `config/contact_mail.php` | `mail()` helpers (contact + booking) |
| `config/google_script_notify.php` | POST to Google Apps Script web app (reference parity) |
| `config/salon_notify.php` | Orchestrates mail + Script for contact and bookings |

## Environment variables

Defined in `.env` / `docker-compose.yml`:

- `WEB_PORT` — host port for Apache (default `8080`)
- `DB_HOST` — sa loob ng Docker web container default ay `db`; sa XAMPP + Docker DB gamitin ang `127.0.0.1`
- `DB_PORT` — optional; kailangan mula sa host (hal. XAMPP) kapag MySQL naka-map sa `3307` (tingnan `docker-compose.yml`)
- `DB_NAME`, `DB_USER`, `DB_PASS` — app database credentials
- `DB_ROOT_PASSWORD` — MySQL root (change for anything beyond local dev)

The `web` service receives `DB_HOST=db`, `DB_NAME`, `DB_USER`, `DB_PASS` automatically (walang `DB_PORT` sa container — default `3306` sa network ng Compose).

- `CONTACT_MAIL_TO` — optional; overrides salon `contact_email` for the contact form
- `CONTACT_MAIL_FROM` — optional; **From** header for `mail()` (use a domain your provider accepts)
- `CHB_GOOGLE_SCRIPT_URL` — optional; Google Apps Script **Web app** URL (same as reference `VITE_GOOGLE_SCRIPT_URL`) for contact + booking notifications

## Security notes (implemented)

- Passwords: `password_hash()` / `password_verify()`
- Queries: PDO prepared statements, emulated prepares disabled
- Sessions: strict mode, HTTP-only cookies, SameSite=Lax, regenerate ID on login
- Slots: `UNIQUE (booking_date, booking_time)` + check before insert + user feedback for taken slots

## Reference repos

The [tekmaxwebsite](https://github.com/tekmaxusa/tekmaxwebsite) repo was not reachable from this environment; auth follows common secure PHP session patterns. [tigerleetkd](https://github.com/qiangcui/tigerleetkd) is a React/Vite marketing site (schedule PDF, etc.); this app implements **server-side** slot booking with MySQL instead.
