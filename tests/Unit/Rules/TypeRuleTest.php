<?php

use Enricky\RequestValidator\Abstract\DataTypeInterface;
use Enricky\RequestValidator\Exceptions\InvalidDataTypeException;
use Enricky\RequestValidator\Rules\TypeRule;

it("should not be a major rule", function () {
    $typeRule = new TypeRule(createType());
    expect($typeRule->isMajor())->toBeTrue();
});

it("should return the default error message", function () {
    $typeRule = new TypeRule(createType());
    expect($typeRule->getMessage())->toBe("field :name is not of type :type");
});

it("should return the custom error message", function () {
    $typeRule = new TypeRule(createType(), "wrong data type");
    expect($typeRule->getMessage())->toBe("wrong data type");
});

it("should throw InvalidDataTypeException if the data type sent as string does not exist", function (string $invalidType) {
    $closure = fn () => new TypeRule($invalidType);
    expect($closure)->toThrow(InvalidDataTypeException::class);
})->with(["strin", "integer", "double", "boolean", "", "aaaaaa"]);

it("should validate if sent value is null", function () {
    $typeRule = new TypeRule(createType());
    expect($typeRule->validate(null))->toBeTrue();
});

it("should validate on strict", function () {
    $typeRule = new TypeRule(createType(false, true));
    expect($typeRule->validate("value"))->toBeTrue();
});

it("should validate non strict", function () {
    $typeRule = new TypeRule(createType(true, false), strict: false);
    expect($typeRule->validate("value"))->toBeTrue();
});

it("should not validate on strict", function () {
    $typeRule = new TypeRule(createType(true, false));
    expect($typeRule->validate("value"))->toBeFalse();
});

it("should not validate on non strict", function () {
    $typeRule = new TypeRule(createType(false, true), strict: false);
    expect($typeRule->validate("value"))->toBeFalse();
});

it("should replace :type parameter with the correct type name", function (string $name) {
    $typeRule = new TypeRule(createType(name: $name));
    $message = $typeRule->resolveMessage(new AttributeMock());
    expect($message)->toBe("field 'name' is not of type '$name'");
})->with(["name", "value", "other name"]);

it("should return type", function () {
    $type = createType();
    $typeRule = new TypeRule($type);
    expect($typeRule->getType())->toBe($type);
});

it("should get strict mode", function () {
    $typeRule1 = new TypeRule(createType());
    $typeRule2 = new TypeRule(createType(), strict: false);

    expect($typeRule1->getStrictMode())->toBeTrue();
    expect($typeRule2->getStrictMode())->toBeFalse();
});
