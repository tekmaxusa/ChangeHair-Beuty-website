<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/auth/login.php';
require_once __DIR__ . '/partials/redirect.php';

$next = trim((string) ($_GET['next'] ?? ''));
logout_user();
header('Location: ' . ($next !== '' ? chb_safe_next($next) : '/login.php'));
exit;
