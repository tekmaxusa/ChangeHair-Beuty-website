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

$setupCodeRequired = is_string(getenv('ADMIN_SETUP_CODE')) && trim((string) getenv('ADMIN_SETUP_CODE')) !== '';

$error = '';
$signupError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = (string) ($_POST['action'] ?? '');

    if ($action === 'merchant_signup') {
        $name = (string) ($_POST['name'] ?? '');
        $email = (string) ($_POST['email'] ?? '');
        $password = (string) ($_POST['password'] ?? '');
        $password2 = (string) ($_POST['password_confirm'] ?? '');
        $setupCode = (string) ($_POST['setup_code'] ?? '');

        if ($password !== $password2) {
            $signupError = 'Passwords do not match.';
        } else {
            $r = chb_create_merchant_from_login_signup($name, $email, $password, $setupCode);
            if (!$r['ok']) {
                $signupError = $r['error'] ?? 'Could not create account.';
            } else {
                $login = login_admin_user($email, $password);
                if ($login['ok']) {
                    header('Location: /admin/');
                    exit;
                }
                $signupError = 'Account created but sign-in failed. Try signing in with your email and password.';
            }
        }
    } elseif ($action === 'login') {
        $email = (string) ($_POST['email'] ?? '');
        $password = (string) ($_POST['password'] ?? '');
        $r = login_admin_user($email, $password);
        if ($r['ok']) {
            header('Location: /admin/');
            exit;
        }
        $error = $r['error'] ?? 'Login failed.';
    }
}

$signupModalOpen = $signupError !== '';

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
<body class="chb-body chb-subpage chb-admin-login-page<?= $signupModalOpen ? ' chb-modal-open' : '' ?>">
    <main class="chb-page-shell chb-admin-login-shell">
        <div class="chb-admin-login-brand">
            <p class="chb-kicker chb-admin-login-kicker">Merchant</p>
            <h1 class="chb-admin-login-title">Merchant dashboard</h1>
            <p class="chb-admin-login-lead">Sign in below. New merchants: use <strong>Sign up</strong> to create an admin account.</p>
        </div>

        <div class="chb-panel chb-admin-login-single">
            <h2 class="chb-admin-login-card-title" style="margin-top:0;">Sign in</h2>
            <p class="chb-admin-login-card-desc">Use your merchant email and password.</p>

            <?php if ($error !== ''): ?><p class="msg err"><?= h($error) ?></p><?php endif; ?>

            <form method="post" action="">
                <input type="hidden" name="action" value="login">
                <label>Email
                    <input type="email" name="email" required autocomplete="username" value="<?= h((string) ($_POST['email'] ?? '')) ?>">
                </label>
                <label>Password
                    <input type="password" name="password" required autocomplete="current-password">
                </label>
                <button type="submit" class="chb-submit">Sign in</button>
            </form>

            <div class="chb-admin-login-signup-row">
                New merchant?
                <button type="button" class="chb-admin-login-signup-btn chb-admin-login-signup-btn--button" data-chb-admin-open-signup>Sign up</button>
            </div>
        </div>

        <p class="chb-admin-login-back"><a href="/">← Back to site</a></p>
    </main>

    <div id="chb-admin-signup-modal" class="chb-modal chb-admin-signup-modal" role="presentation" <?= $signupModalOpen ? '' : 'hidden' ?> aria-hidden="<?= $signupModalOpen ? 'false' : 'true' ?>">
        <div class="chb-modal__backdrop" data-chb-admin-modal-close tabindex="-1"></div>
        <div class="chb-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="merchant-signup-heading">
            <div class="chb-modal__head">
                <h2 id="merchant-signup-heading" class="chb-modal__title">Sign up</h2>
                <button type="button" class="chb-modal__close" data-chb-admin-modal-close aria-label="Close">&times;</button>
            </div>
            <div class="chb-modal__content chb-admin-signup-modal__content">
                <p class="chb-admin-login-card-desc" style="margin-top:0;">Creates a new <strong>admin</strong> user and opens the dashboard when successful.</p>
                <?php if ($signupError !== ''): ?><p class="msg err"><?= h($signupError) ?></p><?php endif; ?>

                <form method="post" action="" class="chb-admin-bootstrap-form">
                    <input type="hidden" name="action" value="merchant_signup">
                    <label>Name
                        <input type="text" name="name" required autocomplete="name" maxlength="255" value="<?= h((string) ($_POST['name'] ?? '')) ?>">
                    </label>
                    <label>Email
                        <input type="email" name="email" required autocomplete="email" value="<?= h((string) ($_POST['email'] ?? '')) ?>">
                    </label>
                    <label>Password
                        <input type="password" name="password" required minlength="8" autocomplete="new-password">
                    </label>
                    <label>Confirm password
                        <input type="password" name="password_confirm" required minlength="8" autocomplete="new-password">
                    </label>
                    <?php if ($setupCodeRequired): ?>
                        <label>Setup code
                            <input type="password" name="setup_code" required autocomplete="off" placeholder="From .env ADMIN_SETUP_CODE">
                        </label>
                        <p class="chb-hint" style="margin-top:0.35rem;">Required when <code>ADMIN_SETUP_CODE</code> is set in <code>.env</code>.</p>
                    <?php endif; ?>
                    <button type="submit" class="chb-submit">Create account &amp; continue</button>
                </form>

                <p class="chb-admin-signup-modal__back">
                    <button type="button" class="chb-admin-signup-modal__back-btn" data-chb-admin-modal-close>← Back</button>
                </p>
            </div>
        </div>
    </div>

    <script>
    (function () {
        var modal = document.getElementById('chb-admin-signup-modal');
        if (!modal) return;

        function openModal() {
            modal.removeAttribute('hidden');
            modal.setAttribute('aria-hidden', 'false');
            document.body.classList.add('chb-modal-open');
        }

        function closeModal() {
            modal.setAttribute('hidden', '');
            modal.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('chb-modal-open');
        }

        document.querySelectorAll('[data-chb-admin-open-signup]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                openModal();
            });
        });

        modal.querySelectorAll('[data-chb-admin-modal-close]').forEach(function (el) {
            el.addEventListener('click', function () {
                closeModal();
            });
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && !modal.hasAttribute('hidden')) {
                closeModal();
            }
        });

        <?php if ($signupModalOpen): ?>
        openModal();
        <?php endif; ?>
    })();
    </script>
</body>
</html>
