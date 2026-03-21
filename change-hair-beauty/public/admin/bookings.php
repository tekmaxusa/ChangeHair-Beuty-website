<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
require_once $root . '/config/session.php';
require_once $root . '/auth/admin_auth.php';
require_once $root . '/booking/booking.php';
require_once $root . '/config/contact_mail.php';
require __DIR__ . '/../layout.php';

require_admin();

if (empty($_SESSION['chb_csrf_admin']) || !is_string($_SESSION['chb_csrf_admin'])) {
    $_SESSION['chb_csrf_admin'] = bin2hex(random_bytes(16));
}
$csrf = (string) $_SESSION['chb_csrf_admin'];

$msg = '';
$msgOk = true;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = (string) ($_POST['csrf'] ?? '');
    if (!hash_equals($csrf, $token)) {
        $msg = 'Invalid session token. Refresh and try again.';
        $msgOk = false;
    } else {
        $action = (string) ($_POST['action'] ?? '');
        $bid = (int) ($_POST['booking_id'] ?? 0);
        if ($bid <= 0 || ($action !== 'confirm' && $action !== 'cancel')) {
            $msg = 'Invalid request.';
            $msgOk = false;
        } else {
            $newStatus = $action === 'confirm' ? CHB_BOOKING_CONFIRMED : CHB_BOOKING_CANCELLED;
            $r = admin_set_booking_status($bid, $newStatus);
            if (!$r['ok']) {
                $msg = $r['error'] ?? 'Update failed.';
                $msgOk = false;
            } else {
                $old = (string) ($r['old_status'] ?? '');
                $new = (string) ($r['new_status'] ?? '');
                if ($old !== $new && ($new === CHB_BOOKING_CONFIRMED || $new === CHB_BOOKING_CANCELLED)) {
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
                $msg = 'Booking updated.';
                $msgOk = true;
                $_SESSION['chb_csrf_admin'] = bin2hex(random_bytes(16));
                $csrf = (string) $_SESSION['chb_csrf_admin'];
            }
        }
    }
}

$rows = fetch_all_bookings_for_admin();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookings — merchant admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;14..32,500;14..32,600&family=Montserrat:wght@400;500;600&family=Playfair+Display:ital,wght@0,500;0,600;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/style.css">
</head>
<body class="chb-body chb-subpage chb-admin-dashboard">
    <main class="chb-page-shell chb-admin-dash-shell">
        <?php $chbAdminNavActive = 'bookings'; require __DIR__ . '/partials/admin-nav.php'; ?>

        <header class="chb-admin-pagehead">
            <p class="chb-kicker chb-admin-pagehead-kicker">Merchant</p>
            <h1 class="chb-admin-pagehead-title">All bookings</h1>
            <p class="chb-admin-pagehead-meta">
                <?= h(current_user_email()) ?>
                <span aria-hidden="true">·</span>
                <a href="/logout.php?next=<?= rawurlencode('/admin/login.php') ?>">Log out</a>
            </p>
        </header>

        <?php if ($msg !== ''): ?>
            <p class="msg <?= $msgOk ? 'ok' : 'err' ?>"><?= h($msg) ?></p>
        <?php endif; ?>

        <section class="chb-admin-bookings-card">
            <div class="chb-admin-bookings-card__head">
                <p class="chb-admin-bookings-card__lead">Confirm or cancel requests. Clients receive an email when status changes (if mail is configured).</p>
            </div>
            <?php if ($rows === []): ?>
                <p class="chb-admin-empty">No bookings yet. Client requests will appear here.</p>
            <?php else: ?>
                <div class="chb-table-wrap chb-admin-table">
                    <table class="chb-admin-bookings-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Status</th>
                                <th>Client</th>
                                <th>Service</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rows as $b): ?>
                                <?php
                                $st = (string) $b['status'];
                                $bid = (int) $b['id'];
                                ?>
                                <tr>
                                    <td><?= $bid ?></td>
                                    <td><span class="chb-badge chb-badge--<?= h($st) ?>"><?= h(ucfirst($st)) ?></span></td>
                                    <td><?= h((string) $b['client_name']) ?><br><small><?= h((string) $b['client_email']) ?></small></td>
                                    <td><?= h(trim((string) $b['service_category'] . ' — ' . (string) $b['service_name'], ' —')) ?></td>
                                    <td><?= h((string) $b['booking_date']) ?></td>
                                    <td><?= h(substr((string) $b['booking_time'], 0, 5)) ?></td>
                                    <td class="chb-admin-actions">
                                        <?php if ($st === CHB_BOOKING_PENDING): ?>
                                            <form method="post" class="chb-inline-form">
                                                <input type="hidden" name="csrf" value="<?= h($csrf) ?>">
                                                <input type="hidden" name="booking_id" value="<?= $bid ?>">
                                                <input type="hidden" name="action" value="confirm">
                                                <button type="submit" class="chb-btn-admin chb-btn-admin--ok">Confirm</button>
                                            </form>
                                            <form method="post" class="chb-inline-form">
                                                <input type="hidden" name="csrf" value="<?= h($csrf) ?>">
                                                <input type="hidden" name="booking_id" value="<?= $bid ?>">
                                                <input type="hidden" name="action" value="cancel">
                                                <button type="submit" class="chb-btn-admin chb-btn-admin--danger">Cancel</button>
                                            </form>
                                        <?php elseif ($st === CHB_BOOKING_CONFIRMED): ?>
                                            <form method="post" class="chb-inline-form">
                                                <input type="hidden" name="csrf" value="<?= h($csrf) ?>">
                                                <input type="hidden" name="booking_id" value="<?= $bid ?>">
                                                <input type="hidden" name="action" value="cancel">
                                                <button type="submit" class="chb-btn-admin chb-btn-admin--danger">Cancel</button>
                                            </form>
                                        <?php else: ?>
                                            <span class="chb-hint">—</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>
