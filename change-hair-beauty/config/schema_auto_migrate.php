<?php

declare(strict_types=1);

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

    $done = true;
}
