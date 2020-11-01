<?php

namespace AccountTravel\EmailReservations\Tests\flight;

use AccountTravel\EmailReservations\Parser;

class S7ReservationTest extends FlightParserTestCase
{
    const RESULT = [
        [
            'FlightReservation' => [
                'reservationFor' => [
                    'provider' => 'S7',
                    'departureAirport' => [
                        'iata' => 'LED',
//                        'name' => 'Пулково',
                    ],
                    'arrivalAirport' => [
                        'iata' => 'SKX',
//                        'name' => 'Саранск',
                    ],
                    'departureTime' => 1568632200,//1568643000,
                    'arrivalTime' => 1568639700,//1568650500,
                    'flightNumber' => 'S76003',
                ],
                'underName' => 'Andrei Dokin',
                'reservationId' => 'TW9EHZ',
                'reservationStatus' => 'ReservationConfirmed',
            ]
        ],
    ];

    public function testParseReservationMicrodata()
    {
        $file = __DIR__ . '/fixtures/s7-confirm-original.eml';
        $result = (new Parser)->parseFile($file);

        $this->assertEmptyDiff(self::RESULT, $result);
    }

    public function testParseReservationPdf()
    {
        $file = __DIR__ . '/fixtures/s7-confirm-forwarded.eml';
        $result = (new Parser)->parseFile($file);

        $this->assertEmptyDiff(self::RESULT, $result);
    }
}