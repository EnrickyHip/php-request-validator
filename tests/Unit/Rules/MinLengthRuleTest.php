<?php

use Enricky\RequestValidator\Rules\MinLengthRule;

beforeEach(function () {
    $this->rule = new MinLengthRule(10);
});

it("should be a major rule", function () {
    expect($this->rule->isMajor())->toBeTrue();
});

it("should return the default error message", function () {
    expect($this->rule->getMessage())->toBe("array :name length is lower than :max");
});

it("should return the custom error message", function () {
    $rule = new MinLengthRule(10, "too low!");
    expect($rule->getMessage())->toBe("too low!");
});

it("should not validate if not sent an array", function (mixed $value) {
    expect($this->rule->validate($value))->toBeFalse();
})->with([true, new stdClass(), 1, "1"]);

it("should replace :min parameter with given value", function () {
    $message = $this->rule->resolveMessage(new AttributeMock("array", []));
    expect($message)->toBe("array 'array' length is lower than 10");
});

it("should validate if array doesnt have less elements than the minimum", function (array $array) {
    expect($this->rule->validate($array))->toBeTrue();
})->with([
    fn () => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
    fn () => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11]
]);

it("should not validate if array has less elements than the minimum", function (array $array) {
    expect($this->rule->validate($array))->toBeFalse();
})->with([
    fn () => [1, 2, 3, 4, 5, 6, 7, 8, 9],
    fn () => [1, 2, 3, 4, 5, 6, 7, 8]
]);

it("should get min value", function (int|float $value) {
    $rule = new MinLengthRule($value);
    expect($rule->getMin())->toBe($value);
})->with([10, 1, 2, 3, 4, 12]);
