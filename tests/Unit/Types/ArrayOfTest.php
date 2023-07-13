<?php

use Enricky\RequestValidator\Abstract\DataTypeInterface;
use Enricky\RequestValidator\Types\DataType;
use Enricky\RequestValidator\Types\ArrayOf;

it("should return name as 'array' when no type is specified", function () {
    $array = new ArrayOf();
    expect($array->getName())->toBe("array");
});

it("should return name as '{type}[]' when no type is specified", function () {
    $array = new ArrayOf(DataType::INT);
    expect($array->getName())->toBe("int[]");

    $array = new ArrayOf("string");
    expect($array->getName())->toBe("string[]");
});

it("should return name as '{type}[][]' when using array of arrays", function () {
    $array = new ArrayOf(new ArrayOf("bool"));
    expect($array->getName())->toBe("bool[][]");
});

it("should not validate if value is not an array", function () {
    $array = new ArrayOf();
    expect($array->validate("string"))->toBeFalse();
    expect($array->strictValidate("string"))->toBeFalse();
});

it("should validate when value is array and no type is sent", function (array $value) {
    $arrayOf = new ArrayOf();
    expect($arrayOf->validate($value))->toBeTrue();
    expect($arrayOf->strictValidate($value))->toBeTrue();
})->with([
    fn () => [1, "string", true],
    fn () => ["string", false, null],
    fn () => [],
]);

it("should validate if all element have the specified type on strict mode", function (DataTypeInterface $type, array $array) {
    $arrayOf = new ArrayOf($type);
    $result = $arrayOf->strictValidate($array);
    expect($result)->toBeTrue();
})->with([
    [DataType::INT, fn () => [1, 2, 3]],
    [DataType::BOOL, fn () => [true, false, true]],
    [DataType::FLOAT, fn () => [1.5, 10.0, 10]],
    [DataType::STRING, fn () => ["string", "test", "text"]],
    [new ArrayOf("int"), fn () => [[1, 2], [3, 4]]],
    [new ArrayOf("bool"), fn () => [[true, true], [false, false]]],
    [new ArrayOf("float"), fn () => [[1.5, 10.0], [10, 7.25]]],
    [new ArrayOf("string"), fn () => [["just", "a"], ["simple", "test"]]],
]);

it("should validate if all element have the specified type on non strict mode", function (DataTypeInterface $type, array $array) {
    $arrayOf = new ArrayOf($type);
    $result = $arrayOf->validate($array);
    expect($result)->toBeTrue();
})->with([
    [DataType::INT, fn () => [1, 2, "3"]],
    [DataType::BOOL, fn () => [true, false, "true", "1", 0]],
    [DataType::FLOAT, fn () => [1.5, "10.0", 10]],
    [DataType::STRING, fn () => ["string", "test", "text"]],
    [new ArrayOf(DataType::INT), fn () => [["1", 2], ["3", 4]]],
    [new ArrayOf(DataType::BOOL), fn () => [[true, "true"], ["1", 0]]],
    [new ArrayOf(DataType::FLOAT), fn () => [["1.5", 10.0], ["10", 7.25]]],
    [new ArrayOf(DataType::STRING), fn () => [["just", "a"], ["simple", "test"]]],
]);

it("should not validate if one element does not have the specified type on strict mode", function (DataTypeInterface $type, array $array) {
    $arrayOf = new ArrayOf($type);
    $result = $arrayOf->strictValidate($array);
    expect($result)->toBeFalse();
})->with([
    [DataType::INT, fn () => ["1", 2, 3]],
    [DataType::BOOL, fn () => [1, false, true]],
    [DataType::FLOAT, fn () => [1.5, "10.0", 10]],
    [DataType::STRING, fn () => [true, "test", "text"]],
    [new ArrayOf(DataType::INT), fn () => [[true, 2], [3, 4]]],
    [new ArrayOf(DataType::BOOL), fn () => [["true", true], [false, false]]],
    [new ArrayOf(DataType::FLOAT), fn () => [[1.5, true], [10, 7.25]]],
    [new ArrayOf(DataType::STRING), fn () => [[1, "a"], ["simple", "test"]]],
]);

it("should not validate if one element does not have the specified type on non strict mode", function (DataTypeInterface $type, array $array) {
    $arrayOf = new ArrayOf($type);
    $result = $arrayOf->validate($array);
    expect($result)->toBeFalse();
})->with([
    [DataType::INT, fn () => [true, 2, "3"]],
    [DataType::BOOL, fn () => [true, 2, "true", "1", 0]],
    [DataType::FLOAT, fn () => [1.5, "10.0", false]],
    [DataType::STRING, fn () => [1, "test", "text"]],
    [new ArrayOf("INT"), fn () => [["1", 2], ["3", true]]],
    [new ArrayOf("BOOL"), fn () => [[2, "true"], ["1", 0]]],
    [new ArrayOf("FLOAT"), fn () => [["1.5", false], ["10", 7.25]]],
    [new ArrayOf("STRING"), fn () => [["just", true], ["simple", "test"]]],
]);
