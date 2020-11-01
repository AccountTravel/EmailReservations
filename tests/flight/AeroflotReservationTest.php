<?php

namespace AccountTravel\EmailReservations\Tests\flight;

use AccountTravel\EmailReservations\Parser;

class AeroflotReservationTest extends FlightParserTestCase
{
    public function testParseReservation()
    {
        $file = __DIR__ . '/fixtures/aeroflot-receipt.eml';
        $result = (new Parser)->parseFile($file);

        $this->assertEmptyDiff([
            [
                'FlightReservation' => [
                    'reservationFor' => [
                        'provider' => 'Аэрофлот',
                        'departureAirport' => [
                            'iata' => 'SVO',
//                            'name' => 'Sheremetyevo International Airport',
                        ],
                        'arrivalAirport' => [
                            'iata' => 'DEL',
//                            'name' => 'Indira Gandhi International Airport',
                        ],
                        'departureTime' => 1537546200,
                        'arrivalTime' => 1537480800,
                        'flightNumber' => 'SU0232',
                    ],
                    'underName' => 'ALEKSANDR DOKIN',
                    'reservationId' => 'BQSFSZ',
                    'reservationStatus' => 'ReservationConfirmed',
                ]
            ],
            [
                'FlightReservation' => [
                    'reservationFor' => [
                        'provider' => 'Аэрофлот',
                        'departureAirport' => [
                            'iata' => 'DEL',
//                            'name' => 'Indira Gandhi International Airport',
                        ],
                        'arrivalAirport' => [
                            'iata' => 'SVO',
//                            'name' => 'Sheremetyevo International Airport',
                        ],
                        'departureTime' => 1538868600,
                        'arrivalTime' => 1538891700,
                        'flightNumber' => 'SU0233',
                    ],
                    'underName' => 'ALEKSANDR DOKIN',
                    'reservationId' => 'BQSFSZ',
                    'reservationStatus' => 'ReservationConfirmed',
                ]
            ]
        ], $result);
    }
}