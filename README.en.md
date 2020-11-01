# Parsing travel reservations from email

Parsing email messages with travel tickets and creating schema.org reservation structure:
- https://schema.org/BusReservation
- https://schema.org/FlightReservation
- https://schema.org/LodgingReservation
- https://schema.org/TrainReservation

## Install
```shell script
composer require account-travel/email-reservations
```

## Usage
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

We want to make life easier for independent travelers. Tasks can be solved faster if there are supporters.

## How can you help

### Share reservation tickets
We would be grateful if you have emails with travel reservations and don't mind sharing it with our team. It can be done by forwarding the original email to [dev@account.travel](mailto:dev@account.travel).

### Development

We will be glad to new contributors.

Your can coding parser for a new travel provider or fix exists. There are few recommendations before start:
1. Make sure there are needs some works for this provider: directory `/src` doesn't contain class for a new provider.
2. You should be able to create and run tests. See `/tests/*` directories for example. 
Run: `php ./vendor/bin/phpunit`
3. You agree to share the original emails with everyone or have permissions to publish other people's emails. Directory `fixtures` in tests.
