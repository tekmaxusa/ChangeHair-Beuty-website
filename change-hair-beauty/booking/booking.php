<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/booking_services.php';

/**
 * @return list<array{id:int,service_category:string,service_name:string,booking_date:string,booking_time:string,created_at:string}>
 */
function fetch_bookings_for_user(int $userId): array
{
    $pdo = db();
    $stmt = $pdo->prepare(
        'SELECT id, service_category, service_name, booking_date, booking_time, created_at
         FROM bookings WHERE user_id = :uid ORDER BY booking_date ASC, booking_time ASC'
    );
    $stmt->execute([':uid' => $userId]);
    return $stmt->fetchAll();
}

function is_slot_taken(string $dateYmd, string $timeHi): bool
{
    $pdo = db();
    $stmt = $pdo->prepare(
        'SELECT 1 FROM bookings WHERE booking_date = :d AND booking_time = :t LIMIT 1'
    );
    $stmt->execute([':d' => $dateYmd, ':t' => $timeHi . ':00']);
    return (bool) $stmt->fetchColumn();
}

/**
 * Slot exists in schedule and is not in the past (for "today").
 */
function slot_is_bookable_relative_now(string $dateYmd, string $timeHi): bool
{
    $today = (new DateTimeImmutable('today'))->format('Y-m-d');
    if ($dateYmd > $today) {
        return true;
    }
    if ($dateYmd < $today) {
        return false;
    }
    $dt = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $dateYmd . ' ' . $timeHi . ':00');
    if (!$dt || $dt->format('Y-m-d H:i:s') !== $dateYmd . ' ' . $timeHi . ':00') {
        return false;
    }

    return $dt > new DateTimeImmutable('now');
}

/**
 * Count of slots that are free and still bookable (future if today).
 */
function count_available_slots_for_date(string $dateYmd): int
{
    $n = 0;
    foreach (booking_time_options() as $opt) {
        if (!is_slot_taken($dateYmd, $opt) && slot_is_bookable_relative_now($dateYmd, $opt)) {
            $n++;
        }
    }

    return $n;
}

/**
 * True when no slot can be booked on this calendar day (all taken or all past).
 */
function date_is_fully_blocked(string $dateYmd): bool
{
    return count_available_slots_for_date($dateYmd) === 0;
}

/**
 * @return list<string> Y-m-d for days with at least one bookable free slot
 */
function booking_available_dates(int $daysAhead = 90): array
{
    $out = [];
    $start = new DateTimeImmutable('today');
    for ($i = 0; $i < $daysAhead; $i++) {
        $d = $start->modify('+' . $i . ' days')->format('Y-m-d');
        if (!date_is_fully_blocked($d)) {
            $out[] = $d;
        }
    }

    return $out;
}

/**
 * @param list<array{category:string,service:string}> $serviceLines validated lines (display names)
 *
 * @return array{ok: bool, error?: string}
 */
function create_booking(
    int $userId,
    string $dateYmd,
    string $timeHi,
    array $serviceLines
): array {
    if ($userId <= 0) {
        return ['ok' => false, 'error' => 'You must be logged in.'];
    }

    if ($serviceLines === []) {
        return ['ok' => false, 'error' => 'Please select at least one service.'];
    }

    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateYmd)) {
        return ['ok' => false, 'error' => 'Invalid date.'];
    }

    if (!preg_match('/^\d{2}:\d{2}$/', $timeHi)) {
        return ['ok' => false, 'error' => 'Invalid time.'];
    }

    $labels = [];
    $detailParts = [];
    foreach ($serviceLines as $line) {
        $cat = trim((string) ($line['category'] ?? ''));
        $svc = trim((string) ($line['service'] ?? ''));
        if ($cat === '' || $svc === '') {
            return ['ok' => false, 'error' => 'Invalid service data.'];
        }
        $labels[$cat] = true;
        $detailParts[] = $cat . ' — ' . $svc;
    }

    $categoryKeys = array_keys($labels);
    $service_category = count($categoryKeys) === 1
        ? $categoryKeys[0]
        : implode(' + ', $categoryKeys);
    if (function_exists('mb_strlen') && mb_strlen($service_category) > 190) {
        $service_category = mb_substr($service_category, 0, 187) . '…';
    } elseif (strlen($service_category) > 190) {
        $service_category = substr($service_category, 0, 187) . '…';
    }

    $service_name = implode(' · ', $detailParts);

    $today = new DateTimeImmutable('today');
    $picked = DateTimeImmutable::createFromFormat('Y-m-d', $dateYmd);
    if (!$picked || $picked->format('Y-m-d') !== $dateYmd) {
        return ['ok' => false, 'error' => 'Invalid date.'];
    }
    if ($picked < $today) {
        return ['ok' => false, 'error' => 'Cannot book a date in the past.'];
    }

    if (date_is_fully_blocked($dateYmd)) {
        return ['ok' => false, 'error' => 'That date has no open slots.'];
    }

    $timeNorm = $timeHi . ':00';
    $dt = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $dateYmd . ' ' . $timeNorm);
    if (!$dt || $dt->format('Y-m-d H:i:s') !== $dateYmd . ' ' . $timeNorm) {
        return ['ok' => false, 'error' => 'Invalid time.'];
    }
    if (!slot_is_bookable_relative_now($dateYmd, $timeHi)) {
        return ['ok' => false, 'error' => 'Cannot book a time in the past.'];
    }

    if (is_slot_taken($dateYmd, $timeHi)) {
        return ['ok' => false, 'error' => 'That time slot is already booked.'];
    }

    $pdo = db();
    $stmt = $pdo->prepare(
        'INSERT INTO bookings (user_id, service_category, service_name, booking_date, booking_time)
         VALUES (:uid, :sc, :sn, :d, :t)'
    );
    try {
        $stmt->execute([
            ':uid' => $userId,
            ':sc' => $service_category,
            ':sn' => $service_name,
            ':d' => $dateYmd,
            ':t' => $timeNorm,
        ]);
    } catch (PDOException $e) {
        if ((int) $e->errorInfo[1] === 1062) {
            return ['ok' => false, 'error' => 'That time slot was just taken. Please pick another.'];
        }
        throw $e;
    }

    return ['ok' => true];
}

/**
 * @return list<string> HH:MM options (30-minute steps, business hours)
 */
function booking_time_options(): array
{
    $out = [];
    for ($h = 9; $h <= 17; $h++) {
        foreach (['00', '30'] as $m) {
            if ($h === 17 && $m === '30') {
                break;
            }
            $out[] = sprintf('%02d:%s', $h, $m);
        }
    }

    return $out;
}
