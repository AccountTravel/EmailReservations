<?php

namespace AccountTravel\EmailReservations;

use AccountTravel\EmailReservations\Flight\Aeroflot;
use AccountTravel\EmailReservations\Flight\AirArabia;
use AccountTravel\EmailReservations\Flight\Biletik;
use AccountTravel\EmailReservations\Flight\Kiwi;
use AccountTravel\EmailReservations\Flight\S7;
use AccountTravel\EmailReservations\Flight\Tinkoff;
use AccountTravel\EmailReservations\Flight\Utair;
use AccountTravel\EmailReservations\Flight\Wizzair;
use AccountTravel\EmailReservations\Lodging\Booking;
use AccountTravel\EmailReservations\Train\Italo;
use AccountTravel\EmailReservations\Train\RegioJet;
use AccountTravel\EmailReservations\Train\Rzd;
use Exception;
use ZBateson\MailMimeParser\Message;

class Parser
{
    public function parseFile(string $filename)
    {
        $content = file_get_contents($filename);
        return $this->parseContent($content);
    }

    public function parseContent(string $content)
    {
        $message = Message::from($content);

        $all = [
            new Aeroflot($message),
            new AirArabia($message),
            new Biletik($message),
            new Kiwi($message),
            new S7($message),
            new Tinkoff($message),
            new Utair($message),
            new Wizzair($message),

            new Italo($message),
            new RegioJet($message),
            new Rzd($message),

            new Booking($message),

            new MicrodataParser($message),
        ];

        $parser = $this->getSuitableParser($all);
        if (!$parser) {
            throw new Exception('Undefined email type');
        }

        return $parser->getResult();
    }

    private function getSuitableParser(array $parsers): ?ParserInterface
    {
        foreach ($parsers as $parser) {
            if ($parser->isValid()) {
                return $parser;
            }
        }

        return null;
    }
}