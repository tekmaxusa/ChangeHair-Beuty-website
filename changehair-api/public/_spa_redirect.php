<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/env.php';

/**
 * Build a SPA URL for redirects. If CHB_APP_PUBLIC_URL exists, use it as base.
 */
function chb_spa_url(string $path = '/', array $query = []): string
{
    $path = '/' . ltrim($path, '/');
    $base = trim((string) env_get('CHB_APP_PUBLIC_URL', ''));
    $url = $base !== '' ? rtrim($base, '/') . $path : $path;
    if ($query !== []) {
        $qs = http_build_query($query);
        if ($qs !== '') {
            $url .= '?' . $qs;
        }
    }
    return $url;
}

function chb_redirect_spa(string $path = '/', array $query = [], int $status = 302): void
{
    header('Location: ' . chb_spa_url($path, $query), true, $status);
    exit;
}
