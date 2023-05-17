<?php

use Enricky\RequestValidator\Exceptions\InvalidDataTypeException;

it("should throw InvalidDataTypeException exception", function () {
    throw new InvalidDataTypeException("Invalid data type");
})->throws(InvalidDataTypeException::class, "Invalid data type");
