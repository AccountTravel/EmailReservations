<?php

namespace AccountTravel\EmailReservations\Tests\train;

use AccountTravel\EmailReservations\Parser;

class RzdReservationTest extends TrainParserTestCase
{
    public function testParseReservation()
    {
        $file = __DIR__ . '/fixtures/Rzd.eml';
        $result = (new Parser)->parseFile($file);

        $this->assertEmptyDiff([
            [
                'TrainReservation' => [
                    'reservationFor' => [
                        'trainCompany' => 'РЖД',
                        'trainNumber' => '022ЧА',
                        'departureTime' => 1570460760,
                        'arrivalTime' => 1570472580,
                        'departureStation' => 'АПАТИТЫ 1',
                        'arrivalStation' => 'МУРМАНСК',
                    ],
                    'reservedTicket' => [
                        'ticketedSeat' => [
                            'seatSection' => 4,
                            'seatNumber' => 48,
                        ],
//                        'underName' => '', //todo
                    ],

//                    'reservationId' => '', // todo
                    'reservationStatus' => 'ReservationConfirmed',
                ]
            ]
        ], $result);
    }
}