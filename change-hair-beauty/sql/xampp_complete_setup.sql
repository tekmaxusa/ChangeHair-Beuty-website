-- =============================================================================
-- SINGLE IMPORT — XAMPP MySQL → also visible in Docker phpMyAdmin (:8082)
-- =============================================================================
-- 1. Start XAMPP → MySQL.
-- 2. Open http://localhost/phpmyadmin → log in as root (often no password).
-- 3. Tab "Import" → choose this file → Go.
-- 4. In change-hair-beauty folder: docker compose -f docker-compose.xampp-phpmyadmin.yml up -d
-- 5. Open http://localhost:8082 → login: salon_user / salon_secret → browse databases.
--
-- Creates: user `salon_user`, databases `change_hair_beauty` (salon app) + `tekmax_app` (demo).
-- Safe to run again: CREATE IF NOT EXISTS + INSERT IGNORE where applicable.
-- =============================================================================

CREATE DATABASE IF NOT EXISTS change_hair_beauty
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

CREATE USER IF NOT EXISTS 'salon_user'@'%' IDENTIFIED BY 'salon_secret';

GRANT ALL PRIVILEGES ON change_hair_beauty.* TO 'salon_user'@'%';

FLUSH PRIVILEGES;

USE change_hair_beauty;

CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    password VARCHAR(255) NULL,
    google_sub VARCHAR(255) NULL,
    role VARCHAR(32) NOT NULL DEFAULT 'client',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_users_email (email),
    UNIQUE KEY uq_users_google_sub (google_sub),
    KEY idx_users_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS bookings (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id INT UNSIGNED NOT NULL,
    service_category VARCHAR(191) NOT NULL DEFAULT '',
    service_name TEXT NOT NULL,
    booking_date DATE NOT NULL,
    booking_time TIME NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_bookings_slot (booking_date, booking_time),
    KEY idx_bookings_status (status),
    CONSTRAINT fk_bookings_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE DATABASE IF NOT EXISTS tekmax_app
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

GRANT ALL PRIVILEGES ON tekmax_app.* TO 'salon_user'@'%';
FLUSH PRIVILEGES;

USE tekmax_app;

CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role VARCHAR(32) NOT NULL DEFAULT 'client',
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_users_email (email),
    KEY idx_users_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS customers (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NULL,
    phone VARCHAR(64) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_customers_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS bookings (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id INT UNSIGNED NULL,
    customer_id INT UNSIGNED NULL,
    service_name VARCHAR(255) NOT NULL DEFAULT '',
    booking_date DATE NOT NULL,
    booking_time TIME NOT NULL,
    status VARCHAR(32) NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_bookings_date (booking_date, booking_time),
    KEY idx_bookings_user (user_id),
    KEY idx_bookings_customer (customer_id),
    CONSTRAINT fk_bookings_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE SET NULL,
    CONSTRAINT fk_bookings_customer FOREIGN KEY (customer_id) REFERENCES customers (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO users (id, name, email, password_hash, role, is_active, created_at) VALUES
(1, 'Test User', 'test51025@example.com', '$2y$12$W8WMfRGGeGn4U5C18tpFre/LEYWPOmHR/NA5ZdUd38diuiq9Y1Ad6', 'client', 1, '2025-03-20 08:00:00'),
(2, 'Sample Client A', 'client.a@example.com', '$2y$12$W8WMfRGGeGn4U5C18tpFre/LEYWPOmHR/NA5ZdUd38diuiq9Y1Ad6', 'client', 1, '2025-03-20 08:15:00'),
(3, 'Sample Client B', 'client.b@example.com', '$2y$12$W8WMfRGGeGn4U5C18tpFre/LEYWPOmHR/NA5ZdUd38diuiq9Y1Ad6', 'client', 1, '2025-03-20 08:30:00'),
(4, 'Sample Client C', 'client.c@example.com', '$2y$12$W8WMfRGGeGn4U5C18tpFre/LEYWPOmHR/NA5ZdUd38diuiq9Y1Ad6', 'client', 1, '2025-03-20 08:45:00'),
(5, 'Sample Client D', 'client.d@example.com', '$2y$12$W8WMfRGGeGn4U5C18tpFre/LEYWPOmHR/NA5ZdUd38diuiq9Y1Ad6', 'client', 1, '2025-03-20 09:00:00'),
(6, 'Sample Client E', 'client.e@example.com', '$2y$12$W8WMfRGGeGn4U5C18tpFre/LEYWPOmHR/NA5ZdUd38diuiq9Y1Ad6', 'client', 1, '2025-03-20 09:15:00'),
(7, 'Sample Client F', 'client.f@example.com', '$2y$12$W8WMfRGGeGn4U5C18tpFre/LEYWPOmHR/NA5ZdUd38diuiq9Y1Ad6', 'client', 1, '2025-03-20 09:30:00'),
(8, 'Admin User', 'admin@example.com', '$2y$12$W8WMfRGGeGn4U5C18tpFre/LEYWPOmHR/NA5ZdUd38diuiq9Y1Ad6', 'admin', 1, '2025-03-20 09:45:00'),
(9, 'Client One', 'client1@example.com', '$2y$12$W8WMfRGGeGn4U5C18tpFre/LEYWPOmHR/NA5ZdUd38diuiq9Y1Ad6', 'client', 1, '2025-03-20 10:00:00'),
(10, 'Client Two', 'client2@example.com', '$2y$12$W8WMfRGGeGn4U5C18tpFre/LEYWPOmHR/NA5ZdUd38diuiq9Y1Ad6', 'client', 1, '2025-03-20 10:15:00');

INSERT IGNORE INTO customers (id, name, email, phone, created_at) VALUES
(1, 'Walk-in Guest', 'walkin@example.com', '555-0100', '2025-03-20 11:00:00'),
(2, 'VIP Customer', 'vip@example.com', '555-0200', '2025-03-20 11:15:00');

INSERT IGNORE INTO bookings (id, user_id, customer_id, service_name, booking_date, booking_time, status, created_at) VALUES
(1, 1, NULL, 'Haircut & Style', '2025-03-22', '10:00:00', 'confirmed', '2025-03-20 12:00:00'),
(2, 9, NULL, 'Color treatment', '2025-03-23', '14:30:00', 'pending', '2025-03-20 12:05:00'),
(3, NULL, 1, 'Consultation', '2025-03-24', '09:00:00', 'pending', '2025-03-20 12:10:00');
