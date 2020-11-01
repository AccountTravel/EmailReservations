<?php

namespace AccountTravel\EmailReservations\Flight;

use AccountTravel\EmailReservations\AbstractEmailParser;
use AccountTravel\EmailReservations\helpers\Airport;

class Aeroflot extends AbstractEmailParser
{
    public array $providerEmailAddresses = ['callcenter@aeroflot.ru'];

//    /* пробелы между символами */
//    public function getResultByPdf(array $pagesContent): array {}

    public function getResultByHtml(): array
    {
        $result = [];

        preg_match('#pnr_locator=(.*?)&#s', $this->html, $reservationId);
        $reservationId = $reservationId[1] ?? null;

        preg_match('#<!-- Пассажиры -->(.*?)<!-- Дополнительные услуги -->#s', $this->html, $passengersMatch);
        preg_match('#cid:img/icon--\w+.png.*?<strong>(.*?)</strong>#s', $passengersMatch[1], $passengers);
        $passengers = $passengers[1] ?? [];


        preg_match('#<!-- Рейсы -->(.*?)<!-- Информация и предупреждения -->#s', $this->html, $match);
        preg_match_all('#<table.*?>(.*?)</table>\s+<!-- Рейс -->\s+<table.*?>(.*?)</table>\s+<!-- Питание -->\s+<table.*?>(.*?)</table>#s', $match[1], $flights, PREG_SET_ORDER);
        foreach ($flights as $flight) {

            preg_match('#<tr>\s+<td.*?>\s+<span>(.*?)</span>.*?<span.*?>(.*?)</span>\s+</td>\s+<td.*?>\s+<strong>(.*?) г\..*?</strong>\s+</td>\s+</tr>#s', $flight[1], $info1);

            $departureCity = $info1[1]; // Москва
            $arrivalCity = $info1[2]; // Дели
            $departureDate = $info1[3]; // 21 сентября 2018
            $date = $this->dateYmd($departureDate); // 2018-09-21

            preg_match('#<tr.*?>\s+<td>\s+<strong.*?>(.*?)</strong>\s+<span.*?>(.*?)</span>\s+</td>\s+<td>\s+<span.*?>(.*?)</span>\s+<span.*?>(.*?)</span>\s+<span.*?>(.*?)</span>\s+</td>.*?&rarr;.*?<td.*?>\s+<span.*?>(.*?)</span>\s+<span.*?>(.*?)</span>\s+<span.*?>(.*?)</span>\s+(<strong.*?>(.*?)</strong>\s+)?</td>\s+<td>&nbsp;</td>\s+<td>(.*?)</td>\s+<td.*?>(.*?)</td>\s+</tr>#s', $flight[2], $info2);

            $flightNumber = $info2[1]; // SU 0232
            $flightAirbus = $info2[2]; // Airbus A330-200
            $departureTime = $info2[3]; // 19:10
            $departureAirport = $info2[4]; // SVO
            $departureAirportTerminal = $info2[5]; // F
            $arrivalAirport = $info2[6]; // DEL
            $arrivalAirportTerminal = $info2[7]; // 3
            $arrivalTime = $info2[8]; // 03:30
            $arrivalTimezoneDiff = $info2[10]; // +1
            $duration = $info2[11]; // 5 ч. 50 мин.
            $airline = $info2[12]; // Аэрофлот

            preg_match('#<td.*?>\s+<strong>(.*?)</strong>\s+</td>\s+</tr>#s', $flight[3], $info3);
            $status = $info3[1];

            $departureAirport = Airport::getAirportByCode($departureAirport);
            $arrivalAirport = Airport::getAirportByCode($arrivalAirport);

            $result[]['FlightReservation'] = [
                'reservationFor' => [
                    'provider' => $airline,
                    'arrivalAirport' => $arrivalAirport,
                    'arrivalTime' => $this->toTimestamp($date, $arrivalTime, $arrivalAirport['timezone']),
                    'departureAirport' => $departureAirport,
                    'departureTime' => $this->toTimestamp($date, $departureTime, $departureAirport['timezone']),
                    'flightNumber' => str_replace(' ', '', $flightNumber),
                ],
                'reservationId' => $reservationId,
                'underName' => $passengers,
                'reservationStatus' => $this->getStatus($status),
            ];

        }

        return $result;
    }

    private function dateYmd(string $date)
    {
        // 21 сентября 2018
        $months = ['января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'];

        [$day, $month, $year] = explode(' ', $date);
        $index = array_search($month, $months);
        $month = sprintf('%02d', $index + 1);
        $day = sprintf('%02d', $day);

        return "{$year}-{$month}-{$day}";
    }

    private function toTimestamp(string $date, string $time, string $timezone)
    {
        return (new \DateTime("{$date} {$time}", new \DateTimeZone($timezone)))->format('U');
    }

    private function getStatus(string $status)
    {
        $keys = [
            'Подтвержден' => 'ReservationConfirmed',
            'Отменен' => 'ReservationCancelled',
        ];
        return $keys[$status] ?? null;
    }
}