<?php

declare(strict_types=1);

require_once __DIR__ . '/_spa_redirect.php';

$query = [];
if (isset($_GET['next']) && is_string($_GET['next']) && trim($_GET['next']) !== '') {
    $query['next'] = $_GET['next'];
}

chb_redirect_spa('/signup', $query);
