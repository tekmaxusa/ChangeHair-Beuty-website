<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/_init.php';
require_once dirname(__DIR__, 3) . '/booking/booking.php';
require_once dirname(__DIR__, 3) . '/config/contact_mail.php';

chb_api_require_admin_json();

$method = $_SERVER['REQUEST_METHOD'] ?? '';

if ($method === 'GET') {
    if (empty($_SESSION['chb_csrf_admin']) || !is_string($_SESSION['chb_csrf_admin'])) {
        $_SESSION['chb_csrf_admin'] = bin2hex(random_bytes(16));
    }
    $rows = fetch_all_bookings_for_admin();
    chb_api_json([
        'ok' => true,
        'csrf' => (string) $_SESSION['chb_csrf_admin'],
        'bookings' => $rows,
    ]);
}

if ($method !== 'POST') {
    chb_api_json_error('Method not allowed', 405);
}

$body = chb_api_read_json();
$csrf = (string) ($body['csrf'] ?? '');
$sessionCsrf = (string) ($_SESSION['chb_csrf_admin'] ?? '');
if ($sessionCsrf === '' || !hash_equals($sessionCsrf, $csrf)) {
    chb_api_json_error('Invalid session token.', 400);
}

$action = (string) ($body['action'] ?? '');

if ($action === 'delete_many') {
    $rawIds = $body['booking_ids'] ?? [];
    if (!is_array($rawIds)) {
        chb_api_json_error('Invalid request.', 400);
    }
    $r = admin_delete_bookings($rawIds);
    if (!$r['ok']) {
        chb_api_json_error($r['error'] ?? 'Delete failed.', 400);
    }
    $_SESSION['chb_csrf_admin'] = bin2hex(random_bytes(16));
    chb_api_json([
        'ok' => true,
        'csrf' => (string) $_SESSION['chb_csrf_admin'],
        'message' => 'Bookings removed.',
        'deleted' => (int) ($r['deleted'] ?? 0),
    ]);
}

$bid = (int) ($body['booking_id'] ?? 0);
if ($bid <= 0 || $action !== 'cancel') {
    chb_api_json_error('Invalid request.', 400);
}

$r = admin_set_booking_status($bid, CHB_BOOKING_CANCELLED);
if (!$r['ok']) {
    chb_api_json_error($r['error'] ?? 'Update failed.', 400);
}

$old = (string) ($r['old_status'] ?? '');
$new = (string) ($r['new_status'] ?? '');
if ($old !== $new && $new === CHB_BOOKING_CANCELLED) {
    $row = fetch_booking_by_id($bid);
    if ($row) {
        $timeHi = substr((string) $row['booking_time'], 0, 5);
        $summary = chb_booking_services_summary($row);
        chb_send_booking_status_email_to_client(
            (string) $row['client_email'],
            (string) $row['client_name'],
            $new,
            (string) $row['booking_date'],
            $timeHi,
            $summary
        );
    }
}

$_SESSION['chb_csrf_admin'] = bin2hex(random_bytes(16));
chb_api_json(['ok' => true, 'csrf' => (string) $_SESSION['chb_csrf_admin'], 'message' => 'Booking updated.']);
