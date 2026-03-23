<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/_init.php';

chb_api_require_method('GET');

session_bootstrap();
if (empty($_SESSION['user_id'])) {
    chb_api_json(['ok' => true, 'user' => null]);
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
