<?php

use Enricky\RequestValidator\Rules\MinRule;

beforeEach(function () {
    $this->minRule = new MinRule(10);
});

it("should not be a major rule", function () {
    expect($this->minRule->isMajor())->toBeFalse();
});

it("should return the default error message", function () {
    expect($this->minRule->getMessage())->toBe("field :attributeName length is lower than :min");
});

it("should return the correct error message", function () {
    $minRule = new MinRule(10, "too small!");
    expect($minRule->getMessage())->toBe("too small!");
});

it("should not validate if not sent a string", function (mixed $value) {
    expect($this->minRule->validate($value))->toBeFalse();
})->with([1, true, [1, 2, 3], new stdClass()]);

it("should not validate if value length is less than the minimum", function (string $value) {
    expect($this->minRule->validate($value))->toBeFalse();
})->with([
    "a",
    "aaaaa",
    "aaaaaaaaa",
]);

it("should validate if value length is bigger than or equal the maximum", function (string $value) {
    expect($this->minRule->validate($value))->toBeTrue();
})->with([
    "aaaaaaaaaa",
    "aaaaaaaaaaaa",
    "aaaaaaaaaaaaaaaa",
]);

it("should replace :min parameter with given value", function () {
    $message = $this->minRule->resolveMessage(new AttributeMock());
    expect($message)->toBe("field 'name' length is lower than 10");
});

it("should not validate if value is a number lower than the minimum", function (int|float $number) {
    expect($this->minRule->validate($number))->toBeFalse();
})->with([9.9, 9, 1]);

it("should validate if value is a number higher or equal than the minimum", function (int|float $number) {
    expect($this->minRule->validate($number))->toBeTrue();
})->with([10, 10.0, 11, 10.1, 20]);

it("should get min value", function (int|float $value) {
    $minRule = new MinRule($value);
    expect($minRule->getMin())->toBe($value);
})->with([10, 1.1, 2, 3, 4.5, 12]);
