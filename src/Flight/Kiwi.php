<?php

namespace AccountTravel\EmailReservations\Flight;

use AccountTravel\EmailReservations\AbstractEmailParser;
use AccountTravel\EmailReservations\helpers\Airport;
use AccountTravel\EmailReservations\helpers\Date;

class Kiwi extends AbstractEmailParser
{
    public array $providerEmailAddresses = ['tickets@kiwi.com'];

    public function getResultByMicrodata(array $microdata): array
    {
        foreach ($microdata as &$reservation) {
            $flightReservation = &$reservation['FlightReservation'];
            $reservationFor = &$flightReservation['reservationFor'];

            $this->renameKey($reservationFor, 'airline', 'provider');

            // В разметке неверно указано время рейсов (не учитываются временные зоны)
            $this->fixReservationTimezones($reservation);

            $flightReservation['underName'] = ucwords(strtolower($flightReservation['underName']['name']));
            $this->renameKey($flightReservation, 'reservationNumber', 'reservationId');

            // Статус определяем по теме письма
            $flightReservation['reservationStatus'] = 'Reservation' . $flightReservation['reservationStatus'];
        }
        return $microdata;
    }

    public function getResultByPdf(array $pagesContent): array
    {
        $result = [];
        foreach ($pagesContent as $pageContent) {
            preg_match_all('#(\w{3})\s+(- Терминал (\d+) )?(\d{4}) (\w+). (\d+)\s+(\d+:\d+)  Местное время#su', $pageContent, $flight);
            if (!isset($flight[0][0])) {
                continue;
            }

            $departureAirportCode = $flight[1][0];
            $departureAirportTerminal = $flight[3][0] ?? null;
            $departureDate = "{$flight[6][0]} {$flight[5][0]} {$flight[4][0]} {$flight[7][0]}";
            $departureAirport = Airport::getAirportByCode($departureAirportCode);
            $departureTime = Date::getTimestampByStringRu($departureAirport['timezone'], $departureDate);

            $arrivalAirportCode = $flight[1][1];
            $arrivalAirportTerminal = $flight[3][1] ?? null;
            $arrivalDate = "{$flight[6][1]} {$flight[5][1]} {$flight[4][1]} {$flight[7][1]}";
            $arrivalAirport = Airport::getAirportByCode($arrivalAirportCode);
            $arrivalTime = Date::getTimestampByStringRu($arrivalAirport['timezone'], $arrivalDate);

            preg_match('#Номер рейса: ([^ ]+)#s', $pageContent, $flightNumber);
            $flightNumber = $flightNumber[1];

            preg_match('#Авиакомпания: ([^\n]+)#s', $pageContent, $airline);
            $airline = $airline[1];

            preg_match('#Пассажиры\s+([\w\s]+)\s+PNR:(.*?)\n#su', $pageContent, $matches);
            $passenger = rtrim($matches[1]);
            $reservationId = str_replace(' ', '', $matches[2]);

            $result[] = [
                'FlightReservation' => [
                    'reservationFor' => [
                        'provider' => $airline,
                        'departureAirport' => $departureAirport,
                        'arrivalAirport' => $arrivalAirport,
                        'departureTerminal' => $departureAirportTerminal,
                        'arrivalTerminal' => $arrivalAirportTerminal,
                        'departureTime' => $departureTime,
                        'arrivalTime' => $arrivalTime,
                        'flightNumber' => $flightNumber,
                    ],
                    'underName' => $passenger,
                    'reservationId' => $reservationId,
                    'reservationStatus' => 'ReservationConfirmed',
                ]
            ];
        }

        return $result;
    }
}