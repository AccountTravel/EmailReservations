<?php

namespace AccountTravel\EmailReservations\Flight;

use AccountTravel\EmailReservations\AbstractEmailParser;
use AccountTravel\EmailReservations\helpers\Airport;

class Biletik extends AbstractEmailParser
{
    public array $providerEmailAddresses = ['noreply@biletik.aero'];

    public function getResultByHtml(): array
    {
        preg_match('#Вылет<br />Departure</span></td>\s+<td.*?><span.*?><strong>(.*?) \((.*?)\)\s+(Терминал: (.*?))?<br />(.*?)</strong><br /><span.*?><strong>(.*?)</strong>#s', $this->html, $departure);
        preg_match('#Прибытие<br />Arrival</span></td>\s+<td.*?><span.*?><strong>(.*?) \((.*?)\)\s+(Терминал: (.*?)<br />)?(.*?)</strong><br /><span.*?><strong>(.*?)</strong>#s', $this->html, $arrival);

        preg_match('#Номер брони в авиакомпании:<br /><span.*?>(.*?)</span>#s', $this->html, $number);

        preg_match('#Данный рейс выполняет:<br /><span.*?>(.*?)</span>#s', $this->html, $airline);

        preg_match('#Рейс/Flight:</span><br /><span.*?>(.*?)(<br />.*?)?</span>#', $this->html, $flightNumber);

        preg_match('#Пассажир / Документ<br />Passenger / Document</span><br /><span.*?>(Mr.|Ms.) (.*?) /&nbsp;  (\d+)</span>#', $this->html, $passenger);

        $result = [];
//        foreach ($flights as $i=>$flight) {
        $departureAirportName = $departure[1];
        $departureAirportTerminal = $departure[4];
        $departureAirportIata = $departure[2];
        $departureDate = $this->dateYmd($departure[6]);
        $departureTime = strip_tags($departure[5]);

        $arrivalAirportName = $arrival[1];
        $arrivalAirportTerminal = $arrival[4];
        $arrivalAirportIata = $arrival[2];
        $arrivalDate = $this->dateYmd($arrival[6]);
        $arrivalTime = strip_tags($arrival[5]);

        $departureAirport = Airport::getAirportByCode($departureAirportIata);
        $arrivalAirport = Airport::getAirportByCode($arrivalAirportIata);

        $result[]['FlightReservation'] = [
            'reservationFor' => [
                'provider' => $airline[1],
                'arrivalAirport' => $arrivalAirport,
                'arrivalTime' => $this->toTimestamp($arrivalDate, $arrivalTime, $arrivalAirport['timezone']),
                'departureAirport' => $departureAirport,
                'departureTime' => $this->toTimestamp($departureDate, $departureTime, $departureAirport['timezone']),
                'flightNumber' => $flightNumber[1],
            ],
            'underName' => strtr($passenger[2], ['  ' => ' ']),
            'reservationId' => $number[1],
            'reservationStatus' => 'ReservationConfirmed',
        ];
//        }

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
}