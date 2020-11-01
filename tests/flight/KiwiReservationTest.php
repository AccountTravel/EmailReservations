<?php

namespace AccountTravel\EmailReservations\Tests\flight;

use AccountTravel\EmailReservations\Parser;

class KiwiReservationTest extends FlightParserTestCase
{
    const RESULT = [
        [
            'FlightReservation' => [
                'reservationFor' => [
                    'provider' => 'Go Air',
                    'departureAirport' => [
                        'iata' => 'DEL',
//                        'name' => 'New Delhi',
                    ],
                    'arrivalAirport' => [
                        'iata' => 'IXL',
//                        'name' => 'Leh',
                    ],
                    'departureTime' => 1537585200,
                    'arrivalTime' => 1537590300,
                    'flightNumber' => 'G8715',
                ],
                'underName' => 'Aleksandr Dokin',
                'reservationId' => 'V348JY',
                'reservationStatus' => 'ReservationConfirmed',
            ]
        ],
        [
            'FlightReservation' => [
                'reservationFor' => [
                    'provider' => 'Go Air',
                    'departureAirport' => [
                        'iata' => 'IXL',
//                        'name' => 'Leh',
                    ],
                    'arrivalAirport' => [
                        'iata' => 'DEL',
//                        'name' => 'New Delhi',
                    ],
                    'departureTime' => 1538801700,
                    'arrivalTime' => 1538807100,
                    'flightNumber' => 'G8194',
                ],
                'underName' => 'Aleksandr Dokin',
                'reservationId' => 'V348JY',
                'reservationStatus' => 'ReservationConfirmed',
            ]
        ],
    ];

    public function testParseReservationMicrodata()
    {
        $file = __DIR__ . '/fixtures/kiwi.eml';
        $result = (new Parser)->parseFile($file);

        $this->assertEmptyDiff(self::RESULT, $result);
    }

    public function testParseReservationPdf()
    {
        $file = __DIR__ . '/fixtures/kiwi-forwarded.eml';
        $result = (new Parser)->parseFile($file);

        $this->assertEmptyDiff(self::RESULT, $result);
    }
}