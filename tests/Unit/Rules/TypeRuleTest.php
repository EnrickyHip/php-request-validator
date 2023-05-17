<?php

use Enricky\RequestValidator\Enums\DataType;
use Enricky\RequestValidator\Exceptions\InvalidDataTypeException;
use Enricky\RequestValidator\Rules\TypeRule;

it("should not be a major rule", function () {
    $typeRule = new TypeRule(DataType::INT, "wrong data type");
    expect($typeRule->isMajor())->toBeTrue();
});

it("should return the correct error message", function () {
    $typeRule = new TypeRule(DataType::INT, "wrong data type");
    expect($typeRule->getMessage())->toBe("wrong data type");
});

it("should throw InvalidDataTypeException if the data type sent as string does not exist", function (string $invalidType) {
    $closure = fn () => new TypeRule($invalidType, "wrong data type");
    expect($closure)->toThrow(InvalidDataTypeException::class);
})->with(["strin", "integer", "double", "boolean", "", "aaaaaa"]);

it("should validate if sent value is null", function () {
    $typeRule = new TypeRule(DataType::INT, "wrong data type");
    expect($typeRule->validate(null))->toBeTrue();
});

it("should validate if value type is correct", function (DataType|string $type, mixed $value) {
    $typeRule = new TypeRule($type, "wrong data type");
    expect($typeRule->validate($value))->toBeTrue();
})->with([
    [DataType::INT, 1],
    [DataType::INT, 10],
    [DataType::STRING, "text"],
    [DataType::STRING, "another text"],
    [DataType::BOOL, true],
    [DataType::BOOL, false],
    [DataType::FLOAT, 1.5],
    [DataType::FLOAT, 10.5],
    [DataType::FLOAT, 1],
    [DataType::FLOAT, 10],
    ["int", 1],
    ["INT", 10],
    ["string", "text"],
    ["STRING", "12345"],
    ["bool", true],
    ["BOOL", false],
    ["float", 1.5],
    ["FLOAT", 10.5],
    ["float", 1],
    ["FLOAT", 10],
]);

it("should not validate if value type is not correct", function (DataType|string $type, mixed $value) {
    $typeRule = new TypeRule($type, "wrong data type");
    expect($typeRule->validate($value))->toBeFalse();
})->with([
    [DataType::INT, "1"],
    [DataType::INT, true],
    [DataType::INT, []],
    [DataType::INT, 10.6],
    [DataType::STRING, 1],
    [DataType::STRING, false],
    [DataType::BOOL, "true"],
    [DataType::BOOL, 1],
    [DataType::BOOL, 0],
    [DataType::FLOAT, "adasd"],
    [DataType::FLOAT, false],
    [DataType::FLOAT, "10"],
    [DataType::FLOAT, "10.5"],
    ["int", "1"],
    ["INT", true],
    ["int", []],
    ["INT", 10.6],
    ["STRING", 1],
    ["string", false],
    ["BOOL", "true"],
    ["bool", 1],
    ["BOOL", 0],
    ["float", "adasd"],
    ["FLOAT", false],
    ["float", "10"],
    ["FLOAT", "10.5"],
]);
