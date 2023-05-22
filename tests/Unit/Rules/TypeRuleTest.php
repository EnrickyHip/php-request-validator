<?php

use Enricky\RequestValidator\Enums\DataType;
use Enricky\RequestValidator\Exceptions\InvalidDataTypeException;
use Enricky\RequestValidator\Rules\TypeRule;

it("should not be a major rule", function () {
    $typeRule = new TypeRule(DataType::INT);
    expect($typeRule->isMajor())->toBeTrue();
});

it("should return the default error message", function () {
    $typeRule = new TypeRule(DataType::INT);
    expect($typeRule->getMessage())->toBe("field :fieldName is not a :type type");
});

it("should return the custom error message", function () {
    $typeRule = new TypeRule(DataType::INT, "wrong data type");
    expect($typeRule->getMessage())->toBe("wrong data type");
});

it("should throw InvalidDataTypeException if the data type sent as string does not exist", function (string $invalidType) {
    $closure = fn () => new TypeRule($invalidType);
    expect($closure)->toThrow(InvalidDataTypeException::class);
})->with(["strin", "integer", "double", "boolean", "", "aaaaaa"]);

it("should validate if sent value is null", function () {
    $typeRule = new TypeRule(DataType::INT);
    expect($typeRule->validate(null))->toBeTrue();
});

it("should validate if value type is correct", function (DataType|string $type, mixed $value) {
    $typeRule = new TypeRule($type);
    expect($typeRule->validate($value))->toBeTrue();
})->with("correct_types");

it("should not validate if value type is not correct", function (DataType|string $type, mixed $value) {
    $typeRule = new TypeRule($type);
    expect($typeRule->validate($value))->toBeFalse();
})->with("incorrect_types");
