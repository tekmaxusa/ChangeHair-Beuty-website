<?php

declare(strict_types=1);

require __DIR__ . '/layout.php';

/** @var array<string,mixed> $salon */
$salon = require dirname(__DIR__) . '/config/salon_data.php';

$bookHref = '/book-appointment.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu &amp; Pricing — Change Hair &amp; Beauty | Lewisville TX</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,400;0,14..32,500;0,14..32,600;0,14..32,700;1,14..32,400&family=Montserrat:ital,wght@0,400;0,500;0,600;1,400&family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/style.css">
</head>
<body class="chb-body chb-subpage">
    <?php require __DIR__ . '/partials/site-header.php'; ?>

    <main class="chb-marketing-main">
        <?php require __DIR__ . '/partials/section-services.php'; ?>
    </main>

    <?php require __DIR__ . '/partials/section-contact-footer.php'; ?>

<?php require __DIR__ . '/partials/chb-marketing-tail.php'; ?>
</body>
</html>
