<?php

namespace AccountTravel\EmailReservations\Flight;

use AccountTravel\EmailReservations\AbstractEmailParser;
use AccountTravel\EmailReservations\helpers\Airport;
use AccountTravel\EmailReservations\helpers\Date;
use AccountTravel\EmailReservations\helpers\Timezone;

class AirArabia extends AbstractEmailParser
{
    public array $providerEmailAddresses = ['reservations@airarabia.com'];

    public function getResultByHtml(): array
    {
        $reservationId = $this->getReservationNumberFromHtml();
        $passengers = $this->getPassengersFromHtml();
        $flights = $this->getFlightsFromHtml();

        $result = [];
        foreach ($passengers as $passenger) {
            foreach ($flights as $flight) {
                $result[] = [
                    'FlightReservation' => [
                        'reservationFor' => [
                            'provider' => 'AirArabia',
                            'flightNumber' => $flight['number'],
                            'departureAirport' => $flight['departureAirport'],
                            'arrivalAirport' => $flight['arrivalAirport'],
                            'departureTime' => $flight['departureTime'],
                            'arrivalTime' => $flight['arrivalTime'],
                        ],
                        'underName' => $passenger,
                        'reservationId' => $reservationId,
                        'reservationStatus' => 'ReservationConfirmed',
                    ],
                ];
            }
        }

        return $result;
    }

    private function getReservationNumberFromHtml(): string
    {
        preg_match('#НОМЕР БРОНИРОВАНИЯ.*?(<td.*?</td>)#s', $this->html, $reservationNumber);
        return trim(strip_tags($reservationNumber[1]));
    }

    private function getPassengersFromHtml(): array
    {
        $passengers = [];
        preg_match('#ИНФОРМАЦИЯ О ПАССАЖИРЕ.*?(<table.*?</table>)#s', $this->html, $passengersTable);
        preg_match_all('#<tr>(.*?)</tr>#s', $passengersTable[1], $passengersTableRows);
        // [0] headers
        // [1] passenger 1
        // ...
        // [2] totals
        // [3] contact headers
        // [4] contact details
        $lastTrIndex = count($passengersTableRows[1]);
        $firstPassengerTrIndex = 1;
        $lastPassengerTrIndex = $lastTrIndex - 3;
        for ($i = $firstPassengerTrIndex; $i < $lastPassengerTrIndex; $i++) {
            preg_match_all('#<td[^>]*>(.*?)</td>#s', $passengersTableRows[1][$i], $passengerTableColumns);
            $underName = trim(preg_replace('#[\t\r\n]+#s', ' ', strtr(strip_tags($passengerTableColumns[1][0]), [
                "\r\n" => '',
                ' ' => ' ',
                'MR' => '',
                'MRS' => '',
                'MS' => '',
            ])));
            $passengers[] = $underName;
        }
        return $passengers;
    }

    private function getFlightsFromHtml(): array
    {
        $flights = [];
        preg_match('#СЕГМЕНТЫ ПУТЕШЕСТВИЯ.*?(<table.*?</table>)#s', $this->html, $flightsTable);
        preg_match_all('#<tr[^>]*>(.*?)</tr>#s', $flightsTable[1], $flightsTableRows);
        // [0] - headers
        // [1] - flight 1
        // ...
        $flightIndex = -1;
        for ($i = 1; $i < count($flightsTableRows[1]); $i++) {
            $row = $flightsTableRows[1][$i];
            $type = '';
            preg_match_all('#<td[^>]*>(.*?)</td>#s', $row, $columns);
            $columnOffset = 0;
            if (strpos($row, 'rowspan') !== false) {
                $flightIndex++;
                $columnOffset = 1;
                $flights[$flightIndex]['number'] = trim(preg_replace(
                    '#[\t\r\n]+#s',
                    '',
                    explode(" ", strip_tags($columns[1][0]))[0]
                ));

                $type = 'departure';
            } elseif (strpos($row, 'Duration') === false && strpos($row, 'colspan') === false) {
                $type = 'arrival';
            }

            if ($type) {
                $place = explode(' - ', trim(strip_tags(str_replace(" ", '', $columns[1][$columnOffset]))));
                $city = $place[0];
                $airport = $place[1] ?? $city;

                $timezone = Timezone::getTimezoneByCity($city);
                $dateTime = preg_replace(
                    '#([\w]+, )#su',
                    '',
                    trim(strip_tags($columns[1][$columnOffset+1])) . ' ' . trim(strip_tags($columns[1][$columnOffset+2]))
                );
                $flights[$flightIndex]["{$type}Time"] = Date::getTimestampByStringRu($timezone, $dateTime);
                $flights[$flightIndex]["{$type}Airport"] = Airport::getAirportByName($airport);
            }
        }
        return $flights;
    }
}