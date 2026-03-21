<?php

declare(strict_types=1);

require __DIR__ . '/layout.php';
require_once dirname(__DIR__) . '/auth/login.php';
require_once dirname(__DIR__) . '/auth/google_oauth.php';
require_once __DIR__ . '/partials/redirect.php';

$next = chb_safe_next((string) ($_POST['next'] ?? $_GET['next'] ?? ''));

if ($loggedIn) {
    header('Location: ' . $next);
    exit;
}

$error = '';
$googleErr = (string) ($_GET['google_err'] ?? '');
if ($googleErr === 'not_configured') {
    $error = 'Google sign-in is not configured yet (add GOOGLE_CLIENT_ID and GOOGLE_CLIENT_SECRET).';
} elseif ($googleErr === 'invalid_state') {
    $error = 'Google sign-in session expired. Please try again.';
} elseif ($googleErr === 'auth_failed') {
    $error = 'Google sign-in failed. Please try again or use email and password.';
} elseif ($googleErr !== '' && $googleErr !== 'access_denied') {
    $error = 'Google sign-in error. Please try again.';
} elseif ($googleErr === 'access_denied') {
    $error = 'Google sign-in was cancelled.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = (string) ($_POST['email'] ?? '');
    $password = (string) ($_POST['password'] ?? '');
    $next = chb_safe_next((string) ($_POST['next'] ?? $next));
    $r = login_user($email, $password);
    if ($r['ok']) {
        header('Location: ' . $next);
        exit;
    }
    $error = $r['error'] ?? 'Login failed.';
}

$googleStart = '/google-oauth-start.php?next=' . rawurlencode($next);
$googleOAuthReady = google_oauth_configured();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log in — Change Hair &amp; Beauty</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;14..32,500;14..32,600&family=Montserrat:wght@400;500;600&family=Playfair+Display:ital,wght@0,400;0,500;0,600;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/style.css">
</head>
<body class="chb-body chb-subpage">
    <?php require __DIR__ . '/partials/site-header.php'; ?>

    <main class="chb-page-shell">
        <div class="chb-panel">
            <h1 class="chb-page-title">Sign in to your account</h1>
            <p class="chb-hint" style="margin-top:0;">Client dashboard &amp; online booking.</p>
            <?php if ($error !== ''): ?><p class="msg err"><?= h($error) ?></p><?php endif; ?>

            <form method="post" action="">
                <input type="hidden" name="next" value="<?= h($next) ?>">
                <label>Your email address
                    <input type="email" name="email" required autocomplete="username" value="<?= h((string) ($_POST['email'] ?? '')) ?>" placeholder="you@example.com">
                </label>
                <label>Your password
                    <input type="password" name="password" required autocomplete="current-password">
                </label>
                <button type="submit" class="chb-submit">Continue</button>
            </form>

            <div class="chb-oauth-or"><span>Or</span></div>

            <?php if ($googleOAuthReady): ?>
                <a class="chb-google-btn" href="<?= h($googleStart) ?>"><span class="chb-google-g">G</span> Continue with Google</a>
            <?php else: ?>
                <?php /* Same as tekmaxusa/changehair-beauty: opens Google in a new tab when OAuth is not configured */ ?>
                <a class="chb-google-btn" href="https://accounts.google.com/" target="_blank" rel="noopener noreferrer"><span class="chb-google-g">G</span> Continue with Google</a>
            <?php endif; ?>

            <p class="chb-hint">No account? <a href="/signup.php">Sign up</a></p>
        </div>
    </main>

    <script src="/js/site.js" defer></script>
    <?php require __DIR__ . '/partials/tawk.php'; ?>
</body>
</html>
