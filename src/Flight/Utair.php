<?php

namespace AccountTravel\EmailReservations\Flight;

use AccountTravel\EmailReservations\AbstractEmailParser;
use AccountTravel\EmailReservations\helpers\Airport;
use AccountTravel\EmailReservations\helpers\Date;

class Utair extends AbstractEmailParser
{
    public array $providerEmailAddresses = ['booking@utair.ru'];

    public function getResultByPdf(array $pagesContent): array
    {
        $result = [];
        foreach ($pagesContent as $pageContent) {
            preg_match('#Пассажир Документ № Статус\s+(.*?) ps(\d+) (\w+)#su', $pageContent, $matches);
            if (!isset($matches[1])) {
                continue;
            }

            $passenger = $matches[1];
            $passport = $matches[2];
            $status = $matches[3];

            preg_match('#Номер бронирования.*?\n(.*?) \d+.\d+.(\d+)#su', $pageContent, $matches);
            $reservationId = $matches[1];
            $year = $matches[2];

            preg_match_all("#(\d{2}:\d{2})\n(\d+ \w+)\n(\w+)\n(\w+), (\w{3})#su", $pageContent, $matches);
            $time = $matches[1];
            $date = $matches[2];
            $airportCode = $matches[5];

            preg_match_all('#(UT\d+)#s', $pageContent, $matches);
            $flightNumber = $matches[1];

            for ($i=0; $i<count($flightNumber); $i++) {
                $departureIndex = 2 * $i;
                $arrivalIndex = $departureIndex + 1;

                $departureAirportIata = $airportCode[$departureIndex];
                $arrivalAirportIata = $airportCode[$arrivalIndex];

                $departureAirport = Airport::getAirportByCode($departureAirportIata);
                $arrivalAirport = Airport::getAirportByCode($arrivalAirportIata);

                $result[] = [
                    'FlightReservation' => [
                        'reservationFor' => [
                            'provider' => 'UTair',
                            'departureAirport' => [
                                'iata' => $departureAirportIata,
                            ],
                            'arrivalAirport' => [
                                'iata' => $arrivalAirportIata,
                            ],
                            'departureTime' => Date::getTimestampByStringRu($departureAirport['timezone'], "{$date[$departureIndex]} {$year} {$time[$departureIndex]}"),
                            'arrivalTime' => Date::getTimestampByStringRu($arrivalAirport['timezone'], "{$date[$arrivalIndex]} {$year} {$time[$arrivalIndex]}"),
                            'flightNumber' => $flightNumber[$i],
                        ],
                        'underName' => $passenger,
                        'reservationId' => $reservationId,
                        'reservationStatus' => $status === 'Отменено' ? 'ReservationCancelled' : 'ReservationConfirmed',
                    ]
                ];
            }
        }

        return $result;
    }

    public function getResultByMicrodata(array $microdata): array
    {
        foreach ($microdata as &$reservation) {
            $flightReservation = &$reservation['FlightReservation'];
            $reservationFor = &$flightReservation['reservationFor'];

            $this->renameKey($flightReservation, 'reservationNumber', 'reservationId');
            $flightReservation['reservationId'] = explode('/', $flightReservation['reservationId'])[0];
            $flightReservation['underName'] = $flightReservation['underName']['name'];
            $flightReservation['reservationStatus'] = 'ReservationConfirmed'; // todo

            $this->renameKey($reservationFor, 'airline', 'provider');
            $reservationFor['flightNumber'] = 'UT' . $reservationFor['flightNumber'];

            $this->fixReservationTimezones($reservation);
        }
        return $microdata;
    }
}