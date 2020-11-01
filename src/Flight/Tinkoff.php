<?php

namespace AccountTravel\EmailReservations\Flight;

use AccountTravel\EmailReservations\AbstractEmailParser;
use AccountTravel\EmailReservations\helpers\Date;
use AccountTravel\EmailReservations\helpers\Iata;
use AccountTravel\EmailReservations\helpers\Timezone;

class Tinkoff extends AbstractEmailParser
{
    public array $providerEmailAddresses = ['inform@emails.tinkoff.ru'];

//    // todo - проблемы с кодировкой
//    public function getResultByPdf(string $content): array
//    {
//        return [];
//    }

    public function getResultByHtml(): array
    {
        preg_match('#PASSENGER\s+</td>\s+</tr>\s+<tr>\s+<td.*?>(.*?)</td>#s', $this->html, $passenger);
        $passenger = explode('<br>',trim($passenger[1]))[0];

        preg_match_all('#/logos/24/(.*?).png#s', $this->html, $airline);
        $airline = $airline[1];

        preg_match_all('#FLIGHT\s+</td>\s+</tr>\s+<tr>\s+<td.*?>(.*?)</td>#s', $this->html, $flightNumbers);
        $flightNumbers = array_map(function($html) {
            $string = preg_replace('#\s+#', ' ', strip_tags($html));
            $string = str_replace(" ", '', $string);
            return trim($string);
        }, $flightNumbers[1]);

        preg_match('#ORDER\s+</td>\s+</tr>\s+<tr>\s+<td.*?>(.*?)</td>#s', $this->html, $reservationNumber);
        $reservationNumber = trim(strip_tags($reservationNumber[1]));

        preg_match('#DATE\s+</td>\s+</tr>\s+<tr>\s+<td.*?>(.*?)</td>#s', $this->html, $date);
        $date = trim(strip_tags($date[1]));
        $year = date('Y', strtotime($date));

        preg_match_all('#DEPARTURE\s+</td>(.*?)direction:rtl#s', $this->html, $departure);
        $departure = array_map(function($html) use ($year) {
            $departureData = explode(' ', trim(preg_replace('#\s+#', ' ', strip_tags($html))));
            $departureCity = $departureData[3];
            $departureTimezone = Timezone::getTimezoneByCity($departureCity);
            $departureTime = Date::getTimestampByStringRu($departureTimezone, "{$departureData[1]} {$departureData[2]} {$year} {$departureData[0]}");

            return [
                'iata' => $departureData[5],
                'name' => "{$departureCity}, {$departureData[4]}",
                'time' => $departureTime,
            ];
        }, $departure[1]);

        preg_match_all('#ARRIVAL\s+</td>(.*?)</div>#s', $this->html, $arrival);
        $arrival = array_map(function($html) use ($year) {
            $arrivalData = explode(' ', trim(preg_replace('#\s+#', ' ', strip_tags($html))));

            $arrivalCity = $arrivalData[3];
            $arrivalTimezone = Timezone::getTimezoneByCity($arrivalCity);
            $arrivalTime = Date::getTimestampByStringRu($arrivalTimezone, "{$arrivalData[1]} {$arrivalData[2]} {$year} {$arrivalData[0]}");

            return [
                'iata' => $arrivalData[5],
                'name' => "{$arrivalCity}, {$arrivalData[4]}",
                'time' => $arrivalTime,
            ];
        }, $arrival[1]);

        $result = [];
        for ($i=0; $i<count($departure); $i++) {
            $result[] = [
                'FlightReservation' => [
                    'reservationFor' => [
                        'provider' => Iata::getAirlineNameByCode($airline[$i]),
                        'departureAirport' => [
                            'iata' => $departure[$i]['iata'],
                            'name' => $departure[$i]['name'],
                        ],
                        'arrivalAirport' => [
                            'iata' => $arrival[$i]['iata'],
                            'name' => $arrival[$i]['name'],
                        ],
                        'departureTime' => $departure[$i]['time'],
                        'arrivalTime' => $arrival[$i]['time'],
                        'flightNumber' => $flightNumbers[$i],
                    ],
                    'underName' => $passenger,
                    'reservationId' => $reservationNumber,
                    'reservationStatus' => 'ReservationConfirmed',
                ]
            ];
        }

        return $result;
    }
}