<?php

namespace AccountTravel\EmailReservations\helpers;

class Airport
{
    private static array $resultsByIata = [];
    private static array $resultsByName = [];

    public static function getAirportByCode(string $iata)
    {
        if (!self::$resultsByIata) {
            self::init();
        }
        return self::$resultsByIata[$iata] ?? null;
    }

    public static function getAirportByName(string $name)
    {
        if (!self::$resultsByName) {
            self::init();
        }
        return self::$resultsByName[$name] ?? null;
    }

    private static function init()
    {
        $file = fopen(__DIR__ . '/data/airports.csv', 'r');
        while ($line = fgetcsv($file, 0, ';')) {
            $data = [
                'iata' => $line[0],
                'name_en' => $line[3],
                'name' => $line[4],
                'timezone' => $line[5],
            ];
            self::$resultsByIata[$line[0]] = $data;
            self::$resultsByName[$line[4]] = $data;
        }
        fclose($file);
    }
}