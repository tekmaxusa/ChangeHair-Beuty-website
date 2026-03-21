<?php

declare(strict_types=1);

function chb_migrate_drop_booking_slot_unique_if_exists(PDO $pdo, string $dbName): void
{
    $st = $pdo->prepare(
        'SELECT 1 FROM information_schema.STATISTICS
         WHERE TABLE_SCHEMA = :s AND TABLE_NAME = \'bookings\' AND INDEX_NAME = \'uq_booking_slot\' LIMIT 1'
    );
    $st->execute([':s' => $dbName]);
    if ($st->fetchColumn()) {
        $pdo->exec('ALTER TABLE bookings DROP INDEX uq_booking_slot');
    }

    $st2 = $pdo->prepare(
        'SELECT 1 FROM information_schema.STATISTICS
         WHERE TABLE_SCHEMA = :s AND TABLE_NAME = \'bookings\' AND INDEX_NAME = \'idx_bookings_slot\' LIMIT 1'
    );
    $st2->execute([':s' => $dbName]);
    if (!$st2->fetchColumn()) {
        $pdo->exec('ALTER TABLE bookings ADD KEY idx_bookings_slot (booking_date, booking_time)');
    }
}

/**
 * First admin from .env when no admin exists (see ADMIN_EMAIL / ADMIN_INITIAL_PASSWORD).
 */
function chb_ensure_admin_user_from_env(PDO $pdo): void
{
    $n = (int) $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'admin'")->fetchColumn();
    if ($n > 0) {
        return;
    }

    $em = getenv('ADMIN_EMAIL');
    $pw = getenv('ADMIN_INITIAL_PASSWORD');
    if (!is_string($em) || trim($em) === '' || !is_string($pw) || $pw === '') {
        return;
    }

    $em = strtolower(trim($em));
    if (!filter_var($em, FILTER_VALIDATE_EMAIL)) {
        return;
    }

    $hash = password_hash($pw, PASSWORD_DEFAULT);
    if ($hash === false) {
        return;
    }

    $ex = $pdo->prepare('SELECT id FROM users WHERE email = :e LIMIT 1');
    $ex->execute([':e' => $em]);
    $id = $ex->fetchColumn();
    if ($id !== false) {
        $u = $pdo->prepare('UPDATE users SET role = :r, password = :p WHERE id = :id');
        $u->execute([':r' => 'admin', ':p' => $hash, ':id' => (int) $id]);

        return;
    }

    $ins = $pdo->prepare(
        'INSERT INTO users (name, email, password, google_sub, role) VALUES (:n, :e, :p, NULL, :r)'
    );
    $ins->execute([':n' => 'Admin', ':e' => $em, ':p' => $hash, ':r' => 'admin']);
}

/**
 * Applies additive schema updates for DBs created before service/Google columns existed.
 * Safe to run on every request (no-op when already migrated).
 */
function db_ensure_schema(PDO $pdo): void
{
    static $done = false;
    if ($done) {
        return;
    }

    $dbName = $pdo->query('SELECT DATABASE()')->fetchColumn();
    if (!is_string($dbName) || $dbName === '') {
        $done = true;

        return;
    }

    $hasCol = static function (PDO $pdo, string $schema, string $table, string $column): bool {
        $st = $pdo->prepare(
            'SELECT 1 FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = :s AND TABLE_NAME = :t AND COLUMN_NAME = :c LIMIT 1'
        );
        $st->execute([':s' => $schema, ':t' => $table, ':c' => $column]);

        return (bool) $st->fetchColumn();
    };

    if (!$hasCol($pdo, $dbName, 'bookings', 'service_category')) {
        $pdo->exec(
            "ALTER TABLE bookings ADD COLUMN service_category VARCHAR(64) NOT NULL DEFAULT '' AFTER user_id"
        );
    }
    if (!$hasCol($pdo, $dbName, 'bookings', 'service_name')) {
        $pdo->exec(
            "ALTER TABLE bookings ADD COLUMN service_name VARCHAR(255) NOT NULL DEFAULT '' AFTER service_category"
        );
    }

    if (!$hasCol($pdo, $dbName, 'users', 'google_sub')) {
        $pdo->exec('ALTER TABLE users MODIFY password VARCHAR(255) NULL');
        $pdo->exec(
            'ALTER TABLE users ADD COLUMN google_sub VARCHAR(255) NULL UNIQUE AFTER password'
        );
    }

    if (!$hasCol($pdo, $dbName, 'users', 'role')) {
        $pdo->exec(
            "ALTER TABLE users ADD COLUMN role VARCHAR(32) NOT NULL DEFAULT 'client' AFTER google_sub"
        );
        $pdo->exec('ALTER TABLE users ADD KEY idx_users_role (role)');
    }

    if (!$hasCol($pdo, $dbName, 'bookings', 'status')) {
        $pdo->exec(
            "ALTER TABLE bookings ADD COLUMN status VARCHAR(20) NOT NULL DEFAULT 'confirmed' AFTER booking_time"
        );
        $pdo->exec('ALTER TABLE bookings ADD KEY idx_bookings_status (status)');
    }

    chb_migrate_drop_booking_slot_unique_if_exists($pdo, $dbName);

    if ($hasCol($pdo, $dbName, 'bookings', 'service_name')) {
        $t = $pdo->prepare(
            'SELECT DATA_TYPE, CHARACTER_MAXIMUM_LENGTH FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = :s AND TABLE_NAME = \'bookings\' AND COLUMN_NAME = \'service_name\''
        );
        $t->execute([':s' => $dbName]);
        $sn = $t->fetch();
        if ($sn && strtolower((string) $sn['DATA_TYPE']) === 'varchar') {
            $pdo->exec('ALTER TABLE bookings MODIFY service_name TEXT NOT NULL');
        }

        $u = $pdo->prepare(
            'SELECT CHARACTER_MAXIMUM_LENGTH FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = :s AND TABLE_NAME = \'bookings\' AND COLUMN_NAME = \'service_category\''
        );
        $u->execute([':s' => $dbName]);
        $len = $u->fetchColumn();
        if ($len !== false && (int) $len > 0 && (int) $len < 191) {
            $pdo->exec(
                "ALTER TABLE bookings MODIFY service_category VARCHAR(191) NOT NULL DEFAULT ''"
            );
        }
    }

    if ($hasCol($pdo, $dbName, 'users', 'role')) {
        chb_ensure_admin_user_from_env($pdo);
    }

    $done = true;
}
