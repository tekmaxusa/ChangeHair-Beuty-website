<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
require_once $root . '/config/session.php';
require_once $root . '/auth/admin_auth.php';
require_once $root . '/booking/booking.php';
require __DIR__ . '/../layout.php';

require_admin();

$rows = fetch_all_bookings_for_admin();

$nPending = 0;
$nConfirmed = 0;
$nCancelled = 0;
foreach ($rows as $br) {
    $s = (string) ($br['status'] ?? '');
    if ($s === CHB_BOOKING_PENDING) {
        ++$nPending;
    } elseif ($s === CHB_BOOKING_CONFIRMED) {
        ++$nConfirmed;
    } elseif ($s === CHB_BOOKING_CANCELLED) {
        ++$nCancelled;
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;14..32,500;14..32,600&family=Montserrat:wght@400;500;600&family=Playfair+Display:ital,wght@0,500;0,600;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/style.css">
</head>
<body class="chb-body chb-subpage chb-admin-dashboard">
    <main class="chb-page-shell chb-admin-dash-shell">
        <?php $chbAdminNavActive = 'dashboard'; require __DIR__ . '/partials/admin-nav.php'; ?>

        <header class="chb-admin-pagehead">
            <p class="chb-kicker chb-admin-pagehead-kicker">Merchant</p>
            <h1 class="chb-admin-pagehead-title">Admin dashboard</h1>
            <p class="chb-admin-pagehead-meta">
                <?= h(current_user_email()) ?>
                <span aria-hidden="true">·</span>
                <a href="/logout.php?next=<?= rawurlencode('/admin/login.php') ?>">Log out</a>
            </p>
        </header>

        <section class="chb-admin-stats" aria-label="Booking summary">
            <article class="chb-admin-stat chb-admin-stat--pending">
                <span class="chb-admin-stat__value"><?= $nPending ?></span>
                <span class="chb-admin-stat__label">Pending</span>
            </article>
            <article class="chb-admin-stat chb-admin-stat--confirmed">
                <span class="chb-admin-stat__value"><?= $nConfirmed ?></span>
                <span class="chb-admin-stat__label">Confirmed</span>
            </article>
            <article class="chb-admin-stat chb-admin-stat--cancelled">
                <span class="chb-admin-stat__value"><?= $nCancelled ?></span>
                <span class="chb-admin-stat__label">Cancelled</span>
            </article>
            <article class="chb-admin-stat chb-admin-stat--total">
                <span class="chb-admin-stat__value"><?= count($rows) ?></span>
                <span class="chb-admin-stat__label">Total rows</span>
            </article>
        </section>

        <section class="chb-admin-dash-links" aria-label="Quick links">
            <a class="chb-admin-dash-links__card" href="/admin/bookings.php">
                <span class="chb-admin-dash-links__label">Bookings</span>
                <span class="chb-admin-dash-links__hint">View and manage every request</span>
            </a>
            <a class="chb-admin-dash-links__card" href="/admin/users.php">
                <span class="chb-admin-dash-links__label">Accounts</span>
                <span class="chb-admin-dash-links__hint">All client logins &amp; create users</span>
            </a>
        </section>
    </main>
</body>
</html>
