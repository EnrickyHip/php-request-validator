<?php

use Enricky\RequestValidator\Rules\CustomRule;

beforeEach(function () {
    $condition = fn (mixed $value) => $value === 1;
    $this->customRule = new CustomRule($condition, "Should be 1");
});

it("should validate using custom condition", function () {
    expect($this->customRule->validate(1))->toBeTrue();
});

it("should not validate using custom condition", function () {
    expect($this->customRule->validate(2))->toBeFalse();
});

it("should not be a major rule", function () {
    expect($this->customRule->isMajor())->toBeFalse();
});

it("should return the correct error message", function () {
    expect($this->customRule->getMessage())->toBe("Should be 1");
});
