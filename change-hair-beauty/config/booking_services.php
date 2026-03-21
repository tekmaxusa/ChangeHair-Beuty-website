<?php

declare(strict_types=1);

/**
 * Service catalog (aligned with changehair-beauty booking modal).
 *
 * @return list<array{id:string,name:string,services:list<array{name:string,price:string}>}>
 */
function booking_service_categories(): array
{
    return [
        [
            'id' => 'cut',
            'name' => 'Cut',
            'services' => [
                ['name' => 'Women', 'price' => '$35+'],
                ['name' => 'Men', 'price' => '$25+'],
                ['name' => 'Kids', 'price' => '$25+'],
            ],
        ],
        [
            'id' => 'color',
            'name' => 'Color',
            'services' => [
                ['name' => 'Root', 'price' => '$80+'],
                ['name' => 'Manicure', 'price' => '$80+'],
                ['name' => 'Highlight (F)', 'price' => '$200+'],
                ['name' => 'Highlight (M)', 'price' => '$150+'],
            ],
        ],
        [
            'id' => 'perm',
            'name' => 'Perm',
            'services' => [
                ['name' => "Men's Iron Perm", 'price' => '$130+'],
                ['name' => "Basic Women's Perm", 'price' => '$100+'],
                ['name' => 'Set / Digital', 'price' => '$200+'],
                ['name' => 'Magic Setting', 'price' => '$250+'],
                ['name' => 'Japanese Magic Straight', 'price' => '$230+'],
            ],
        ],
        [
            'id' => 'style',
            'name' => 'Style',
            'services' => [
                ['name' => 'Shampoo', 'price' => '$20+'],
                ['name' => 'Blow Dry', 'price' => '$35+'],
                ['name' => 'Upstyle', 'price' => '$130+'],
                ['name' => 'Makeup', 'price' => '$150+'],
            ],
        ],
    ];
}

/**
 * @return array{ok: bool, category?: string, service?: string, error?: string}
 */
function booking_validate_service_pick(string $categoryId, string $serviceName): array
{
    $categoryId = trim($categoryId);
    $serviceName = trim($serviceName);
    if ($categoryId === '' || $serviceName === '') {
        return ['ok' => false, 'error' => 'Please choose a service.'];
    }

    foreach (booking_service_categories() as $cat) {
        if ($cat['id'] !== $categoryId) {
            continue;
        }
        foreach ($cat['services'] as $svc) {
            if ($svc['name'] === $serviceName) {
                return ['ok' => true, 'category' => $cat['name'], 'service' => $serviceName];
            }
        }
    }

    return ['ok' => false, 'error' => 'Invalid service selection.'];
}

/**
 * @param mixed $picks POST services[] (list of "categoryId|serviceName")
 * @return array{ok: bool, lines?: list<array{category:string,service:string}>, error?: string}
 */
function booking_validate_service_picks($picks): array
{
    if (!is_array($picks)) {
        return ['ok' => false, 'error' => 'Please select at least one service.'];
    }

    $flat = [];
    foreach ($picks as $p) {
        if (is_string($p) && $p !== '') {
            $flat[] = trim($p);
        }
    }

    if ($flat === []) {
        return ['ok' => false, 'error' => 'Please select at least one service.'];
    }

    $lines = [];
    $seen = [];

    foreach ($flat as $pick) {
        $parts = explode('|', $pick, 2);
        if (count($parts) < 2) {
            return ['ok' => false, 'error' => 'Invalid service selection.'];
        }
        $v = booking_validate_service_pick($parts[0], $parts[1]);
        if (!$v['ok']) {
            return ['ok' => false, 'error' => $v['error'] ?? 'Invalid service.'];
        }
        $cat = (string) ($v['category'] ?? '');
        $svc = (string) ($v['service'] ?? '');
        $key = strtolower($parts[0]) . '|' . strtolower($svc);
        if (isset($seen[$key])) {
            continue;
        }
        $seen[$key] = true;
        $lines[] = ['category' => $cat, 'service' => $svc];
    }

    if ($lines === []) {
        return ['ok' => false, 'error' => 'Please select at least one service.'];
    }

    return ['ok' => true, 'lines' => $lines];
}
