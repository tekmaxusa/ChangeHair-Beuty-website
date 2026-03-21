<?php

declare(strict_types=1);

require_once __DIR__ . '/contact_mail.php';
require_once __DIR__ . '/google_script_notify.php';

/**
 * @return list<string> e.g. ['mail', 'script']
 */
function chb_salon_notify_channels(): array
{
    $c = [];
    if (chb_contact_recipient_email() !== '') {
        $c[] = 'mail';
    }
    if (chb_google_script_url() !== '') {
        $c[] = 'script';
    }

    return $c;
}

function chb_salon_notify_configured(): bool
{
    return chb_salon_notify_channels() !== [];
}

/**
 * Contact form: mail + optional Google Script (reference sends message in `time`, service = Contact Request).
 *
 * @return bool true if at least one configured channel succeeds
 */
function chb_notify_contact_salon(string $name, string $email, string $phone, string $message): bool
{
    $channels = chb_salon_notify_channels();
    if ($channels === []) {
        return false;
    }

    $mailOk = true;
    if (in_array('mail', $channels, true)) {
        $mailOk = chb_send_contact_message($name, $email, $phone, $message);
    }

    $scriptOk = true;
    if (in_array('script', $channels, true)) {
        $scriptOk = chb_post_google_script_form([
            'name' => $name,
            'email' => $email,
            'phone' => $phone !== '' ? $phone : '—',
            'service' => 'Contact Request',
            'date' => (new DateTimeImmutable('today'))->format('Y-m-d'),
            'time' => $message,
        ]);
    }

    return $mailOk || $scriptOk;
}

/**
 * Dashboard booking confirmed: same field shape as reference BookingModal submit.
 *
 * @return bool true if at least one configured channel succeeds
 */
function chb_notify_booking_salon(
    string $name,
    string $email,
    string $phone,
    string $serviceSummary,
    string $dateYmd,
    string $timeHi
): bool {
    $channels = chb_salon_notify_channels();
    if ($channels === []) {
        return false;
    }

    $ts = strtotime($dateYmd . ' ' . $timeHi . ':00');
    $timeLabel = $ts ? date('g:i A', $ts) : $timeHi;

    $mailOk = true;
    if (in_array('mail', $channels, true)) {
        $mailOk = chb_send_booking_request_email($name, $email, $phone, $dateYmd, $timeHi, $serviceSummary);
    }

    $svcForScript = $serviceSummary;
    if (function_exists('mb_strlen') && mb_strlen($svcForScript) > 500) {
        $svcForScript = mb_substr($svcForScript, 0, 497) . '…';
    } elseif (strlen($svcForScript) > 500) {
        $svcForScript = substr($svcForScript, 0, 497) . '…';
    }

    $scriptOk = true;
    if (in_array('script', $channels, true)) {
        $scriptOk = chb_post_google_script_form([
            'name' => $name !== '' ? $name : '—',
            'email' => $email !== '' ? $email : '—',
            'phone' => $phone !== '' ? $phone : '—',
            'service' => $svcForScript !== '' ? $svcForScript : '—',
            'date' => $dateYmd,
            'time' => $timeLabel,
        ]);
    }

    return $mailOk || $scriptOk;
}
