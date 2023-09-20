<?php

use Enricky\RequestValidator\Abstract\DataTypeInterface;
use Enricky\RequestValidator\Exceptions\InvalidDataTypeException;
use Enricky\RequestValidator\Exceptions\NoTypeSentException;
use Enricky\RequestValidator\Types\DataType;
use Enricky\RequestValidator\Types\ArrayOf;

it("should return name as 'array' when no type is specified", function () {
    $array = new ArrayOf();
    expect($array->getName())->toBe("array");
});

it("should return name as '{type}[]' when type is specified", function () {
    $array = new ArrayOf(DataType::INT);
    expect($array->getName())->toBe("int[]");

    $array = new ArrayOf("string");
    expect($array->getName())->toBe("string[]");
});

it("should return name as {union of types}[]' when array of types is sent", function () {
    $array = new ArrayOf([DataType::INT, "string"]);
    expect($array->getName())->toBe("(int|string)[]");
});

it("should return name as '{type}[][]' when using array of arrays", function (ArrayOf $arrayOf, $result) {
    $array = new ArrayOf($arrayOf);
    expect($array->getName())->toBe($result);
})->with([
    [fn () => new ArrayOf("bool"), "bool[][]"],
    [fn () => new ArrayOf("int"), "int[][]"],
    [fn () => new ArrayOf(["string", DataType::FLOAT]), "(string|float)[][]"],
]);

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

it("should validate if all elements have the specified type on non strict mode", function (DataTypeInterface $type, array $array) {
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

it("should validate if all elements have one of the specified types on non strict mode", function (DataTypeInterface|array $type, array $array) {
    $arrayOf = new ArrayOf($type);
    $result = $arrayOf->validate($array);
    expect($result)->toBeTrue();
})->with([
    [
        fn () => [DataType::INT, DataType::FLOAT],
        fn () => [1, "2", 2.3, "10.5"]
    ],
    [
        fn () => ["bool", "string"],
        fn () => [true, false, "true", "1", "teste"]
    ],
    [
        fn () => new ArrayOf([DataType::INT, "float"]),
        fn () => [["1", 2], ["3.2", 4.3]]
    ],
    [
        fn () => new ArrayOf(["int", DataType::BOOL]),
        fn () => [[true, "true"], ["2", 0, "1"]]
    ],
    [
        fn () => new ArrayOf([DataType::STRING, DataType::BOOL]),
        fn () => [["just", "dance"], ["true", true, "2", 0, 1]]
    ],
]);

it("should validate if all elements have one of the specified types on strict mode", function (DataTypeInterface|array $type, array $array) {
    $arrayOf = new ArrayOf($type);
    $result = $arrayOf->strictValidate($array);
    expect($result)->toBeTrue();
})->with([
    [
        fn () => [DataType::INT, DataType::FLOAT],
        fn () => [1, 2, 2.3, 10.5]
    ],
    [
        fn () => ["bool", "string"],
        fn () => [true, false, "true", "1", "teste"]
    ],
    [
        fn () => new ArrayOf([DataType::INT, "float"]),
        fn () => [[1, 2], [3.2, 4.3]]
    ],
    [
        fn () => new ArrayOf(["int", DataType::BOOL]),
        fn () => [[true, 1], [false, 0]]
    ],
    [
        fn () => new ArrayOf([DataType::STRING, DataType::BOOL]),
        fn () => [["just", "dance", false], [true, "2"]]
    ],
]);

it("should not validate if one element does not have the specified type on strict mode", function (DataTypeInterface|string $type, array $array) {
    $arrayOf = new ArrayOf($type);
    $result = $arrayOf->strictValidate($array);
    expect($result)->toBeFalse();
})->with([
    [DataType::INT, fn () => ["1", 2, 3]],
    [DataType::BOOL, fn () => [1, false, true]],
    ["float", fn () => [1.5, "10.0", 10]],
    ["string", fn () => [true, "test", "text"]],
    [new ArrayOf("int"), fn () => [[true, 2], [3, 4]]],
    [new ArrayOf("bool"), fn () => [["true", true], [false, false]]],
    [new ArrayOf(DataType::FLOAT), fn () => [[1.5, true], [10, 7.25]]],
    [new ArrayOf(DataType::STRING), fn () => [[1, "a"], ["simple", "test"]]],
]);

it(
    "should not validate if one element does not have one of the specified types on strict mode",
    function (ArrayOf|array $type, array $array) {
        $arrayOf = new ArrayOf($type);
        $result = $arrayOf->strictValidate($array);
        expect($result)->toBeFalse();
    }
)->with([
    [
        fn () => [DataType::INT, DataType::FLOAT],
        fn () => [1, 2, 3.2, "1.1"]
    ],
    [
        fn () => ["bool", DataType::STRING],
        fn () => [1, 0, false, true, "teste", 2]
    ],
    [
        fn () => new ArrayOf(["int", "float"]),
        fn () => [["2.2", 2.2], [3, 4]]
    ],
    [
        fn () => new ArrayOf(["string", DataType::BOOL]),
        fn () => [["true", true, 2], [false, false, "1", 0]]
    ],

]);
it("should not validate if one element does not have one of the specified types on non strict mode", function (DataTypeInterface|array $type, array $array) {
    $arrayOf = new ArrayOf($type);
    $result = $arrayOf->validate($array);
    expect($result)->toBeFalse();
})->with([
    [
        fn () => [DataType::INT, DataType::FLOAT],
        fn () => [1, "2", 3.2, "1.1", true]
    ],
    [
        fn () => ["bool", DataType::STRING],
        fn () => [1, 0, false, true, "teste", 2]
    ],
    [
        fn () => new ArrayOf(["int", "float"]),
        fn () => [["2.2", 2.2, true], [3, 4, "string"]]
    ],
    [
        fn () => new ArrayOf(["string", DataType::BOOL]),
        fn () => [["true", true, 2], [false, false, "1", 0,]]
    ],
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

it("should throw InvalidDataTypeException if string type does not exists", function () {
    $closure = fn () => new ArrayOf("type");
    expect($closure)->toThrow(InvalidDataTypeException::class);
});

it("should get type", function (DataTypeInterface $type) {
    $arrayOf = new ArrayOf($type);
    expect($arrayOf->getType())->toBe($type);
})->with([
    DataType::INT,
    DataType::FLOAT,
    fn () => new ArrayOf(),
    fn () =>  new ArrayOf(DataType::BOOL)
]);

it("should throw NoTypeSentException array if types is empty", function () {
    $closure = fn () => new ArrayOf([]);
    expect($closure)->toThrow(NoTypeSentException::class);
});
