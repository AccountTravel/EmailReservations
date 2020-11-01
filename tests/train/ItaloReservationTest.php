<?php

namespace AccountTravel\EmailReservations\Tests\train;

use AccountTravel\EmailReservations\Parser;

class ItaloReservationTest extends TrainParserTestCase
{
    /**
     * @dataProvider reservationDataProvider
     * @param string $fixture
     * @param array $expected
     */
    public function testParseReservation(string $fixture, array $expected)
    {
        $file = __DIR__ . '/fixtures/' . $fixture;
        $result = (new Parser)->parseFile($file);

        $this->assertEmptyDiff($expected, $result);
    }

    public function reservationDataProvider()
    {
        return [
            [
                'fixture' => 'Italo-BookingConfirmation.eml',
                'expected' => [
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
                ],
            ],
            [
                'fixture' => 'Italo–ConfermaAcquisto.eml',
                'expected' => [
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
                ],
            ],
        ];
    }
}