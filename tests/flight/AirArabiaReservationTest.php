<?php

namespace AccountTravel\EmailReservations\Tests\flight;

use AccountTravel\EmailReservations\Parser;

class AirArabiaReservationTest extends FlightParserTestCase
{
    const RESULT = [
        [
            'FlightReservation' => [
                'reservationFor' => [
                    'provider' => 'AirArabia',
                    'departureAirport' => [
                        'iata' => 'DME',
//                        'name' => 'Домодедово',
                    ],
                    'arrivalAirport' => [
                        'iata' => 'SHJ',
//                        'name' => 'Шарджа',
                    ],
                    'departureTime' => 1521891300,
                    'arrivalTime' => 1521909300,
                    'flightNumber' => 'G9956',
                ],
                'underName' => 'VIACHESLAV SOLDATKIN',
                'reservationId' => '91866263',
                'reservationStatus' => 'ReservationConfirmed',
            ]
        ],
        [
            'FlightReservation' => [
                'reservationFor' => [
                    'provider' => 'AirArabia',
                    'departureAirport' => [
                        'iata' => 'SHJ',
//                        'name' => 'Шарджа',
                    ],
                    'arrivalAirport' => [
                        'iata' => 'KTM',
//                        'name' => 'Катманду',
                    ],
                    'departureTime' => 1521935100,
                    'arrivalTime' => 1521948900,
                    'flightNumber' => 'G9539',
                ],
                'underName' => 'VIACHESLAV SOLDATKIN',
                'reservationId' => '91866263',
                'reservationStatus' => 'ReservationConfirmed',
            ]
        ],
        [
            'FlightReservation' => [
                'reservationFor' => [
                    'provider' => 'AirArabia',
                    'departureAirport' => [
                        'iata' => 'KTM',
//                        'name' => 'Катманду',
                    ],
                    'arrivalAirport' => [
                        'iata' => 'SHJ',
//                        'name' => 'Шарджа',
                    ],
                    'departureTime' => 1523296500,
                    'arrivalTime' => 1523312400,
                    'flightNumber' => 'G9532',
                ],
                'underName' => 'VIACHESLAV SOLDATKIN',
                'reservationId' => '91866263',
                'reservationStatus' => 'ReservationConfirmed',
            ]
        ],
        [
            'FlightReservation' => [
                'reservationFor' => [
                    'provider' => 'AirArabia',
                    'departureAirport' => [
                        'iata' => 'SHJ',
//                        'name' => 'Шарджа',
                    ],
                    'arrivalAirport' => [
                        'iata' => 'DME',
//                        'name' => 'Домодедово',
                    ],
                    'departureTime' => 1523337000,
                    'arrivalTime' => 1523355600,
                    'flightNumber' => 'G9955',
                ],
                'underName' => 'VIACHESLAV SOLDATKIN',
                'reservationId' => '91866263',
                'reservationStatus' => 'ReservationConfirmed',
            ]
        ],
    ];


    public function testParseReservation()
    {
        $file = __DIR__ . '/fixtures/air-arabia.eml';
        $result = (new Parser)->parseFile($file);

        $this->assertEmptyDiff(self::RESULT, $result);
    }
}