<?php

namespace AccountTravel\EmailReservations\Flight;

use AccountTravel\EmailReservations\AbstractEmailParser;
use AccountTravel\EmailReservations\helpers\Airport;

class Wizzair extends AbstractEmailParser
{
    public array $providerEmailAddresses = ['noreply@wizzair.com'];

    public function getResultByHtml(): array
    {
        preg_match('#Ваше бронирование (.*?)\.#s', $this->html, $status);

        preg_match('#/FlightReminder/(.*?)/#s', $this->html, $reservationId);
        $reservationId = $reservationId[1];

        preg_match('#<td.*?>\s+Контактное лицо клиента:\s+</td><td.*?>(.*?)</td>#s', $this->html, $underName);
        $underName = trim(preg_replace('#\s+#', ' ', strtr($underName[1], [
            "\r\n" => '',
            ' ' => ' ',
            'MR' => '',
            'MRS' => '',
            'MS' => '',
        ])));
        preg_match('#ПУНКТ ОТПРАВЛЕНИЯ(.*?)ВЫ ЗАЩИЩЕНЫ#s', $this->html, $match);

        preg_match_all('#Номер рейса: (.*?)</td>#', $match[1], $flightNumber, PREG_SET_ORDER);
        preg_match_all('#Аэропорт прибытия.*?<td.*?>(.*?) \(Терминал (.*?)\) \(([\w]+)\).*?arrow-img\.jpg.*?<td.*?>(.*?) \(Терминал (.*?)\) \(([\w]+)\)\s+</td>.*?>([\d.]+) ([\d:]+)</td>.*?>([\d.]+) ([\d:]+)</td>#s', $match[1], $flights, PREG_SET_ORDER);

        $result = [];
        foreach ($flights as $i=>$flight) {
//            $departureAirportName = $flight[1];
            $departureAirportTerminal = $flight[2];
            $departureAirportIata = $flight[3];

//            $arrivalAirportName = $flight[4];
            $arrivalAirportTerminal = $flight[5];
            $arrivalAirportIata = $flight[6];

            $departureDate = $flight[7];
            $departureTime = $flight[8];
            $arrivalDate = $flight[9];
            $arrivalTime = $flight[10];

            $departureAirport = Airport::getAirportByCode($departureAirportIata);
            $arrivalAirport = Airport::getAirportByCode($arrivalAirportIata);

            $result[]['FlightReservation'] = [
                'reservationFor' => [
                    'provider' => 'Wizzair',
                    'arrivalAirport' => $arrivalAirport,
                    'arrivalTerminal' => $arrivalAirportTerminal,
                    'arrivalTime' => $this->toTimestamp($arrivalDate, $arrivalTime, $arrivalAirport['timezone']),
                    'departureAirport' => $departureAirport,
                    'departureTerminal' => $departureAirportTerminal,
                    'departureTime' => $this->toTimestamp($departureDate, $departureTime, $departureAirport['timezone']),
                    'flightNumber' => str_replace(' ', '', $flightNumber[$i][1]),
                ],
                'underName' => $underName,
                'reservationId' => $reservationId,
                'reservationStatus' => $this->getStatus($status[1]),
            ];
        }

        return $result;
    }

    private function toTimestamp(string $date, string $time, string $timezone)
    {
        return (new \DateTime("{$date} {$time}", new \DateTimeZone($timezone)))->format('U');
    }

    private function getStatus(string $status)
    {
        $keys = [
            'подтверждено' => 'ReservationConfirmed',
            'отменено' => 'ReservationCancelled',
        ];
        return $keys[$status] ?? null;
    }
}