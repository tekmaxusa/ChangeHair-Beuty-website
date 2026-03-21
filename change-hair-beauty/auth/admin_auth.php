<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/signup.php';

/**
 * Create a merchant (admin) from /admin/login.php sign-up form.
 * If ADMIN_SETUP_CODE is set in the environment, it must match (recommended on public URLs).
 *
 * @return array{ok: bool, error?: string}
 */
function chb_create_merchant_from_login_signup(string $name, string $email, string $password, string $setupCodeInput): array
{
    $required = getenv('ADMIN_SETUP_CODE');
    if (is_string($required) && trim($required) !== '') {
        if (!hash_equals(trim($required), trim($setupCodeInput))) {
            return ['ok' => false, 'error' => 'Invalid setup code.'];
        }
    }

    return admin_create_user_account($name, $email, $password, 'admin');
}

/**
 * Merchant login — only users with role = admin (same `users` table as clients).
 *
 * @return array{ok: bool, error?: string}
 */
function login_admin_user(string $email, string $password): array
{
    session_bootstrap();

    $email = strtolower(trim($email));
    if ($email === '' || $password === '') {
        return ['ok' => false, 'error' => 'Email and password are required.'];
    }

    $pdo = db();
    $stmt = $pdo->prepare('SELECT id, name, email, password, role FROM users WHERE email = :email LIMIT 1');
    $stmt->execute([':email' => $email]);
    $row = $stmt->fetch();

    if (!$row || empty($row['password'])) {
        return ['ok' => false, 'error' => 'Invalid email or password.'];
    }

    if ((string) ($row['role'] ?? '') !== 'admin') {
        return ['ok' => false, 'error' => 'This account is not authorized for the merchant dashboard.'];
    }

    if (!password_verify($password, $row['password'])) {
        return ['ok' => false, 'error' => 'Invalid email or password.'];
    }

    session_regenerate_id(true);
    $_SESSION['user_id'] = (int) $row['id'];
    $_SESSION['user_name'] = $row['name'];
    $_SESSION['user_email'] = $row['email'];
    $_SESSION['user_role'] = 'admin';

    return ['ok' => true];
}

function require_admin(): void
{
    session_bootstrap();
    if (empty($_SESSION['user_id']) || (string) ($_SESSION['user_role'] ?? '') !== 'admin') {
        header('Location: /admin/login.php');
        exit;
    }
}
