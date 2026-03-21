<?php

declare(strict_types=1);

require __DIR__ . '/layout.php';
require_once dirname(__DIR__) . '/auth/signup.php';
require_once dirname(__DIR__) . '/auth/google_oauth.php';
require_once __DIR__ . '/partials/redirect.php';

$next = chb_safe_next((string) ($_GET['next'] ?? '/dashboard/'));

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = (string) ($_POST['name'] ?? '');
    $email = (string) ($_POST['email'] ?? '');
    $password = (string) ($_POST['password'] ?? '');
    $r = register_user($name, $email, $password);
    if ($r['ok']) {
        $success = 'Account created. Log in to open your client dashboard and book.';
    } else {
        $error = $r['error'] ?? 'Signup failed.';
    }
}

$loginAfter = '/login.php?next=' . rawurlencode($next);
$googleStart = '/google-oauth-start.php?next=' . rawurlencode($next);
$googleOAuthReady = google_oauth_configured();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign up — Change Hair &amp; Beauty</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;14..32,500;14..32,600&family=Montserrat:wght@400;500;600&family=Playfair+Display:ital,wght@0,400;0,500;0,600;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/style.css">
</head>
<body class="chb-body chb-subpage">
    <?php require __DIR__ . '/partials/site-header.php'; ?>

    <main class="chb-page-shell">
        <div class="chb-panel">
            <h1 class="chb-page-title">Create account</h1>
            <?php if ($error !== ''): ?><p class="msg err"><?= h($error) ?></p><?php endif; ?>
            <?php if ($success !== ''): ?>
                <p class="msg ok"><?= h($success) ?></p>
                <p><a class="chb-btn-gold" href="<?= h($loginAfter) ?>">Log in</a></p>
            <?php else: ?>
                <form method="post" action="">
                    <label>Name
                        <input name="name" required autocomplete="name" value="<?= h((string) ($_POST['name'] ?? '')) ?>">
                    </label>
                    <label>Email
                        <input type="email" name="email" required autocomplete="email" value="<?= h((string) ($_POST['email'] ?? '')) ?>">
                    </label>
                    <label>Password
                        <input type="password" name="password" required minlength="8" autocomplete="new-password">
                    </label>
                    <button type="submit" class="chb-submit">Sign up</button>
                </form>

                <div class="chb-oauth-or"><span>Or</span></div>

                <?php if ($googleOAuthReady): ?>
                    <a class="chb-google-btn" href="<?= h($googleStart) ?>"><span class="chb-google-g">G</span> Continue with Google</a>
                <?php else: ?>
                    <?php /* tekmaxusa/changehair-beauty: window.open(accounts.google.com) — no backend OAuth */ ?>
                    <a class="chb-google-btn" href="https://accounts.google.com/" target="_blank" rel="noopener noreferrer"><span class="chb-google-g">G</span> Continue with Google</a>
                <?php endif; ?>
            <?php endif; ?>
            <p class="chb-hint">Already registered? <a href="<?= h($loginAfter) ?>">Log in</a></p>
        </div>
    </main>

    <script src="/js/site.js" defer></script>
    <?php require __DIR__ . '/partials/tawk.php'; ?>
</body>
</html>
