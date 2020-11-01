<?php

namespace AccountTravel\EmailReservations\helpers;

class Timezone
{
    private static $timezoneByCity;

    public static function getTimezoneByCity(string $cityName)
    {
        if (is_null(self::$timezoneByCity)) {
            $timezones = [
                'Europe/Amsterdam' => ['Амстердам'],
                'Europe/Andorra' => ['Андорра'],
                'Europe/Astrakhan' => ['Астрахань'],
                'Europe/Athens' => ['Афины'],
                'Europe/Belgrade' => ['Белград'],
                'Europe/Berlin' => ['Берлин'],
                'Europe/Bratislava' => ['Братислава'],
                'Europe/Brussels' => ['Брюссель'],
                'Europe/Bucharest' => ['Бухарест'],
                'Europe/Budapest' => ['Будапешт'],
                'Europe/Busingen' => ['Бюзинген'],
                'Europe/Chisinau' => ['Кишинев'],
                'Europe/Copenhagen' => ['Копенгаген'],
                'Europe/Dublin' => ['Дублин'],
                'Europe/Gibraltar' => ['Гибралтар'],
                'Europe/Guernsey' => ['Гернси'],
                'Europe/Helsinki' => ['Хельсинки'],
                'Europe/Isle_of_Man' => ['Остров Мэн'],
                'Europe/Istanbul' => ['Стамбул'],
                'Europe/Jersey' => ['Джерси'],
                'Europe/Kaliningrad' => ['Калининград'],
                'Europe/Kiev' => ['Киев'],
                'Europe/Kirov' => ['Киров'],
                'Europe/Lisbon' => ['Лиссабон'],
                'Europe/Ljubljana' => ['Любляна'],
                'Europe/London' => ['Лондон'],
                'Europe/Luxembourg' => ['Люксембург'],
                'Europe/Madrid' => ['Мадрид'],
                'Europe/Malta' => ['Мальта'],
                'Europe/Mariehamn' => ['Мариехамн'],
                'Europe/Minsk' => ['Минск'],
                'Europe/Monaco' => ['Монако'],
                'Europe/Moscow' => ['Москва', 'Санкт-Петербург', 'Саранск'],
                'Europe/Oslo' => ['Осло'],
                'Europe/Paris' => ['Париж'],
                'Europe/Podgorica' => ['Подгорица'],
                'Europe/Prague' => ['Прага'],
                'Europe/Riga' => ['Рига'],
                'Europe/Rome' => ['Рим'],
                'Europe/Samara' => ['Самара'],
                'Europe/San_Marino' => ['Сан-Марино'],
                'Europe/Sarajevo' => ['Сараево'],
                'Europe/Saratov' => ['Саратов'],
                'Europe/Simferopol' => ['Симферополь'],
                'Europe/Skopje' => ['Скопье'],
                'Europe/Sofia' => ['София'],
                'Europe/Stockholm' => ['Стокгольм'],
                'Europe/Tallinn' => ['Таллин'],
                'Europe/Tirane' => ['Тирана'],
                'Europe/Ulyanovsk' => ['Ульяновск'],
                'Europe/Uzhgorod' => ['Ужгород'],
                'Europe/Vaduz' => ['Вадуц'],
                'Europe/Vatican' => ['Ватикан'],
                'Europe/Vienna' => ['Вена'],
                'Europe/Vilnius' => ['Вильнюс'],
                'Europe/Volgograd' => ['Волгоград'],
                'Europe/Warsaw' => ['Варшава'],
                'Europe/Zagreb' => ['Загреб'],
                'Europe/Zaporozhye' => ['Запорожье'],
                'Europe/Zurich' => ['Цюрих'],








                'Asia/Dubai' => ['Дубай', 'Шарджа'],
                'Asia/Kathmandu' => ['Катманду'],
            ];

            foreach ($timezones as $timezone => $cities) {
                foreach ($cities as $city) {
                    self::$timezoneByCity[mb_strtolower($city, 'utf-8')] = $timezone;
                }
            }
        }

        return self::$timezoneByCity[mb_strtolower($cityName, 'utf-8')] ?? null;
    }
}