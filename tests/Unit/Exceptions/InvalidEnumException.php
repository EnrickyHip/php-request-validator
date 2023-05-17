<?php

use Enricky\RequestValidator\Exceptions\InvalidEnumException;

it("should throw InvalidEnumException exception", function () {
    throw new InvalidEnumException("Invalid enum class");
})->throws(InvalidEnumException::class, "Invalid enum class");
