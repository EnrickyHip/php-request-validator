<?php

use Enricky\RequestValidator\Rules\MaxRule;

beforeEach(function () {
    $this->maxRule = new MaxRule(10);
});

it("should not be a major rule", function () {
    expect($this->maxRule->isMajor())->toBeFalse();
});

it("should return the default error message", function () {
    expect($this->maxRule->getMessage())->toBe("field :attributeName length is bigger than :max");
});

it("should return the curtom error message", function () {
    $maxRule = new MaxRule(10, "too big!");
    expect($maxRule->getMessage())->toBe("too big!");
});

it("should not validate if not sent a string", function (mixed $value) {
    expect($this->maxRule->validate($value))->toBeFalse();
})->with([1, true, [1, 2, 3], new stdClass()]);

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
