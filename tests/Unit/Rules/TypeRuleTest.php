<?php

use Enricky\RequestValidator\Exceptions\InvalidDataTypeException;
use Enricky\RequestValidator\Exceptions\NoTypeSentException;
use Enricky\RequestValidator\Rules\TypeRule;
use Enricky\RequestValidator\Types\DataType;

it("should not be a major rule", function () {
    $typeRule = new TypeRule(createType());
    expect($typeRule->isMajor())->toBeTrue();
});

it("should return the default error message", function () {
    $typeRule = new TypeRule(createType());
    expect($typeRule->getMessage())->toBe("field :name is not of type: :type");
});

it("should return the custom error message", function () {
    $typeRule = new TypeRule(createType(), "wrong data type");
    expect($typeRule->getMessage())->toBe("wrong data type");
});

it("should throw InvalidDataTypeException if the data type sent as string does not exist", function (string $invalidType) {
    $closure = fn () => new TypeRule($invalidType);
    expect($closure)->toThrow(InvalidDataTypeException::class);
})->with(["strin", "integer", "double", "boolean", "", "aaaaaa"]);

it("should accept valid data type strings", function (string $invalidType) {
    $closure = fn () => new TypeRule($invalidType);
    expect($closure)->not->toThrow(InvalidDataTypeException::class);
})->with(["string", "int", "float", "bool"]);

it("should validate if value is of at least one data type", function () {
    $rule = new TypeRule(
        [
            createType(false, false),
            createType(false, false),
            createType(true, true),
        ]
    );

    expect($rule->validate("teste"))->toBeTrue();
});

it("should not validate if value is not of any data type sent", function () {
    $rule = new TypeRule(
        [
            createType(false, false),
            createType(false, false),
            createType(false, false),
        ]
    );

    expect($rule->validate("teste"))->toBeFalse();
});

it("should throw InvalidDataTypeException if at least one data type sent does not exist", function () {
    $closure = fn () => new TypeRule(['int', 'bool', 'invalid']);
    expect($closure)->toThrow(InvalidDataTypeException::class);
});

it("should throw NoTypeSentException array if types is empty", function () {
    $closure = fn () => new TypeRule([]);
    expect($closure)->toThrow(NoTypeSentException::class);
});

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
    expect($message)->toBe("field 'name' is not of type: '$name'");
})->with(["name", "value", "other name"]);

it("should replace :type parameter with an array of type", function () {
    $typeRule = new TypeRule(['int', 'bool', 'float']);
    $message = $typeRule->resolveMessage(new AttributeMock());
    expect($message)->toBe("field 'name' is not of type: 'int | bool | float'");
});

it("should return type", function () {
    $type = createType();
    $typeRule = new TypeRule($type);
    expect($typeRule->getType())->toBe($type);
});

it("should return array of types", function (array $types, array $expectedArray) {
    $typeRule = new TypeRule($types);
    expect($typeRule->getType())->toBe($expectedArray);
})->with([
    [
        fn () => ["string", DataType::BOOL],
        fn () => [DataType::STRING, DataType::BOOL]
    ],
    [
        fn () => [DataType::FLOAT, DataType::INT],
        fn () => [DataType::FLOAT, DataType::INT]
    ],
    [
        fn () => ["string", "float"],
        fn () => [DataType::STRING, DataType::FLOAT]
    ],
]);

it("should get strict mode", function () {
    $typeRule1 = new TypeRule(createType());
    $typeRule2 = new TypeRule(createType(), strict: false);

    expect($typeRule1->getStrictMode())->toBeTrue();
    expect($typeRule2->getStrictMode())->toBeFalse();
});
