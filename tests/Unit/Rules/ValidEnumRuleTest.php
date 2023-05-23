<?php

use Enricky\RequestValidator\Enums\DataType;
use Enricky\RequestValidator\Exceptions\InvalidEnumException;
use Enricky\RequestValidator\FieldValidator;
use Enricky\RequestValidator\Rules\ValidEnumRule;

enum BackedEnumMock: string
{
    case VALUE_1 = "value1";
    case VALUE_2 = "value2";
    case VALUE_3 = "value3";
    case VALUE_4 = "value4";
}

enum EnumMock
{
    case VALUE_1;
    case VALUE_2;
    case VALUE_3;
    case VALUE_4;
}

it("should not be a major rule", function () {
    $enumRule = new ValidEnumRule(BackedEnumMock::class);
    expect($enumRule->isMajor())->toBeFalse();
});

it("should return the default error message", function () {
    $enumRule = new ValidEnumRule(BackedEnumMock::class);
    expect($enumRule->getMessage())->toBe("field :fieldName is not a part of the enum :enum");
});

it("should return the custom error message", function () {
    $enumRule = new ValidEnumRule(BackedEnumMock::class, "not part of the enum");
    expect($enumRule->getMessage())->toBe("not part of the enum");
});

it("should throw InvalidEnumException if class sent is not an backed enum", function (string $class) {
    $closure = fn () => new ValidEnumRule($class);
    expect($closure)->toThrow(InvalidEnumException::class);
})->with([EnumMock::class, ValidEnumRule::class, "", "some random text", FieldValidator::class]);

it("should create Rule if class sent is a backed enum", function (string $class) {
    $enumRule = new ValidEnumRule($class);
    expect($enumRule)->toBeInstanceOf(ValidEnumRule::class);
})->with([DataType::class, BackedEnumMock::class]);

it("should validate if value is part of the enum", function (string $enumClass, mixed $value) {
    $enumRule = new ValidEnumRule($enumClass);
    expect($enumRule->validate($value))->toBeTrue();
})->with([
    [BackedEnumMock::class, "value1"],
    [BackedEnumMock::class, "value2"],
    [BackedEnumMock::class, "value3"],
    [BackedEnumMock::class, "value4"],
    [DataType::class, "string"],
    [DataType::class, "int"],
    [DataType::class, "bool"],
    [DataType::class, "float"],
]);

it("should not validate if value is part of the enum", function (string $enumClass, mixed $value) {
    $enumRule = new ValidEnumRule($enumClass);
    expect($enumRule->validate($value))->toBeFalse();
})->with([
    [BackedEnumMock::class, "value"],
    [BackedEnumMock::class, ""],
    [BackedEnumMock::class, "int"],
    [BackedEnumMock::class, "2"],
    [DataType::class, "strin"],
    [DataType::class, "integer"],
    [DataType::class, "2"],
    [DataType::class, "value"],
]);

it("should replace :enum parameter with the enum class name", function (string $enumClass) {
    $enumRule = new ValidEnumRule($enumClass);
    $message = $enumRule->resolveMessage(new FieldMock());
    expect($message)->toBe("field 'name' is not a part of the enum '$enumClass'");
})->with([BackedEnumMock::class, DataType::class]);
