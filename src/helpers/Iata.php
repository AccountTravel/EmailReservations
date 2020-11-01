<?php

namespace AccountTravel\EmailReservations\helpers;

class Iata
{
    private static $airlines;

    public static function getAirlineNameByCode(string $code)
    {
        if (is_null(self::$airlines)) {
            self::$airlines = require_once(__DIR__ . '/data/airlines.php');
        }
        return self::$airlines[$code];
    }
}