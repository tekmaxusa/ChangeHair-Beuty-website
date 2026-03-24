<?php

declare(strict_types=1);

require_once __DIR__ . '/_spa_redirect.php';

$query = [];
if (isset($_GET['next']) && is_string($_GET['next']) && trim($_GET['next']) !== '') {
    $query['next'] = $_GET['next'];
}
if (isset($_GET['google_err']) && is_string($_GET['google_err']) && trim($_GET['google_err']) !== '') {
    $query['google_err'] = $_GET['google_err'];
}

chb_redirect_spa('/login', $query);
