<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/session.php';
require_once dirname(__DIR__) . '/config/salon_notify.php';

session_bootstrap();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /#contact', true, 303);
    exit;
}

$honeypot = trim((string) ($_POST['website'] ?? ''));
if ($honeypot !== '') {
    header('Location: /?contact=sent#contact', true, 303);
    exit;
}

$csrfPost = (string) ($_POST['csrf'] ?? '');
$csrfSess = (string) ($_SESSION['chb_csrf_contact'] ?? '');
if ($csrfSess === '' || !hash_equals($csrfSess, $csrfPost)) {
    $_SESSION['chb_contact_flash'] = ['ok' => false, 'message' => 'Something went wrong. Please refresh the page and try again.'];
    header('Location: /#contact', true, 303);
    exit;
}

$last = (int) ($_SESSION['chb_contact_last_send'] ?? 0);
if ($last > 0 && time() - $last < 90) {
    $_SESSION['chb_contact_flash'] = ['ok' => false, 'message' => 'Please wait a minute before sending another message.'];
    header('Location: /#contact', true, 303);
    exit;
}

$name = trim((string) ($_POST['name'] ?? ''));
$email = trim((string) ($_POST['email'] ?? ''));
$phone = trim((string) ($_POST['phone'] ?? ''));
$message = trim((string) ($_POST['message'] ?? ''));

if ($name === '' || $email === '' || $message === '') {
    $_SESSION['chb_contact_flash'] = ['ok' => false, 'message' => 'Please fill in your name, email, and message.'];
    header('Location: /#contact', true, 303);
    exit;
}

if (mb_strlen($name) > 120 || mb_strlen($email) > 254 || mb_strlen($phone) > 40 || mb_strlen($message) > 6000) {
    $_SESSION['chb_contact_flash'] = ['ok' => false, 'message' => 'One of the fields is too long. Please shorten and try again.'];
    header('Location: /#contact', true, 303);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['chb_contact_flash'] = ['ok' => false, 'message' => 'Please enter a valid email address.'];
    header('Location: /#contact', true, 303);
    exit;
}

if (!chb_salon_notify_configured()) {
    $_SESSION['chb_contact_flash'] = ['ok' => false, 'message' => 'Contact is not configured yet. Please call us or use live chat.'];
    header('Location: /#contact', true, 303);
    exit;
}

$ok = chb_notify_contact_salon($name, $email, $phone, $message);

if ($ok) {
    $_SESSION['chb_contact_last_send'] = time();
    $_SESSION['chb_csrf_contact'] = bin2hex(random_bytes(16));
    $_SESSION['chb_contact_flash'] = ['ok' => true, 'message' => 'Thank you — your message was sent. We’ll get back to you soon.'];
} else {
    $_SESSION['chb_contact_flash'] = ['ok' => false, 'message' => 'We could not deliver your message. Please email us directly or use live chat.'];
}

header('Location: /?contact=sent#contact', true, 303);
exit;
