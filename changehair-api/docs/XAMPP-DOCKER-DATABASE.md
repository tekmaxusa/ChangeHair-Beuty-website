# Database on XAMPP — visible in Docker

**MySQL runs in XAMPP** on your PC. **Docker** only runs **phpMyAdmin**, which connects to that same MySQL, so you see **one database** in both XAMPP phpMyAdmin and the browser on `:8082`.

## Steps

### 1. XAMPP MySQL

- Open **XAMPP Control Panel** → **Start** **MySQL**.
- Make sure **nothing else** is using port **3306** (e.g. another Docker MySQL with `3306:3306`). Check: `.\scripts\check-mysql-3306.ps1`.

### 2. Single import (create databases and tables)

- Open **XAMPP phpMyAdmin** (usually `http://localhost/phpmyadmin`).
- Log in as **root** (default XAMPP: often **no password**).
- **Import** → choose **`sql/xampp_complete_setup.sql`** from this repo → **Go**.

You get:

| Database | Contents |
|----------|----------|
| **change_hair_beauty** | Salon app: `users`, `bookings` |
| **tekmax_app** | Demo: `users`, `customers`, `bookings` + sample data |

App / Docker phpMyAdmin login: **`salon_user`** / **`salon_secret`**.

### 3. Docker phpMyAdmin (see the same DBs)

In a terminal, in the **`changehair-api`** folder:

```powershell
docker compose -f docker-compose.xampp-phpmyadmin.yml up -d
```

Or: **`.\scripts\start-phpmyadmin-xampp.ps1`**

Open: **http://localhost:8082**  
Login: **salon_user** / **salon_secret**  
In the sidebar: **change_hair_beauty** and **tekmax_app** — click a database and table to browse data.

### 4. (Optional) Salon site in Docker using XAMPP MySQL

```powershell
docker compose -f docker-compose.yml -f docker-compose.xampp-mysql.yml up -d --build --no-deps web
```

App: **http://localhost:8080** (connects to `host.docker.internal:3306`).

---

**Note:** phpMyAdmin at **http://localhost:8081** (full `docker compose up`) is for **MySQL in Docker** (`db`), not XAMPP. For XAMPP MySQL, use the **:8082** setup above.
