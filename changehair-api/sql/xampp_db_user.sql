-- Run in XAMPP phpMyAdmin as root (or import this file).
-- Creates database + user so Docker can connect via host.docker.internal.
-- Easiest: use `xampp_complete_setup.sql` once (includes schema + tekmax_app).
-- Manual path: after this → import `schema.sql` into `change_hair_beauty`; optional → `tekmax_app_full.sql`.

CREATE DATABASE IF NOT EXISTS change_hair_beauty
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

-- '%' allows connections from other hosts (including Docker on Windows → host)
-- On older MySQL without IF NOT EXISTS, remove IF NOT EXISTS or run:
-- CREATE USER 'salon_user'@'%' IDENTIFIED BY 'salon_secret';
CREATE USER IF NOT EXISTS 'salon_user'@'%' IDENTIFIED BY 'salon_secret';

GRANT ALL PRIVILEGES ON change_hair_beauty.* TO 'salon_user'@'%';

FLUSH PRIVILEGES;
