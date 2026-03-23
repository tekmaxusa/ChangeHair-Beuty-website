<?php

declare(strict_types=1);

/**
 * Load project root .env into the environment when vars are unset or empty.
 * Docker / real env vars take precedence if already non-empty.
 */
(function (): void {
    static $loaded = false;
    if ($loaded) {
        return;
    }
    $loaded = true;

    // Project root .env first, then parent folder (e.g. monorepo .env at Pika-Pika-main/.env)
    $paths = [
        dirname(__DIR__) . DIRECTORY_SEPARATOR . '.env',
        dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . '.env',
    ];

    foreach ($paths as $path) {
        if (!is_readable($path)) {
            continue;
        }

        $raw = file_get_contents($path);
        if ($raw === false) {
            continue;
        }

        foreach (explode("\n", str_replace("\r\n", "\n", $raw)) as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }
            if (!str_contains($line, '=')) {
                continue;
            }
            [$name, $value] = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);
            if ($name === '') {
                continue;
            }
            if (
                (str_starts_with($value, '"') && str_ends_with($value, '"'))
                || (str_starts_with($value, "'") && str_ends_with($value, "'"))
            ) {
                $value = substr($value, 1, -1);
            }

            $existing = getenv($name);
            if (is_string($existing) && $existing !== '') {
                continue;
            }

            putenv("{$name}={$value}");
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
})();
