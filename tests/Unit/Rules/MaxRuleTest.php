<?php

use Enricky\RequestValidator\Rules\MaxRule;

beforeEach(function () {
    $this->maxRule = new MaxRule(10, "too big!");
});

it("should not be a major rule", function () {
    expect($this->maxRule->isMajor())->toBeFalse();
});

it("should return the correct error message", function () {
    expect($this->maxRule->getMessage())->toBe("too big!");
});

it("should not validate if not sent a string", function (mixed $value) {
    expect($this->maxRule->validate($value))->toBeFalse();
})->with([1, true, [1, 2, 3], new stdClass()]);

it("should validate if value has less or equal characteres than the maximum", function (string $value) {
    expect($this->maxRule->validate($value))->toBeTrue();
})->with([
    "a",
    "aaaaa",
    "aaaaaaaaa",
    "aaaaaaaaaa",
]);

it("should not validate if value has less or equal characteres than the maximum", function (string $value) {
    expect($this->maxRule->validate($value))->toBeFalse();
})->with([
    "aaaaaaaaaaaa",
    "aaaaaaaaaaaaaaaa",
]);
