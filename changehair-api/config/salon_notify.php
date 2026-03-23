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
            'event' => 'contact',
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
 * New confirmed booking: merchant email + client email (PHP mail) + optional Google Apps Script webhook.
 * Script receives event=new_booking so you can send both messages from Apps Script (MailApp) if desired.
 *
 * @return bool true if at least one configured channel succeeds (mail and/or script)
 */
function chb_notify_booking_created_all_channels(
    string $clientName,
    string $clientEmail,
    string $clientPhone,
    string $serviceSummary,
    string $dateYmd,
    string $timeHi
): bool {
    $channels = chb_salon_notify_channels();
    $clientMailOk = true;
    if (filter_var($clientEmail, FILTER_VALIDATE_EMAIL)) {
        $clientMailOk = chb_send_booking_confirmation_to_client_email(
            $clientEmail,
            $clientName,
            $dateYmd,
            $timeHi,
            $serviceSummary
        );
    }

    $mailOk = true;
    if (in_array('mail', $channels, true)) {
        $mailOk = chb_send_booking_request_email(
            $clientName,
            $clientEmail,
            $clientPhone,
            $dateYmd,
            $timeHi,
            $serviceSummary
        );
    }

    $ts = strtotime($dateYmd . ' ' . $timeHi . ':00');
    $timeLabel = $ts ? date('g:i A', $ts) : $timeHi;

    $svcForScript = $serviceSummary;
    if (function_exists('mb_strlen') && mb_strlen($svcForScript) > 500) {
        $svcForScript = mb_substr($svcForScript, 0, 497) . '…';
    } elseif (strlen($svcForScript) > 500) {
        $svcForScript = substr($svcForScript, 0, 497) . '…';
    }

    $details = "NEW BOOKING (confirmed)\nClient: {$clientName}\nEmail: {$clientEmail}\nPhone: "
        . ($clientPhone !== '' ? $clientPhone : '—') . "\nDate: {$dateYmd}\nTime: {$timeLabel}\nServices: {$serviceSummary}";
    if (function_exists('mb_strlen') && mb_strlen($details) > 1800) {
        $details = mb_substr($details, 0, 1797) . '…';
    } elseif (strlen($details) > 1800) {
        $details = substr($details, 0, 1797) . '…';
    }

    $scriptOk = true;
    if (in_array('script', $channels, true)) {
        $scriptOk = chb_post_google_script_form([
            'event' => 'new_booking',
            'name' => $clientName !== '' ? $clientName : '—',
            'email' => $clientEmail !== '' ? $clientEmail : '—',
            'phone' => $clientPhone !== '' ? $clientPhone : '—',
            'service' => $svcForScript !== '' ? $svcForScript : '—',
            'date' => $dateYmd,
            'time' => $timeLabel,
            'details' => $details,
        ]);
    }

    if ($channels === []) {
        return $clientMailOk;
    }

    return $mailOk || $scriptOk || $clientMailOk;
}

/**
 * @deprecated Use chb_notify_booking_created_all_channels(); kept for legacy PHP entry points.
 */
function chb_notify_booking_salon(
    string $name,
    string $email,
    string $phone,
    string $serviceSummary,
    string $dateYmd,
    string $timeHi
): bool {
    return chb_notify_booking_created_all_channels($name, $email, $phone, $serviceSummary, $dateYmd, $timeHi);
}
