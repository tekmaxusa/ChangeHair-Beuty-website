<?php

declare(strict_types=1);

require __DIR__ . '/layout.php';

/** @var array<string,mixed> $salon */
$salon = require dirname(__DIR__) . '/config/salon_data.php';

$chb_home_book_success = '';
if (isset($_GET['booked']) && isset($_SESSION['chb_book_flash']) && is_array($_SESSION['chb_book_flash'])) {
    $fl = $_SESSION['chb_book_flash'];
    unset($_SESSION['chb_book_flash']);
    if (!empty($fl['message'])) {
        $chb_home_book_success = (string) $fl['message'];
    }
}

$nextDash = rawurlencode('/dashboard/');
$bookHref = '/book-appointment.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Hair &amp; Beauty | Lewisville TX</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,400;0,14..32,500;0,14..32,600;0,14..32,700;1,14..32,400&family=Montserrat:ital,wght@0,400;0,500;0,600;1,400&family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/style.css">
</head>
<body class="chb-body chb-home">
    <?php require __DIR__ . '/partials/site-header.php'; ?>

    <?php if ($chb_home_book_success !== ''): ?>
        <div class="chb-wrap" style="padding-top:1rem;">
            <p class="msg ok" role="status" style="max-width:42rem;margin:0 auto;"><?= h($chb_home_book_success) ?></p>
        </div>
    <?php endif; ?>

    <?php require __DIR__ . '/partials/section-hero.php'; ?>
    <?php require __DIR__ . '/partials/section-story.php'; ?>
    <?php require __DIR__ . '/partials/section-signature.php'; ?>
    <?php require __DIR__ . '/partials/section-services.php'; ?>
    <?php require __DIR__ . '/partials/section-gallery.php'; ?>
    <?php require __DIR__ . '/partials/section-testimonials.php'; ?>
    <?php require __DIR__ . '/partials/section-booking-cta.php'; ?>
    <?php require __DIR__ . '/partials/section-contact-footer.php'; ?>

    <script src="/js/site.js" defer></script>
    <?php require __DIR__ . '/partials/tawk.php'; ?>
</body>
</html>
