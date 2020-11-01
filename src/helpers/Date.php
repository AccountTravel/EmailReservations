<?php

namespace AccountTravel\EmailReservations\helpers;

class Date
{
    const MONTHS = [
        ['jan', 'янв', 'янв', 'январь', 'января'],
        ['feb', 'фев', 'фев', 'февраль', 'февраля'],
        ['mar', 'мар', 'мар', 'март', 'марта'],
        ['apr', 'апр', 'апр', 'апрель', 'апреля'],
        ['may', 'май', 'май', 'май', 'мая'],
        ['jun', 'июн', 'июн', 'июнь', 'июня'],
        ['jul', 'июл', 'июл', 'июль', 'июля'],
        ['aug', 'авг', 'авг', 'август', 'августа'],
        ['sep', 'сен', 'сент', 'сентябрь', 'сентября'],
        ['oct', 'окт', 'окт', 'октябрь', 'октября'],
        ['nov', 'ноя', 'ноя', 'ноябрь', 'ноября'],
        ['dec', 'дек', 'дек', 'декабрь', 'декабря'],
    ];

    public static function getTimestampByStringRu(string $timezone, string $date)
    {
        $date = mb_strtolower($date);
        foreach (self::MONTHS as $month) {
            $date = strtr($date, [
                $month[4] => $month[0],
                $month[3] => $month[0],
                $month[2] => $month[0],
                $month[1] => $month[0],
            ]);
        }
        $date = str_replace(' ', ' ', $date);
        $dateTime = date('Y-m-d H:i:s', strtotime($date));
        return (new \DateTime($dateTime, new \DateTimeZone($timezone)))->format('U');
    }
}