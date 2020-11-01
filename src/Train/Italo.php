<?php

namespace AccountTravel\EmailReservations\Train;

use AccountTravel\EmailReservations\AbstractEmailParser;

class Italo extends AbstractEmailParser
{
    public array $providerEmailAddresses = ['italo@mail.italotreno.it'];
    public array $providerEmailSubjects = ['Conferma', 'Confirmation'];

    public function getResult(): array
    {
        preg_match('#<tr.*>\s+<td.*>\s+(Data|Departure Date)\s+</td>\s+</tr>\s+<tr.*>\s+<td.*>\s+(\d+)/(\d+)\s+</td>#', $this->html, $date);
        if (!$date) {
            return [];
        }

        $emailDate = $this->message->getHeaderValue('date');
        $emailYear = date('Y', strtotime($emailDate));
        $date = "{$emailYear}-{$date[3]}-{$date[2]}"; // Y-m-d

        $html = str_replace(",\r\n}\r\n}", "\r\n}\r\n}", $this->html);
        echo $html . PHP_EOL . PHP_EOL;
        $result = $this->getMicrodataByHtml($html, $this->getCustomHandlers($date));
        return $this->getResultByMicrodata($result);
    }

    private function getCustomHandlers(string $date)
    {
        $dateTime = function(array $values) use($date) {
            return strtotime("{$date} {$values[0]} (CET)");
        };

        $seats = function(array $values) {
            $properties = $values[0]->properties;
            return [
                'seatSection' => $properties['http://schema.org/seatSection'][0],
                'seatNumber' => $properties['http://schema.org/seatNumber'][0],
            ];
        };

        return [
            'arrivalTime' => $dateTime,
            'departureTime' => $dateTime,
            'ticketedSeat' => $seats
        ];
    }

    public function getResultByMicrodata(array $microdata): array
    {
        $reservation = &$microdata[0]['TrainReservation'];
        $reservation['reservedTicket']['underName'] = $reservation['underName'];
        return $microdata;
    }
}