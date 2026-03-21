<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/session.php';
require_once dirname(__DIR__) . '/auth/google_oauth.php';
require_once __DIR__ . '/partials/redirect.php';

session_bootstrap();

$next = chb_safe_next((string) ($_SESSION['oauth_google_next'] ?? '/dashboard/'));
unset($_SESSION['oauth_google_next']);

$err = (string) ($_GET['error'] ?? '');
if ($err !== '') {
    header('Location: /login.php?google_err=' . rawurlencode($err));
    exit;
}

$code = (string) ($_GET['code'] ?? '');
$state = (string) ($_GET['state'] ?? '');
$sessState = (string) ($_SESSION['oauth_google_state'] ?? '');
unset($_SESSION['oauth_google_state']);

if ($code === '' || $state === '' || $sessState === '' || !hash_equals($sessState, $state)) {
    header('Location: /login.php?google_err=invalid_state');
    exit;
}

try {
    $tok = google_oauth_exchange_code($code);
    $info = google_oauth_userinfo($tok['access_token']);
    login_or_register_google_user($info['sub'], $info['email'], $info['name']);
} catch (Throwable $e) {
    header('Location: /login.php?google_err=' . rawurlencode('auth_failed'));
    exit;
}

header('Location: ' . $next);
exit;
