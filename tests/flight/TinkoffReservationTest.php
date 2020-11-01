<?php

namespace AccountTravel\EmailReservations\Tests\flight;

use AccountTravel\EmailReservations\Parser;

class TinkoffReservationTest extends FlightParserTestCase
{
    public function testParseReservation()
    {
        $file = __DIR__ . '/fixtures/tinkoff.eml';
        $result = (new Parser)->parseFile($file);

        $this->assertEmptyDiff([
            [
                'FlightReservation' => [
                    'reservationFor' => [
                        'provider' => 'Utair',
                        'departureAirport' => [
                            'iata' => 'VKO',
//                            'name' => 'Москва, Внуково',
                        ],
                        'arrivalAirport' => [
                            'iata' => 'TXL',
//                            'name' => 'Берлин, Тегель',
                        ],
                        'departureTime' => 1588578900,
                        'arrivalTime' => 1588589100,
                        'flightNumber' => 'UT705',
                    ],
                    'underName' => 'DOKIN ANDREI',
                    'reservationId' => '6N20SM', // 'G3K7FB/UT',
                    'reservationStatus' => 'ReservationConfirmed',
                ]
            ],
            [
                'FlightReservation' => [
                    'reservationFor' => [
                        'provider' => 'Utair',
                        'departureAirport' => [
                            'iata' => 'TXL',
//                            'name' => 'Берлин, Тегель',
                        ],
                        'arrivalAirport' => [
                            'iata' => 'VKO',
//                            'name' => 'Москва, Внуково',
                        ],
                        'departureTime' => 1589715600,
                        'arrivalTime' => 1589725200,
                        'flightNumber' => 'UT706',
                    ],
                    'underName' => 'DOKIN ANDREI',
                    'reservationId' => '6N20SM', // 'G3K7FB/UT',
                    'reservationStatus' => 'ReservationConfirmed',
                ]
            ],
        ], $result);
    }
}