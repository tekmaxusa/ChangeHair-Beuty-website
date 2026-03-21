-- Run once on existing databases (volume already initialized with old schema).
-- docker compose exec db mysql -u root -p"$MYSQL_ROOT_PASSWORD" change_hair_beauty < sql/migrate_v2_booking_services_google.sql

ALTER TABLE users MODIFY password VARCHAR(255) NULL;
ALTER TABLE users ADD COLUMN google_sub VARCHAR(255) NULL UNIQUE AFTER password;

ALTER TABLE bookings ADD COLUMN service_category VARCHAR(64) NOT NULL DEFAULT '' AFTER user_id;
ALTER TABLE bookings ADD COLUMN service_name VARCHAR(255) NOT NULL DEFAULT '' AFTER service_category;
