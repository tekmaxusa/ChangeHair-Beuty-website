<?php

declare(strict_types=1);

$root = dirname(__DIR__);
require __DIR__ . '/layout.php';
require_once $root . '/auth/login.php';
require_once $root . '/auth/google_oauth.php';

$self = '/book-appointment.php';
$nextSignup = '/signup.php?next=' . rawurlencode($self);
$googleStart = '/google-oauth-start.php?next=' . rawurlencode($self);
$googleOAuthReady = google_oauth_configured();

$authError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && (string) ($_POST['action'] ?? '') === 'modal_login') {
    $email = (string) ($_POST['email'] ?? '');
    $password = (string) ($_POST['password'] ?? '');
    $r = login_user($email, $password);
    if ($r['ok']) {
        header('Location: ' . $self, true, 303);
        exit;
    }
    $authError = $r['error'] ?? 'Login failed.';
}

$chb_book_form_action = $self;
$chb_book_msg = '';
$chb_book_msg_ok = true;
$chb_book_categories = [];
$chb_book_slotsByDate = [];
$chb_book_postServices = [];
$chb_book_postDate = '';
$chb_book_postTime = '';
$chb_book_slotsJson = '{}';
$chb_book_defaultDate = '';

if ($loggedIn) {
    require_once $root . '/booking/booking.php';
    require_once $root . '/config/booking_services.php';

    $userId = current_user_id();

    $chb_book_categories = booking_service_categories();
    $availableDates = booking_available_dates(90);

    foreach ($availableDates as $d) {
        $slots = [];
        foreach (booking_time_options() as $opt) {
            if (!is_slot_taken($d, $opt) && slot_is_bookable_relative_now($d, $opt)) {
                $slots[] = $opt;
            }
        }
        if ($slots !== []) {
            $chb_book_slotsByDate[$d] = $slots;
        }
    }

    $rawSvc = $_POST['services'] ?? [];
    if (is_array($rawSvc)) {
        foreach ($rawSvc as $p) {
            if (is_string($p) && $p !== '') {
                $chb_book_postServices[] = $p;
            }
        }
    }
    $chb_book_postDate = (string) ($_POST['booking_date'] ?? '');
    $chb_book_postTime = (string) ($_POST['booking_time'] ?? '');

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && (string) ($_POST['action'] ?? '') === 'book') {
        $date = $chb_book_postDate;
        $time = $chb_book_postTime;
        $svcVal = booking_validate_service_picks($_POST['services'] ?? []);

        if (!$svcVal['ok']) {
            $chb_book_msg = (string) ($svcVal['error'] ?? 'Please select at least one service.');
            $chb_book_msg_ok = false;
        } elseif (!isset($chb_book_slotsByDate[$date])) {
            $chb_book_msg = 'That date is not available (fully booked or invalid).';
            $chb_book_msg_ok = false;
        } elseif (!in_array($time, $chb_book_slotsByDate[$date], true)) {
            $chb_book_msg = 'That time is not available for the selected date.';
            $chb_book_msg_ok = false;
        } else {
            $r = create_booking($userId, $date, $time, $svcVal['lines']);
            $chb_book_msg_ok = $r['ok'];
            $chb_book_msg = $r['ok']
                ? 'Request submitted. Your appointment is pending until the salon confirms it.'
                : ($r['error'] ?? 'Could not book.');
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
                $_SESSION['chb_book_flash'] = ['ok' => true, 'message' => $chb_book_msg];
                header('Content-Type: text/html; charset=UTF-8');
                echo '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><title>Redirect</title></head><body>';
                echo '<script>try{window.top.location.href="/?booked=1";}catch(e){location.href="/?booked=1";}</script>';
                echo '<noscript><meta http-equiv="refresh" content="0;url=/?booked=1"><p><a href="/?booked=1">Continue</a></p></noscript>';
                echo '</body></html>';
                exit;
            }
        }
    }

    $dateKeys = array_keys($chb_book_slotsByDate);
    $chb_book_defaultDate = $chb_book_postDate;
    if ($chb_book_defaultDate === '' || !isset($chb_book_slotsByDate[$chb_book_defaultDate])) {
        $chb_book_defaultDate = $dateKeys[0] ?? '';
    }

    $chb_book_slotsJson = json_encode($chb_book_slotsByDate, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book appointment — Change Hair &amp; Beauty</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;14..32,500;14..32,600&family=Montserrat:wght@400;500;600&family=Playfair+Display:ital,wght@0,500;0,600;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/style.css">
</head>
<body class="chb-body chb-subpage chb-book-frame">
    <main class="chb-book-frame-inner">
        <?php if (!$loggedIn): ?>
            <h1 class="chb-page-title" style="margin-top:0;">Book appointment</h1>
            <p class="chb-hint">Sign in or create an account to submit a booking request. Status updates appear in your <a href="/dashboard/" target="_top">client dashboard</a>.</p>

            <?php if ($authError !== ''): ?>
                <p class="msg err"><?= h($authError) ?></p>
            <?php endif; ?>

            <form method="post" action="<?= h($self) ?>" class="chb-panel" style="padding:1.25rem;">
                <input type="hidden" name="action" value="modal_login">
                <label>Email
                    <input type="email" name="email" required autocomplete="username" value="<?= h((string) ($_POST['email'] ?? '')) ?>">
                </label>
                <label>Password
                    <input type="password" name="password" required autocomplete="current-password">
                </label>
                <button type="submit" class="chb-submit">Log in</button>
            </form>

            <p class="chb-hint"><a href="<?= h($nextSignup) ?>" target="_top" rel="noopener">Create account</a></p>

            <div class="chb-oauth-or"><span>Or</span></div>

            <?php if ($googleOAuthReady): ?>
                <a class="chb-google-btn" href="<?= h($googleStart) ?>" target="_top" rel="noopener"><span class="chb-google-g">G</span> Continue with Google</a>
            <?php else: ?>
                <a class="chb-google-btn" href="https://accounts.google.com/" target="_blank" rel="noopener noreferrer"><span class="chb-google-g">G</span> Continue with Google</a>
            <?php endif; ?>
        <?php else: ?>
            <h1 class="chb-page-title" style="margin-top:0;">Book appointment</h1>
            <p class="chb-hint">Signed in as <strong><?= h(current_user_name()) ?></strong>. <a href="/logout.php?next=<?= rawurlencode($self) ?>" target="_top">Log out</a></p>

            <?php if ($chb_book_msg !== ''): ?>
                <p class="msg <?= $chb_book_msg_ok ? 'ok' : 'err' ?>"><?= h($chb_book_msg) ?></p>
            <?php endif; ?>

            <?php require __DIR__ . '/partials/booking-request-form.php'; ?>
        <?php endif; ?>
    </main>
</body>
</html>
