<?php

use Enricky\RequestValidator\Rules\IsArrayRule;

beforeEach(function () {
    $this->arrayRule = new IsArrayRule();
});

it("should validate", function (mixed $value) {
    expect($this->arrayRule->validate($value))->toBeTrue();
})->with([
    fn() => [],
    fn() => [1, 2, 3],
    fn() => ["value", "test", "text"],
]);

it("should not validate", function (mixed $value) {
    expect($this->arrayRule->validate($value))->toBeFalse();
})->with(["1", 1, "value", true]);

it("should not be a major rule", function () {
    expect($this->arrayRule->isMajor())->toBeFalse();
});

it("should return the default error message", function () {
    expect($this->arrayRule->getMessage())->toBe("field :name is not an array");
});

it("should return the custom error message", function () {
    $arrayRule = new IsArrayRule("invalid array");
    expect($arrayRule->getMessage())->toBe("invalid array");
});
