<?php

use Enricky\RequestValidator\Rules\CustomRule;

beforeEach(function () {
    $condition = fn (mixed $value) => $value === 1;
    $this->customRule = new CustomRule($condition);
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

it("should return the default error message if not sent", function () {
    expect($this->customRule->getMessage())->toBe("field :name is invalid");
});

it("should return the custom error message if sent", function () {
    $condition = fn (mixed $value) => $value === 1;
    $customRule = new CustomRule($condition, "Should be 1");
    expect($customRule->getMessage())->toBe("Should be 1");
});

it("should get condition", function () {
    $condition = fn (mixed $value) => $value === 1;
    $customRule = new CustomRule($condition, "Should be 1");
    expect($customRule->getCondition())->toBe($condition);
});
