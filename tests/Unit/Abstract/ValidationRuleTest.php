<?php

declare(strict_types=1);

use Enricky\RequestValidator\Abstract\ValidationRule;

class TestRule extends ValidationRule
{
    public function validate(mixed $value): bool
    {
        return $value === "valid value";
    }
}

it("should return the correct error message", function () {
    $testRule = new TestRule("Error message");
    expect($testRule->getMessage())->toBe("Error message");
});

it("should return false when validation fails", function () {
    $testRule = new TestRule("Error message");
    expect($testRule->validate("invalid value"))->toBeFalse();
});

it("should return true when validation fails", function () {
    $testRule = new TestRule("Error message");
    expect($testRule->validate("valid value"))->toBeTrue();
});

it("should not be a major rule by default", function () {
    $testRule = new TestRule("Error message");
    expect($testRule->isMajor())->toBeFalse();
});

class TestMajorRule extends ValidationRule
{
    public function validate(mixed $value): bool
    {
        return $value === "valid value";
    }

    public function isMajor(): bool
    {
        return true;
    }
}

it("should be a major rule", function () {
    $testRule = new TestMajorRule("Error message");
    expect($testRule->isMajor())->toBeTrue();
});
