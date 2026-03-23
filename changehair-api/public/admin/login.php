<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
require_once $root . '/config/session.php';
require_once $root . '/auth/admin_auth.php';
require __DIR__ . '/../layout.php';

if (is_admin_session()) {
    header('Location: /admin/');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = (string) ($_POST['email'] ?? '');
    $password = (string) ($_POST['password'] ?? '');
    $r = login_admin_user($email, $password);
    if ($r['ok']) {
        header('Location: /admin/');
        exit;
    }
    $error = $r['error'] ?? 'Login failed.';
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Merchant sign in — Change Hair &amp; Beauty</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;14..32,500;14..32,600&family=Montserrat:wght@400;500;600&family=Playfair+Display:ital,wght@0,400;0,500;0,600;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/style.css">
</head>
<body class="chb-body chb-subpage chb-admin-login-page">
    <main class="chb-page-shell chb-admin-login-shell">
        <div class="chb-admin-login-brand">
            <p class="chb-kicker chb-admin-login-kicker">Merchant</p>
            <h1 class="chb-admin-login-title">Merchant dashboard</h1>
            <p class="chb-admin-login-lead">Merchant sign-in only — client accounts are rejected. Use the admin email and password from <code>.env</code> (<code>ADMIN_EMAIL</code> / <code>ADMIN_INITIAL_PASSWORD</code>).</p>
        </div>

        <div class="chb-panel chb-admin-login-single">
            <h2 class="chb-admin-login-card-title" style="margin-top:0;">Sign in</h2>
            <p class="chb-admin-login-card-desc">Use your merchant email and password.</p>

            <?php if ($error !== ''): ?><p class="msg err"><?= h($error) ?></p><?php endif; ?>

            <form method="post" action="">
                <label>Email
                    <input type="email" name="email" required autocomplete="username" value="<?= h((string) ($_POST['email'] ?? '')) ?>">
                </label>
                <label>Password
                    <input type="password" name="password" required autocomplete="current-password">
                </label>
                <button type="submit" class="chb-submit">Sign in</button>
            </form>
        </div>

        <p class="chb-admin-login-back"><a href="/">← Back to site</a></p>
    </main>
</body>
</html>
