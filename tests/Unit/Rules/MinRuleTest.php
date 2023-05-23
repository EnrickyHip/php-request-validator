<?php

use Enricky\RequestValidator\Rules\MinRule;

beforeEach(function () {
    $this->minRule = new MinRule(10);
});

it("should not be a major rule", function () {
    expect($this->minRule->isMajor())->toBeFalse();
});

it("should return the default error message", function () {
    expect($this->minRule->getMessage())->toBe("field :fieldName length is lower than :min");
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
    $message = $this->minRule->resolveMessage(new FieldMock());
    expect($message)->toBe("field 'name' length is lower than 10");
});
