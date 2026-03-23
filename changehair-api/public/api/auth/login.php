<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/_init.php';
require_once dirname(__DIR__, 3) . '/auth/login.php';

chb_api_require_method('POST');

$body = chb_api_read_json();
$email = (string) ($body['email'] ?? '');
$password = (string) ($body['password'] ?? '');

$r = login_user($email, $password);
if (!$r['ok']) {
    chb_api_json_error($r['error'] ?? 'Login failed', 401);
}

chb_api_json([
    'ok' => true,
    'user' => [
        'id' => current_user_id(),
        'name' => current_user_name(),
        'email' => current_user_email(),
        'role' => current_user_role(),
    ],
]);
