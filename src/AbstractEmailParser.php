<?php

namespace AccountTravel\EmailReservations;

use AccountTravel\EmailReservations\helpers\Airport;
use AccountTravel\EmailReservations\helpers\Date;
use Smalot\PdfParser\Parser;

class AbstractEmailParser extends MicrodataParser
{
    public function getResult(): array
    {
        // Parse microdata
        $result = $this->getMicrodataByHtml($this->html, $this->getHandlers());
        if ($result) {
            return $this->getResultByMicrodata($result);
        }

        // Parse PDF attachment
        foreach ($this->message->getAllAttachmentParts() as $attachment) {
            if ($attachment->getContentType() === 'application/pdf' || pathinfo($attachment->getFilename(), PATHINFO_EXTENSION) === 'pdf') {
                $parser = new Parser();
                $pdf = $parser->parseContent($attachment->getContent());
                $contentByPages = array_map(function($page) {return $page->getText();}, $pdf->getPages());
                $result = $this->getResultByPdf($contentByPages);
                if ($result) {
                    return $result;
                }
            }
        }

        // Parse html
        return $this->getResultByHtml();
    }

    public function getResultByMicrodata(array $microdata): array
    {
        return [];
    }

    public function getResultByPdf(array $pagesContent): array
    {
        return [];
    }

    public function getResultByHtml(): array
    {
        return [];
    }

    protected function fixReservationTimezones(&$reservation)
    {
        $reservationFor = &$reservation['FlightReservation']['reservationFor'];

        $departureAirport = Airport::getAirportByCode($reservationFor['departureAirport']['iata']);
        $reservationFor['departureTime'] = Date::getTimestampByStringRu($departureAirport['timezone'], date('d.m.Y H:i', $reservationFor['departureTime']));

        $arrivalAirport = Airport::getAirportByCode($reservationFor['arrivalAirport']['iata']);
        $reservationFor['arrivalTime'] = Date::getTimestampByStringRu($arrivalAirport['timezone'], date('d.m.Y H:i', $reservationFor['arrivalTime']));
    }
}