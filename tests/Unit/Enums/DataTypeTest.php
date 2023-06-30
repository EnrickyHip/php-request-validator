<?php

use Enricky\RequestValidator\Enums\DataType;

it("should validate with STRING if value is string", function (string $value) {
    expect(DataType::STRING->strictValidate($value))->toBeTrue();
})->with(["", "string", "value"]);

it("should not validate with STRING if value is not a string", function (mixed $value) {
    expect(DataType::STRING->strictValidate($value))->toBeFalse();
})->with([1, true, fn () => [], new stdClass()]);

it("should validate with INT if value is int", function (int $value) {
    expect(DataType::INT->strictValidate($value))->toBeTrue();
})->with([1, 2, 10, -10]);

it("should not validate with INT if value is not a integer", function (mixed $value) {
    expect(DataType::INT->strictValidate($value))->toBeFalse();
})->with(["string", "1", 1.1, true, fn () => [], new stdClass()]);

it("should validate with FLOAT if value is float or int", function (float|int $value) {
    expect(DataType::FLOAT->strictValidate($value))->toBeTrue();
})->with([1, 2.1, -10, -10.1]);

it("should not validate with FLOAT if value is not a float number or integer", function (mixed $value) {
    expect(DataType::FLOAT->strictValidate($value))->toBeFalse();
})->with(["string", "1", true, fn () => [], new stdClass()]);

it("should validate with BOOL if value is boolean", function (bool $value) {
    expect(DataType::BOOL->strictValidate($value))->toBeTrue();
})->with([true, false]);

it("should not validate with BOOL if value is not boolean", function (mixed $value) {
    expect(DataType::BOOL->strictValidate($value))->toBeFalse();
})->with(["string", "1", 1, 1.1, fn () => [], new stdClass()]);

// NON STRICT

it("should validate non strict with STRING if value is string", function (string $value) {
    expect(DataType::STRING->validate($value))->toBeTrue();
})->with(["", "string", "value"]);

it("should not validate non strict with STRING if value is not a string", function (mixed $value) {
    expect(DataType::STRING->validate($value))->toBeFalse();
})->with([1, true, fn () => [], new stdClass()]);

it("should validate non strict with INT if value is string", function (string|int $value) {
    expect(DataType::INT->validate($value))->toBeTrue();
})->with([1, 2, 10, -10, "1", "2", "10", "-10"]);

it("should not validate non strict with INT if value is not a string", function (mixed $value) {
    expect(DataType::INT->validate($value))->toBeFalse();
})->with(["value", 1.1, "2.1", true, fn () => [], new stdClass()]);

it("should validate non strict with FLOAT if value is string", function (string|int|float $value) {
    expect(DataType::FLOAT->validate($value))->toBeTrue();
})->with([1, 2, 10, -10, "1", "2", "10", "-10", 1.1, 20.5, "1.1", "20.5"]);

it("should not validate non strict with FLOAT if value is not a string", function (mixed $value) {
    expect(DataType::FLOAT->validate($value))->toBeFalse();
})->with(["value", true, fn () => [], new stdClass()]);

it("should validate non strict with BOOL", function (string|int|bool $value) {
    expect(DataType::BOOL->validate($value))->toBeTrue();
})->with([true, false, "true", "false", 0, 1, "0", "1"]);

it("should not validate non strict with BOOL", function (mixed $value) {
    expect(DataType::BOOL->validate($value))->toBeFalse();
})->with(["value", -1, 2, fn () => [], new stdClass()]);
