<?php

namespace AccountTravel\EmailReservations\Tests\train;

use AccountTravel\EmailReservations\Parser;

class ItaloReservationTest extends TrainParserTestCase
{
    public function testParseReservation()
    {
        $file = __DIR__ . '/fixtures/Italo-BookingConfirmation.eml';
        $result = (new Parser)->parseFile($file);

        $this->assertEmptyDiff([
            [
                'TrainReservation' => [
                    'reservationFor' => [
                        'trainCompany' => 'NTV',
                        'trainName' => 'Italo',
                        'trainNumber' => 8924,
                        'departureTime' => 1567878840,
                        'arrivalTime' => 1567885320,
                        'departureStation' => 'Firenze SMN',
                        'arrivalStation' => 'Venezia ME',
                    ],
                    'reservedTicket' => [
                        'ticketNumber' => 'BFSUKE',
                        'ticketedSeat' => [
                            'seatSection' => 5,
                            'seatNumber' => 5,
                        ],
                        'underName' => 'Andrei Dokin',
                    ],

                    'reservationNumber' => 'BFSUKE',
                    'reservationStatus' => 'ReservationConfirmed',
                ]
            ]
        ], $result);
    }

    public function testParseReservationItalian()
    {
        $file = __DIR__ . '/fixtures/Italo–ConfermaAcquisto.eml';
        $result = (new Parser)->parseFile($file);

        $this->assertEmptyDiff([
            [
                'TrainReservation' => [
                    'reservationFor' => [
                        'trainCompany' => 'NTV',
                        'trainName' => 'Italo',
                        'trainNumber' => 8902,
                        'departureTime' => 1567833300,
                        'arrivalTime' => 1567838760,
                        'departureStation' => 'Roma Ter.',
                        'arrivalStation' => 'Firenze SMN',
                    ],
                    'reservedTicket' => [
                        'ticketNumber' => 'RC45QT',
                        'ticketedSeat' => [
                            'seatSection' => 4,
                            'seatNumber' => 9,
                        ],
                        'underName' => 'Andrei Dokin',
                    ],

                    'reservationNumber' => 'RC45QT',
                    'reservationStatus' => 'ReservationConfirmed',
                ]
            ]
        ], $result);
    }
}