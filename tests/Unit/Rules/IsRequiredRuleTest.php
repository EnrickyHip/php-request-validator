<?php

use Enricky\RequestValidator\Rules\IsRequiredRule;

it("should be a major rule", function () {
    $isRequiredRule = new IsRequiredRule("is required");
    expect($isRequiredRule->isMajor())->toBeTrue();
});

it("should return the correct error message", function () {
    $isRequiredRule = new IsRequiredRule("is required");
    expect($isRequiredRule->getMessage())->toBe("is required");
});

it("should validate if value is set", function () {
    $isRequiredRule = new IsRequiredRule("is required");
    expect($isRequiredRule->validate("value"))->toBeTrue();
});

it("should not validate if value is not set and condition set", function (bool|Closure $condition) {
    $isRequiredRule = new IsRequiredRule("is required", $condition);
    expect($isRequiredRule->validate(null))->toBeFalse();
})->with([
    true,
    fn () => fn () => true,
]);

it("should not validate if value is not set and condition not sent (true as default)", function () {
    $isRequiredRule = new IsRequiredRule("is required");
    expect($isRequiredRule->validate(null))->toBeFalse();
});

it("should validate if condition is false (regardless of the value)", function (Closure|bool $condition) {
    $isRequiredRule = new IsRequiredRule("is required", $condition);
    expect($isRequiredRule->validate(null))->toBeTrue();
    expect($isRequiredRule->validate("value"))->toBeTrue();
})->with([
    false,
    fn () => fn () => false,
]);
