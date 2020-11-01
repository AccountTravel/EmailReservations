<?php

namespace AccountTravel\EmailReservations;

use Jkphl\Micrometa\Ports\Format;
use Jkphl\Micrometa\Ports\Parser;

class MicrodataParser extends AbstractParser implements ParserInterface
{
    public function getResult(): array
    {
        $result = $this->getMicrodataByHtml($this->html, $this->getHandlers());
        return $this->getResultByMicrodata($result);
    }

    public function getResultByMicrodata(array $microdata): array
    {
        return $microdata;
    }

    protected function getHandlers()
    {
        $toTimestamp = function($value) {
            $value = is_array($value) ? $value[0] : $value;
            return strtotime($value);
        };

        $addressMap = [
            'postalCode',
            'addressCountry',
            'addressLocality',
            'streetAddress',
        ];
        $addressToString = function(array $values) use ($addressMap) {
            $address = $values[0];
            return implode(', ', array_filter(array_map(function($key) use($address) {
                return $address->properties[$key][0] ?? $address->properties["http://schema.org/{$key}"][0] ?? null;
            }, $addressMap)));
        };

        $airportData = function(array $values) {
            $airport = $values[0]->properties;
            return [
                'iata' => $airport['http://schema.org/iataCode'][0],
                'name' => $airport['http://schema.org/name'][0],
            ];
        };

        return [
            'bookingTime' => $toTimestamp,
            'arrivalTime' => $toTimestamp,
            'departureTime' => $toTimestamp,
            'modifiedTime' => $toTimestamp,
            'checkinDate' => $toTimestamp,
            'checkoutDate' => $toTimestamp,
            'address' => $addressToString,
            'departureAirport' => $airportData,
            'arrivalAirport' => $airportData,
        ];
    }

    protected function getMicrodataByHtml(string $html, array $handlers = [])
    {
        $micrometa = new Parser(Format::ALL);
        $schema = $micrometa('', $html)->toObject();

        $result = [];
        foreach ($schema->items as $i => $item) {
            $type = $item->types[0];
            if (!strpos($type, 'schema.org')) {
                continue;
            }
            $type = $this->prepareKey($type);

            foreach ($item->properties as $key => $property) {
                $this->preparePropertiesRecursive($result[$i][$type], $key, $property, $handlers);
            }
        }

        return $result;
    }

    private function defaultHandler($propertyValues)
    {
        return is_object($propertyValues[0]) && isset($propertyValues[0]->properties['http://schema.org/name'][0])
            ? $propertyValues[0]->properties['http://schema.org/name'][0]
            : $propertyValues[0];
    }

    private function preparePropertiesRecursive(&$result, $key, $property, $handlers)
    {
        $key = $this->prepareKey($key);
        $property = $property[0];

        if (is_object($property)) {
            foreach ($property->properties as $propertyKey => $propertyValues) {
                $propertyKey = $this->prepareKey($propertyKey);

                if (isset($handlers[$propertyKey])) {
                    $result[$key][$propertyKey] = $handlers[$propertyKey]($propertyValues);
                } else {
                    $propertyString = $this->defaultHandler($propertyValues);
                    if (is_string($propertyString)) {
                        $result[$key][$propertyKey] = $propertyString;
                    } else {
                        $this->preparePropertiesRecursive($result[$key], $propertyKey, $propertyValues, $handlers);
                    }
                }
            }
        } else {
            $result[$key] = isset($handlers[$key])
                ? $handlers[$key]($property)
                : $this->prepareKey($property);
        }
    }

    /**
     * @param string $key - http://schema.org/FlightReservation
     * @return string - FlightReservation
     */
    private function prepareKey(string $key)
    {
        return str_replace('http://schema.org/', '', $key);
    }

    public function isSuccess(): bool
    {
        return !empty($this->result);
    }

    public function renameKey(&$object, $keyOld, $keyNew)
    {
        if (isset($object[$keyOld])) {
            $object[$keyNew] = $object[$keyOld];
            unset($object[$keyOld]);
        }
    }
}