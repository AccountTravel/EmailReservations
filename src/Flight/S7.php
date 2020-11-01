<?php

namespace AccountTravel\EmailReservations\Flight;

use AccountTravel\EmailReservations\AbstractEmailParser;
use AccountTravel\EmailReservations\helpers\Airport;
use AccountTravel\EmailReservations\helpers\Date;
use AccountTravel\EmailReservations\helpers\Timezone;

class S7 extends AbstractEmailParser
{
    public array $providerEmailAddresses = ['etix.aero@s7.ru', 'noreply@s7.ru'];

    public function getResultByMicrodata(array $microdata): array
    {
        $reservation = $microdata[1];
        $flightReservation = &$reservation['FlightReservation'];
        $reservationFor = &$flightReservation['reservationFor'];

        $this->renameKey($reservationFor, 'airline', 'provider');
        $reservationFor['departureAirport']['name'] = html_entity_decode($reservationFor['departureAirport']['name']);
        $reservationFor['arrivalAirport']['name'] = html_entity_decode($reservationFor['arrivalAirport']['name']);

        // В разметке неверно указано время рейсов (не учитываются временные зоны)
        $this->fixReservationTimezones($reservation);

        $flightReservation['underName'] = $flightReservation['underName']['name'];
        $this->renameKey($flightReservation, 'reservationNumber', 'reservationId');

        // Статус определяем по теме письма
        if (!isset($flightReservation['reservationStatus'])) {
            $subject = $this->message->getHeaderValue('subject');
            $flightReservation['reservationStatus'] = mb_stripos($subject, 'отмен') ? 'ReservationCancelled' : 'ReservationConfirmed';
        }

        return [
            $reservation
        ];
    }

    public function getResultByPdf(array $pagesContent): array
    {
        $content = implode(PHP_EOL, $pagesContent);

        preg_match('#PNR\s+(.*?)\s#s', $content, $reservationId);
        $reservationId = $reservationId[1] ?? null;

        preg_match('#Issued\s+\d+ \w+ (\d+)\s#us', $content, $issuedDate);
        $year = $issuedDate[1] ?? null; // todo год из даты заказа, а не полета

        preg_match('#Status\s+(.*?)\s#s', $content, $status);
        $status = $status[1] ?? null;

//        preg_match('#\s+(S7 \d+)\s#s', $content, $flightNumber);
//        $flightNumber = isset($flightNumber[1]) ? str_replace(' ', '', $flightNumber[1]) : null;

        preg_match('#Номер билета\s+(Mr|Ms|Mrs)+\s(.*?)\s\d#s', $content, $passenger);
        $passenger = $passenger[2] ?? null;

        preg_match('#Маршрут(.*)Мое бронирование#s', $content, $itinerary);
        $itineraryLines = explode(PHP_EOL, $itinerary[1]);

        $departure = $this->getArrivalDeparture("{$itineraryLines[4]} {$year} {$itineraryLines[5]}");
        $arrival = $this->getArrivalDeparture("{$itineraryLines[6]} {$year} {$itineraryLines[7]}");

        $flightNumber = str_replace(' ', '', $itineraryLines[2]);

        return [
            [
                'FlightReservation' => [
                    'reservationFor' => [
                        'provider' => 'S7',
                        'departureAirport' => $departure['airport'],
                        'arrivalAirport' => $arrival['airport'],
                        'departureTime' => $departure['time'],
                        'arrivalTime' => $arrival['time'],
                        'flightNumber' => $flightNumber,
                    ],
                    'underName' => $passenger,
                    'reservationId' => $reservationId,
                    'reservationStatus' => 'ReservationConfirmed',
                ]
            ]
        ];
    }

    private function getArrivalDeparture(string $string)
    {
        preg_match('#(\d+ \w+ \d+ \d+:\d+)([^,]+)(, (\w+))?(Терминал: (\d+))?\s#su', $string, $result);

        $city = $result[2];
        $timestamp = Date::getTimestampByStringRu(Timezone::getTimezoneByCity($city), $result[1]);
        $airport = Airport::getAirportByName($result[4] ?? $city);

        return [
            'time' => $timestamp,
            'airport' => $airport,
            'terminal' => $result[6] ?? null,
        ];
    }
}