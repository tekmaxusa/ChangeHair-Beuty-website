<?php

declare(strict_types=1);

require_once __DIR__ . '/env.php';

/**
 * Same transport as tekmaxusa/changehair-beauty (Vite): POST application/x-www-form-urlencoded
 * to a Google Apps Script web app. Fields: name, email, phone, service, date, time.
 *
 * @see https://github.com/tekmaxusa/changehair-beauty/blob/main/src/App.tsx (ContactForm, BookingModal)
 */

function chb_google_script_url(): string
{
    foreach (['CHB_GOOGLE_SCRIPT_URL', 'CONTACT_GOOGLE_SCRIPT_URL', 'VITE_GOOGLE_SCRIPT_URL'] as $key) {
        $v = getenv($key);
        if (!is_string($v)) {
            continue;
        }
        $v = trim($v);
        if ($v !== '' && preg_match('#^https?://#i', $v)) {
            return $v;
        }
    }

    return '';
}

/**
 * Default: when CHB_GOOGLE_SCRIPT_URL is set, admin cancellation emails the client via Apps Script (MailApp).
 * Set CHB_BOOKING_CANCEL_EMAIL_VIA_GOOGLE_SCRIPT=0 (or false/no/off) to use PHP mail() instead.
 */
function chb_booking_cancel_email_via_google_script(): bool
{
    if (chb_google_script_url() === '') {
        return false;
    }
    if (chb_env_flag_false('CHB_BOOKING_CANCEL_EMAIL_VIA_GOOGLE_SCRIPT')) {
        return false;
    }

    return true;
}

/**
 * POST event=booking_cancelled — Apps Script sends the client cancellation email (see google-apps-script-email.example.js).
 *
 * @param 'void'|'refund'|'skipped'|null $depositKind
 */
function chb_post_booking_cancelled_to_google_script(
    string $clientEmail,
    string $clientName,
    string $datePretty,
    string $timePretty,
    string $servicesSummary,
    ?string $depositKind,
    ?int $depositAmountCents,
    string $clientPhone = ''
): bool {
    if (!filter_var($clientEmail, FILTER_VALIDATE_EMAIL)) {
        return false;
    }

    $svc = $servicesSummary;
    if (function_exists('mb_strlen') && mb_strlen($svc) > 800) {
        $svc = mb_substr($svc, 0, 797) . '…';
    } elseif (strlen($svc) > 800) {
        $svc = substr($svc, 0, 797) . '…';
    }

    $fields = [
        'event' => 'booking_cancelled',
        'name' => $clientName !== '' ? $clientName : '—',
        'email' => $clientEmail,
        'phone' => $clientPhone !== '' ? $clientPhone : '—',
        'service' => $svc !== '' ? $svc : '—',
        'date' => $datePretty,
        'time' => $timePretty,
        'deposit_kind' => $depositKind !== null && $depositKind !== '' ? $depositKind : '',
        'deposit_amount_cents' => $depositAmountCents !== null && $depositAmountCents > 0 ? (string) $depositAmountCents : '',
    ];

    return chb_post_google_script_form($fields);
}

/**
 * POST event=password_reset — Apps Script sends the client password reset email.
 */
function chb_post_password_reset_to_google_script(
    string $clientEmail,
    string $clientName,
    string $resetUrl
): bool {
    if (!filter_var($clientEmail, FILTER_VALIDATE_EMAIL)) {
        return false;
    }

    $u = trim($resetUrl);
    if ($u === '' || !filter_var($u, FILTER_VALIDATE_URL)) {
        return false;
    }

    $fields = [
        'event' => 'password_reset',
        'name' => $clientName !== '' ? $clientName : '—',
        'email' => $clientEmail,
        'reset_url' => $u,
    ];

    return chb_post_google_script_form($fields);
}

/**
 * @param array<string, string> $fields
 */
function chb_post_google_script_form(array $fields): bool
{
    $url = chb_google_script_url();
    if ($url === '') {
        return false;
    }

    $body = http_build_query($fields, '', '&');

    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        if ($ch === false) {
            return false;
        }
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 20,
            CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
        ]);
        $respBody = curl_exec($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_error($ch);
        curl_close($ch);

        $ok = $err === '' && $code >= 200 && $code < 400;
        if (!$ok && chb_env_flag_true('CHB_DEV_MAIL_LOG')) {
            $respSnippet = is_string($respBody) ? mb_substr($respBody, 0, 500) : '';
            error_log('CHB_GOOGLE_SCRIPT_POST_FAILED code=' . $code . ' err=' . $err . ' url=' . $url . ' bodySnippet=' . $respSnippet);
        }

        return $ok;
    }

    $ctx = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
            'content' => $body,
            'timeout' => 20,
            'ignore_errors' => true,
        ],
    ]);
    $result = @file_get_contents($url, false, $ctx);
    if ($result === false) {
        if (chb_env_flag_true('CHB_DEV_MAIL_LOG')) {
            error_log('CHB_GOOGLE_SCRIPT_POST_FAILED file_get_contents url=' . $url);
        }
        return false;
    }
    if (!isset($http_response_header) || !is_array($http_response_header)) {
        return true;
    }
    $statusLine = $http_response_header[0] ?? '';
    if (preg_match('#\s(\d{3})\s#', $statusLine, $m)) {
        $code = (int) $m[1];

        return $code >= 200 && $code < 400;
    }

    return true;
}
