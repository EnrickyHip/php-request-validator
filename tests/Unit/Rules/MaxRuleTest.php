<?php

use Enricky\RequestValidator\Rules\MaxRule;

beforeEach(function () {
    $this->maxRule = new MaxRule(10);
});

it("should not be a major rule", function () {
    expect($this->maxRule->isMajor())->toBeFalse();
});

it("should return the default error message", function () {
    expect($this->maxRule->getMessage())->toBe("field :name length is bigger than :max");
});

it("should return the curtom error message", function () {
    $maxRule = new MaxRule(10, "too big!");
    expect($maxRule->getMessage())->toBe("too big!");
});

it("should not validate if not sent a string, a number or an array", function (mixed $value) {
    expect($this->maxRule->validate($value))->toBeFalse();
})->with([true, new stdClass()]);

it("should validate if value length is less or equal than the maximum", function (string $value) {
    expect($this->maxRule->validate($value))->toBeTrue();
})->with([
    "a",
    "aaaaa",
    "aaaaaaaaa",
    "aaaaaaaaaa",
]);

it("should not validate if value length is bigger than the maximum", function (string $value) {
    expect($this->maxRule->validate($value))->toBeFalse();
})->with([
    "aaaaaaaaaaa",
    "aaaaaaaaaaaaaaaa",
]);

it("should replace :min parameter with given value", function () {
    $message = $this->maxRule->resolveMessage(new AttributeMock());
    expect($message)->toBe("field 'name' length is bigger than 10");
});

it("should not validate if value is a number higher than the maximum", function (int|float $number) {
    expect($this->maxRule->validate($number))->toBeFalse();
})->with([11, 10.1, 12, 20]);

it("should validate if value is a number lower or equal than the maximum", function (int|float $number) {
    expect($this->maxRule->validate($number))->toBeTrue();
})->with([10, 10.0, 9, 9.9, 1]);

it("should not validate if array has more elements than the maximum", function (array $array) {
    expect($this->maxRule->validate($array))->toBeFalse();
})->with([
    fn () => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
    fn () => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13]
]);

it("should validate if array doesnt have more elements than the maximum", function (array $array) {
    expect($this->maxRule->validate($array))->toBeTrue();
})->with([
    fn () => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
    fn () => [1, 2, 3, 4, 5, 6, 7, 8, 9]
]);

it("should get max value", function (int|float $value) {
    $maxRule = new MaxRule($value);
    expect($maxRule->getMax())->toBe($value);
})->with([10, 1.1, 2, 3, 4.5, 12]);
