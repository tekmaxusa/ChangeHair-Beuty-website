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
