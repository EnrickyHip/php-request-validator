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

beforeEach(function () {
    $this->testRule = new TestRule("Error message");
});

it("should return default error message if not sent", function () {
    $testRule = new TestRule();
    expect($testRule->getMessage())->toBe("field :fieldName is not valid");
});

it("should return custom message if sent", function () {
    expect($this->testRule->getMessage())->toBe("Error message");
});

it("should return false when validation fails", function () {
    expect($this->testRule->validate("invalid value"))->toBeFalse();
});

it("should return true when validation fails", function () {
    expect($this->testRule->validate("valid value"))->toBeTrue();
});

it("should not be a major rule by default", function () {
    expect($this->testRule->isMajor())->toBeFalse();
});
