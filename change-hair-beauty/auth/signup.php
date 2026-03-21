<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';

/**
 * @return array{ok: bool, error?: string}
 */
function register_user(string $name, string $email, string $password): array
{
    $name = trim($name);
    $email = strtolower(trim($email));

    if ($name === '' || $email === '' || $password === '') {
        return ['ok' => false, 'error' => 'All fields are required.'];
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['ok' => false, 'error' => 'Invalid email address.'];
    }

    if (strlen($password) < 8) {
        return ['ok' => false, 'error' => 'Password must be at least 8 characters.'];
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    if ($hash === false) {
        return ['ok' => false, 'error' => 'Could not hash password.'];
    }

    $pdo = db();
    $stmt = $pdo->prepare('INSERT INTO users (name, email, password) VALUES (:name, :email, :password)');
    try {
        $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':password' => $hash,
        ]);
    } catch (PDOException $e) {
        if ((int) $e->errorInfo[1] === 1062) {
            return ['ok' => false, 'error' => 'That email is already registered.'];
        }
        throw $e;
    }

    return ['ok' => true];
}
