<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
require_once $root . '/config/session.php';
require_once $root . '/auth/admin_auth.php';
require_once $root . '/auth/signup.php';
require __DIR__ . '/../layout.php';

require_admin();

if (empty($_SESSION['chb_csrf_admin']) || !is_string($_SESSION['chb_csrf_admin'])) {
    $_SESSION['chb_csrf_admin'] = bin2hex(random_bytes(16));
}
$csrf = (string) $_SESSION['chb_csrf_admin'];

$msg = '';
$msgOk = true;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = (string) ($_POST['csrf'] ?? '');
    if (!hash_equals($csrf, $token)) {
        $msg = 'Invalid session token. Refresh and try again.';
        $msgOk = false;
    } else {
        $action = (string) ($_POST['action'] ?? '');
        if ($action === 'create_user') {
            $name = (string) ($_POST['name'] ?? '');
            $email = (string) ($_POST['email'] ?? '');
            $password = (string) ($_POST['password'] ?? '');
            $role = (string) ($_POST['role'] ?? 'client');
            $r = admin_create_user_account($name, $email, $password, $role);
            if ($r['ok']) {
                $msg = 'Account created.';
                $msgOk = true;
                $_SESSION['chb_csrf_admin'] = bin2hex(random_bytes(16));
                $csrf = (string) $_SESSION['chb_csrf_admin'];
            } else {
                $msg = $r['error'] ?? 'Could not create account.';
                $msgOk = false;
            }
        }
    }
}

$pdo = db();
$clients = $pdo->query(
    "SELECT id, name, email, role, created_at FROM users WHERE role = 'client' ORDER BY id DESC LIMIT 200"
)->fetchAll();
$admins = $pdo->query(
    "SELECT id, name, email, role, created_at FROM users WHERE role = 'admin' ORDER BY id DESC LIMIT 50"
)->fetchAll();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin — user accounts</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;14..32,500;14..32,600&family=Montserrat:wght@400;500;600&family=Playfair+Display:ital,wght@0,500;0,600;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/style.css">
</head>
<body class="chb-body chb-subpage chb-admin-dashboard">
    <main class="chb-page-shell chb-admin-dash-shell">
        <?php $chbAdminNavActive = 'accounts'; require __DIR__ . '/partials/admin-nav.php'; ?>

        <header class="chb-admin-pagehead">
            <p class="chb-kicker chb-admin-pagehead-kicker">Merchant</p>
            <h1 class="chb-admin-pagehead-title">Accounts</h1>
            <p class="chb-admin-pagehead-meta">
                <?= h(current_user_email()) ?>
                <span aria-hidden="true">·</span>
                <a href="/logout.php?next=<?= rawurlencode('/admin/login.php') ?>">Log out</a>
            </p>
        </header>

        <?php if ($msg !== ''): ?>
            <p class="msg <?= $msgOk ? 'ok' : 'err' ?>"><?= h($msg) ?></p>
        <?php endif; ?>

        <div class="chb-panel chb-dashboard-panel">
            <h2 class="chb-section-title">Create account</h2>
            <p class="chb-book-intro" style="margin-top:0;">New users can log in at <a href="/login.php">/login.php</a> with the password you set. Choose <strong>Client</strong> for customers or <strong>Admin</strong> for another merchant login.</p>

            <form method="post" class="chb-admin-user-form" style="max-width:22rem;">
                <input type="hidden" name="csrf" value="<?= h($csrf) ?>">
                <input type="hidden" name="action" value="create_user">
                <label class="chb-book-label" style="margin-bottom:1rem;">
                    <span>Name</span>
                    <input type="text" name="name" required maxlength="255" autocomplete="name" value="<?= h((string) ($_POST['name'] ?? '')) ?>">
                </label>
                <label class="chb-book-label" style="margin-bottom:1rem;">
                    <span>Email</span>
                    <input type="email" name="email" required maxlength="254" autocomplete="email" value="<?= h((string) ($_POST['email'] ?? '')) ?>">
                </label>
                <label class="chb-book-label" style="margin-bottom:1rem;">
                    <span>Password (min 8 characters)</span>
                    <input type="password" name="password" required minlength="8" autocomplete="new-password">
                </label>
                <label class="chb-book-label" style="margin-bottom:1rem;">
                    <span>Role</span>
                    <select name="role" class="chb-book-select">
                        <option value="client" <?= (string) ($_POST['role'] ?? '') !== 'admin' ? 'selected' : '' ?>>Client</option>
                        <option value="admin" <?= (string) ($_POST['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Admin</option>
                    </select>
                </label>
                <button type="submit" class="chb-submit">Create account</button>
            </form>
        </div>

        <div class="chb-panel chb-dashboard-panel" style="margin-top:1.5rem;">
            <h2 class="chb-section-title">All client accounts</h2>
            <p class="chb-book-intro" style="margin-top:0;">Customers who can log in at <a href="/login.php">/login.php</a> and book appointments. Use the form above to add a client or another admin.</p>
            <?php if ($clients === []): ?>
                <p class="chb-hint">No client accounts yet.</p>
            <?php else: ?>
                <div class="chb-table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($clients as $u): ?>
                                <tr>
                                    <td><?= (int) $u['id'] ?></td>
                                    <td><?= h((string) $u['name']) ?></td>
                                    <td><?= h((string) $u['email']) ?></td>
                                    <td><?= h((string) $u['created_at']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <div class="chb-panel chb-dashboard-panel" style="margin-top:1.5rem;">
            <h2 class="chb-section-title">Merchant admins</h2>
            <p class="chb-book-intro" style="margin-top:0;">Users who can access <a href="/admin/">this dashboard</a>.</p>
            <?php if ($admins === []): ?>
                <p class="chb-hint">No admin users (unexpected).</p>
            <?php else: ?>
                <div class="chb-table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($admins as $u): ?>
                                <tr>
                                    <td><?= (int) $u['id'] ?></td>
                                    <td><?= h((string) $u['name']) ?></td>
                                    <td><?= h((string) $u['email']) ?></td>
                                    <td><?= h((string) $u['created_at']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
