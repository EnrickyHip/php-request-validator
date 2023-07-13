<?php

use Enricky\RequestValidator\Types\DataType;
use Enricky\RequestValidator\Exceptions\InvalidDataTypeException;
use Enricky\RequestValidator\Rules\TypeRule;

it("should not be a major rule", function () {
    $typeRule = new TypeRule(DataType::INT);
    expect($typeRule->isMajor())->toBeTrue();
});

it("should return the default error message", function () {
    $typeRule = new TypeRule(DataType::INT);
    expect($typeRule->getMessage())->toBe("field :name is not of type :type");
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

it("should replace :type parameter with the correct type name", function (DataType $type) {
    $typeRule = new TypeRule($type);
    $message = $typeRule->resolveMessage(new AttributeMock());
    expect($message)->toBe("field 'name' is not of type '{$type->value}'");
})->with(DataType::cases());

it("should return type", function () {
    $typeRule1 = new TypeRule(DataType::INT);
    $typeRule2 = new TypeRule(DataType::STRING);

    expect($typeRule1->getType())->toBe(DataType::INT);
    expect($typeRule2->getType())->toBe(DataType::STRING);
});

it("should get strict mode", function () {
    $typeRule1 = new TypeRule(DataType::INT);
    $typeRule2 = new TypeRule(DataType::INT, strict: false);

    expect($typeRule1->getStrictMode())->toBeTrue();
    expect($typeRule2->getStrictMode())->toBeFalse();
});

it("should validate on no strict", function (DataType $type, mixed $value) {
    $typeRule = new TypeRule($type, strict: false);
    expect($typeRule->validate($value))->toBeTrue();
})->with([
    [DataType::STRING, "value"],
    [DataType::INT, "1"],
    [DataType::FLOAT, "1.1"],
    [DataType::BOOL, "true"],
]);

it("should not validate on no strict", function (DataType $type, mixed $value) {
    $typeRule = new TypeRule($type, strict: false);
    expect($typeRule->validate($value))->toBeFalse();
})->with([
    [DataType::STRING, 1],
    [DataType::INT, true],
    [DataType::FLOAT, new stdClass()],
    [DataType::BOOL, "value"],
]);
