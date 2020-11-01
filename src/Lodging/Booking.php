<?php

namespace AccountTravel\EmailReservations\Lodging;

use AccountTravel\EmailReservations\AbstractEmailParser;

class Booking extends AbstractEmailParser
{
    public array $providerEmailAddresses = ['customer.service@booking.com'];

    public function getResultByMicrodata(array $microdata): array
    {
        $lodgingReservation = $microdata[0]['LodgingReservation'];
        $result[0]['LodgingReservation'] = array_merge($lodgingReservation, [
            'numAdults' => $lodgingReservation['numAdults'] ?? 0,
            'numChildren' => $lodgingReservation['numChildren'] ?? 0,
            'underName' => strtr($lodgingReservation['underName']['name'], [
                'mr ' => '',
                'mrs ' => '',
                'ms ' => '',
            ]),
            'checkinTime' => $lodgingReservation['checkinDate'],
            'checkoutTime' => $lodgingReservation['checkoutDate'],
            'reservationId' => $lodgingReservation['reservationNumber'],
            'reservationStatus' => $this->getStatus($lodgingReservation['reservationStatus']),
        ]);

        return $result;
    }

    private function getStatus(string $status)
    {
        $keys = [
            'Confirmed' => 'ReservationConfirmed',
            'Canceled' => 'ReservationCancelled',
        ];
        return $keys[$status] ?? null;
    }
}