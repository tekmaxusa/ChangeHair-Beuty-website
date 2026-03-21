<?php

declare(strict_types=1);

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
        curl_exec($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_error($ch);
        curl_close($ch);

        return $err === '' && $code >= 200 && $code < 400;
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
