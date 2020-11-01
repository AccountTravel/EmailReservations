<?php

namespace AccountTravel\EmailReservations;

interface ParserInterface
{
    public function getResult(): array;

    public function isValid(): bool;

    public function isSuccess(): bool;
}