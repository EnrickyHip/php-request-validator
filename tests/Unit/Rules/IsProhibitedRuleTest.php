<?php

use Enricky\RequestValidator\Rules\IsProhibitedRule;

it("should be a major rule", function () {
    $isProhibitedRule = new IsProhibitedRule(true, "is prohibited");
    expect($isProhibitedRule->isMajor())->toBeTrue();
});

it("should return the correct error message", function () {
    $isProhibitedRule = new IsProhibitedRule(true, "is prohibited");
    expect($isProhibitedRule->getMessage())->toBe("is prohibited");
});

it("should validate if not prohibited (condition is false, regardless of the value)", function (Closure|bool $condition) {
    $isProhibitedRule = new IsProhibitedRule($condition, "is prohibited");
    expect($isProhibitedRule->validate(null))->toBeTrue();
    expect($isProhibitedRule->validate("random text"))->toBeTrue();
})->with([
    false,
    fn () => fn () => false,
]);

it("should validate if prohibited but value not sent", function (Closure|bool $condition) {
    $isProhibitedRule = new IsProhibitedRule($condition, "is prohibited");
    expect($isProhibitedRule->validate(null))->toBeTrue();
})->with([
    true,
    fn () => fn () => true,
]);

it("should not validate if prohibited and value sent", function (Closure|bool $condition) {
    $isProhibitedRule = new IsProhibitedRule($condition, "is prohibited");
    expect($isProhibitedRule->validate("random text"))->toBeFalse();
})->with([
    true,
    fn () => fn () => true,
]);
