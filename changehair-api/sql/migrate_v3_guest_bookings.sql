-- Guest bookings: optional user_id + guest contact.
-- Safe to run multiple times: no-ops if guest_name already exists.
-- Example: docker exec -i chb-mysql mysql -usalon_user -psalon_secret change_hair_beauty < sql/migrate_v3_guest_bookings.sql
-- If DROP FOREIGN KEY fails, run: SHOW CREATE TABLE bookings; and fix the constraint name below.

DROP PROCEDURE IF EXISTS chb_apply_migrate_v3_guest_bookings;

DELIMITER $$

CREATE PROCEDURE chb_apply_migrate_v3_guest_bookings()
BEGIN
  DECLARE v_count INT DEFAULT 0;

  SELECT COUNT(*) INTO v_count
  FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'bookings'
    AND COLUMN_NAME = 'guest_name';

  IF v_count > 0 THEN
    SELECT 'migrate_v3: already applied (guest columns present). Nothing to do.' AS result;
  ELSE
    ALTER TABLE bookings DROP FOREIGN KEY fk_bookings_user;

    ALTER TABLE bookings
      MODIFY user_id INT UNSIGNED NULL,
      ADD COLUMN guest_name VARCHAR(255) NULL AFTER user_id,
      ADD COLUMN guest_email VARCHAR(255) NULL AFTER guest_name,
      ADD COLUMN guest_phone VARCHAR(64) NULL AFTER guest_email;

    ALTER TABLE bookings
      ADD CONSTRAINT fk_bookings_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE SET NULL;

    SELECT 'migrate_v3: applied successfully.' AS result;
  END IF;
END$$

DELIMITER ;

CALL chb_apply_migrate_v3_guest_bookings();

DROP PROCEDURE IF EXISTS chb_apply_migrate_v3_guest_bookings;
