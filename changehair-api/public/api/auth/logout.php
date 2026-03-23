<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/_init.php';
require_once dirname(__DIR__, 3) . '/auth/login.php';

chb_api_require_method('POST');

logout_user();
chb_api_json(['ok' => true]);
