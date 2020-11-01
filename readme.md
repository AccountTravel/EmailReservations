#Парсинг писем с бронями туристических услуг

Парсинг email-сообщений с бронями (билетами) и создание на их основе соответствующей структуры schema.org:
- https://schema.org/BusReservation
- https://schema.org/FlightReservation
- https://schema.org/LodgingReservation
- https://schema.org/TrainReservation

## Установка
```shell script
composer install
```

## Примеры
```php
require_once('vendor/autoload.php');

$parser = new AccountTravel\EmailReservations\Parser();

// parse .eml file
$result1 = $parser->parseFile('path_to_file.eml'); 
print_r($result1);

// parse email content
$content = '...'; // file_get_contents, procmail
$result2 = $parser->parseContent($content); 
print_r($result2);
```
---

<img src="https://account.travel/img/logo2x_180.png" alt="Account.Travel" width="180"/>

Мы стараемся упростить жизнь самостоятельным путешественникам, для этого разрабатываем инструменты, которые помогают в планировании поездки. Если ты разделяешь наше начинание и хочешь помочь, читай об этом ниже.

## Как можно помочь?

### Поделись своими бронями
Будем признательны, если поделишься с нашей командой своими письмами с бронированиями апартаментов, билетов на самолеты или поезда, переслав сообщения на [dev@account.travel](mailto:dev@account.travel).

[У нас уже есть парсеры для некоторых поставщиков](./docs/providers.md), но существует и много других. Надеемся общими усилиями собрать как можно больше примеров, чтобы сделать обработчик под каждого провайдера.

### Разработка

Если хочешь помочь с разработкой, милости просим. Только предварительно почитай рекомендации:
1. Удостоверься, что парсер действительно нужен: посмотри, нет ли уже нужного класса в `/src`.
2. Умеешь писать и запускать тесты. <br>Для примера смотри папку `/tests/*`. <br>Запуск: `php ./vendor/bin/phpunit`
3. Размещая оригиналы email-сообщений в `/tests/*/fixtures`, ты подтверждаешь свое согласие на то, что их содержимое станет общедоступно. Или, если это не твои письма, подтверждаешь согласие от имени их оригинального получателя.