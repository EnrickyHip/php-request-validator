<?php

use Enricky\RequestValidator\Rules\NotEmptyRule;

beforeEach(function () {
    $this->rule = new NotEmptyRule();
});

it("should be a major rule", function () {
    expect($this->rule->isMajor())->toBeTrue();
});

it("should return the default error message", function () {
    expect($this->rule->getMessage())->toBe("field :name cannot be empty");
});

it("should return the custom error message", function () {
    $rule = new NotEmptyRule("not empty!");
    expect($rule->getMessage())->toBe("not empty!");
});

it("should not validate if value is empty", function (mixed $value) {
    expect($this->rule->validate($value))->toBeFalse();
})->with([0, "0", null, "", fn () => [], false]);

it("should validate if value is not empty", function (mixed $value) {
    expect($this->rule->validate($value))->toBeTrue();
})->with([1, "1", true, fn () => [1, 2, 3], new stdClass()]);