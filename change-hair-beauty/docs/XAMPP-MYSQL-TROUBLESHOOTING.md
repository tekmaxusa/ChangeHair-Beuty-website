# XAMPP: “MySQL shutdown unexpectedly” (Windows)

This message almost always means one of: **port 3306 already in use**, **corrupt / incompatible data folder**, or **missing runtime (VC++ DLL)**.

## 1. Port 3306 — most common

XAMPP MySQL needs **3306** on your machine. If **another MySQL/MariaDB** (Docker, Laragon, a standalone install, another XAMPP copy) is already listening there, XAMPP may fail to start or exit immediately.

### Check (PowerShell)

```powershell
netstat -ano | findstr ":3306"
```

If you see `LISTENING`, note the last column (PID). Identify the process:

```powershell
tasklist /FI "PID eq YOUR_PID"
```

### Docker on your machine

Many projects **publish** `3306:3306`. For example, if a container like **`php_app_mysql`** is running, it holds **3306** on the host — **XAMPP MySQL cannot bind there**.

**Options (pick one):**

| Goal | Action |
|------|--------|
| You want **XAMPP MySQL** to run | Stop the container using 3306: `docker stop <container_name>` (e.g. `php_app_mysql`). Or change the other project’s `docker-compose` to `3309:3306` and restart. |
| You want **both** running | Change **XAMPP MySQL’s port** (see §2) *or* do not start XAMPP MySQL and use MySQL in Docker only. |

In this repo, **Change Hair Beauty** Docker MySQL defaults to host port **3307** (`DB_PORT`), so it should not clash with XAMPP **unless** you changed the mapping.

Helper script: `scripts/check-mysql-3306.ps1`.

## 2. Change XAMPP MySQL port (optional)

1. Stop MySQL in the XAMPP Control Panel.
2. Open `xampp/mysql/bin/my.ini` (sometimes `my.cnf`).
3. Find `port=3306` and change it e.g. to `port=3308`.
4. In **XAMPP Control Panel** → MySQL → **Config** → `my.ini` — ensure the port is consistent if there are duplicate sections.
5. Start MySQL again.

Point every app (phpMyAdmin, PDO) at the new port.

## 3. Corrupt data directory (after crash / upgrade)

If you are **sure** there is no port conflict but it still fails:

1. Stop MySQL.
2. Back up the entire folder: `xampp/mysql/data`.
3. Try restoring a known-good backup; or rename `data` and let XAMPP create a new data folder (old data is gone — back up first).
4. Official discussions: [Apache Friends / XAMPP forums](https://community.apachefriends.org/) — many threads on `ibdata1` / InnoDB recovery.

## 4. Missing DLL / Visual C++

Install the **Microsoft Visual C++ Redistributable** that matches your XAMPP build (x64). Check the error log via XAMPP’s **Logs** button — if you see `VCRUNTIME` / missing `.dll`, that’s the direction.

## 5. Antivirus / OneDrive

Rare but possible: temporarily exclude `xampp/mysql/data` from real-time scanning. **OneDrive** on Desktop — prefer installing XAMPP on a path that is not synced (e.g. `C:\xampp`) to avoid file locking.

---

**Summary:** With Docker + XAMPP, first check whether **`3306` is already LISTENING** — often **`docker stop`** on the other app’s MySQL container is the quickest fix.
