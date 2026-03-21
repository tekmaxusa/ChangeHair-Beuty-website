<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/session.php';
require_once dirname(__DIR__) . '/auth/google_oauth.php';
require_once __DIR__ . '/partials/redirect.php';

session_bootstrap();

if (!google_oauth_configured()) {
    header('Location: /login.php?google_err=not_configured');
    exit;
}

$next = chb_safe_next((string) ($_GET['next'] ?? '/dashboard/'));
$_SESSION['oauth_google_next'] = $next;

$state = bin2hex(random_bytes(16));
$_SESSION['oauth_google_state'] = $state;

header('Location: ' . google_oauth_authorization_url($state));
exit;
