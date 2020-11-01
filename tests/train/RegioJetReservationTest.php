<?php

namespace AccountTravel\EmailReservations\Tests\train;

use AccountTravel\EmailReservations\Parser;

class RegioJetReservationTest extends TrainParserTestCase
{
    public function testParseReservation()
    {
        $file = __DIR__ . '/fixtures/RegioJet.eml';
        $result = (new Parser)->parseFile($file);

        $this->assertEmptyDiff([
            [
                'TrainReservation' => [
                    'reservationFor' => [
                        'trainCompany' => 'RegioJet',
                        'trainNumber' => 'RJ 1037',
                        'departureTime' => 1568211600,
                        'arrivalTime' => 1568226120,
                        'departureStation' => 'Прага, hl.n.',
                        'arrivalStation' => 'Вена, Hbf',
                    ],
                    'reservedTicket' => [
                        'ticketedSeat' => [
                            'seatSection' => 1,
                            'seatNumber' => 57,
                        ],
//                        'underName' => '', // todo
                    ],
                    'reservationId' => '9827613848',
                    'reservationStatus' => 'ReservationConfirmed',
                ]
            ]
        ], $result);
    }
}