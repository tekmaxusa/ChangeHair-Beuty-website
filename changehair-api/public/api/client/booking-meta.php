<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/_init.php';
require_once dirname(__DIR__, 3) . '/booking/booking.php';
require_once dirname(__DIR__, 3) . '/config/booking_services.php';

chb_api_require_method('GET');

$chb_book_categories = booking_service_categories();
$calendarDates = booking_calendar_dates(90);
$slotsByDate = [];

foreach ($calendarDates as $d) {
    $row = [];
    foreach (booking_time_options() as $opt) {
        $row[] = [
            'time' => $opt,
            'state' => booking_time_slot_state($d, $opt),
        ];
    }
    $slotsByDate[$d] = $row;
}

chb_api_json([
    'ok' => true,
    'categories' => $chb_book_categories,
    'slotsByDate' => $slotsByDate,
]);
