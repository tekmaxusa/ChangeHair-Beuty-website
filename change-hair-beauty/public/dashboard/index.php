<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
require_once $root . '/config/session.php';
require_once $root . '/booking/booking.php';
require_once $root . '/config/booking_services.php';
require __DIR__ . '/../layout.php';

require_login();

$userId = current_user_id();
$msg = '';
$msgOk = false;

$categories = booking_service_categories();
$availableDates = booking_available_dates(90);

/** @var array<string, list<string>> */
$slotsByDate = [];
foreach ($availableDates as $d) {
    $slots = [];
    foreach (booking_time_options() as $opt) {
        if (!is_slot_taken($d, $opt) && slot_is_bookable_relative_now($d, $opt)) {
            $slots[] = $opt;
        }
    }
    if ($slots !== []) {
        $slotsByDate[$d] = $slots;
    }
}

/** @var list<string> */
$postServices = [];
$rawSvc = $_POST['services'] ?? [];
if (is_array($rawSvc)) {
    foreach ($rawSvc as $p) {
        if (is_string($p) && $p !== '') {
            $postServices[] = $p;
        }
    }
}
$postDate = (string) ($_POST['booking_date'] ?? '');
$postTime = (string) ($_POST['booking_time'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && (string) ($_POST['action'] ?? '') === 'book') {
    $date = $postDate;
    $time = $postTime;
    $svcVal = booking_validate_service_picks($_POST['services'] ?? []);

    if (!$svcVal['ok']) {
        $msg = (string) ($svcVal['error'] ?? 'Please select at least one service.');
        $msgOk = false;
    } elseif (!isset($slotsByDate[$date])) {
        $msg = 'That date is not available (fully booked or invalid).';
        $msgOk = false;
    } elseif (!in_array($time, $slotsByDate[$date], true)) {
        $msg = 'That time is not available for the selected date.';
        $msgOk = false;
    } else {
        $r = create_booking($userId, $date, $time, $svcVal['lines']);
        $msgOk = $r['ok'];
        $msg = $r['ok'] ? 'Booking confirmed.' : ($r['error'] ?? 'Could not book.');
        if ($r['ok']) {
            require_once $root . '/config/salon_notify.php';
            $summaryParts = [];
            foreach ($svcVal['lines'] as $line) {
                $summaryParts[] = trim((string) ($line['category'] ?? '')) . ' — ' . trim((string) ($line['service'] ?? ''));
            }
            $serviceSummary = implode(' · ', $summaryParts);
            chb_notify_booking_salon(
                current_user_name(),
                current_user_email(),
                '—',
                $serviceSummary,
                $date,
                $time
            );
            header('Location: /dashboard/?booked=1');
            exit;
        }
    }
}

if (isset($_GET['booked'])) {
    $msgOk = true;
    $msg = 'Booking confirmed.';
    $postServices = [];
    $postDate = '';
    $postTime = '';
}

$bookings = fetch_bookings_for_user($userId);

$dateKeys = array_keys($slotsByDate);
$defaultDate = $postDate;
if ($defaultDate === '' || !isset($slotsByDate[$defaultDate])) {
    $defaultDate = $dateKeys[0] ?? '';
}

$slotsJson = json_encode($slotsByDate, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);

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

    <main class="chb-page-shell">
        <h1 class="chb-dash-title">Client dashboard</h1>
        <p class="chb-dash-sub">Signed in as <strong><?= h(current_user_name()) ?></strong> — <?= h(current_user_email()) ?></p>

        <?php if ($msg !== ''): ?>
            <p class="msg <?= $msgOk ? 'ok' : 'err' ?>"><?= h($msg) ?></p>
        <?php endif; ?>

        <div class="chb-panel chb-dashboard-panel chb-book-panel">
            <header class="chb-book-panel-head">
                <h2 class="chb-section-title">Book appointment</h2>
                <p class="chb-book-intro">Select one or more services (e.g. cut and color), then pick date and time. Each time slot can only be booked once; full days are hidden.</p>
            </header>

            <?php if ($slotsByDate === []): ?>
                <p class="msg err">No open dates in the next 90 days. Please check back later or contact the salon.</p>
            <?php else: ?>
                <form method="post" action="" class="chb-book-form" id="chb-book-form">
                    <input type="hidden" name="action" value="book">

                    <div class="chb-book-fieldset" role="group" aria-labelledby="chb-book-step-service">
                        <h3 class="chb-book-legend" id="chb-book-step-service">1. Service</h3>
                        <div class="chb-book-services">
                            <?php foreach ($categories as $cat): ?>
                                <section class="chb-book-cat" aria-labelledby="cat-<?= h($cat['id']) ?>">
                                    <h4 class="chb-book-cat-title" id="cat-<?= h($cat['id']) ?>"><?= h($cat['name']) ?></h4>
                                    <ul class="chb-book-svc-list" role="list">
                                        <?php foreach ($cat['services'] as $svc): ?>
                                            <?php
                                            $val = $cat['id'] . '|' . $svc['name'];
                                            $checked = in_array($val, $postServices, true);
                                            ?>
                                            <li>
                                                <label class="chb-book-svc">
                                                    <input
                                                        type="checkbox"
                                                        name="services[]"
                                                        value="<?= h($val) ?>"
                                                        class="chb-book-svc-input"
                                                        <?= $checked ? 'checked' : '' ?>>
                                                    <span class="chb-book-svc-card">
                                                        <span class="chb-book-svc-name"><?= h($svc['name']) ?></span>
                                                        <span class="chb-book-svc-price"><?= h($svc['price']) ?></span>
                                                    </span>
                                                </label>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </section>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="chb-book-fieldset chb-book-fieldset-datetime" role="group" aria-labelledby="chb-book-step-datetime">
                        <h3 class="chb-book-legend" id="chb-book-step-datetime">2. Date &amp; time</h3>
                        <div class="chb-book-datetime-grid">
                            <div class="chb-book-field">
                                <label class="chb-book-label" for="booking_date">Date</label>
                                <select id="booking_date" name="booking_date" class="chb-book-select" required>
                                    <?php foreach (array_keys($slotsByDate) as $d): ?>
                                        <option value="<?= h($d) ?>" <?= $d === $defaultDate ? 'selected' : '' ?>><?= h($d) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="chb-book-field">
                                <label class="chb-book-label" for="booking_time">Time</label>
                                <select id="booking_time" name="booking_time" class="chb-book-select" required>
                                    <option value="">Select time</option>
                                </select>
                            </div>
                        </div>
                        <p class="chb-book-slot-note">30-minute slots · 9:00 a.m.–5:00 p.m.</p>
                    </div>

                    <button type="submit" class="chb-submit chb-book-submit">Confirm booking</button>
                </form>

                <script type="application/json" id="chb-slots-by-date"><?= $slotsJson ?></script>
                <script>
                (function () {
                  var raw = document.getElementById('chb-slots-by-date');
                  var dateSel = document.getElementById('booking_date');
                  var timeSel = document.getElementById('booking_time');
                  if (!raw || !dateSel || !timeSel) return;
                  var byDate = JSON.parse(raw.textContent || '{}');
                  var savedTime = <?= json_encode($postTime, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;

                  function fillTimes() {
                    var d = dateSel.value;
                    var slots = byDate[d] || [];
                    timeSel.innerHTML = '';
                    if (!slots.length) {
                      var o = document.createElement('option');
                      o.value = '';
                      o.textContent = 'No times available';
                      o.selected = true;
                      timeSel.appendChild(o);
                      timeSel.disabled = true;
                      return;
                    }
                    timeSel.disabled = false;
                    var keep = savedTime && slots.indexOf(savedTime) !== -1;
                    var ph = document.createElement('option');
                    ph.value = '';
                    ph.textContent = 'Select time';
                    ph.setAttribute('disabled', 'disabled');
                    ph.selected = !keep;
                    timeSel.appendChild(ph);
                    slots.forEach(function (t) {
                      var opt = document.createElement('option');
                      opt.value = t;
                      opt.textContent = t;
                      if (savedTime === t) opt.selected = true;
                      timeSel.appendChild(opt);
                    });
                  }

                  dateSel.addEventListener('change', function () { savedTime = ''; fillTimes(); });
                  fillTimes();
                })();
                </script>
            <?php endif; ?>
        </div>

        <div class="chb-panel chb-dashboard-panel" style="margin-top:1.5rem;">
            <h2 class="chb-section-title">Your appointments</h2>
            <?php if ($bookings === []): ?>
                <p class="chb-hint">No bookings yet.</p>
            <?php else: ?>
                <div class="chb-table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Service</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Booked at</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($bookings as $b): ?>
                                <tr>
                                    <td><?= h(trim($b['service_category'] . ' — ' . $b['service_name'], ' —')) ?></td>
                                    <td><?= h($b['booking_date']) ?></td>
                                    <td><?= h(substr((string) $b['booking_time'], 0, 5)) ?></td>
                                    <td><?= h($b['created_at']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <p class="chb-hint" style="text-align:center;margin-top:2rem;">
            <a href="/">← Back to site</a>
        </p>
    </main>

    <script src="/js/site.js" defer></script>
    <?php require __DIR__ . '/../partials/tawk.php'; ?>
</body>
</html>
