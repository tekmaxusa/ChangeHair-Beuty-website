<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/_init.php';
require_once dirname(__DIR__, 3) . '/booking/booking.php';
require_once dirname(__DIR__, 3) . '/config/booking_services.php';
require_once dirname(__DIR__, 3) . '/config/salon_notify.php';
require_once dirname(__DIR__, 3) . '/config/booking_webhook.php';

$method = $_SERVER['REQUEST_METHOD'] ?? '';

if ($method === 'GET') {
    chb_api_require_login();
    $rows = fetch_bookings_for_user(current_user_id());
    chb_api_json(['ok' => true, 'bookings' => $rows]);
}

if ($method !== 'POST') {
    chb_api_json_error('Method not allowed', 405);
}

$body = chb_api_read_json();
$date = (string) ($body['booking_date'] ?? '');
$time = (string) ($body['booking_time'] ?? '');
$services = $body['services'] ?? [];
if (!is_array($services)) {
    $services = [];
}

$svcVal = booking_validate_service_picks($services);
if (!$svcVal['ok']) {
    chb_api_json_error($svcVal['error'] ?? 'Invalid services', 400);
}

session_bootstrap();
$isClientSession = !empty($_SESSION['user_id']) && !is_admin_session();

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
    chb_api_json_error('Invalid date.', 400);
}

if (!booking_slot_is_available_for_request($date, $time)) {
    chb_api_json_error('That time slot is no longer available. Please choose another.', 400);
}

/** @var list<array{category:string,service:string}> $lines */
$lines = $svcVal['lines'];

if ($isClientSession) {
    chb_api_require_client();
    $userId = current_user_id();
    $r = create_booking($userId, $date, $time, $lines);
    if (!$r['ok']) {
        chb_api_json_error($r['error'] ?? 'Could not book.', 400);
    }

    $summaryParts = [];
    foreach ($lines as $line) {
        $summaryParts[] = trim((string) ($line['category'] ?? '')) . ' — ' . trim((string) ($line['service'] ?? ''));
    }
    $serviceSummary = implode(' · ', $summaryParts);
    chb_notify_booking_created_all_channels(
        current_user_name(),
        current_user_email(),
        '—',
        $serviceSummary,
        $date,
        $time
    );

    chb_notify_booking_created_webhook((int) ($r['booking_id'] ?? 0));

    chb_api_json(['ok' => true, 'message' => 'Your appointment is confirmed. A confirmation email has been sent.']);
}

$guestName = (string) ($body['guest_name'] ?? '');
$guestEmail = (string) ($body['guest_email'] ?? '');
$guestPhone = (string) ($body['guest_phone'] ?? '');

$guestEmailNorm = strtolower(trim($guestEmail));
if ($guestEmailNorm !== '') {
    $pdo = db();
    $stmt = $pdo->prepare('SELECT 1 FROM users WHERE email = :email LIMIT 1');
    $stmt->execute([':email' => $guestEmailNorm]);
    if ($stmt->fetchColumn()) {
        chb_api_json([
            'ok' => false,
            'code' => 'email_registered',
            'error' => 'This email already has an account. Sign in to book and track your requests in the dashboard.',
        ], 400);
    }
}

$r = create_booking(null, $date, $time, $lines, $guestName, $guestEmail, $guestPhone);
if (!$r['ok']) {
    chb_api_json_error($r['error'] ?? 'Could not book.', 400);
}

$summaryParts = [];
foreach ($lines as $line) {
    $summaryParts[] = trim((string) ($line['category'] ?? '')) . ' — ' . trim((string) ($line['service'] ?? ''));
}
$serviceSummary = implode(' · ', $summaryParts);
$gName = trim($guestName);
$gEmail = trim($guestEmail);
$gPhone = trim($guestPhone);
chb_notify_booking_created_all_channels(
    $gName,
    $gEmail,
    $gPhone !== '' ? $gPhone : '—',
    $serviceSummary,
    $date,
    $time
);

chb_notify_booking_created_webhook((int) ($r['booking_id'] ?? 0));

chb_api_json(['ok' => true, 'message' => 'Your appointment is confirmed. A confirmation email has been sent.']);
