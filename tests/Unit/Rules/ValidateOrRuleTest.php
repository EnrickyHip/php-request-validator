<?php

use Enricky\RequestValidator\Rules\ValidateOrRule;

it("should not be a major rule", function () {
    $rule = new ValidateOrRule([]);
    expect($rule->isMajor())->toBeFalse();
});

it("should return the default error message if not sent", function () {
    $rule = new ValidateOrRule([]);
    expect($rule->getMessage())->toBe("field :name is invalid");
});

it("should return the custom error message if sent", function () {
    $rule = new ValidateOrRule([], "No rule was validated");
    expect($rule->getMessage())->toBe("No rule was validated");
});

it("should get rules", function () {
    $rules = [
        createRule(true),
        createRule(true),
        createRule(true),
    ];

    $rule = new ValidateOrRule($rules);
    expect($rule->getRules())->toBe($rules);
});

it("should not be exclusive by default", function () {
    $rule = new ValidateOrRule([]);
    expect($rule->isExclusive())->toBeFalse();
});

it("should set as exclusive", function () {
    $rule = new ValidateOrRule([], exclusive: true);
    expect($rule->isExclusive())->toBeTrue();
});

it("should validate if not exclusive", function (array $rules) {
    $rule = new ValidateOrRule($rules);
    expect($rule->validate("value"))->toBeTrue();
})->with([
    fn () => [
        createRule(false),
        createRule(false),
        createRule(true),
    ],
    fn () => [
        createRule(false),
        createRule(true),
        createRule(true),
    ],
    fn () => [
        createRule(true),
        createRule(true),
        createRule(true),
    ],
]);

it("should not validate if not exclusive", function () {
    $rule = new ValidateOrRule([
        createRule(false),
        createRule(false),
        createRule(false),
    ]);
    expect($rule->validate("value"))->toBeFalse();
});

it("should validate if exclusive", function () {
    $rule = new ValidateOrRule([
        createRule(false),
        createRule(false),
        createRule(true),
    ]);

    expect($rule->validate("value"))->toBeTrue();
});

it("should not validate if exclusive", function (array $rules) {
    $rule = new ValidateOrRule($rules, exclusive: true);
    expect($rule->validate("value"))->toBeFalse();
})->with([
    fn () => [
        createRule(false),
        createRule(false),
        createRule(false),
    ],
    fn () =>  [
        createRule(false),
        createRule(true),
        createRule(true),
    ],
    fn () => [
        createRule(true),
        createRule(true),
        createRule(true),
    ],
]);
