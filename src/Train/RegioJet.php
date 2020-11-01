<?php

namespace AccountTravel\EmailReservations\Train;

use AccountTravel\EmailReservations\AbstractEmailParser;
use AccountTravel\EmailReservations\helpers\Timezone;

class RegioJet extends AbstractEmailParser
{
    public array $providerEmailAddresses = ['info@regiojet.cz'];
    public array $providerEmailSubjects = ['Подтверждение'];

    public function getResultByHtml(): array
    {
        preg_match('#Подтверждение бронирования <b.*?>(.*?)</b>#s', $this->html, $reservationId);
        $reservationId = $reservationId[1];

        preg_match('#<h2.*?>Поездка</h2>(.*?)</table>#s', $this->html, $table);

        preg_match_all('#<th.*?>(.*?)</th>#s', $table[1], $tableHeaderColumns);
        $tableHeaderColumns = $tableHeaderColumns[1];

        preg_match_all('#<td.*?>(.*?)</td>#s', $table[1], $tableRowsColumns);
        $tableRowsColumns = $tableRowsColumns[1];

        $columnsCount = count($tableHeaderColumns);
        $rowColumnsCount = count($tableRowsColumns);
        $rows = [];
        for ($i = 0; $i < $rowColumnsCount; $i++) {
            $colIndex = $i % $columnsCount;
            $colName = $tableHeaderColumns[$colIndex];
            $rowIndex = intdiv($i, $columnsCount);
            $rows[$rowIndex][$colName] = $tableRowsColumns[$i];
        }

        $departure = $rows[0];
        $arrival = $rows[1];

        $departureStation = $departure['Остановка/пересадка'];
        $departureTimezone = Timezone::getTimezoneByCity(explode(',', $departureStation)[0]);
        preg_match('#\w+ (\d+).(\d+).(\d+)#u', $departure['Дата'], $departureDate);
        [$date, $day, $month, $year] = $departureDate;
        $departureDateTime = "20{$year}-{$month}-{$day} {$departure['Отбытие']}";
        $departureTime = (new \DateTime($departureDateTime, new \DateTimeZone($departureTimezone)))->format('U');

        $arrivalStation = $arrival['Остановка/пересадка'];
        $arrivalTimezone = Timezone::getTimezoneByCity(explode(',', $arrivalStation)[0]);
        if ($arrival['Дата']) {
            preg_match('#\w+ (\d+).(\d+).(\d+)#u', $arrival['Дата'], $arrivalDate);
            [$date, $day, $month, $year] = $arrivalDate;
        }

        $arrivalDateTime = "20{$year}-{$month}-{$day} {$arrival['Прибытие']}";
        $arrivalTime = (new \DateTime($arrivalDateTime, new \DateTimeZone($arrivalTimezone)))->format('U');

        preg_match('#\(RJ, (RJ \d+)\)#s', $departure['Автобус/поезд'], $trainNumber);
        preg_match('#<span.*?>(\d+)/(\d+)</span>#s', $departure['Вагон/места'], $seat);

        return [
            [
                'TrainReservation' => [
                    'reservationFor' => [
                        'trainCompany' => 'RegioJet',
                        'trainNumber' => $trainNumber[1],
                        'departureTime' => $departureTime,
                        'arrivalTime' => $arrivalTime,
                        'departureStation' => $departureStation,
                        'arrivalStation' => $arrivalStation,
                    ],
                    'reservedTicket' => [
                        'ticketedSeat' => [
                            'seatSection' => $seat[1],
                            'seatNumber' => $seat[2],
                        ],
                    ],
                    'reservationId' => $reservationId,
                    'reservationStatus' => 'ReservationConfirmed',
                ]
            ]
        ];
    }
}