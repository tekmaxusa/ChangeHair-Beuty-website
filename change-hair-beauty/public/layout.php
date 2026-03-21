<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/session.php';

session_bootstrap();
$loggedIn = !empty($_SESSION['user_id']);

if (empty($_SESSION['chb_csrf_contact']) || !is_string($_SESSION['chb_csrf_contact'])) {
    $_SESSION['chb_csrf_contact'] = bin2hex(random_bytes(16));
}

if (!function_exists('h')) {
    function h(string $s): string
    {
        return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
    }
}
