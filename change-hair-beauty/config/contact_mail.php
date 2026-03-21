<?php

declare(strict_types=1);

/**
 * Contact form email — uses PHP mail() with Reply-To set to the visitor.
 * Set CONTACT_MAIL_TO / CONTACT_MAIL_FROM in .env for production (see .env.example).
 */

function chb_contact_recipient_email(): string
{
    $fromEnv = getenv('CONTACT_MAIL_TO');
    if (is_string($fromEnv) && $fromEnv !== '') {
        $t = trim($fromEnv);
        if (filter_var($t, FILTER_VALIDATE_EMAIL)) {
            return $t;
        }
    }

    $salon = require __DIR__ . '/salon_data.php';
    $pub = trim((string) ($salon['contact_email'] ?? ''));

    return filter_var($pub, FILTER_VALIDATE_EMAIL) ? $pub : '';
}

/**
 * “From” must be a mailbox your host allows (often same domain as the site).
 */
function chb_contact_from_header(): string
{
    $fromEnv = getenv('CONTACT_MAIL_FROM');
    if (is_string($fromEnv) && $fromEnv !== '') {
        $t = trim($fromEnv);
        if (filter_var($t, FILTER_VALIDATE_EMAIL)) {
            return 'Change Hair & Beauty <' . $t . '>';
        }
    }

    return 'Change Hair & Beauty <noreply@changehairbeauty.local>';
}

function chb_mail_send_plain(string $to, string $subject, string $body, string $replyTo): bool
{
    if ($to === '' || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
        return false;
    }

    $from = chb_contact_from_header();
    $encodedSubject = '=?UTF-8?B?' . base64_encode($subject) . '?=';
    $headers = [
        'MIME-Version: 1.0',
        'Content-Type: text/plain; charset=UTF-8',
        'From: ' . $from,
        'Reply-To: ' . $replyTo,
        'X-Mailer: PHP/' . PHP_VERSION,
    ];

    return @mail($to, $encodedSubject, $body, implode("\r\n", $headers));
}

function chb_send_contact_message(
    string $visitorName,
    string $visitorEmail,
    string $visitorPhone,
    string $visitorMessage
): bool {
    $to = chb_contact_recipient_email();
    if ($to === '') {
        return false;
    }

    $subject = 'Website contact: ' . mb_substr(preg_replace('/\s+/', ' ', $visitorName), 0, 80);
    $body = "New message from the website contact form.\r\n\r\n";
    $body .= 'Name: ' . $visitorName . "\r\n";
    $body .= 'Email: ' . $visitorEmail . "\r\n";
    $body .= 'Phone: ' . $visitorPhone . "\r\n\r\n";
    $body .= "Message:\r\n" . $visitorMessage . "\r\n";

    return chb_mail_send_plain($to, $subject, $body, $visitorEmail);
}

/**
 * Email salon when a client confirms a dashboard booking (MySQL slot).
 */
function chb_send_booking_request_email(
    string $clientName,
    string $clientEmail,
    string $clientPhone,
    string $dateYmd,
    string $timeHi,
    string $servicesSummary
): bool {
    $to = chb_contact_recipient_email();
    if ($to === '') {
        return false;
    }

    $ts = strtotime($dateYmd . ' ' . $timeHi . ':00');
    $timePretty = $ts ? date('g:i A', $ts) : $timeHi;

    $subject = 'Appointment request: ' . $dateYmd . ' ' . $timePretty;
    $body = "New appointment booking from the website dashboard.\r\n\r\n";
    $body .= 'Name: ' . $clientName . "\r\n";
    $body .= 'Email: ' . $clientEmail . "\r\n";
    $body .= 'Phone: ' . ($clientPhone !== '' ? $clientPhone : '—') . "\r\n\r\n";
    $body .= 'Date: ' . $dateYmd . "\r\n";
    $body .= 'Time: ' . $timePretty . " ({$timeHi})\r\n\r\n";
    $body .= "Services:\r\n" . $servicesSummary . "\r\n";

    return chb_mail_send_plain($to, $subject, $body, $clientEmail);
}
