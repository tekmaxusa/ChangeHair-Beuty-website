<?php

declare(strict_types=1);

/**
 * Contact + booking email (PHP mail()). HTML + plain multipart.
 * Merchant vs client: different header treatments; tables + inline CSS for clients.
 */

function chb_h(string $s): string
{
    return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function chb_email_nl2br(string $escapedPlain): string
{
    return str_replace(["\r\n", "\r", "\n"], '<br>', $escapedPlain);
}

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

function chb_booking_format_time_pretty(string $dateYmd, string $timeHi): string
{
    $ts = strtotime($dateYmd . ' ' . $timeHi . ':00');

    return $ts ? date('g:i A', $ts) : $timeHi;
}

function chb_booking_format_date_pretty(string $dateYmd): string
{
    $ts = strtotime($dateYmd . ' 12:00:00');

    return $ts ? date('l, F j, Y', $ts) : $dateYmd;
}

/** @return array{location:string,address:string,phone:string} */
function chb_email_salon_venue_bits(): array
{
    static $cache = null;
    if ($cache !== null) {
        return $cache;
    }
    $salon = require __DIR__ . '/salon_data.php';
    $cache = [
        'location' => trim((string) ($salon['location_name'] ?? '')),
        'address' => trim((string) ($salon['address'] ?? '')),
        'phone' => trim((string) ($salon['phone'] ?? '')),
    ];

    return $cache;
}

function chb_mail_send_multipart(
    string $to,
    string $subject,
    string $plainBody,
    string $htmlBody,
    string $replyTo
): bool {
    if ($to === '' || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
        return false;
    }

    $rt = $replyTo !== '' && filter_var($replyTo, FILTER_VALIDATE_EMAIL) ? $replyTo : $to;
    $from = chb_contact_from_header();
    $encodedSubject = '=?UTF-8?B?' . base64_encode($subject) . '?=';
    $boundary = 'chb_' . bin2hex(random_bytes(8));

    $plainBody = str_replace(["\r\n", "\r"], "\n", $plainBody);
    $htmlBody = str_replace(["\r\n", "\r"], "\n", $htmlBody);

    $body = '--' . $boundary . "\r\n";
    $body .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $body .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
    $body .= $plainBody . "\r\n\r\n";
    $body .= '--' . $boundary . "\r\n";
    $body .= "Content-Type: text/html; charset=UTF-8\r\n";
    $body .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
    $body .= $htmlBody . "\r\n\r\n";
    $body .= '--' . $boundary . "--\r\n";

    $headers = [
        'MIME-Version: 1.0',
        'Content-Type: multipart/alternative; boundary="' . $boundary . '"',
        'From: ' . $from,
        'Reply-To: ' . $rt,
        'X-Mailer: PHP/' . PHP_VERSION,
    ];

    return @mail($to, $encodedSubject, $body, implode("\r\n", $headers));
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

/** @param 'accent'|'success'|'warning'|'security'|'merchant' $tone */
function chb_email_badge(string $label, string $tone = 'accent'): string
{
    $styles = [
        'accent' => 'background-color:#fffbeb;color:#92400e;border:1px solid #fcd34d;',
        'success' => 'background-color:#ecfdf5;color:#047857;border:1px solid #6ee7b7;',
        'warning' => 'background-color:#fff1f2;color:#be123c;border:1px solid #fda4af;',
        'security' => 'background-color:#f1f5f9;color:#1e293b;border:1px solid #94a3b8;',
        'merchant' => 'background-color:#422006;color:#fde68a;border:1px solid #ca8a04;',
    ];
    $css = $styles[$tone] ?? $styles['accent'];

    return '<span style="display:inline-block;padding:7px 16px;border-radius:999px;font-size:10px;'
        . 'font-weight:700;letter-spacing:0.16em;text-transform:uppercase;font-family:\'Segoe UI\',Tahoma,Geneva,Verdana,sans-serif;'
        . $css . '">' . chb_h($label) . '</span>';
}

function chb_email_section_rule(string $label): string
{
    return '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 18px 0;">'
        . '<tr><td style="padding:0 0 10px 0;border-bottom:1px solid #e7e5e4;">'
        . '<p style="margin:0;font-size:10px;font-weight:700;letter-spacing:0.22em;text-transform:uppercase;'
        . 'color:#a8a29e;font-family:\'Segoe UI\',Tahoma,Geneva,Verdana,sans-serif;">' . chb_h($label) . '</p>'
        . '</td></tr></table>';
}

/**
 * Large featured date/time — merchant: dark luxe; client: warm editorial.
 *
 * @param 'merchant'|'client' $audience
 */
function chb_email_featured_datetime_html(string $datePretty, string $timePretty, string $audience): string
{
    if ($audience === 'merchant') {
        return '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 28px 0;border-collapse:separate;">'
            . '<tr><td style="padding:0 0 0 4px;background-color:#c5a059;border-radius:14px 0 0 14px;width:4px;font-size:0;line-height:0;">&nbsp;</td>'
            . '<td style="padding:26px 28px;background-color:#1c1917;border-radius:0 14px 14px 0;">'
            . '<p style="margin:0;font-size:10px;font-weight:700;letter-spacing:0.28em;text-transform:uppercase;'
            . 'color:#a8a29e;font-family:\'Segoe UI\',Tahoma,Geneva,Verdana,sans-serif;">Appointment time</p>'
            . '<p style="margin:10px 0 4px 0;font-size:22px;line-height:1.25;color:#fafaf9;font-family:Georgia,\'Times New Roman\',serif;">'
            . chb_h($datePretty) . '</p>'
            . '<p style="margin:0;font-size:20px;color:#d4a853;font-family:Georgia,\'Times New Roman\',serif;font-weight:400;">'
            . chb_h($timePretty) . '</p>'
            . '</td></tr></table>';
    }

    return '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 28px 0;">'
        . '<tr><td style="padding:28px 30px;background-color:#fdfbf7;border:1px solid #e8dfc8;border-radius:16px;">'
        . '<p style="margin:0;font-size:10px;font-weight:700;letter-spacing:0.28em;text-transform:uppercase;'
        . 'color:#a18b5c;font-family:\'Segoe UI\',Tahoma,Geneva,Verdana,sans-serif;">Your reservation</p>'
        . '<p style="margin:12px 0 6px 0;font-size:26px;line-height:1.2;color:#1c1917;font-family:Georgia,\'Times New Roman\',serif;">'
        . chb_h($datePretty) . '</p>'
        . '<p style="margin:0;font-size:22px;color:#9a7b35;font-family:Georgia,\'Times New Roman\',serif;">'
        . chb_h($timePretty) . '</p>'
        . '</td></tr></table>';
}

function chb_email_cta_button(string $href, string $label, string $style = 'dark'): string
{
    $h = chb_h($href);
    $l = chb_h($label);
    if ($style === 'gold') {
        return '<table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 10px 0;">'
            . '<tr><td style="border-radius:12px;background-color:#b8860b;background:linear-gradient(180deg,#d4a853 0%,#b8860b 100%);padding:2px;">'
            . '<table role="presentation" cellpadding="0" cellspacing="0" width="100%"><tr>'
            . '<td style="border-radius:10px;background-color:#1c1917;text-align:center;">'
            . '<a href="' . $h . '" target="_blank" style="display:inline-block;padding:16px 36px;font-family:\'Segoe UI\',Tahoma,Geneva,Verdana,sans-serif;'
            . 'font-size:12px;font-weight:700;letter-spacing:0.14em;text-transform:uppercase;color:#fefce8;text-decoration:none;">'
            . $l . '</a></td></tr></table></td></tr></table>';
    }

    return '<table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 10px 0;">'
        . '<tr><td style="border-radius:12px;background-color:#1c1917;">'
        . '<a href="' . $h . '" target="_blank" style="display:inline-block;padding:17px 36px;font-family:\'Segoe UI\',Tahoma,Geneva,Verdana,sans-serif;'
        . 'font-size:12px;font-weight:700;letter-spacing:0.14em;text-transform:uppercase;color:#fafaf9;text-decoration:none;border-radius:12px;">'
        . $l . '</a></td></tr></table>';
}

/**
 * @param 'merchant'|'client' $audience
 */
function chb_email_detail_rows_html(array $pairs, string $audience = 'client'): string
{
    $bg = $audience === 'merchant' ? '#f4f4f5' : '#fafaf9';
    $border = $audience === 'merchant' ? '#d4d4d8' : '#e7e5e4';
    $labelColor = $audience === 'merchant' ? '#52525b' : '#78716c';
    $valueColor = $audience === 'merchant' ? '#18181b' : '#1c1917';

    $rows = '';
    foreach ($pairs as $label => $value) {
        $rows .= '<tr>'
            . '<td style="padding:16px 18px;border-bottom:1px solid ' . $border . ';vertical-align:top;width:32%;">'
            . '<p style="margin:0;font-size:10px;font-weight:700;letter-spacing:0.18em;text-transform:uppercase;'
            . 'color:' . $labelColor . ';font-family:\'Segoe UI\',Tahoma,Geneva,Verdana,sans-serif;">' . chb_h($label) . '</p>'
            . '</td>'
            . '<td style="padding:16px 18px;border-bottom:1px solid ' . $border . ';vertical-align:top;">'
            . '<p style="margin:0;font-size:16px;line-height:1.55;color:' . $valueColor . ';font-family:Georgia,\'Times New Roman\',serif;">'
            . chb_h($value) . '</p>'
            . '</td></tr>';
    }

    return '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" '
        . 'style="border-collapse:collapse;background-color:' . $bg . ';border:1px solid ' . $border . ';border-radius:16px;overflow:hidden;">'
        . $rows . '</table>';
}

function chb_email_quote_block(string $escapedHtmlBody): string
{
    return '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:0;">'
        . '<tr><td style="padding:22px 24px;background-color:#fafaf9;border-left:4px solid #c5a059;'
        . 'border-radius:0 14px 14px 0;border:1px solid #e7e5e4;border-left-width:4px;">'
        . '<p style="margin:0;font-size:15px;line-height:1.7;color:#3f3f46;font-family:Georgia,\'Times New Roman\',serif;">'
        . $escapedHtmlBody . '</p></td></tr></table>';
}

/** Salon strip for client emails (address + phone). */
function chb_email_salon_visit_strip_html(): string
{
    $v = chb_email_salon_venue_bits();
    if ($v['address'] === '' && $v['phone'] === '') {
        return '';
    }
    $locLine = $v['location'] !== '' ? chb_h($v['location']) . ' · ' : '';
    $addr = chb_h($v['address']);
    $phone = chb_h($v['phone']);

    return '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-top:32px;">'
        . '<tr><td style="padding:22px 24px;background-color:#1c1917;border-radius:14px;">'
        . '<p style="margin:0 0 6px 0;font-size:10px;font-weight:700;letter-spacing:0.22em;text-transform:uppercase;'
        . 'color:#a8a29e;font-family:\'Segoe UI\',Tahoma,Geneva,Verdana,sans-serif;">Visit us</p>'
        . '<p style="margin:0;font-size:15px;line-height:1.55;color:#fafaf9;font-family:Georgia,\'Times New Roman\',serif;">'
        . $locLine . $addr . '</p>'
        . ($phone !== '' ? '<p style="margin:10px 0 0 0;font-size:14px;color:#d4a853;font-family:\'Segoe UI\',Tahoma,Geneva,Verdana,sans-serif;">'
            . $phone . '</p>' : '')
        . '</td></tr></table>';
}

/**
 * @param 'merchant'|'client' $audience
 */
function chb_email_brand_wrap(string $preheader, string $innerHtml, string $audience = 'client'): string
{
    $pre = chb_h($preheader);
    $year = (string) (int) date('Y');
    $venue = chb_email_salon_venue_bits();
    $sub = $venue['location'] !== ''
        ? chb_h($venue['location'])
        : ($venue['address'] !== '' ? chb_h($venue['address']) : 'Professional hair care & color');

    $footerClient = '<p style="margin:0 0 10px 0;font-size:12px;line-height:1.65;color:#78716c;font-family:\'Segoe UI\',Tahoma,Geneva,Verdana,sans-serif;">'
        . 'You received this because you used our website. Please do not send passwords or card numbers by email.</p>'
        . '<p style="margin:0;font-size:11px;color:#a8a29e;font-family:\'Segoe UI\',Tahoma,Geneva,Verdana,sans-serif;">'
        . '&copy; ' . chb_h($year) . ' Change Hair & Beauty</p>';

    $footerMerchant = '<p style="margin:0 0 10px 0;font-size:12px;line-height:1.65;color:#a8a29e;font-family:\'Segoe UI\',Tahoma,Geneva,Verdana,sans-serif;">'
        . 'Operational message from your booking system. Not intended for forwarding to clients unless you edit the content.</p>'
        . '<p style="margin:0;font-size:11px;color:#737373;font-family:\'Segoe UI\',Tahoma,Geneva,Verdana,sans-serif;">'
        . '&copy; ' . chb_h($year) . ' Change Hair & Beauty · Merchant notifications</p>';

    $footer = $audience === 'merchant' ? $footerMerchant : $footerClient;

    if ($audience === 'merchant') {
        $header = '<tr><td style="padding:0;background-color:#0c0a09;border-radius:20px 20px 0 0;">'
            . '<table role="presentation" width="100%" cellpadding="0" cellspacing="0"><tr>'
            . '<td style="height:4px;background-color:#c5a059;font-size:0;line-height:0;border-radius:20px 20px 0 0;">&nbsp;</td></tr>'
            . '<tr><td style="padding:32px 40px 28px 40px;">'
            . '<p style="margin:0 0 6px 0;font-size:10px;font-weight:700;letter-spacing:0.35em;text-transform:uppercase;'
            . 'color:#a8a29e;font-family:\'Segoe UI\',Tahoma,Geneva,Verdana,sans-serif;">Operations</p>'
            . '<p style="margin:0;font-size:28px;line-height:1.15;color:#fafaf9;font-family:Georgia,\'Times New Roman\',serif;font-weight:400;">'
            . 'Change Hair <span style="color:#d4a853;">&amp;</span> Beauty</p>'
            . '<p style="margin:12px 0 0 0;font-size:13px;line-height:1.5;color:#78716c;font-family:\'Segoe UI\',Tahoma,Geneva,Verdana,sans-serif;">'
            . 'Merchant dashboard · internal notification</p>'
            . '</td></tr></table></td></tr>';
    } else {
        $header = '<tr><td style="padding:0;background-color:#fdfbf7;border-radius:20px 20px 0 0;border:1px solid #e8dfc8;border-bottom:none;">'
            . '<table role="presentation" width="100%" cellpadding="0" cellspacing="0"><tr>'
            . '<td style="height:5px;background-color:#c5a059;font-size:0;line-height:0;border-radius:20px 20px 0 0;">&nbsp;</td></tr>'
            . '<tr><td style="padding:36px 40px 32px 40px;">'
            . '<p style="margin:0 0 8px 0;font-size:11px;font-weight:700;letter-spacing:0.35em;text-transform:uppercase;'
            . 'color:#b45309;font-family:\'Segoe UI\',Tahoma,Geneva,Verdana,sans-serif;">Change Hair & Beauty</p>'
            . '<p style="margin:0;font-size:30px;line-height:1.12;color:#1c1917;font-family:Georgia,\'Times New Roman\',serif;font-weight:400;">'
            . 'A moment for you</p>'
            . '<p style="margin:14px 0 0 0;font-size:14px;line-height:1.5;color:#57534e;font-family:\'Segoe UI\',Tahoma,Geneva,Verdana,sans-serif;">'
            . $sub . '</p>'
            . '</td></tr></table></td></tr>';
    }

    $cardBg = $audience === 'merchant' ? '#ffffff' : '#ffffff';
    $cardBorder = $audience === 'merchant' ? '#d4d4d8' : '#e7e5e4';
    $outerBg = $audience === 'merchant' ? '#d4d4d8' : '#e8e0d5';

    return '<!DOCTYPE html><html lang="en"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">'
        . '<meta name="viewport" content="width=device-width,initial-scale=1">'
        . '<meta name="color-scheme" content="light"><meta name="supported-color-schemes" content="light"></head>'
        . '<body style="margin:0;padding:0;background-color:' . $outerBg . ';">'
        . '<span style="display:none!important;visibility:hidden;opacity:0;color:transparent;height:0;width:0;max-height:0;max-width:0;overflow:hidden;">'
        . $pre . '</span>'
        . '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:' . $outerBg . ';padding:44px 18px;">'
        . '<tr><td align="center">'
        . '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:620px;border-collapse:separate;">'
        . $header
        . '<tr><td style="padding:0;background-color:' . $cardBg . ';border:1px solid ' . $cardBorder . ';border-top:none;'
        . 'border-radius:0 0 20px 20px;box-shadow:0 32px 64px -16px rgba(0,0,0,0.14);">'
        . '<table role="presentation" width="100%" cellpadding="0" cellspacing="0">'
        . '<tr><td style="padding:40px 40px 44px 40px;">' . $innerHtml . '</td></tr>'
        . '<tr><td style="padding:28px 40px 36px 40px;background-color:' . ($audience === 'merchant' ? '#fafafa' : '#fdfbf7') . ';'
        . 'border-top:1px solid ' . $cardBorder . ';border-radius:0 0 20px 20px;">'
        . $footer
        . '</td></tr></table></td></tr></table></td></tr></table></body></html>';
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
    $plain = "New message from the website contact form.\r\n\r\n";
    $plain .= 'Name: ' . $visitorName . "\r\n";
    $plain .= 'Email: ' . $visitorEmail . "\r\n";
    $plain .= 'Phone: ' . $visitorPhone . "\r\n\r\n";
    $plain .= "Message:\r\n" . $visitorMessage . "\r\n";

    $inner = '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:22px;">'
        . '<tr><td>' . chb_email_badge('Inquiry', 'merchant') . '</td></tr></table>'
        . '<p style="margin:0 0 10px 0;font-size:28px;line-height:1.2;color:#18181b;font-family:Georgia,\'Times New Roman\',serif;">'
        . 'New website message</p>'
        . '<p style="margin:0 0 32px 0;font-size:15px;line-height:1.65;color:#52525b;font-family:\'Segoe UI\',Tahoma,Geneva,Verdana,sans-serif;">'
        . 'A visitor submitted the contact form. Use <strong style="color:#18181b;">Reply</strong> to respond from your mail client.</p>'
        . chb_email_section_rule('Contact details')
        . chb_email_detail_rows_html([
            'Name' => $visitorName,
            'Email' => $visitorEmail,
            'Phone' => $visitorPhone !== '' ? $visitorPhone : '—',
        ], 'merchant')
        . '<p style="margin:32px 0 14px 0;font-size:10px;font-weight:700;letter-spacing:0.2em;text-transform:uppercase;'
        . 'color:#71717a;font-family:\'Segoe UI\',Tahoma,Geneva,Verdana,sans-serif;">Message</p>'
        . chb_email_quote_block(chb_email_nl2br(chb_h($visitorMessage)));

    $html = chb_email_brand_wrap('New contact form submission.', $inner, 'merchant');

    return chb_mail_send_multipart($to, $subject, $plain, $html, $visitorEmail);
}

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

    $datePretty = chb_booking_format_date_pretty($dateYmd);
    $timePretty = chb_booking_format_time_pretty($dateYmd, $timeHi);
    $phone = $clientPhone !== '' ? $clientPhone : '—';

    $subject = 'New booking — ' . $datePretty;
    $plain = "A new appointment was booked online and is confirmed in the schedule.\r\n\r\n";
    $plain .= "Client: {$clientName}\r\nEmail: {$clientEmail}\r\nPhone: {$phone}\r\n\r\n";
    $plain .= "When: {$datePretty} at {$timePretty}\r\nServices: {$servicesSummary}\r\n";

    $inner = '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:20px;">'
        . '<tr><td>' . chb_email_badge('Confirmed booking', 'merchant') . '</td></tr></table>'
        . '<p style="margin:0 0 10px 0;font-size:28px;line-height:1.2;color:#18181b;font-family:Georgia,\'Times New Roman\',serif;">'
        . 'Calendar update</p>'
        . '<p style="margin:0 0 28px 0;font-size:15px;line-height:1.65;color:#52525b;font-family:\'Segoe UI\',Tahoma,Geneva,Verdana,sans-serif;">'
        . 'The client completed booking online. This slot is stored as <strong style="color:#166534;">confirmed</strong> — no staff confirmation step required.</p>'
        . chb_email_featured_datetime_html($datePretty, $timePretty, 'merchant')
        . chb_email_section_rule('Client & service')
        . chb_email_detail_rows_html([
            'Client' => $clientName,
            'Email' => $clientEmail,
            'Phone' => $phone,
            'Services' => $servicesSummary,
        ], 'merchant')
        . '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-top:28px;">'
        . '<tr><td style="padding:18px 20px;background-color:#f4f4f5;border-radius:12px;border:1px solid #d4d4d8;">'
        . '<p style="margin:0;font-size:13px;line-height:1.6;color:#3f3f46;font-family:\'Segoe UI\',Tahoma,Geneva,Verdana,sans-serif;">'
        . '<strong style="color:#18181b;">Quick action:</strong> Reply-all is set up so your reply goes to the client email on file.</p>'
        . '</td></tr></table>';

    $html = chb_email_brand_wrap('New confirmed booking — calendar.', $inner, 'merchant');

    return chb_mail_send_multipart($to, $subject, $plain, $html, $clientEmail);
}

function chb_send_booking_confirmation_to_client_email(
    string $clientEmail,
    string $clientName,
    string $dateYmd,
    string $timeHi,
    string $servicesSummary
): bool {
    if (!filter_var($clientEmail, FILTER_VALIDATE_EMAIL)) {
        return false;
    }

    $salonReply = chb_contact_recipient_email();
    $replyTo = $salonReply !== '' ? $salonReply : $clientEmail;
    $datePretty = chb_booking_format_date_pretty($dateYmd);
    $timePretty = chb_booking_format_time_pretty($dateYmd, $timeHi);

    $subject = 'You are booked — Change Hair & Beauty';
    $plain = "Hi {$clientName},\r\n\r\n";
    $plain .= "Thank you for booking with Change Hair & Beauty. Your appointment is confirmed.\r\n\r\n";
    $plain .= "Date: {$datePretty}\r\nTime: {$timePretty}\r\nServices: {$servicesSummary}\r\n\r\n";
    $plain .= "If you need to make a change, please contact the salon as soon as possible.\r\n";

    $inner = '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:20px;">'
        . '<tr><td>' . chb_email_badge('Confirmed', 'success') . '</td></tr></table>'
        . '<p style="margin:0 0 8px 0;font-size:28px;line-height:1.2;color:#1c1917;font-family:Georgia,\'Times New Roman\',serif;">'
        . 'Dear ' . chb_h($clientName) . ',</p>'
        . '<p style="margin:0 0 28px 0;font-size:16px;line-height:1.65;color:#44403c;font-family:\'Segoe UI\',Tahoma,Geneva,Verdana,sans-serif;">'
        . 'Thank you for choosing us. Your appointment is reserved and we look forward to welcoming you.</p>'
        . chb_email_featured_datetime_html($datePretty, $timePretty, 'client')
        . chb_email_section_rule('Service')
        . chb_email_detail_rows_html([
            'Service' => $servicesSummary,
        ], 'client')
        . '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-top:28px;">'
        . '<tr><td style="padding:22px 24px;background-color:#f0fdf4;border:1px solid #86efac;border-radius:14px;">'
        . '<p style="margin:0;font-size:14px;line-height:1.65;color:#166534;font-family:\'Segoe UI\',Tahoma,Geneva,Verdana,sans-serif;">'
        . '<strong>Plans changed?</strong> Reply to this message or call us — the earlier we know, the easier it is to offer your time to another guest.</p>'
        . '</td></tr></table>'
        . chb_email_salon_visit_strip_html();

    $html = chb_email_brand_wrap('Your appointment is confirmed. We look forward to seeing you.', $inner, 'client');

    return chb_mail_send_multipart($clientEmail, $subject, $plain, $html, $replyTo);
}

function chb_send_booking_status_email_to_client(
    string $clientEmail,
    string $clientName,
    string $status,
    string $dateYmd,
    string $timeHi,
    string $servicesSummary
): bool {
    if (!filter_var($clientEmail, FILTER_VALIDATE_EMAIL)) {
        return false;
    }

    if ($status !== 'cancelled') {
        return false;
    }

    $salonReply = chb_contact_recipient_email();
    $replyTo = $salonReply !== '' ? $salonReply : $clientEmail;
    $datePretty = chb_booking_format_date_pretty($dateYmd);
    $timePretty = chb_booking_format_time_pretty($dateYmd, $timeHi);

    $subject = 'Appointment cancelled — Change Hair & Beauty';
    $plain = "Hi {$clientName},\r\n\r\n";
    $plain .= "Your appointment has been cancelled by the salon.\r\n\r\n";
    $plain .= "Was scheduled for: {$datePretty} at {$timePretty}\r\nServices: {$servicesSummary}\r\n\r\n";
    $plain .= "If you did not expect this or would like to reschedule, please contact us.\r\n";

    $inner = '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:20px;">'
        . '<tr><td>' . chb_email_badge('Update', 'warning') . '</td></tr></table>'
        . '<p style="margin:0 0 8px 0;font-size:28px;line-height:1.2;color:#1c1917;font-family:Georgia,\'Times New Roman\',serif;">'
        . 'Hello ' . chb_h($clientName) . '</p>'
        . '<p style="margin:0 0 28px 0;font-size:16px;line-height:1.65;color:#44403c;font-family:\'Segoe UI\',Tahoma,Geneva,Verdana,sans-serif;">'
        . 'We need to let you know that we have <strong style="color:#be123c;">cancelled</strong> your upcoming appointment. We apologize for any inconvenience this may cause.</p>'
        . chb_email_featured_datetime_html($datePretty, $timePretty, 'client')
        . chb_email_section_rule('Previous reservation')
        . chb_email_detail_rows_html([
            'Service' => $servicesSummary,
        ], 'client')
        . '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-top:28px;">'
        . '<tr><td style="padding:22px 24px;background-color:#fffbeb;border:1px solid #fde68a;border-radius:14px;">'
        . '<p style="margin:0;font-size:14px;line-height:1.65;color:#92400e;font-family:\'Segoe UI\',Tahoma,Geneva,Verdana,sans-serif;">'
        . '<strong>We would love to see you another time.</strong> Reply to this email and we will help you find a new date that works.</p>'
        . '</td></tr></table>'
        . chb_email_salon_visit_strip_html();

    $html = chb_email_brand_wrap('Update regarding your appointment.', $inner, 'client');

    return chb_mail_send_multipart($clientEmail, $subject, $plain, $html, $replyTo);
}

function chb_send_password_reset_email(string $clientEmail, string $clientName, string $resetUrl): bool
{
    if (!filter_var($clientEmail, FILTER_VALIDATE_EMAIL)) {
        return false;
    }

    $salonReply = chb_contact_recipient_email();
    $replyTo = $salonReply !== '' ? $salonReply : $clientEmail;
    $subject = 'Reset your password — Change Hair & Beauty';
    $plain = "Hi {$clientName},\r\n\r\n";
    $plain .= "We received a request to reset your password. Open this link (valid for one hour):\r\n\r\n";
    $plain .= $resetUrl . "\r\n\r\n";
    $plain .= "If you did not ask for this, you can ignore this email.\r\n";

    $safeUrl = chb_h($resetUrl);
    $inner = '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:20px;">'
        . '<tr><td>' . chb_email_badge('Security', 'security') . '</td></tr></table>'
        . '<p style="margin:0 0 8px 0;font-size:28px;line-height:1.2;color:#1c1917;font-family:Georgia,\'Times New Roman\',serif;">'
        . 'Password reset</p>'
        . '<p style="margin:0 0 30px 0;font-size:16px;line-height:1.65;color:#44403c;font-family:\'Segoe UI\',Tahoma,Geneva,Verdana,sans-serif;">'
        . 'Hi ' . chb_h($clientName) . ', we received a request to reset your account password. '
        . 'Tap the button below — the link stays valid for <strong>one hour</strong>.</p>'
        . chb_email_cta_button($resetUrl, 'Reset my password', 'gold')
        . '<p style="margin:0 0 24px 0;font-size:12px;line-height:1.55;color:#78716c;font-family:\'Segoe UI\',Tahoma,Geneva,Verdana,sans-serif;word-break:break-all;">'
        . 'If the button does not work, paste this into your browser:<br><span style="color:#57534e;">' . $safeUrl . '</span></p>'
        . '<table role="presentation" width="100%" cellpadding="0" cellspacing="0">'
        . '<tr><td style="padding:20px 22px;background-color:#f8fafc;border:1px solid #cbd5e1;border-radius:14px;">'
        . '<p style="margin:0;font-size:13px;line-height:1.65;color:#475569;font-family:\'Segoe UI\',Tahoma,Geneva,Verdana,sans-serif;">'
        . '<strong style="color:#1e293b;">You did not ask for this?</strong> No action needed — your password will remain unchanged.</p></td></tr></table>';

    $html = chb_email_brand_wrap('Reset your password securely.', $inner, 'client');

    return chb_mail_send_multipart($clientEmail, $subject, $plain, $html, $replyTo);
}
