<?php

namespace AccountTravel\EmailReservations\Tests;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    public function assertEmptyDiff($expected, $actual, $message = '')
    {
        for ($i = 0; $i < count($expected); $i++) {
            $expectedItem = $expected[$i] ?? [];
            $actualItem = $actual[$i] ?? [];
            $diff = self::arrayRecursiveDiff($expectedItem, $actualItem);

            $diffMessage = 'Diff: ' . print_r($diff, true) . PHP_EOL . 'Actual: ' . print_r($actualItem, true);

            $this->assertEmpty($diff, $message ?: $diffMessage);
        }
    }

    protected static function arrayRecursiveDiff($array1, $array2)
    {
        $diff = [];

        foreach ($array1 as $key => $val) {
            if (array_key_exists($key, $array2)) {
                if (is_array($val)) {
                    $recursiveDiff = self::arrayRecursiveDiff($val, $array2[$key]);
                    if (count($recursiveDiff)) { $diff[$key] = $recursiveDiff; }
                } else {
                    if ($val != $array2[$key]) {
                        $diff[$key] = $val;
                    }
                }
            } else {
                $diff[$key] = $val;
            }
        }
        return $diff;
    }
}