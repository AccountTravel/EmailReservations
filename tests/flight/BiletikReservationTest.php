<?php

namespace AccountTravel\EmailReservations\Tests\flight;

use AccountTravel\EmailReservations\Parser;

class BiletikReservationTest extends FlightParserTestCase
{
    public function testParseReservation()
    {
        $file = __DIR__ . '/fixtures/biletik-receipt.eml';
        $result = (new Parser)->parseFile($file);

        $this->assertEmptyDiff([
            [
                'FlightReservation' => [
                    'reservationFor' => [
                        'provider' => 'S7 Airlines',
                        'departureAirport' => [
                            'iata' => 'LED',
//                            'name' => 'Pulkovo Airport',
                        ],
                        'arrivalAirport' => [
                            'iata' => 'SKX',
//                            'name' => 'Saransk Airport',
                        ],
                        'departureTime' => 1568632200,
                        'arrivalTime' => 1568639700,
                        'flightNumber' => 'S7 Airlines S7 6003',
                    ],
                    'underName' => 'ANDREI DOKIN',
                    'reservationId' => 'TW9EHZ',
                    'reservationStatus' => 'ReservationConfirmed',
                ]
            ],
        ], $result);
    }
}