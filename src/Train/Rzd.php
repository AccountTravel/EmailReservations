<?php

namespace AccountTravel\EmailReservations\Train;

use AccountTravel\EmailReservations\AbstractEmailParser;

class Rzd extends AbstractEmailParser
{
    public array $providerEmailAddresses = ['noreply@rzd.ru'];
    public array $providerEmailSubjects = ['Your tickets'];

    protected array $values;

    public function getResultByHtml(): array
    {
        $this->values['reservationFor'] = [];
        $this->values['reservedTicket'] = [];

        $this->values['reservationFor']['trainCompany'] = 'РЖД';
        $this->values['reservationStatus'] = 'ReservationConfirmed';
        $this->parseRussianVersion();
        if ($this->isSuccess()) {
            return $this->prepareResult();
        }

        $this->parseEnglishVersion();
        return $this->prepareResult();
    }

    public function prepareResult()
    {
        return [
            ['TrainReservation' => $this->values]
        ];
    }

    public function isSuccess(): bool
    {
        $result = $this->prepareResult();
        $firstReservation = $result[0] ?? [];

        return array_key_exists('TrainReservation', $firstReservation)
            && ($firstReservation['TrainReservation']['reservationFor']['departureStation'] ?: false);
    }

    private function parseRussianVersion()
    {
        preg_match('#<li>\s+Поезд №(.*?)\s+</li>#s', $this->html, $train);
        $this->values['reservationFor']['trainNumber'] = $train[1] ?? null;

        preg_match('#<li>\s+Вагон №(.*?)\s+</li>#s', $this->html, $carriage);
        preg_match('#<li>\s+Место\(а\) №(.*?)\s+</li>#s', $this->html, $seats);
        $this->values['reservedTicket']['ticketedSeat'] = [
            'seatSection' => isset($carriage[1]) ? intval($carriage[1]) : null,
            'seatNumber' => isset($seats[1]) ? intval($seats[1]) : null,
        ];

        preg_match('#<li>\s+Время отправления: (.*?)\s+</li>#s', $this->html, $departureTime);
        $this->values['reservationFor']['departureTime'] = $this->toTimestamp($departureTime[1], true);

        preg_match('#<li>\s+Станция отправления: (.*?)\s+</li>#s', $this->html, $departureStation);
        $this->values['reservationFor']['departureStation'] = $departureStation[1] ?? null;

        preg_match('#<li>\s+Время прибытия: (.*?)\s+</li>#s', $this->html, $arrivalTime);
        $this->values['reservationFor']['arrivalTime'] = $this->toTimestamp($arrivalTime[1], true);

        preg_match('#<li>\s+Станция прибытия: (.*?)\s+</li>#s', $this->html, $arrivalStation);
        $this->values['reservationFor']['arrivalStation'] = $arrivalStation[1] ?? null;
    }

    private function parseEnglishVersion()
    {
        preg_match('#<li>\s+Train No(.*?)\s+</li>#s', $this->html, $train);
        $this->values['reservationFor']['trainNumber'] = $train[1] ?? null;

        preg_match('#<li>\s+Carriage No\.(.*?)\s+</li>#s', $this->html, $carriage);
        preg_match('#<li>\s+Seat\(s\)/Berth\(s\) No\(s\)\.(.*?)\s+</li>#s', $this->html, $seats);
        $this->values['reservedTicket']['ticketedSeat'] = [
            'seatSection' => $carriage[1] ?? null,
            'seatNumber' => $seats[1] ?? null,
        ];

        preg_match('#<li>\s+Departure time: (.*?)\s+</li>#s', $this->html, $departureTime);
        $this->values['reservationFor']['departureTime'] = $this->toTimestamp($departureTime[1], false);

        preg_match('#<li>\s+Departure station: (.*?)\s+</li>#s', $this->html, $departureStation);
        $this->values['reservationFor']['departureStation'] = $departureStation[1] ?? null;

        preg_match('#<li>\s+Arrival time: (.*?)\s+</li>#s', $this->html, $arrivalTime);
        $this->values['reservationFor']['arrivalTime'] = $this->toTimestamp($arrivalTime[1], false);

        preg_match('#<li>\s+Arrival station: (.*?)\s+</li>#s', $this->html, $arrivalStation);
        $this->values['reservationFor']['arrivalStation'] = $arrivalStation[1] ?? null;
    }

    private function toTimestamp(?string $date, $isRussian = true)
    {
        if (!$date) {
            return null;
        }
        $search = $isRussian ? [' в', 'МСК'] : [' at'];
        $replace = $isRussian ? ['', 'MSK'] : [''];
        $dateTime = str_replace($search, $replace, $date); // 07.10.2019 18:06(MSK)

        return strtotime($dateTime);
    }
}