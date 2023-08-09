<?php

use Enricky\RequestValidator\Rules\MaxLengthRule;

beforeEach(function () {
    $this->rule = new MaxLengthRule(10);
});

it("should be a major rule", function () {
    expect($this->rule->isMajor())->toBeTrue();
});

it("should return the default error message", function () {
    expect($this->rule->getMessage())->toBe("array :name length is bigger than :max");
});

it("should return the custom error message", function () {
    $rule = new MaxLengthRule(10, "too big!");
    expect($rule->getMessage())->toBe("too big!");
});

it("should not validate if not sent an array", function (mixed $value) {
    expect($this->rule->validate($value))->toBeFalse();
})->with([true, new stdClass(), 1, "1"]);

it("should replace :max parameter with given value", function () {
    $message = $this->rule->resolveMessage(new AttributeMock("array", []));
    expect($message)->toBe("array 'array' length is bigger than 10");
});

it("should not validate if array has more elements than the maximum", function (array $array) {
    expect($this->rule->validate($array))->toBeFalse();
})->with([
    fn () => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
    fn () => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13]
]);

it("should validate if array doesnt have more elements than the maximum", function (array $array) {
    expect($this->rule->validate($array))->toBeTrue();
})->with([
    fn () => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
    fn () => [1, 2, 3, 4, 5, 6, 7, 8, 9]
]);

it("should get max value", function (int|float $value) {
    $rule = new MaxLengthRule($value);
    expect($rule->getMax())->toBe($value);
})->with([10, 1, 2, 3, 4, 12]);
