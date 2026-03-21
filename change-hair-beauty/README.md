# Change Hair Beauty — local PHP + MySQL (Docker)

Salon demo app: signup/login with hashed passwords, sessions, and appointments with **one booking per date+time** (DB unique key + server checks). No heavy frameworks.

The **marketing layout** is aligned with the public site **[changehair-beauty on GitHub Pages](https://tekmaxusa.github.io/changehair-beauty/)** and repo **[tekmaxusa/changehair-beauty](https://github.com/tekmaxusa/changehair-beauty)** (colors `#fdfbf7` / `#f5f2ed` / `#c5a059`, Playfair + Inter, hero slides, story, signature services, menu, gallery, testimonials, contact/footer). Copy and images are driven from `config/salon_data.php` and `public/assets/`. **Booking** opens in a **modal** (iframe to **`/book-appointment.php`**) from “Book appointment” links; guests see **log in**, **sign up**, and **Continue with Google** there before submitting. **`/book.php`** redirects to **`/?open=booking`** so the modal opens on the home page. You can still open **`/book-appointment.php`** directly (full page). Rows are stored in MySQL as **pending** until a merchant **confirms** or **cancels** in **`/admin/`**. The **client dashboard** (`/dashboard/`) is **read-only** and lists status, date, time, and services. Confirm/cancel triggers a plain-text **email to the client** (when `mail()` / `CONTACT_MAIL_TO` is configured).

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

   This starts **web** (PHP app), **MySQL** (`db`), and **phpMyAdmin** — one shared database.

3. **Open the app**: [http://localhost:8080](http://localhost:8080) (not port 80 — other tools like Postgres may show a different page there).  
   - Plain check: [http://localhost:8080/status.php](http://localhost:8080/status.php) → `server is up and running.`  
   - Sign up → log in → **Dashboard** lists your bookings and lets you book 30-minute slots (9:00–17:00).  
   - **Merchant dashboard (same Docker `web` container):** [http://localhost:8080/admin/login.php](http://localhost:8080/admin/login.php) — staff **Sign up** / **Sign in**, then [http://localhost:8080/admin/](http://localhost:8080/admin/), bookings, and users. Use `ADMIN_SETUP_CODE` in `.env` on public servers.

4. **Database in the browser (Docker phpMyAdmin)**: [http://localhost:8081](http://localhost:8081) (default `PMA_PORT`; change in `.env` if needed).  
   - Login: **`salon_user`** / **`salon_secret`** (or **`root`** / **`DB_ROOT_PASSWORD`**).  
   - Server field: **`db`** (pre-filled) — same MySQL instance the app uses inside Docker.

**Port 3306 in use?** MySQL from Compose maps to host **`3307`** by default (`DB_PORT` in `.env`). Change it if that port is taken too.

### `ERR_CONNECTION_REFUSED` on localhost

This means **nothing is listening on the port you opened** (or Docker is not running).

1. **Open Docker Desktop** (Windows) and wait until the engine is “running” (whale icon in the system tray).
2. In the **`change-hair-beauty`** folder:

   ```bash
   docker compose up -d
   ```

3. **Correct URLs / ports:**

   | Goal | URL / settings |
   |---------|----------------|
   | Website (Docker) | **http://localhost:8080** — *not* `http://localhost` (80) unless XAMPP owns that port |
   | phpMyAdmin (Docker) | **http://localhost:8081** |
   | MySQL from the host (XAMPP, HeidiSQL, etc.) | Host **127.0.0.1**, port **3307**, user **`salon_user`**, password from `.env` |

4. **Check that containers are running:**

   ```bash
   docker compose ps
   ```

   `chb-web`, `chb-mysql`, and (if you use the browser DB UI) `chb-phpmyadmin` should be **Up**. If phpMyAdmin is missing, run: `docker compose up phpmyadmin -d`.

5. **PowerShell helper:** `.\scripts\check-docker.ps1` — prints `docker compose ps` and the expected URLs.

**XAMPP: “MySQL shutdown unexpectedly”?** Usually **port 3306 is already in use** (e.g. another Docker MySQL with `3306:3306`). See **`docs/XAMPP-MYSQL-TROUBLESHOOTING.md`** and run **`.\scripts\check-mysql-3306.ps1`**.

If **XAMPP** serves the site but **Docker** runs MySQL, you still need **`docker compose up db -d`** for `127.0.0.1:3307` to work.

### Docker (PHP/Apache) + MySQL on XAMPP

**Fastest path — one SQL import + Docker phpMyAdmin:** read **[`docs/XAMPP-DOCKER-DATABASE.md`](docs/XAMPP-DOCKER-DATABASE.md)**. Import **`sql/xampp_complete_setup.sql`** in XAMPP, then run `.\scripts\setup-xampp-docker-database.ps1` or `docker compose -f docker-compose.xampp-phpmyadmin.yml up -d` → **http://localhost:8082** (`salon_user` / `salon_secret`).

---

If the **database lives on XAMPP** (port **3306**) and only the **app** runs in Docker (`chb-web`):

1. **Start XAMPP** → **MySQL** (do not run Docker `db` on the same port — stop `chb-mysql` if you previously ran full `docker compose up -d`).
2. Open **XAMPP phpMyAdmin** → **SQL** or **Import** tab:
   - **Recommended:** import **`sql/xampp_complete_setup.sql`** as **root** — includes `change_hair_beauty` + `tekmax_app` + `salon_user`.
   - **Manual:** **`sql/xampp_db_user.sql`** → select **`change_hair_beauty`** → import **`sql/schema.sql`** → optional **`sql/tekmax_app_full.sql`**.
3. Start **web** only (no MySQL container):

   ```bash
   docker compose -f docker-compose.yml -f docker-compose.xampp-mysql.yml up -d --build --no-deps web
   ```

   PowerShell: **`.\scripts\start-web-with-xampp-mysql.ps1`**

The override sets **`DB_HOST=host.docker.internal`** and **`DB_PORT=3306`** so the container reaches MySQL on the Windows host. See **`.env.xampp-mysql.example`** for sample `DB_USER` / `DB_PASS`.

**Cannot connect?** In XAMPP `my.ini` / `my.cnf`, try **`bind-address=0.0.0.0`** (dev only; do not expose to the internet). Restart MySQL.

#### Docker phpMyAdmin → same databases as XAMPP

After importing into XAMPP (including `tekmax_app` if you want that layout), start the standalone phpMyAdmin container:

```bash
docker compose -f docker-compose.xampp-phpmyadmin.yml up -d
```

PowerShell: **`.\scripts\start-phpmyadmin-xampp.ps1`**

Open **[http://localhost:8082](http://localhost:8082)** (or **`PMA_XAMPP_PORT`** from `.env`). Log in as **`salon_user`** / **`salon_secret`**. In the left sidebar you’ll see **`tekmax_app`** and **`change_hair_beauty`** — open **`tekmax_app`** → **`users`** to browse rows like the reference UI.

*This is separate from phpMyAdmin on `:8081`, which talks to the Docker `db` container.*

### XAMPP (PHP/Apache on the PC) + MySQL in Docker

Yes: **MySQL runs in Docker**, the **site runs under XAMPP** on `localhost`. **One dataset** is visible in Docker phpMyAdmin, XAMPP PHP, and the `chb-web` container (all use the same MySQL data).

**Files in this repo:**

| File | Purpose |
|------|--------|
| **`.env.xampp.example`** | Copy to **`.env`** (`copy .env.xampp.example .env`) — `DB_HOST=127.0.0.1`, `DB_PORT=3307` (host → mapped Docker MySQL) |
| **`scripts/start-docker-db.ps1`** | PowerShell: `.\scripts\start-docker-db.ps1` → `docker compose up db phpmyadmin -d` |
| **`scripts/check-docker.ps1`** | PowerShell: `.\scripts\check-docker.ps1` → `docker compose ps` + URLs (troubleshoot **ERR_CONNECTION_REFUSED**) |
| **`scripts/start-docker-db.sh`** | macOS/Linux: `sh scripts/start-docker-db.sh` |
| **`docker/xampp-vhost.conf.example`** | Sample Apache `VirtualHost` → `DocumentRoot` at `public/` |

1. In the `change-hair-beauty` folder:

   ```bash
   copy .env.xampp.example .env
   docker compose up db phpmyadmin -d
   ```

   (or use a script from `scripts/`.)

2. **View the DB in Docker:** open [http://127.0.0.1:8081](http://127.0.0.1:8081) (or `PMA_PORT` from `.env`). Login: `salon_user` / `salon_secret`.

3. **XAMPP phpMyAdmin (optional):** to use XAMPP’s built-in phpMyAdmin, connect to host `127.0.0.1`, port `3307`, same user/password — that is the published Docker MySQL port. (If the login UI has no port field, edit XAMPP `config.inc.php` to add a second server, or use Docker phpMyAdmin on `:8081`.)

4. In XAMPP: **DocumentRoot** = `change-hair-beauty/public` (see `docker/xampp-vhost.conf.example`). Enable **`pdo_mysql`** in `php.ini`.

5. Open the site at your XAMPP URL.  
   **OAuth redirect:** add the exact URI in Google Console (e.g. `http://changehair.local/google-oauth-callback.php`).

Do not run XAMPP **MySQL** if you want only one MySQL (Docker) — or leave XAMPP MySQL on `3306` and Docker on `3307` to avoid conflicts.

**Already ran Docker before (old DB)?** On first DB connection the app runs **`config/schema_auto_migrate.php`** and adds missing columns (`users.role`, `bookings.status`, `service_category`, `service_name`, `google_sub`, nullable `password`) and drops the old **unique (date, time)** index if present so **cancelled** slots can be re-booked. Requires `ALTER` privilege (default Docker user has it).

### Merchant admin (`/admin/`)

- Admins are normal rows in **`users`** with **`role = 'admin'`** (same table as clients; clients default to **`client`**).
- **Merchant sign up / sign in:** **`/admin/login.php`** always has **Sign up** (new admin user) and **Sign in**. Successful sign-up creates `role = admin` and logs you into **`/admin/`**. You can still bootstrap the first admin silently with **`ADMIN_EMAIL`** + **`ADMIN_INITIAL_PASSWORD`** on deploy (see `config/schema_auto_migrate.php`).
- **Recommended on the public internet:** set **`ADMIN_SETUP_CODE`** in `.env` so every merchant sign-up must enter that code (otherwise anyone who finds `/admin/login.php` can create admins).
- Sign in at **`/admin/login.php`**. **`/admin/`** = dashboard summary; **`/admin/bookings.php`** = all bookings (confirm/cancel); **`/admin/users.php`** = all **client** accounts + merchant admins + create users. **`/logout.php`** ends the session.

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
| `docker-compose.yml` | PHP-Apache + MySQL + phpMyAdmin; MySQL host port `DB_PORT` (default 3307) for XAMPP/host tools |
| `docker-compose.xampp-mysql.yml` | Merge override: `web` → XAMPP MySQL (`host.docker.internal:3306`); use with `docker-compose.yml` + `--no-deps web` |
| `sql/xampp_complete_setup.sql` | **Single import** (XAMPP root): `change_hair_beauty` + `tekmax_app` + `salon_user` — same data visible in Docker phpMyAdmin on `:8082` |
| `docs/XAMPP-DOCKER-DATABASE.md` | Step-by-step: XAMPP → import → Docker |
| `scripts/setup-xampp-docker-database.ps1` | Starts Docker phpMyAdmin and prints the URL |
| `sql/xampp_db_user.sql` | Creates DB + `salon_user`@`%` for connections from Docker to XAMPP |
| `sql/tekmax_app_full.sql` | Demo DB **`tekmax_app`** (`users` + `customers` + `bookings`) — Tekmax-style layout; import after `xampp_db_user.sql` |
| `docker-compose.xampp-phpmyadmin.yml` | Standalone phpMyAdmin → **XAMPP** MySQL (`host.docker.internal`), default host port **8082** |
| `scripts/start-web-with-xampp-mysql.ps1` | PowerShell: start `web` with XAMPP MySQL override |
| `scripts/start-phpmyadmin-xampp.ps1` | PowerShell: start Docker phpMyAdmin for XAMPP DB |
| `Dockerfile` | `php:8.2-apache` + `pdo_mysql` |
| `docker/apache/000-default.conf` | `DocumentRoot` → `/var/www/html/public` |
| `sql/schema.sql` | Tables: `users.role`, `bookings.status` (no unique on slot — enforced in app for pending/confirmed) |
| `config/database.php` | PDO factory (`ATTR_EMULATE_PREPARES` off) |
| `config/session.php` | Session bootstrap, `require_login()` |
| `auth/signup.php` | `register_user()` — `password_hash`, prepared INSERT |
| `auth/login.php` | `login_user()` / `logout_user()` — `password_verify`, `session_regenerate_id`, `user_role` |
| `auth/admin_auth.php` | `login_admin_user()`, `require_admin()`, `chb_create_merchant_from_login_signup()` |
| `booking/booking.php` | `create_booking` (pending), `admin_set_booking_status`, slot checks |
| `public/` | `index.php` (home + booking modal), marketing pages, `book-appointment.php`, `book.php` → `/?open=booking`, `login.php`, `signup.php`, `dashboard/` (clients), **`admin/`** (merchant: `login.php`, `index.php`, `bookings.php`, `users.php`), `contact-send.php` |
| `config/contact_mail.php` | `mail()` helpers (contact + booking) |
| `config/google_script_notify.php` | POST to Google Apps Script web app (reference parity) |
| `config/salon_notify.php` | Orchestrates mail + Script for contact and bookings |

## Environment variables

Defined in `.env` / `docker-compose.yml`:

- `WEB_PORT` — host port for Apache (default `8080`)
- `PMA_PORT` — host port for Docker **phpMyAdmin** (default `8081`)
- `DB_HOST` — inside the Docker web container the default is `db`; for XAMPP PHP + Docker MySQL use `127.0.0.1`; for **Docker web + XAMPP MySQL** the override is `host.docker.internal` (see `docker-compose.xampp-mysql.yml`)
- `DB_PORT` — optional; required from the host (e.g. XAMPP PHP → Docker MySQL on `3307`); for Docker web + XAMPP MySQL the override is `3306`
- `DB_NAME`, `DB_USER`, `DB_PASS` — app database credentials
- `DB_ROOT_PASSWORD` — MySQL root (change for anything beyond local dev)

The `web` service receives `DB_HOST=db`, `DB_NAME`, `DB_USER`, `DB_PASS` automatically (no `DB_PORT` in the container — default `3306` on the Compose network).

- `CONTACT_MAIL_TO` — optional; overrides salon `contact_email` for the contact form
- `CONTACT_MAIL_FROM` — optional; **From** header for `mail()` (use a domain your provider accepts)
- `CHB_GOOGLE_SCRIPT_URL` — optional; Google Apps Script **Web app** URL (same as reference `VITE_GOOGLE_SCRIPT_URL`) for contact + booking notifications
- `ADMIN_EMAIL`, `ADMIN_INITIAL_PASSWORD` — optional; create/promote first **admin** when none exists (DB bootstrap; see Merchant admin above)
- `ADMIN_SETUP_CODE` — optional; if set, **Sign up** on `/admin/login.php` requires this code for each new merchant

## Security notes (implemented)

- Passwords: `password_hash()` / `password_verify()`
- Queries: PDO prepared statements, emulated prepares disabled
- Sessions: strict mode, HTTP-only cookies, SameSite=Lax, regenerate ID on login
- Slots: `UNIQUE (booking_date, booking_time)` + check before insert + user feedback for taken slots

## Reference repos

The [tekmaxwebsite](https://github.com/tekmaxusa/tekmaxwebsite) repo was not reachable from this environment; auth follows common secure PHP session patterns. [tigerleetkd](https://github.com/qiangcui/tigerleetkd) is a React/Vite marketing site (schedule PDF, etc.); this app implements **server-side** slot booking with MySQL instead.
