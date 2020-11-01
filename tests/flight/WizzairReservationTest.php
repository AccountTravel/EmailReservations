<?php

namespace AccountTravel\EmailReservations\Tests\flight;

use AccountTravel\EmailReservations\Parser;

class WizzairReservationTest extends FlightParserTestCase
{
    public function testParseReservation()
    {
        $file = __DIR__ . '/fixtures/wizzair-receipt.eml';
        $result = (new Parser)->parseFile($file);

        $this->assertEmptyDiff([
            [
                'FlightReservation' => [
                    'reservationFor' => [
                        'provider' => 'Wizzair',
                        'departureAirport' => [
                            'iata' => 'VKO',
//                            'name' => 'Vnukovo International Airport',
                        ],
                        'departureTerminal' => 'А',
                        'departureTime' => 1567494300,
                        'arrivalAirport' => [
                            'iata' => 'BUD',
//                            'name' => 'Budapest Liszt Ferenc International Airport',
                        ],
                        'arrivalTerminal' => '2B',
                        'arrivalTime' => 1567503600,
                        'flightNumber' => 'W62490',
                    ],
                    'underName' => 'Andrei Dokin',
                    'reservationId' => 'GEGEQQ',
                    'reservationStatus' => 'ReservationConfirmed',
                ]
            ],
        ], $result);
    }
}