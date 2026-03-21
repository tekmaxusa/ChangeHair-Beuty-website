<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/auth/google_oauth.php';

header('Content-Type: text/plain; charset=UTF-8');

echo "Change Hair Beauty — server is up and running.\n\n";

echo "Google OAuth (Continue with Google):\n";
echo '  Configured: ' . (google_oauth_configured() ? 'yes' : 'no') . "\n";

foreach (google_oauth_env_file_candidates() as $p) {
    $ok = is_readable($p) ? 'readable' : 'missing';
    echo '  .env candidate: ' . $ok . ' — ' . $p . "\n";
}

if (!google_oauth_configured()) {
    echo "\nFix: copy .env.example to change-hair-beauty/.env and set GOOGLE_CLIENT_ID and GOOGLE_CLIENT_SECRET.\n";
    echo "Or set those variables in Docker / hosting panel. Restart the container after editing.\n";
}

echo "\nMySQL (PDO):\n";
try {
    require_once dirname(__DIR__) . '/config/database.php';
    db()->query('SELECT 1');
    echo "  OK — connected.\n";
} catch (Throwable $e) {
    echo '  FAILED — ' . $e->getMessage() . "\n";
    echo "  Docker: ensure `db` is up (`docker compose up db -d`). XAMPP: use DB_HOST=127.0.0.1 and DB_PORT=3307 in .env.\n";
}

$base = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http')
    . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost');
echo "\nRoutes (same Docker web container — DocumentRoot is public/):\n";
echo "  Public site:     {$base}/\n";
echo "  Client login:    {$base}/login.php\n";
echo "  Client dashboard: {$base}/dashboard/\n";
echo "  Merchant admin:  {$base}/admin/login.php  →  {$base}/admin/\n";
