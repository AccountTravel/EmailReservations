<?php

namespace AccountTravel\EmailReservations\Tests\flight;

use AccountTravel\EmailReservations\Parser;

class UtairReservationTest extends FlightParserTestCase
{
    const RESULT = [
        [
            'FlightReservation' => [
                'reservationFor' => [
                    'provider' => 'UTair',
                    'departureAirport' => [
                        'iata' => 'VKO',
//                        'name' => 'Moscow, Vnukovo',
                    ],
                    'arrivalAirport' => [
                        'iata' => 'MMK',
//                        'name' => 'Murmansk',
                    ],
                    'departureTime' => 1569708000,
                    'arrivalTime' => 1569717000,
                    'flightNumber' => 'UT577',
                ],
                'underName' => 'ДОКИН АЛЕКСАНДР НИКОЛАЕВИЧ',
                'reservationId' => '5N00VF',
                'reservationStatus' => 'ReservationConfirmed',
            ]
        ],
        [
            'FlightReservation' => [
                'reservationFor' => [
                    'provider' => 'UTair',
                    'departureAirport' => [
                        'iata' => 'MMK',
//                        'name' => 'Murmansk',
                    ],
                    'arrivalAirport' => [
                        'iata' => 'VKO',
//                        'name' => 'Moscow, Vnukovo',
                    ],
                    'departureTime' => 1570584600,
                    'arrivalTime' => 1570593900,
                    'flightNumber' => 'UT578',
                ],
                'underName' => 'ДОКИН АЛЕКСАНДР НИКОЛАЕВИЧ',
                'reservationId' => '5N00VF',
                'reservationStatus' => 'ReservationConfirmed',
            ]
        ],
        [
            'FlightReservation' => [
                'reservationFor' => [
                    'provider' => 'UTair',
                    'departureAirport' => [
                        'iata' => 'VKO',
//                        'name' => 'Moscow, Vnukovo',
                    ],
                    'arrivalAirport' => [
                        'iata' => 'MMK',
//                        'name' => 'Murmansk',
                    ],
                    'departureTime' => 1569708000,
                    'arrivalTime' => 1569717000,
                    'flightNumber' => 'UT577',
                ],
                'underName' => 'СОЛДАТКИН ВЯЧЕСЛАВ БОРИСОВИЧ',
                'reservationId' => '5N00VF',
                'reservationStatus' => 'ReservationConfirmed',
            ]
        ],
        [
            'FlightReservation' => [
                'reservationFor' => [
                    'provider' => 'UTair',
                    'departureAirport' => [
                        'iata' => 'MMK',
//                        'name' => 'Murmansk',
                    ],
                    'arrivalAirport' => [
                        'iata' => 'VKO',
//                        'name' => 'Moscow, Vnukovo',
                    ],
                    'departureTime' => 1570584600,
                    'arrivalTime' => 1570593900,
                    'flightNumber' => 'UT578',
                ],
                'underName' => 'СОЛДАТКИН ВЯЧЕСЛАВ БОРИСОВИЧ',
                'reservationId' => '5N00VF',
                'reservationStatus' => 'ReservationConfirmed',
            ]
        ]
    ];

    public function testParseReservationMicrodata()
    {
        $file = __DIR__ . '/fixtures/utair.eml';
        $result = (new Parser)->parseFile($file);

        $this->assertEmptyDiff(self::RESULT, $result);
    }

    public function testParseReservationPdf()
    {
        $file = __DIR__ . '/fixtures/utair-forwarded.eml';
        $result = (new Parser)->parseFile($file);

        $this->assertEmptyDiff(self::RESULT, $result);
    }
}