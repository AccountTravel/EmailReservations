<?php

namespace AccountTravel\EmailReservations\Tests\lodging;

use AccountTravel\EmailReservations\Parser;

class BookingReservationTest extends LodgingParserTestCase
{
    public function testParseReservation()
    {
        $file = __DIR__ . '/fixtures/booking.eml';
        $result = (new Parser)->parseFile($file);

        $this->assertEmptyDiff([
            [
                'LodgingReservation' => [
                    'numAdults' => 3,
                    'numChildren' => 0,
                    'checkinTime' => 1500116400,
                    'checkoutTime' => 1500195600,
                    'reservationFor' => [
                        'name' => 'Guest house Alla',
                        'address' => '357500, Российская Федерация, Пятигорск, Universitetskaya  55',
                        'telephone' => '+79283083388',
                    ],
                    'underName' => 'Александр Докин',
                    'reservationId' => 1090739966,
                    'reservationStatus' => 'ReservationConfirmed',
                ]
            ]
        ], $result);
    }
}