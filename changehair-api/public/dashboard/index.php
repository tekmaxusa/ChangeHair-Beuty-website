<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
require_once $root . '/config/session.php';
require_once $root . '/booking/booking.php';
require __DIR__ . '/../layout.php';

require_login();

$userId = current_user_id();
$bookings = fetch_bookings_for_user($userId);

$name = current_user_name();
$nm = trim($name);
$initial = strtoupper($nm !== '' ? substr($nm, 0, 1) : '?');

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client dashboard — Change Hair &amp; Beauty</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;14..32,500;14..32,600&family=Montserrat:wght@400;500;600&family=Playfair+Display:ital,wght@0,500;0,600;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/style.css">
</head>
<body class="chb-body chb-subpage">
    <?php require __DIR__ . '/../partials/site-header.php'; ?>

    <main class="chb-page-shell chb-client-dashboard">
        <div class="chb-dash-shell">
            <header class="chb-dash-pagehead">
                <p class="chb-kicker chb-dash-pagehead-kicker">Client account</p>
                <h1 class="chb-dash-pagehead-title">Dashboard</h1>
                <p class="chb-dash-pagehead-lead">View your appointment requests and status in one place.</p>
            </header>

            <section class="chb-dash-profile" aria-label="Signed-in account">
                <div class="chb-dash-profile-main">
                    <div class="chb-dash-avatar" aria-hidden="true"><?= h($initial) ?></div>
                    <div class="chb-dash-profile-text">
                        <p class="chb-dash-profile-name"><?= h($name) ?></p>
                        <p class="chb-dash-profile-email"><?= h(current_user_email()) ?></p>
                    </div>
                </div>
                <a class="chb-btn-gold chb-dash-profile-cta chb-booking-open" href="/book-appointment.php">Book appointment</a>
            </section>

            <section class="chb-dash-card" aria-labelledby="chb-dash-appts">
                <div class="chb-dash-card-head">
                    <h2 id="chb-dash-appts" class="chb-dash-card-title">Your appointments</h2>
                    <p class="chb-dash-card-desc">
                        <strong>Pending</strong> means the salon has not confirmed yet.
                        <strong>Confirmed</strong> and <strong>Cancelled</strong> updates are emailed to you when the salon acts on your request.
                    </p>
                </div>

                <?php if ($bookings === []): ?>
                    <div class="chb-dash-empty" role="status">
                        <p class="chb-dash-empty-title">No appointments yet</p>
                        <p class="chb-dash-empty-text">Submit a booking request to see it listed here with live status.</p>
                        <a class="chb-btn-gold chb-booking-open" href="/book-appointment.php">Request an appointment</a>
                    </div>
                <?php else: ?>
                    <div class="chb-dash-table-scroll">
                        <table class="chb-dash-table">
                            <thead>
                                <tr>
                                    <th scope="col">Status</th>
                                    <th scope="col">Service</th>
                                    <th scope="col">Date</th>
                                    <th scope="col">Time</th>
                                    <th scope="col">Requested</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($bookings as $b): ?>
                                    <?php
                                    $svc = trim((string) $b['service_category'] . ' — ' . (string) $b['service_name'], ' —');
                                    $bdTs = strtotime((string) $b['booking_date']);
                                    $dateLabel = $bdTs ? date('M j, Y', $bdTs) : (string) $b['booking_date'];
                                    $timeRaw = substr((string) $b['booking_time'], 0, 5);
                                    $timeTs = strtotime('2000-01-01 ' . $timeRaw . ':00');
                                    $timeLabel = $timeTs ? date('g:i A', $timeTs) : $timeRaw;
                                    $crTs = strtotime((string) $b['created_at']);
                                    $createdLabel = $crTs ? date('M j, Y · g:i A', $crTs) : (string) $b['created_at'];
                                    ?>
                                    <tr>
                                        <td>
                                            <span class="chb-badge chb-badge--<?= h((string) $b['status']) ?>"><?= h(ucfirst((string) $b['status'])) ?></span>
                                        </td>
                                        <td class="chb-dash-cell-service"><?= h($svc !== '' ? $svc : '—') ?></td>
                                        <td><?= h($dateLabel) ?></td>
                                        <td><?= h($timeLabel) ?></td>
                                        <td class="chb-dash-cell-muted"><?= h($createdLabel) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </section>

            <p class="chb-dash-footer-link">
                <a href="/">← Back to site</a>
            </p>
        </div>
    </main>

    <script src="/js/site.js" defer></script>
    <?php require __DIR__ . '/../partials/tawk.php'; ?>
</body>
</html>
