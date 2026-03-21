<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/auth/login.php';

logout_user();
header('Location: /login.php');
exit;
